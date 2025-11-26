<?php
// Konfigurasi untuk AirSys Auto Mode
return [
    // Threshold kelembapan (%)
    'humidity_high' => 70,      // Nyalakan jika > 70%
    'humidity_low' => 30,       // (untuk info, belum digunakan)

    // Threshold suhu (°C)
    'temperature_high' => 30,   // Nyalakan jika > 30°C
    'temperature_low' => 20,    // (untuk info, belum digunakan)

    // Mode otomatis aktif atau tidak (default: false)
    'auto_mode_enabled' => false,

    // Interval check (detik)
    'check_interval' => 30,
];
