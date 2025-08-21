<?php include __DIR__ . '/../includes/header.inc.php'; ?>
<script src ="../scripts.js"></script>
<?php



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

    <h1>Edit Event</h1>

    <form method="POST" action="../includes/event_handler.inc.php">
        <input type="hidden" name="table" value="events">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
        <input type="hidden" name="animal_id" value="<?= $event['animal_id'] ?>">

        <label for="event_date">Date:</label>
            <input type="date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required>
        
        <br>

        <label for="event_time">Time:</label>
            <input type="time" name="event_time" value="<?= htmlspecialchars($event['event_time']) ?>">
        
        <br>

        <label for="weight">Weight:</label>
            <input type="text" name="weight" value="<?= htmlspecialchars($event['weight']) ?>">
        
        <br>

        <label for="comments">Comments:</label>
            <textarea name="comments"><?= htmlspecialchars($event['comments']) ?></textarea>
        
        <br>
        <label for="reason_release">Reason for release:</label>
        <select type="text" name="reason_release" value="<?= htmlspecialchars($event['reason_release']) ?>" placeholder="Reason for release">
            <option value="">Select reason</option>
            <option value="Died" <?= $event['reason_release'] === 'Died' ? 'selected' : '' ?>>Died</option>
            <option value="Euthanised" <?= $event['reason_release'] === 'Euthanised' ? 'selected' : '' ?>>Euthanised</option>
            <option value="Released" <?= $event['reason_release'] === 'Released' ? 'selected' : '' ?>>Released</option>
            <option value="Readmitted" <?= $event['reason_release'] === 'Readmitted' ? 'selected' : '' ?>   >Readmitted</option>
        </select>
                
<h3>Medications Used</h3>
<!--<button onclick="toggle_medication()">Show Medication Table</button>
<button type="button" onclick="window.location.href='add_event_medication.php?event_id=<?= $event['id'] ?>'">Add Medication</button>-->
<?php
// Fetch all medications for the dropdown/list
$medicationsStmt = $pdo->query("SELECT * FROM medications ORDER BY name ASC");
$allMedications = $medicationsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing event medications if editing
$existingEventMeds = [];
if (isset($event['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM event_medications WHERE event_id = ?");
    $stmt->execute([$event['id']]);
    $existingEventMeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Map by medication_id for quick lookup
    $existingMedMap = [];
    foreach ($existingEventMeds as $em) {
        $existingMedMap[$em['medication_id']] = $em['amount_used'];
    }
}
?>



<table>
    <thead>
        <tr>
            <th>Medication</th>
            <th>Unit</th>
            <th>Amount Used</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allMedications as $med): ?>
            <tr>
                <td><?= htmlspecialchars($med['name']) ?></td>
                <td><?= htmlspecialchars($med['unit']) ?></td>
                <td>
                    <input type="number" step="0.01" min="0" name="medications[<?= $med['id'] ?>]" 
                        value="<?= $existingMedMap[$med['id']] ?? '' ?>" placeholder="0" />
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


    <button type="submit">💾 Save Changes</button>
    <br><br>

    </form>


<button onclick="window.location.href='javascript:history.back()'">← Back</button>



<?php include '../includes/footer.inc.php'; ?>