<?php
require_once __DIR__ . '/../includes/room_functions.php';

$pageTitle = 'Rooms | Study Nook';
$pageDescription = 'Browse the library study rooms.';
$roomsPerPage = 9;
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$roomsTotalPages = max(1, (int) ceil(countActiveRooms() / $roomsPerPage));
$roomsCurrentPage = min($requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1, $roomsTotalPages);
$rooms = getActiveRoomsWithUpcomingSlotCounts($roomsCurrentPage, $roomsPerPage);
require __DIR__ . '/../includes/layout/header.php';
require __DIR__ . '/../includes/layout/navbar.php';
?>
<main id="main-content" class="page-section">
    <div class="container">
        <div class="section-heading home-section__heading">
            <div>
                <p class="eyebrow">FIND YOUR SPACE</p>
                <h1>Study rooms</h1>
                <p class="text-muted">Choose a room and see its upcoming study times.</p>
            </div>
        </div>

        <?php if ($rooms === []): ?>
            <section class="card rooms-empty" aria-live="polite">
                <h2>No rooms available right now</h2>
                <p class="text-muted">Please check back later.</p>
            </section>
        <?php else: ?>
            <?php $roomImages = ['study-room.png', 'quiet-study-room.png', 'group-study-room.png']; ?>
            <div class="room-grid">
                <?php foreach ($rooms as $room): ?>
                    <?php
                    $roomImage = trim((string) ($room['image_url'] ?? ''));
                    if ($roomImage === '') {
                        $roomImage = 'assets/images/rooms/' . $roomImages[((int) $room['room_id'] - 1) % count($roomImages)];
                    }
                    ?>
                    <a class="room-card card" href="<?= htmlspecialchars(app_url('pages/room-details.php?room=' . (int) $room['room_id']), ENT_QUOTES, 'UTF-8') ?>">
                        <img class="room-card__image" src="<?= htmlspecialchars(app_url($roomImage), ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                        <div class="room-card__body">
                            <div class="room-card__topline">
                                <span class="room-card__tag">Room</span>
                                <span class="room-card__capacity"><?= (int) $room['room_capacity'] ?> seats</span>
                            </div>
                            <h2><?= htmlspecialchars((string) $room['room_name'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <p><?= trim((string) ($room['description'] ?? '')) !== ''
                                ? htmlspecialchars((string) $room['description'], ENT_QUOTES, 'UTF-8')
                                : 'No description provided.' ?></p>
                            <div class="room-card__bottom">
                                <span class="availability">
                                    <?= (int) $room['upcoming_slots'] ?> upcoming <?= (int) $room['upcoming_slots'] === 1 ? 'slot' : 'slots' ?>
                                </span>
                                <span class="room-card__link">View room <span aria-hidden="true">→</span></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($roomsTotalPages > 1): ?>
            <nav class="room-pagination" aria-label="Rooms pages">
                <span class="room-pagination__status">Page <?= $roomsCurrentPage ?> of <?= $roomsTotalPages ?></span>
                <div class="room-pagination__controls">
                    <?php if ($roomsCurrentPage > 1): ?>
                        <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/rooms.php?page=' . ($roomsCurrentPage - 1)), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                    <?php endif; ?>
                    <?php if ($roomsCurrentPage < $roomsTotalPages): ?>
                        <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/rooms.php?page=' . ($roomsCurrentPage + 1)), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>    </div>
</main>
<?php require __DIR__ . '/../includes/layout/footer.php'; ?>
