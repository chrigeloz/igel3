<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/dbh.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_ids'])) {
    $ids = $_POST['delete_ids'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("DELETE FROM bug_reports WHERE id IN ($placeholders)");
    if ($stmt->execute($ids)) {
        echo "<p>✅ Selected bug reports deleted.</p>";
    } else {
        echo "<p>❌ Failed to delete selected reports.</p>";
    }
} else {
    echo "<p>No reports selected.</p>";
}

// Back button
//echo '<button onclick="window.location.href=\'../bug_reports.php\'">← Back</button>';

// Redirect back to bug_reports.php immediately
header('Location: ../bug_reports.php');
exit;
