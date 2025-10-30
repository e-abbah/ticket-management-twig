<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

echo $twig->render('login.twig', [
    'error' => $error,
    'success' => $success,
]);
