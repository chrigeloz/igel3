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
  $stmt = $pdo->prepare("SELECT * FROM events WHERE animal_id = ? ORDER BY event_date DESC");
  $stmt->execute([$animalId]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
