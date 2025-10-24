document.addEventListener('DOMContentLoaded', () => {
  const tabButtons = document.querySelectorAll('[data-tab]');
  const tabContents = document.querySelectorAll('.tab-content');

  // Fungsi untuk mengaktifkan tab
  function activateTab(tabName) {
    // Nonaktifkan semua tombol dan konten
    tabButtons.forEach(button => {
      button.classList.remove('tab-active');
    });

    tabContents.forEach(content => {
      content.classList.add('hidden');
    });

    // Aktifkan tombol dan konten yang dipilih
    const activeTabButton = document.querySelector(`[data-tab="${tabName}"]`);
    const activeTabContent = document.getElementById(`tab-${tabName}`);

    if (activeTabButton) {
      activeTabButton.classList.add('tab-active');
    }
    if (activeTabContent) {
      activeTabContent.classList.remove('hidden');
    }
  }

  // Tambahkan event listener ke setiap tombol tab
  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      const tabName = button.getAttribute('data-tab');
      activateTab(tabName);
    });
  });

  // Set tab default saat halaman dimuat (default: feed)
  // Note: Anda dapat mengubah ini menjadi 'scan-history' jika itu yang diinginkan
  activateTab('feed');
});
