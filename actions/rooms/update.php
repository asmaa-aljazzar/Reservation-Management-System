<?php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();

require_once __DIR__ . '/../../includes/paths.php';
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/room_functions.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('pages/admin/rooms.php'));
    exit();
}

$roomId = (int) ($_POST['room_id'] ?? 0);
$roomName = trim($_POST['room_name'] ?? '');
$roomCapacity = filter_var(trim($_POST['room_capacity'] ?? ''), FILTER_VALIDATE_INT);
$description = trim($_POST['description'] ?? '');
$page = max(1, (int) ($_POST['page'] ?? 1));
$error = '';

if ($roomId < 1 || $roomCapacity === false) {
    $error = 'Please enter valid room details.';
} else {
    try {
        $result = updateRoom($roomId, $roomName, $roomCapacity, $description === '' ? null : $description);

        if (is_array($result)) {
            $error = implode(' ', $result);
        } elseif ($result !== true) {
            $error = 'Error while updating room.';
        }
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? null) === 1062) {
            $error = 'A room with that name already exists.';
        } else {
            throw $e;
        }
    }
}

if ($error !== '') {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => $error,
    ];
} else {
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Room updated successfully.',
    ];
}

header('Location: ' . app_url('pages/admin/rooms.php?page=' . $page));
exit();
