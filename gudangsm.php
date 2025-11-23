
<?php
require_once __DIR__ . '/config/auth.php';
// contoh data dari backend (bisa diganti dari database / ESP32 API)
$kelembapan = 75;
$suhu       = 29;

// status kelembapan
if($kelembapan > 70){
    $status_kelembapan = "High";
    $color_kelembapan  = "high";
} else {
    $status_kelembapan = "Ideal";
    $color_kelembapan  = "ideal";
}

// status suhu
if($suhu < 18 || $suhu > 35){
    $status_suhu = "Warning";
    $color_suhu  = "high";
} else {
    $status_suhu = "Ideal";
    $color_suhu  = "ideal";
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Supply Makanan</title>
      <link rel="stylesheet" href="content/style/header.css">
    <link rel="stylesheet" href="content/style/stylesm.css">
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
        <a href="logout.php" title="Logout">
            <img src="content/header-powerbutton.png" alt="Logout" style="width:65px;height:65px;">
        </a>
    </div>
</header>

<section class="content">

    <!-- Info Gudang -->
    <div class="card-left">
        <div class="card-left-top">
            <h2 class="titlesuhulembab">Gudang Supply Makanan</h2>
                <div class="device-row">
                    <div class="device-icon">
                        <img src="content/LogoGudangMakanan.png" alt="Logo">
                    </div>
                    <div class="devicedesc">
                        <div class="deviceline">

                            <p >2 Device</p>
                        </div>
                        <div class="statusline">
                            <div class="statusleft">
                                <span class="status-text"> Status : <span id="statusText" class="status-active">Active</span> </span>
                            </div>
                            <div class="statusright">
                                <button id="toggleBtn" class="toggle-switch" onclick="toggleStatus()"></button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="card-left-bottom">
            <h2 class="titlesuhulembab">Kondisi Ideal</h2>
            <div class="ideal-content">
                <div class="ideal-row">
                    <div class="ideal-box">
                        <p>Kelembapan</p>
                        <img src="content/logolembab.png"/>
                        <p>< 70 %</p>
                    </div>
                    <div class="ideal-box">
                        <p>Suhu</p>
                        <img src="content/logosuhu.png"/>
                        <p>18°C - 35°C</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-middle">
        <div class="vl"></div>
    </div>

    <!-- Kondisi Saat Ini -->
    <div class="card-right">
        <div class="card-right-top">
            <h2 class="titlesuhulembab">Kondisi Saat Ini</h2>

            <!-- kelembapan -->
            <div class="status-box <?= $color_kelembapan ?>">
                <img class="icon" src="content/logolembab.png" style="width:50px;height:50px;" />
                <div>
                    <p>Kelembapan : <?= $kelembapan ?> %</p>
                    <p>Status : <?= $status_kelembapan ?></p>
                </div>
            </div>

            <!-- suhu -->
            <div class="status-box <?= $color_suhu ?>">
                <div class="icon">
                    <img class="icon" src="content/logosuhu.png" style="width:50px;height:50px;" />
                </div>
                <div>
                    <p>Suhu : <?= $suhu ?> °C</p>
                    <p>Status : <?= $status_suhu ?></p>
                </div>
            </div>
        </div>

        <div class="card-right-bottom">
                <h2 class="titlesuhulembab">Kontrol Perangkat</h2>
            <div class="card-right-bottom-content">
            <!-- tombol ON/OFF -->
                <div class="button-container">
                    <button class="power-button" id="powerBtn">
                        <div class="button-ring off" id="ring"></div>
                        <span class="button-text">ON / OFF</span>
                    </button>
                    <div class="status-text off" id="status">OFF</div>
                </div>

                <div class="menu-right">
                    <div class="menu-item">
                        <img src="content/autobutton.png" alt="auto">
                    </div>
                    <div class="menu-item">
                        <img src="content/schedbutton.png" alt="schedule">
                    </div>
                    <div class="menu-item">
                        <img src="content/speedbutton.png" alt="speed">
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>


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

        const powerBtn = document.getElementById('powerBtn');
        const ring = document.getElementById('ring');
        const status = document.getElementById('status');
        let isOn = false;

        powerBtn.addEventListener('click', function() {
            isOn = !isOn;
            
            if (isOn) {
                ring.classList.remove('off');
                ring.classList.add('on');
                status.classList.remove('off');
                status.classList.add('on');
                status.textContent = 'ON';
            } else {
                ring.classList.remove('on');
                ring.classList.add('off');
                status.classList.remove('on');
                status.classList.add('off');
                status.textContent = 'OFF';
            }
        });

                    let isActive = true;
        let currentMode = 'auto';

        // Toggle Status Function
        function toggleStatus() {
            isActive = !isActive;
            const statusText = document.getElementById('statusText');
            const toggleBtn = document.getElementById('toggleBtn');

            if (isActive) {
                statusText.textContent = 'Active';
                statusText.className = 'status-active';
                toggleBtn.classList.remove('inactive');
            } else {
                statusText.textContent = 'Inactive';
                statusText.className = 'status-inactive';
                toggleBtn.classList.add('inactive');
            }

            // Send status to server
            updateStatusToServer(isActive);
        }

        // Set Mode Function
        function setMode(mode) {
            currentMode = mode;
            const autoMode = document.getElementById('autoMode');
            const scheduleMode = document.getElementById('scheduleMode');

            if (mode === 'auto') {
                autoMode.classList.add('active');
                scheduleMode.classList.remove('active');
            } else {
                scheduleMode.classList.add('active');
                autoMode.classList.remove('active');
            }

            // Send mode to server
            updateModeToServer(mode);
        }

        // Update Status to Server
        function updateStatusToServer(status) {
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    status: status ? 'active' : 'inactive',
                    timestamp: new Date().toISOString()
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Status updated:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Update Mode to Server
        function updateModeToServer(mode) {
            fetch('update_mode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mode: mode,
                    timestamp: new Date().toISOString()
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Mode updated:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Load Status from Server
        function loadStatus() {
            fetch('get_status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        isActive = data.status === 'active';
                        const statusText = document.getElementById('statusText');
                        const toggleBtn = document.getElementById('toggleBtn');

                        if (isActive) {
                            statusText.textContent = 'Active';
                            statusText.className = 'status-active';
                            toggleBtn.classList.remove('inactive');
                        } else {
                            statusText.textContent = 'Inactive';
                            statusText.className = 'status-inactive';
                            toggleBtn.classList.add('inactive');
                        }
                    }

                    if (data.mode) {
                        setMode(data.mode);
                    }

                    if (data.device_count) {
                        document.getElementById('deviceCount').textContent = data.device_count;
                    }
                })
                .catch(error => {
                    console.error('Error loading status:', error);
                });
        }

        // Auto refresh every 5 seconds
        setInterval(loadStatus, 5000);

        // Load initial status
        loadStatus();
    </script>
</body>
</html>
