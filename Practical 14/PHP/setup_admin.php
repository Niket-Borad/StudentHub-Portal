<?php
require_once __DIR__ . '/db.php';
session_start();

$error = '';
$created = false;
$adminCount = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (int) $adminCount === 0) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = 'Enter a username, valid email, and password with at least 8 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'admin';
        $statement = $conn->prepare(
            'INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)'
        );
        $statement->bind_param('ssss', $username, $email, $hash, $role);
        $statement->execute();
        $created = true;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create admin - StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container narrow">
    <h1>Initial admin setup</h1>
    <?php if ($created): ?>
        <p>Admin created. Delete <code>setup_admin.php</code> now, then log in.</p>
        <p><a href="login.php">Go to login</a></p>
    <?php elseif ((int) $adminCount > 0): ?>
        <p>An admin account already exists. For safety, setup is disabled. Delete <code>setup_admin.php</code>.</p>
    <?php else: ?>
        <?php if ($error !== ''): ?>
            <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form method="post">
            <label>Username <input name="username" required></label>
            <label>Email <input type="email" name="email" required></label>
            <label>Password (at least 8 characters) <input type="password" name="password" minlength="8" required></label>
            <button type="submit">Create admin</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
