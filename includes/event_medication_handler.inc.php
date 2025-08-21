<?php
function handle_event_medications(PDO $pdo, int $eventId, array $medications) {
    // Remove old links
    $pdo->prepare("DELETE FROM event_medications WHERE event_id = ?")->execute([$eventId]);

    $insertStmt = $pdo->prepare("
        INSERT INTO event_medications (event_id, medication_id, amount_used)
        VALUES (?, ?, ?)
    ");

    $updateStockStmt = $pdo->prepare("
        UPDATE medications SET total_amount = total_amount - ? WHERE id = ?
    ");

    foreach ($medications as $medId => $amount) {
        $amount = trim($amount);
        if ($amount !== '' && $amount > 0) {
            $insertStmt->execute([$eventId, $medId, $amount]);
            $updateStockStmt->execute([$amount, $medId]);
        }
    }
}
