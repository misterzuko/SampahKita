// =======================================================
// DOM ELEMENTS
// =======================================================
const deleteModal = document.getElementById('delete-modal');
const confirmDeleteButton = document.getElementById('confirm-delete-button');
const cancelDeleteButton = document.getElementById('cancel-delete-button');
const postToDeleteIdInput = document.getElementById('post-to-delete-id');
const postNewButton = document.getElementById('post-new-button'); // Tombol Posting baru
const newPostInput = document.getElementById('new-post-input'); // Input Post baru
const toastContainer = document.getElementById('toast-container'); // Container Toast

// =======================================================
// TOAST BAR FUNCTION
// =======================================================

/**
 * Menampilkan Toast Bar di pojok kanan atas.
 * @param {string} message - Pesan yang akan ditampilkan.
 * @param {string} type - Tipe pesan ('success' atau 'error').
 * @param {number} duration - Durasi tampilan (ms). Default: 3000ms.
 */
function showToast(message, type = 'success', duration = 3000) {
  const icon =
    type === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle';
  const colorClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';

  // Buat elemen toast
  const toast = document.createElement('div');
  toast.className = `toast-bar ${colorClass} text-white px-4 py-3 rounded-lg shadow-xl max-w-xs`;
  toast.innerHTML = `
          <div class="flex items-center gap-3">
            <i class="${icon} text-lg"></i>
            <span class="font-semibold text-sm">${message}</span>
          </div>
        `;

  // Tambahkan toast ke bagian atas container
  toastContainer.prepend(toast);

  // Paksa reflow agar CSS transition bekerja
  void toast.offsetWidth;

  // Tampilkan toast
  toast.classList.add('show');

  // Sembunyikan dan hapus toast setelah durasi
  setTimeout(() => {
    toast.classList.remove('show');
    toast.classList.add('hide');

    // Hapus elemen dari DOM setelah transisi selesai
    toast.addEventListener('transitionend', () => {
      if (toast.classList.contains('hide')) {
        toast.remove();
      }
    });
  }, duration);
}

// =======================================================
// DROP DOWN LOGIC (Toggle dan Outside Click)
// =======================================================
function toggleDropdown(button) {
  const targetId = button.getAttribute('data-target-id');
  const menu = document.getElementById(targetId);
  const isExpanded = button.getAttribute('aria-expanded') === 'true';

  // 1. Tutup SEMUA dropdown yang sedang terbuka
  document
    .querySelectorAll('[data-dropdown-toggle][aria-expanded="true"]')
    .forEach(openButton => {
      if (openButton !== button) {
        const openMenu = document.getElementById(
          openButton.getAttribute('data-target-id')
        );
        openButton.setAttribute('aria-expanded', 'false');
        if (openMenu) openMenu.classList.add('hidden');
      }
    });

  // 2. Toggle (Buka/Tutup) dropdown yang ditargetkan
  if (menu) {
    if (isExpanded) {
      button.setAttribute('aria-expanded', 'false');
      menu.classList.add('hidden');
    } else {
      button.setAttribute('aria-expanded', 'true');
      menu.classList.remove('hidden');
    }
  }
}

// 3. Event Listener untuk semua tombol toggle
document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
  button.addEventListener('click', event => {
    event.stopPropagation();
    toggleDropdown(button);
  });
});

// 4. Penanganan "Outside Click" untuk menutup semua dropdown
document.addEventListener('click', event => {
  document
    .querySelectorAll('[data-dropdown-toggle][aria-expanded="true"]')
    .forEach(button => {
      const targetId = button.getAttribute('data-target-id');
      const menu = document.getElementById(targetId);

      // Cek apakah klik TIDAK berasal dari tombol ATAU menu itu sendiri
      if (
        !button.contains(event.target) &&
        menu &&
        !menu.contains(event.target)
      ) {
        button.setAttribute('aria-expanded', 'false');
        menu.classList.add('hidden');
      }
    });
});

// =======================================================
// MODAL & POST ACTIONS LOGIC
// =======================================================

// SIMULASI: Tambah Post (Tombol Posting)
postNewButton.addEventListener('click', () => {
  const postText = newPostInput.value.trim();
  if (postText) {
    // Simulasi kirim data ke server (Asumsi berhasil)
    console.log('Posting baru:', postText);

    // Tampilkan Toast Sukses
    showToast('Postingan berhasil ditambahkan!');

    // Bersihkan input
    newPostInput.value = '';

    // Tambahkan logika untuk menambahkan elemen post baru ke DOM di sini
  } else {
    showToast('Isi postingan tidak boleh kosong.', 'error');
  }
});

// 5. Penanganan Aksi (Delete) yang memicu Modal
document.addEventListener('click', event => {
  const actionButton = event.target.closest('[data-action]');
  if (actionButton) {
    const action = actionButton.getAttribute('data-action');
    const postContainer = actionButton.closest('.feed-post');
    const postId = postContainer
      ? postContainer.getAttribute('data-post-id')
      : null;

    // Tutup menu dropdown setelah aksi dipilih
    const menu = actionButton.closest('div[role="menu"]');
    const toggleButton = menu
      ? document.querySelector(`[data-target-id="${menu.id}"]`)
      : null;
    if (toggleButton) {
      toggleButton.setAttribute('aria-expanded', 'false');
      menu.classList.add('hidden');
    }

    if (action === 'delete' && postId) {
      postToDeleteIdInput.value = postId;
      deleteModal.showModal();
    } else if (action === 'report') {
      alert('Anda memilih Laporkan untuk ID: ' + postId);
    }
  }
});

// 6. Logika Tutup Modal via Backdrop (Outside Click)
deleteModal.addEventListener('click', event => {
  const dialogDimensions = deleteModal.getBoundingClientRect();
  if (
    event.clientX < dialogDimensions.left ||
    event.clientX > dialogDimensions.right ||
    event.clientY < dialogDimensions.top ||
    event.clientY > dialogDimensions.bottom
  ) {
    deleteModal.close('backdrop');
  }
});

// 7. Logika Tutup Modal via Tombol Batal
cancelDeleteButton.addEventListener('click', () => {
  deleteModal.close('batal');
});

// 8. Logika Konfirmasi Hapus
confirmDeleteButton.addEventListener('click', () => {
  deleteModal.close('hapus');
});

// 9. Tindakan Pasca-Tutup Modal
deleteModal.addEventListener('close', () => {
  const postId = postToDeleteIdInput.value;
  const result = deleteModal.returnValue;

  if (result === 'hapus' && postId) {
    // --- LOGIKA HAPUS POSTINGAN YANG SEBENARNYA DI SINI ---
    console.log(`Menghapus postingan dengan ID: ${postId}...`);

    // Tampilkan Toast Sukses Penghapusan
    showToast(`Postingan ID ${postId} berhasil dihapus!`);

    // Opsional: Hapus elemen DOM post di sini
    // document.querySelector(`[data-post-id="${postId}"]`).remove();
  } else {
    console.log(`Aksi dibatalkan. Return value: ${result}`);
  }
  postToDeleteIdInput.value = '';
});
