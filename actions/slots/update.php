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

$slotId = filter_var($_POST['slot_id'] ?? '', FILTER_VALIDATE_INT);
$slotDate = trim($_POST['slot_date'] ?? '');
$startTime = trim($_POST['start_time'] ?? '');
$endTime = trim($_POST['end_time'] ?? '');
$slotCapacity = filter_var(trim($_POST['slot_capacity'] ?? ''), FILTER_VALIDATE_INT);
$page = max(1, (int) ($_POST['page'] ?? 1));
$error = "";

if ($slotId === false || $slotCapacity === false) {
    $error = "Please enter valid slot details.";
} else {
    $stmt = prepare("SELECT room_id FROM slots WHERE slot_id = :slotId");
    $stmt->execute([":slotId" => $slotId]);
    $roomId = $stmt->fetchColumn();

    if ($roomId === false) {
        $error = "Slot does not exist.";
    } elseif (!isValidSlotData((int) $roomId, $slotDate, $startTime, $endTime, (int) $slotCapacity, (int) $slotId)) {
        $error = "Please enter valid slot details.";
    } else {
        try {
            $result = updateSlot((int) $slotId, $slotDate, $startTime, $endTime, (int) $slotCapacity);
            if (is_array($result)) {
                $error = implode(' ', $result);
            } elseif ($result !== true) {
                $error = "Error while updating slot.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = "Another slot is already scheduled at that time for this room.";
            } else {
                throw $e;
            }
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
        "message" => "Slot updated successfully."
    ];
}

header("Location: " . app_url("pages/admin/slots.php?page=" . $page));
exit();
