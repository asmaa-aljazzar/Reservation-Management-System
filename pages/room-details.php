<?php
require_once __DIR__ . '/../includes/room_functions.php';
require_once __DIR__ . '/../includes/slot_functions.php';

$roomId = filter_var($_GET['room'] ?? null, FILTER_VALIDATE_INT);
if ($roomId === false || $roomId < 1) {
    header('Location: ' . app_url('pages/rooms.php'));
    exit;
}

$room = getActiveRoom($roomId);
if ($room === null) {
    header('Location: ' . app_url('pages/rooms.php'));
    exit;
}

$slotsPerPage = 9;
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$slotsTotalPages = max(1, (int) ceil(countAvailableSlotsForRoom($roomId) / $slotsPerPage));
$slotsCurrentPage = min($requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1, $slotsTotalPages);
$slots = getUpcomingSlotsForRoom($roomId, $slotsCurrentPage, $slotsPerPage);
$roomImage = trim((string) ($room['image_url'] ?? ''));
if ($roomImage === '') {
    $roomImages = ['study-room.png', 'quiet-study-room.png', 'group-study-room.png'];
    $roomImage = 'assets/images/rooms/' . $roomImages[($roomId - 1) % count($roomImages)];
}

$pageTitle = (string) $room['room_name'] . ' | Study Nook';
$pageDescription = 'View room details and available study times.';
require __DIR__ . '/../includes/layout/header.php';
require __DIR__ . '/../includes/layout/navbar.php';
?>
<main id="main-content" class="page-section room-details-page">
    <div class="container">
        <a class="room-details__back" href="<?= htmlspecialchars(app_url('pages/rooms.php'), ENT_QUOTES, 'UTF-8') ?>">← All rooms</a>
        <section class="card room-details-card">
            <img class="room-details-card__image" src="<?= htmlspecialchars(app_url($roomImage), ENT_QUOTES, 'UTF-8') ?>" alt="">
            <div class="room-details-card__content">
                <p class="eyebrow">STUDY ROOM</p>
                <h1><?= htmlspecialchars((string) $room['room_name'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="room-details-card__capacity"><?= (int) $room['room_capacity'] ?> seats</p>
                <p><?= trim((string) ($room['description'] ?? '')) !== '' ? nl2br(htmlspecialchars((string) $room['description'], ENT_QUOTES, 'UTF-8')) : 'No description provided.' ?></p>
            </div>
        </section>

        <section class="room-details-slots" aria-labelledby="room-slots-title">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">PLAN YOUR VISIT</p>
                    <h2 id="room-slots-title">Available study times</h2>
                    <p class="text-muted">Choose a slot to send a reservation request.</p>
                </div>
            </div>
            <?php if ($slots === []): ?>
                <div class="card rooms-empty">
                    <h3>No available slots right now</h3>
                    <p class="text-muted">Please check back later for new study times.</p>
                </div>
            <?php else: ?>
                <div class="room-grid">
                    <?php foreach ($slots as $slot): ?>
                        <a class="room-card card slot-card" href="<?= htmlspecialchars(app_url('pages/create-reservation.php?slot_id=' . (int) $slot['slot_id']), ENT_QUOTES, 'UTF-8') ?>">
                            <div class="room-card__body">
                                <div class="room-card__topline">
                                    <span class="room-card__tag"><?= htmlspecialchars(date('D, M j, Y', strtotime((string) $slot['slot_date'])), ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="room-card__capacity"><?= (int) $slot['slot_capacity'] ?> seats</span>
                                </div>
                                <h3><?= htmlspecialchars(date('g:i A', strtotime((string) $slot['start_time'])), ENT_QUOTES, 'UTF-8') ?>–<?= htmlspecialchars(date('g:i A', strtotime((string) $slot['end_time'])), ENT_QUOTES, 'UTF-8') ?></h3>
                                <div class="slot-card__seat-counts" aria-label="Seat status">
                                    <span><strong><?= (int) $slot['confirmed_seats'] ?></strong><small>confirmed</small></span>
                                    <span><strong><?= (int) $slot['pending_seats'] ?></strong><small>pending</small></span>
                                    <span><strong><?= (int) $slot['remaining_seats'] ?></strong><small>remaining</small></span>
                                </div>
                                <div class="room-card__bottom">
                                    <span class="room-card__link">Reserve slot <span aria-hidden="true">→</span></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($slotsTotalPages > 1): ?>
                <nav class="room-pagination" aria-label="Room slot pages">
                    <span class="room-pagination__status">Page <?= $slotsCurrentPage ?> of <?= $slotsTotalPages ?></span>
                    <div class="room-pagination__controls">
                        <?php if ($slotsCurrentPage > 1): ?>
                            <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/room-details.php?room=' . $roomId . '&page=' . ($slotsCurrentPage - 1)), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                        <?php endif; ?>
                        <?php if ($slotsCurrentPage < $slotsTotalPages): ?>
                            <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/room-details.php?room=' . $roomId . '&page=' . ($slotsCurrentPage + 1)), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                        <?php endif; ?>
                    </div>
                </nav>
            <?php endif; ?>        </section>
    </div>
</main>
<?php require __DIR__ . '/../includes/layout/footer.php'; ?>