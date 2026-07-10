<?php
require_once __DIR__ . '/config.php';
require_login();

$slug = $_GET['slug'] ?? '';
$data = load_data();
$data['products'] = array_values(array_filter($data['products'], fn($p) => $p['slug'] !== $slug));
$data['featured_slugs'] = array_values(array_filter($data['featured_slugs'], fn($s) => $s !== $slug));
save_data($data);

foreach (LANGS as $lang) {
    $langDir = $lang === 'tr' ? SITE_ROOT : SITE_ROOT . "/{$lang}";
    @unlink("{$langDir}/eserler/{$slug}.html");
}

generate_all();
header('Location: index.php?ok=deleted');
exit;
