<?php
require 'db.php';

$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? 'success';
unset($_SESSION['message'], $_SESSION['message_type']);

$search = $_GET['search'] ?? '';
$courseFilter = $_GET['course'] ?? '';
$yearFilter = $_GET['year'] ?? '';
$students = [];
$courses = [];
$error = '';

if (!is_string($search)) {
    $search = '';
}
if (!is_string($courseFilter)) {
    $courseFilter = '';
}
if (!is_string($yearFilter) || !in_array($yearFilter, ['', '1', '2', '3', '4', '5', '6'], true)) {
    $yearFilter = '';
}
$search = trim(substr($search, 0, 100));

try {
    // Get course names for the course filter.
    $courseQuery = $conn->prepare('SELECT DISTINCT course FROM students ORDER BY course');
    $courseQuery->execute();
    $courseResult = $courseQuery->get_result();
    while ($row = $courseResult->fetch_assoc()) {
        $courses[] = $row['course'];
    }

    // Search by name/email and optionally filter by course/year.
    $pattern = '%' . $search . '%';
    $year = $yearFilter === '' ? 0 : (int) $yearFilter;
    $studentQuery = $conn->prepare(
        "SELECT id, name, email, mobile, course, year
         FROM students
         WHERE (? = '' OR name LIKE ? OR email LIKE ?)
           AND (? = '' OR course = ?)
           AND (? = 0 OR year = ?)
         ORDER BY name"
    );
    $studentQuery->bind_param(
        'sssssii',
        $search,
        $pattern,
        $pattern,
        $courseFilter,
        $courseFilter,
        $year,
        $year
    );
    $studentQuery->execute();
    $result = $studentQuery->get_result();

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
} catch (mysqli_sql_exception $exception) {
    $error = 'Could not load students. Check MySQL and the database table.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <header class="header">
        <h1>StudentHub</h1>
        <a class="button purple" href="add.php">Add Student</a>
    </header>

    <section class="intro">
        <h2>Student Management</h2>
        <p>Add, view, search, edit, and delete student records.</p>
    </section>

    <?php if ($message !== ''): ?>
        <p class="message <?= h($messageType) ?>"><?= h($message) ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="message error"><?= h($error) ?></p>
    <?php endif; ?>

    <section class="card">
        <h2>Find Students</h2>
        <form class="filters" method="get" action="index.php">
            <input type="search" name="search" placeholder="Search name or email"
                   value="<?= h($search) ?>">

            <select name="course">
                <option value="">All courses</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= h($course) ?>" <?= $courseFilter === $course ? 'selected' : '' ?>>
                        <?= h($course) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="year">
                <option value="">All years</option>
                <?php for ($yearOption = 1; $yearOption <= 6; $yearOption++): ?>
                    <option value="<?= $yearOption ?>" <?= $yearFilter === (string) $yearOption ? 'selected' : '' ?>>
                        Year <?= $yearOption ?>
                    </option>
                <?php endfor; ?>
            </select>

            <button class="button cyan" type="submit">Search / Filter</button>
            <a class="button gray" href="index.php">Clear</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($students) === 0): ?>
                    <tr><td colspan="6" class="empty">No students found.</td></tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= h($student['name']) ?></td>
                            <td><?= h($student['email']) ?></td>
                            <td><?= h($student['mobile']) ?></td>
                            <td><?= h($student['course']) ?></td>
                            <td><?= (int) $student['year'] ?></td>
                            <td class="actions">
                                <a class="button small cyan" href="edit.php?id=<?= (int) $student['id'] ?>">Edit</a>
                                <form method="post" action="delete.php"
                                      onsubmit="return confirm('Delete this student?');">
                                    <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                    <button class="button small red" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="count">Students shown: <?= count($students) ?></p>
    </section>
</main>
</body>
</html>
