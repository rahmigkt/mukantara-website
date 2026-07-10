<?php
require_once __DIR__ . '/config.php';
require_login();
$data = load_data();
$flash = $_GET['ok'] ?? '';

admin_head('Yönetim Paneli');
?>
<div class="topbar">
  <span class="tag">MUKANTARA · Yönetim Paneli</span>
  <div>
    <a href="../index.html" target="_blank">Siteyi Gör ↗</a>
    &nbsp;·&nbsp;
    <a href="change-password.php">Şifre Değiştir</a>
    &nbsp;·&nbsp;
    <a href="logout.php">Çıkış</a>
  </div>
</div>
<div class="wrap">

  <?php if ($flash === 'saved'): ?><div class="flash">Kaydedildi ve site yeniden yayınlandı.</div><?php endif; ?>
  <?php if ($flash === 'deleted'): ?><div class="flash">Eser silindi ve site yeniden yayınlandı.</div><?php endif; ?>
  <?php if (file_exists(__DIR__ . '/../data/ILK-SIFRE-BUNU-SIL.txt')): ?>
    <div class="flash">
      İlk giriş şifreniz <code><?= htmlspecialchars(trim(str_replace('İlk giriş şifreniz:', '', explode("\n", file_get_contents(__DIR__.'/../data/ILK-SIFRE-BUNU-SIL.txt'))[0]))) ?></code> idi.
      Lütfen <a href="change-password.php">şifrenizi değiştirin</a> — bu değiştikten sonra bu uyarı kaybolacak.
    </div>
  <?php endif; ?>

  <div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <p class="tag">Ana Sayfa</p>
        <h2 style="margin-top:6px;">Hero metni, küratör notu, öne çıkan eserler</h2>
      </div>
      <a href="home.php" class="btn btn-line">Düzenle</a>
    </div>
  </div>

  <div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <div>
        <p class="tag">Koleksiyon</p>
        <h2 style="margin-top:6px;"><?= count($data['products']) ?> Eser</h2>
      </div>
      <a href="product.php" class="btn btn-brass">+ Yeni Eser Ekle</a>
    </div>
    <div class="product-list">
      <?php foreach ($data['products'] as $p): ?>
        <div class="product-row">
          <img src="../assets/img/<?= htmlspecialchars($p['images'][0]) ?>" alt="">
          <div class="grow">
            <div><?= htmlspecialchars($p['title']['tr']) ?></div>
            <div class="cat"><?= htmlspecialchars($p['category']) ?> · <?= htmlspecialchars($p['envanter_no']) ?></div>
          </div>
          <a href="product.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-line">Düzenle</a>
          <a href="delete.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-danger" onclick="return confirm('“<?= htmlspecialchars($p['title']['tr'], ENT_QUOTES) ?>” eserini silmek istediğinize emin misiniz?');">Sil</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <p class="tag">Bakım</p>
    <p style="margin:10px 0 16px;">Bir sorun olduğunu düşünüyorsanız, veriyi değiştirmeden siteyi olduğu gibi yeniden üretebilirsiniz.</p>
    <a href="regenerate.php" class="btn btn-line">Siteyi Yeniden Yayınla</a>
  </div>

</div>
<?php admin_foot(); ?>
