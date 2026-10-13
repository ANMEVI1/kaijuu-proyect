/**
 * pages/cart-page.js — Renderiza la página completa del carrito
 */
import { getCart, removeItem, updateQty, getTotalFormatted, saveCart } from '../cart.js';

function render() {
  const container = document.getElementById('cart-page-items');
  const emptyMsg  = document.getElementById('cart-empty-msg');
  const totalEl   = document.getElementById('summary-total');
  const subEl     = document.getElementById('summary-subtotal');
  const goBtn     = document.getElementById('go-checkout-btn');
  const items     = getCart();

  if (!container) return;
  container.innerHTML = '';

  if (items.length === 0) {
    container.innerHTML = `<div class="empty-state">
      <p>Tu carrito está vacío 😢</p>
      <a href="/catalogo" class="btn btn--primary">Ir al catálogo</a>
    </div>`;
    goBtn?.setAttribute('disabled', '');
  } else {
    goBtn?.removeAttribute('disabled');
    items.forEach(item => {
      const el = document.createElement('article');
      el.className = 'cart-page-item';
      el.dataset.id = item.id;
      el.style.cssText = `
        display:flex;align-items:center;gap:1rem;
        background:var(--grad-card);
        border:1px solid var(--clr-border);
        border-radius:var(--r-lg);
        padding:1rem;
        opacity:0;
      `;
      el.innerHTML = `
        <img src="${item.image}" alt="${item.name}"
          style="width:80px;height:80px;object-fit:cover;border-radius:var(--r-md)"/>
        <div style="flex:1">
          <p style="font-weight:700">${item.name}</p>
          <p style="color:var(--clr-text-muted);font-size:.85rem">$${item.price.toFixed(2)} c/u</p>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem">
          <button class="qty-btn cp-minus" data-id="${item.id}">−</button>
          <span style="font-weight:700;min-width:1.2rem;text-align:center">${item.qty}</span>
          <button class="qty-btn cp-plus" data-id="${item.id}">+</button>
        </div>
        <strong style="min-width:60px;text-align:right">$${(item.price*item.qty).toFixed(2)}</strong>
        <button class="cp-remove" data-id="${item.id}"
          style="color:var(--clr-secondary);font-size:1.2rem;margin-left:.5rem">✕</button>
      `;
      container.appendChild(el);
    });
    // Animate in
    anime({
      targets: '.cart-page-item',
      opacity: [0, 1],
      translateX: [-30, 0],
      delay: anime.stagger(80),
      duration: 500,
      easing: 'easeOutQuad',
    });
  }

  const total = getCart().reduce((s,i)=>s+i.price*i.qty, 0);
  if (totalEl) totalEl.textContent = `$${total.toFixed(2)}`;
  if (subEl)   subEl.textContent   = `$${total.toFixed(2)}`;
}

document.addEventListener('DOMContentLoaded', () => {
  render();

  document.addEventListener('click', e => {
    const minus  = e.target.closest('.cp-minus');
    const plus   = e.target.closest('.cp-plus');
    const remove = e.target.closest('.cp-remove');
    if (minus)  { updateQty(minus.dataset.id, parseInt(minus.nextElementSibling?.textContent??1)-1); render(); }
    if (plus)   { updateQty(plus.dataset.id,  parseInt(plus.previousElementSibling?.textContent??1)+1); render(); }
    if (remove) { removeItem(remove.dataset.id); render(); }
  });
});
