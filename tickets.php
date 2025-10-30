<?php
// tickets.php
session_start();

// ------------------- Session Check -------------------
if (!isset($_SESSION['ticketapp_user'])) {
    header("Location: login.php"); // redirect to login if not logged in
    exit;
}

// ------------------- Current User -------------------
$currentUser = $_SESSION['ticketapp_user'];

// ------------------- Twig Setup -------------------
require_once __DIR__ . '/vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

// ------------------- Tickets Storage -------------------
$ticketsFile = __DIR__ . '/tickets.json';
if (!file_exists($ticketsFile)) {
    file_put_contents($ticketsFile, json_encode([]));
}
$tickets = json_decode(file_get_contents($ticketsFile), true);

// ------------------- Helper Function -------------------
function saveTickets($tickets, $file) {
    file_put_contents($file, json_encode(array_values($tickets), JSON_PRETTY_PRINT));
}

// ------------------- Handle POST Actions -------------------
$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'general');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $description === '') {
        echo $twig->render('ticket_management.twig', [
            'tickets' => $tickets,
            'user' => $currentUser,
            'error' => 'Please fill in all fields.'
        ]);
        exit;
    }

    $newTicket = [
        'id' => uniqid(),
        'title' => htmlspecialchars($title),
        'category' => htmlspecialchars($category),
        'description' => htmlspecialchars($description),
        'status' => 'open',
        'createdBy' => $currentUser['email'],
        'createdAt' => date('Y-m-d H:i:s')
    ];

    $tickets[] = $newTicket;
    saveTickets($tickets, $ticketsFile);

    echo $twig->render('ticket_management.twig', [
        'tickets' => $tickets,
        'user' => $currentUser,
        'success' => 'Ticket created successfully!'
    ]);
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    $tickets = array_filter($tickets, fn($t) => $t['id'] !== $id);
    saveTickets($tickets, $ticketsFile);

    echo $twig->render('ticket_management.twig', [
        'tickets' => $tickets,
        'user' => $currentUser,
        'success' => 'Ticket deleted successfully!'
    ]);
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'] ?? '';
    foreach ($tickets as &$t) {
        if ($t['id'] === $id) {
            $t['title'] = htmlspecialchars($_POST['title'] ?? $t['title']);
            $t['category'] = htmlspecialchars($_POST['category'] ?? $t['category']);
            $t['description'] = htmlspecialchars($_POST['description'] ?? $t['description']);
            $t['status'] = htmlspecialchars($_POST['status'] ?? $t['status']);
            break;
        }
    }
    saveTickets($tickets, $ticketsFile);

    echo $twig->render('ticket_management.twig', [
        'tickets' => $tickets,
        'user' => $currentUser,
        'success' => 'Ticket updated successfully!'
    ]);
    exit;
}

// ------------------- Default: Show Tickets -------------------
echo $twig->render('ticket_management.twig', [
    'tickets' => $tickets,
    'user' => $currentUser
]);
