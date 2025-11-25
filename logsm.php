<?php require_once __DIR__ . '/config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <link rel="stylesheet" href="content/style/stylelogsm.css">
        <link rel="stylesheet" href="content/style/header.css">
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
            <div id="notifications"></div>
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

    async function loadData() {
      try {
        const response = await fetch(`http://localhost/project_iot/api_pdo/get_sensor_data_airsys.php?device_id=${deviceId}`); // PHP
        // const response = await fetch(`http://localhost:3000/api_pdo/get_sensor_data_airsys?device_id=${deviceId}`); // JS
        const datas = await response.json();

        if (!datas || datas.length === 0) {
          tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Tidak Ada Data</td></tr>`;
          return;
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