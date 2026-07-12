<?php
// MUKANTARA — teklif formu gönderim işleyicisi.
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$data = load_data();
$lang = in_array($_POST['lang'] ?? '', LANGS) ? $_POST['lang'] : 'tr';

$ad = trim($_POST['ad'] ?? '');
$telefon = trim($_POST['telefon'] ?? '');
$eposta = trim($_POST['eposta'] ?? '');
$urun = trim($_POST['urun'] ?? '');
$kurulum_tipi = trim($_POST['kurulum_tipi'] ?? '');
$mesaj = trim($_POST['mesaj'] ?? '');

// Basit doğrulama
if ($ad === '' || $telefon === '' || !filter_var($eposta, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . ($lang === 'tr' ? '' : "{$lang}/") . 'teklif.html?error=1');
    exit;
}

$to = $data['contact_email'] ?? 'hrahmi@gmail.com';
$subject = "MUKANTARA — Yeni Teklif Talebi: {$urun}";
$body = "Yeni bir teklif talebi alındı.\n\n"
      . "Ad Soyad: {$ad}\n"
      . "Telefon: {$telefon}\n"
      . "E-posta: {$eposta}\n"
      . "İlgilenilen Eser: {$urun}\n"
      . "Kurulum Tipi: " . ($kurulum_tipi !== '' ? $kurulum_tipi : 'Belirtilmedi') . "\n"
      . "Dil: {$lang}\n"
      . "Mesaj:\n{$mesaj}\n";
$headers = "From: no-reply@mukantara.com\r\nReply-To: {$eposta}\r\n";

$mailSent = @mail($to, $subject, $body, $headers);

// Yedek: her durumda bir JSON log dosyasına da yaz (e-posta ulaşmazsa kaybolmasın)
$logPath = __DIR__ . '/data/teklifler.json';
$log = file_exists($logPath) ? json_decode(file_get_contents($logPath), true) : [];
$log[] = [
    'tarih' => date('Y-m-d H:i:s'),
    'ad' => $ad, 'telefon' => $telefon, 'eposta' => $eposta,
    'urun' => $urun, 'kurulum_tipi' => $kurulum_tipi, 'mesaj' => $mesaj, 'dil' => $lang,
    'eposta_gonderildi' => $mailSent,
];
file_put_contents($logPath, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

$redirect = ($lang === 'tr' ? '' : "{$lang}/") . 'teklif.html?ok=1';
header("Location: {$redirect}");
exit;
