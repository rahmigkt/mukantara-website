// MUKANTARA — ortak site scripti (kaydırmalı galeri + katalog filtresi)

document.addEventListener('DOMContentLoaded', function () {

  /* ---------- Kaydırmalı görsel galerisi ---------- */
  document.querySelectorAll('.gallery').forEach(function (gallery) {
    const track = gallery.querySelector('.gallery-track');
    const slides = Array.from(gallery.querySelectorAll('.slide'));
    if (slides.length <= 1) { gallery.classList.add('single'); return; }

    let index = 0;
    const dotsWrap = gallery.querySelector('.gallery-dots');
    const dots = slides.map((_, i) => {
      const d = document.createElement('span');
      if (i === 0) d.classList.add('active');
      d.addEventListener('click', () => goTo(i));
      dotsWrap.appendChild(d);
      return d;
    });

    function goTo(i) {
      index = (i + slides.length) % slides.length;
      track.style.transform = `translateX(-${index * 100}%)`;
      dots.forEach((d, di) => d.classList.toggle('active', di === index));
    }

    const prevBtn = gallery.querySelector('.gallery-nav .prev');
    const nextBtn = gallery.querySelector('.gallery-nav .next');
    if (prevBtn) prevBtn.addEventListener('click', () => goTo(index - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goTo(index + 1));

    // Dokunmatik / fare ile kaydırma
    let startX = 0, isDown = false, delta = 0;
    track.addEventListener('pointerdown', (e) => { isDown = true; startX = e.clientX; track.style.transition = 'none'; });
    window.addEventListener('pointerup', () => {
      if (!isDown) return;
      isDown = false;
      track.style.transition = '';
      if (Math.abs(delta) > 60) { goTo(delta < 0 ? index + 1 : index - 1); }
      else { goTo(index); }
      delta = 0;
    });
    window.addEventListener('pointermove', (e) => {
      if (!isDown) return;
      delta = e.clientX - startX;
      track.style.transform = `translateX(calc(-${index * 100}% + ${delta}px))`;
    });
  });

  /* ---------- Katalog filtresi (Tüm Eserler sayfası) ---------- */
  const filterBar = document.querySelector('.filter-bar');
  if (filterBar) {
    const buttons = Array.from(filterBar.querySelectorAll('button'));
    const cards = Array.from(document.querySelectorAll('.catalog-card'));
    buttons.forEach((btn) => {
      btn.addEventListener('click', () => {
        buttons.forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.dataset.cat;
        cards.forEach((card) => {
          card.hidden = cat !== 'all' && card.dataset.cat !== cat;
        });
      });
    });
  }

  /* ---------- Teklif formu: eser ön-seçimi (?eser=...) ---------- */
  const urunSelect = document.getElementById('urun');
  if (urunSelect) {
    const params = new URLSearchParams(window.location.search);
    const preset = params.get('eser');
    if (preset) {
      const opt = Array.from(urunSelect.options).find(o => o.value === preset || o.text === preset);
      if (opt) urunSelect.value = opt.value;
    }
  }
});
