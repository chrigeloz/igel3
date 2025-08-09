<?php
require_once '../includes/dbh.inc.php';
require_once '../includes/utils.php';

// Validate and fetch event ID
$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($eventId <= 0) {
    die("Invalid event ID.");
}

// Fetch the event from the database
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link rel="stylesheet" href="../styles.css">
</head>

<body>

    <h1>Edit Event</h1>

    <form method="POST" action="../includes/handler.inc.php">
        <input type="hidden" name="table" value="events">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
        <input type="hidden" name="animal_id" value="<?= $event['animal_id'] ?>">

        <label>Date:
            <input type="date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required>
        </label>
        <br>

        <label>Time:
            <input type="time" name="event_time" value="<?= htmlspecialchars($event['event_time']) ?>">
        </label>
        <br>

        <label>Weight:
            <input type="text" name="weight" value="<?= htmlspecialchars($event['weight']) ?>">
        </label>
        <br>

        <label>Comments:
            <textarea name="comments"><?= htmlspecialchars($event['comments']) ?></textarea>
        </label>
        <br><br>

        <button type="submit">💾 Save Changes</button>
        <a href="case_detail.php?animal_id=<?= $event['animal_id'] ?>">← Cancel</a>
    </form>

</body>

</html>