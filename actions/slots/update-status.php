<?php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();

require_once __DIR__ . "/../../includes/paths.php";
require_once __DIR__ . "/../../includes/access_control.php";
require_once __DIR__ . "/../../includes/slot_functions.php";

requireRole("admin");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . app_url("pages/admin/slots.php"));
    exit();
}

$slotId = filter_var($_POST['slot_id'] ?? '', FILTER_VALIDATE_INT);
$isActive = $_POST['is_active'] ?? '';
$page = max(1, (int) ($_POST['page'] ?? 1));
$error = "";

if ($slotId === false || !in_array($isActive, ['0', '1'], true)) {
    $error = "Invalid slot details.";
} elseif (!setSlotStatus((int) $slotId, $isActive === '1')) {
    $error = "Slot does not exist.";
}

if ($error !== "") {
    $_SESSION["flash"] = [
        "type" => "error",
        "message" => $error
    ];
} else {
    $_SESSION["flash"] = [
        "type" => "success",
        "message" => $isActive === '1' ? "Slot activated successfully." : "Slot deactivated successfully."
    ];
}

header("Location: " . app_url("pages/admin/slots.php?page=" . $page));
exit();
