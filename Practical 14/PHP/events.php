<?php
require_once __DIR__ . '/auth.php';
require_admin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'update') {
        $title = trim($_POST['title'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $dateInput = $_POST['starts_at'] ?? '';
        $dateObject = DateTime::createFromFormat('Y-m-d\TH:i', $dateInput);

        if ($title === '' || $location === '' || !$dateObject ||
            $dateObject->format('Y-m-d\TH:i') !== $dateInput) {
            $error = 'Enter an event title, location, and valid date and time.';
        } elseif ($action === 'add') {
            $startsAt = $dateObject->format('Y-m-d H:i:s');
            $statement = $conn->prepare(
                'INSERT INTO events (title, starts_at, location) VALUES (?, ?, ?)'
            );
            $statement->bind_param('sss', $title, $startsAt, $location);
            $statement->execute();
            log_admin_action($conn, 'add', 'Added event: ' . $title);
            header('Location: events.php');
            exit;
        } else {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                $error = 'Invalid event.';
            } else {
                $startsAt = $dateObject->format('Y-m-d H:i:s');
                $statement = $conn->prepare(
                    'UPDATE events SET title = ?, starts_at = ?, location = ? WHERE id = ?'
                );
                $statement->bind_param('sssi', $title, $startsAt, $location, $id);
                $statement->execute();
                log_admin_action($conn, 'update', 'Updated event: ' . $title);
                header('Location: events.php');
                exit;
            }
        }
    } elseif ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $error = 'Invalid event.';
        } else {
            $statement = $conn->prepare('SELECT title FROM events WHERE id = ?');
            $statement->bind_param('i', $id);
            $statement->execute();
            $event = $statement->get_result()->fetch_assoc();

            if ($event) {
                $statement = $conn->prepare('DELETE FROM events WHERE id = ?');
                $statement->bind_param('i', $id);
                $statement->execute();
                log_admin_action($conn, 'delete', 'Deleted event: ' . $event['title']);
            }
            header('Location: events.php');
            exit;
        }
    }
}

$events = $conn->query('SELECT id, title, starts_at, location FROM events ORDER BY starts_at DESC');
require __DIR__ . '/header.php';
?>
<h1>Manage events</h1>
<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<section class="card">
    <h2>Add event</h2>
    <form method="post" class="inline-form">
        <input type="hidden" name="action" value="add">
        <label>Event title <input name="title" maxlength="150" required></label>
        <label>Location <input name="location" maxlength="150" required></label>
        <label>Date and time <input type="datetime-local" name="starts_at" required></label>
        <button type="submit">Add event</button>
    </form>
</section>
<h2>Events</h2>
<?php while ($event = $events->fetch_assoc()): ?>
    <section class="card">
        <form method="post" class="inline-form">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int) $event['id'] ?>">
            <label>Title <input name="title" maxlength="150" required value="<?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Location <input name="location" maxlength="150" required value="<?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Date and time <input type="datetime-local" name="starts_at" required value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($event['starts_at'])), ENT_QUOTES, 'UTF-8') ?>"></label>
            <button type="submit">Save changes</button>
        </form>
        <form method="post" onsubmit="return confirm('Delete this event?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $event['id'] ?>">
            <button class="danger" type="submit">Delete</button>
        </form>
    </section>
<?php endwhile; ?>
<?php require __DIR__ . '/footer.php'; ?>
