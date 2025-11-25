
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
$stmt = $pdo->prepare("INSERT INTO secusys (device_id, sensor_type, value, raw_value)
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


// ===== LOGIKA ALARM OTOMATIS (MAGNETIC + PIR) =====
// Ambil perintah alarm terakhir (terbaru) — jangan hanya yang 'executed'
$alarmStmt = $pdo->prepare("
    SELECT command, status, created_at, executed_at
    FROM commands
    WHERE device_id = :device_id
      AND command IN ('alarm_on','alarm_off')
    ORDER BY created_at DESC
    LIMIT 1
");
$alarmStmt->execute([':device_id' => $device_id]);
$alarmRow = $alarmStmt->fetch(PDO::FETCH_ASSOC);
$alarmEnabled = $alarmRow && $alarmRow['command'] === 'alarm_on';

// Helper: cek apakah ada command tertentu baru-baru ini (pending/executed)
// NOTE: build INTERVAL with intval to avoid binding into INTERVAL
function hasRecentCommand(PDO $pdo, $device_id, $cmd, $seconds = 5) {
    $sec = (int)$seconds;
    $sql = "
        SELECT id FROM commands
        WHERE device_id = :device_id
          AND command = :cmd
          AND status IN ('pending','executed')
          AND created_at > DATE_SUB(NOW(), INTERVAL $sec SECOND)
        LIMIT 1
    ";
    $check = $pdo->prepare($sql);
    $check->execute([':device_id' => $device_id, ':cmd' => $cmd]);
    return (bool) $check->fetchColumn();
}

// Jika alarm diaktifkan dan sensor MAGNETIC atau PIR mendeteksi (value == 1) -> buzzer_on
if ($alarmEnabled && in_array(strtoupper($sensor_type), ['MAGNETIC', 'PIR']) && intval($value) === 1) {
    if (!hasRecentCommand($pdo, $device_id, 'buzzer_on', 5)) {
        $buzzerStmt = $pdo->prepare("
            INSERT INTO commands (device_id, command, payload, status, created_at)
            VALUES (:device_id, 'buzzer_on', :payload, 'pending', NOW(6))
        ");
        $buzzerStmt->execute([
            ':device_id' => $device_id,
            ':payload'   => json_encode(['source' => 'auto_alarm', 'trigger' => strtoupper($sensor_type), 'ts' => date('c')])
        ]);
        error_log("Auto buzzer_on triggered for device: $device_id (sensor={$sensor_type} value=1)");
    }
}

// Jika sensor berubah ke 0, cek kedua sensor; jika keduanya 0 -> buzzer_off
if (in_array(strtoupper($sensor_type), ['MAGNETIC', 'PIR']) && intval($value) === 0) {
    $latestSensor = $pdo->prepare("
        SELECT sensor_type, value
        FROM secusys
        WHERE device_id = :device_id
          AND sensor_type IN ('MAGNETIC','PIR')
        ORDER BY recorded_at DESC
        LIMIT 2
    ");
    $latestSensor->execute([':device_id' => $device_id]);
    $rows = $latestSensor->fetchAll(PDO::FETCH_ASSOC);

    $magneticLatest = 0; $pirLatest = 0;
    foreach ($rows as $r) {
        $stype = strtoupper($r['sensor_type']);
        if ($stype === 'MAGNETIC' && $magneticLatest === 0) $magneticLatest = intval($r['value']);
        if ($stype === 'PIR' && $pirLatest === 0) $pirLatest = intval($r['value']);
    }

    if ($magneticLatest === 0 && $pirLatest === 0) {
        if (!hasRecentCommand($pdo, $device_id, 'buzzer_off', 5)) {
            $buzzerOffStmt = $pdo->prepare("
                INSERT INTO commands (device_id, command, payload, status, created_at)
                VALUES (:device_id, 'buzzer_off', :payload, 'pending', NOW(6))
            ");
            $buzzerOffStmt->execute([
                ':device_id' => $device_id,
                ':payload'   => json_encode(['source' => 'auto_alarm', 'trigger' => 'sensors_cleared', 'ts' => date('c')])
            ]);
            error_log("Auto buzzer_off triggered for device: $device_id (sensors cleared)");
        }
    }
}

// Jika alarm baru saja dimatikan (perintah terakhir adalah alarm_off), pastikan buzzer_off dikirim
if (!$alarmEnabled) {
    // Jika ada buzzer_on recent, segera kirim buzzer_off
    if (hasRecentCommand($pdo, $device_id, 'buzzer_on', 600) && !hasRecentCommand($pdo, $device_id, 'buzzer_off', 5)) {
        $buzzerOffStmt = $pdo->prepare("
            INSERT INTO commands (device_id, command, payload, status, created_at)
            VALUES (:device_id, 'buzzer_off', :payload, 'pending', NOW(6))
        ");
        $buzzerOffStmt->execute([
            ':device_id' => $device_id,
            ':payload'   => json_encode(['source' => 'auto_alarm', 'trigger' => 'alarm_disabled', 'ts' => date('c')])
        ]);
        error_log("Auto buzzer_off triggered for device: $device_id (alarm disabled)");
    }
}



// Kembalikan status OK
echo json_encode(['status' => 'ok']);

