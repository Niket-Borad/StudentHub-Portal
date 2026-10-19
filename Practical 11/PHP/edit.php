<?php
require 'db.php';

$id = check_student_id($_POST['id'] ?? $_GET['id'] ?? '');
$student = [
    'name' => '',
    'email' => '',
    'mobile' => '',
    'course' => '',
    'year' => '',
];
$error = '';
$notFound = false;

if (!$id) {
    $error = 'Invalid student ID.';
    $notFound = true;
} else {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $student = [
                'name' => form_value('name'),
                'email' => form_value('email'),
                'mobile' => form_value('mobile'),
                'course' => form_value('course'),
                'year' => form_value('year'),
            ];

            check_csrf_token();
            $student = validate_student($student);

            $query = $conn->prepare(
                'UPDATE students SET name = ?, email = ?, mobile = ?, course = ?, year = ? WHERE id = ?'
            );
            $query->bind_param(
                'ssssii',
                $student['name'],
                $student['email'],
                $student['mobile'],
                $student['course'],
                $student['year'],
                $id
            );
            $query->execute();

            if ($query->affected_rows === 0) {
                $check = $conn->prepare('SELECT id FROM students WHERE id = ?');
                $check->bind_param('i', $id);
                $check->execute();
                if (!$check->get_result()->fetch_assoc()) {
                    throw new RuntimeException('Student not found.');
                }
            }

            show_message('Student updated successfully.');
            go_home();
        }

        // Load existing information to fill the form.
        $query = $conn->prepare(
            'SELECT name, email, mobile, course, year FROM students WHERE id = ?'
        );
        $query->bind_param('i', $id);
        $query->execute();
        $student = $query->get_result()->fetch_assoc();

        if (!$student) {
            $error = 'Student not found.';
            $notFound = true;
        }
    } catch (mysqli_sql_exception $exception) {
        $error = $exception->getCode() === 1062
            ? 'That email address is already used by another student.'
            : 'Could not update the student. Check the database and try again.';
    } catch (RuntimeException $exception) {
        $error = $exception->getMessage();
        if ($error === 'Student not found.') {
            $notFound = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Student | StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <header class="header">
        <h1><a href="index.php">StudentHub</a></h1>
        <a class="button gray" href="index.php">Back to list</a>
    </header>

    <section class="card form-card">
        <h2>Edit Student</h2>
        <p>Change the details and save the form.</p>

        <?php if ($error !== ''): ?>
            <p class="message error"><?= h($error) ?></p>
        <?php endif; ?>

        <?php if (!$notFound): ?>
            <form method="post" action="edit.php">
                <input type="hidden" name="id" value="<?= (int) $id ?>">
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
                        <?php for ($year = 1; $year <= 6; $year++): ?>
                            <option value="<?= $year ?>" <?= (int) $student['year'] === $year ? 'selected' : '' ?>>
                                Year <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </label>
                <button class="button purple" type="submit">Update Student</button>
            </form>
        <?php else: ?>
            <a class="button cyan" href="index.php">Return to student list</a>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
