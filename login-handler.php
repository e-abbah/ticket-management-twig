<?php
// login-handler.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        header('Location: login.php?error=missing_fields');
        exit();
    }

    $file = __DIR__ . '/users.json';
    if (!file_exists($file)) {
        header('Location: login.php?error=no_users');
        exit();
    }

    $users = json_decode(file_get_contents($file), true);
    $userFound = false;

    foreach ($users as $user) {
        if (strtolower($user['email']) === $email) {
            $userFound = true;

            if (password_verify($password, $user['password'])) {
                $_SESSION['ticketapp_user'] = [
                    'fullName' => $user['fullName'],
                    'email' => $user['email'],
                ];
                header('Location: dashboard.php'); // ensure path is correct
                exit();
            } else {
                header('Location: login.php?error=wrong_password');
                exit();
            }
        }
    }

    if (!$userFound) {
        header('Location: login.php?error=user_not_found');
        exit();
    }
}

header('Location: login.php');
exit();
