<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/db.php';

$categories = ['Conference', 'Workshop', 'Seminar', 'Cultural', 'Sports', 'Other'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('A valid event ID is required.');
}

try {
    $stmt = $conn->prepare('SELECT id, title, description, event_date, event_time, venue, category, poster FROM events WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());
    die('The event could not be loaded. Check the database setup and try again.');
}

if (!$event) {
    http_response_code(404);
    exit('Event not found.');
}

$title = $event['title'];
$description = $event['description'];
$event_date = $event['event_date'];
$event_time = substr($event['event_time'], 0, 5);
$venue = $event['venue'];
$category = $event['category'];
$current_poster = $event['poster'];
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

    $new_poster = null;
    if ($message === '') {
        $upload = upload_poster($_FILES['poster'] ?? []);
        if ($upload['error'] !== null) {
            $message = $upload['error'];
        } else {
            $new_poster = $upload['filename'];
        }
    }

    if ($message === '') {
        $poster_to_save = $new_poster !== null ? $new_poster : $current_poster;

        try {
            $stmt = $conn->prepare('UPDATE events SET title = ?, description = ?, event_date = ?, event_time = ?, venue = ?, category = ?, poster = ? WHERE id = ?');
            $stmt->bind_param('sssssssi', $title, $description, $event_date, $event_time, $venue, $category, $poster_to_save, $id);
            $stmt->execute();

            if ($new_poster !== null) {
                delete_poster_file($current_poster);
            }

            $_SESSION['flash_message'] = 'Event updated successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $error) {
            error_log($error->getMessage());
            delete_poster_file($new_poster);
            $message = 'The event could not be updated. Your existing poster has been kept.';
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
    <title>Edit Event | Event Horizon</title>
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
        <p class="eyebrow">UPDATE EVENT</p>
        <h1>Edit event</h1>

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

                <label>Current poster</label>
                <?php if (!empty($current_poster)): ?>
                    <img class="poster-preview" src="uploads/posters/<?= rawurlencode($current_poster) ?>" alt="Current event poster">
                <?php else: ?>
                    <p class="muted">No poster has been uploaded.</p>
                <?php endif; ?>

                <label for="poster">Replace poster (optional)</label>
                <input type="file" id="poster" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="help-text">Leave empty to keep the current poster. JPG, JPEG, PNG, or WebP, up to 2 MB.</p>

                <div class="form-actions">
                    <button type="submit">Save changes</button>
                    <a class="button button-secondary" href="index.php">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
