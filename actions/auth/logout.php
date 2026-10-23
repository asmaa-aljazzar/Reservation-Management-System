<?php
require_once __DIR__ . "/../../includes/paths.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('index.php'));
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();

// $_SESSION = [];
session_unset();
session_destroy();

header('Location: ' . app_url('index.php'));
exit;
