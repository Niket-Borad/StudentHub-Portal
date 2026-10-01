<?php

require_once __DIR__ . "/form_helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");
$errors = [];

if ($name === "" || !preg_match("/^[\p{L}][\p{L}\p{M} .'-]{0,99}$/u", $name)) {
    $errors[] = "Enter a valid name using letters, spaces, apostrophes, periods, or hyphens.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}

if ($message === "" || strlen($message) > 5000) {
    $errors[] = "Enter a message of no more than 5,000 characters.";
}

if ($errors) {
    renderFormResponse("Message Not Sent", $errors, false, "../Pages/contact.html", "Back to Contact");
    exit;
}

$saved = appendCsvRecord(
    __DIR__ . "/../data/contact.csv",
    ["Submitted At", "Name", "Email", "Message"],
    [date("Y-m-d H:i:s"), $name, $email, $message]
);

if (!$saved) {
    http_response_code(500);
    renderFormResponse("Message Not Sent", ["Unable to save your message. Check that the data folder is writable by PHP."], false, "../Pages/contact.html", "Back to Contact", 500);
    exit;
}

renderFormResponse("Message Sent", ["Your message has been saved."], true, "../Pages/contact.html", "Send Another Message");
