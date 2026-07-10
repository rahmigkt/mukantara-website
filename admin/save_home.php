<?php
require_once __DIR__ . '/config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$data = load_data();
$langs = ['tr', 'en', 'es', 'ar'];

$heroImage = trim($_POST['hero_image'] ?? $data['home']['tr']['hero_image']);
foreach ($langs as $lang) {
    $data['home'][$lang]['hero_image'] = $heroImage;
    $data['home'][$lang]['hero_tag'] = trim($_POST["hero_tag_{$lang}"] ?? '');
    $data['home'][$lang]['hero_title'] = trim($_POST["hero_title_{$lang}"] ?? '');
    $data['home'][$lang]['hero_body'] = trim($_POST["hero_body_{$lang}"] ?? '');
    $data['home'][$lang]['curator_tag'] = trim($_POST["curator_tag_{$lang}"] ?? '');
    $data['home'][$lang]['curator_quote'] = trim($_POST["curator_quote_{$lang}"] ?? '');
    $data['home'][$lang]['curator_body'] = trim($_POST["curator_body_{$lang}"] ?? '');
}

$featured = array_map('trim', explode(',', $_POST['featured_slugs'] ?? ''));
$validSlugs = array_column($data['products'], 'slug');
$data['featured_slugs'] = array_values(array_intersect($featured, $validSlugs));

save_data($data);
generate_all();

header('Location: index.php?ok=saved');
exit;
