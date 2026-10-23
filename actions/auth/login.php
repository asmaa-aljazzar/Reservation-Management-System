<?php
require_once __DIR__ . '/../../includes/paths.php';
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/access_control.php';

if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('login.php'));
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (loginUser($email, $password)) {
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Logged in successfully.',
    ];

    if (checkRole('admin')) {
        $destination = 'pages/admin/dashboard.php';
    } else {
        $destination = 'pages/rooms.php';
    }
} else {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Email or password is incorrect.',
    ];

    $destination = 'login.php';
}

header('Location: ' . app_url($destination));
exit;
