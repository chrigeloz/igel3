<?php
require_once 'dbh.inc.php';
require_once 'event_medication_handler.inc.php';

$table = 'events';
$fields = ['animal_id', 'event_date', 'event_time', 'weight', 'comments', 'reason_release'];

$data = [];
foreach ($fields as $field) {
    $data[$field] = $_POST[$field] ?? null;
    if (in_array($field, ['event_date', 'event_time']) && $data[$field] === '') {
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

        handle_event_medications($pdo, $id, $_POST['medications'] ?? []);

    } else {
        $cols = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $pdo->prepare("INSERT INTO $table ($cols) VALUES ($placeholders)")
            ->execute(array_values($data));

        $eventId = $pdo->lastInsertId();
        handle_event_medications($pdo, $eventId, $_POST['medications'] ?? []);
    }

    // Release date sync
    $animalId = $data['animal_id'];
    if (in_array($data['reason_release'], ['Died', 'Euthanised', 'Released'])) {
        $pdo->prepare("UPDATE animals SET date_release = ? WHERE id = ?")
            ->execute([$data['event_date'], $animalId]);
    } elseif ($data['reason_release'] === 'Readmitted') {
        $pdo->prepare("UPDATE animals SET date_release = NULL WHERE id = ?")
            ->execute([$animalId]);
    }

    header("Location: ../cases/case_detail.php?animal_id=" . $animalId);
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
