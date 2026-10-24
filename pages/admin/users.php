<?php
require_once __DIR__ . '/../../includes/access_control.php';
require_once __DIR__ . '/../../includes/user_functions.php';
requireRole('admin');

$adminPage = true;
$currentAdminPage = 'users.php';
$pageTitle = 'Manage Users | Study Nook';
$pageDescription = 'Manage user accounts for Study Nook.';
$usersPerPage = 10;
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$usersCurrentPage = $requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1;
$usersTotalPages = max(1, (int) ceil(countAllUsers() / $usersPerPage));
$usersCurrentPage = min($usersCurrentPage, $usersTotalPages);
$users = getAllUsers($usersCurrentPage, $usersPerPage);

require __DIR__ . '/../../includes/layout/header.php';
require __DIR__ . '/../../includes/layout/navbar.php';
?>
<main id="main-content" class="admin-page page-section">
    <div class="container admin-layout">
        <?php require __DIR__ . '/../../includes/layout/admin_sidebar.php'; ?>

        <div class="admin-content">
            <header class="admin-page-heading">
                <p class="eyebrow">ADMINISTRATION</p>
                <h1>Users</h1>
                <p class="text-muted">View user accounts, roles, and account status.</p>
            </header>

            <section class="admin-panel card" aria-labelledby="users-list-title">
                <div class="admin-panel__heading">
                    <div>
                        <h2 id="users-list-title">All users</h2>
                    </div>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="visually-hidden">User accounts</caption>
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Status</th>
                                <th scope="col">Joined</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users === []): ?>
                                <tr><td class="admin-empty" colspan="6"><span class="admin-empty__title">No users found</span></td></tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <?php $userIsActive = (bool) ($user['is_active'] ?? false); ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string) $user['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars(ucfirst((string) $user['role']), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><span class="room-status<?= $userIsActive ? ' is-active' : ' is-inactive' ?>"><?= $userIsActive ? 'Active' : 'Inactive' ?></span></td>
                                        <td><?= htmlspecialchars(date('M j, Y', strtotime((string) $user['created_at'])), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <form method="post" action="<?= htmlspecialchars(app_url('actions/users/update-status.php'), ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                                                <input type="hidden" name="is_active" value="<?= $userIsActive ? '0' : '1' ?>">
                                                <input type="hidden" name="page" value="<?= $usersCurrentPage ?>">
                                                <button class="button button--secondary room-status__action" type="submit"<?= (int) ($_SESSION['user']['user_id'] ?? 0) === (int) $user['user_id'] && $userIsActive ? ' disabled' : '' ?>><?= $userIsActive ? 'Deactivate' : 'Activate' ?></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($usersTotalPages > 1): ?>
                    <?php $pageWindow = range(max(1, $usersCurrentPage - 2), min($usersTotalPages, $usersCurrentPage + 2)); ?>
                    <nav class="room-pagination" aria-label="Users pages">
                        <span class="room-pagination__status">Page <?= $usersCurrentPage ?> of <?= $usersTotalPages ?></span>
                        <div class="room-pagination__controls">
                            <?php if ($usersCurrentPage > 1): ?>
                                <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/admin/users.php?page=' . ($usersCurrentPage - 1)), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                            <?php endif; ?>
                            <?php foreach ($pageWindow as $pageNumber): ?>
                                <a class="room-pagination__link<?= $pageNumber === $usersCurrentPage ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/admin/users.php?page=' . $pageNumber), ENT_QUOTES, 'UTF-8') ?>"<?= $pageNumber === $usersCurrentPage ? ' aria-current="page"' : '' ?>><?= $pageNumber ?></a>
                            <?php endforeach; ?>
                            <?php if ($usersCurrentPage < $usersTotalPages): ?>
                                <a class="room-pagination__link" href="<?= htmlspecialchars(app_url('pages/admin/users.php?page=' . ($usersCurrentPage + 1)), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../../includes/layout/footer.php'; ?>
