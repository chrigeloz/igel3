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
<h1>Edit Animal</h1>
<form method="POST" action="../includes/handler.inc.php">
    <input type="hidden" name="table" value="animals">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?= $animal['id'] ?>">
    <input name="name" value="<?= htmlspecialchars($animal['name']) ?>" placeholder="Name">
    <input name="species" value="<?= htmlspecialchars($animal['species']) ?>" placeholder="Species" required>
    <input type="number" name="age" value="<?= htmlspecialchars($animal['age']) ?>" placeholder="Age">
    <select name="gender">
        <option value="male" <?= $animal['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
        <option value="female" <?= $animal['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
        <option value="other" <?= $animal['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
    </select>
    <input type="text" name="description" value="<?= htmlspecialchars($animal['description']) ?>" placeholder="Description">
    <input name="location_found" value="<?= htmlspecialchars($animal['location_found']) ?>" placeholder="Location Found">
    <input type="date" name="date_found" value="<?= $animal['date_found'] ?>" required>
    <input type="date" name="date_returned" value="<?= $animal['date_returned'] ?>">
    <button type="submit">Save</button>
</form>
<a href="javascript:history.back()">Cancel</a>