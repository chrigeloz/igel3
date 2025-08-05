<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/dbh.inc.php';
$finderId = $_GET['finder_id'] ?? null;
?>
<h1>Step 2: Add Animal</h1>
<form method="POST" action="../includes/handler.inc.php">
    <input type="hidden" name="table" value="animals">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="finder_id" value="<?= htmlspecialchars($finderId) ?>">
    <!-- Animal fields -->
    <input name="name" placeholder="Name" >
    <input name="species" placeholder="Species" required>
    <input type="number" name="age" placeholder="Age">
    <select name="gender" placeholder="gender">
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
    </select>
    <input type="text" name="description" placeholder="description" >
    <input name="location_found" placeholder="Location found">
    <input type="date" name="date_found" placeholder="Date found" required>
    <input type="date" name="date_returned" placeholder="Date return">
    <!-- Additional fields -->
    <button type="submit">Add and Continue</button>
</form>
<a href="new_case_step1_finder.php">← Back</a>