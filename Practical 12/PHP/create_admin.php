<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$message = '';
$message_type = '';
$admin_count = 0;

try {
    $result = $conn->query('SELECT COUNT(*) AS total FROM admins');
    $admin_count = (int) $result->fetch_assoc()['total'];
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());
    die('Could not check the admin table. Import database.sql and try again.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf_token();

    if ($admin_count > 0) {
        $message = 'An admin account already exists. Remove or rename create_admin.php now.';
        $message_type = 'error';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || strlen($username) > 50) {
            $message = 'Enter a username with 1 to 50 characters.';
            $message_type = 'error';
        } elseif (strlen($password) < 8) {
            $message = 'Choose a password with at least 8 characters.';
            $message_type = 'error';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $conn->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
                $stmt->bind_param('ss', $username, $password_hash);
                $stmt->execute();
                $admin_count = 1;
                $message = 'Admin account created. Delete or rename create_admin.php before using the site.';
                $message_type = 'success';
            } catch (mysqli_sql_exception $error) {
                error_log($error->getMessage());
                $message = 'The admin account could not be created. The username may already be in use.';
                $message_type = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin | Event Horizon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <p class="eyebrow">ONE-TIME SETUP</p>
        <h1>Create the first admin</h1>

        <?php if ($message !== ''): ?>
            <p class="message <?= h($message_type) ?>"><?= h($message) ?></p>
        <?php endif; ?>

        <?php if ($admin_count === 0): ?>
            <form method="post">
                <?= csrf_field() ?>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="50" required>

                <label for="password">Password (at least 8 characters)</label>
                <input type="password" id="password" name="password" minlength="8" required>

                <button type="submit" class="button">Create admin account</button>
            </form>
        <?php else: ?>
            <p>An admin is already set up. For safety, remove this setup file from the project folder.</p>
            <a class="button button-secondary" href="login.php">Go to login</a>
        <?php endif; ?>
    </main>
</body>
</html>
