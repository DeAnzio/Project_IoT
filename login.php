<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple login handler (connects to local iot_app database)
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username === '' || $password === '') {
        header('Location: login.php?error=empty');
        exit;
    }

    $dbHost = 'localhost';
    $dbUser = 'root';
    $dbPass = '';
    $dbName = 'iot_app';

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        // You may want to show a friendly page instead of exposing DB errors
        header('Location: login.php?error=invalid');
        exit;
    }

    $stmt = $conn->prepare('SELECT id, username, password FROM account WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $stored = $row['password'];
        $ok = false;
        // Support both hashed and plain passwords (legacy). Prefer hashed in future.
        if (password_verify($password, $stored)) {
            $ok = true;
        } elseif (hash_equals($stored, $password)) {
            $ok = true;
        }

        if ($ok) {
            // successful login
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $stmt->close();
            $conn->close();
            header('Location: dashboard.php');
            exit;
        }
    }

    // invalid credentials
    if (isset($stmt) && !empty($stmt)) $stmt->close();
    if (isset($conn) && $conn) $conn->close();
    header('Location: login.php?error=invalid');
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - O-WareHouse</title>
    <link rel="stylesheet" href="content/style/stylelogin.css">
</head>
<body>
    <div class="header">
            <div class="headerkiri">
            </div>
            <div class="headertengah"> 
                <img src="content/LogoWareHouse.png" alt="Logo">
                <div class="title">O - Warehouse</div>
            </div>
            <div class="headerkanan">
            </div>
    </div>

    <div class="main-content">
        <div class="login-container">
            <h2>Sign In</h2>
            
            <div class="error-message" id="errorMessage"></div>

            <form action="login.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required>
                        <span class="toggle-password" onclick="togglePassword()">👁</span>
                    </div>
                </div>

                <button type="submit" class="btn-signin">Sign In</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '👁‍🗨';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁';
            }
        }

        // Menampilkan error message jika ada
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        
        if (error === 'invalid') {
            const errorMsg = document.getElementById('errorMessage');
            errorMsg.textContent = 'Username atau password salah!';
            errorMsg.classList.add('show');
        } else if (error === 'empty') {
            const errorMsg = document.getElementById('errorMessage');
            errorMsg.textContent = 'Harap isi username dan password!';
            errorMsg.classList.add('show');
        }
    </script>
</body>
</html>