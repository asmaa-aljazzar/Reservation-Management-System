<?php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();

require_once __DIR__ . "/../../includes/paths.php";
require_once __DIR__ . "/../../includes/access_control.php";
require_once __DIR__ . "/../../includes/room_functions.php";
require_once __DIR__ . "/../../includes/validation.php";

requireRole("admin");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . app_url("pages/admin/rooms.php"));
    exit();
}

$roomName = trim($_POST['room_name'] ?? "");
$rawCapacity = trim($_POST['room_capacity'] ?? '');
$roomCapacity = filter_var($rawCapacity, FILTER_VALIDATE_INT);
$description = trim($_POST['description'] ?? "");

$error = "";

if ($roomCapacity === false || !isValidRoomData($roomName, $roomCapacity, $description)) {
    $error = 'Please enter valid room details.';
} else {
    try {

        if (!createRoom($roomName, $roomCapacity, $description)) {
            $error = "Error While Creating Room";
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $error = "A room with that name already exists.";
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
        "message" => "Room created successfully"
    ];
}

header("Location: " . app_url("pages/admin/rooms.php"));
exit();
