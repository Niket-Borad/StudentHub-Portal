<?php

require_once __DIR__ . "/auth.php";
startAppSession();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

$submittedToken = $_POST["csrf_token"] ?? "";
if (
    !is_string($submittedToken)
    || empty($_SESSION["csrf_token"])
    || !hash_equals($_SESSION["csrf_token"], $submittedToken)
) {
    http_response_code(403);
    exit("Invalid logout request.");
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $cookie = session_get_cookie_params();
    setcookie(session_name(), "", [
        "expires" => time() - 42000,
        "path" => $cookie["path"],
        "domain" => $cookie["domain"],
        "secure" => $cookie["secure"],
        "httponly" => $cookie["httponly"],
        "samesite" => "Lax",
    ]);
}
session_destroy();

header("Location: ../Pages/login.html?logged_out=1");
exit;
