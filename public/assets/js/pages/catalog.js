/**
 * pages/catalog.js — Animaciones del catálogo + filtros
 */
document.addEventListener('DOMContentLoaded', () => {
  // Reveal header
  anime({
    targets: '#catalog-header',
    opacity: [0, 1],
    translateY: [30, 0],
    duration: 700,
    easing: 'easeOutExpo',
  });

  // Reveal filter chips
  anime({
    targets: '#catalog-filters .filter-chip',
    opacity: [0, 1],
    translateX: [-20, 0],
    delay: anime.stagger(60),
    duration: 500,
    easing: 'easeOutQuad',
  });

  // Reveal product cards
  anime({
    targets: '#products-grid .product-card',
    opacity: [0, 1],
    translateY: [50, 0],
    scale: [.95, 1],
    delay: anime.stagger(80),
    duration: 700,
    easing: 'easeOutQuint',
  });
});
