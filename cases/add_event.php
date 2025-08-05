<?php
$animalId = $_GET['animal_id'] ?? null;
?>
<h1>Add Event</h1>
<form method="POST" action="../includes/handler.inc.php">
  <input type="hidden" name="table" value="events">
  <input type="hidden" name="action" value="add">
  <input type="hidden" name="animal_id" value="<?= htmlspecialchars($animalId) ?>">
  <input type="date" name="event_date" required>
  <input type="time" name="event_time">
  <input type="number" name="weight" step="0.01" placeholder="Weight (kg)">
  <textarea name="comments" placeholder="Comments"></textarea>
  <button type="submit">Add Event</button>
</form>
<a href="case_detail.php?animal_id=<?= $animalId ?>">← Cancel</a>
