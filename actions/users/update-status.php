<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../includes/paths.php';
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/user_functions.php';
require_once __DIR__ . '/../../includes/validation.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('pages/admin/users.php'));
    exit();
}

$userId = (int) ($_POST['user_id'] ?? 0);
$statusValue = $_POST['is_active'] ?? '';
$page = max(1, (int) ($_POST['page'] ?? 1));
$error = '';

if (!isValidUserStatusData($userId, $statusValue)) {
    $error = 'Invalid user status request.';
} elseif (!userExists($userId)) {
    $error = 'User does not exist.';
} elseif ((int) ($_SESSION['user']['user_id'] ?? 0) === $userId && $statusValue === '0') {
    $error = 'You cannot deactivate your own account.';
} elseif (!setUserStatus($userId, $statusValue === '1')) {
    $error = 'User status could not be updated.';
}

if ($error !== '') {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => $error,
    ];
} else {
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => $statusValue === '1' ? 'User activated.' : 'User deactivated.',
    ];
}

header('Location: ' . app_url('pages/admin/users.php?page=' . $page));
exit();
