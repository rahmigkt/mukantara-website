<?php
require_once __DIR__ . '/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$data = load_data();
$langs = ['tr', 'en', 'es', 'ar'];

$original_slug = trim($_POST['original_slug'] ?? '');
$slug_input = trim($_POST['slug'] ?? '');
$title_tr = trim($_POST['title_tr'] ?? '');

$slug = $slug_input !== '' ? slugify($slug_input) : slugify($title_tr);
if ($slug === '') { $slug = 'eser-' . substr(md5(microtime()), 0, 6); }

// Slug çakışması kontrolü (kendisi hariç)
$existingSlugs = array_column($data['products'], 'slug');
if ($slug !== $original_slug && in_array($slug, $existingSlugs)) {
    $slug .= '-' . substr(md5(microtime()), 0, 4);
}

// Mevcut ürünü bul (varsa)
$index = null;
foreach ($data['products'] as $i => $p) {
    if ($p['slug'] === $original_slug && $original_slug !== '') { $index = $i; break; }
}

$images = $index !== null ? $data['products'][$index]['images'] : [];

// Kaldırılacak görseller
if (!empty($_POST['remove_images']) && is_array($_POST['remove_images'])) {
    $images = array_values(array_diff($images, $_POST['remove_images']));
}

// Yeni yüklenen görseller
$imgDir = __DIR__ . '/../assets/img/';
if (!empty($_FILES['new_images']) && is_array($_FILES['new_images']['name'])) {
    foreach ($_FILES['new_images']['name'] as $i => $name) {
        if ($_FILES['new_images']['error'][$i] !== UPLOAD_ERR_OK || $name === '') continue;
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;
        $newName = $slug . '-' . substr(md5(uniqid('', true)), 0, 6) . '.' . $ext;
        move_uploaded_file($_FILES['new_images']['tmp_name'][$i], $imgDir . $newName);
        $images[] = $newName;
    }
}

// Facts birleştirme (diller arası satır bazlı eşleme)
$factsByLang = [];
$maxRows = 0;
foreach ($langs as $lang) {
    $labels = $_POST["fact_label_{$lang}"] ?? [];
    $values = $_POST["fact_value_{$lang}"] ?? [];
    $rows = [];
    foreach ($labels as $i => $lbl) {
        $val = $values[$i] ?? '';
        if (trim($lbl) === '' && trim($val) === '') continue;
        $rows[] = ['label' => trim($lbl), 'value' => trim($val)];
    }
    $factsByLang[$lang] = $rows;
    $maxRows = max($maxRows, count($rows));
}
$facts = [];
for ($i = 0; $i < $maxRows; $i++) {
    $row = ['label' => [], 'value' => []];
    foreach ($langs as $lang) {
        $row['label'][$lang] = $factsByLang[$lang][$i]['label'] ?? ($factsByLang['tr'][$i]['label'] ?? '');
        $row['value'][$lang] = $factsByLang[$lang][$i]['value'] ?? '';
    }
    $facts[] = $row;
}

// Başlık, açıklama, not
$title = []; $desc = []; $note = [];
foreach ($langs as $lang) {
    $title[$lang] = trim($_POST["title_{$lang}"] ?? '');
    $lines = preg_split('/\r\n|\r|\n/', $_POST["desc_{$lang}"] ?? '');
    $lines = array_values(array_filter(array_map('trim', $lines), fn($l) => $l !== ''));
    $desc[$lang] = $lines;
    $note[$lang] = trim($_POST["note_{$lang}"] ?? '');
}

$product = [
    'slug' => $slug,
    'category' => $_POST['category'] ?? array_key_first($data['category_i18n']),
    'envanter_no' => trim($_POST['envanter_no'] ?? ''),
    'images' => array_values($images),
    'title' => $title,
    'facts' => $facts,
    'desc' => $desc,
    'note' => $note,
];

// Slug değiştiyse eski dosyaları temizle
if ($original_slug !== '' && $original_slug !== $slug) {
    foreach (LANGS as $lang) {
        $langDir = $lang === 'tr' ? SITE_ROOT : SITE_ROOT . "/{$lang}";
        @unlink("{$langDir}/eserler/{$original_slug}.html");
    }
}

if ($index !== null) {
    $data['products'][$index] = $product;
} else {
    $data['products'][] = $product;
}

save_data($data);
generate_all();

header('Location: index.php?ok=saved');
exit;
