<?php
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/room_functions.php';
require_once __DIR__ . '/../../includes/slot_functions.php';
requireRole('admin');

$adminPage = true;
$currentAdminPage = 'slots.php';
$pageTitle = 'Manage Slots | Study Nook';
$pageDescription = 'Create and manage room reservation slots.';
$slotsPerPage = 10;
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$slotsCurrentPage = $requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1;
$slotsTotalPages = max(1, (int) ceil(countAllSlots() / $slotsPerPage));
$slotsCurrentPage = min($slotsCurrentPage, $slotsTotalPages);
$slots = getAllSlots($slotsCurrentPage, $slotsPerPage);
$activeRooms = getActiveRooms();
require __DIR__ . '/../../includes/layout/header.php';
require __DIR__ . '/../../includes/layout/navbar.php';
?>
<main id="main-content" class="admin-page page-section">
    <div class="container admin-layout">
        <?php require __DIR__ . '/../../includes/layout/admin_sidebar.php'; ?>

        <div class="admin-content">
            <header class="admin-page-heading">
                <p class="eyebrow">ADMINISTRATION</p>
                <h1>Manage slots</h1>
                <p class="text-muted">Schedule study times for each room. Leave at least 30 minutes between slots in the same room.</p>
            </header>

            <section class="admin-panel card" aria-labelledby="add-slot-title">
                <div class="admin-panel__heading">
                    <div>
                        <h2 id="add-slot-title">Add a slot</h2>
                        <p class="text-muted">Slot capacity cannot be greater than the room’s capacity.</p>
                    </div>
                </div>
                <form class="admin-form" action="<?= htmlspecialchars(app_url('actions/slots/create.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                    <div class="form-field">
                        <label class="form-label" for="slot-room">Room</label>
                        <select class="form-control" id="slot-room" name="room_id" required>
                            <?php if ($activeRooms === []): ?>
                                <option value="" selected disabled>No active rooms available</option>
                            <?php else: ?>
                                <option value="" selected disabled>Select a room</option>
                                <?php foreach ($activeRooms as $room): ?>
                                    <option value="<?= (int) $room['room_id'] ?>" data-capacity="<?= (int) $room['room_capacity'] ?>"><?= htmlspecialchars((string) $room['room_name'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="slot-date">Date</label>
                        <!-- Prevent admins from scheduling slots in the past. -->
                        <input class="form-control" id="slot-date" name="slot_date" type="date" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="slot-start">Start time</label>
                        <input class="form-control" id="slot-start" name="start_time" type="time" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="slot-end">End time</label>
                        <input class="form-control" id="slot-end" name="end_time" type="time" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="slot-capacity">Slot capacity</label>
                        <div class="number-stepper">
                            <button class="number-stepper__button" type="button" data-number-step="-1" aria-label="Decrease slot capacity">−</button>
                            <input class="form-control" id="slot-capacity" name="slot_capacity" type="number" min="1" step="1" aria-describedby="slot-capacity-hint" required>
                            <button class="number-stepper__button" type="button" data-number-step="1" aria-label="Increase slot capacity">+</button>
                        </div>
                        <small class="form-hint text-muted" id="slot-capacity-hint">Select a room to see its maximum capacity.</small>
                    </div>
                    <div class="admin-form__wide admin-form__actions">
                        <button class="button button--primary" type="submit">Add slot</button>
                    </div>
                </form>
            </section>

            <section class="admin-panel card" aria-labelledby="slots-list-title">
                <div class="admin-panel__heading">
                    <div>
                        <h2 id="slots-list-title">Scheduled slots</h2>
                        <p class="text-muted">Cancel slots to make them unavailable while keeping their booking history.</p>
                    </div>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="visually-hidden">Scheduled room slots</caption>
                        <thead>
                            <tr><th scope="col">Room</th><th scope="col">Date</th><th scope="col">Time</th><th class="admin-table__numeric" scope="col">Capacity (seats)</th><th scope="col">Status</th><th scope="col">Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($slots === []): ?>
                                <tr><td class="admin-empty" colspan="6"><span class="admin-empty__title">No slots yet</span><span>Create a slot using the form above.</span></td></tr>
                            <?php else: ?>
                                <?php foreach ($slots as $slot): ?>
                                    <?php
                                    $slotEnd = strtotime(($slot['slot_date'] ?? '') . ' ' . ($slot['end_time'] ?? ''));
                                    $slotHasPassed = $slotEnd !== false && $slotEnd <= time();
                                    $slotIsActive = (bool) ($slot['is_active'] ?? true) && !$slotHasPassed;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string) ($slot['room_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($slot['slot_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($slot['start_time'] ?? ''), ENT_QUOTES, 'UTF-8') ?>–<?= htmlspecialchars((string) ($slot['end_time'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="admin-table__numeric"><?= (int) ($slot['slot_capacity'] ?? 0) ?></td>
                                        <td><span class="room-status<?= $slotIsActive ? ' is-active' : ' is-inactive' ?>"><?= $slotIsActive ? 'Active' : 'Inactive' ?></span></td>
                                        <td class="room-actions">
                                            <?php if ($slotHasPassed): ?>
                                                <span class="text-muted">—</span>
                                            <?php else: ?>
                                                <button class="button button--secondary room-status__action" type="button"
                                                    data-slot-edit
                                                    data-slot-id="<?= (int) ($slot['slot_id'] ?? 0) ?>"
                                                    data-slot-page="<?= $slotsCurrentPage ?>"
                                                    data-slot-date="<?= htmlspecialchars((string) ($slot['slot_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                    data-slot-start="<?= htmlspecialchars(substr((string) ($slot['start_time'] ?? ''), 0, 5), ENT_QUOTES, 'UTF-8') ?>"
                                                    data-slot-end="<?= htmlspecialchars(substr((string) ($slot['end_time'] ?? ''), 0, 5), ENT_QUOTES, 'UTF-8') ?>"
                                                    data-slot-capacity="<?= (int) ($slot['slot_capacity'] ?? 0) ?>">Edit</button>
                                                <?php if ($slotIsActive): ?>
                                                <form action="<?= htmlspecialchars(app_url('actions/slots/update-status.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                                                    <input type="hidden" name="slot_id" value="<?= (int) ($slot['slot_id'] ?? 0) ?>">
                                                    <input type="hidden" name="is_active" value="0">
                                                    <input type="hidden" name="page" value="<?= $slotsCurrentPage ?>">
                                                    <button class="button button--secondary room-status__action" type="submit">Deactivate</button>
                                                </form>
                                                <?php else: ?>
                                                <form action="<?= htmlspecialchars(app_url('actions/slots/update-status.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                                                    <input type="hidden" name="slot_id" value="<?= (int) ($slot['slot_id'] ?? 0) ?>">
                                                    <input type="hidden" name="is_active" value="1">
                                                    <input type="hidden" name="page" value="<?= $slotsCurrentPage ?>">
                                                    <button class="button button--secondary room-status__action" type="submit">Activate</button>
                                                </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($slotsTotalPages > 1): ?>
                    <?php $pageWindow = range(max(1, $slotsCurrentPage - 2), min($slotsTotalPages, $slotsCurrentPage + 2)); ?>
                    <nav class="room-pagination" aria-label="Slots pages">
                        <span class="room-pagination__status">Page <?= $slotsCurrentPage ?> of <?= $slotsTotalPages ?></span>
                        <div class="room-pagination__controls">
                            <?php if ($slotsCurrentPage > 1): ?>
                                <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/admin/slots.php?page=' . ($slotsCurrentPage - 1)), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                            <?php endif; ?>
                            <?php foreach ($pageWindow as $pageNumber): ?>
                                <a class="room-pagination__link<?= $pageNumber === $slotsCurrentPage ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/admin/slots.php?page=' . $pageNumber), ENT_QUOTES, 'UTF-8') ?>"<?= $pageNumber === $slotsCurrentPage ? ' aria-current="page"' : '' ?>><?= $pageNumber ?></a>
                            <?php endforeach; ?>
                            <?php if ($slotsCurrentPage < $slotsTotalPages): ?>
                                <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/admin/slots.php?page=' . ($slotsCurrentPage + 1)), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>
<dialog class="room-edit-dialog" id="slot-edit-dialog" aria-labelledby="slot-edit-title">
    <div class="room-edit-dialog__panel">
        <div class="admin-panel__heading">
            <div>
                <h2 id="slot-edit-title">Edit slot</h2>
                <p class="text-muted">Update the slot date, time, or capacity.</p>
            </div>
            <button class="room-edit-dialog__close" type="button" data-slot-edit-close aria-label="Close edit slot dialog">×</button>
        </div>
        <form class="admin-form" action="<?= htmlspecialchars(app_url('actions/slots/update.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
            <input type="hidden" name="slot_id" id="edit-slot-id">
            <input type="hidden" name="page" id="edit-slot-page" value="1">
            <div class="form-field">
                <label class="form-label" for="edit-slot-date">Date</label>
                <input class="form-control" id="edit-slot-date" name="slot_date" type="date" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-field">
                <label class="form-label" for="edit-slot-capacity">Slot capacity</label>
                <div class="number-stepper">
                    <button class="number-stepper__button" type="button" data-number-step="-1" aria-label="Decrease slot capacity">−</button>
                    <input class="form-control" id="edit-slot-capacity" name="slot_capacity" type="number" min="1" step="1" required>
                    <button class="number-stepper__button" type="button" data-number-step="1" aria-label="Increase slot capacity">+</button>
                </div>
            </div>
            <div class="form-field">
                <label class="form-label" for="edit-slot-start">Start time</label>
                <input class="form-control" id="edit-slot-start" name="start_time" type="time" required>
            </div>
            <div class="form-field">
                <label class="form-label" for="edit-slot-end">End time</label>
                <input class="form-control" id="edit-slot-end" name="end_time" type="time" required>
            </div>
            <div class="admin-form__wide admin-form__actions">
                <button class="button button--secondary" type="button" data-slot-edit-close>Cancel</button>
                <button class="button button--primary" type="submit">Save changes</button>
            </div>
        </form>
    </div>
</dialog>
<?php require __DIR__ . '/../../includes/layout/footer.php'; ?>
