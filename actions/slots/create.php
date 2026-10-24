<?php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();

require_once __DIR__ . "/../../includes/paths.php";
require_once __DIR__ . "/../../includes/access_control.php";
require_once __DIR__ . "/../../includes/slot_functions.php";
require_once __DIR__ . "/../../includes/validation.php";

requireRole("admin");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . app_url("pages/admin/slots.php"));
    exit();
}

$roomId = filter_var(trim($_POST['room_id'] ?? ''), FILTER_VALIDATE_INT);
$slotDate = trim($_POST['slot_date'] ?? '');
$startTime = trim($_POST['start_time'] ?? '');
$endTime = trim($_POST['end_time'] ?? '');
$slotCapacity = filter_var(trim($_POST['slot_capacity'] ?? ''), FILTER_VALIDATE_INT);

$error = "";

if (
    $roomId === false ||
    $slotCapacity === false ||
    !isValidSlotData((int) $roomId, $slotDate, $startTime, $endTime, (int) $slotCapacity)
) {
    $error = 'Please enter valid slot details.';
} else {
    try {
        if (!createSlot((int) $roomId, $slotDate, $startTime, $endTime, (int) $slotCapacity)) {
            $error = 'Error while creating slot.';
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $error = 'A slot already exists at that time for this room.';
        } else {
            throw $e;
        }
    }
}

if ($error !== "") {

    $_SESSION["flash"] = [
        "type" => "error",
        "message" => $error
    ];
} else {

    $_SESSION["flash"] = [
        "type" => "success",
        "message" => "Slot created successfully."
    ];
}

header("Location: " . app_url("pages/admin/slots.php"));
exit();
