/*
 * Ganti bahasa tanpa reload.
 * Halaman bahasa lain diambil lewat fetch, lalu teks & atribut yang berubah disalin ke DOM yang sedang tampil.
 * Struktur HTML /id dan /en identik, jadi node dipasangkan berurutan. Elemen yang dikelola JS lain
 * (kartu 3D, counter, efek ketik, lightbox) tidak disentuh agar animasinya tidak ter-reset.
 * Jika ada yang tidak cocok, jatuh ke navigasi biasa.
 */
(function () {
  const SKIP = '.marquee-wrapper, .counter, #typedText, #lightbox, #idcard3d, #intro, #scrollProgress, script, style, noscript';
  const ATTRS = ['alt', 'aria-label', 'placeholder', 'title', 'data-roles', 'data-card', 'data-err-send', 'data-err-conn', 'hreflang'];
  const inSkip = (node) => {
    const el = node.nodeType === 1 ? node : node.parentElement;
    return !!el && !!el.closest(SKIP);
  };

  function textNodes(root) {
    const list = [];
    const w = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
    for (let n = w.nextNode(); n; n = w.nextNode()) if (!inSkip(n)) list.push(n);
    return list;
  }
  function elements(root) {
    return [...root.querySelectorAll('*')].filter((el) => !el.closest(SKIP));
  }

  let busy = false;

  async function switchTo(url, push) {
    if (busy) return;
    busy = true;
    try {
      const res = await fetch(url, { headers: { Accept: 'text/html' } });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const next = new DOMParser().parseFromString(await res.text(), 'text/html');

      const curText = textNodes(document.body), newText = textNodes(next.body);
      const curEls = elements(document.body), newEls = elements(next.body);
      if (curText.length !== newText.length || curEls.length !== newEls.length) throw new Error('struktur berbeda');

      curText.forEach((n, i) => { if (n.nodeValue !== newText[i].nodeValue) n.nodeValue = newText[i].nodeValue; });
      curEls.forEach((el, i) => {
        const src = newEls[i];
        for (const a of ATTRS) {
          if (src.hasAttribute(a)) { if (el.getAttribute(a) !== src.getAttribute(a)) el.setAttribute(a, src.getAttribute(a)); }
          else if (el.hasAttribute(a)) el.removeAttribute(a);
        }
        if (el.classList.contains('lang-option')) el.className = src.className; // status aktif EN/ID
      });

      // <head>: judul, meta, canonical
      document.title = next.title;
      document.documentElement.lang = next.documentElement.lang;
      for (const sel of ['meta[name="description"]', 'meta[property^="og:"]', 'link[rel="canonical"]']) {
        const a = [...document.head.querySelectorAll(sel)], b = [...next.head.querySelectorAll(sel)];
        a.forEach((el, i) => b[i] && ['content', 'href'].forEach((k) => b[i].hasAttribute(k) && el.setAttribute(k, b[i].getAttribute(k))));
      }

      // teks kartu 3D ada di #idcard3d (dilewati di atas), salin data-card-nya agar texture digambar ulang
      const host = document.getElementById('idcard3d'), nextHost = next.getElementById('idcard3d');
      if (host && nextHost) host.dataset.card = nextHost.dataset.card;

      if (push) history.pushState({ i18n: true }, '', url);
      window.dispatchEvent(new CustomEvent('localechange', { detail: { locale: next.documentElement.lang } }));
    } catch (err) {
      console.warn('Ganti bahasa tanpa reload gagal, memuat ulang halaman.', err);
      location.href = url;
    } finally {
      busy = false;
    }
  }

  document.addEventListener('click', (e) => {
    const a = e.target.closest('a.lang-option');
    if (!a || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
    e.preventDefault();
    if (a.pathname !== location.pathname) switchTo(a.href, true);
  });

  // tombol back/forward browser
  window.addEventListener('popstate', () => switchTo(location.href, false));
})();
