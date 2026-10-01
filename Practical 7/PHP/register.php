
<?php

require_once __DIR__ . "/form_helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$errors = [];

if (!preg_match("/^[A-Za-z][A-Za-z\s.'-]{1,49}$/", $name)) {
    $errors[] = "Enter a valid full name.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}

if (!preg_match("/^(?:\+91|91)?[6-9][0-9]{9}$/", $mobile)) {
    $errors[] = "Enter a valid mobile number.";
}

if (!in_array($gender, ["Male", "Female"], true)) {
    $errors[] = "Select a gender.";
}

if (!in_array($course, ["Computer Engineering", "Information Technology", "Mechanical Engineering"], true)) {
    $errors[] = "Select a valid course.";
}

if (!in_array($year, ["First Year", "Second Year", "Third Year", "Fourth Year"], true)) {
    $errors[] = "Select a valid year.";
}

if (($_POST["terms"] ?? "") !== "on") {
    $errors[] = "Accept the terms and conditions to continue.";
}

if ($errors) {
    renderFormResponse("Registration Failed", $errors, false, "../Pages/registration.html", "Back to Registration");
    exit;
}

$saved = appendCsvRecord(
    __DIR__ . "/../data/registration.csv",
    ["Submitted At", "Name", "Email", "Mobile Number", "Gender", "Course", "Year", "Terms Accepted"],
    [date("Y-m-d H:i:s"), $name, $email, $mobile, $gender, $course, $year, "Yes"]
);

if (!$saved) {
    http_response_code(500);
    renderFormResponse("Registration Failed", ["Unable to save your registration. Check that the data folder is writable by PHP."], false, "../Pages/registration.html", "Back to Registration", 500);
    exit;
}

renderFormResponse("Registration Successful", ["Your registration has been saved."], true, "../Pages/registration.html", "Register Another Student");