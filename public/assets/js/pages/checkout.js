/**
 * pages/checkout.js — Carga resumen del carrito en checkout + animaciones
 */
import { getCart, getTotalFormatted } from '../cart.js';

document.addEventListener('DOMContentLoaded', () => {
  // Populate summary
  const items   = getCart();
  const summary = document.getElementById('checkout-summary-items');
  const total   = document.getElementById('checkout-display-total');
  const cartIn  = document.getElementById('cart-items-input');
  const totalIn = document.getElementById('checkout-total-input');

  if (summary) {
    summary.innerHTML = items.length
      ? items.map(i => `
          <div style="display:flex;justify-content:space-between;font-size:.9rem;margin-bottom:5px;">
            <span>${i.name} ×${i.qty}</span>
            <span>${(i.price * i.qty).toFixed(2)} ZEN</span>
          </div>`)
        .join('')
      : '<p style="color:var(--clr-text-muted)">Carrito vacío.</p>';
  }

  const numericTotal = getCart().reduce((s,i)=>s+i.price*i.qty, 0);
  if (total)   total.textContent   = `${numericTotal.toFixed(2)} ZEN`;
  if (cartIn)  cartIn.value        = JSON.stringify(items);
  if (totalIn) totalIn.value       = numericTotal.toFixed(2);

  // Fetch Monedero ZEN Balance
  const fetchZenBalance = async () => {
    const zenBalEl = document.getElementById('checkout-zen-balance');
    if (!zenBalEl) return;
    try {
      const res = await fetch('/api/v1/monedero/saldo');
      if (res.ok) {
        const data = await res.json();
        const bal = parseFloat(data.balance_zen || 0).toFixed(2);
        zenBalEl.textContent = `${bal} ZEN`;
      } else {
        zenBalEl.textContent = 'Inicia sesión para usar ZEN';
      }
    } catch(e) {
      zenBalEl.textContent = '0.00 ZEN';
    }
  };
  fetchZenBalance();

  // Animate steps
  anime({
    targets: '.checkout-steps .step',
    opacity: [0, 1],
    translateY: [10, 0],
    delay: anime.stagger(120),
    duration: 500,
    easing: 'easeOutQuad',
  });

  // Animate form groups
  anime({
    targets: '.form-group',
    opacity: [0, 1],
    translateY: [30, 0],
    delay: anime.stagger(150),
    duration: 600,
    easing: 'easeOutExpo',
  });

  // Step active highlight on form focus
  document.querySelectorAll('fieldset').forEach(fs => {
    fs.addEventListener('focusin', () => {
      const idx = fs.id.replace('step-', '');
      document.querySelectorAll('.step').forEach((s,i) => {
        s.classList.toggle('step--active', String(i+1) === idx);
      });
    });
  });
});
