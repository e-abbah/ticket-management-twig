<?php
// signup-handler.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($fullName) || empty($email) || empty($password)) {
        header('Location: signup.php?error=missing_fields');
        exit();
    }

    // Load existing users from a JSON file (fake DB)
    $file = __DIR__ . '/users.json';
    $users = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    // Check if user already exists
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            header('Location: signup.php?error=email_exists');
            exit();
        }
    }

    // Hash password and save new user
    $users[] = [
        'fullName' => $fullName,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ];
    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

    // Redirect to login page after signup
    header('Location: login.php?success=account_created');
    exit();
}

header('Location: signup.php');
exit();
