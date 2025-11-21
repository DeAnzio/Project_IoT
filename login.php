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