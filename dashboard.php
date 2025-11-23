<?php require_once __DIR__ . '/config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
      <link rel="stylesheet" href="content/style/header.css">
      <link rel="stylesheet" href="content/style/styledash.css">
</head>
<body>
    <header class="header">
    <div class="headerkiri">
    <div class="logo-container">
        <img src="content/stripheader.png" alt="Logo" class="logo-clickable">
        <div class="dropdown-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="gudangInv.php">Ruang Inventaris</a>
            <a href="gudangsm.php">Ruang Supply Makanan</a>
        </div>
    </div>
        <div class="title">Info Ruangan</div>
    </div>
    <div class="headertengah"> 
        <img src="content/LogoWareHouse.png" alt="Logo">
        <div class="title">O - Warehouse</div>
    </div>
    <div class="headerkanan">
        <a href="config/logout.php" title="Logout">
            <img src="content/header-powerbutton.png" alt="Logout" style="width:65px;height:65px;">
        </a>
    </div>
    </header>

    <div class="container">
        <h2 class="section-title">Cuaca Hari Ini</h2>
        <div class="weather-section">
            <div class="weather-left">
                <div class="weather-icon">🌤️</div>
                <div>
                    <div class="weather-time" id="currentTime">13.12 PM</div>
                    <div class="weather-date" id="currentDate">Selasa, 14 Oktober 2025</div>
                </div>
            </div>
            <div class="weather-right">
                <div class="loc">
                    <div class="location-name">Cerah Berawan</div>
                    <div class="location-detail">Condong Catur - Yogyakarta</div>
                </div>
                <div class= "temp">
                    <div class="temperature">29°</div>
                </div>
            </div>
        </div>

        <h2 class="section-title">Daftar Area</h2>
        <div class="area-grid" id="areaGrid">
            <!-- Area cards akan diisi oleh JavaScript -->
        </div>
    </div>

    <!-- Modal untuk tambah area -->
    <div class="modal" id="addAreaModal">
        <div class="modal-content">
            <h3 class="modal-title">Tambah Area Baru</h3>
            <form id="addAreaForm">
                <div class="form-group">
                    <label class="form-label">Nama Area</label>
                    <input type="text" class="form-input" id="areaName" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Device</label>
                    <input type="number" class="form-input" id="deviceCount" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL Gambar</label>
                    <input type="text" class="form-input" id="imageUrl" required>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>

<script>
        document.addEventListener('DOMContentLoaded', function() {
        const logo = document.querySelector('.logo-clickable');
        const dropdown = document.querySelector('.dropdown-menu');
        
        logo.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
        
        // Menutup dropdown ketika klik di luar
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.logo-container')) {
                dropdown.classList.remove('show');
            }
        });
    });

    let areas = [
            {
                id: 1,
                name: 'Gudang Supply Makanan',
                devices: 2,
                image: 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?w=400',
                active: true
            },
            {
                id: 2,
                name: 'Gudang Inventaris',
                devices: 4,
                image: 'https://images.unsplash.com/photo-1553413077-190dd305871c?w=400',
                active: true
            },
            {
                id: 4,
                name: 'Perlengkapan Gudang',
                devices: 3,
                image: 'https://images.unsplash.com/photo-1594563703937-fdc149e3a7ec?w=400',
                active: false
            }
        ];

        // Update waktu
        function updateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateStr = now.toLocaleDateString('id-ID', options);
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            
            document.getElementById('currentTime').textContent = timeStr;
            document.getElementById('currentDate').textContent = dateStr;
        }

        // Render areas
        function renderAreas() {
            const grid = document.getElementById('areaGrid');
            grid.innerHTML = '';

            areas.forEach(area => {
                const card = document.createElement('div');
                card.className = 'area-card';
                card.innerHTML = `
                    <img src="${area.image}" alt="${area.name}" class="area-image">
                    <div class="area-content">
                        <div class="area-header">
                            <div class="area-name">${area.name}</div>
                            <div class="toggle-switch ${area.active ? 'active' : ''}" onclick="toggleArea(${area.id})"></div>
                        </div>
                        <div class="device-count">${area.devices} Device</div>
                    </div>
                `;
                grid.appendChild(card);
            });

            // Tambah card untuk tambah area baru
            const addCard = document.createElement('div');
            addCard.className = 'area-card add-new';
            addCard.innerHTML = `
                <div class="add-icon">+</div>
                <div class="add-text">Tambahkan Ruangan</div>
            `;
            addCard.onclick = openModal;
            grid.appendChild(addCard);
        }

        // Toggle area status
        function toggleArea(id) {
            const area = areas.find(a => a.id === id);
            if (area) {
                area.active = !area.active;
                renderAreas();
            }
        }

        // Modal functions
        function openModal() {
            document.getElementById('addAreaModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('addAreaModal').classList.remove('active');
            document.getElementById('addAreaForm').reset();
        }

        // Handle form submit
        document.getElementById('addAreaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newArea = {
                id: areas.length + 1,
                name: document.getElementById('areaName').value,
                devices: parseInt(document.getElementById('deviceCount').value),
                image: document.getElementById('imageUrl').value,
                active: false
            };

            areas.push(newArea);
            renderAreas();
            closeModal();
        });

        // Refresh data
        function refreshData() {
            updateTime();
            renderAreas();
        }

        // Toggle menu
        function toggleMenu() {
            alert('Menu sidebar akan ditampilkan di sini');
        }

        // Initialize
        updateTime();
        renderAreas();
        setInterval(updateTime, 60000); // Update setiap menit
</script>

</html>