


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

            <div class="status-box bg-secondary" id="kelembapan-box">
                <img class="icon" src="content/logolembab.png" style="width:50px;height:50px;" />
                <div id="kelembapan-content">
                    <p id="kelembapan-value">Kelembapan : -- %</p>
                    <p id="kelembapan-status">Status : Memuat...</p>
                </div>
            </div>

            <!-- Suhu -->
            <div class="status-box bg-secondary" id="suhu-box">
                <div class="icon">
                    <img class="icon" src="content/logosuhu.png" style="width:50px;height:50px;" />
                </div>
                <div id="suhu-content">
                    <p id="suhu-value">Suhu : -- °C</p>
                    <p id="suhu-status">Status : Memuat...</p>
                </div>
            </div>
        </div>

        <div class="card-right-bottom">
                <h2 class="titlesuhulembab">Kontrol Perangkat</h2>
            <div class="card-right-bottom-content">
            <!-- tombol ON/OFF -->
            <div class="button-container">
                <form action="logic/lampu.logic.php" method="GET">                    
                    <button type="submit" class="power-button" id="powerBtn" name="" value="true">
                        <div class="button-ring off" id="ring"></div>
                        <span class="button-text">ON / OFF</span>
                    </button>
                </form>
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

powerBtn.addEventListener('click', function(e) {
    e.preventDefault(); // Mencegah submit form
    
    isOn = !isOn;
    
    const param = isOn ? 'lampu_on' : 'lampu_off';
    
    // Update UI
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
    
    // Kirim request via AJAX
    fetch(`logic/lampu.logic.php?${param}=true`)
        .then(response => {
            if (!response.ok) {
                console.error('Request gagal');
                // Kembalikan state jika gagal
                isOn = !isOn;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Kembalikan state jika error
            isOn = !isOn;
        });
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

const deviceId = "esp32-unit-001";

async function loadData() {
  try {
    const response = await fetch(`http://localhost/project_iot/api_pdo/get_sensor_data.php?device_id=${deviceId}`);
    const datas = await response.json();

    if (!datas || datas.length === 0) {
      console.log('Tidak ada data');
      return;
    }

    // Ambil data terbaru
    const latestData = datas[0];
    
    // Update kelembapan
    const kelembapan = latestData.humidity || latestData.kelembapan || 0;
    const statusKelembapan = kelembapan < 30 ? 'Kering' : kelembapan > 70 ? 'Lembap' : 'Normal';
    const colorKelembapan = kelembapan < 30 ? 'bg-danger' : kelembapan > 70 ? 'bg-warning' : 'bg-success';
    
    document.getElementById('kelembapan-box').className = `status-box ${colorKelembapan}`;
    document.getElementById('kelembapan-value').textContent = `Kelembapan : ${kelembapan} %`;
    document.getElementById('kelembapan-status').textContent = `Status : ${statusKelembapan}`;

    // Update suhu
    const suhu = latestData.temperature || latestData.suhu || 0;
    const statusSuhu = suhu < 20 ? 'Dingin' : suhu > 30 ? 'Panas' : 'Normal';
    const colorSuhu = suhu < 20 ? 'bg-primary' : suhu > 30 ? 'bg-danger' : 'bg-success';
    
    document.getElementById('suhu-box').className = `status-box ${colorSuhu}`;
    document.getElementById('suhu-value').textContent = `Suhu : ${suhu} °C`;
    document.getElementById('suhu-status').textContent = `Status : ${statusSuhu}`;

  } catch (error) {
    console.error('Gagal memuat data:', error);
  }
}

        // Auto refresh every 5 seconds
        setInterval(loadStatus, 5000);

        // Load initial status
        loadData();
        loadStatus();
    </script>
</body>
</html>
