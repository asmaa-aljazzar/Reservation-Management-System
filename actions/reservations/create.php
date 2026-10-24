<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../includes/paths.php';
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/reservation_functions.php';
require_once __DIR__ . '/../../includes/validation.php';

requireRole('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('pages/all-slots.php'));
    exit;
}

$slotId = filter_var($_POST['slot_id'] ?? null, FILTER_VALIDATE_INT);
$numberOfPeople = filter_var($_POST['number_of_people'] ?? null, FILTER_VALIDATE_INT);
$purpose = trim($_POST['purpose'] ?? '');
$userId = (int) $_SESSION['user']['user_id'];
$error = '';

if ($slotId === false || $slotId < 1 || $numberOfPeople === false
    || !isValidReservationData($userId, $slotId, $numberOfPeople, $purpose)) {
    $error = 'The slot is unavailable or the reservation details are invalid.';
} else {
    $result = createReservation($userId, $slotId, $numberOfPeople, $purpose);
    if ($result !== true) {
        $error = is_array($result) ? implode(' ', $result) : 'Could not create the reservation.';
    }
}

$_SESSION['flash'] = [
    'type' => $error === '' ? 'success' : 'error',
    'message' => $error === '' ? 'Reservation request sent.' : $error,
];

if ($error === '') {
    header('Location: ' . app_url('pages/my-reservations.php'));
} elseif ($slotId !== false && $slotId > 0) {
    header('Location: ' . app_url('pages/create-reservation.php?slot_id=' . $slotId));
} else {
    header('Location: ' . app_url('pages/all-slots.php'));
}
exit;