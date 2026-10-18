<?php

function startAppSession()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set("session.use_strict_mode", "1");
    ini_set("session.use_only_cookies", "1");
    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax",
    ]);
    session_start();

    if (isset($_SESSION["last_activity"]) && time() - $_SESSION["last_activity"] > 1800) {
        $_SESSION = [];
        session_destroy();
        session_start();
        session_regenerate_id(true);
        $_SESSION["session_expired"] = true;
    }

    if (isset($_SESSION["user_id"])) {
        $_SESSION["last_activity"] = time();
    }
}

function requireUserRole($role)
{
    startAppSession();

    if (!isset($_SESSION["user_id"], $_SESSION["role"])) {
        $reason = !empty($_SESSION["session_expired"]) ? "?timeout=1" : "";
        header("Location: login.html" . $reason);
        exit;
    }

    if ($_SESSION["role"] !== $role) {
        $destination = $_SESSION["role"] === "admin" ? "admin-dashboard.php" : "dashboard.php";
        header("Location: " . $destination);
        exit;
    }
}

function ensureCsrfToken()
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}
