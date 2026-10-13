/**
 * cart.js — Gestión del carrito con localStorage + Anime.js
 */

const CART_KEY = 'kaijuu_cart';

export function getCart() {
  try { return JSON.parse(localStorage.getItem(CART_KEY)) ?? []; }
  catch { return []; }
}

export function saveCart(items) {
  localStorage.setItem(CART_KEY, JSON.stringify(items));
  updateCartCount();
}

export function addItem(item) {
  const cart = getCart();
  const existing = cart.find(i => i.id === item.id);
  if (existing) {
    existing.qty = Math.min(existing.qty + (item.qty ?? 1), 99);
  } else {
    cart.push({ ...item, qty: item.qty ?? 1 });
  }
  saveCart(cart);
  animateCartBounce();
}

export function removeItem(id) {
  saveCart(getCart().filter(i => i.id !== id));
}

export function updateQty(id, qty) {
  const cart = getCart();
  const item = cart.find(i => i.id === id);
  if (item) {
    item.qty = Math.max(1, qty);
    saveCart(cart);
  }
}

export function clearCart() {
  localStorage.removeItem(CART_KEY);
  updateCartCount();
}

export function getTotal() {
  return getCart().reduce((sum, i) => sum + i.price * i.qty, 0);
}

export function getTotalFormatted() {
  return `${getTotal().toFixed(2)} Zeni`;
}

export function updateCartCount() {
  const count = getCart().reduce((s, i) => s + i.qty, 0);
  document.querySelectorAll('#cart-count').forEach(el => {
    el.textContent = count;
    el.style.display = count > 0 ? 'inline-flex' : 'none';
  });
}

function animateCartBounce() {
  const btn = document.getElementById('cart-toggle-btn');
  if (!btn || !window.anime) return;
  anime({
    targets: btn,
    scale: [1, 1.3, 1],
    duration: 500,
    easing: 'easeOutElastic(1, .5)',
  });
}

// ── Sidebar cart ─────────────────────────────────────────────────────────────
export function renderCartSidebar() {
  const container = document.getElementById('cart-items');
  const totalEl   = document.getElementById('cart-total');
  if (!container) return;

  const items = getCart();
  container.innerHTML = '';

  if (items.length === 0) {
    container.innerHTML = `<p class="empty-state" style="padding:1rem">Tu carrito está vacío 😢</p>`;
  } else {
    items.forEach(item => {
      const el = document.createElement('div');
      el.className = 'bolsa-item';
      el.dataset.id = item.id;
      el.innerHTML = `
        <img src="${item.image}" alt="${item.name}"/>
        <div class="bolsa-item__info">
          <p style="font-size: 1.4rem;">${item.name}</p>
          <p>PRECIO: ${item.price} Zeni</p>
          <div style="display:flex; align-items:center; gap:10px;">
            <p>CANTIDAD: </p>
            <button class="qty-btn cart-qty-minus" data-id="${item.id}" style="background:var(--clr-border);color:white;padding:0 5px;font-family:var(--font-comic);">-</button>
            <span>${item.qty}</span>
            <button class="qty-btn cart-qty-plus" data-id="${item.id}" style="background:var(--clr-border);color:white;padding:0 5px;font-family:var(--font-comic);">+</button>
          </div>
          <p>SUBTOTAL: ${item.price * item.qty} Zeni</p>
        </div>
        <button class="bolsa-item__remove cart-item-remove" data-id="${item.id}">✕</button>
      `;
      container.appendChild(el);
    });
    // Animate items in
    if (window.anime) {
      anime({
        targets: '.bolsa-item',
        translateX: [40, 0],
        opacity:    [0, 1],
        delay:      anime.stagger(60),
        duration:   400,
        easing:     'easeOutQuad',
      });
    }
  }

  if (totalEl) totalEl.textContent = getTotalFormatted();
}

// ── Cart Sidebar toggle ───────────────────────────────────────────────────────
export function initCartSidebar() {
  const sidebar   = document.getElementById('cart-sidebar');
  const openBtn   = document.getElementById('cart-toggle-btn');
  const closeBtn  = document.getElementById('cart-close-btn');
  const overlay   = document.getElementById('cart-overlay');
  const continueBtn = document.getElementById('cart-continue-btn'); // El botón "¡SEGUIR BUSCANDO!"
  if (!sidebar) return;

  const open = () => {
    renderCartSidebar();
    sidebar.classList.add('is-open');
    openBtn?.setAttribute('aria-expanded', 'true');
    // Animación de entrada profesional
    anime.timeline({ easing: 'easeOutExpo' })
      .add({
        targets: sidebar,
        opacity: [0, 1],
        duration: 300
      })
      .add({
        targets: '.bolsa-content',
        scale: [0.5, 1],
        opacity: [0, 1],
        duration: 500,
        rotate: [-5, 0]
      }, '-=200');
  };

  const close = () => {
    // Animación de salida profesional
    anime.timeline({ easing: 'easeInQuad' })
      .add({
        targets: '.bolsa-content',
        scale: [1, 0.8],
        opacity: [1, 0],
        duration: 300
      })
      .add({
        targets: sidebar,
        opacity: [1, 0],
        duration: 300,
        complete: () => {
          sidebar.classList.remove('is-open');
          openBtn?.setAttribute('aria-expanded', 'false');
          // Reset inline styles applied by anime
          document.querySelector('.bolsa-content').style.opacity = '';
          document.querySelector('.bolsa-content').style.transform = '';
          sidebar.style.opacity = '';
        }
      }, '-=150');
  };

  openBtn?.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  continueBtn?.addEventListener('click', close);
  sidebar?.addEventListener('click', (e) => {
    // Solo cerrar si hacen clic exactamente en el fondo oscuro
    if (e.target === sidebar) close();
  });
}

// Exponer globalmente en window para llamadas inline o retrocompatibilidad
window.__kaijuCart = {
  getCart,
  saveCart,
  addItem,
  removeItem,
  updateQty,
  clearCart,
  getTotal,
  renderCartSidebar
};
