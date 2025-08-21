<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
// edit_animal.php


$animalId = $_GET['id'] ?? 0;
$animal = getAnimal($pdo, $animalId);
if (!$animal) {
    die("Animal not found.");
}
?>

<h1>Edit Animal</h1>
<form method="POST" action="../includes/animal_handler.inc.php">
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
    <input type="text" name="age" value="<?= htmlspecialchars($animal['age']) ?>" placeholder="Age">
    <label for="gender">Gender:</label>
    <select type="text" name="gender">
        <option value="male" <?= $animal['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
        <option value="female" <?= $animal['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
        <option value="other" <?= $animal['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
    </select>
    <label for="description">Description:</label>
    <input type="text" name="description" value="<?= htmlspecialchars($animal['description']) ?>"
        placeholder="Description">
    <label for="reason_for_admission">Reason for Admission:</label>
    <input type="text" name="reason_for_admission" value="<?= htmlspecialchars($animal['reason_for_admission']) ?>"
        placeholder="Reason for Admission">
    <label for="location_found">Location Found:</label>
    <input type="text" name="location_found" value="<?= htmlspecialchars($animal['location_found']) ?>"
        placeholder="Location Found">
    <label for="date_admission">Date Admission:</label>
    <input type="date" name="date_admission" value="<?= $animal['date_admission'] ?>" required>
    <label for="date_release">Date Release:</label>
    <input type="date" name="date_release" value="<?= $animal['date_release'] ?>" disabled>
    <br>
    <button type="submit">Save</button>
</form>
<br>
<button onclick="window.location.href='javascript:history.back()'">← Back</button>

<?php include '../includes/footer.inc.php'; ?>
