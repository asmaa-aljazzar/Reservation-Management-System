<?php
require_once __DIR__ . '/../paths.php';
$currentPage = $currentPage ?? basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
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
                <a class="site-nav__link<?= $currentPage === 'rooms.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/rooms.php'), ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'rooms.php' ? ' aria-current="page"' : '' ?>>Rooms</a>
                <a class="site-nav__link<?= $currentPage === 'all-slots.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(app_url('pages/all-slots.php'), ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'all-slots.php' ? ' aria-current="page"' : '' ?>>All slots</a>
            </div>
            <div class="site-nav__actions">
                <button class="theme-toggle" type="button" data-theme-toggle aria-pressed="false"><span aria-hidden="true" data-theme-icon>◐</span><span data-theme-label>Dark mode</span></button>
                <a class="site-nav__login" href="<?= htmlspecialchars(app_url('login.php'), ENT_QUOTES, 'UTF-8') ?>">Log in</a>
                <a class="button button--primary site-nav__signup" href="<?= htmlspecialchars(app_url('signup.php'), ENT_QUOTES, 'UTF-8') ?>">Create account</a>
            </div>
        </div>
    </nav>
</header>

