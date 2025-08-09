<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/dbh.inc.php';
$finderId = $_GET['finder_id'] ?? null;
?>

<link rel="stylesheet" href="../styles.css">

<h1>Step 2: Add Animal</h1>
<form method="POST" action="../includes/handler.inc.php">

    <div class="animal-fields">
        <input type="hidden" name="table" value="animals">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="finder_id" value="<?= htmlspecialchars($finderId) ?>">

        <!-- Animal fields -->
        <label for="name">Name:</label>
        <input type="text" name="name" placeholder="Name">

        <label for="species">Species:</label>
        <input type="text" name="species" placeholder="Species" required>

        <label for="age">Age:</label>
        <input type="number" name="age" placeholder="Age">

        <label for="gender">Gender:</label>
        <select type="text" name="gender" placeholder="gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        <label for="description">Description:</label>
        <input type="text" name="description" placeholder="description">
        <label for="location_found">Location Found:</label>
        <input type="text" name="location_found" placeholder="Location found">
        <label for="date_found">Date Found:</label>
        <input type="date" name="date_found" placeholder="Date found" required>
        <label for="date_returned">Date Returned:</label>
        <input type="date" name="date_returned" placeholder="Date returned">
    </div>

    <!-- Additional fields -->
    <button type="submit">Add and Continue</button>
</form>
<a href="new_case_step1_finder.php">
    <button type="button">← Back</button>
</a>