<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../includes/paths.php';
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/reservation_functions.php';

requireRole('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('pages/my-reservations.php'));
    exit;
}

$bookingId = filter_var($_POST['booking_id'] ?? null, FILTER_VALIDATE_INT);
$userId = (int) $_SESSION['user']['user_id'];
$cancelled = $bookingId !== false && $bookingId > 0 && cancelReservation($bookingId, $userId);

$_SESSION['flash'] = [
    'type' => $cancelled ? 'success' : 'error',
    'message' => $cancelled ? 'Reservation cancelled.' : 'This reservation could not be cancelled.',
];

header('Location: ' . app_url('pages/my-reservations.php'));
exit;