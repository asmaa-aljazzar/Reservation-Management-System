<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/access_control.php';

function redirectLoggedInUser(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['user']['user_id'])) {
        return;
    }

    if (checkRole('admin')) {
        $destination = 'pages/admin/dashboard.php';
    } elseif (checkRole('user')) {
        $destination = 'pages/rooms.php';
    } else {
        return;
    }

    header('Location: ' . app_url($destination));
    exit;
}

function emailAlreadyRegistered(string $email): bool
{
    $stmt = prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);

    $exists = $stmt->fetch();
    $stmt->closeCursor();

    return $exists;
}

function signUp(string $fullName, string $email, string $password, string $confirmPassword): mixed
{
    $errors = passwordValidationErrors($password);

    if (!isValidFullName($fullName))
        $errors[] = "Your full name field has an invalid value";

    if (!isValidEmail($email))
        $errors[] = "Invalid Email Address";
    else if (emailAlreadyRegistered($email))
        $errors[] = "This email address is already registered";

    if ($confirmPassword !== $password)
        $errors[] = "Password doesn't match";

    if ($errors !== [])
        return $errors;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = prepare("INSERT INTO users (full_name, email, password) VALUES
    (:fullName, :email, :password)");

    try {
        $stmt->execute([
            ":fullName" => $fullName,
            ":email" => $email,
            ":password" => $hashedPassword,
        ]);
    } catch (PDOException $e) {
        $stmt->closeCursor();
        error_log($e->getMessage());

        if (($e->errorInfo[1] ?? null) === 1062) {
            return ['This email address is already registered. Try logging in instead.'];
        }

        return ['We couldn’t create your account right now. Please try again.'];
    }

    $stmt->closeCursor();
    return true;
}

function loginUser(string $email, string $password): bool
{
    $stmt = prepare('SELECT user_id, full_name, email, password, role, is_active FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => trim($email)]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if (!$user || !(bool) $user['is_active'] || !password_verify($password, $user['password']))
        return false;

    if (session_status() !== PHP_SESSION_ACTIVE)
        session_start();

    session_regenerate_id(true);
    unset($user['password']);
    $_SESSION['user'] = $user;

    return true;
}
