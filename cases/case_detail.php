<?php
require_once '../includes/dbh.inc.php';
require_once '../includes/utils.php';

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
$activeIds = $pdo->query("SELECT id FROM animals WHERE date_returned IS NULL ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

// Find current index and determine previous/next IDs
$currentIndex = array_search($animalId, $activeIds);
$prevId = $activeIds[$currentIndex - 1] ?? null;
$nextId = $activeIds[$currentIndex + 1] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Case Details</title>
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <!-- Back to Active Cases -->

    <h1>Case Details</h1>

    <div>

        <button onclick="window.location.href='list_active.php'">← Back to Active Cases</button>
        <?php if ($prevId): ?>
            <button onclick="window.location.href='case_detail.php?animal_id=<?= $prevId ?>'">&larr; Previous</button>
        <?php endif; ?>
        <?php if ($nextId): ?>
            <button onclick="window.location.href='case_detail.php?animal_id=<?= $nextId ?>'">Next &rarr;</button>
        <?php endif; ?>

    </div>



    <!-- Finder Info -->
    <h2>Finder</h2>

    <!-- Add Edit Finder button -->
    <button onclick="window.location.href='edit_finder.php?id=<?= $finder['id'] ?>'">✏️ Edit Finder</button>
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
            <td><?= htmlspecialchars($finder['street'] . ', ' . $finder['postcode'] . ' ' . $finder['city'] . ', ' . $finder['state']) ?>
            </td>
        </tr>
        <tr>
            <th>Notes</th>
            <td><?= htmlspecialchars($finder['notes']) ?></td>
        </tr>
    </table>

    <!-- Animal Info -->
    <h2>Animal</h2>

    <button onclick="window.location.href='edit_animal.php?id=<?= $animal['id'] ?>'">✏️ Edit Animal</button>
    </p>

    <table>
        <tr>
            <th>ID</th>
            <td><?= htmlspecialchars($animalId) ?></td>
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
            <td><?= htmlspecialchars($animal['description']) ?></td>
        </tr>
        <tr>
            <th>Location Found</th>
            <td><?= htmlspecialchars($animal['location_found']) ?></td>
        </tr>
        <tr>
            <th>Date Found</th>
            <td><?= htmlspecialchars($animal['date_found']) ?></td>
        </tr>
        <tr>
            <th>Date Returned</th>
            <td><?= htmlspecialchars($animal['date_returned']) ?></td>
        </tr>
    </table>

    <!-- Event Info -->

    <h2>Events</h2>

    <!-- Action Buttons -->
    <p>
        <button onclick="window.location.href='add_event.php?animal_id=<?= $animalId ?>'">➕ Add Event</button>
    </p>

    <?php if (count($events)): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Weight (if any)</th>
                    <th>Comments</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['event_date']) ?></td>
                        <td><?= htmlspecialchars($event['event_time']) ?></td>
                        <td><?= htmlspecialchars($event['weight']) ?></td>
                        <td><?= htmlspecialchars($event['comments']) ?></td>
                        <td>
                            <a href="edit_event.php?id=<?= $event['id'] ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No events recorded.</p>
    <?php endif; ?>








</body>

</html>