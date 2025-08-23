<?php
require_once 'dbh.inc.php';

$table = 'medications';
$fields = ['name', 'unit', 'total_amount', 'expiry'];

$data = [];
foreach ($fields as $field) {
    $data[$field] = $_POST[$field] ?? null;
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

    header("Location: ../medication/list_medication.php");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
