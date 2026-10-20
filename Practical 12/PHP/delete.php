<?php
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

check_csrf_token();
require_once __DIR__ . '/db.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    $_SESSION['flash_message'] = 'A valid event ID is required.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $conn->prepare('SELECT poster FROM events WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    if (!$event) {
        $_SESSION['flash_message'] = 'That event no longer exists.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php');
        exit;
    }

    $stmt = $conn->prepare('DELETE FROM events WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    delete_poster_file($event['poster']);
    $_SESSION['flash_message'] = 'Event deleted successfully.';
    $_SESSION['flash_type'] = 'success';
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());
    $_SESSION['flash_message'] = 'The event could not be deleted. Its poster has been kept.';
    $_SESSION['flash_type'] = 'error';
}

header('Location: index.php');
exit;
