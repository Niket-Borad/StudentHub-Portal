<?php

require_once __DIR__ . "/form_helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

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
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userId, $passwordHash);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found || !password_verify($password, $passwordHash)) {
        $conn->close();
        renderFormResponse("Login Failed", ["Invalid username or password."], false, "../Pages/login.html", "Back to Login");
        exit;
    }

    $conn->close();
} catch (mysqli_sql_exception $exception) {
    error_log("StudentHub login failed: " . $exception->getMessage());
    renderFormResponse("Login Failed", ["Unable to log in right now."], false, "../Pages/login.html", "Back to Login", 500);
    exit;
}

session_set_cookie_params([
    "httponly" => true,
    "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
    "samesite" => "Lax",
]);
session_start();
session_regenerate_id(true);
$_SESSION["user_id"] = $userId;
$_SESSION["username"] = $username;

header("Location: ../Pages/dashboard.html");
exit;
