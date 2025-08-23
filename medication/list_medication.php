<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
// Fetch all medications
$stmt = $pdo->query("SELECT * FROM medications ORDER BY name ASC");
$medications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = $_GET['msg'] ?? '';
?>

    <h1>Medications Inventory</h1>

    <?php if ($msg): ?>
        <p class="message">
            <?php
            switch ($msg) {
                case 'added': echo "Medication added successfully."; break;
                case 'updated': echo "Medication updated successfully."; break;
                case 'deleted': echo "Medication deleted successfully."; break;
                default: echo "";
            }
            ?>
        </p>
    <?php endif; ?>

    
<button onclick="window.location.href='edit_medication.php'">➕ Add New Medication</button>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Unit</th>
                <th>Total Amount</th>
		<th>Expiry</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($medications) === 0): ?>
                <tr><td colspan="4" style="text-align:center;">No medications found.</td></tr>
            <?php else: ?>
                <?php foreach ($medications as $med): ?>
                    <tr>
                        <td><?= htmlspecialchars($med['name']) ?></td>
                        <td><?= htmlspecialchars($med['unit']) ?></td>
                        <td><?= htmlspecialchars($med['total_amount']) ?></td>
			<td><?= htmlspecialchars($med['expiry']) ?></td>
                        <td>
                            <a href="edit_medication.php?id=<?= $med['id'] ?>">Edit</a> |
                            <form action="../includes/medication_handler.inc.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this medication?');">
                                <input type="hidden" name="action" value="delete" />
                                <input type="hidden" name="id" value="<?= $med['id'] ?>" />
                                <button type="submit" style="background:none;border:none;color:red;cursor:pointer;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
    <button onclick="window.location.href='../index.php'">← Back to Dashboard</button>
    

<?php include __DIR__ . '/../includes/footer.inc.php'; ?>
