<?php
require_once __DIR__ . '/config.php';
require_login();
$data = load_data();
$langLabels = ['tr'=>'Türkçe','en'=>'English','es'=>'Español','ar'=>'العربية'];

admin_head('Ana Sayfa Düzenle');
?>
<div class="topbar">
  <span class="tag">MUKANTARA · Yönetim Paneli</span>
  <a href="index.php">← Panele Dön</a>
</div>
<div class="wrap">
  <h1>Ana Sayfa</h1>

  <form method="post" action="save_home.php">
    <div class="card">
      <p class="tag">Hero Görseli &amp; Öne Çıkan Eserler</p>
      <label>Hero Görseli (dosya adı — assets/img/ klasöründen)</label>
      <input type="text" name="hero_image" value="<?= htmlspecialchars($data['home']['tr']['hero_image']) ?>">
      <p class="helptext">Mevcut görsellerden birinin dosya adını yazın, örn: 07-ulug-bey-usturlabi.jpg</p>

      <label>Öne Çıkan Eserler (sergi bölümünde gösterilecek, sırasıyla, virgülle ayırın)</label>
      <input type="text" name="featured_slugs" value="<?= htmlspecialchars(implode(', ', $data['featured_slugs'])) ?>">
      <p class="helptext">Geçerli slug'lar: <?= htmlspecialchars(implode(', ', array_column($data['products'], 'slug'))) ?></p>
    </div>

    <div class="tabs">
      <?php foreach ($langLabels as $code => $label): ?>
        <button type="button" data-tab="<?= $code ?>" class="<?= $code === 'tr' ? 'active' : '' ?>"><?= $label ?></button>
      <?php endforeach; ?>
    </div>

    <?php foreach ($langLabels as $code => $label): $h = $data['home'][$code]; ?>
      <div class="tabpanel <?= $code === 'tr' ? 'active' : '' ?>" data-panel="<?= $code ?>">
        <div class="card">
          <label>Hero Üst Etiket (<?= $label ?>)</label>
          <input type="text" name="hero_tag_<?= $code ?>" value="<?= htmlspecialchars($h['hero_tag']) ?>">
          <label>Hero Başlığı (<?= $label ?>)</label>
          <textarea name="hero_title_<?= $code ?>"><?= htmlspecialchars($h['hero_title']) ?></textarea>
          <label>Hero Açıklaması (<?= $label ?>)</label>
          <textarea name="hero_body_<?= $code ?>"><?= htmlspecialchars($h['hero_body']) ?></textarea>
          <label>Küratör Notu Üst Etiket (<?= $label ?>)</label>
          <input type="text" name="curator_tag_<?= $code ?>" value="<?= htmlspecialchars($h['curator_tag']) ?>">
          <label>Küratör Alıntısı (<?= $label ?>)</label>
          <textarea name="curator_quote_<?= $code ?>"><?= htmlspecialchars($h['curator_quote']) ?></textarea>
          <label>Küratör Notu Metni (<?= $label ?>)</label>
          <textarea name="curator_body_<?= $code ?>"><?= htmlspecialchars($h['curator_body']) ?></textarea>
        </div>
      </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-brass">Kaydet ve Yayınla</button>
    <a href="index.php" class="btn btn-line">Vazgeç</a>
  </form>
</div>
<script>
document.querySelectorAll('.tabs button').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tabs button').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tabpanel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.querySelector('.tabpanel[data-panel="' + btn.dataset.tab + '"]').classList.add('active');
  });
});
</script>
<?php admin_foot(); ?>
