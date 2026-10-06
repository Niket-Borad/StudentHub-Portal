<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = getenv("STUDENTHUB_DB_HOST") ?: "localhost";
$username = getenv("STUDENTHUB_DB_USER") ?: "root";
$password = getenv("STUDENTHUB_DB_PASSWORD") ?: "";
$database = getenv("STUDENTHUB_DB_NAME") ?: "studenthub";

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");
