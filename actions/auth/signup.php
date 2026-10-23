<?php
require_once __DIR__ . '/../../includes/paths.php';
require_once __DIR__ . '/../../includes/auth_functions.php';

if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('signup.php'));
    exit;
}

$fullName = trim($_POST['fullName'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

$result = signUp($fullName, $email, $password, $confirmPassword);

if ($result === true){
    $_SESSION["flash"] = [
        "type" => "success",
        "message" => "Account created successfully. You can log in now."
    ];

    header("Location: " . app_url("login.php"));
    exit ();
}

$_SESSION['signup_errors'] = $result;

header("Location: " . app_url("signup.php"));

exit ();
