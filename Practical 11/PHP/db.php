<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Change these settings if your MySQL username or password is different.
try {
    $conn = new mysqli('localhost', 'root', '', 'studenthub');
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    http_response_code(503);
    exit('Cannot connect to MySQL. Start MySQL in XAMPP and import database.sql.');
}

// Escape text before showing it in HTML.
function h($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Make one token for the session to protect forms.
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function check_csrf_token()
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        throw new RuntimeException('Form expired. Please try again.');
    }
}

function show_message($text, $type = 'success')
{
    $_SESSION['message'] = $text;
    $_SESSION['message_type'] = $type;
}

function go_home()
{
    header('Location: index.php');
    exit;
}

// Return one form value as safe, trimmed text.
function form_value($name)
{
    $value = $_POST[$name] ?? '';
    return is_string($value) ? trim($value) : '';
}

function check_student_id($value)
{
    $id = filter_var($value, FILTER_VALIDATE_INT);
    return $id !== false && $id > 0 ? $id : false;
}

function validate_student($student)
{
    if (
        $student['name'] === '' || $student['email'] === '' ||
        $student['mobile'] === '' || $student['course'] === '' ||
        $student['year'] === ''
    ) {
        throw new RuntimeException('Please fill in every field.');
    }

    if (
        strlen($student['name']) > 100 || strlen($student['email']) > 100 ||
        strlen($student['mobile']) > 15 || strlen($student['course']) > 50
    ) {
        throw new RuntimeException('One or more fields are too long.');
    }

    if (!filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Enter a valid email address.');
    }

    if (!preg_match('/^[0-9+\-\s()]{7,15}$/', $student['mobile'])) {
        throw new RuntimeException('Enter a valid mobile number.');
    }

    $year = filter_var($student['year'], FILTER_VALIDATE_INT);
    if ($year === false || $year < 1 || $year > 6) {
        throw new RuntimeException('Academic year must be from 1 to 6.');
    }

    $student['year'] = $year;
    return $student;
}
