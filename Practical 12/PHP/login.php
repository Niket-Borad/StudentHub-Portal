<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf_token();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = 'Enter your username and password.';
        $message_type = 'error';
    } else {
        try {
            $stmt = $conn->prepare('SELECT id, username, password FROM admins WHERE username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();

            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: index.php');
                exit;
            }

            $message = 'The username or password is incorrect.';
            $message_type = 'error';
        } catch (mysqli_sql_exception $error) {
            error_log($error->getMessage());
            $message = 'Login could not be completed. Check the database setup and try again.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Event Horizon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <p class="eyebrow">EVENT MANAGEMENT</p>
        <h1>Admin login</h1>
        <p class="muted">Sign in to manage college events.</p>

        <?php if ($message !== ''): ?>
            <p class="message <?= h($message_type) ?>"><?= h($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" maxlength="50" autocomplete="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <button type="submit" class="button">Log in</button>
        </form>
    </main>
</body>
</html>
