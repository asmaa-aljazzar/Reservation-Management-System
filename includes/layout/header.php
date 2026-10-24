<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../paths.php';
$pageTitle = $pageTitle ?? 'Study Nook | Library Room Reservations';
$pageDescription = $pageDescription ?? 'Find and request a library study room that fits your day.';
$userAccountApp = ($_SESSION['user']['role'] ?? null) === 'user'
    && basename($_SERVER['SCRIPT_NAME'] ?? '') !== 'index.php';
$appClass = !empty($adminPage) ? 'admin-app' : ($userAccountApp ? 'user-app' : '');
?>
<!doctype html>
<html lang="en"<?= $appClass !== '' ? ' class="' . $appClass . '"' : '' ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('assets/css/style.css'), ENT_QUOTES, 'UTF-8') ?>?v=<?= (int) filemtime(dirname(__DIR__, 2) . '/assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('assets/css/navbar.css'), ENT_QUOTES, 'UTF-8') ?>?v=<?= (int) filemtime(dirname(__DIR__, 2) . '/assets/css/navbar.css') ?>">
    <?php if (!empty($adminPage)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars(app_url('assets/css/admin.css'), ENT_QUOTES, 'UTF-8') ?>?v=<?= (int) filemtime(dirname(__DIR__, 2) . '/assets/css/admin.css') ?>">
    <?php endif; ?>
    <script src="<?= htmlspecialchars(app_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>?v=<?= (int) filemtime(dirname(__DIR__, 2) . '/assets/js/main.js') ?>" defer></script>
</head>
<body<?= $appClass !== '' ? ' class="' . $appClass . '"' : '' ?>>
<a class="skip-link" href="#main-content">Skip to main content</a>
<?php require __DIR__ . '/flash_messages.php'; ?>


