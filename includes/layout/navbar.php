<?php
require_once __DIR__ . '/../paths.php';
$currentPage = $currentPage ?? basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$userRole = $_SESSION['user']['role'] ?? null;
$isLoggedIn = isset($_SESSION['user']['user_id']);
?>
<header class="site-header">
    <nav class="site-nav container" aria-label="Main navigation">
        <a class="site-brand" href="<?= htmlspecialchars(app_url('index.php'), ENT_QUOTES, 'UTF-8') ?>">
            <span class="site-brand__mark" aria-hidden="true">S</span>
            <span>Study <strong>Nook</strong></span>
        </a>
        <button class="site-nav__toggle" type="button" aria-expanded="false" aria-controls="primary-menu" data-nav-toggle>
            <span class="visually-hidden">Toggle navigation</span>
            <span class="site-nav__toggle-line" aria-hidden="true"></span><span class="site-nav__toggle-line" aria-hidden="true"></span><span class="site-nav__toggle-line" aria-hidden="true"></span>
        </button>
        <div class="site-nav__menu" id="primary-menu" data-nav-menu>
            <div class="site-nav__links">
                <a class="site-nav__link<?= $currentPage === 'index.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('index.php'), ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'index.php' ? ' aria-current="page"' : '' ?>>Home</a>
                <?php if ($userRole === 'admin'): ?>
                    <?php
                    $adminNavLinks = [
                        ['rooms.php', 'Rooms', 'pages/admin/rooms.php'],
                        ['slots.php', 'Slots', 'pages/admin/slots.php'],
                        ['reservations.php', 'Reservations', 'pages/admin/reservations.php'],
                        ['users.php', 'Users', 'pages/admin/users.php'],
                    ];
                    $isAdminSection = in_array($currentPage, array_column($adminNavLinks, 0), true);
                    ?>
                    <a class="site-nav__link<?= $currentPage === 'dashboard.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/admin/dashboard.php'), ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'dashboard.php' ? ' aria-current="page"' : '' ?>>Admin dashboard</a>
                    <details class="site-nav__dropdown">
                        <summary class="site-nav__link site-nav__dropdown-summary<?= $isAdminSection ? ' is-current' : '' ?>"<?= $isAdminSection ? ' aria-current="page"' : '' ?>>Manage</summary>
                        <div class="site-nav__dropdown-menu">
                            <?php foreach ($adminNavLinks as [$page, $label, $path]): ?>
                                <a class="site-nav__dropdown-link<?= $currentPage === $page ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url($path), ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === $page ? ' aria-current="page"' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php else: ?>
                    <a class="site-nav__link<?= $currentPage === 'rooms.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/rooms.php'), ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'rooms.php' ? ' aria-current="page"' : '' ?>>Rooms</a>
                    <a class="site-nav__link<?= $currentPage === 'all-slots.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/all-slots.php'), ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'all-slots.php' ? ' aria-current="page"' : '' ?>>All slots</a>
                    <?php if ($userRole === 'user'): ?>
                        <a class="site-nav__link<?= $currentPage === 'my-reservations.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/my-reservations.php'), ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'my-reservations.php' ? ' aria-current="page"' : '' ?>>My reservations</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="site-nav__actions">
                <button class="theme-toggle" type="button" data-theme-toggle aria-pressed="false"><span aria-hidden="true" data-theme-icon>◐</span><span data-theme-label>Dark mode</span></button>
                <?php if ($isLoggedIn): ?>
                    <form class="site-nav__logout-form" action="<?= htmlspecialchars(app_url('actions/auth/logout.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                        <button class="site-nav__logout" type="submit">Log out</button>
                    </form>
                <?php else: ?>
                    <a class="site-nav__login" href="<?= htmlspecialchars(app_url('login.php'), ENT_QUOTES, 'UTF-8') ?>">Log in</a>
                    <a class="button button--primary site-nav__signup" href="<?= htmlspecialchars(app_url('signup.php'), ENT_QUOTES, 'UTF-8') ?>">Create account</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

