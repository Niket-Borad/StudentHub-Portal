<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/db.php';

$message = $_SESSION['flash_message'] ?? '';
$message_type = $_SESSION['flash_type'] ?? 'success';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

try {
    $result = $conn->query('SELECT id, title, description, event_date, event_time, venue, category, poster FROM events ORDER BY event_date ASC, event_time ASC');
    $events = $result->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());
    die('Events could not be loaded. Check that database.sql was imported correctly.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events | Event Horizon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a class="brand" href="index.php">Event <span>Horizon</span></a>
            <div class="topbar-actions">
                <span class="welcome">Signed in as <?= h($_SESSION['admin_username'] ?? 'Admin') ?></span>
                <form class="inline-form" action="logout.php" method="post">
                    <?= csrf_field() ?>
                    <button class="button-secondary button-small" type="submit">Log out</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container page">
        <div class="page-heading">
            <div>
                <p class="eyebrow">ADMIN DASHBOARD</p>
                <h1>College events</h1>
            </div>
            <a class="button" href="add.php">+ Add event</a>
        </div>

        <?php if ($message !== ''): ?>
            <p class="message <?= h($message_type) ?>"><?= h($message) ?></p>
        <?php endif; ?>

        <?php if (count($events) === 0): ?>
            <section class="empty-state">
                <h2>No events yet</h2>
                <p>Add your first event to see it listed here.</p>
                <a class="button" href="add.php">Add your first event</a>
            </section>
        <?php else: ?>
            <section class="event-grid">
                <?php foreach ($events as $event): ?>
                    <article class="event-card">
                        <?php if (!empty($event['poster'])): ?>
                            <img
                                class="event-poster"
                                src="uploads/posters/<?= rawurlencode($event['poster']) ?>"
                                alt="Poster for <?= h($event['title']) ?>">
                        <?php else: ?>
                            <div class="poster-placeholder">No poster uploaded</div>
                        <?php endif; ?>

                        <div class="event-content">
                            <h2><?= h($event['title']) ?></h2>
                            <p class="event-description"><?= h($event['description']) ?></p>
                            <div class="event-details">
                                <span><strong>Date:</strong> <?= h($event['event_date']) ?></span>
                                <span><strong>Time:</strong> <?= h(date('g:i A', strtotime($event['event_time']))) ?></span>
                                <span><strong>Venue:</strong> <?= h($event['venue']) ?></span>
                                <span><strong>Category:</strong> <?= h($event['category']) ?></span>
                            </div>
                            <div class="event-actions">
                                <a class="button button-small" href="edit.php?id=<?= (int) $event['id'] ?>">Edit</a>
                                <form class="inline-form" action="delete.php" method="post" onsubmit="return confirm('Delete this event? This cannot be undone.');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $event['id'] ?>">
                                    <button class="button-danger button-small" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
