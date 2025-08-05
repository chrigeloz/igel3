<?php
// edit_finder.php
require_once '../includes/dbh.inc.php';
require_once '../includes/utils.php';

$finderId = $_GET['id'] ?? 0;
$finder = getFinder($pdo, $finderId);
if (!$finder) {
    die("Finder not found.");
}
?>
<h1>Edit Finder</h1>
<form method="POST" action="../includes/handler.inc.php">
    <input type="hidden" name="table" value="finders">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?= $finder['id'] ?>">
    <input name="firstname" value="<?= htmlspecialchars($finder['firstname']) ?>" placeholder="First Name" required>
    <input name="lastname" value="<?= htmlspecialchars($finder['lastname']) ?>" placeholder="Last Name" required>
    <input name="phone" value="<?= htmlspecialchars($finder['phone']) ?>" placeholder="Phone" required>
    <input type="email" name="email" value="<?= htmlspecialchars($finder['email']) ?>" placeholder="Email" required>
    <input name="street" value="<?= htmlspecialchars($finder['street']) ?>" placeholder="Street" required>
    <input name="postcode" value="<?= htmlspecialchars($finder['postcode']) ?>" placeholder="Postcode" required>
    <input name="city" value="<?= htmlspecialchars($finder['city']) ?>" placeholder="City" required>
    <input name="state" value="<?= htmlspecialchars($finder['state']) ?>" placeholder="State" required>
    <input name="notes" value="<?= htmlspecialchars($finder['notes']) ?>" placeholder="Notes">
    <button type="submit">Save</button>
</form>
<a href="javascript:history.back()">Cancel</a>