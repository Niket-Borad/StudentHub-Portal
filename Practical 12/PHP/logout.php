<?php
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

check_csrf_token();
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
