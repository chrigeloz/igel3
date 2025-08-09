<?php
$animalId = $_GET['animal_id'] ?? null;
?>

<link rel="stylesheet" href="../styles.css">

<h1>Add Event</h1>
<form method="POST" action="../includes/handler.inc.php">
  <input type="hidden" name="table" value="events">
  <input type="hidden" name="action" value="add">
  <input type="hidden" name="animal_id" value="<?= htmlspecialchars($animalId) ?>">
  <label for="event_date">Event Date:</label>
  <input type="date" name="event_date" required>
  <label for="event_time">Event Time:</label>
  <input type="time" name="event_time">
  <label for="weight">Weight (kg):</label>
  <input type="number" name="weight" step="0.01" placeholder="Weight (kg)">
  <label for="comments">Comments:</label>
  <textarea name="comments" placeholder="Comments"></textarea>
  <br>
  <button type="submit">Add Event</button>
</form>
<a href="case_detail.php?animal_id=<?= $animalId ?>">← Cancel</a>