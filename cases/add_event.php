<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
$animalId = $_GET['animal_id'] ?? null;
?>

<h1>Add Event <button type="submit">Add Event</button>  <button onclick="location.href='case_detail.php?animal_id=<?= $animalId ?>'">Cancel</button></h1>
<form method="POST" action="../includes/event_handler.inc.php">
  <input type="hidden" name="table" value="events">
  <input type="hidden" name="action" value="add">
  <input type="hidden" name="animal_id" value="<?= htmlspecialchars($animalId) ?>">
  <label for="event_date">Event Date:</label>
  <input type="date" name="event_date" value="<?php echo date("Y-m-d"); ?>">
  <label for="event_time">Event Time:</label>
  <input type="time" name="event_time" value="<?php echo $dt->format("H:i"); ?>">
  <label for="weight">Weight (grams):</label>
  <input type="number" name="weight" step="1" placeholder="Weight (grams)" value=0>
  <label for="comments">Comments:</label>
  <textarea name="comments" placeholder="Comments"></textarea>
  <label for="reason_release">Reason for release:</label>
        <select type="text" name="reason_release" placeholder="Reason for release">
            <option value="">Select reason</option>
            <option value="Died">Died</option>
            <option value="Euthanised">Euthanised</option>
            <option value="Released">Released</option>
            <option value="Readmitted">Readmitted</option>
        </select>
 
<h3>Medications Used</h3>

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
	    <th>Available</th>	
	    <th>Expiry</th>	
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
		<td><?= htmlspecialchars($med['total_amount']) ?></td>
		<td><?= htmlspecialchars($med['expiry']) ?></td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

 
  <button type="submit">Add Event</button>

<br>
<br>
<button onclick="location.href='case_detail.php?animal_id=<?= $animalId ?>'">← Cancel</button>


</form>


<?php include '../includes/footer.inc.php'; ?>
