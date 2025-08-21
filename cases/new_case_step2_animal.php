<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
$finderId = $_GET['finder_id'] ?? null;
?>

<h1>Step 2: Add Animal</h1>
<form method="POST" action="../includes/animal_handler.inc.php">

    <div class="animal-fields">
        <input type="hidden" name="table" value="animals">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="finder_id" value="<?= htmlspecialchars($finderId) ?>">

        <!-- Animal fields -->
        <label for="name">Name:</label>
        <input type="text" name="name" placeholder="Name">

        <label for="species">Species:</label>
        <input type="text" name="species" placeholder="Species" value="Igel" required>

        <label for="age">Age:</label>
        <input type="text" name="age" placeholder="Age">

        <label for="gender">Gender:</label>
        <select type="text" name="gender" placeholder="gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>

        <label for="description">Description:</label>
        <input type="text" name="description" placeholder="Description">

        <label for="reason_for_admission">Reason for Admission:</label>
        <input type="text" name="reason_for_admission" placeholder="Reason for Admission">
        
        <label for="location_found">Location Found:</label>
        <input type="text" name="location_found" placeholder="Location found">
        
        <label for="date_admission">Date Admission:</label>
        <input type="date" name="date_admission" value="<?php echo date("Y-m-d"); ?>">
        
        
    </div>

    <!-- Additional fields -->
    <button type="submit">Add and Continue</button>
</form>
<br>
<a href="new_case_step1_finder.php">
    <button type="button">← Back</button>
</a>

<?php include '../includes/footer.inc.php'; ?>
