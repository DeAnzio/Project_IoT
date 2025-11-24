<?php
require '../config/koneksi_pdo.php';

$pdo = $koneksi;

// delegate to canonical api implementation if present
if (file_exists(__DIR__ . '/../api/set_threshold.php')) {
	require __DIR__ . '/../api/set_threshold.php';
	return;
}

// Otherwise implement a simple handler similar to the original
require_once __DIR__ . '/../config/auth.php'; // ensure logged in

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo 'Method not allowed';
	exit;
}

$device_id = $_POST['device_id'] ?? 'global';
$temp_high = $_POST['temp_high'] ?? null;
$gas_high = $_POST['gas_high'] ?? null;
$gas_warn = $_POST['gas_warn'] ?? null;
$hum_low = $_POST['hum_low'] ?? null;

if ($temp_high === null || $gas_high === null || $gas_warn === null || $hum_low === null) {
	header('Location: /admin/thresholds.php?msg=' . urlencode('Missing fields'));
	exit;
}

try {
	$stmt = $pdo->prepare("INSERT INTO thresholds (device_id, temp_high, gas_high, gas_warn, hum_low) VALUES (:device_id, :temp_high, :gas_high, :gas_warn, :hum_low)
		ON DUPLICATE KEY UPDATE temp_high = VALUES(temp_high), gas_high = VALUES(gas_high), gas_warn = VALUES(gas_warn), hum_low = VALUES(hum_low)");
	$stmt->execute([
		':device_id' => $device_id,
		':temp_high' => floatval($temp_high),
		':gas_high' => intval($gas_high),
		':gas_warn' => intval($gas_warn),
		':hum_low' => floatval($hum_low),
	]);

	header('Location: /admin/thresholds.php?msg=' . urlencode('Saved'));
	exit;
} catch (Exception $e) {
	header('Location: /admin/thresholds.php?msg=' . urlencode('Error'));
	exit;
}
