<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'dbh.inc.php'; // your PDO $pdo connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $page = htmlspecialchars(trim($_POST['page'] ?? 'Unknown page'));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (empty($message)) {
        die("Bug message cannot be empty.");
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO bug_reports (page, message) VALUES (:page, :message)");
    if ($stmt->execute(['page' => $page, 'message' => $message])) {
        echo "<p>✅ Bug report logged successfully!</p>";
    } else {
        echo "<p>❌ Could not log bug report.</p>";
    }
    
    // Back button
    echo '<button onclick="window.history.back()">← Back</button>';

} else {
    die("Invalid request.");
}
