<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
$stmt = $pdo->query("SELECT * FROM animals WHERE date_release IS NULL ORDER BY case_code ASC");
$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Active Cases</h1>

<table>
  <thead>
    <tr>
      <th>Case Code</th>
      <th>Animal Name</th>
      <th>Species</th>
      <th>Location Found</th>
      <th>Date of Admission</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($animals) > 0): ?>
      <?php foreach ($animals as $animal): ?>
        
        <tr>
          <td>
          <a href="case_detail.php?animal_id=<?= $animal['id'] ?>">
              <?= htmlspecialchars($animal['case_code']) ?>
            </a>
          </td>
          <td>
            <a href="case_detail.php?animal_id=<?= $animal['id'] ?>">
              <?= htmlspecialchars($animal['name']) ?>
            </a>
          </td>
          <td><?= htmlspecialchars($animal['species']) ?></td>
          <td><?= htmlspecialchars($animal['location_found']) ?></td>
          <td><?= htmlspecialchars($animal['date_admission']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="5" style="text-align:center;">No active cases found</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>


<?php include '../includes/footer.inc.php'; ?>