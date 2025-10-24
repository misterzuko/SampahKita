const imageUploadInput = document.getElementById('image-upload');
const imagePreview = document.getElementById('image-preview');
const deleteButton = document.getElementById('delete-button');
const deleteModal = document.getElementById('delete-modal');
const confirmDeleteButton = document.getElementById('confirm-delete-button');
const cancelDeleteButton = document.getElementById('cancel-delete-button');
const editForm = document.getElementById('edit-form');

// Fungsi untuk menampilkan pratinjau gambar baru
imageUploadInput.addEventListener('change', event => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      imagePreview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
});

// 1. Tambahkan event listener pada modal untuk mendeteksi klik
deleteModal.addEventListener('click', event => {
  // Cek apakah target klik adalah elemen <dialog> itu sendiri,
  // BUKAN konten di dalamnya
  const dialogDimensions = deleteModal.getBoundingClientRect();
  if (
    event.clientX < dialogDimensions.left ||
    event.clientX > dialogDimensions.right ||
    event.clientY < dialogDimensions.top ||
    event.clientY > dialogDimensions.bottom
  ) {
    // Jika klik berada di luar batas konten dialog, tutup modal
    deleteModal.close('backdrop');
  }
});

// 2. Tampilkan modal saat tombol Hapus diklik
deleteButton.addEventListener('click', event => {
  event.preventDefault();
  deleteModal.showModal(); // Metode bawaan <dialog> untuk menampilkan sebagai modal
});

// 3. Logika saat tombol Batal di modal diklik
cancelDeleteButton.addEventListener('click', () => {
  deleteModal.close('batal'); // Menutup modal
});

// 4. Logika saat tombol Hapus Permanen di modal diklik
confirmDeleteButton.addEventListener('click', () => {
  deleteModal.close('hapus'); // Menutup modal dan memberikan return value 'hapus'
});

// 5. Menangani penutupan modal (misalnya untuk logging atau tindakan pasca-tutup)
deleteModal.addEventListener('close', () => {
  if (deleteModal.returnValue === 'hapus') {
    console.log('Menghapus postingan...');
    // LOGIKA HAPUS POSTINGAN YANG SEBENARNYA DI SINI
    alert('Postingan (simulasi) berhasil dihapus!');
    // window.location.href = '/feed.html';
  } else if (deleteModal.returnValue === 'batal') {
    console.log('Penghapusan dibatalkan oleh user.');
  } else if (deleteModal.returnValue === 'backdrop') {
    console.log('Penghapusan dibatalkan (dialog ditutup via backdrop).');
  } else {
    // Ini akan menangani penutupan via tombol ESC
    console.log('Penghapusan dibatalkan (dialog ditutup via ESC).');
  }
});
