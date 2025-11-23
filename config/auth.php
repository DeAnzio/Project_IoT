<?php
// Simple auth include - require this on pages that need login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user not logged in, redirect to login page with return URL
if (empty($_SESSION['user_id'])) {
    $current = $_SERVER['REQUEST_URI'] ?? '';
    $redirect = urlencode($current);
    header('Location: login.php?redirect=' . $redirect);
    exit;
}
