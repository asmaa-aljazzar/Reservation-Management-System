<?php
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/reservation_functions.php';
requireRole('admin');
$adminPage = true;
$currentAdminPage = 'reservations.php';
$pageTitle = 'Manage Reservations | Study Nook';
$pageDescription = 'Review library room reservations.';
$reservationStatusOptions = ['pending', 'confirmed', 'rejected', 'cancelled'];
$requestedStatus = $_GET['status'] ?? 'all';
$reservationStatus = in_array($requestedStatus, $reservationStatusOptions, true) ? $requestedStatus : null;
$reservationsPerPage = 10;
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$reservationsCurrentPage = $requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1;
$reservationsTotalPages = max(1, (int) ceil(countAdminReservations($reservationStatus) / $reservationsPerPage));
$reservationsCurrentPage = min($reservationsCurrentPage, $reservationsTotalPages);
$reservations = getAdminReservations($reservationsCurrentPage, $reservationsPerPage, $reservationStatus);
$statusQuery = $reservationStatus !== null ? '&status=' . rawurlencode($reservationStatus) : '';
require __DIR__ . '/../../includes/layout/header.php';
require __DIR__ . '/../../includes/layout/navbar.php';
?>
<main id="main-content" class="admin-page page-section">
    <div class="container admin-layout">
        <?php require __DIR__ . '/../../includes/layout/admin_sidebar.php'; ?>
        <div class="admin-content">
            <header class="admin-page-heading">
                <p class="eyebrow">ADMINISTRATION</p>
                <h1>Reservations</h1>
                <p class="text-muted">Review upcoming requests and check seat capacity before confirming.</p>
            </header>

            <section class="admin-panel card" aria-labelledby="reservations-list-title">
                <div class="admin-panel__heading">
                    <div>
                        <h2 id="reservations-list-title">Upcoming reservations</h2>
                        <p class="text-muted">Pending requests appear first. Reviewed reservations stay visible here.</p>
                    </div>
                    <form class="admin-filter" method="get" action="<?= htmlspecialchars(app_url('pages/admin/reservations.php'), ENT_QUOTES, 'UTF-8') ?>">
                        <label class="form-label" for="reservation-status-filter">Status</label>
                        <select class="form-control" id="reservation-status-filter" name="status">
                            <option value="all"<?= $reservationStatus === null ? ' selected' : '' ?>>All</option>
                            <?php foreach ($reservationStatusOptions as $statusOption): ?>
                                <option value="<?= htmlspecialchars($statusOption, ENT_QUOTES, 'UTF-8') ?>"<?= $reservationStatus === $statusOption ? ' selected' : '' ?>><?= htmlspecialchars(ucfirst($statusOption), ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="button button--secondary room-status__action" type="submit">Filter</button>
                    </form>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="visually-hidden">Upcoming reservations</caption>
                        <thead>
                            <tr>
                                <th scope="col">User</th>
                                <th scope="col">Room</th>
                                <th scope="col">Date and time</th>
                                <th class="admin-table__numeric" scope="col">People</th>
                                <th scope="col">Purpose</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($reservations === []): ?>
                                <tr><td class="admin-empty" colspan="7"><span class="admin-empty__title">No upcoming reservations</span></td></tr>
                            <?php else: ?>
                                <?php foreach ($reservations as $reservation): ?>
                                    <?php $isPending = $reservation['status'] === 'pending'; ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars((string) $reservation['full_name'], ENT_QUOTES, 'UTF-8') ?><br>
                                            <small class="text-muted"><?= htmlspecialchars((string) $reservation['email'], ENT_QUOTES, 'UTF-8') ?></small>
                                        </td>
                                        <td><?= htmlspecialchars((string) $reservation['room_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <?= htmlspecialchars(date('M j, Y', strtotime((string) $reservation['slot_date'])), ENT_QUOTES, 'UTF-8') ?><br>
                                            <small class="text-muted"><?= htmlspecialchars(date('g:i A', strtotime((string) $reservation['start_time'])), ENT_QUOTES, 'UTF-8') ?>–<?= htmlspecialchars(date('g:i A', strtotime((string) $reservation['end_time'])), ENT_QUOTES, 'UTF-8') ?></small>
                                        </td>
                                        <td class="admin-table__numeric"><?= (int) $reservation['number_of_people'] ?></td>
                                        <td><?= htmlspecialchars((string) $reservation['purpose'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><span class="reservation-status reservation-status--<?= htmlspecialchars((string) $reservation['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst((string) $reservation['status']), ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td class="room-actions">
                                            <?php if ($isPending): ?>
                                                <form action="<?= htmlspecialchars(app_url('actions/reservations/update-status.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                                                    <input type="hidden" name="booking_id" value="<?= (int) $reservation['booking_id'] ?>">
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <input type="hidden" name="page" value="<?= $reservationsCurrentPage ?>">
                                                    <input type="hidden" name="filter_status" value="<?= htmlspecialchars((string) $requestedStatus, ENT_QUOTES, 'UTF-8') ?>">
                                                    <button class="button button--secondary room-status__action" type="submit">Confirm</button>
                                                </form>
                                                <form action="<?= htmlspecialchars(app_url('actions/reservations/update-status.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                                                    <input type="hidden" name="booking_id" value="<?= (int) $reservation['booking_id'] ?>">
                                                    <input type="hidden" name="status" value="rejected">
                                                    <input type="hidden" name="page" value="<?= $reservationsCurrentPage ?>">
                                                    <input type="hidden" name="filter_status" value="<?= htmlspecialchars((string) $requestedStatus, ENT_QUOTES, 'UTF-8') ?>">
                                                    <button class="button button--secondary room-status__action" type="submit">Reject</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($isPending || $reservation['status'] === 'confirmed'): ?>
                                                <form action="<?= htmlspecialchars(app_url('actions/reservations/update-status.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                                                    <input type="hidden" name="booking_id" value="<?= (int) $reservation['booking_id'] ?>">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <input type="hidden" name="page" value="<?= $reservationsCurrentPage ?>">
                                                    <input type="hidden" name="filter_status" value="<?= htmlspecialchars((string) $requestedStatus, ENT_QUOTES, 'UTF-8') ?>">
                                                    <button class="button button--secondary room-status__action" type="submit">Cancel</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($reservationsTotalPages > 1): ?>
                    <?php $pageWindow = range(max(1, $reservationsCurrentPage - 2), min($reservationsTotalPages, $reservationsCurrentPage + 2)); ?>
                    <nav class="room-pagination" aria-label="Reservation pages">
                        <span class="room-pagination__status">Page <?= $reservationsCurrentPage ?> of <?= $reservationsTotalPages ?></span>
                        <div class="room-pagination__controls">
                            <?php if ($reservationsCurrentPage > 1): ?>
                                <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/admin/reservations.php?page=' . ($reservationsCurrentPage - 1) . $statusQuery), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                            <?php endif; ?>
                            <?php foreach ($pageWindow as $pageNumber): ?>
                                <a class="room-pagination__link<?= $pageNumber === $reservationsCurrentPage ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/admin/reservations.php?page=' . $pageNumber . $statusQuery), ENT_QUOTES, 'UTF-8') ?>"<?= $pageNumber === $reservationsCurrentPage ? ' aria-current="page"' : '' ?>><?= $pageNumber ?></a>
                            <?php endforeach; ?>
                            <?php if ($reservationsCurrentPage < $reservationsTotalPages): ?>
                                <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/admin/reservations.php?page=' . ($reservationsCurrentPage + 1) . $statusQuery), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../../includes/layout/footer.php'; ?>
