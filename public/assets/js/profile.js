/**
 * profile.js — Gestión del Perfil del Cliente
 */
document.addEventListener('DOMContentLoaded', () => {
  const profileModal = document.getElementById('profile-modal');
  const profileCloseBtn = document.getElementById('profile-close-btn');
  const btnLogoutProfile = document.getElementById('profile-logout-btn');
  
  // Elements inside modal
  const profileUsername = document.getElementById('profile-username');
  const profileFullname = document.getElementById('profile-fullname');
  const profileEmail = document.getElementById('profile-email');
  const profileAvatarHeader = document.getElementById('profile-avatar-header');
  const profileAvatarLarge = document.getElementById('profile-avatar-large');

  // Trigger buttons (In the UI there might be multiple "MI CUENTA" links)
  // For now, we attach it to the "Hola, [USER]" span inside the menu-sidebar, or a specific "Mi Cuenta" button if it exists.
  
  const btnOpenProfile = document.getElementById('btn-open-profile');

  const openProfileModal = () => {
    // Cerrar menú o carrito si están abiertos
    const menuSidebar = document.getElementById('menu-sidebar');
    const cartSidebar = document.getElementById('cart-sidebar');
    if (menuSidebar && menuSidebar.classList.contains('is-open')) {
      menuSidebar.classList.remove('is-open');
      menuSidebar.style.opacity = '';
    }
    if (cartSidebar && cartSidebar.classList.contains('is-open')) {
      cartSidebar.classList.remove('is-open');
      cartSidebar.style.opacity = '';
    }

    // Cargar datos de la sesión
    const sessionStr = localStorage.getItem('kaijuu_session');
    if (sessionStr) {
      const session = JSON.parse(sessionStr);
      const email = session.email;
      const username = session.name ? session.name.split(' ')[0] : email.split('@')[0];
      const fullname = session.name || username;
      const photo = session.photo || `https://ui-avatars.com/api/?name=${username}&background=random`;

      if(profileUsername) profileUsername.textContent = username.toUpperCase();
      if(profileFullname) profileFullname.textContent = fullname;
      if(profileEmail) profileEmail.textContent = email;
      if(profileAvatarHeader) profileAvatarHeader.src = photo;
      if(profileAvatarLarge) profileAvatarLarge.src = photo;
    }

    profileModal.classList.add('is-open');
    anime.timeline({ easing: 'easeOutExpo' })
      .add({ targets: profileModal, opacity: [0, 1], duration: 300 })
      .add({ targets: profileModal.querySelector('.bolsa-content'), scale: [0.8, 1], opacity: [0, 1], duration: 500 }, '-=200');
  };

  const closeProfileModal = () => {
    anime.timeline({ easing: 'easeInQuad' })
      .add({ targets: profileModal.querySelector('.bolsa-content'), scale: [1, 0.9], opacity: [1, 0], duration: 300 })
      .add({ targets: profileModal, opacity: [1, 0], duration: 300, complete: () => {
        profileModal.classList.remove('is-open');
        profileModal.querySelector('.bolsa-content').style.opacity = '';
        profileModal.querySelector('.bolsa-content').style.transform = '';
      } }, '-=150');
  };

  profileCloseBtn?.addEventListener('click', closeProfileModal);
  btnOpenProfile?.addEventListener('click', openProfileModal);
  profileModal?.addEventListener('click', (e) => {
    if (e.target === profileModal) closeProfileModal();
  });

  // Navbar Avatar logic
  const btnNavbarProfile = document.getElementById('btn-navbar-profile');
  btnNavbarProfile?.addEventListener('click', () => {
    const sessionStr = localStorage.getItem('kaijuu_session');
    if (sessionStr) {
      const session = JSON.parse(sessionStr);
      if (session.email === 'admin_kaijuu@gmail.com' || session.role === 'admin') {
        window.location.href = '/admin';
      } else {
        openProfileModal();
      }
    } else {
      document.getElementById('btn-open-login')?.click();
    }
  });

  // Tabs logic
  const profileTabs = document.querySelectorAll('.profile-tab');
  const tabContents = document.querySelectorAll('.profile-tab-content');
  
  profileTabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
      profileTabs.forEach(t => t.classList.remove('active'));
      e.target.classList.add('active');
      
      const targetId = e.target.getAttribute('data-target');
      if (targetId) {
        tabContents.forEach(content => {
          content.style.display = content.id === targetId ? 'block' : 'none';
        });
      }
    });
  });

  // Edit Profile Logic
  const btnEditProfile = document.getElementById('btn-edit-profile');
  
  btnEditProfile?.addEventListener('click', () => {
    const currentName = profileFullname.textContent;
    if (btnEditProfile.textContent.trim() === 'EDITAR PERFIL') {
      // Switch to input
      profileFullname.innerHTML = `<input type="text" id="input-edit-fullname" value="${currentName}" style="font-family: var(--font-body); font-size: 1rem; padding: 2px 5px; border: 2px solid #000;">`;
      btnEditProfile.innerHTML = '<span>GUARDAR CAMBIOS</span>';
      btnEditProfile.style.background = 'var(--clr-green)';
    } else {
      // Save changes
      const newName = document.getElementById('input-edit-fullname').value;
      profileFullname.textContent = newName || currentName;
      btnEditProfile.innerHTML = '<span>EDITAR PERFIL</span>';
      btnEditProfile.style.background = 'var(--clr-orange)';
      
      // Update session storage
      const sessionStr = localStorage.getItem('kaijuu_session');
      if (sessionStr) {
        const session = JSON.parse(sessionStr);
        session.name = newName || currentName;
        localStorage.setItem('kaijuu_session', JSON.stringify(session));
        // Force UI updates
        const firstName = session.name.split(' ')[0].toUpperCase();
        document.getElementById('nav-avatar-name').textContent = firstName;
        if (document.getElementById('profile-username')) {
           document.getElementById('profile-username').textContent = firstName;
        }
      }
    }
  });

  // Exportar funcion globalmente por si necesitamos abrirlo desde un onClick
  window.openProfileModal = openProfileModal;

  // Escuchar botón de logout dentro del perfil
  btnLogoutProfile?.addEventListener('click', () => {
    localStorage.removeItem('kaijuu_session');
    // Forzar actualización del menú invocando checkSession si existe, o recargando
    window.location.reload(); 
  });
});
