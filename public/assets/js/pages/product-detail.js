/**
 * pages/product-detail.js — Animaciones de detalle de producto
 */
document.addEventListener('DOMContentLoaded', () => {
  // Imagen slide-in from left
  anime({
    targets: '#product-image-wrap',
    opacity: [0, 1],
    translateX: [-60, 0],
    duration: 800,
    easing: 'easeOutExpo',
  });
  // Info slide-in from right
  anime({
    targets: '#product-info',
    opacity: [0, 1],
    translateX: [60, 0],
    duration: 800,
    delay: 150,
    easing: 'easeOutExpo',
  });
});
