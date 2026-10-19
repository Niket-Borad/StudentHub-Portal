<?php
require 'db.php';

$student = [
    'name' => '',
    'email' => '',
    'mobile' => '',
    'course' => '',
    'year' => '',
];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = [
        'name' => form_value('name'),
        'email' => form_value('email'),
        'mobile' => form_value('mobile'),
        'course' => form_value('course'),
        'year' => form_value('year'),
    ];

    try {
        check_csrf_token();
        $student = validate_student($student);

        // The ? marks are filled with the values below, safely.
        $query = $conn->prepare(
            'INSERT INTO students (name, email, mobile, course, year) VALUES (?, ?, ?, ?, ?)'
        );
        $query->bind_param(
            'ssssi',
            $student['name'],
            $student['email'],
            $student['mobile'],
            $student['course'],
            $student['year']
        );
        $query->execute();

        show_message('Student added successfully.');
        go_home();
    } catch (mysqli_sql_exception $exception) {
        if ($exception->getCode() === 1062) {
            $error = 'That email address is already in use.';
        } else {
            $error = 'Could not save the student. Check the database and try again.';
        }
    } catch (RuntimeException $exception) {
        $error = $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Student | StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <header class="header">
        <h1><a href="index.php">StudentHub</a></h1>
        <a class="button gray" href="index.php">Back to list</a>
    </header>

    <section class="card form-card">
        <h2>Add a Student</h2>
        <p>Enter the student's details.</p>
        <?php if ($error !== ''): ?>
            <p class="message error"><?= h($error) ?></p>
        <?php endif; ?>

        <form method="post" action="add.php">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

            <label>Name
                <input type="text" name="name" maxlength="100" required value="<?= h($student['name']) ?>">
            </label>
            <label>Email
                <input type="email" name="email" maxlength="100" required value="<?= h($student['email']) ?>">
            </label>
            <label>Mobile number
                <input type="tel" name="mobile" maxlength="15" pattern="[0-9+\-\s()]{7,15}"
                       required value="<?= h($student['mobile']) ?>">
            </label>
            <label>Course
                <input type="text" name="course" maxlength="50" required value="<?= h($student['course']) ?>">
            </label>
            <label>Academic year
                <select name="year" required>
                    <option value="">Select a year</option>
                    <?php for ($year = 1; $year <= 6; $year++): ?>
                        <option value="<?= $year ?>" <?= (string) $student['year'] === (string) $year ? 'selected' : '' ?>>
                            Year <?= $year ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </label>

            <button class="button purple" type="submit">Save Student</button>
        </form>
    </section>
</main>
</body>
</html>
