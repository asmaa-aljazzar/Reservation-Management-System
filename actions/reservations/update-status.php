<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../includes/paths.php';
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/reservation_functions.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('pages/admin/reservations.php'));
    exit;
}

$bookingId = filter_var($_POST['booking_id'] ?? null, FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';
$page = filter_var($_POST['page'] ?? 1, FILTER_VALIDATE_INT);
$page = $page !== false && $page > 0 ? $page : 1;
$filterStatus = $_POST['filter_status'] ?? 'all';
$filterQuery = in_array($filterStatus, ['pending', 'confirmed', 'rejected', 'cancelled'], true)
    ? '&status=' . rawurlencode($filterStatus)
    : '';
$error = '';

if ($bookingId === false || $bookingId < 1 || !in_array($status, ['confirmed', 'rejected', 'cancelled'], true)) {
    $error = 'Invalid reservation status request.';
} else {
    $result = setReservationStatus($bookingId, $status, (int) ($_SESSION['user']['user_id'] ?? 0));
    if ($result !== true) {
        $error = is_array($result) ? implode(' ', $result) : 'Reservation status could not be updated.';
    }
}

$_SESSION['flash'] = [
    'type' => $error === '' ? 'success' : 'error',
    'message' => $error === ''
        ? match ($status) {
            'confirmed' => 'Reservation confirmed.',
            'rejected' => 'Reservation rejected.',
            default => 'Reservation cancelled.',
        }
        : $error,
];

header('Location: ' . app_url('pages/admin/reservations.php?page=' . $page . $filterQuery));
exit;
