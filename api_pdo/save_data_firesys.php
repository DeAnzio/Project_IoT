
<?php
require '../config/koneksi_pdo.php';

$pdo = $koneksi;

// Baca input JSON
$input = json_decode(file_get_contents('php://input'), true);

// Cek data JSON di API nya (jika tidak mau hapus kode ini)
// if (!$input || !isset($input['api_key']) || $input['api_key'] !== $API_KEY) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Unauthorized']);
//     exit;
// }

// Nangkap Data JSON
$device_id   = $input['device_id'] ?? null;
$sensor_type = $input['sensor_type'] ?? 'unknown';
$value       = $input['value'] ?? null;
$raw         = $input['raw'] ?? null;

// Cek apakah device_id dan value ada
if (!$device_id || $value === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Simpan / update device
$stmt = $pdo->prepare("INSERT INTO devices (device_id, last_seen) VALUES (:id, NOW())
                       ON DUPLICATE KEY UPDATE last_seen = NOW()");
$stmt->execute([':id' => $device_id]);

// Error Handling
if ($stmt->rowCount() === 0) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save device']);
    exit;
}

// Simpan data sensor
$stmt = $pdo->prepare("INSERT INTO firesys (device_id, sensor_type, value, raw_value)
                       VALUES (:device_id, :stype, :value, :raw)");
$stmt->execute([
    ':device_id' => $device_id,
    ':stype'     => $sensor_type,
    ':value'     => $value,
    ':raw'       => $raw
]);

// Error Handling
if ($stmt->rowCount() === 0) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save sensor data']);
    exit;
}

// Jika data MQ2 dikirim, jalankan logika fire suppression sebelum response
if (strtoupper($sensor_type) === 'MQ2_GAS' || strtoupper($sensor_type) === 'MQ2') {
    try {
        require_once __DIR__ . '/../logic/firesup.logic.php';
        $res = firesup_handle(floatval($value), $device_id);
        error_log('firesup triggered: ' . json_encode($res));
    } catch (Exception $e) {
        error_log('firesup.handle error: ' . $e->getMessage());
    }
}

// Kembalikan status OK
echo json_encode(['status' => 'ok']);

