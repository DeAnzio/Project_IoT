
<?php
require '../config/koneksi_pdo.php';

$pdo = $koneksi;

// Baca input JSON
$input = json_decode(file_get_contents('php://input'), true);

// Cek Input JSON
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Tidak ada data JSON diterima']);
    exit;
}

// Cek data JSON di API nya (jika tidak mau hapus kode ini)
// if (!$input || !isset($input['api_key']) || $input['api_key'] !== $API_KEY) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Unauthorized']);
//     exit;
// }

// Nangkap Data JSON
// extract raw values
$device_id = isset($input['device_id']) ? $input['device_id'] : null;
$command   = isset($input['command']) ? $input['command'] : null;
$payload   = isset($input['payload']) ? $input['payload'] : null;

// Cek apakah device_id dan command ada
if (!$device_id || !$command) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Simpan perintah baru (gunakan binding parameter --- jangan quote manual)
$stmt = $pdo->prepare("INSERT INTO commands (device_id, command, payload) VALUES (:device_id, :cmd, :payload)");
$ok = $stmt->execute([
    ':device_id' => $device_id,
    ':cmd'       => $command,
    ':payload'   => $payload
]);

if (!$ok) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save command']);
    exit;
}

// Kembalikan response
echo json_encode([
    'status' => 'ok',
    'command_id' => $pdo->lastInsertId()
]);
