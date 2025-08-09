<?php
require_once '../includes/dbh.inc.php';
$stmt = $pdo->query("SELECT * FROM animals WHERE date_returned IS NULL ORDER BY date_found DESC");
$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../styles.css">
<h1>Active Cases</h1>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Animal Name</th>
      <th>Species</th>
      <th>Location Found</th>
      <th>Date Found</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($animals) > 0): ?>
      <?php foreach ($animals as $animal): ?>
        <tr>
          <td><?= htmlspecialchars($animal['id']) ?></td>
          <td>
            <a href="case_detail.php?animal_id=<?= $animal['id'] ?>">
              <?= htmlspecialchars($animal['name']) ?>
            </a>
          </td>
          <td><?= htmlspecialchars($animal['species']) ?></td>
          <td><?= htmlspecialchars($animal['location_found']) ?></td>
          <td><?= htmlspecialchars($animal['date_found']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="5" style="text-align:center;">No active cases found</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<button onclick="window.location.href='../index.php'">← Back to Dashboard</button>