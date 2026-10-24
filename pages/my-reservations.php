<?php
require_once __DIR__ . '/../includes/access_control.php';
require_once __DIR__ . '/../includes/reservation_functions.php';
requireRole('user');

$userId = (int) $_SESSION['user']['user_id'];
$perPage = 10;
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$totalPages = max(1, (int) ceil(countUserReservations($userId) / $perPage));
$currentPage = min($requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1, $totalPages);
$reservations = getUserReservations($userId, $currentPage, $perPage);

$pageTitle = 'My reservations | Study Nook';
$pageDescription = 'View your study room reservation requests and history.';
require __DIR__ . '/../includes/layout/header.php';
require __DIR__ . '/../includes/layout/navbar.php';
?>
<main id="main-content" class="page-section">
    <div class="container">
        <div class="section-heading">
            <p class="eyebrow">YOUR STUDY TIME</p>
            <h1>My reservations</h1>
            <p class="text-muted">Track requests and see your reservation history.</p>
        </div>

        <?php if ($reservations === []): ?>
            <section class="card rooms-empty">
                <h2>No reservations yet</h2>
                <p class="text-muted">When you request a slot, it will appear here.</p>
                <a class="button button--primary" href="<?= htmlspecialchars(app_url('pages/all-slots.php'), ENT_QUOTES, 'UTF-8') ?>">Browse available slots</a>
            </section>
        <?php else: ?>
            <div class="reservation-list">
                <?php foreach ($reservations as $reservation): ?>
                    <?php
                    $canCancel = in_array($reservation['status'], ['pending', 'confirmed'], true)
                        && (strtotime((string) $reservation['slot_date'] . ' ' . (string) $reservation['start_time']) > time());
                    $statusLabels = [
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ];
                    ?>
                    <article class="card reservation-item">
                        <div class="reservation-item__heading">
                            <div>
                                <h2><?= htmlspecialchars((string) $reservation['room_name'], ENT_QUOTES, 'UTF-8') ?></h2>
                                <p><?= htmlspecialchars(date('D, M j, Y', strtotime((string) $reservation['slot_date'])), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars(date('g:i A', strtotime((string) $reservation['start_time'])), ENT_QUOTES, 'UTF-8') ?>–<?= htmlspecialchars(date('g:i A', strtotime((string) $reservation['end_time'])), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <span class="reservation-status reservation-status--<?= htmlspecialchars((string) $reservation['status'], ENT_QUOTES, 'UTF-8') ?>"><?= $statusLabels[$reservation['status']] ?? htmlspecialchars((string) $reservation['status'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="reservation-item__details">
                            <p><strong><?= (int) $reservation['number_of_people'] ?></strong> <?= (int) $reservation['number_of_people'] === 1 ? 'person' : 'people' ?></p>
                            <p><?= htmlspecialchars((string) $reservation['purpose'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <?php if ($canCancel): ?>
                            <form class="reservation-item__actions" action="<?= htmlspecialchars(app_url('actions/reservations/cancel.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                                <input type="hidden" name="booking_id" value="<?= (int) $reservation['booking_id'] ?>">
                                <button class="button button--secondary" type="submit">Cancel reservation</button>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="room-pagination" aria-label="Reservation pages">
                    <span class="room-pagination__status">Page <?= $currentPage ?> of <?= $totalPages ?></span>
                    <div class="room-pagination__controls">
                        <?php if ($currentPage > 1): ?>
                            <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/my-reservations.php?page=' . ($currentPage - 1)), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                        <?php endif; ?>
                        <?php if ($currentPage < $totalPages): ?>
                            <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/my-reservations.php?page=' . ($currentPage + 1)), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                        <?php endif; ?>
                    </div>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../includes/layout/footer.php'; ?>