/**
 * pages/home.js — Animaciones de la página principal con Anime.js
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── Partículas flotantes en el Hero ────────────────────────────────────
  const particleContainer = document.getElementById('hero-particles');
  if (particleContainer) {
    const count = 60;
    const emojis = ['✨','⭐','🌸','💜','🌀','🎌','🔮'];
    for (let i = 0; i < count; i++) {
      const p = document.createElement('span');
      p.textContent = emojis[Math.floor(Math.random() * emojis.length)];
      p.style.cssText = `
        position: absolute;
        font-size: ${Math.random() * 16 + 8}px;
        left: ${Math.random() * 100}%;
        top:  ${Math.random() * 100}%;
        opacity: 0;
        pointer-events: none;
        user-select: none;
      `;
      particleContainer.appendChild(p);
    }
    anime({
      targets: '#hero-particles span',
      opacity:     [0, .6, 0],
      translateY:  () => anime.random(-60, 60),
      translateX:  () => anime.random(-40, 40),
      scale:       () => [.5, anime.random(.8, 1.4)],
      delay:       anime.stagger(100, { from: 'random' }),
      duration:    () => anime.random(3000, 6000),
      loop:        true,
      easing:      'easeInOutSine',
    });
  }

  // ── Hero text cascade ──────────────────────────────────────────────────
  const tl = anime.timeline({ easing: 'easeOutExpo' });
  tl
    .add({ targets: '#hero-eyebrow',  opacity: [0,1], translateY: [20,0], duration: 700 })
    .add({ targets: '#hero-title',    opacity: [0,1], translateY: [40,0], duration: 800 }, '-=400')
    .add({ targets: '#hero-subtitle', opacity: [0,1], translateY: [20,0], duration: 600 }, '-=400')
    .add({ targets: '#hero-cta',      opacity: [0,1], translateY: [20,0], duration: 500 }, '-=300');

  // ── Featured products stagger ──────────────────────────────────────────
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      anime({
        targets: '#products-grid .product-card',
        opacity:    [0, 1],
        translateY: [50, 0],
        scale:      [.95, 1],
        delay:      anime.stagger(80),
        duration:   700,
        easing:     'easeOutQuint',
      });
      observer.disconnect();
    });
  }, { threshold: .1 });
  const grid = document.getElementById('products-grid');
  if (grid) observer.observe(grid);

  // ── Categories stagger ─────────────────────────────────────────────────
  const catObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      anime({
        targets: '#categories-grid .category-card',
        opacity:    [0, 1],
        translateY: [30, 0],
        rotateZ:    [-3, 0],
        delay:      anime.stagger(100),
        duration:   600,
        easing:     'easeOutBack',
      });
      catObserver.disconnect();
    });
  }, { threshold: .1 });
  const catGrid = document.getElementById('categories-grid');
  if (catGrid) catObserver.observe(catGrid);

  // ── Value props stagger ────────────────────────────────────────────────
  const valObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      anime({
        targets: '#value-grid .value-card',
        opacity:    [0, 1],
        scale:      [.85, 1],
        delay:      anime.stagger(120),
        duration:   600,
        easing:     'easeOutElastic(1, .8)',
      });
      valObserver.disconnect();
    });
  }, { threshold: .1 });
  const valGrid = document.getElementById('value-grid');
  if (valGrid) valObserver.observe(valGrid);
});
