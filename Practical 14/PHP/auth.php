<?php
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();

    if ($_SESSION['role'] !== 'admin') {
        header('Location: student.php');
        exit;
    }
}

function log_admin_action(mysqli $conn, string $action, string $details): void
{
    $userId = (int) $_SESSION['user_id'];
    $statement = $conn->prepare(
        'INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)'
    );
    $statement->bind_param('iss', $userId, $action, $details);
    $statement->execute();
}
