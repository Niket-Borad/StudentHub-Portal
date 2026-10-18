<?php

require_once __DIR__ . "/form_helpers.php";
require_once __DIR__ . "/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

startAppSession();

$username = $_POST["username"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";
$csrfToken = $_POST["csrf_token"] ?? "";

$username = is_string($username) ? trim($username) : "";
$email = is_string($email) ? strtolower(trim($email)) : "";
$password = is_string($password) ? $password : "";
$confirmPassword = is_string($confirmPassword) ? $confirmPassword : "";
$csrfToken = is_string($csrfToken) ? $csrfToken : "";

if (
    empty($_SESSION["csrf_token"])
    || !hash_equals($_SESSION["csrf_token"], $csrfToken)
) {
    renderFormResponse("Account Creation Failed", ["The form expired or is invalid. Refresh the page and try again."], false, "../Pages/account-registration.html", "Back to Account Creation", 403);
    exit;
}

$errors = [];
if (!preg_match("/^[A-Za-z0-9_]{3,30}$/", $username)) {
    $errors[] = "Username must be 3-30 characters and contain only letters, numbers, or underscores.";
}
if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}
if (strlen($password) < 12 || strlen($password) > 72) {
    $errors[] = "Password must be between 12 and 72 bytes.";
}
if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

if ($errors) {
    renderFormResponse("Account Creation Failed", $errors, false, "../Pages/account-registration.html", "Back to Account Creation");
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
if ($passwordHash === false) {
    error_log("StudentHub account creation failed: password hashing failed.");
    renderFormResponse("Account Creation Failed", ["Unable to securely create your login."], false, "../Pages/account-registration.html", "Back to Account Creation", 500);
    exit;
}

$conn = null;
try {
    require_once __DIR__ . "/db.php";
    $stmt = $conn->prepare(
        "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'student')"
    );
    $stmt->bind_param("sss", $username, $email, $passwordHash);
    $stmt->execute();
    $stmt->close();
    $conn->close();
} catch (mysqli_sql_exception $exception) {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
    error_log("StudentHub account creation failed: " . $exception->getMessage());
    $isDuplicate = $exception->getCode() === 1062;
    $message = $isDuplicate
        ? "That username or email is already in use."
        : "Unable to create your login right now.";
    renderFormResponse("Account Creation Failed", [$message], false, "../Pages/account-registration.html", "Back to Account Creation", $isDuplicate ? 422 : 500);
    exit;
}

renderFormResponse("Account Created", ["Your login has been created. You can now sign in."], true, "../Pages/login.html", "Go to Login");
