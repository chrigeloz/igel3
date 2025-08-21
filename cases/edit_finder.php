<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
// edit_finder.php


$finderId = $_GET['id'] ?? 0;
$finder = getFinder($pdo, $finderId);
if (!$finder) {
    die("Finder not found.");
}
?>

<h1>Edit Finder</h1>
<form method="POST" action="../includes/finder_handler.inc.php">
    <input type="hidden" name="table" value="finders">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?= $finder['id'] ?>">
    <label for="firstname">First Name:</label>
    <input type="text" name="firstname" value="<?= htmlspecialchars($finder['firstname']) ?>" placeholder="First Name"
        required>
    <label for="lastname">Last Name:</label>        
    <input type="text" name="lastname" value="<?= htmlspecialchars($finder['lastname']) ?>" placeholder="Last Name"
        required>
    <label for="phone">Phone:</label>
    <input type="tel" name="phone" value="<?= htmlspecialchars($finder['phone']) ?>" placeholder="Phone" required>
    <label for="email">Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($finder['email']) ?>" placeholder="Email" >
    <label for="street">Street:</label>
    <input type="text" name="street" value="<?= htmlspecialchars($finder['street']) ?>" placeholder="Street" >
    <label for="postcode">Postcode:</label>
    <input type="text" name="postcode" value="<?= htmlspecialchars($finder['postcode']) ?>" placeholder="Postcode"
        required>
    <label for="city">City:</label>
    <input type="text" name="city" value="<?= htmlspecialchars($finder['city']) ?>" placeholder="City" >
    <label for="state">State:</label>
    <input type="text" name="state" value="<?= htmlspecialchars($finder['state']) ?>" placeholder="State" >
    <label for="notes">Notes:</label>
    <textarea name="notes" placeholder="Notes"><?= htmlspecialchars($finder['notes']) ?></textarea>
    <br><button type="submit">Save</button>
</form>
<br>
<button onclick="window.location.href='javascript:history.back()'">← Back</button>

<?php include '../includes/footer.inc.php'; ?>