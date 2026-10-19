<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    show_message('Use the Delete button beside a student.', 'error');
    go_home();
}

try {
    check_csrf_token();
    $id = check_student_id($_POST['id'] ?? '');

    if (!$id) {
        throw new RuntimeException('Invalid student ID. Nothing was deleted.');
    }

    $query = $conn->prepare('DELETE FROM students WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    if ($query->affected_rows === 1) {
        show_message('Student deleted successfully.');
    } else {
        show_message('Student not found.', 'error');
    }
} catch (mysqli_sql_exception $exception) {
    show_message('Could not delete the student. Check the database and try again.', 'error');
} catch (RuntimeException $exception) {
    show_message($exception->getMessage(), 'error');
}

go_home();
