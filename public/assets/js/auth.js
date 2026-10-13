/**
 * auth.js — Gestión de Autenticación con Supabase (Email) y Firebase (Google)
 */
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

// --- CONFIGURACIÓN SUPABASE ---
const SUPABASE_URL = 'https://bctasjnqxdtrdcdjjkex.supabase.co';
const SUPABASE_ANON_KEY = 'sb_publishable_-aNJTAuFjvSxdM-Vgw8rmg_SzjeNoOR';

// --- CONFIGURACIÓN FIREBASE (Para Google Auth) ---
const firebaseConfig = {
  apiKey: "AIzaSyC5fApwA3PF1iDmr4c6IdwdZUpljoXY1cU",
  authDomain: "kaijuu-store.firebaseapp.com",
  projectId: "kaijuu-store",
  storageBucket: "kaijuu-store.firebasestorage.app",
  messagingSenderId: "332737994734",
  appId: "1:332737994734:web:2564482c24aaea5d7538ff",
  measurementId: "G-162S76KCDH"
};

// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const firebaseAuth = getAuth(app);
const googleProvider = new GoogleAuthProvider();

document.addEventListener('DOMContentLoaded', () => {
  // ── Elements ────────────────────────────────────────────────────────────
  const authModal = document.getElementById('auth-modal');
  const authCloseBtn = document.getElementById('auth-close-btn');
  const btnOpenLogin = document.getElementById('btn-open-login');
  
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');
  const authTitle = document.getElementById('auth-title');
  const goToRegisterBtn = document.getElementById('go-to-register');
  const goToLoginBtn = document.getElementById('go-to-login');

  const btnGoogleLogin = document.getElementById('btn-google-login');
  const btnGoogleRegister = document.getElementById('btn-google-register');

  const menuLoggedOut = document.getElementById('menu-auth-logged-out');
  const menuLoggedIn = document.getElementById('menu-auth-logged-in');
  const authUserEmail = document.getElementById('auth-user-email');
  const btnLogout = document.getElementById('btn-logout');

  // Password visibility toggle
  const toggleBtns = document.querySelectorAll('.toggle-password');
  toggleBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const input = e.target.previousElementSibling;
      if (input.type === 'password') {
        input.type = 'text';
        e.target.textContent = '🙈';
      } else {
        input.type = 'password';
        e.target.textContent = '👁️';
      }
    });
  });

  // ── Modal Logic ──────────────────────────────────────────────────────────
  const openAuthModal = () => {
    const menuSidebar = document.getElementById('menu-sidebar');
    if (menuSidebar && menuSidebar.classList.contains('is-open')) {
      menuSidebar.classList.remove('is-open');
      menuSidebar.style.opacity = '';
    }

    authModal.classList.add('is-open');
    anime.timeline({ easing: 'easeOutExpo' })
      .add({ targets: authModal, opacity: [0, 1], duration: 300 })
      .add({ targets: authModal.querySelector('.bolsa-content'), scale: [0.8, 1], opacity: [0, 1], duration: 500, rotate: [-2, 0] }, '-=200');
  };

  const closeAuthModal = () => {
    anime.timeline({ easing: 'easeInQuad' })
      .add({ targets: authModal.querySelector('.bolsa-content'), scale: [1, 0.9], opacity: [1, 0], duration: 300 })
      .add({ targets: authModal, opacity: [1, 0], duration: 300, complete: () => {
        authModal.classList.remove('is-open');
        authModal.querySelector('.bolsa-content').style.opacity = '';
        authModal.querySelector('.bolsa-content').style.transform = '';
      } }, '-=150');
  };

  btnOpenLogin?.addEventListener('click', openAuthModal);
  authCloseBtn?.addEventListener('click', closeAuthModal);
  authModal?.addEventListener('click', (e) => {
    if (e.target === authModal) closeAuthModal();
  });

  goToRegisterBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    loginForm.style.display = 'none';
    registerForm.style.display = 'flex';
    authTitle.innerHTML = '¡ÚNETE<br>AL Gremio!';
  });

  goToLoginBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    registerForm.style.display = 'none';
    loginForm.style.display = 'flex';
    authTitle.innerHTML = '¡INICIA TU<br>AVENTURA!';
  });

  // ── Auth Logic ───────────────────────────────────────────────────────────
  const updateNavZenBalance = async () => {
    const navZenBadge = document.getElementById('nav-zen-badge');
    const navZenBalance = document.getElementById('nav-zen-balance');
    const profileZenBalance = document.getElementById('profile-zen-balance');
    
    if (!localStorage.getItem('kaijuu_session')) {
      if (navZenBadge) navZenBadge.style.display = 'none';
      return;
    }
    
    try {
      const res = await fetch('/api/v1/monedero/saldo');
      if (res.ok) {
        const data = await res.json();
        const bal = parseFloat(data.balance_zen || 0).toFixed(2);
        if (navZenBalance) navZenBalance.textContent = bal;
        if (profileZenBalance) profileZenBalance.textContent = bal;
        if (navZenBadge) navZenBadge.style.display = 'inline-flex';
      }
    } catch (e) {
      console.error("Error fetching Zen balance:", e);
    }
  };

  const checkSession = () => {
    const session = localStorage.getItem('kaijuu_session');
    
    // Elements from Navbar
    const navAvatarName = document.getElementById('nav-avatar-name');
    const navAvatarImg = document.getElementById('nav-avatar-img');

    if (session) {
      document.cookie = `kaijuu_session=${encodeURIComponent(session)}; path=/; max-age=8640000; SameSite=Lax`;
      const user = JSON.parse(session);
      const email = user.email;
      const username = user.name ? user.name.split(' ')[0] : email.split('@')[0];
      const photo = user.photo || `https://ui-avatars.com/api/?name=${username}&background=random`;

      if (menuLoggedOut) menuLoggedOut.style.display = 'none';
      if (menuLoggedIn) menuLoggedIn.style.display = 'flex';
      if (authUserEmail) authUserEmail.textContent = username.toUpperCase();
      
      // Update Navbar
      if (navAvatarName) navAvatarName.textContent = username.toUpperCase();
      if (navAvatarImg) navAvatarImg.src = photo;

      updateNavZenBalance();

    } else {
      document.cookie = `kaijuu_session=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
      if (menuLoggedOut) menuLoggedOut.style.display = 'block';
      if (menuLoggedIn) menuLoggedIn.style.display = 'none';
      
      // Update Navbar
      if (navAvatarName) navAvatarName.textContent = 'ENTRAR';
      if (navAvatarImg) navAvatarImg.src = 'https://ui-avatars.com/api/?name=Invitado&background=000&color=fff';
      
      const navZenBadge = document.getElementById('nav-zen-badge');
      if (navZenBadge) navZenBadge.style.display = 'none';
    }
  };

  checkSession();

  btnLogout?.addEventListener('click', () => {
    localStorage.removeItem('kaijuu_session');
    document.cookie = `kaijuu_session=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
    checkSession();
  });

  // ── Supabase Auth (Email / Password) ─────────────────────────────────────
  loginForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = loginForm.email.value;
    const password = loginForm.password.value;
    const btn = loginForm.querySelector('button[type="submit"]');
    btn.innerHTML = '<span>CARGANDO...</span>';

    try {
      const res = await fetch(`${SUPABASE_URL}/auth/v1/token?grant_type=password`, {
        method: 'POST',
        headers: {
          'apikey': SUPABASE_ANON_KEY,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
      });
      const data = await res.json();
      
      if (res.ok) {
        let userId = data.user?.id || null;
        // Fetch user from public.users to get UUID if needed
        try {
          const uRes = await fetch(`${SUPABASE_URL}/rest/v1/users?email=eq.${encodeURIComponent(email)}`, {
            headers: { 'apikey': SUPABASE_ANON_KEY }
          });
          const uData = await uRes.json();
          if (uData && uData[0]?.id) userId = uData[0].id;
        } catch(e) {}

        const sessData = { id: userId, email: data.user.email, token: data.access_token, provider: 'supabase' };
        localStorage.setItem('kaijuu_session', JSON.stringify(sessData));
        document.cookie = `kaijuu_session=${encodeURIComponent(JSON.stringify(sessData))}; path=/; max-age=8640000; SameSite=Lax`;
        checkSession();
        closeAuthModal();
        loginForm.reset();
      } else {
        alert('Error: ' + (data.error_description || data.msg || 'Credenciales incorrectas'));
      }
    } catch (err) {
      alert('Error de conexión.');
    } finally {
      btn.innerHTML = '<span>INICIAR SESIÓN</span>';
    }
  });

  registerForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = registerForm.email.value;
    const password = registerForm.password.value;
    const confirm = registerForm.confirm_password.value;

    document.getElementById('pwd-error').style.display = 'none';
    document.getElementById('confirm-error').style.display = 'none';

    if (password.length < 6) {
      document.getElementById('pwd-error').style.display = 'block';
      return;
    }
    if (password !== confirm) {
      document.getElementById('confirm-error').style.display = 'block';
      return;
    }

    const btn = registerForm.querySelector('button[type="submit"]');
    btn.innerHTML = '<span>CARGANDO...</span>';

    try {
      const res = await fetch(`${SUPABASE_URL}/auth/v1/signup`, {
        method: 'POST',
        headers: {
          'apikey': SUPABASE_ANON_KEY,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
      });
      const data = await res.json();
      
      if (res.ok) {
        // Sincronizar usuario con Supabase (public.users)
        try {
          await fetch(`${SUPABASE_URL}/rest/v1/users`, {
            method: 'POST',
            headers: {
              'apikey': SUPABASE_ANON_KEY,
              'Content-Type': 'application/json',
              'Prefer': 'resolution=ignore-duplicates' // Ignora si el correo ya existe
            },
            body: JSON.stringify({
              email: email,
              role: 'customer'
            })
          });
        } catch (e) {
          console.error("Error guardando en Supabase public.users:", e);
        }

        alert('¡Registro exitoso! Ya puedes iniciar sesión.');
        goToLoginBtn.click();
        registerForm.reset();
      } else {
        alert('Error: ' + (data.error_description || data.msg || 'No se pudo registrar'));
      }
    } catch (err) {
      alert('Error de conexión.');
    } finally {
      btn.innerHTML = '<span>CREAR CUENTA Z</span>';
    }
  });

  // ── Firebase Auth (Google Sign-In) ───────────────────────────────────────
  const handleGoogleLogin = async () => {
    try {
      const result = await signInWithPopup(firebaseAuth, googleProvider);
      const user = result.user;
      
      // Sincronizar usuario con Supabase (public.users)
      let userId = null;
      try {
        await fetch(`${SUPABASE_URL}/rest/v1/users`, {
          method: 'POST',
          headers: {
            'apikey': SUPABASE_ANON_KEY,
            'Content-Type': 'application/json',
            'Prefer': 'resolution=ignore-duplicates' // Ignora si el correo ya existe
          },
          body: JSON.stringify({
            email: user.email,
            role: 'customer'
          })
        });

        const uRes = await fetch(`${SUPABASE_URL}/rest/v1/users?email=eq.${encodeURIComponent(user.email)}`, {
          headers: { 'apikey': SUPABASE_ANON_KEY }
        });
        const uData = await uRes.json();
        if (uData && uData[0]?.id) userId = uData[0].id;
      } catch (e) {
        console.error("Error guardando en Supabase:", e);
      }

      // Guardar sesión
      const sessData = { 
        id: userId,
        email: user.email, 
        name: user.displayName,
        photo: user.photoURL,
        token: user.accessToken,
        provider: 'firebase-google'
      };
      localStorage.setItem('kaijuu_session', JSON.stringify(sessData));
      document.cookie = `kaijuu_session=${encodeURIComponent(JSON.stringify(sessData))}; path=/; max-age=8640000; SameSite=Lax`;

      checkSession();
      closeAuthModal();
      
    } catch (error) {
      console.error("Error en Google Sign-In:", error);
      if (error.code === 'auth/invalid-api-key') {
         alert("¡Hola Desarrollador! Tienes que reemplazar 'TU_API_KEY' en auth.js con tus credenciales reales de Firebase.");
      } else {
         alert('Error al iniciar sesión con Google.');
      }
    }
  };

  btnGoogleLogin?.addEventListener('click', handleGoogleLogin);
  btnGoogleRegister?.addEventListener('click', handleGoogleLogin);
});
