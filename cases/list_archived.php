<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
// Select animals with date_release not null, and get the latest reason_release from events
$sql = "
    SELECT 
        animals.id,
        animals.case_code,
        animals.name,
        animals.species,
        animals.location_found,
        animals.date_release,
        latest_event.reason_release
    FROM animals
    LEFT JOIN (
        SELECT e.animal_id, e.reason_release
        FROM events e
        INNER JOIN (
            SELECT animal_id, MAX(id) AS latest_event_id
            FROM events
            GROUP BY animal_id
        ) le ON le.animal_id = e.animal_id AND le.latest_event_id = e.id
    ) AS latest_event ON latest_event.animal_id = animals.id
    WHERE animals.date_release IS NOT NULL
    ORDER BY animals.date_release DESC
";

$stmt = $pdo->query($sql);
$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Archived Cases</h1>

<table>
  <thead>
    <tr>
      <th>Case Code</th>
      <th>Animal Name</th>
      <th>Species</th>
      <th>Location Found</th>
      <th>Date of Release</th>
      <th>Reason for Release</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (count($animals) > 0) {
        foreach ($animals as $animal) {
            echo "<tr>";
            echo "<td><a href='case_detail_archived.php?animal_id=" . htmlspecialchars($animal['id']) . "'>" . htmlspecialchars($animal['case_code']) . "</a></td>";
            echo "<td><a href='case_detail_archived.php?animal_id=" . htmlspecialchars($animal['id']) . "'>" . htmlspecialchars($animal['name']) . "</a></td>";
            echo "<td>" . htmlspecialchars($animal['species']) . "</td>";
            echo "<td>" . htmlspecialchars($animal['location_found']) . "</td>";
            echo "<td>" . htmlspecialchars($animal['date_release']) . "</td>";
            echo "<td>" . htmlspecialchars($animal['reason_release']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center;'>No active cases found</td></tr>";
    }
    ?>
  </tbody>
</table>

<button onclick="window.location.href='../index.php'">← Back to Dashboard</button>

<?php include '../includes/footer.inc.php'; ?>
