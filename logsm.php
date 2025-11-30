<?php require_once __DIR__ . '/config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Inventaris</title>
        <link rel="stylesheet" href="content/style/stylelogsm.css">
        <link rel="stylesheet" href="content/style/header.css">
        <link rel="stylesheet" href="content/style/notifications.css">
</head>
<body>
    <header class="header">
        <div class="headerkiri">
        <div class="logo-container">
            <img src="content/stripheader.png" alt="Logo" class="logo-clickable">
            <div class="dropdown-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="gudangInv.php">Ruang Inventaris</a>
                <a href="logsm.php">Data Log</a>
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
        <div class="section">
            <h2>Notification</h2>
            <div id="notifications" class="notification-container">
                <div class="notification-empty">Tidak ada notifikasi</div>
            </div>
        </div>

        <div class="section">
            <h2>History Data Air Quality</h2>
            <div class="table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Tanggal / Jam </th>
                            <th>Kelembapan</th>
                            <th>Suhu</th>
                        </tr>
                    </thead>
                    <tbody id="sensor-table-body">
                        <tr>
                            <td colspan="4" class="text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <button class="back-btn" onclick="goBack()">BACK</button>
</body>

<script>
    // Configuration for sensor thresholds
    const THRESHOLDS = {
        temperature: { min: 18, max: 28 },
        humidity: { min: 30, max: 80 }
    };
    const NOTIFICATION_DURATION = 5000; // ms, 0 = unlimited

    // Track notified data to avoid duplicate notifications
    const notifiedData = new Set();

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

    const deviceId = "esp32-unit-001";
    const tableBody = document.getElementById("sensor-table-body");
    const notificationsContainer = document.getElementById("notifications");

    /**
     * Menampilkan notifikasi
     * @param {string} message - Pesan notifikasi
     * @param {string} type - Tipe notifikasi: 'success', 'warning', 'error', 'info'
     * @param {number} duration - Durasi tampil (ms), 0 = unlimited
     */
    function showNotification(message, type = 'info', duration = NOTIFICATION_DURATION) {
        // Hapus pesan "Tidak ada notifikasi" jika ada
        const emptyMsg = notificationsContainer.querySelector('.notification-empty');
        if (emptyMsg) {
            emptyMsg.remove();
        }

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close">×</button>
        `;

        // Close button handler
        notification.querySelector('.notification-close').addEventListener('click', function() {
            removeNotification(notification);
        });

        notificationsContainer.appendChild(notification);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                removeNotification(notification);
            }, duration);
        }
    }

    /**
     * Menghapus notifikasi dengan animasi
     */
    function removeNotification(notificationEl) {
        notificationEl.classList.add('removing');
        setTimeout(() => {
            notificationEl.remove();
            // Show empty message jika tidak ada notifikasi lagi
            if (notificationsContainer.children.length === 0) {
                notificationsContainer.innerHTML = '<div class="notification-empty">Tidak ada notifikasi</div>';
            }
        }, 300);
    }

    /**
     * Check sensor data terhadap threshold dan generate notifikasi
     */
    function checkThresholds(data) {
        const uniqueKey = `${data.recorded_at}-${data.value}-${data.raw_value}`;

        // Skip jika sudah di-notify untuk data yang sama
        if (notifiedData.has(uniqueKey)) {
            return;
        }

        const temperature = parseFloat(data.value);
        const humidity = parseFloat(data.raw_value);

        // Check temperature
        if (temperature < THRESHOLDS.temperature.min) {
            showNotification(
                `⚠️ Suhu Rendah: ${temperature}°C (minimum: ${THRESHOLDS.temperature.min}°C)`,
                'warning'
            );
            notifiedData.add(uniqueKey);
        } else if (temperature > THRESHOLDS.temperature.max) {
            showNotification(
                `⚠️ Suhu Tinggi: ${temperature}°C (maksimum: ${THRESHOLDS.temperature.max}°C)`,
                'warning'
            );
            notifiedData.add(uniqueKey);
        }

        // Check humidity
        if (humidity < THRESHOLDS.humidity.min) {
            showNotification(
                `⚠️ Kelembapan Rendah: ${humidity}% (minimum: ${THRESHOLDS.humidity.min}%)`,
                'warning'
            );
            notifiedData.add(uniqueKey);
        } else if (humidity > THRESHOLDS.humidity.max) {
            showNotification(
                `⚠️ Kelembapan Tinggi: ${humidity}% (maksimum: ${THRESHOLDS.humidity.max}%)`,
                'warning'
            );
            notifiedData.add(uniqueKey);
        }
    }

    async function loadData() {
      try {
        const response = await fetch(`http://localhost/project_iot/api_pdo/get_sensor_data_airsys.php?device_id=${deviceId}`);
        const datas = await response.json();

        if (!datas || datas.length === 0) {
          tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Tidak Ada Data</td></tr>`;
          return;
        }

        // Check latest data untuk notifikasi (ambil yang paling baru)
        if (datas.length > 0) {
            checkThresholds(datas[0]);
        }

        tableBody.innerHTML = datas.map((data, index) => `
          <tr>
            <td>${data.recorded_at}</td>
            <td>${data.raw_value}</td>
            <td>${data.value}</td>
          </tr>
        `).join('');
      } catch (error) {
        tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>`;
        console.error(error);
      }
    }

    // Load data pertama kali
    loadData();

    // Perbarui setiap 5 detik
    setInterval(loadData, 5000);
</script>
</html>