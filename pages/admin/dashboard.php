<?php
require_once __DIR__ . '/../../includes/access_control.php';
requireRole('admin');

$adminPage = true;
$currentAdminPage = 'dashboard.php';
$pageTitle = 'Admin Dashboard | Study Nook';
$pageDescription = 'Manage the Study Nook reservation system.';
require __DIR__ . '/../../includes/layout/header.php';
require __DIR__ . '/../../includes/layout/navbar.php';
?>
<main id="main-content" class="admin-page page-section">
    <div class="container admin-layout">
        <?php require __DIR__ . '/../../includes/layout/admin_sidebar.php'; ?>
        <section class="admin-content admin-placeholder">
            <p class="eyebrow">ADMINISTRATION</p>
            <h1>Admin Dashboard</h1>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../../includes/layout/footer.php'; ?>
