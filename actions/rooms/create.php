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
$roomImage = $_FILES['room_image'] ?? null;

$error = "";
$imageUrl = null;
$savedImagePath = null;

if ($roomCapacity === false || !isValidRoomData($roomName, $roomCapacity, $description)) {
    $error = 'Please enter valid room details.';
} elseif (!isValidRoomImage($roomImage)) {
    $error = 'Choose a JPG, PNG, or WEBP image up to 5 MB.';
} else {
    try {
        if ($roomImage !== null && $roomImage['error'] !== UPLOAD_ERR_NO_FILE) {
            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($roomImage['tmp_name']);
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            ];
            $fileName = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
            $uploadDirectory = __DIR__ . '/../../assets/images/rooms';
            $savedImagePath = $uploadDirectory . '/' . $fileName;

            if (!move_uploaded_file($roomImage['tmp_name'], $savedImagePath)) {
                $error = 'The room image could not be uploaded.';
            } else {
                $imageUrl = 'assets/images/rooms/' . $fileName;
            }
        }

        if ($error === '' && !createRoom($roomName, $roomCapacity, $description, $imageUrl)) {
            $error = 'Error While Creating Room';
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $error = "A room with that name already exists.";
        } else {
            if ($savedImagePath !== null && is_file($savedImagePath)) {
                unlink($savedImagePath);
            }
            throw $e;
        }
    }
}

if ($error !== '' && $savedImagePath !== null && is_file($savedImagePath)) {
    unlink($savedImagePath);
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
