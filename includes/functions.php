<?php
// MUKANTARA — ortak PHP render fonksiyonları.
// site-data.json dosyasını okuyup statik HTML sayfalarını üretir.

define('DATA_PATH', __DIR__ . '/../data/site-data.json');
define('SITE_ROOT', __DIR__ . '/..');
define('LANGS', ['tr', 'en', 'es', 'ar']);
define('RTL_LANGS', ['ar']);

function load_data() {
    $json = file_get_contents(DATA_PATH);
    return json_decode($json, true);
}

function save_data($data) {
    $ok = file_put_contents(DATA_PATH, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    return $ok !== false;
}

function slugify($text) {
    $map = ['ı'=>'i','İ'=>'i','ş'=>'s','Ş'=>'s','ğ'=>'g','Ğ'=>'g','ü'=>'u','Ü'=>'u','ö'=>'o','Ö'=>'o','ç'=>'c','Ç'=>'c'];
    $text = strtr($text, $map);
    $text = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// ---------------------------------------------------------------
// HTML parça üreticileri
// ---------------------------------------------------------------

function tpl_head($title, $aroot, $htmllang, $dir) {
    return <<<HTML
<!DOCTYPE html>
<html lang="{$htmllang}" dir="{$dir}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$title} — MUKANTARA</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,340;0,9..144,480;1,9..144,340;1,9..144,420&family=Newsreader:ital,wght@0,400;0,500;1,400&family=IBM+Plex+Mono:wght@400;500&family=Noto+Naskh+Arabic:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{$aroot}assets/css/style.css">
</head>
<body>

HTML;
}

function tpl_nav($u, $aroot, $lroot, $active) {
    $a = function($key, $href, $label) use ($active) {
        $cls = $key === $active ? ' class="active"' : '';
        return "<a href=\"{$href}\"{$cls}>{$label}</a>";
    };
    $all_works = htmlspecialchars($u['all_works']);
    $galleries = htmlspecialchars($u['galleries']);
    $quote_nav = htmlspecialchars($u['quote_nav']);
    $sister = htmlspecialchars($u['sister']);
    return <<<HTML
<header class="site solid">
  <div class="wrap navbar">
    <a href="{$lroot}index.html" class="wordmark">Mukantara</a>
    <nav class="links">
      <a href="{$lroot}eserler/index.html">{$all_works}</a>
      <a href="{$lroot}index.html#galeriler">{$galleries}</a>
      <a href="{$lroot}teklif.html">{$quote_nav}</a>
      <div class="lang-switch">
        {$a('tr', $aroot.'index.html', 'TR')}<span class="divider">/</span>
        {$a('en', $aroot.'en/index.html', 'EN')}<span class="divider">/</span>
        {$a('ar', $aroot.'ar/index.html', 'AR')}<span class="divider">/</span>
        {$a('es', $aroot.'es/index.html', 'ES')}
      </div>
      <a class="trlink" href="https://www.mukantara.com.tr" target="_blank" rel="noopener">{$sister}</a>
    </nav>
  </div>
</header>

HTML;
}

function tpl_whatsapp($u) {
    $msg = rawurlencode($u['whatsapp_msg']);
    $label = htmlspecialchars($u['whatsapp_text']);
    return <<<HTML
<a class="whatsapp-float" href="https://wa.me/905539789212?text={$msg}" target="_blank" rel="noopener" aria-label="{$label}">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l1.4-4.1A7.9 7.9 0 1 1 9 18.5L4 20Z"/><path d="M8.5 10.2c0 3 2.6 5.6 5.6 5.6"/></svg>
  <span>{$label}</span>
</a>
HTML;
}

function tpl_footer($u, $aroot, $lroot) {
    $foot_desc = htmlspecialchars($u['foot_desc']);
    $foot_sister = htmlspecialchars($u['foot_sister']);
    $foot_galleries = htmlspecialchars($u['foot_galleries']);
    $all_works = htmlspecialchars($u['all_works']);
    $foot_categories = htmlspecialchars($u['foot_categories']);
    $foot_contact = htmlspecialchars($u['foot_contact']);
    $address = $u['address'];
    $wa = tpl_whatsapp($u);
    return <<<HTML
<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div>
        <p class="foot-word">Mukantara</p>
        <p style="margin-top:10px; max-width:320px;">{$foot_desc}</p>
        <a class="sister-link" href="https://www.mukantara.com.tr" target="_blank" rel="noopener">{$foot_sister}</a>
      </div>
      <div>
        <h4>{$foot_galleries}</h4>
        <p><a href="{$lroot}eserler/index.html">{$all_works}</a></p>
        <p><a href="{$lroot}index.html#galeriler">{$foot_categories}</a></p>
      </div>
      <div>
        <h4>{$foot_contact}</h4>
        <p>{$address}</p>
        <p>+90 553 978 92 12</p>
      </div>
    </div>
    <div class="bottom-bar">
      <span>© 2026 MUKANTARA Bilim Kültür Sanat A.Ş.</span>
      <span>www.mukantara.com · www.mukantara.com.tr</span>
    </div>
  </div>
</footer>
{$wa}
<script src="{$aroot}assets/js/site.js"></script>
</body>
</html>
HTML;
}

// ---------------------------------------------------------------
// Sayfa üreticileri
// ---------------------------------------------------------------

function render_product_page($p, $lang, $data) {
    $u = $data['ui'][$lang];
    $aroot = $lang !== 'tr' ? '../../' : '../';
    $lroot = '../';
    $title = $p['title'][$lang];
    $slides = '';
    foreach ($p['images'] as $img) {
        $slides .= "<div class=\"slide\"><img src=\"{$aroot}assets/img/{$img}\" alt=\"" . htmlspecialchars($title) . "\"></div>\n";
    }
    $facts_html = '';
    foreach ($p['facts'] as $f) {
        $facts_html .= "<div>{$f['label'][$lang]}<b>{$f['value'][$lang]}</b></div>\n";
    }
    $desc_html = '';
    foreach ($p['desc'][$lang] as $d) { $desc_html .= "<p>{$d}</p>\n"; }
    $note_html = '';
    if (!empty($p['note'][$lang])) { $note_html = '<p class="category-note">' . $p['note'][$lang] . '</p>'; }
    $cat_name = $data['category_i18n'][$p['category']][$lang] ?? $p['category'];

    // önceki / sonraki eser
    $slugs = array_column($data['products'], 'slug');
    $idx = array_search($p['slug'], $slugs);
    $prev = $data['products'][($idx - 1 + count($slugs)) % count($slugs)];
    $next = $data['products'][($idx + 1) % count($slugs)];

    $home_lbl = htmlspecialchars($u['home']);
    $all_works = htmlspecialchars($u['all_works']);
    $inv_no = htmlspecialchars($u['inv_no']);
    $quote_cta = htmlspecialchars($u['quote_cta']);
    $back_all = htmlspecialchars($u['back_all']);
    $category_label = htmlspecialchars($u['category_label']);
    $prev_lbl = htmlspecialchars($u['prev']);
    $next_lbl = htmlspecialchars($u['next']);
    $quote_url = $lroot . 'teklif.html?eser=' . rawurlencode($title);

    $body = <<<HTML

<div class="breadcrumb wrap">
  <a href="{$lroot}index.html">{$home_lbl}</a><span class="sep">/</span>
  <a href="{$lroot}eserler/index.html">{$all_works}</a><span class="sep">/</span>
  <span style="color:var(--label);">{$title}</span>
</div>

<div class="wrap">
  <div class="gallery">
    <div class="gallery-track">
      {$slides}
    </div>
    <div class="gallery-nav"><button class="prev">‹</button><button class="next">›</button></div>
    <div class="gallery-dots"></div>
  </div>

  <div class="plate">
    <div class="cachet">
      <span class="dot"></span>
      <span class="num">{$p['envanter_no']}</span>
      <span class="lbl">{$inv_no}</span>
    </div>
    <h1>{$title}</h1>
    <div class="desc">
      {$desc_html}
    </div>
    <div class="facts">
      {$facts_html}
    </div>
    {$note_html}
    <div class="cta-row">
      <a href="{$quote_url}" class="btn btn-dark">{$quote_cta}</a>
      <a href="{$lroot}eserler/index.html" class="btn btn-line">{$back_all}</a>
    </div>
    <p class="category-note" style="margin-top:26px;">{$category_label} <a href="{$lroot}eserler/index.html">{$cat_name}</a></p>
  </div>
</div>

<div class="walk-nav">
  <a class="prev" href="{$prev['slug']}.html">
    <div class="thumb"><img src="{$aroot}assets/img/{$prev['images'][0]}" alt=""></div>
    <div class="meta"><span class="tag">{$prev_lbl}</span><h4>{$prev['title'][$lang]}</h4></div>
  </a>
  <a class="next" href="{$next['slug']}.html">
    <div class="thumb"><img src="{$aroot}assets/img/{$next['images'][0]}" alt=""></div>
    <div class="meta"><span class="tag">{$next_lbl}</span><h4>{$next['title'][$lang]}</h4></div>
  </a>
</div>
HTML;

    $dir = in_array($lang, RTL_LANGS) ? 'rtl' : 'ltr';
    return tpl_head($title, $aroot, $lang, $dir) . tpl_nav($u, $aroot, $lroot, $lang) . $body . tpl_footer($u, $aroot, $lroot);
}

function render_catalog_page($lang, $data) {
    $u = $data['ui'][$lang];
    $aroot = $lang !== 'tr' ? '../../' : '../';
    $lroot = '../';
    $cats = array_values(array_unique(array_column($data['products'], 'category')));
    $filter_all = htmlspecialchars($u['filter_all']);
    $filters = "<button class=\"active\" data-cat=\"all\">{$filter_all}</button>\n";
    foreach ($cats as $c) {
        $cn = $data['category_i18n'][$c][$lang] ?? $c;
        $filters .= "<button data-cat=\"{$c}\">{$cn}</button>\n";
    }
    $cards = '';
    foreach ($data['products'] as $p) {
        $cn = $data['category_i18n'][$p['category']][$lang] ?? $p['category'];
        $t = htmlspecialchars($p['title'][$lang]);
        $cards .= <<<HTML
<a class="catalog-card" data-cat="{$p['category']}" href="{$p['slug']}.html">
  <div class="thumb"><img src="{$aroot}assets/img/{$p['images'][0]}" alt="{$t}"></div>
  <div class="info"><span class="tag">{$cn}</span><h3>{$t}</h3></div>
</a>
HTML;
    }
    $eyebrow = htmlspecialchars($u['catalog_eyebrow']);
    $title = htmlspecialchars($u['catalog_title']);
    $sub = htmlspecialchars($u['catalog_sub']);
    $count = count($data['products']);
    $body = <<<HTML

<section style="padding-top:170px;">
  <div class="wrap">
    <div class="section-head">
      <span class="tag">{$eyebrow}</span>
      <h2>{$title}</h2>
      <p>{$count} {$sub}</p>
    </div>
    <div class="filter-bar">{$filters}</div>
    <div class="catalog-grid">
      {$cards}
    </div>
  </div>
</section>
HTML;
    $dir = in_array($lang, RTL_LANGS) ? 'rtl' : 'ltr';
    return tpl_head($u['all_works'], $aroot, $lang, $dir) . tpl_nav($u, $aroot, $lroot, $lang) . $body . tpl_footer($u, $aroot, $lroot);
}

function render_teklif_page($lang, $data) {
    $u = $data['ui'][$lang];
    $aroot = $lang !== 'tr' ? '../' : '';
    $lroot = '';
    $options = '';
    foreach ($data['products'] as $p) {
        $t = htmlspecialchars($p['title'][$lang]);
        $options .= "<option value=\"{$t}\">{$t}</option>\n";
    }
    $other = htmlspecialchars($u['form_other']);
    $eyebrow = htmlspecialchars($u['quote_eyebrow']);
    $title = htmlspecialchars($u['quote_title']);
    $qbody = htmlspecialchars($u['quote_body']);
    $address = $u['address'];
    $body = <<<HTML

<section class="offer" style="padding-top:170px;">
  <div class="wrap offer-inner">
    <div>
      <span class="tag">{$eyebrow}</span>
      <h2>{$title}</h2>
      <p>{$qbody}</p>
      <p style="margin-top:30px; color:var(--label);">{$address}</p>
      <p>+90 553 978 92 12</p>
    </div>
    <form class="teklif" action="gonder.php" method="post">
      <div class="row2">
        <div><label for="ad">{$u['form_ad']}</label><input type="text" id="ad" name="ad" required></div>
        <div><label for="telefon">{$u['form_tel']}</label><input type="tel" id="telefon" name="telefon" required></div>
      </div>
      <label for="eposta">{$u['form_mail']}</label>
      <input type="email" id="eposta" name="eposta" required>
      <label for="urun">{$u['form_urun']}</label>
      <select id="urun" name="urun">
        {$options}
        <option value="{$other}">{$other}</option>
      </select>
      <label for="mesaj">{$u['form_mesaj']}</label>
      <textarea id="mesaj" name="mesaj" placeholder="{$u['form_mesaj_ph']}"></textarea>
      <button type="submit">{$u['form_submit']}</button>
    </form>
  </div>
</section>
HTML;
    $dir = in_array($lang, RTL_LANGS) ? 'rtl' : 'ltr';
    return tpl_head($u['quote_nav'], $aroot, $lang, $dir) . tpl_nav($u, $aroot, $lroot, $lang) . $body . tpl_footer($u, $aroot, $lroot);
}

function render_home_page($lang, $data) {
    $u = $data['ui'][$lang];
    $h = $data['home'][$lang];
    $aroot = $lang !== 'tr' ? '../' : '';
    $lroot = '';

    // 3 galeri kapısı — her kategoriden temsili bir görsel
    $cats = array_values(array_unique(array_column($data['products'], 'category')));
    $doors = '';
    $i = 1;
    foreach ($cats as $c) {
        $rep = null;
        foreach ($data['products'] as $p) { if ($p['category'] === $c) { $rep = $p; break; } }
        if (!$rep) continue;
        $cn = $data['category_i18n'][$c][$lang] ?? $c;
        $img = $rep['images'][0];
        $doors .= <<<HTML
<a class="door" href="{$lroot}eserler/index.html">
  <img src="assets/img/{$img}" alt="{$cn}">
  <div class="door-label"><span class="tag">{$u['galleries']} {$i}</span><h3>{$cn}</h3></div>
</a>
HTML;
        $i++;
    }

    // sergi gezisi — öne çıkan ürünler
    $showcases = '';
    $n = 1;
    foreach ($data['featured_slugs'] as $slug) {
        $p = null;
        foreach ($data['products'] as $pp) { if ($pp['slug'] === $slug) { $p = $pp; break; } }
        if (!$p) continue;
        $cn = $data['category_i18n'][$p['category']][$lang] ?? $p['category'];
        $title = htmlspecialchars($p['title'][$lang]);
        $desc = htmlspecialchars($p['desc'][$lang][0] ?? '');
        $facts = '';
        $shown = array_slice($p['facts'], 0, 4);
        foreach ($shown as $f) {
            $facts .= "<div>{$f['label'][$lang]}<b>{$f['value'][$lang]}</b></div>\n";
        }
        $numLbl = str_pad($n, 2, '0', STR_PAD_LEFT);
        $showcases .= <<<HTML
<div class="showcase">
  <div class="sc-image"><img src="assets/img/{$p['images'][0]}" alt="{$title}"></div>
  <div class="sc-plate">
    <span class="tag">{$u['inv_no']} {$numLbl} · {$cn}</span>
    <h3>{$title}</h3>
    <p>{$desc}</p>
    <div class="sc-facts">{$facts}</div>
    <a href="eserler/{$p['slug']}.html" class="cta">{$title} →</a>
  </div>
</div>
HTML;
        $n++;
    }

    $hero_tag = htmlspecialchars($h['hero_tag']);
    $curator_tag = htmlspecialchars($h['curator_tag']);
    $curator_body = htmlspecialchars($h['curator_body']);
    $all_works = htmlspecialchars($u['all_works']);
    $body = <<<HTML

<section class="hero">
  <div class="hero-bg"><img src="assets/img/{$h['hero_image']}" alt=""></div>
  <div class="wrap hero-inner">
    <div class="hero-plate">
      <span class="tag">{$hero_tag}</span>
      <h1>{$h['hero_title']}</h1>
      <p>{$h['hero_body']}</p>
      <a href="#sergi" class="cta">↓</a>
    </div>
  </div>
</section>

<section class="manifesto">
  <div class="wrap">
    <span class="tag">{$curator_tag}</span>
    <p class="big">{$h['curator_quote']}</p>
    <p class="small">{$curator_body}</p>
  </div>
</section>

<section id="galeriler">
  <div class="wrap">
    <div class="section-head">
      <span class="tag">{$u['galleries']}</span>
      <h2>{$u['catalog_title']}</h2>
    </div>
  </div>
  <div class="doors">
    {$doors}
  </div>
</section>

<section id="sergi" style="padding-top:0;">
  <div class="wrap" style="margin-bottom:80px;">
    <div class="section-head">
      <span class="tag">{$u['catalog_eyebrow']}</span>
      <h2>{$u['catalog_title']}</h2>
      <p><a href="eserler/index.html" style="color:var(--brass-light); text-decoration:underline;">{$all_works}</a></p>
    </div>
  </div>
  {$showcases}
  <div class="wrap" style="text-align:center; padding-top:70px;">
    <a href="eserler/index.html" class="btn btn-line" style="border:1px solid var(--hairline); color:var(--label);">{$all_works} →</a>
  </div>
</section>

<section class="offer" id="teklif">
  <div class="wrap offer-inner">
    <div>
      <span class="tag">{$u['quote_eyebrow']}</span>
      <h2>{$u['quote_title']}</h2>
      <p>{$u['quote_body']}</p>
      <p style="margin-top:30px; color:var(--label);">{$u['address']}</p>
      <p>+90 553 978 92 12</p>
    </div>
    <div>
      <a href="teklif.html" class="btn btn-dark" style="background:var(--brass); color:var(--ink);">{$u['quote_nav']} →</a>
    </div>
  </div>
</section>
HTML;
    $dir = in_array($lang, RTL_LANGS) ? 'rtl' : 'ltr';
    return tpl_head('MUKANTARA', $aroot, $lang, $dir) . tpl_nav($u, $aroot, $lroot, $lang) . $body . tpl_footer($u, $aroot, $lroot);
}

// ---------------------------------------------------------------
// Ana üretim fonksiyonu — admin panelinden "Yayınla" ile çağrılır
// ---------------------------------------------------------------
function generate_all() {
    $data = load_data();
    foreach (LANGS as $lang) {
        $langDir = $lang === 'tr' ? SITE_ROOT : SITE_ROOT . "/{$lang}";
        @mkdir("{$langDir}/eserler", 0755, true);

        foreach ($data['products'] as $p) {
            file_put_contents("{$langDir}/eserler/{$p['slug']}.html", render_product_page($p, $lang, $data));
        }
        file_put_contents("{$langDir}/eserler/index.html", render_catalog_page($lang, $data));
        file_put_contents("{$langDir}/teklif.html", render_teklif_page($lang, $data));
        file_put_contents("{$langDir}/index.html", render_home_page($lang, $data));
    }
    return true;
}
