<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../includes/paths.php';
require_once __DIR__ . '/../includes/access_control.php';
require_once __DIR__ . '/../includes/reservation_functions.php';

$slotId = filter_var($_GET['slot_id'] ?? null, FILTER_VALIDATE_INT);
if ($slotId === false || $slotId < 1) {
    header('Location: ' . app_url('pages/all-slots.php'));
    exit;
}

if (!isset($_SESSION['user']['user_id'])) {
    $_SESSION['return_to'] = 'pages/create-reservation.php?slot_id=' . $slotId;
    header('Location: ' . app_url('login.php'));
    exit;
}

requireRole('user');
$userId = (int) $_SESSION['user']['user_id'];
if (userHasActiveReservation($userId, $slotId)) {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'You already have a pending or confirmed reservation for this slot.'];
    header('Location: ' . app_url('pages/all-slots.php'));
    exit;
}

$slot = getSlotForReservation($slotId);
if ($slot === null || (int) $slot['remaining_seats'] < 1) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'This slot is no longer available.'];
    header('Location: ' . app_url('pages/all-slots.php'));
    exit;
}

$pageTitle = 'Reserve a slot | Study Nook';
$pageDescription = 'Send a reservation request for a study slot.';
require __DIR__ . '/../includes/layout/header.php';
require __DIR__ . '/../includes/layout/navbar.php';
?>
<main id="main-content" class="page-section reservation-page">
    <div class="container">
        <section class="card reservation-form-card">
            <a class="reservation-form-card__back" href="<?= htmlspecialchars(app_url('pages/all-slots.php'), ENT_QUOTES, 'UTF-8') ?>">← All slots</a>
            <p class="eyebrow">RESERVE A STUDY TIME</p>
            <h1><?= htmlspecialchars((string) $slot['room_name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="reservation-slot-summary">
                <p><?= htmlspecialchars(date('D, M j, Y', strtotime((string) $slot['slot_date'])), ENT_QUOTES, 'UTF-8') ?></p>
                <span aria-hidden="true">·</span>
                <p><?= htmlspecialchars(date('g:i A', strtotime((string) $slot['start_time'])), ENT_QUOTES, 'UTF-8') ?>–<?= htmlspecialchars(date('g:i A', strtotime((string) $slot['end_time'])), ENT_QUOTES, 'UTF-8') ?></p>
                <span class="reservation-slot-summary__seats"><?= (int) $slot['remaining_seats'] ?> seats available</span>
            </div>
            <form class="reservation-form" action="<?= htmlspecialchars(app_url('actions/reservations/create.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                <input type="hidden" name="slot_id" value="<?= (int) $slot['slot_id'] ?>">
                <div class="form-field">
                    <label class="form-label" for="number_of_people">Number of people</label>
                    <div class="number-stepper">
                        <button class="number-stepper__button" type="button" data-number-step="-1" aria-label="Decrease number of people">−</button>
                        <input class="form-control" id="number_of_people" name="number_of_people" type="number" min="1" max="<?= (int) $slot['remaining_seats'] ?>" step="1" value="1" required>
                        <button class="number-stepper__button" type="button" data-number-step="1" aria-label="Increase number of people">+</button>
                    </div>
                </div>
                <div class="form-field">
                    <label class="form-label" for="purpose">Purpose</label>
                    <textarea class="form-control" id="purpose" name="purpose" maxlength="1000" placeholder="What will you be using the room for?" required></textarea>
                </div>
                <button class="button button--primary" type="submit">Send reservation request</button>
            </form>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../includes/layout/footer.php'; ?>
