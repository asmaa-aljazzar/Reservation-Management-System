<?php
$currentAdminPage = $currentAdminPage ?? basename($_SERVER['SCRIPT_NAME'] ?? '');
$adminLinks = [
    'dashboard.php' => ['Dashboard', 'pages/admin/dashboard.php'],
    'rooms.php' => ['Rooms', 'pages/admin/rooms.php'],
    'slots.php' => ['Slots', 'pages/admin/slots.php'],
    'reservations.php' => ['Reservations', 'pages/admin/reservations.php'],
    'users.php' => ['Users', 'pages/admin/users.php'],
];
?>
<nav class="admin-sidebar card" aria-label="Admin navigation">
    <p class="admin-sidebar__title">Administration</p>
    <?php foreach ($adminLinks as $page => [$label, $path]): ?>
        <a class="admin-sidebar__link<?= $currentAdminPage === $page ? ' is-current' : '' ?>"
           href="<?= htmlspecialchars(app_url($path), ENT_QUOTES, 'UTF-8') ?>"
           <?= $currentAdminPage === $page ? 'aria-current="page"' : '' ?>>
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
        </a>
    <?php endforeach; ?>
    <details class="admin-shortcuts" open>
        <summary class="admin-shortcuts__summary"><svg class="admin-shortcuts__icon" aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="M13.5 2.5 5.5 13h5l-1 8.5 9-12h-5.3l.3-7Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg><span>Quick access</span></summary>
        <div class="admin-shortcuts__links">
            <a class="admin-sidebar__link" href="<?= htmlspecialchars(app_url('pages/admin/rooms.php#add-room-title'), ENT_QUOTES, 'UTF-8') ?>"><span class="admin-shortcuts__plus" aria-hidden="true">+</span>Create room</a>
            <a class="admin-sidebar__link" href="<?= htmlspecialchars(app_url('pages/admin/slots.php#add-slot-title'), ENT_QUOTES, 'UTF-8') ?>"><span class="admin-shortcuts__plus" aria-hidden="true">+</span>Create slot</a>
        </div>
    </details>
</nav>
