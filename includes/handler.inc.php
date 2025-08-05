<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'dbh.inc.php';

$table = $_POST['table'] ?? '';
$action = $_POST['action'] ?? 'add';
$id = $_POST['id'] ?? null;

$fieldMaps = [
    'finders' => ['lastname', 'firstname', 'phone', 'email', 'street', 'postcode', 'city', 'state', 'notes'],
    'animals' => ['finder_id', 'name', 'species', 'age', 'gender', 'description', 'location_found', 'date_found', 'date_returned'],
    'events' => ['animal_id', 'event_date', 'event_time', 'weight', 'comments']
];

if (!array_key_exists($table, $fieldMaps)) {
    die("Invalid table.");
}

$fields = $fieldMaps[$table];
$data = [];
foreach ($fields as $field) {
    // Collect posted data or null if missing
    $data[$field] = $_POST[$field] ?? null;
}

// Convert empty date/time strings to null to avoid MySQL errors
$dateFields = ['date_found', 'date_returned', 'event_date', 'event_time'];
foreach ($dateFields as $dateField) {
    if (array_key_exists($dateField, $data) && $data[$dateField] === '') {
        $data[$dateField] = null;
    }
}

try {
    if ($action === 'edit' && $id) {
        // Build update query string with placeholders
        $setClause = implode(', ', array_map(fn($f) => "$f = ?", $fields));
        $sql = "UPDATE $table SET $setClause WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        // Execute update with data values plus id for WHERE clause
        $stmt->execute([...array_values($data), $id]);
        header('Location: ../index.php');
        exit;
    } else {
        // Build insert query string with placeholders
        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));

        // If inserting a finder, redirect with new finder_id to continue workflow
        if ($table === 'finders') {
            $lastId = $pdo->lastInsertId();
            header("Location: ../cases/new_case_step2_animal.php?finder_id=$lastId");
        } else {
            header('Location: ../index.php');
        }

        exit;
    }

} catch (PDOException $e) {
    // Log or display database errors for debugging (adjust for production)
    die("Database error: " . $e->getMessage());
}
