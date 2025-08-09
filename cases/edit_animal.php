<?php
// edit_animal.php
require_once '../includes/dbh.inc.php';
require_once '../includes/utils.php';

$animalId = $_GET['id'] ?? 0;
$animal = getAnimal($pdo, $animalId);
if (!$animal) {
    die("Animal not found.");
}
?>

<link rel="stylesheet" href="../styles.css">

<h1>Edit Animal</h1>
<form method="POST" action="../includes/handler.inc.php">
    <input type="hidden" name="finder_id" value="<?= $animal['finder_id'] ?>">
    <input type="hidden" name="table" value="animals">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?= $animal['id'] ?>">
    <label for="name">Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($animal['name']) ?>" placeholder="Name">
    <label for="species">Species:</label>
    <input type="text" name="species" value="<?= htmlspecialchars($animal['species']) ?>" placeholder="Species"
        required>
    <label for="age">Age:</label>
    <input type="number" name="age" value="<?= htmlspecialchars($animal['age']) ?>" placeholder="Age">
    <label for="gender">Gender:</label>
    <select type="text" name="gender">
        <option value="male" <?= $animal['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
        <option value="female" <?= $animal['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
        <option value="other" <?= $animal['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
    </select>
    <label for="description">Description:</label>
    <input type="text" name="description" value="<?= htmlspecialchars($animal['description']) ?>"
        placeholder="Description">
    <label for="location_found">Location Found:</label>
    <input type="text" name="location_found" value="<?= htmlspecialchars($animal['location_found']) ?>"
        placeholder="Location Found">
    <label for="date_found">Date Found:</label>
    <input type="date" name="date_found" value="<?= $animal['date_found'] ?>" required>
    <label for="date_returned">Date Returned:</label>
    <input type="date" name="date_returned" value="<?= $animal['date_returned'] ?>">
    <br>
    <button type="submit">Save</button>
</form>
<a href="javascript:history.back()">Cancel</a>