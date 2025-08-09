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
        $setClause = implode(', ', array_map(fn($f) => "$f = ?", $fields));
        $sql = "UPDATE $table SET $setClause WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([...array_values($data), $id]);
        header('Location: ../index.php');
        exit;
    } else {
        // 🔍 Check for existing finder BEFORE inserting
        if ($table === 'finders') {
            $checkSql = "SELECT id FROM finders WHERE firstname = ? AND lastname = ? AND phone = ?";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([$data['firstname'], $data['lastname'], $data['phone']]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Redirect with existing ID
                header("Location: ../cases/new_case_step2_animal.php?finder_id=" . $existing['id']);
                exit;
            }
        }

        // Proceed with insert if not a duplicate
        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));

        if ($table === 'finders') {
            $lastId = $pdo->lastInsertId();
            header("Location: ../cases/new_case_step2_animal.php?finder_id=$lastId");
        } else if ($table === 'animals') {
            header('Location: ../cases/list_active.php');
        } else if ($table === 'events') {
            header("Location: ../cases/case_detail.php?animal_id=" . $data['animal_id']);

        } else {
            header('Location: ../index.php');
        }

        exit;
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
