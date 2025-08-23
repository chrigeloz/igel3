<?php
function getAnimal($pdo, $id) {
  $stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
  $stmt->execute([$id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getFinder($pdo, $id) {
  $stmt = $pdo->prepare("SELECT * FROM finders WHERE id = ?");
  $stmt->execute([$id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getEvents($pdo, $animalId) {
    // 1. Fetch events
    $stmt = $pdo->prepare("SELECT * FROM events WHERE animal_id = ? ORDER BY event_date DESC, event_time DESC");
    $stmt->execute([$animalId]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$events) return [];

    // 2. Get all event IDs
    $eventIds = array_column($events, 'id');

    // 3. Fetch medications assigned to these events
    $placeholders = implode(',', array_fill(0, count($eventIds), '?'));
    $sql = "
        SELECT em.event_id, m.name, m.unit, em.amount_used
        FROM event_medications em
        JOIN medications m ON em.medication_id = m.id
        WHERE em.event_id IN ($placeholders)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($eventIds);
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Group medications by event_id
    $medsByEvent = [];
    foreach ($medications as $med) {
        $medsByEvent[$med['event_id']][] = [
            'name' => $med['name'],
            'dose' => $med['amount_used'],
            'unit' => $med['unit']
        ];
    }

    // 5. Attach medications to events
    foreach ($events as &$event) {
        $event['medications'] = $medsByEvent[$event['id']] ?? [];
    }

    return $events;
}

?>
