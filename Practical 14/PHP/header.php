<?php
require_once __DIR__ . '/auth.php';
require_login();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <strong>StudentHub</strong>
    <nav>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="events.php">Manage events</a>
        <?php else: ?>
            <a href="student.php">My registrations</a>
        <?php endif; ?>
        <span><?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?>
            (<?= htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') ?>)</span>
        <a href="logout.php">Log out</a>
    </nav>
</header>
<main class="container">
