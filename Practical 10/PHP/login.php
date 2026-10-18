<?php

require_once __DIR__ . "/form_helpers.php";
require_once __DIR__ . "/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

startAppSession();

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";
$username = is_string($username) ? trim($username) : "";
$password = is_string($password) ? $password : "";

if (!preg_match("/^[A-Za-z0-9_]{3,30}$/", $username) || strlen($password) < 12 || strlen($password) > 72) {
    renderFormResponse("Login Failed", ["Invalid username or password."], false, "../Pages/login.html", "Back to Login");
    exit;
}

try {
    require_once __DIR__ . "/db.php";
    $stmt = $conn->prepare("SELECT id, password_hash, role FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userId, $passwordHash, $role);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found || !password_verify($password, $passwordHash) || !in_array($role, ["student", "admin"], true)) {
        $conn->close();
        renderFormResponse("Login Failed", ["Invalid username or password."], false, "../Pages/login.html", "Back to Login");
        exit;
    }

    $conn->close();
    session_regenerate_id(true);
    $_SESSION = [
        "user_id" => $userId,
        "username" => $username,
        "role" => $role,
        "last_activity" => time(),
        "csrf_token" => bin2hex(random_bytes(32)),
    ];
} catch (mysqli_sql_exception $exception) {
    error_log("StudentHub login failed: " . $exception->getMessage());
    renderFormResponse("Login Failed", ["Unable to log in right now."], false, "../Pages/login.html", "Back to Login", 500);
    exit;
}

header("Location: ../Pages/" . ($role === "admin" ? "admin-dashboard.php" : "dashboard.php"));
exit;
