<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

define('ADMIN_CONFIG_PATH', __DIR__ . '/../data/admin-config.json');

function load_admin_config() {
    if (!file_exists(ADMIN_CONFIG_PATH)) {
        // İlk kurulum: rastgele bir şifre üret ve kaydet.
        $default_pass = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'), 0, 10);
        $cfg = ['password_hash' => password_hash($default_pass, PASSWORD_DEFAULT)];
        file_put_contents(ADMIN_CONFIG_PATH, json_encode($cfg));
        // Bu şifreyi bir kereliğine dosyaya da yazalım ki kaybolmasın.
        file_put_contents(__DIR__ . '/../data/ILK-SIFRE-BUNU-SIL.txt',
            "İlk giriş şifreniz: {$default_pass}\nGiriş yaptıktan sonra bu dosyayı silin ve şifrenizi değiştirin.");
        return $cfg;
    }
    return json_decode(file_get_contents(ADMIN_CONFIG_PATH), true);
}

function save_admin_config($cfg) {
    file_put_contents(ADMIN_CONFIG_PATH, json_encode($cfg));
}

function is_logged_in() {
    return !empty($_SESSION['mukantara_admin']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function admin_head($title) {
    echo "<!DOCTYPE html><html lang='tr'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>{$title} — Mukantara Yönetim</title>";
    echo "<link href='https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Newsreader:ital,wght@0,400;0,500;1,400&display=swap' rel='stylesheet'>";
    echo "<link rel='stylesheet' href='admin.css'></head><body>";
}
function admin_foot() {
    echo "</body></html>";
}
