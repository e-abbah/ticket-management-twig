<?php
require_once __DIR__ . '/vendor/autoload.php';

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Get the requested page from query string, default to landing
$page = $_GET['page'] ?? 'landing';

// Map page names to Twig templates
$pages = [
    'landing' => 'landing.twig',
    'login' => 'login.twig',
    'signup' => 'signup.twig',
];

// Check if page exists, else show 404
$template = $pages[$page] ?? null;

if ($template) {
    echo $twig->render($template);
} else {
    // Simple 404 page
    echo "<h1>404 - Page Not Found</h1>";
}
