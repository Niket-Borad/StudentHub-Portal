<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

$statement = $conn->prepare(
    'SELECT e.title, e.starts_at, r.registered_at
     FROM students s
     JOIN registrations r ON r.student_id = s.id
     JOIN events e ON e.id = r.event_id
     JOIN users u ON u.email = s.email
     WHERE u.id = ?
     ORDER BY r.registered_at DESC'
);
$userId = (int) $_SESSION['user_id'];
$statement->bind_param('i', $userId);
$statement->execute();
$registrations = $statement->get_result();

require __DIR__ . '/header.php';
?>
<h1>My registrations</h1>
<table>
    <thead><tr><th>Event</th><th>Event date</th><th>Registered on</th></tr></thead>
    <tbody>
    <?php while ($registration = $registrations->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($registration['title'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($registration['starts_at'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($registration['registered_at'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>
