<?php
require_once __DIR__ . '/config.php';
require_login();
$cfg = load_admin_config();
$error = ''; $ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current'] ?? '';
    $new1 = $_POST['new1'] ?? '';
    $new2 = $_POST['new2'] ?? '';
    if (!password_verify($current, $cfg['password_hash'])) {
        $error = 'Mevcut şifre yanlış.';
    } elseif (strlen($new1) < 6) {
        $error = 'Yeni şifre en az 6 karakter olmalı.';
    } elseif ($new1 !== $new2) {
        $error = 'Yeni şifreler eşleşmiyor.';
    } else {
        $cfg['password_hash'] = password_hash($new1, PASSWORD_DEFAULT);
        save_admin_config($cfg);
        @unlink(__DIR__ . '/../data/ILK-SIFRE-BUNU-SIL.txt');
        $ok = true;
    }
}
admin_head('Şifre Değiştir');
?>
<div class="topbar">
  <span class="tag">MUKANTARA · Yönetim Paneli</span>
  <a href="index.php">← Panele Dön</a>
</div>
<div class="wrap" style="max-width:480px;">
  <div class="card">
    <h1>Şifre Değiştir</h1>
    <?php if ($ok): ?>
      <p style="color:var(--brass-light);">Şifreniz güncellendi.</p>
    <?php else: ?>
      <?php if ($error): ?><p style="color:#e39b9b;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
      <form method="post">
        <label>Mevcut Şifre</label>
        <input type="password" name="current" required>
        <label>Yeni Şifre</label>
        <input type="password" name="new1" required>
        <label>Yeni Şifre (Tekrar)</label>
        <input type="password" name="new2" required>
        <button type="submit" class="btn btn-brass" style="margin-top:22px;">Güncelle</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php admin_foot(); ?>
