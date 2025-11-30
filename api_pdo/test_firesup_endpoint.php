<?php
// Bypass koneksi_pdo.php header - setup DB langsung
$pdo = new PDO(
    "mysql:host=localhost;dbname=iot_app;charset=utf8mb4",
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Inject ke global untuk firesup.logic.php
$GLOBALS['koneksi'] = $pdo;

header('Content-Type: text/plain; charset=utf-8');

echo "=== FIRESUP LOGIC TEST ===\n\n";

try {
    require_once __DIR__ . '/logic/firesup.logic.php';
    echo "[✓] Firesup logic loaded\n\n";
    
    // Test 1: High MQ-2 value
    echo "Test 1: MQ-2 = 2000 (di atas threshold 1500)\n";
    echo str_repeat("-", 50) . "\n";
    $result1 = firesup_handle(2000, 'esp32-unit-002');
    echo json_encode($result1, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";
    
    // Test 2: Normal MQ-2 value
    echo "Test 2: MQ-2 = 1000 (di bawah threshold 1500)\n";
    echo str_repeat("-", 50) . "\n";
    $result2 = firesup_handle(1000, 'esp32-unit-002');
    echo json_encode($result2, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";
    
    // Check pending commands
    echo "Pending Commands untuk esp32-unit-002:\n";
    echo str_repeat("-", 50) . "\n";
    $stmt = $pdo->prepare("SELECT id, command, status, created_at FROM commands WHERE device_id='esp32-unit-002' AND status='pending' ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($rows) {
        echo sprintf("%-5s %-15s %-10s %s\n", "ID", "Command", "Status", "Created At");
        echo str_repeat("-", 50) . "\n";
        foreach ($rows as $row) {
            echo sprintf("%-5s %-15s %-10s %s\n", $row['id'], $row['command'], $row['status'], $row['created_at']);
        }
    } else {
        echo "No pending commands\n";
    }
    
} catch (Exception $e) {
    echo "[✗] ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>
