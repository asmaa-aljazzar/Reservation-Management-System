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
