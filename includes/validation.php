<?php

function isValidFullName(string $fullName) : bool
{
    $fullName = trim($fullName);

    // "mb" means multibyte; mb_strlen counts UTF-8 characters, not bytes.
    return ($fullName !== '' && mb_strlen($fullName, 'UTF-8') <= 120);
}

function isValidEmail(string $email) : bool
{
    $email = trim($email);

    return $email !== ''
        && mb_strlen($email, 'UTF-8') <= 190
        && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function passwordValidationErrors(string $password) : array
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

// function isValidRoomStatus(string $status): bool
// {
//     // TODO: Accept only active or inactive.
// }

// function isValidSlotStatus(string $status): bool
// {
//     // TODO: Match the schema's active or cancelled slot statuses.
// }

// function isValidReservationStatus(string $status): bool
// {
//     // TODO: Accept only the reservation statuses an admin may set.
// }
