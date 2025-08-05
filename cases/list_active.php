<?php
require_once '../includes/dbh.inc.php';
$stmt = $pdo->query("SELECT * FROM animals WHERE date_returned IS NULL ORDER BY date_found DESC");
$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../styles.css">
<h1>Active Cases</h1>
<ul>
  <?php foreach ($animals as $animal): ?>
    <li><a href="case_detail.php?animal_id=<?= $animal['id'] ?>">
      ID: <?= htmlspecialchars($animal['id']) ?> -
    <?= htmlspecialchars($animal['name']) ?> - 
      <?= htmlspecialchars($animal['species']) ?> (
      <?= htmlspecialchars($animal['location_found']) ?>, 
      <?= htmlspecialchars($animal['date_found']) ?>)

      </a>
    </li>
  <?php endforeach; ?>
</ul>
<button onclick="window.location.href='../index.php'">← Back to Dashboard</button>