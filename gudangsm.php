


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Ruang Inventaris</title>
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
            <a href="gudangInv.php">Gudang</a>
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
            <h2 class="titlesuhulembab">Ruang Inventaris</h2>
                <div class="device-row">
                    <div class="device-icon">
                        <img src="content/LogoGudangMakanan.png" alt="Logo">
                    </div>
                    <div class="devicedesc">
                        <div class="deviceline">

                            <p >AirSys</p>
                        </div>
                        <div class="statusline">
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
                    <div class="menu-item" id="autoBtn" title="Auto Mode" style="cursor: pointer;">
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

// Auto Mode State
let autoModeEnabled = false;
let autoModeCheckInterval = null;
const AUTO_CHECK_INTERVAL = 30000; // 30 detik

// Toggle Auto Mode
function toggleAutoMode() {
    autoModeEnabled = !autoModeEnabled;
    const autoBtn = document.getElementById('autoBtn');
    
    if (autoModeEnabled) {
        autoBtn.style.opacity = '1';
        autoBtn.style.filter = 'brightness(1.2)';
        console.log('Auto Mode: ON');
        
        // Mulai check otomatis
        startAutoModeCheck();
    } else {
        autoBtn.style.opacity = '0.6';
        autoBtn.style.filter = 'brightness(1)';
        console.log('Auto Mode: OFF');
        
        // Stop check otomatis
        stopAutoModeCheck();
    }
}

// Fungsi untuk check sensor dan nyalakan/matikan secara otomatis
function checkAndControlAuto() {
    fetch(`logic/auto.logic.php?action=check&device_id=${deviceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok' && data.recommendation) {
                console.log('Auto Mode Check:', data);
                
                // Bandingkan dengan status UI saat ini
                if (data.recommendation === 'on' && !isOn) {
                    console.log('Auto turning ON (reason: ' + data.reason + ')');
                    togglePowerButton(true);
                } else if (data.recommendation === 'off' && isOn) {
                    console.log('Auto turning OFF');
                    togglePowerButton(false);
                }
            }
        })
        .catch(error => console.error('Auto check error:', error));
}

// Helper untuk toggle power button
function togglePowerButton(turnOn) {
    if (turnOn === isOn) return; // Sudah dalam state yang diinginkan
    
    isOn = turnOn;
    const param = turnOn ? 'lampu_on' : 'lampu_off';
    
    // Update UI
    const ring = document.getElementById('ring');
    const status = document.getElementById('status');
    
    if (turnOn) {
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
    
    // Kirim ke server
    fetch(`logic/lampu.logic.php?${param}=true`)
        .then(response => {
            if (!response.ok) {
                console.error('Request gagal');
                isOn = !isOn; // Revert
            }
        })
        .catch(error => {
            console.error('Error:', error);
            isOn = !isOn; // Revert
        });
}

// Start/Stop auto check
function startAutoModeCheck() {
    autoModeCheckInterval = setInterval(checkAndControlAuto, AUTO_CHECK_INTERVAL);
    // Check immediately
    checkAndControlAuto();
}

function stopAutoModeCheck() {
    if (autoModeCheckInterval) {
        clearInterval(autoModeCheckInterval);
        autoModeCheckInterval = null;
    }
}

// Add event listener untuk auto button
document.addEventListener('DOMContentLoaded', function() {
    const autoBtn = document.getElementById('autoBtn');
    if (autoBtn) {
        autoBtn.style.opacity = '0.6';
        autoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleAutoMode();
        });
    }
});

function parseNumber(v) {
    if (v === null || v === undefined) return null;
    if (typeof v === 'number') return v;
    // try direct parse
    const asNum = Number(v);
    if (!Number.isNaN(asNum)) return asNum;
    // extract first number from strings like 'h=65.00' or 't:25.8'
    const s = String(v);
    const m = s.match(/-?\d+(?:\.\d+)?/);
    if (m) return parseFloat(m[0]);
    return null;
}

function pickLatest(items) {
    if (!Array.isArray(items) || items.length === 0) return null;
    let best = items[0];
    let bestTs = new Date(best.recorded_at || best.created_at || 0).getTime();
    for (let i = 1; i < items.length; i++) {
        const it = items[i];
        const ts = new Date(it.recorded_at || it.created_at || 0).getTime();
        if (ts > bestTs) {
            best = it;
            bestTs = ts;
        }
    }
    return best;
}

function extractFromRecord(rec) {
    // normalize record -> try sensor_type, value, and embedded JSON `data`
    const obj = {};
    const st = (rec.sensor_type || '').toString().toLowerCase();
    const val = rec.value ?? rec.v ?? rec.temp ?? rec.t ?? rec.h ?? null;

    // try parse data field if exists
    if (rec.data) {
        try {
            const parsed = (typeof rec.data === 'string') ? JSON.parse(rec.data) : rec.data;
            if (parsed) {
                if (parsed.temperature !== undefined) obj.temperature = parsed.temperature;
                if (parsed.temp !== undefined) obj.temperature = obj.temperature ?? parsed.temp;
                if (parsed.humidity !== undefined) obj.humidity = parsed.humidity;
                if (parsed.hum !== undefined) obj.humidity = obj.humidity ?? parsed.hum;
                if (parsed.suhu !== undefined) obj.temperature = obj.temperature ?? parsed.suhu;
                if (parsed.kelembapan !== undefined) obj.humidity = obj.humidity ?? parsed.kelembapan;
            }
        } catch (e) {
            // ignore parse errors
        }
    }

    // sensor_type based
    if (st.includes('temp') || st.includes('dht') || st.includes('suhu')) {
        obj.temperature = obj.temperature ?? val;
    }
    if (st.includes('hum') || st.includes('kelembap') || st.includes('humid')) {
        obj.humidity = obj.humidity ?? val;
    }

    // fallback: if value looks like temperature/humidity numeric, try assign
    if (obj.temperature === undefined && obj.humidity === undefined && val !== null) {
        // ambiguous: assign to temperature if within common temp range; else humidity
        const n = parseNumber(val);
        if (n !== null) {
            if (n >= -10 && n <= 60) obj.temperature = n;
            else if (n >= 0 && n <= 100) obj.humidity = n;
        }
    }

    return obj;
}

async function loadData() {
  try {
    const base = window.location.origin || (window.location.protocol + '//' + window.location.host);
    const url = `${base}/project_iot/api_pdo/get_sensor_data_airsys.php?device_id=${encodeURIComponent(deviceId)}&limit=100`;
    const response = await fetch(url, { cache: 'no-store' });

    if (!response.ok) {
      console.error('fetch error status', response.status, response.statusText);
      return;
    }

    const json = await response.json();
    let datas = json;

    if (json && typeof json === 'object' && !Array.isArray(json)) {
      if (Array.isArray(json.data)) datas = json.data;
      else if (Array.isArray(json.results)) datas = json.results;
      else if (Array.isArray(json.items)) datas = json.items;
      else datas = [json];
    }

    if (!Array.isArray(datas) || datas.length === 0) {
      console.log('Tidak ada data sensor dari API');
      return;
    }

    // build arrays of records with parsed temp/hum fields
        const parsed = datas.map(d => {
            const rec = Object.assign({}, d);
            const ex = extractFromRecord(rec);
            rec._temperature = ex.temperature !== undefined ? parseNumber(ex.temperature) : null;
            // humidity can be in many places; use parsed humidity or fallback to raw_value
            rec._humidity = ex.humidity !== undefined ? parseNumber(ex.humidity) : (rec.raw_value !== undefined ? parseNumber(rec.raw_value) : null);
            return rec;
        });

    // pick latest record that has temperature/humidity
    const tempCandidates = parsed.filter(r => r._temperature !== null);
    const humCandidates = parsed.filter(r => r._humidity !== null);

    const latestTempRec = pickLatest(tempCandidates);
    const latestHumRec = pickLatest(humCandidates);

    const suhu = latestTempRec ? latestTempRec._temperature : null;
    const kelembapan = latestHumRec ? latestHumRec._humidity : null;

    if (kelembapan === null || kelembapan === undefined) {
      document.getElementById('kelembapan-value').textContent = `Kelembapan : -- %`;
      document.getElementById('kelembapan-status').textContent = `Status : Tidak tersedia`;
      document.getElementById('kelembapan-box').className = 'status-box bg-secondary';
    } else {
      const statusKelembapan = kelembapan < 30 ? 'Kering' : kelembapan > 70 ? 'Lembap' : 'Normal';
      const colorKelembapan = kelembapan < 30 ? 'bg-danger' : kelembapan > 70 ? 'bg-warning' : 'bg-success';
      document.getElementById('kelembapan-box').className = `status-box ${colorKelembapan}`;
      document.getElementById('kelembapan-value').textContent = `Kelembapan : ${kelembapan} %`;
      document.getElementById('kelembapan-status').textContent = `Status : ${statusKelembapan}`;
    }

    if (suhu === null || suhu === undefined) {
      document.getElementById('suhu-value').textContent = `Suhu : -- °C`;
      document.getElementById('suhu-status').textContent = `Status : Tidak tersedia`;
      document.getElementById('suhu-box').className = 'status-box bg-secondary';
    } else {
      const statusSuhu = suhu < 20 ? 'Dingin' : suhu > 30 ? 'Panas' : 'Normal';
      const colorSuhu = suhu < 20 ? 'bg-primary' : suhu > 30 ? 'bg-danger' : 'bg-success';
      document.getElementById('suhu-box').className = `status-box ${colorSuhu}`;
      document.getElementById('suhu-value').textContent = `Suhu : ${suhu} °C`;
      document.getElementById('suhu-status').textContent = `Status : ${statusSuhu}`;
    }

  } catch (error) {
    console.error('Gagal memuat data:', error);
  }
}

        // Auto refresh every 5 seconds untuk sensor data
            setInterval(loadData, 5000);

            // Auto refresh every 5 seconds untuk status
            setInterval(loadStatus, 5000);

        // Load initial status
        loadData();
        loadStatus();
    </script>
</body>
</html>
