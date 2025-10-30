<?php
// dashboard.php

session_start();
require_once __DIR__ . '/vendor/autoload.php';

// Redirect to login if not logged in
if (!isset($_SESSION['ticketapp_user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['ticketapp_user'];

// Simulated ticket stats
$ticketsFile = __DIR__ . '/tickets.json';
$tickets = file_exists($ticketsFile) ? json_decode(file_get_contents($ticketsFile), true) : [];

$total = count($tickets);
$open = count(array_filter($tickets, fn($t) => $t['status'] === 'open'));
$resolved = count(array_filter($tickets, fn($t) => $t['status'] === 'resolved'));

// Twig setup
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

// Render template
echo $twig->render('dashboard.twig', [
    'user' => $user,           // pass full session user
    'stats' => [
        'total' => $total,
        'open' => $open,
        'resolved' => $resolved,
    ],
]);
