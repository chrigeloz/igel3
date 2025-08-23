<?php
require_once 'dbh.inc.php';

$table = 'animals';
$fields = ['finder_id', 'name', 'species', 'age', 'gender', 'description',
           'reason_for_admission', 'location_found', 'date_admission', 'date_release'];

$data = [];
foreach ($fields as $field) {
    $data[$field] = $_POST[$field] ?? null;
    if (in_array($field, ['date_admission', 'date_release']) && $data[$field] === '') {
        $data[$field] = null;
    }
}

$action = $_POST['action'] ?? 'add';
$id = $_POST['id'] ?? null;

try {
    if ($action === 'edit' && $id) {
        $setClause = implode(', ', array_map(fn($f) => "$f = ?", $fields));
        $pdo->prepare("UPDATE $table SET $setClause WHERE id = ?")
            ->execute([...array_values($data), $id]);
    } else {
        $cols = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $pdo->prepare("INSERT INTO $table ($cols) VALUES ($placeholders)")
            ->execute(array_values($data));
    }

    header("Location: ../cases/list_active.php");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
