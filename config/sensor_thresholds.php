<?php
// Konfigurasi threshold untuk sensor suhu dan kelembapan
return [
    'air_quality' => [
        'temperature' => [
            'min' => 18,      // Suhu minimum (°C)
            'max' => 28,      // Suhu maksimum (°C)
        ],
        'humidity' => [
            'min' => 30,      // Kelembapan minimum (%)
            'max' => 80,      // Kelembapan maksimum (%)
        ]
    ],
    'notification_duration' => 5000, // Durasi tampil notifikasi (ms), 0 = unlimited
];
