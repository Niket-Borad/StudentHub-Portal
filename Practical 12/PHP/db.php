<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'studenthub';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());
    die('Could not connect to the database. Check the settings in db.php and make sure MySQL is running.');
}
