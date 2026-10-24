<?php
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/dashboard_functions.php';
requireRole('admin');

$adminPage = true;
$currentAdminPage = 'dashboard.php';
$pageTitle = 'Admin Dashboard | Study Nook';
$pageDescription = 'Manage the Study Nook reservation system.';
$summary = getDashboardSummary();
$pendingReservations = getPendingReservations(5);
$todaysBookings = getTodaysBookings(5);
$weeklyBookingRows = getConfirmedBookingCountsByDay(7);
$weeklyBookingCounts = [];
foreach ($weeklyBookingRows as $row) {
    $weeklyBookingCounts[(string) $row['slot_date']] = (int) $row['booking_count'];
}
$weeklyChart = [];
for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
    $chartDate = date('Y-m-d', strtotime('+' . $dayOffset . ' days'));
    $weeklyChart[] = [
        'date' => $chartDate,
        'label' => date('D j', strtotime($chartDate)),
        'count' => $weeklyBookingCounts[$chartDate] ?? 0,
    ];
}
$weeklyChartMax = max(1, ...array_column($weeklyChart, 'count'));
$reservationStatusCounts = getUpcomingReservationStatusCounts();
$statusChartTotal = array_sum($reservationStatusCounts);
$statusChartColors = [
    'pending' => 'var(--color-terracotta)',
    'confirmed' => 'var(--color-success)',
    'rejected' => 'var(--color-danger)',
    'cancelled' => 'var(--color-text-muted)',
];
$statusChartSlices = [];
$statusChartAngle = -90;
if ($statusChartTotal > 0) {
    foreach ($reservationStatusCounts as $status => $count) {
        if ($count < 1) {
            continue;
        }
        $sweepAngle = $count / $statusChartTotal * 360;
        if ($sweepAngle >= 359.999) {
            $statusChartSlices[] = '<circle cx="100" cy="100" r="95" fill="' . $statusChartColors[$status] . '"><title>' . (int) $count . ' ' . ucfirst($status) . ' reservations</title></circle>';
            continue;
        }
        $nextAngle = $statusChartAngle + $sweepAngle;
        $startRadians = deg2rad($statusChartAngle);
        $endRadians = deg2rad($nextAngle);
        $startX = 100 + 95 * cos($startRadians);
        $startY = 100 + 95 * sin($startRadians);
        $endX = 100 + 95 * cos($endRadians);
        $endY = 100 + 95 * sin($endRadians);
        $largeArc = $sweepAngle > 180 ? 1 : 0;
        $path = sprintf(
            'M 100 100 L %.3f %.3f A 95 95 0 %d 1 %.3f %.3f Z',
            $startX,
            $startY,
            $largeArc,
            $endX,
            $endY
        );
        $statusChartSlices[] = '<path d="' . $path . '" fill="' . $statusChartColors[$status] . '" stroke="var(--color-surface)" stroke-width="2"><title>' . (int) $count . ' ' . ucfirst($status) . ' reservations</title></path>';
        $statusChartAngle = $nextAngle;
    }
}
require __DIR__ . '/../../includes/layout/header.php';
require __DIR__ . '/../../includes/layout/navbar.php';
?>
<main id="main-content" class="admin-page page-section">
    <div class="container admin-layout">
        <?php require __DIR__ . '/../../includes/layout/admin_sidebar.php'; ?>
        <div class="admin-content">
            <header class="admin-page-heading">
                <p class="eyebrow">ADMINISTRATION</p>
                <h1>Dashboard</h1>
                <p class="text-muted">A quick view of today’s activity and upcoming requests.</p>
            </header>

            <section class="dashboard-summary" aria-label="Reservation summary">
                <a class="dashboard-stat card" href="<?= htmlspecialchars(app_url('pages/admin/reservations.php?status=pending'), ENT_QUOTES, 'UTF-8') ?>">
                    <span class="dashboard-stat__label">Pending reservations</span>
                    <strong><?= (int) $summary['pending_reservations'] ?></strong>
                </a>
                <a class="dashboard-stat card" href="<?= htmlspecialchars(app_url('pages/admin/reservations.php'), ENT_QUOTES, 'UTF-8') ?>">
                    <span class="dashboard-stat__label">Confirmed bookings today</span>
                    <strong><?= (int) $summary['confirmed_today'] ?></strong>
                </a>
                <a class="dashboard-stat card" href="<?= htmlspecialchars(app_url('pages/admin/rooms.php'), ENT_QUOTES, 'UTF-8') ?>">
                    <span class="dashboard-stat__label">Active rooms</span>
                    <strong><?= (int) $summary['active_rooms'] ?></strong>
                </a>
                <a class="dashboard-stat card" href="<?= htmlspecialchars(app_url('pages/admin/slots.php'), ENT_QUOTES, 'UTF-8') ?>">
                    <span class="dashboard-stat__label">Upcoming slots</span>
                    <strong><?= (int) $summary['upcoming_slots'] ?></strong>
                </a>
            </section>

            <section class="dashboard-charts" aria-label="Reservation charts">
                <article class="admin-panel card" aria-labelledby="weekly-chart-title">
                    <div class="admin-panel__heading">
                        <div>
                            <h2 id="weekly-chart-title">Confirmed bookings over the next 7 days</h2>
                            <p class="text-muted">Number of reservations scheduled each day.</p>
                        </div>
                    </div>
                    <div class="dashboard-chart__bars" role="img" aria-label="Confirmed bookings for the next seven days">
                        <?php foreach ($weeklyChart as $chartDay): ?>
                            <?php $barHeight = (int) round(((int) $chartDay['count'] / $weeklyChartMax) * 100); ?>
                            <div class="dashboard-chart__column" title="<?= htmlspecialchars(date('M j', strtotime($chartDay['date'])) . ': ' . $chartDay['count'] . ' bookings', ENT_QUOTES, 'UTF-8') ?>">
                                <span class="dashboard-chart__value" aria-hidden="true"><?= (int) $chartDay['count'] ?></span>
                                <div class="dashboard-chart__track" aria-hidden="true">
                                    <span class="dashboard-chart__bar" style="height: <?= $barHeight ?>%"></span>
                                </div>
                                <span class="dashboard-chart__label" aria-hidden="true"><?= htmlspecialchars($chartDay['label'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="admin-panel card" aria-labelledby="status-chart-title">
                    <div class="admin-panel__heading">
                        <div>
                            <h2 id="status-chart-title">Upcoming reservations by status</h2>
                            <p class="text-muted">Requests for slots that have not started.</p>
                        </div>
                    </div>
                    <div class="dashboard-status-chart">
                        <svg class="dashboard-status-chart__pie" viewBox="0 0 200 200" role="img" aria-labelledby="status-pie-title">
                            <title id="status-pie-title">Upcoming reservations by status; hover a slice to see its count.</title>
                            <?php if ($statusChartSlices === []): ?>
                                <circle cx="100" cy="100" r="95" fill="var(--color-border)"><title>No upcoming reservations</title></circle>
                            <?php else: ?>
                                <?= implode("\n", $statusChartSlices) ?>
                            <?php endif; ?>
                        </svg>
                        <ul class="dashboard-status-chart__legend" aria-label="Reservation status counts">
                        <?php foreach ($reservationStatusCounts as $status => $count): ?>
                            <li class="dashboard-status-chart__item">
                                <span class="dashboard-status-chart__key dashboard-status-chart__key--<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></span>
                                <span><?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?></span>
                                <strong><?= (int) $count ?></strong>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </article>
            </section>

            <div class="dashboard-lists">
                <section class="admin-panel card" aria-labelledby="pending-reservations-title">
                    <div class="admin-panel__heading">
                        <div>
                            <h2 id="pending-reservations-title">Pending reservations</h2>
                            <p class="text-muted">Newest requests awaiting review.</p>
                        </div>
                        <a class="dashboard-panel__link" href="<?= htmlspecialchars(app_url('pages/admin/reservations.php?status=pending'), ENT_QUOTES, 'UTF-8') ?>">View all</a>
                    </div>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <caption class="visually-hidden">Newest pending reservations</caption>
                            <thead>
                                <tr><th scope="col">User</th><th scope="col">Room and time</th><th class="admin-table__numeric" scope="col">People</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($pendingReservations === []): ?>
                                    <tr><td class="admin-empty" colspan="3">No pending reservations.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($pendingReservations as $reservation): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $reservation['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <?= htmlspecialchars((string) $reservation['room_name'], ENT_QUOTES, 'UTF-8') ?><br>
                                                <small class="text-muted"><?= htmlspecialchars(date('M j · g:i A', strtotime((string) $reservation['slot_date'] . ' ' . (string) $reservation['start_time'])), ENT_QUOTES, 'UTF-8') ?></small>
                                            </td>
                                            <td class="admin-table__numeric"><?= (int) $reservation['number_of_people'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="admin-panel card" aria-labelledby="today-bookings-title">
                    <div class="admin-panel__heading">
                        <div>
                            <h2 id="today-bookings-title">Today’s bookings</h2>
                            <p class="text-muted">Confirmed reservations scheduled today.</p>
                        </div>
                    </div>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <caption class="visually-hidden">Today’s confirmed bookings</caption>
                            <thead>
                                <tr><th scope="col">User</th><th scope="col">Room and time</th><th class="admin-table__numeric" scope="col">People</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($todaysBookings === []): ?>
                                    <tr><td class="admin-empty" colspan="3">No confirmed bookings today.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($todaysBookings as $booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $booking['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <?= htmlspecialchars((string) $booking['room_name'], ENT_QUOTES, 'UTF-8') ?><br>
                                                <small class="text-muted"><?= htmlspecialchars(date('g:i A', strtotime((string) $booking['start_time'])), ENT_QUOTES, 'UTF-8') ?>–<?= htmlspecialchars(date('g:i A', strtotime((string) $booking['end_time'])), ENT_QUOTES, 'UTF-8') ?></small>
                                            </td>
                                            <td class="admin-table__numeric"><?= (int) $booking['number_of_people'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../../includes/layout/footer.php'; ?>
