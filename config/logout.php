<?php
// logout.php moved into config/
// Destroys session and redirects to login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = [];

// If there's a session cookie, clear it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Redirect to login page (absolute to project path)
// Use an absolute path for reliability when included from different locations
header('Location: /project_iot/login.php');
exit;
