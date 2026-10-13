/**
 * app.js — Bootstrap global: navbar, cart sidebar, add-to-cart delegates
 */
import { initCartSidebar, updateCartCount, addItem, removeItem, updateQty, renderCartSidebar } from './cart.js';

document.addEventListener('DOMContentLoaded', () => {

  // ── Cart init ───────────────────────────────────────────────────────────
  updateCartCount();
  initCartSidebar();

  // ── Navbar hamburger / Menu Sidebar ───────────────────────────────────────
  const hamburger = document.getElementById('hamburger-btn');
  const menuSidebar = document.getElementById('menu-sidebar');
  const menuCloseBtn = document.getElementById('menu-close-btn');

  if (hamburger && menuSidebar) {
    const openMenu = () => {
      menuSidebar.classList.add('is-open');
      hamburger.setAttribute('aria-expanded', 'true');
      anime.timeline({ easing: 'easeOutExpo' })
        .add({
          targets: menuSidebar,
          opacity: [0, 1],
          duration: 300
        })
        .add({
          targets: menuSidebar.querySelector('.bolsa-content'),
          translateX: [400, 0],
          opacity: [0, 1],
          duration: 500
        }, '-=200');
    };

    const closeMenu = () => {
      anime.timeline({ easing: 'easeInQuad' })
        .add({
          targets: menuSidebar.querySelector('.bolsa-content'),
          translateX: [0, 400],
          opacity: [1, 0],
          duration: 300
        })
        .add({
          targets: menuSidebar,
          opacity: [1, 0],
          duration: 300,
          complete: () => {
            menuSidebar.classList.remove('is-open');
            hamburger.setAttribute('aria-expanded', 'false');
            menuSidebar.style.opacity = '';
            menuSidebar.querySelector('.bolsa-content').style.opacity = '';
            menuSidebar.querySelector('.bolsa-content').style.transform = '';
          }
        }, '-=150');
    };

    hamburger.addEventListener('click', openMenu);
    menuCloseBtn?.addEventListener('click', closeMenu);
    menuSidebar.addEventListener('click', (e) => {
      // Solo cerrar si hacen clic exactamente en el fondo oscuro
      if (e.target === menuSidebar) closeMenu();
    });
  }

  // ── Navbar scroll shrink ────────────────────────────────────────────────
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.style.background = window.scrollY > 50
        ? 'rgba(10,10,15,.98)'
        : 'rgba(10,10,15,.85)';
    }, { passive: true });
  }

  // ── Global "add-to-cart" button delegation ──────────────────────────────
  document.body.addEventListener('click', e => {
    const btn = e.target.closest('.add-cart-btn, #add-to-cart-btn');
    if (!btn || btn.disabled) return;
    e.preventDefault();
    const qtyEl = document.getElementById('qty-value');
    addItem({
      id:    btn.dataset.id,
      name:  btn.dataset.name,
      price: parseFloat(btn.dataset.price),
      image: btn.dataset.image,
      qty:   qtyEl ? parseInt(qtyEl.textContent, 10) : 1,
    });
    // Micro feedback animation
    if (window.anime) {
      anime({
        targets: btn,
        scale: [1, 0.9, 1.05, 1],
        duration: 400,
        easing: 'easeOutElastic(1, .6)',
      });
    }
  });

  // ── Qty selector (product detail) ──────────────────────────────────────
  const minusBtn = document.getElementById('qty-minus');
  const plusBtn  = document.getElementById('qty-plus');
  const qtyEl    = document.getElementById('qty-value');
  if (minusBtn && plusBtn && qtyEl) {
    minusBtn.addEventListener('click', () => {
      const n = parseInt(qtyEl.textContent, 10);
      if (n > 1) qtyEl.textContent = n - 1;
    });
    plusBtn.addEventListener('click', () => {
      const n = parseInt(qtyEl.textContent, 10);
      if (n < 99) qtyEl.textContent = n + 1;
    });
  }

  // ── Cart sidebar item actions ────────────────────────────────────────────
  document.addEventListener('click', e => {
    const removeBtn = e.target.closest('.cart-item-remove');
    const minusBtn  = e.target.closest('.cart-qty-minus');
    const plusBtn2  = e.target.closest('.cart-qty-plus');
    
    if (removeBtn) {
      removeItem(removeBtn.dataset.id);
      renderCartSidebar();
    }
    
    if (minusBtn) {
      const id = minusBtn.dataset.id;
      const span = minusBtn.nextElementSibling;
      const qty = parseInt(span.textContent, 10);
      if (qty > 1) {
        updateQty(id, qty - 1);
        renderCartSidebar();
      }
    }
    
    if (plusBtn2) {
      const id = plusBtn2.dataset.id;
      const span = plusBtn2.previousElementSibling;
      const qty = parseInt(span.textContent, 10);
      if (qty < 99) {
        updateQty(id, qty + 1);
        renderCartSidebar();
      }
    }
  });
});
