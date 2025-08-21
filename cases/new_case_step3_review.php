<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
$animalId = $_GET['animal_id'] ?? null;
$animal = getAnimal($pdo, $animalId);
$finder = getFinder($pdo, $animal['finder_id']);
$events = getEvents($pdo, $animalId);
?>

<h1>Review Case</h1>
<h2>Finder</h2>
<p><?= htmlspecialchars($finder['firstname'] . ' ' . $finder['lastname']) ?></p>
<h2>Animal</h2>
<p><?= htmlspecialchars($animal['species']) ?></p>
<h2>Events</h2>
<?php if ($events): ?>
  <ul>
    <?php foreach ($events as $e): ?>
      <li><?= htmlspecialchars($e['event_date']) ?> - <?= htmlspecialchars($e['comments']) ?></li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p>No events recorded.</p>
<?php endif; ?>
<a href="add_event.php?animal_id=<?= $animalId ?>">➕ Add Event</a>
<br><a href="list_active.php">← Back to Active Cases</a>

<?php include '../includes/footer.inc.php'; ?>