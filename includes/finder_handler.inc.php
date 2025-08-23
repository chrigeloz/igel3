<?php
require_once 'dbh.inc.php';

$table = 'finders';
$fields = ['lastname', 'firstname', 'phone', 'email', 'street', 'postcode', 'city', 'state', 'notes'];

$data = [];
foreach ($fields as $field) {
    $data[$field] = $_POST[$field] ?? null;
}

$action = $_POST['action'] ?? 'add';
$id = $_POST['id'] ?? null;

try {
    if ($action === 'edit' && $id) {
        $setClause = implode(', ', array_map(fn($f) => "$f = ?", $fields));
        $sql = "UPDATE $table SET $setClause WHERE id = ?";
        $pdo->prepare($sql)->execute([...array_values($data), $id]);

    } else {
        // Duplicate check
        $check = $pdo->prepare("SELECT id FROM $table WHERE firstname=? AND lastname=? AND phone=?");
        $check->execute([$data['firstname'], $data['lastname'], $data['phone']]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            header("Location: ../cases/new_case_step2_animal.php?finder_id=" . $existing['id']);
            exit;
        }

        $cols = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $pdo->prepare("INSERT INTO $table ($cols) VALUES ($placeholders)")
            ->execute(array_values($data));

        $id = $pdo->lastInsertId();
        header("Location: ../cases/new_case_step2_animal.php?finder_id=$id");
        exit;
    }

    header("Location: ../cases/list_active.php");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
