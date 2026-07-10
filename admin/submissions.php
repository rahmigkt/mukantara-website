<?php
require_once __DIR__ . '/config.php';
require_login();
$data = load_data();

$logPath = __DIR__ . '/../data/teklifler.json';
$talepler = file_exists($logPath) ? json_decode(file_get_contents($logPath), true) : [];
$talepler = array_reverse($talepler);

admin_head('Teklif Talepleri');
?>
<div class="topbar">
  <span class="tag">MUKANTARA · Yönetim Paneli</span>
  <a href="index.php">← Panele Dön</a>
</div>
<div class="wrap">
  <h1>Teklif Talepleri (<?= count($talepler) ?>)</h1>
  <p class="helptext" style="margin-bottom:20px;">Bu talepler <?= htmlspecialchars($data['contact_email'] ?? '') ?> adresine e-posta olarak da gönderilmeye çalışılır; bu liste, e-posta ulaşmazsa diye bir yedektir.</p>

  <?php if (empty($talepler)): ?>
    <div class="card">Henüz teklif talebi yok.</div>
  <?php else: ?>
    <?php foreach ($talepler as $t): ?>
      <div class="card">
        <div style="display:flex; justify-content:space-between;">
          <strong><?= htmlspecialchars($t['ad']) ?></strong>
          <span class="tag"><?= htmlspecialchars($t['tarih']) ?></span>
        </div>
        <p style="margin:10px 0 4px;">📞 <?= htmlspecialchars($t['telefon']) ?> &nbsp;·&nbsp; ✉️ <?= htmlspecialchars($t['eposta']) ?></p>
        <p style="margin:4px 0;">İlgilenilen Eser: <strong><?= htmlspecialchars($t['urun']) ?></strong> (<?= htmlspecialchars(strtoupper($t['dil'])) ?>)</p>
        <?php if (!empty($t['mesaj'])): ?><p style="margin-top:10px; color:rgba(236,229,211,0.7);"><?= nl2br(htmlspecialchars($t['mesaj'])) ?></p><?php endif; ?>
        <?php if (empty($t['eposta_gonderildi'])): ?><p style="color:#e39b9b; font-size:12px; margin-top:10px;">⚠ E-posta gönderimi başarısız olmuş olabilir, sadece burada kayıtlı.</p><?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<?php admin_foot(); ?>
