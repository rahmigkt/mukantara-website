<?php
require_once __DIR__ . '/config.php';
require_login();
$data = load_data();

$slug = $_GET['slug'] ?? '';
$isNew = $slug === '';
$p = null;
if (!$isNew) {
    foreach ($data['products'] as $pp) { if ($pp['slug'] === $slug) { $p = $pp; break; } }
    if (!$p) { header('Location: index.php'); exit; }
} else {
    $p = [
        'slug' => '', 'category' => array_key_first($data['category_i18n']),
        'envanter_no' => '', 'images' => [],
        'title' => ['tr'=>'','en'=>'','es'=>'','ar'=>''],
        'facts' => [],
        'desc' => ['tr'=>[],'en'=>[],'es'=>[],'ar'=>[]],
        'note' => ['tr'=>'','en'=>'','es'=>'','ar'=>''],
    ];
}

$langLabels = ['tr'=>'Türkçe','en'=>'English','es'=>'Español','ar'=>'العربية'];
admin_head($isNew ? 'Yeni Eser' : 'Eser Düzenle');
?>
<div class="topbar">
  <span class="tag">MUKANTARA · Yönetim Paneli</span>
  <a href="index.php">← Panele Dön</a>
</div>
<div class="wrap">
  <h1><?= $isNew ? 'Yeni Eser Ekle' : htmlspecialchars($p['title']['tr']) ?></h1>

  <form method="post" action="save.php" enctype="multipart/form-data">
    <input type="hidden" name="original_slug" value="<?= htmlspecialchars($p['slug']) ?>">

    <div class="card">
      <p class="tag">Genel Bilgiler (tüm dillerde ortak)</p>
      <div class="row2">
        <div>
          <label>Slug (URL — yalnızca harf/rakam/tire, boş bırakılırsa Türkçe başlıktan otomatik oluşturulur)</label>
          <input type="text" name="slug" value="<?= htmlspecialchars($p['slug']) ?>" placeholder="orn-yeni-eser-adi">
        </div>
        <div>
          <label>Envanter No</label>
          <input type="text" name="envanter_no" value="<?= htmlspecialchars($p['envanter_no']) ?>">
        </div>
      </div>
      <label>Kategori</label>
      <select name="category">
        <?php foreach ($data['category_i18n'] as $key => $names): ?>
          <option value="<?= htmlspecialchars($key) ?>" <?= $p['category'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($key) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Görseller</label>
      <?php if (!empty($p['images'])): ?>
        <div class="img-current">
          <?php foreach ($p['images'] as $i => $img): ?>
            <div class="thumb">
              <img src="../assets/img/<?= htmlspecialchars($img) ?>" alt="">
              <label><input type="checkbox" name="remove_images[]" value="<?= htmlspecialchars($img) ?>"> Kaldır</label>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <input type="file" name="new_images[]" accept="image/*" multiple>
      <p class="helptext">Birden fazla görsel seçerseniz, ürün sayfasında otomatik olarak kaydırmalı galeri oluşur.</p>
    </div>

    <div class="tabs">
      <?php foreach ($langLabels as $code => $label): ?>
        <button type="button" data-tab="<?= $code ?>" class="<?= $code === 'tr' ? 'active' : '' ?>"><?= $label ?></button>
      <?php endforeach; ?>
    </div>

    <?php foreach ($langLabels as $code => $label): ?>
      <div class="tabpanel <?= $code === 'tr' ? 'active' : '' ?>" data-panel="<?= $code ?>">
        <div class="card">
          <label>Başlık (<?= $label ?>)</label>
          <input type="text" name="title_<?= $code ?>" value="<?= htmlspecialchars($p['title'][$code] ?? '') ?>">

          <label>Açıklama (<?= $label ?>) — her paragrafı ayrı satıra yazın</label>
          <textarea name="desc_<?= $code ?>" style="min-height:140px;"><?= htmlspecialchars(implode("\n", $p['desc'][$code] ?? [])) ?></textarea>

          <label>İçerik Notu (<?= $label ?>, opsiyonel — örn. belirsiz/çelişkili bilgi uyarısı)</label>
          <textarea name="note_<?= $code ?>"><?= htmlspecialchars($p['note'][$code] ?? '') ?></textarea>

          <label>Bilgi Kutucukları (Tarih, Malzeme, vb.)</label>
          <div class="facts-wrap" data-lang="<?= $code ?>">
            <?php
            $facts = $p['facts'] ?? [];
            $rows = max(count($facts), 6);
            for ($i = 0; $i < $rows; $i++):
              $lbl = $facts[$i]['label'][$code] ?? '';
              $val = $facts[$i]['value'][$code] ?? '';
            ?>
              <div class="fact-row">
                <input type="text" name="fact_label_<?= $code ?>[]" value="<?= htmlspecialchars($lbl) ?>" placeholder="Etiket (örn. Tarih)">
                <input type="text" name="fact_value_<?= $code ?>[]" value="<?= htmlspecialchars($val) ?>" placeholder="Değer (örn. H. 830 / M. 1428)">
              </div>
            <?php endfor; ?>
          </div>
          <p class="helptext">Boş satırlar otomatik yok sayılır. Etiketlerin sırası ve sayısı 4 dilde de aynı olmalı (örn. 1. satır her dilde "Tarih" olmalı).</p>
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
