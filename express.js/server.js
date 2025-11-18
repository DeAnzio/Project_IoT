// server.js

const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const mysql = require('mysql2/promise'); // Menggunakan promise agar lebih mudah di Node.js
const cors = require('cors'); // Diperlukan untuk akses dari klien web dan ESP32/PHP

const app = express();
const server = http.createServer(app);

// Inisialisasi Socket.IO
// Kita akan menggunakan port 3000 untuk server Node.js dan Socket.IO
const io = new Server(server, {
  cors: {
    origin: ["http://localhost", "http://127.0.0.1", "http://your-domain.com"], // Izinkan dari sumber web PHP Anda
    methods: ["GET", "POST"]
  }
});

// Konfigurasi Database (berdasarkan config/koneksi.php)
const dbConfig = {
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'iot_app'
};

// Middleware
app.use(cors());
app.use(express.json()); // Untuk membaca body JSON dari request POST (ESP32)

// Koneksi ke Database
let pool;
try {
    pool = mysql.createPool(dbConfig);
    console.log('Terhubung ke database MySQL.');
} catch (error) {
    console.error('Gagal terhubung ke database:', error.message);
    process.exit(1);
}


// --- 1. Konversi API: POST /api/save_data.php (Penerimaan Data Sensor) ---
// Ini adalah rute paling penting untuk realtime
app.post('/api/save_data', async (req, res) => {
    const { device_id, sensor_type = 'unknown', value, raw } = req.body;

    if (!device_id || value === null) {
        return res.status(400).json({ error: 'Missing required fields' });
    }

    try {
        const connection = await pool.getConnection();

        // 1. Simpan/Update device (Logic dari save_data.php)
        await connection.query(
            "INSERT INTO devices (device_id, last_seen) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE last_seen = NOW()",
            [device_id]
        );

        // 2. Simpan data sensor (Logic dari save_data.php)
        const [result] = await connection.query(
            "INSERT INTO sensor_data (device_id, sensor_type, value, raw_value) VALUES (?, ?, ?, ?)",
            [device_id, sensor_type, value, raw]
        );
        
        connection.release();

        // **REALTIME STEP: Broadcast Data Baru via Socket.IO**
        const newSensorData = {
            value: value,
            raw_value: raw,
            recorded_at: new Date().toLocaleString('id-ID', { hour12: false }), // Format waktu untuk klien
            device_id: device_id
        };
        io.emit('newSensorData', newSensorData); 
        console.log(`Data dari ${device_id} diterima & dibroadcast.`);

        res.json({ status: 'ok', id: result.insertId });

    } catch (error) {
        console.error('Error saat menyimpan data sensor:', error);
        res.status(500).json({ error: 'Failed to save sensor data' });
    }
});


// --- 2. Konversi API: GET /api/get_command.php (Polling dari ESP32) ---
app.get('/api/get_command', async (req, res) => {
    const device_id = req.query.device_id;
    if (!device_id) {
        return res.status(400).json({ error: 'Missing device_id' });
    }

    try {
        // Ambil perintah pending paling awal (Logic dari get_command.php)
        const [rows] = await pool.query(
            "SELECT id, command, payload FROM commands WHERE device_id = ? AND status = 'pending' ORDER BY created_at ASC LIMIT 1",
            [device_id]
        );

        if (rows.length > 0) {
            const cmd = rows[0];
            res.json({
                status: 'ok',
                command_id: cmd.id,
                command: cmd.command,
                payload: cmd.payload
            });
        } else {
            res.json({ status: 'idle' });
        }

    } catch (error) {
        console.error('Error saat mengambil command:', error);
        res.status(500).json({ error: 'Internal Server Error' });
    }
});


// --- 3. Konversi API: POST /api/set_command.php (Pengiriman Perintah dari Web PHP) ---
// Rute ini dipanggil oleh logic/lampu.logic.php
app.post('/api/set_command', async (req, res) => {
    const { device_id, command, payload = null } = req.body;

    if (!device_id || !command) {
        return res.status(400).json({ error: 'Missing required fields' });
    }

    try {
        // Simpan perintah baru (Logic dari set_command.php)
        const [result] = await pool.query(
            "INSERT INTO commands (device_id, command, payload, status) VALUES (?, ?, ?, 'pending')",
            [device_id, command, payload]
        );

        // Opsional: Notifikasi command baru (misalnya untuk update UI status remote)
        // io.emit('newCommandPending', { device_id, command });

        res.json({ status: 'ok', command_id: result.insertId });

    } catch (error) {
        console.error('Error saat menyimpan command:', error);
        res.status(500).json({ error: 'Failed to save command' });
    }
});


// --- 4. Konversi API: POST /api/ack_command.php (Konfirmasi Eksekusi dari ESP32) ---
app.post('/api/ack_command', async (req, res) => {
    const { command_id, result: command_result } = req.body;

    if (!command_id) {
        return res.status(400).json({ error: 'Missing command_id' });
    }

    try {
        // Update status command (Logic dari ack_command.php)
        const [result] = await pool.query(
            "UPDATE commands SET status = 'executed', executed_at = NOW(), payload = ? WHERE id = ?",
            [command_result, command_id]
        );

        if (result.affectedRows > 0) {
            // Opsional: Notifikasi bahwa command sudah dieksekusi
            io.emit('commandExecuted', { command_id, result: command_result });
            res.json({ status: 'ok' });
        } else {
            res.status(500).json({ error: 'Update command failed' });
        }

    } catch (error) {
        console.error('Error saat ACK command:', error);
        res.status(500).json({ error: 'Internal Server Error' });
    }
});

// --- 5. Konversi API: GET /api/get_sensor_data.php (Untuk Menampilkan di Dashboard Web) ---
app.get('/api/get_sensor_data', async (req, res) => {
    const device_id = req.query.device_id;
    if (!device_id) {
        return res.status(400).json({ error: 'Missing device_id' });
    }

    try {
        // Ambil data sensor terbaru, misal 20 data terakhir
        const [rows] = await pool.query(
            "SELECT sensor_type, value, raw_value, recorded_at FROM sensor_data WHERE device_id = ? ORDER BY recorded_at DESC LIMIT 20",
            [device_id]
        );

        res.json(rows);
    } catch (error) {
        console.error('Error saat mengambil data sensor:', error);
        res.status(500).json({ error: 'Failed to fetch sensor data' });
    }
});


// Event Socket.IO (Koneksi Klien)
io.on('connection', (socket) => {
  console.log('Klien web terhubung ke Socket.IO');
  socket.on('disconnect', () => {
    console.log('Klien web terputus');
  });
});

// Jalankan Server di Port 3000
const PORT = 3000;
server.listen(PORT, () => {
  console.log(`Server Node.js berjalan di http://0.0.0.0:${PORT}`);
});