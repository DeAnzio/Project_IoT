<?php require_once __DIR__ . '/config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="content/style/styleloginv.css">
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
            <h2>Activity Status</h2>
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

    // Perbarui setiap 5 detik
    setInterval(loadData, 5000);
</script>
</html>