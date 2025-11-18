<?php
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
    <title>Warehouse Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <div class="title">O - Warehouse</div>
</header>

<section class="content">

    <!-- Info Gudang -->
    <div class="card-left">
        <h2>Gudang Supply Makanan</h2>
        <div class="device-row">
            <div class="device-icon">📦</div>
            <div>
                <p class="device-count">2 Device</p>
                <p class="status">Status: <span class="active">Active</span></p>
            </div>
        </div>

        <h3>Kondisi Ideal</h3>
        <div class="ideal-row">
            <div class="ideal-box">
                <img src="https://img.icons8.com/ios/50/000000/wet.png"/>
                <p>< 70 %</p>
            </div>
            <div class="ideal-box">
                <img src="https://img.icons8.com/ios/50/000000/temperature.png"/>
                <p>18°C - 35°C</p>
            </div>
        </div>
    </div>

    <!-- Kondisi Saat Ini -->
    <div class="card-right">

        <h2>Kondisi Saat Ini</h2>

        <!-- kelembapan -->
        <div class="status-box <?= $color_kelembapan ?>">
            <div class="icon">💧</div>
            <div>
                <p>Kelembapan : <?= $kelembapan ?> %</p>
                <p>Status : <?= $status_kelembapan ?></p>
            </div>
        </div>

        <!-- suhu -->
        <div class="status-box <?= $color_suhu ?>">
            <div class="icon">🌡️</div>
            <div>
                <p>Suhu : <?= $suhu ?> °C</p>
                <p>Status : <?= $status_suhu ?></p>
            </div>
        </div>

        <!-- tombol ON/OFF -->
        <div class="power-container">
            <button class="power-btn" onclick="togglePower()">ON / OFF</button>
        </div>

        <div class="menu-right">
            <div class="menu-item">AUTO</div>
            <div class="menu-item">SCHEDULE</div>
            <div class="menu-item">SPEED</div>
        </div>
    </div>

</section>

<script src="script.js"></script>
</body>
</html>
