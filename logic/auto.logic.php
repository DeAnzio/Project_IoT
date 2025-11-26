<?php
require_once __DIR__ . '/../config/koneksi_pdo.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

// Ambil konfigurasi auto mode dari config atau gunakan default
$autoConfig = file_exists(__DIR__ . '/../config/airsys_auto.php') 
    ? require __DIR__ . '/../config/airsys_auto.php' 
    : [];

$device_id = $_POST['device_id'] ?? $_GET['device_id'] ?? 'esp32-unit-001';
$action = $_POST['action'] ?? $_GET['action'] ?? null; // 'on', 'off', atau 'check'

if (!$action || !in_array(strtolower($action), ['on', 'off', 'check'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

try {
    if (!isset($koneksi)) $koneksi = null;
    $pdo = $koneksi ?? null;
    
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    $action = strtolower($action);

    if ($action === 'on') {
        // Kirim perintah lampu_on
        $command = 'lampu_on';
        $payload = json_encode(['source' => 'auto.logic', 'action' => 'on', 'ts' => date('c')]);

        $stmt = $pdo->prepare("
            INSERT INTO commands (device_id, command, payload, status, created_at)
            VALUES (:device_id, :command, :payload, 'pending', NOW(6))
        ");
        $stmt->execute([
            ':device_id' => $device_id,
            ':command' => $command,
            ':payload' => $payload
        ]);

        http_response_code(200);
        echo json_encode(['status' => 'ok', 'action' => 'on', 'command' => $command]);
        exit;

    } elseif ($action === 'off') {
        // Kirim perintah lampu_off
        $command = 'lampu_off';
        $payload = json_encode(['source' => 'auto.logic', 'action' => 'off', 'ts' => date('c')]);

        $stmt = $pdo->prepare("
            INSERT INTO commands (device_id, command, payload, status, created_at)
            VALUES (:device_id, :command, :payload, 'pending', NOW(6))
        ");
        $stmt->execute([
            ':device_id' => $device_id,
            ':command' => $command,
            ':payload' => $payload
        ]);

        http_response_code(200);
        echo json_encode(['status' => 'ok', 'action' => 'off', 'command' => $command]);
        exit;

    } elseif ($action === 'check') {
        // Check sensor data dan tentukan apakah harus ON atau OFF
        $stmt = $pdo->prepare("
            SELECT sensor_type, value, raw_value, recorded_at 
            FROM airsys 
            WHERE device_id = :device_id 
            ORDER BY recorded_at DESC 
            LIMIT 10
        ");
        $stmt->execute([':device_id' => $device_id]);
        $sensorData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$sensorData) {
            http_response_code(200);
            echo json_encode(['status' => 'ok', 'action' => 'check', 'result' => 'no_data', 'recommendation' => null]);
            exit;
        }

        // Parse sensor data
        $humidity = null;
        $temperature = null;

        foreach ($sensorData as $data) {
            $sensorType = strtoupper($data['sensor_type'] ?? '');
            $value = $data['value'] ?? null;
            $rawValue = $data['raw_value'] ?? null;

            if (strpos($sensorType, 'HUMIDITY') !== false || strpos($sensorType, 'DHT11_HUMIDITY') !== false) {
                $humidity = floatval($value ?? $rawValue);
            }
            if (strpos($sensorType, 'TEMP') !== false || strpos($sensorType, 'DHT11_TEMP') !== false) {
                $temperature = floatval($value ?? $rawValue);
            }
        }

        // Get thresholds dari config
        $humidityHigh = $autoConfig['humidity_high'] ?? 70;
        $humidityLow = $autoConfig['humidity_low'] ?? 30;
        $temperatureHigh = $autoConfig['temperature_high'] ?? 30;
        $temperatureLow = $autoConfig['temperature_low'] ?? 20;

        $shouldTurnOn = false;
        $reason = '';

        // Logika: Nyalakan jika kelembapan terlalu tinggi ATAU suhu terlalu tinggi
        if ($humidity !== null && $humidity > $humidityHigh) {
            $shouldTurnOn = true;
            $reason = "Humidity terlalu tinggi: {$humidity}% (threshold: {$humidityHigh}%)";
        } elseif ($temperature !== null && $temperature > $temperatureHigh) {
            $shouldTurnOn = true;
            $reason = "Temperature terlalu tinggi: {$temperature}°C (threshold: {$temperatureHigh}°C)";
        }

        http_response_code(200);
        echo json_encode([
            'status' => 'ok',
            'action' => 'check',
            'result' => $shouldTurnOn ? 'turn_on' : 'turn_off',
            'humidity' => $humidity,
            'temperature' => $temperature,
            'reason' => $reason,
            'recommendation' => $shouldTurnOn ? 'on' : 'off'
        ]);
        exit;
    }

} catch (Exception $e) {
    error_log('auto.logic error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
    exit;
}
?>
