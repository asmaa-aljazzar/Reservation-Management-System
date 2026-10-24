<?php
require_once __DIR__ . '/../includes/slot_functions.php';

$pageTitle = 'All Slots | Study Nook';
$pageDescription = 'Browse upcoming study slots in active rooms.';
$slotsPerPage = 9;
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$slotsTotalPages = max(1, (int) ceil(countAvailableSlots() / $slotsPerPage));
$slotsCurrentPage = min($requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1, $slotsTotalPages);
$slots = getActiveSlots($slotsCurrentPage, $slotsPerPage);
$roomImages = ['study-room.png', 'quiet-study-room.png', 'group-study-room.png'];

require __DIR__ . '/../includes/layout/header.php';
require __DIR__ . '/../includes/layout/navbar.php';
?>
<main id="main-content" class="page-section">
    <div class="container">
        <div class="section-heading home-section__heading">
            <div>
                <p class="eyebrow">PLAN YOUR STUDY TIME</p>
                <h1>Available slots</h1>
                <p class="text-muted">Choose a future slot and send a reservation request.</p>
            </div>
        </div>

        <?php if ($slots === []): ?>
            <section class="card rooms-empty" aria-live="polite">
                <h2>No upcoming slots</h2>
                <p class="text-muted">Please check back later for new study times.</p>
            </section>
        <?php else: ?>
            <div class="room-grid">
                <?php foreach ($slots as $slot): ?>
                    <?php
                    $roomImage = trim((string) ($slot['image_url'] ?? ''));
                    if ($roomImage === '') {
                        $roomImage = 'assets/images/rooms/' . $roomImages[((int) $slot['room_id'] - 1) % count($roomImages)];
                    }
                    $remainingSeats = (int) $slot['remaining_seats'];
                    $confirmedSeats = (int) $slot['confirmed_seats'];
                    $pendingSeats = (int) $slot['pending_seats'];
                    ?>
                    <a class="room-card card slot-card" href="<?= htmlspecialchars(app_url('pages/create-reservation.php?slot_id=' . (int) $slot['slot_id']), ENT_QUOTES, 'UTF-8') ?>">
                        <img class="room-card__image" src="<?= htmlspecialchars(app_url($roomImage), ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                        <div class="room-card__body">
                            <div class="room-card__topline">
                                <span class="room-card__tag"><?= htmlspecialchars((string) $slot['room_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="room-card__capacity"><?= (int) $slot['slot_capacity'] ?> seats</span>
                            </div>
                            <h2><?= htmlspecialchars(date('D, M j, Y', strtotime((string) $slot['slot_date'])), ENT_QUOTES, 'UTF-8') ?></h2>
                            <p class="slot-card__time"><?= htmlspecialchars(date('g:i A', strtotime((string) $slot['start_time'])), ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars(date('g:i A', strtotime((string) $slot['end_time'])), ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="slot-card__seat-counts" aria-label="Seat status">
                                <span><strong><?= $confirmedSeats ?></strong><small>confirmed</small></span>
                                <span><strong><?= $pendingSeats ?></strong><small>pending</small></span>
                                <span><strong><?= $remainingSeats ?></strong><small>remaining</small></span>
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
            <nav class="room-pagination" aria-label="Available slots pages">
                <span class="room-pagination__status">Page <?= $slotsCurrentPage ?> of <?= $slotsTotalPages ?></span>
                <div class="room-pagination__controls">
                    <?php if ($slotsCurrentPage > 1): ?>
                        <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/all-slots.php?page=' . ($slotsCurrentPage - 1)), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                    <?php endif; ?>
                    <?php if ($slotsCurrentPage < $slotsTotalPages): ?>
                        <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/all-slots.php?page=' . ($slotsCurrentPage + 1)), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>    </div>
</main>
<?php require __DIR__ . '/../includes/layout/footer.php'; ?>
