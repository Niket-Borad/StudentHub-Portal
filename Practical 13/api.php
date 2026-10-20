<?php
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$database = "studenthub";
$username = "root";
$password = "";

function sendJson($status, $data)
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function readJson()
{
    $data = json_decode(file_get_contents("php://input"), true);
    if (!is_array($data)) {
        sendJson(400, ["message" => "Please send valid JSON data."]);
    }
    return $data;
}

function validStudent($data)
{
    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");
    $mobile = trim($data["mobile"] ?? "");
    $gender = trim($data["gender"] ?? "");
    $course = trim($data["course"] ?? "");
    $year = filter_var($data["year"] ?? null, FILTER_VALIDATE_INT);

    if ($name === "" || $email === "" || $mobile === "" || $course === "" || !$year || $year < 1) {
        sendJson(400, ["message" => "Name, email, mobile, course, and a valid year are required."]);
    }
    if (strlen($name) > 100 || strlen($email) > 100 || strlen($mobile) > 15 || strlen($gender) > 10 || strlen($course) > 50) {
        sendJson(400, ["message" => "One or more fields are too long."]);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(400, ["message" => "Please enter a valid email address."]);
    }

    return [$name, $email, $mobile, $gender === "" ? null : $gender, $course, $year];
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $method = $_SERVER["REQUEST_METHOD"];

    if ($method === "GET") {
        $statement = $pdo->query("SELECT id, name, email, mobile, gender, course, year FROM students ORDER BY id DESC");
        sendJson(200, ["students" => $statement->fetchAll(PDO::FETCH_ASSOC)]);
    }

    if ($method === "POST") {
        $data = readJson();
        [$name, $email, $mobile, $gender, $course, $year] = validStudent($data);
        $statement = $pdo->prepare(
            "INSERT INTO students (name, email, mobile, gender, course, year) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $statement->execute([$name, $email, $mobile, $gender, $course, $year]);
        sendJson(201, ["message" => "Student added successfully."]);
    }

    if ($method === "PUT") {
        $data = readJson();
        $id = filter_var($data["id"] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            sendJson(400, ["message" => "A valid student ID is required."]);
        }
        [$name, $email, $mobile, $gender, $course, $year] = validStudent($data);
        $check = $pdo->prepare("SELECT id FROM students WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            sendJson(404, ["message" => "Student not found."]);
        }

        $statement = $pdo->prepare(
            "UPDATE students SET name = ?, email = ?, mobile = ?, gender = ?, course = ?, year = ? WHERE id = ?"
        );
        $statement->execute([$name, $email, $mobile, $gender, $course, $year, $id]);
        sendJson(200, ["message" => "Student updated successfully."]);
    }

    if ($method === "DELETE") {
        $data = readJson();
        $id = filter_var($data["id"] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            sendJson(400, ["message" => "A valid student ID is required."]);
        }
        $statement = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $statement->execute([$id]);
        if ($statement->rowCount() === 0) {
            sendJson(404, ["message" => "Student not found."]);
        }
        sendJson(200, ["message" => "Student deleted successfully."]);
    }

    header("Allow: GET, POST, PUT, DELETE");
    sendJson(405, ["message" => "This request method is not supported."]);
} catch (PDOException $error) {
    sendJson(500, ["message" => "Database error. Check the database settings and table columns."]);
}
?>
