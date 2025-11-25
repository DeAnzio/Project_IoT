<?php
require_once __DIR__ . '/../config/koneksi_pdo.php';
require_once __DIR__ . '/../config/auth.php';

// Terima POST dari UI (toggle alarm on/off)
$action = $_POST['action'] ?? $_GET['action'] ?? null; // 'on' atau 'off'
$device_id = $_POST['device_id'] ?? $_GET['device_id'] ?? 'esp32-unit-003';

if (!$action || !in_array(strtolower($action), ['on', 'off'])) {
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

    $command = strtolower($action) === 'on' ? 'alarm_on' : 'alarm_off';
    $payload = json_encode(['source' => 'web', 'action' => $command, 'ts' => date('c')]);

    // Insert command ke database
    $stmt = $pdo->prepare("
        INSERT INTO commands (device_id, command, payload, status, created_at)
        VALUES (:device_id, :command, :payload, 'pending', NOW(6))
    ");
    $stmt->execute([
        ':device_id' => $device_id,
        ':command' => $command,
        ':payload' => $payload
    ]);

    // If the alarm was turned OFF, ensure buzzer is turned off immediately
    if ($command === 'alarm_off') {
        // helper: check recent buzzer_on
        $check = $pdo->prepare("SELECT id FROM commands WHERE device_id = :device_id AND command = 'buzzer_on' AND status IN ('pending','executed') ORDER BY created_at DESC LIMIT 1");
        $check->execute([':device_id' => $device_id]);
        $buzzerOnExists = (bool) $check->fetchColumn();

        if ($buzzerOnExists) {
            // insert buzzer_off if not recently sent
            $checkOff = $pdo->prepare("SELECT id FROM commands WHERE device_id = :device_id AND command = 'buzzer_off' AND created_at > DATE_SUB(NOW(), INTERVAL 10 SECOND) LIMIT 1");
            $checkOff->execute([':device_id' => $device_id]);
            $buzzerOffExists = (bool) $checkOff->fetchColumn();

            if (!$buzzerOffExists) {
                $ins = $pdo->prepare("INSERT INTO commands (device_id, command, payload, status, created_at) VALUES (:device_id, 'buzzer_off', :payload, 'pending', NOW(6))");
                $ins->execute([':device_id' => $device_id, ':payload' => json_encode(['source' => 'alarm_off_action','ts'=>date('c')])]);
                    // cancel any pending buzzer_on commands to avoid re-triggering
                    $cancel = $pdo->prepare("UPDATE commands SET status = 'cancelled' WHERE device_id = :device_id AND command = 'buzzer_on' AND status = 'pending'");
                    $cancel->execute([':device_id' => $device_id]);
            }
        }
    }

    http_response_code(200);
    echo json_encode(['status' => 'ok', 'command' => $command]);
    exit;

} catch (Exception $e) {
    error_log('alarm.logic error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
    exit;
}
?>