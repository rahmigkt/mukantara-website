<?php
require_once __DIR__ . '/config.php';
$cfg = load_admin_config();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'] ?? '';
    if (password_verify($pass, $cfg['password_hash'])) {
        $_SESSION['mukantara_admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Şifre yanlış, tekrar deneyin.';
    }
}
admin_head('Giriş');
?>
<div class="login-box card">
  <p class="tag">Mukantara Yönetim Paneli</p>
  <h1 style="margin-top:10px;">Giriş Yap</h1>
  <?php if ($error): ?><p style="color:#e39b9b;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <label>Şifre</label>
    <input type="password" name="password" autofocus required>
    <button type="submit" class="btn btn-brass" style="margin-top:22px; width:100%; justify-content:center;">Giriş Yap</button>
  </form>
</div>
<?php admin_foot(); ?>
