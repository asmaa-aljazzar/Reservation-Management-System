<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../includes/paths.php';
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/room_functions.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('pages/admin/rooms.php'));
    exit();
}

$roomId = (int) ($_POST['room_id'] ?? 0);
$statusValue = $_POST['is_active'] ?? '';
$page = max(1, (int) ($_POST['page'] ?? 1));
$error = '';

if ($roomId < 1 || ($statusValue !== '0' && $statusValue !== '1')) {
    $error = 'Invalid room status request.';
} elseif (!roomExists($roomId)) {
    $error = 'Room does not exist.';
} elseif (!setRoomStatus($roomId, $statusValue === '1')) {
    $error = 'Room status could not be updated.';
}

if ($error !== '') {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => $error,
    ];
} else {
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => $statusValue === '1' ? 'Room activated.' : 'Room deactivated.',
    ];
}

header('Location: ' . app_url('pages/admin/rooms.php?page=' . $page));
exit();
