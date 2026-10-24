<?php

function isValidFullName(string $fullName): bool
{
    $fullName = trim($fullName);

    // "mb" means multibyte; mb_strlen counts UTF-8 characters, not bytes.
    return ($fullName !== '' && mb_strlen($fullName, 'UTF-8') <= 120);
}

function isValidEmail(string $email): bool
{
    $email = trim($email);

    return $email !== ''
        && mb_strlen($email, 'UTF-8') <= 190
        && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function passwordValidationErrors(string $password): array
{

    $errors = [];

    // $errors[] means append new element.

    if ($password === '')
        $errors[] = "Password cannot be empty";

    if (mb_strlen($password, 'UTF-8') < 8)
        $errors[] = "Password must have at least 8 characters";

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain an uppercase letter.';
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain a lowercase letter.';
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain a number.';
    }

    if (!preg_match('/[^a-zA-Z0-9\s]/u', $password)) {
        $errors[] = 'Password must contain a special character.';
    }

    return $errors;
}

function isValidRoomData(string $roomName, int $roomCapacity, ?string $description): bool
{
    $roomName = trim($roomName);

    if ($roomName === '' || mb_strlen($roomName, 'UTF-8') > 120) {
        return false;
    }

    if ($roomCapacity < 1) {
        return false;
    }

    // The MySQL TEXT type can hold up to 65,535 bytes.
    if ($description !== null && strlen($description) > 65535) {
        return false;
    }

    return true;
}

require_once __DIR__ . "/room_functions.php";

function isValidSlotData(
    int $roomId,
    string $slotDate,
    string $startTime,
    string $endTime,
    int $slotCapacity,
    ?int $excludeSlotId = null
): bool {
    if (!isRoomActive($roomId)) {
        return false;
    }

    $start = strtotime("$slotDate $startTime");
    $end = strtotime("$slotDate $endTime");

    if ($start === false || $end === false || $start <= time()) {
        return false;
    }

    // check if the end is less than start and if the time at least 30 minutes
    if ($end <= $start || $end - $start < 30 * 60) {
        return false;
    }

    if ($slotCapacity < 1 || $slotCapacity > roomCapacity($roomId)) {
        return false;
    }

    $stmt = prepare(
        "SELECT slot_id, start_time, end_time
         FROM slots
         WHERE room_id = :roomId AND slot_date = :slotDate"
    );
    $stmt->execute([
        ":roomId" => $roomId,
        ":slotDate" => $slotDate,
    ]);

    // There is a slot that is not the current one
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $existingSlot) {
        if (
            $excludeSlotId !== null &&
            (int) $existingSlot["slot_id"] === $excludeSlotId
        ) {
            continue;
        }

        $existingStart = strtotime("$slotDate " . $existingSlot["start_time"]);
        $existingEnd = strtotime("$slotDate " . $existingSlot["end_time"]);

        // Reject overlap or a gap shorter than 30 minutes.
        if (($start < $existingEnd + 30 * 60) && ($end + 30 * 60 > $existingStart)) {
            return false;
        }
    }

    return true;
}
