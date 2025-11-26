<?php
require_once __DIR__ . '/../config/koneksi_pdo.php';
require_once __DIR__ . '/../config/auth.php';
// Load firesup configuration
$firesupConfig = file_exists(__DIR__ . '/../config/firesup.php') ? require __DIR__ . '/../config/firesup.php' : [];

// Expose the logic as a function so it can be called internally without sending HTTP headers
function firesup_handle($mq2, $device_id = null, $threshold = null) {
    global $koneksi, $firesupConfig;

    $device_id = $device_id ?? ($firesupConfig['device_id'] ?? 'esp32-firesystem');
    $threshold = $threshold ?? ($firesupConfig['mq2_threshold'] ?? 300);
    $debounce = intval($firesupConfig['debounce_seconds'] ?? 10);

    if ($mq2 === null) {
        throw new InvalidArgumentException('mq2_gas value required');
    }

    if (!isset($koneksi)) $koneksi = null;
    $pdo = $koneksi ?? null;
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    $nowPayload = ['source' => 'firesup.logic', 'mq2_gas' => $mq2, 'threshold' => $threshold, 'ts' => date('c')];
    $payloadJson = json_encode($nowPayload);

    $isHigh = $mq2 >= $threshold;
    $actions = [];

    if ($isHigh) {
        // If high: send pompa_on and buzzer_on if not recently sent
        $checkRecentSql = "SELECT id FROM commands WHERE device_id = :device_id AND command = :command AND created_at > DATE_SUB(NOW(), INTERVAL $debounce SECOND) LIMIT 1";
        $checkRecent = $pdo->prepare($checkRecentSql);

        if (!empty($firesupConfig['enable_pump'])) {
            $checkRecent->execute([':device_id' => $device_id, ':command' => 'pompa_on']);
            $pumpRecent = (bool) $checkRecent->fetchColumn();
            if (!$pumpRecent) {
                $ins = $pdo->prepare("INSERT INTO commands (device_id, command, payload, status, created_at) VALUES (:device_id, 'pompa_on', :payload, 'pending', NOW(6))");
                $ins->execute([':device_id' => $device_id, ':payload' => $payloadJson]);
                $actions[] = 'pompa_on';
            }
        }

        if (!empty($firesupConfig['enable_buzzer'])) {
            $checkRecent->execute([':device_id' => $device_id, ':command' => 'buzzer_on']);
            $buzzerRecent = (bool) $checkRecent->fetchColumn();
            if (!$buzzerRecent) {
                $ins = $pdo->prepare("INSERT INTO commands (device_id, command, payload, status, created_at) VALUES (:device_id, 'buzzer_on', :payload, 'pending', NOW(6))");
                $ins->execute([':device_id' => $device_id, ':payload' => $payloadJson]);
                $actions[] = 'buzzer_on';
            }
        }

        $cancelOff = $pdo->prepare("UPDATE commands SET status = 'cancelled' WHERE device_id = :device_id AND command IN ('pompa_off','buzzer_off') AND status = 'pending'");
        $cancelOff->execute([':device_id' => $device_id]);

    } else {
        // If normal/low: ensure pompa and buzzer OFF if previously ON
        $checkOn = $pdo->prepare("SELECT id, command FROM commands WHERE device_id = :device_id AND command IN ('pompa_on','buzzer_on') AND status IN ('pending','executed') ORDER BY created_at DESC LIMIT 1");
        $checkOn->execute([':device_id' => $device_id]);
        $onExists = (bool) $checkOn->fetchColumn();

        if ($onExists) {
            $checkRecentOffSql = "SELECT id FROM commands WHERE device_id = :device_id AND command = :command AND created_at > DATE_SUB(NOW(), INTERVAL $debounce SECOND) LIMIT 1";
            $checkRecentOff = $pdo->prepare($checkRecentOffSql);

            if (!empty($firesupConfig['enable_pump'])) {
                $checkRecentOff->execute([':device_id' => $device_id, ':command' => 'pompa_off']);
                $pumpOffRecent = (bool) $checkRecentOff->fetchColumn();
                if (!$pumpOffRecent) {
                    $ins = $pdo->prepare("INSERT INTO commands (device_id, command, payload, status, created_at) VALUES (:device_id, 'pompa_off', :payload, 'pending', NOW(6))");
                    $ins->execute([':device_id' => $device_id, ':payload' => json_encode(['source'=>'firesup.logic','ts'=>date('c')])]);
                    $actions[] = 'pompa_off';
                }
            }

            if (!empty($firesupConfig['enable_buzzer'])) {
                $checkRecentOff->execute([':device_id' => $device_id, ':command' => 'buzzer_off']);
                $buzzerOffRecent = (bool) $checkRecentOff->fetchColumn();
                if (!$buzzerOffRecent) {
                    $ins = $pdo->prepare("INSERT INTO commands (device_id, command, payload, status, created_at) VALUES (:device_id, 'buzzer_off', :payload, 'pending', NOW(6))");
                    $ins->execute([':device_id' => $device_id, ':payload' => json_encode(['source'=>'firesup.logic','ts'=>date('c')])]);
                    $actions[] = 'buzzer_off';
                }
            }

            $cancelOn = $pdo->prepare("UPDATE commands SET status = 'cancelled' WHERE device_id = :device_id AND command IN ('pompa_on','buzzer_on') AND status = 'pending'");
            $cancelOn->execute([':device_id' => $device_id]);
        }
    }

    return ['status' => 'ok', 'mq2_gas' => $mq2, 'threshold' => $threshold, 'actions' => $actions];
}

// If requested directly over HTTP, parse input and output JSON (backwards compatible)
if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json');

    $mq2 = isset($_POST['mq2_gas']) ? floatval($_POST['mq2_gas']) : (isset($_GET['mq2_gas']) ? floatval($_GET['mq2_gas']) : null);
    $device_id = $_POST['device_id'] ?? $_GET['device_id'] ?? null;
    $threshold = isset($_POST['threshold']) ? floatval($_POST['threshold']) : (isset($_GET['threshold']) ? floatval($_GET['threshold']) : null);

    try {
        $res = firesup_handle($mq2, $device_id, $threshold);
        http_response_code(200);
        echo json_encode($res);
        exit;
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        error_log('firesup.logic error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Server error']);
        exit;
    }
}
?>