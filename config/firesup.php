<?php
return [
    // Device ID used by the fire suppression ESP32 sketch
    'device_id' => 'esp32-unit-002',

    // MQ-2 threshold (analog 0-4095) above which suppression triggers
    'mq2_threshold' => 1500,

    // Minimum seconds between identical command inserts to avoid spamming device
    'debounce_seconds' => 10,

    // Whether to insert buzzer commands (true) and pump commands (true)
    'enable_buzzer' => true,
    'enable_pump' => true,
];
