const locations = [
  {
    id: 1,
    name: 'Bank Sampah Melati Bersih',
    address: 'Jl. Mawar No. 12, Jakarta Pusat',
    lat: -6.175,
    lng: 106.828,
    phone: '0812-1111-2222',
  },
  {
    id: 2,
    name: 'EcoWaste Center Kemang',
    address: 'Jl. Kemang Raya No. 88, Jakarta Selatan',
    lat: -6.261,
    lng: 106.812,
    phone: '0812-3333-4444',
  },
  {
    id: 3,
    name: 'Bank Sampah Hijau Lestari',
    address: 'Jl. Cendrawasih No. 45, Jakarta Barat',
    lat: -6.16,
    lng: 106.75,
    phone: '0812-5555-6666',
  },
  {
    id: 4,
    name: 'Kolektor Kelapa Gading',
    address: 'Jl. Boulevard Raya, Kelapa Gading, Jakarta Utara',
    lat: -6.155,
    lng: 106.901,
    phone: '0812-7777-8888',
  },
  {
    id: 5,
    name: 'Bank Sampah Menteng',
    address: 'Jl. Teuku Umar, Menteng, Jakarta Pusat',
    lat: -6.195,
    lng: 106.83,
    phone: '0812-9999-0000',
  },
];

let map;

// Menggunakan fa-map-marker-alt yang memiliki bentuk pin yang lebih baik
const fontAwesomeIcon = L.divIcon({
  html: '<i class="fas fa-map-marker-alt fa-3x fa-map-marker-alt-custom"></i>',
  iconSize: [30, 42], // Sesuaikan ukuran ikon
  iconAnchor: [15, 42], // Posisikan agar ujung pin tepat di koordinat
  popupAnchor: [0, -35], // Posisikan popup di atas pin
  className: 'custom-map-marker', // Kelas CSS khusus untuk menargetkan ikon
});

function initMap() {
  // Inisialisasi peta Leaflet, berpusat di Jakarta
  map = L.map('map').setView([-6.208, 106.845], 12);

  // Tambahkan tile layer dari OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution:
      '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(map);

  // Tambahkan marker untuk setiap lokasi
  locations.forEach(location => {
    // Buat Popup
    const popupContent = `
            <div class="p-1">
              <h3 class="font-bold text-base mb-1">${location.name}</h3>
              <p class="text-sm text-gray-600">${location.address}</p>
              <p class="text-sm text-gray-600 mt-1">
                <i class="fas fa-phone-alt mr-2 text-green-600"></i>${location.phone}
              </p>
            </div>`;

    // Buat Marker dan ikat Popup
    L.marker([location.lat, location.lng], {
      icon: fontAwesomeIcon,
    })
      .addTo(map)
      .bindPopup(popupContent);
  });
}

initMap();
