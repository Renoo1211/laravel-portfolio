/* Interaksi tambahan: progress, typewriter, tilt, filter, lightbox, back-to-top */
(function () {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Scroll progress + back-to-top */
  const bar = document.getElementById('scrollProgress');
  const toTop = document.getElementById('toTop');
  function onScroll() {
    const max = document.documentElement.scrollHeight - window.innerHeight;
    const ratio = max > 0 ? window.scrollY / max : 0;
    if (bar) bar.style.transform = 'scaleX(' + ratio + ')';
    if (toTop) toTop.classList.toggle('show', window.scrollY > 600);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  if (toTop) toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* Typewriter */
  const typed = document.getElementById('typedText');
  if (typed && !reduceMotion) {
    let r = 0, i = 0, deleting = false;
    (function tick() {
      const roles = JSON.parse(typed.dataset.roles); // dibaca ulang tiap siklus agar ikut ganti bahasa
      const word = roles[r % roles.length];
      i += deleting ? -1 : 1;
      typed.textContent = word.slice(0, i);
      let delay = deleting ? 40 : 85;
      if (!deleting && i === word.length) { deleting = true; delay = 1600; }
      else if (deleting && i === 0) { deleting = false; r = (r + 1) % roles.length; delay = 350; }
      setTimeout(tick, delay);
    })();
  }

  /* Spotlight + 3D tilt (hanya untuk pointer mouse) */
  if (!reduceMotion && window.matchMedia('(hover: hover)').matches) {
    document.querySelectorAll('.tilt').forEach(card => {
      card.addEventListener('mousemove', e => {
        const b = card.getBoundingClientRect();
        const x = (e.clientX - b.left) / b.width;
        const y = (e.clientY - b.top) / b.height;
        card.style.setProperty('--mx', x * 100 + '%');
        card.style.setProperty('--my', y * 100 + '%');
        card.style.transform = 'perspective(900px) rotateX(' + (0.5 - y) * 7 + 'deg) rotateY(' + (x - 0.5) * 7 + 'deg) translateY(-4px)';
      });
      card.addEventListener('mouseleave', () => { card.style.transform = ''; });
    });
  }

  /* Marquee skill: gandakan kartu sampai satu set lebih lebar dari layar, lalu loop mulus */
  const MARQUEE_SPEED = 50; // px per detik
  function buildMarquee(track) {
    const wrap = track.parentElement;
    const [first, second] = track.querySelectorAll('.marquee-set');
    if (!first || !second) return;
    first.querySelectorAll('[data-clone]').forEach((n) => n.remove());
    const originals = [...first.children];
    for (let k = 0; first.getBoundingClientRect().width < wrap.clientWidth && k < 12; k++) {
      originals.forEach((o) => { const c = o.cloneNode(true); c.dataset.clone = '1'; first.appendChild(c); });
    }
    // set kedua = salinan persis set pertama, sehingga translateX(-50%) kembali ke posisi identik
    second.replaceChildren(...[...first.children].map((n) => { const c = n.cloneNode(true); c.dataset.clone = '1'; return c; }));
    track.style.animationDuration = Math.round(first.getBoundingClientRect().width / MARQUEE_SPEED) + 's';
  }
  const tracks = document.querySelectorAll('.marquee-track');
  const rebuildMarquee = () => tracks.forEach(buildMarquee);
  (document.fonts ? document.fonts.ready : Promise.resolve()).then(rebuildMarquee);
  let marqueeW = window.innerWidth, marqueeTimer;
  window.addEventListener('resize', () => {
    if (window.innerWidth === marqueeW) return; // abaikan resize vertikal (mis. bar alamat di HP)
    marqueeW = window.innerWidth;
    clearTimeout(marqueeTimer);
    marqueeTimer = setTimeout(rebuildMarquee, 200);
  });

  /* Filter project */
  const chips = document.querySelectorAll('.filter-chip');
  const cards = document.querySelectorAll('.project-card[data-cats]');
  chips.forEach(chip => chip.addEventListener('click', () => {
    chips.forEach(c => c.classList.toggle('active', c === chip));
    const f = chip.dataset.filter;
    cards.forEach(card => {
      const show = f === 'all' || card.dataset.cats.split(',').includes(f);
      card.classList.toggle('is-hidden', !show);
      card.classList.remove('pop-in');
      if (show) { void card.offsetWidth; card.classList.add('pop-in'); }
    });
  }));

  /* Lightbox sertifikat */
  const box = document.getElementById('lightbox');
  if (box) {
    const img = document.getElementById('lightboxImg');
    const cap = document.getElementById('lightboxCap');
    let lastFocus = null;

    function open(wrap) {
      const src = wrap.querySelector('img');
      lastFocus = wrap;
      img.src = src.currentSrc || src.src;
      img.alt = src.alt;
      cap.textContent = src.alt;
      box.hidden = false;
      requestAnimationFrame(() => box.classList.add('open'));
      document.body.style.overflow = 'hidden';
      box.querySelector('.lightbox-close').focus();
    }
    function close() {
      box.classList.remove('open');
      document.body.style.overflow = '';
      setTimeout(() => { box.hidden = true; }, 250);
      if (lastFocus) lastFocus.focus();
    }

    document.querySelectorAll('[data-lightbox]').forEach(wrap => {
      wrap.addEventListener('click', () => open(wrap));
      wrap.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(wrap); }
      });
    });
    box.addEventListener('click', e => { if (e.target !== img) close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !box.hidden) close(); });
  }
})();
