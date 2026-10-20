<?php
require_once __DIR__ . '/auth.php';
require_admin();

$studentCount = $conn->query('SELECT COUNT(*) AS total FROM students')->fetch_assoc()['total'];
$eventCount = $conn->query('SELECT COUNT(*) AS total FROM events')->fetch_assoc()['total'];
$latest = $conn->query(
    'SELECT s.name AS student_name, e.title AS event_title, r.registered_at
     FROM registrations r
     JOIN students s ON s.id = r.student_id
     JOIN events e ON e.id = r.event_id
     ORDER BY r.registered_at DESC
     LIMIT 5'
);

require __DIR__ . '/header.php';
?>
<h1>Admin dashboard</h1>
<section class="cards">
    <article class="card"><h2>Total students</h2><p class="number"><?= (int) $studentCount ?></p></article>
    <article class="card"><h2>Total events</h2><p class="number"><?= (int) $eventCount ?></p></article>
</section>
<h2>Latest registrations</h2>
<table>
    <thead><tr><th>Student</th><th>Event</th><th>Registered</th></tr></thead>
    <tbody>
    <?php while ($registration = $latest->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($registration['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($registration['event_title'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($registration['registered_at'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>
