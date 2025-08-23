<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php

$id = $_GET['id'] ?? null;
$medication = [
    'name' => '',
    'unit' => '',
    'total_amount' => '',
    'expiry' => ''

];
$action = 'add';

if ($id) {
    // Fetch medication for editing
    $stmt = $pdo->prepare("SELECT * FROM medications WHERE id = ?");
    $stmt->execute([$id]);
    $medication = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$medication) {
        die("Medication not found.");
    }
    $action = 'edit';
}
?>


    <h1><?= $action === 'edit' ? 'Edit' : 'Add New' ?> Medication</h1>

    <form action="../includes/medication_handler.inc.php" method="post">
        <input type="hidden" name="action" value="<?= $action ?>" />
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?= $id ?>" />
        <?php endif; ?>

        <label for="name">Medication Name:</label><br />
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($medication['name']) ?>" /><br /><br />

        <label for="unit">Standard Unit (e.g., ml, mg, tablets):</label><br />
        <input type="text" id="unit" name="unit" required value="<?= htmlspecialchars($medication['unit']) ?>" /><br /><br />

        <label for="total_amount">Total Amount Available:</label><br />
        <input type="number" id="total_amount" name="total_amount" min="0" step="0.01" required value="<?= htmlspecialchars($medication['total_amount']) ?>" /><br /><br />

        <label for="total_amount">Expiry:</label><br />
        <input type="date" id="expiry" name="expiry" min="0" step="0.01" required value="<?= htmlspecialchars($medication['expiry']) ?>" /><br /><br />

        <button type="submit"><?= $action === 'edit' ? 'Update' : 'Add' ?> Medication</button>
        <br><br>
        <button onclick="window.location.href='list_medication.php'">Cancel</button>
        
    </form>

<?php include __DIR__ . '/../includes/footer.inc.php'; ?>
