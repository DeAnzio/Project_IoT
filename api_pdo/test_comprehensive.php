<?php
// Bypass koneksi_pdo.php header
$pdo = new PDO(
    "mysql:host=localhost;dbname=iot_app;charset=utf8mb4",
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$GLOBALS['koneksi'] = $pdo;

header('Content-Type: text/plain; charset=utf-8');

echo "=== COMPREHENSIVE FIRESUP TEST ===\n\n";

try {
    require_once __DIR__ . '/../../logic/firesup.logic.php';
    echo "[✓] Firesup logic loaded\n\n";
    
    // Clear old test commands
    $pdo->exec("DELETE FROM commands WHERE device_id='esp32-unit-002' AND command IN ('pompa_on','pompa_off','buzzer_on','buzzer_off')");
    echo "[✓] Old test commands cleared\n\n";
    
    // Scenario 1: MQ-2 becomes HIGH
    echo "=== SCENARIO 1: MQ-2 BECOMES HIGH (2500) ===\n";
    echo str_repeat("-", 60) . "\n";
    $result1 = firesup_handle(2500, 'esp32-unit-002');
    echo "Result: " . json_encode($result1, JSON_PRETTY_PRINT) . "\n";
    
    $stmt = $pdo->query("SELECT command, status FROM commands WHERE device_id='esp32-unit-002' ORDER BY created_at DESC LIMIT 5");
    echo "Commands in DB after HIGH:\n";
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "  - {$row['command']}: {$row['status']}\n";
    }
    echo "\n";
    
    // Scenario 2: MQ-2 goes NORMAL (1000)
    echo "=== SCENARIO 2: MQ-2 RETURNS TO NORMAL (1000) ===\n";
    echo str_repeat("-", 60) . "\n";
    $result2 = firesup_handle(1000, 'esp32-unit-002');
    echo "Result: " . json_encode($result2, JSON_PRETTY_PRINT) . "\n";
    
    $stmt = $pdo->query("SELECT command, status FROM commands WHERE device_id='esp32-unit-002' ORDER BY created_at DESC LIMIT 5");
    echo "Commands in DB after NORMAL:\n";
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "  - {$row['command']}: {$row['status']}\n";
    }
    echo "\n";
    
    // Check pending commands
    echo "=== PENDING COMMANDS TO EXECUTE ===\n";
    echo str_repeat("-", 60) . "\n";
    $stmt = $pdo->prepare("SELECT id, command, created_at FROM commands WHERE device_id='esp32-unit-002' AND status='pending' ORDER BY created_at");
    $stmt->execute();
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($pending) {
        echo sprintf("%-5s %-15s %s\n", "ID", "Command", "Created At");
        echo str_repeat("-", 60) . "\n";
        foreach ($pending as $p) {
            echo sprintf("%-5s %-15s %s\n", $p['id'], $p['command'], $p['created_at']);
        }
    } else {
        echo "No pending commands\n";
    }
    
    echo "\n[✓] Test complete!\n";
    
} catch (Exception $e) {
    echo "[✗] ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>
