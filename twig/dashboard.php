<?php
// dashboard.php

require_once __DIR__ . '/vendor/autoload.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['ticketapp_user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['ticketapp_user'];
$userName = $user['fullName'] ?? 'User';

// Simulated ticket stats — replace later with DB/API
$tickets = [
    ['id' => 1, 'status' => 'open'],
    ['id' => 2, 'status' => 'resolved'],
    ['id' => 3, 'status' => 'open'],
    ['id' => 4, 'status' => 'resolved'],
];

$total = count($tickets);
$open = count(array_filter($tickets, fn($t) => $t['status'] === 'open'));
$resolved = count(array_filter($tickets, fn($t) => $t['status'] === 'resolved'));

// Setup Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

// Render template
echo $twig->render('dashboard.twig', [
    'userName' => $userName,
    'stats' => [
        'total' => $total,
        'open' => $open,
        'resolved' => $resolved,
    ],
]);
