<?php
// login-handler.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($email) || empty($password)) {
        header('Location: login.php?error=missing_fields');
        exit();
    }

    // Check if users.json exists
    $file = __DIR__ . '/users.json';
    if (!file_exists($file)) {
        header('Location: login.php?error=no_users');
        exit();
    }

    $users = json_decode(file_get_contents($file), true);

    $userFound = false;
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $userFound = true;

            if (password_verify($password, $user['password'])) {
                // ✅ Successful login
                $_SESSION['ticketapp_user'] = [
                    'fullName' => $user['fullName'],
                    'email' => $user['email'],
                ];
                header('Location: dashboard.php');
                exit();
            } else {
                // ❌ Password incorrect
                header('Location: login.php?error=wrong_password');
                exit();
            }
        }
    }

    // ❌ Email not found at all
    if (!$userFound) {
        header('Location: login.php?error=user_not_found');
        exit();
    }
}

// Redirect to login if accessed directly
header('Location: login.php');
exit();
