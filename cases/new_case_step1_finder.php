<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
$finders = $pdo->query("SELECT * FROM finders ORDER BY lastname, firstname, phone")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'exists'): ?>
    <p style="color: red;">That finder already exists. Continuing with the existing record.</p>
<?php endif; ?>


<h1>Step 1: Select or Add Finder</h1>
<form method="GET" action="new_case_step2_animal.php">
    <label for "finder_id">Select Existing Finder:</label><br>
        <select name="finder_id">
            <?php foreach ($finders as $f): ?>
                <option value="<?= $f['id'] ?>">
                    <?= htmlspecialchars($f['lastname'] . ', ' . $f['firstname'] . ', ' . $f['phone']) ?>
                </option>
            <?php endforeach; ?>
        </select>
<br>
    <button type="submit">Continue</button>
</form>
<hr>
<h2>Or Add New Finder</h2>
<form method="POST" action="../includes/finder_handler.inc.php">
    <input type="hidden" name="table" value="finders">
    <input type="hidden" name="action" value="add">

    <!-- Finder fields here -->

    <!-- div class="finder-fields"> -->
        <label for="firstname">First name:</label>
        <input type="text" name="firstname" placeholder="First Name" required>

        <label for="lastname">Last name:</label>
        <input type="text" name="lastname" placeholder="Last Name" required>

        <label for="phone">Phone:</label>
        <input type="tel" name="phone" placeholder="Phone" required>

        <label for="email">E-Mail:</label>
        <input type="email" name="email" placeholder="E-Mail">

        <label for="street">Street:</label>
        <input type="text" name="street" placeholder="Street">

        <label for="postcode">Post Code:</label>
        <input type="text" name="postcode" placeholder="Post Code" required>

        <label for="city">City:</label>
        <input type="text" name="city" placeholder="City" required>

        <label for="state">State:</label>
        <input type="text" name="state" placeholder="State">

        <label for="notes">Notes:</label>
        <input type="textarea" name="notes" placeholder="Notes">



    <!-- Additional fields -->
    <br>
    <button type="submit">Add and Continue</button>
</form><br>
<button onclick="window.location.href='../index.php'">← Cancel</button>

<?php include '../includes/footer.inc.php'; ?>