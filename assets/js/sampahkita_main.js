function load_navbar() {
    fetch("../../assets/html/navbar.html")
        .then(response => response.text())
        .then(data => {
            const navbar = document.getElementById("navbar-sk");
            navbar.innerHTML = data;
        })
}
load_navbar();


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
setupMobileMenu();

function check_session() {
    fetch("../../api/user_profile")
        .then(response => response.json())
        .then(data_profile => {
            const session = document.getElementById("session");

            if (data_profile.status == "error") {
                session.innerHTML = `
              <a href="login.html"
                class="hidden md:block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                Login
              </a>
            `;
            } else {
                session.innerHTML = `
            <a href="profile.html">
              <button
                class="hidden md:block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                Profil
              </button>
            </a>
            `;
            }
            setupMobileMenu();
        })
        .catch(error => {
            console.error('Error checking session:', error);
            setupMobileMenu();
        });
}
check_session();