<?php
require_once __DIR__ . '/../../includes/access_control.php';
requireRole('admin');

$adminPage = true;
$currentAdminPage = 'slots.php';
$pageTitle = 'Manage Slots | Study Nook';
$pageDescription = 'Create and manage room reservation slots.';
$slots = $slots ?? [];
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
                            <option value="" selected disabled>Select a room</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="slot-date">Date</label>
                        <input class="form-control" id="slot-date" name="slot_date" type="date" required>
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
                            <input class="form-control" id="slot-capacity" name="slot_capacity" type="number" min="1" step="1" required>
                            <button class="number-stepper__button" type="button" data-number-step="1" aria-label="Increase slot capacity">+</button>
                        </div>
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
                            <tr><th scope="col">Room</th><th scope="col">Date</th><th scope="col">Time</th><th scope="col">Capacity</th><th scope="col">Status</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr>
                        </thead>
                        <tbody>
                            <?php if ($slots === []): ?>
                                <tr><td class="admin-empty" colspan="6"><span class="admin-empty__title">No slots yet</span><span>Create a slot using the form above.</span></td></tr>
                            <?php else: ?>
                                <?php foreach ($slots as $slot): ?>
                                    <?php $slotIsActive = ($slot['status'] ?? 'active') === 'active'; ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string) ($slot['room_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($slot['slot_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($slot['start_time'] ?? ''), ENT_QUOTES, 'UTF-8') ?>–<?= htmlspecialchars((string) ($slot['end_time'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= (int) ($slot['slot_capacity'] ?? 0) ?> seats</td>
                                        <td><span class="room-status<?= $slotIsActive ? ' is-active' : ' is-inactive' ?>"><?= $slotIsActive ? 'Active' : 'Cancelled' ?></span></td>
                                        <td>
                                            <?php if ($slotIsActive): ?>
                                                <form action="<?= htmlspecialchars(app_url('actions/slots/update-status.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                                                    <input type="hidden" name="slot_id" value="<?= (int) ($slot['slot_id'] ?? 0) ?>">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button class="button button--secondary room-status__action" type="submit">Cancel slot</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../../includes/layout/footer.php'; ?>
