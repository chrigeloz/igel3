<?php
require 'includes/config.php'; // your PDO $pdo connection
include 'includes/header.inc.php';
?>

<h1>Bug Reports</h1>

<?php
// Fetch all bug reports
$stmt = $pdo->query("SELECT * FROM bug_reports ORDER BY created_at DESC");
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($reports) === 0): ?>
    <p>No bug reports submitted yet.</p>
<?php else: ?>
    <form method="POST" action="includes/bug_report_delete_handler.php" onsubmit="return confirm('Are you sure you want to delete selected reports?');">
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll" /></th>
                    <th>ID</th>
                    <th>Page</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><input type="checkbox" name="delete_ids[]" value="<?= $report['id'] ?>" /></td>
                        <td><?= htmlspecialchars($report['id']) ?></td>
                        <td><?= htmlspecialchars($report['page']) ?></td>
                        <td><?= nl2br(htmlspecialchars($report['message'])) ?></td>
                        <td><?= htmlspecialchars($report['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <button type="submit">🗑️ Delete Selected</button>
    </form>

    <script>
        // Check/uncheck all checkboxes
        document.getElementById('checkAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
<?php endif; ?>

<?php include 'includes/footer.inc.php'; ?>
