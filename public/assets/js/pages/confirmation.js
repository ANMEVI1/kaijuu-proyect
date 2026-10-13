/**
 * pages/confirmation.js — Animación épica de confirmación de pedido
 */
import { clearCart } from '../cart.js';

document.addEventListener('DOMContentLoaded', () => {
  // Limpiar el carrito al confirmar
  clearCart();

  // Secuencia de animación con Anime.js
  anime.timeline({ easing: 'easeOutExpo' })
    .add({
      targets: '#confirmation-card',
      opacity: [0, 1],
      scale:   [.7, 1],
      duration: 800,
    })
    .add({
      targets: '#confirmation-icon',
      rotateZ: [-10, 10, -5, 5, 0],
      scale:   [1, 1.3, 1],
      duration: 600,
      easing: 'easeInOutBounce',
    }, '-=300');

  // Confetti-like particles
  const body = document.body;
  const colors = ['#7c3aed','#db2777','#0284c7','#d97706','#22c55e'];
  for (let i = 0; i < 80; i++) {
    const dot = document.createElement('div');
    dot.style.cssText = `
      position:fixed;
      width:8px;height:8px;
      border-radius:50%;
      background:${colors[Math.floor(Math.random()*colors.length)]};
      left:${Math.random()*100}vw;
      top:-10px;
      pointer-events:none;
      z-index:9999;
    `;
    body.appendChild(dot);
    anime({
      targets: dot,
      translateY: [0, `${50 + Math.random()*50}vh`],
      translateX: [0, `${(Math.random()-0.5)*200}px`],
      opacity:    [1, 0],
      rotate:     anime.random(-360, 360),
      duration:   anime.random(1500, 2800),
      delay:      anime.random(0, 800),
      easing:     'easeInQuad',
      complete:   () => dot.remove(),
    });
  }
});
