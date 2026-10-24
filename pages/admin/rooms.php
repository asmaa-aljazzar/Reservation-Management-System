<?php
require_once __DIR__ . '/../../includes/access_control.php';
requireRole('admin');

require_once __DIR__ . '/../../includes/room_functions.php';

$adminPage = true;
$currentAdminPage = 'rooms.php';
$pageTitle = 'Manage Rooms | Study Nook';
$pageDescription = 'Create and manage library study rooms.';
$roomsPerPage = 10;
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$roomsCurrentPage = $requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1;
$roomsTotalPages = max(1, (int) ceil(countAllRooms() / $roomsPerPage));
$roomsCurrentPage = min($roomsCurrentPage, $roomsTotalPages);
$rooms = getAllRooms($roomsCurrentPage, $roomsPerPage);
require __DIR__ . '/../../includes/layout/header.php';
require __DIR__ . '/../../includes/layout/navbar.php';
?>
<main id="main-content" class="admin-page page-section">
    <div class="container admin-layout">
        <?php require __DIR__ . '/../../includes/layout/admin_sidebar.php'; ?>

        <div class="admin-content">
            <header class="admin-page-heading">
                <p class="eyebrow">ADMINISTRATION</p>
                <h1>Rooms</h1>
                <p class="text-muted">Create library rooms and set how many people each room can hold.</p>
            </header>

            <section class="admin-panel card" aria-labelledby="add-room-title">
                <div class="admin-panel__heading">
                    <div>
                        <h2 id="add-room-title">Create a room</h2>
                        <p class="text-muted">Add the room details below. You can schedule slots for it afterward.</p>
                    </div>
                </div>
                <form class="admin-form" action="<?= htmlspecialchars(app_url('actions/rooms/create.php'), ENT_QUOTES, 'UTF-8') ?>" method="post" enctype="multipart/form-data" aria-label="Create a room">
                    <div class="form-field">
                        <label class="form-label" for="room-name">Room name</label>
                        <input class="form-control" id="room-name" name="room_name" type="text" maxlength="120" placeholder="e.g. Quiet Study Room" autocomplete="off" required>
                    </div>
                    <div class="form-field admin-form__capacity">
                        <label class="form-label" for="room-capacity">Room capacity <span class="text-muted">(seats)</span></label>
                        <div class="number-stepper">
                            <button class="number-stepper__button" type="button" data-number-step="-1" aria-label="Decrease room capacity">−</button>
                            <input class="form-control" id="room-capacity" name="room_capacity" type="number" min="1" step="1" placeholder="e.g. 20" aria-describedby="room-capacity-hint" required>
                            <button class="number-stepper__button" type="button" data-number-step="1" aria-label="Increase room capacity">+</button>
                        </div>
                        <small class="form-hint text-muted" id="room-capacity-hint">Maximum number of people the room can hold.</small>
                    </div>
                    <div class="form-field admin-form__wide">
                        <label class="form-label" for="room-description">Description <span class="text-muted">(optional)</span></label>
                        <textarea class="form-control" id="room-description" name="description" rows="3" placeholder="Describe the room and its features."></textarea>
                    </div>
                    <div class="form-field admin-form__wide">
                        <label class="form-label" for="room-image">Room image <span class="text-muted">(optional)</span></label>
                        <input class="form-control" id="room-image" name="room_image" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="room-image-hint">
                        <small class="form-hint text-muted" id="room-image-hint">Choose a JPG, PNG, or WEBP image up to 5 MB.</small>
                    </div>
                    <div class="admin-form__wide admin-form__actions">
                        <button class="button button--primary" type="submit">Create room</button>
                    </div>
                </form>
            </section>

            <section class="admin-panel card" aria-labelledby="rooms-list-title">
                <div class="admin-panel__heading">
                    <div>
                        <h2 id="rooms-list-title">Rooms</h2>
                    </div>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="visually-hidden">Library rooms</caption>
                        <thead>
                            <tr><th scope="col">Room</th><th class="admin-table__numeric" scope="col">Capacity (seats)</th><th scope="col">Description</th><th scope="col">Status</th><th scope="col">Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($rooms === []): ?>
                                <tr><td class="admin-empty" colspan="5"><span class="admin-empty__title">No rooms yet</span><span>Create your first room using the form above.</span></td></tr>
                            <?php else: ?>
                                <?php foreach ($rooms as $room): ?>
                                    <?php $roomIsActive = (bool) ($room['is_active'] ?? true); ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string) ($room['room_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="admin-table__numeric"><?= (int) ($room['room_capacity'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars((string) ($room['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '<span class="text-muted">—</span>' ?></td>
                                        <td><span class="room-status<?= $roomIsActive ? ' is-active' : ' is-inactive' ?>"><?= $roomIsActive ? 'Active' : 'Inactive' ?></span></td>
                                        <td class="room-actions">
                                            <button class="button button--secondary room-status__action" type="button" data-room-edit
                                                data-room-id="<?= (int) ($room['room_id'] ?? 0) ?>"
                                                data-room-name="<?= htmlspecialchars((string) ($room['room_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-room-capacity="<?= (int) ($room['room_capacity'] ?? 0) ?>"
                                                data-room-description="<?= htmlspecialchars((string) ($room['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-room-page="<?= $roomsCurrentPage ?>">Edit</button>
                                            <form action="<?= htmlspecialchars(app_url('actions/rooms/update-status.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                                                <input type="hidden" name="room_id" value="<?= (int) ($room['room_id'] ?? 0) ?>">
                                                <input type="hidden" name="is_active" value="<?= $roomIsActive ? '0' : '1' ?>">
                                                <input type="hidden" name="page" value="<?= $roomsCurrentPage ?>">
                                                <button class="button button--secondary room-status__action" type="submit"><?= $roomIsActive ? 'Deactivate' : 'Activate' ?></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($roomsTotalPages > 1): ?>
                    <?php $pageWindow = range(max(1, $roomsCurrentPage - 2), min($roomsTotalPages, $roomsCurrentPage + 2)); ?>
                    <nav class="room-pagination" aria-label="Rooms pages">
                        <span class="room-pagination__status">Page <?= $roomsCurrentPage ?> of <?= $roomsTotalPages ?></span>
                        <div class="room-pagination__controls">
                            <?php if ($roomsCurrentPage > 1): ?>
                                <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/admin/rooms.php?page=' . ($roomsCurrentPage - 1)), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                            <?php endif; ?>
                            <?php foreach ($pageWindow as $pageNumber): ?>
                                <a class="room-pagination__link<?= $pageNumber === $roomsCurrentPage ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/admin/rooms.php?page=' . $pageNumber), ENT_QUOTES, 'UTF-8') ?>"<?= $pageNumber === $roomsCurrentPage ? ' aria-current="page"' : '' ?>><?= $pageNumber ?></a>
                            <?php endforeach; ?>
                            <?php if ($roomsCurrentPage < $roomsTotalPages): ?>
                                <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/admin/rooms.php?page=' . ($roomsCurrentPage + 1)), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>
<dialog class="room-edit-dialog" id="room-edit-dialog" aria-labelledby="room-edit-title">
    <div class="room-edit-dialog__panel">
        <div class="admin-panel__heading">
            <div>
                <h2 id="room-edit-title">Edit room</h2>
                <p class="text-muted">Update the room details. Capacity must cover all active slots.</p>
            </div>
            <button class="room-edit-dialog__close" type="button" data-room-edit-close aria-label="Close edit room dialog">×</button>
        </div>
        <form class="admin-form" action="<?= htmlspecialchars(app_url('actions/rooms/update.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
            <input type="hidden" name="room_id" id="edit-room-id">
            <input type="hidden" name="page" id="edit-room-page" value="1">
            <div class="form-field">
                <label class="form-label" for="edit-room-name">Room name</label>
                <input class="form-control" id="edit-room-name" name="room_name" type="text" maxlength="120" required>
            </div>
            <div class="form-field">
                <label class="form-label" for="edit-room-capacity">Room capacity <span class="text-muted">(seats)</span></label>
                <div class="number-stepper">
                    <button class="number-stepper__button" type="button" data-number-step="-1" aria-label="Decrease room capacity">−</button>
                    <input class="form-control" id="edit-room-capacity" name="room_capacity" type="number" min="1" step="1" required>
                    <button class="number-stepper__button" type="button" data-number-step="1" aria-label="Increase room capacity">+</button>
                </div>
                <small class="form-hint text-muted">Must be at least the largest active slot capacity.</small>
            </div>
            <div class="form-field admin-form__wide">
                <label class="form-label" for="edit-room-description">Description <span class="text-muted">(optional)</span></label>
                <textarea class="form-control" id="edit-room-description" name="description" rows="3"></textarea>
            </div>
            <div class="admin-form__wide admin-form__actions">
                <button class="button button--secondary" type="button" data-room-edit-close>Cancel</button>
                <button class="button button--primary" type="submit">Save changes</button>
            </div>
        </form>
    </div>
</dialog>
<?php require __DIR__ . '/../../includes/layout/footer.php'; ?>
