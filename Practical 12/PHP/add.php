<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/db.php';

$categories = ['Conference', 'Workshop', 'Seminar', 'Cultural', 'Sports', 'Other'];
$title = '';
$description = '';
$event_date = '';
$event_time = '';
$venue = '';
$category = '';
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf_token();

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $event_time = trim($_POST['event_time'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if ($title === '' || strlen($title) > 150) {
        $message = 'Enter an event title with no more than 150 characters.';
    } elseif ($description === '') {
        $message = 'Enter an event description.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $event_date) || !checkdate((int) substr($event_date, 5, 2), (int) substr($event_date, 8, 2), (int) substr($event_date, 0, 4))) {
        $message = 'Choose a valid event date.';
    } elseif (!preg_match('/^\d{2}:\d{2}$/', $event_time)) {
        $message = 'Choose a valid event time.';
    } elseif ($venue === '' || strlen($venue) > 150) {
        $message = 'Enter a venue with no more than 150 characters.';
    } elseif (!in_array($category, $categories, true)) {
        $message = 'Choose a valid event category.';
    }

    $poster_filename = null;
    if ($message === '') {
        $upload = upload_poster($_FILES['poster'] ?? []);
        if ($upload['error'] !== null) {
            $message = $upload['error'];
        } else {
            $poster_filename = $upload['filename'];
        }
    }

    if ($message === '') {
        try {
            $stmt = $conn->prepare('INSERT INTO events (title, description, event_date, event_time, venue, category, poster) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssss', $title, $description, $event_date, $event_time, $venue, $category, $poster_filename);
            $stmt->execute();
            $_SESSION['flash_message'] = 'Event added successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $error) {
            error_log($error->getMessage());
            delete_poster_file($poster_filename);
            $message = 'The event could not be saved. Check the database connection and try again.';
        }
    }

    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event | Event Horizon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a class="brand" href="index.php">Event <span>Horizon</span></a>
            <a class="button button-secondary button-small" href="index.php">Back to events</a>
        </div>
    </header>

    <main class="container page">
        <p class="eyebrow">NEW EVENT</p>
        <h1>Add an event</h1>

        <section class="form-card">
            <?php if ($message !== ''): ?>
                <p class="message <?= h($message_type) ?>"><?= h($message) ?></p>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <label for="title">Event title</label>
                <input type="text" id="title" name="title" maxlength="150" value="<?= h($title) ?>" required>

                <label for="description">Event description</label>
                <textarea id="description" name="description" required><?= h($description) ?></textarea>

                <label for="event_date">Event date</label>
                <input type="date" id="event_date" name="event_date" value="<?= h($event_date) ?>" required>

                <label for="event_time">Event time</label>
                <input type="time" id="event_time" name="event_time" value="<?= h($event_time) ?>" required>

                <label for="venue">Venue</label>
                <input type="text" id="venue" name="venue" maxlength="150" value="<?= h($venue) ?>" required>

                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Choose a category</option>
                    <?php foreach ($categories as $option): ?>
                        <option value="<?= h($option) ?>" <?= $category === $option ? 'selected' : '' ?>><?= h($option) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="poster">Event poster (optional)</label>
                <input type="file" id="poster" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="help-text">JPG, JPEG, PNG, or WebP. Maximum size: 2 MB.</p>

                <div class="form-actions">
                    <button type="submit">Save event</button>
                    <a class="button button-secondary" href="index.php">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
