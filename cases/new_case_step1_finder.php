<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/dbh.inc.php';
$finders = $pdo->query("SELECT * FROM finders ORDER BY lastname, firstname, phone")->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="styles.css">
<h1>Step 1: Select or Add Finder</h1>
<form method="GET" action="new_case_step2_animal.php">
    <label>Select Existing Finder:
        <select name="finder_id">
            <?php foreach ($finders as $f): ?>
                <option value="<?= $f['id'] ?>">
                    <?= htmlspecialchars($f['lastname'] . ', ' . $f['firstname'] . ', ' . $f['phone']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit">Continue</button>
</form>
<hr>
<h2>Or Add New Finder</h2>
<form method="POST" action="../includes/handler.inc.php">
    <input type="hidden" name="table" value="finders">
    <input type="hidden" name="action" value="add">
    <!-- Finder fields here -->
    <input name="firstname" placeholder="First Name" required>
    <input name="lastname" placeholder="Last Name" required>
    <input type="tel" name="phone" placeholder="Phone" required>
    <input type="email" name="email" placeholder="E-Mail" >
    <input name="street" placeholder="Street" >
    <input name="postcode" placeholder="Post Code" required>
    <input name="city" placeholder="City" required>
    <input name="state" placeholder="State" >
    <input type="textarea" name="notes" placeholder="Notes" >

    <!-- Additional fields -->
    <button type="submit">Add and Continue</button>
</form>
<a href="../index.php">← Cancel</a>