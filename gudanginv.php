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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Inventaris</title>
      <link rel="stylesheet" href="content/style/header.css">
      <link rel="stylesheet" href="content/style/styleinv.css">
</head>
<body>
<header class="header">
    <div class="headerkiri">
    <div class="logo-container">
        <img src="content/stripheader.png" alt="Logo" class="logo-clickable">
        <div class="dropdown-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="gudangsm.php">Ruang Supply</a>
            <a href="loginv.php">Data Log</a>
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

<section class="content">

    <!-- Info Gudang -->
    <div class="card-left">
        <div class="card-left-top">
            <h2 class="titlestakat">Gudang Inventaris</h2>
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
            <h2 class="titlestakat">Status Perangkat</h2>
            <div class="card-left-bottom-content">
                <div class = "left-bottom-left">
                <!-- kiri -->
                    <div class="status-box <?= $color_kelembapan ?>">
                        <img class="icon" src="content/logolembab.png" style="width:50px;height:50px;" />
                        <div>
                            <p>Kelembapan : <?= $kelembapan ?> %</p>
                            <p>Status : <?= $status_kelembapan ?></p>
                        </div>
                    </div>
                    <div class="status-box <?= $color_kelembapan ?>">
                        <img class="icon" src="content/logolembab.png" style="width:50px;height:50px;" />
                        <div>
                            <p>Kelembapan : <?= $kelembapan ?> %</p>
                            <p>Status : <?= $status_kelembapan ?></p>
                        </div>
                    </div>
                </div>
                <div clsas = "left-bottom-right">
                <!-- kanan -->
                    <div class="status-box <?= $color_suhu ?>">
                        <div class="icon">
                            <img class="icon" src="content/logosuhu.png" style="width:50px;height:50px;" />
                        </div>
                        <div>
                            <p>Suhu : <?= $suhu ?> °C</p>
                            <p>Status : <?= $status_suhu ?></p>
                        </div>
                    </div>
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
            </div>
        </div>
    </div>

    <div class="card-middle">
        <div class="vl"></div>
    </div>

    <!-- Kondisi Saat Ini -->
    <div class="card-right">
        <div class="card-right-top">
            <h2 class="titlestakat">Activity Status</h2>
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Tanggal / Jam</th>
                        <th>Gerakan</th>
                        <th>Pintu</th>
                    </tr>
                </thead>
                <tbody id="sensor-table-body">
                    <tr>
                        <td colspan="4" class="text-center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
            
        </div>
        
        <div class="card-right-bottom">
                <h2 class="titlestakat">Notification</h2>
        <div class="card-right-bottom-content">
    </div>

</section>
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

            let isActive = true;
        let currentMode = 'auto';
let alarmActive = false; // track status alarm

// Toggle Status Function (untuk alarm)
function toggleStatus() {
    alarmActive = !alarmActive;
    const statusText = document.getElementById('statusText');
    const toggleBtn = document.getElementById('toggleBtn');

    if (alarmActive) {
        statusText.textContent = 'Active';
        statusText.className = 'status-active';
        toggleBtn.classList.remove('inactive');
    } else {
        statusText.textContent = 'Inactive';
        statusText.className = 'status-inactive';
        toggleBtn.classList.add('inactive');
    }

    // Send alarm status to server
    fetch('logic/alarm.logic.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: alarmActive ? 'on' : 'off',
            device_id: 'esp32-unit-003'
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Alarm status sent:', data);
    })
    .catch(error => {
        console.error('Error sending alarm status:', error);
        // revert state jika gagal
        alarmActive = !alarmActive;
        statusText.textContent = alarmActive ? 'Active' : 'Inactive';
        statusText.className = alarmActive ? 'status-active' : 'status-inactive';
    });
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

    const deviceId = "esp32-unit-003";
    const tableBody = document.getElementById("sensor-table-body");

function detectSensorColumn(sensorType, raw) {
  const s = ((sensorType || raw) + '').toLowerCase();
  if (s.includes('pir') || s.includes('motion')) return 'motion';
  if (s.includes('mc38') || s.includes('mag') || s.includes('magnetic') || s.includes('door') || s.includes('pintu')) return 'door';
  return null;
}

async function loadData() {
  try {
    const response = await fetch(`http://localhost/project_iot/api_pdo/get_sensor_data_secusys.php?device_id=${deviceId}`);
    const datas = await response.json();

    if (!datas || datas.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="3" class="text-center">Tidak Ada Data</td></tr>`;
      return;
    }

    // Group records by exact timestamp (recorded_at); merge PIR and MAG readings per timestamp
    const map = {};
    datas.forEach(d => {
      const time = d.recorded_at || d.recorded_at_local || new Date().toISOString();
      if (!map[time]) map[time] = { recorded_at: time, motion: null, door: null };
      const col = detectSensorColumn(d.sensor_type, d.raw_value || d.raw);
      const val = (d.value !== undefined && d.value !== null) ? d.value : (d.raw_value !== undefined ? d.raw_value : null);

      if (col === 'motion') map[time].motion = val;
      if (col === 'door') map[time].door = val;
    });

    // Sort timestamps descending (newest first)
    const rows = Object.values(map).sort((a, b) => new Date(b.recorded_at) - new Date(a.recorded_at));

    tableBody.innerHTML = rows.map(r => {
      const motionCell = (r.motion == 1 || r.motion === '1' ? '1' : '0');
      const doorCell = (r.door === null || r.door === undefined) ? '-' : ((r.door == 1 || r.door === '1') ? 'Terbuka' : 'Tertutup');
      return `<tr>
                <td>${r.recorded_at}</td>
                <td>${motionCell}</td>
                <td>${doorCell}</td>
              </tr>`;
    }).join('');

  } catch (error) {
    tableBody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>`;
    console.error('Gagal memuat data:', error);
  }
}
    // Load data pertama kali
    loadData();

        // Auto refresh every 5 seconds
        setInterval(loadStatus, 5000);

        // Load initial status
        loadStatus();
</script>
</html>