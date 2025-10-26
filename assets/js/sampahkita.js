fetch('../../assets/js/navbar.html')
  .then(response => response.text())
  .then(data => {
    const navbar = document.getElementById('navbar-sk');
    navbar.innerHTML = data;
    setupMobileMenu();
    check_session_navbar();
  });

//Peruntukan mobile view
function setupMobileMenu() {
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  if (mobileMenuBtn && mobileMenu) {
    const newMobileMenuBtn = mobileMenuBtn.cloneNode(true);
    mobileMenuBtn.parentNode.replaceChild(newMobileMenuBtn, mobileMenuBtn);

    const menuIcon = newMobileMenuBtn.querySelector('i');

    newMobileMenuBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');

      if (mobileMenu.classList.contains('hidden')) {
        menuIcon.classList.remove('fa-times');
        menuIcon.classList.add('fa-bars');
      } else {
        menuIcon.classList.remove('fa-bars');
        menuIcon.classList.add('fa-times');
      }
    });

    const mobileLinks = mobileMenu.querySelectorAll('a');
    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
        menuIcon.classList.remove('fa-times');
        menuIcon.classList.add('fa-bars');
      });
    });
  }
}

function check_session_navbar(i) {
  fetch(`../../api/user_profile?user_id=${i}`)
    .then(response => response.json())
    .then(data_profile => {
      const session = document.getElementById('session');
      const session_mobile = document.getElementById('session-mobile');
      if (data_profile.status == 'error') {
        session.innerHTML = `
              <a href="login"
                class="hidden md:block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                Login
              </a>
            `;
        session_mobile.href = 'login';
        session_mobile.innerText = 'Login';
      } else {
        session.innerHTML = `
            <a
  href="profile"
  class="flex items-center space-x-4 p-2 rounded-lg hover:bg-gray-100 transition"
>
  <div class="text-right flex-grow">
    <h1 class="text-sm font-semibold text-gray-800">Nama Pengguna Anda</h1>
  </div>

  <div class="w-12 h-12 flex-shrink-0">
    <img
      id="profile-picture"
              src="https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1200"
      alt="Foto Profil"
      class="w-full h-full rounded-full object-cover border-2 border-gray-300 shadow-sm"
    />
  </div>
</a>
            `;
        session_mobile.href = 'profile';
        session_mobile.innerText = 'Profile';
      }
      setupMobileMenu();
    })
    .catch(error => {
      console.error('Error checking session:', error);
      setupMobileMenu();
    });
}
