<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php include __DIR__ . '/../includes/header.inc.php'; ?>

<?php
// Validate and fetch the current animal ID from query string
$animalId = isset($_GET['animal_id']) ? (int) $_GET['animal_id'] : 0;
if ($animalId <= 0) {
    die("Invalid animal ID.");
}

// Fetch full animal, finder, and event info
$animal = getAnimal($pdo, $animalId);
if (!$animal) {
    die("Animal not found.");
}
$finder = getFinder($pdo, $animal['finder_id']);
$events = getEvents($pdo, $animalId);

// Fetch all active case IDs (animals without a return date), ordered by ID
$activeIds = $pdo->query("SELECT id FROM animals WHERE date_release IS NULL ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

// Find current index and determine previous/next IDs
$currentIndex = array_search($animalId, $activeIds);
$prevId = $activeIds[$currentIndex - 1] ?? null;
$nextId = $activeIds[$currentIndex + 1] ?? null;
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
  // hide sections initially
  $(".finderDiv, .animalDiv").hide();
  $(".eventsDiv").show();

  // generic toggle handler
  $("#toggleFinder").click(function() {
    $(".finderDiv").toggle();
    $(this).text($(".finderDiv").is(":visible") ? "Hide" : "Show");
  });

  $("#toggleAnimal").click(function() {
    $(".animalDiv").toggle();
    $(this).text($(".animalDiv").is(":visible") ? "Hide" : "Show");
  });
});
</script>


<!-- Back to Active Cases -->

<h1>Case Details</h1>

<div>

    <button onclick="window.location.href='list_active.php'">Back to Active Cases</button>
    <?php if ($prevId): ?>
        <button onclick="window.location.href='case_detail.php?animal_id=<?= $prevId ?>'">Previous Case</button>
    <?php endif; ?>
    <?php if ($nextId): ?>
        <button onclick="window.location.href='case_detail.php?animal_id=<?= $nextId ?>'">Next Case</button>
    <?php endif; ?>

</div>

<!-- Finder Info -->

<h2><button id="toggleFinder">Show</button>  Finder: <?= htmlspecialchars($finder['lastname'] . ', ' . $finder['firstname'] . ', ' . $finder['phone'])?></h2>

<div class="finderDiv" >

<!-- Add Edit Finder button -->
<p>
    <button onclick="window.location.href='edit_finder.php?id=<?= $finder['id'] ?>'">Edit Finder</button>
</p>

<table>
    <tr>
        <th>Name</th>
        <td><?= htmlspecialchars($finder['lastname'] . ', ' . $finder['firstname']) ?></td>
    </tr>
    <tr>
        <th>Phone</th>
        <td><?= htmlspecialchars($finder['phone']) ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= htmlspecialchars($finder['email']) ?></td>
    </tr>
    <tr>

	<th>Address</th>
<td>
  <?= htmlspecialchars(
        implode(', ', array_filter([
            $finder['street'] ?? '',
            trim(($finder['postcode'] ?? '') . ' ' . ($finder['city'] ?? '')),
            $finder['state'] ?? ''
        ]))
    ) ?>
</td>

    </tr>
    <tr>
        <th>Notes</th>
        <td><?= htmlspecialchars($finder['notes']) ?></td>
    </tr>
</table>

</div>



<!-- Animal Info -->
<h2><button id="toggleAnimal">Show</button>  Animal: <?= htmlspecialchars($animal['case_code']) ?></h2>


<div class="animalDiv">

<p>
    <button onclick="window.location.href='edit_animal.php?id=<?= $animal['id'] ?>'">Edit Animal</button>
</p>

<table>
    <tr hidden>
        <th>ID</th>
        <td><?= htmlspecialchars($animalId) ?></td>
    </tr>
    <tr>
        <th>Case code</th>
        <td><?= htmlspecialchars($animal['case_code']) ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= htmlspecialchars($animal['name']) ?></td>
    </tr>
    <tr>
        <th>Species</th>
        <td><?= htmlspecialchars($animal['species']) ?></td>
    </tr>
    <tr>
        <th>Age</th>
        <td><?= htmlspecialchars($animal['age']) ?></td>
    </tr>
    <tr>
        <th>Gender</th>
        <td><?= htmlspecialchars($animal['gender']) ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?= htmlspecialchars($animal['description'] ?? '') ?></td>
    </tr>

    <tr>
        <th>Reason for Admission</th>
        <td><?= htmlspecialchars($animal['reason_admission'] ?? '') ?></td>
    </tr>
    <tr>
        <th>Location Found</th>
        <td><?= htmlspecialchars($animal['location_found'] ?? '') ?></td>
    </tr>
    <tr>
        <th>Date Admission</th>
        <td><?= htmlspecialchars($animal['date_admission'] ?? '') ?></td>
    </tr>
    <tr>
        <th>Date Released</th>
        <td><?= htmlspecialchars($animal['date_release'] ?? '') ?></td>
    </tr>
</table>

</div>

<!-- Event Info -->

<h2>Events</h2>

<!-- Action Buttons -->
<div class="eventsDiv">
<p>
    <button onclick="window.location.href='add_event.php?animal_id=<?= $animalId ?>'">Add Event</button>
</p>

<?php if (count($events)): ?>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Weight (if any)</th>
                <th>Comments</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                    <td><?= htmlspecialchars($event['event_time']) ?></td>
                    <td><?= htmlspecialchars($event['weight']) ?></td>

                    <td>
                        <?php if (!empty($event['comments'])): ?>
                            <?= nl2br(htmlspecialchars($event['comments'])) ?>
                        <?php else: ?>
                            <em>No comments</em>
                        <?php endif; ?>
                        <br>
                        <?php if (!empty($event['medications'])): ?>
                            <br><strong>Medications:</strong>
                            <ul style="margin:0; padding-left:18px;">
                                <?php foreach ($event['medications'] as $med): ?>
                                    <li class="meds">
                                        <?= htmlspecialchars($med['name']) ?>
                                        <?php if (!empty($med['dose'])): ?>
                                            <?= htmlspecialchars('💊 ' . $med['dose'] . ' ' . $med['unit']) ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                    </td>
                    <td><?= htmlspecialchars($event['reason_release'] ?? '') ?></td>
                    <td><a href="edit_event.php?id=<?= $event['id'] ?>">Edit</a></td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?php else: ?>
    <p>No events recorded.</p>
<?php endif; ?>


<?php include '../includes/footer.inc.php'; ?>