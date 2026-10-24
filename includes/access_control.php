<?php

require_once __DIR__ . "/paths.php";

function checkRole(string $requiredRole): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    return isset($_SESSION['user']['role'])
        && $_SESSION['user']['role'] === $requiredRole;
}

function requireRole(string $requiredRole): void
{
    if (checkRole($requiredRole))
        return;

    $role = $_SESSION['user']['role'] ?? null;

    if ($role === 'admin') {
        $destination = 'pages/admin/dashboard.php';
    } elseif ($role === 'user') {
        $destination = 'pages/rooms.php';
    } else {
        $destination = 'login.php';
    }

    header('Location: ' . app_url($destination));
    exit;
}