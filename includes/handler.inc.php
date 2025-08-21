<?php

// Enable error reporting for debugging (remove or comment out in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the PDO database connection instance
require_once 'dbh.inc.php';

// Get input values from POST or set defaults if not provided
$table = $_POST['table'] ?? '';          // Target table for action: 'finders', 'animals', or 'events'
$action = $_POST['action'] ?? 'add';     // Action to perform: 'add' (default) or 'edit'
$id = $_POST['id'] ?? null;              // Record ID (for editing)

// Define the allowed fields for each table to prevent unwanted input
$fieldMaps = [
    'finders' => ['lastname', 'firstname', 'phone', 'email', 'street', 'postcode', 'city', 'state', 'notes'],
    'animals' => ['finder_id', 'name', 'species', 'age', 'gender', 'description', 'reason_for_admission', 'location_found', 'date_admission', 'date_release'],
    'events' => ['animal_id', 'event_date', 'event_time', 'weight', 'comments', 'reason_release']
];

// Validate table parameter - if invalid, stop execution with an error
if (!array_key_exists($table, $fieldMaps)) {
    die("Invalid table.");
}

// Prepare an array of field names for the current table
$fields = $fieldMaps[$table];

// Initialize $data array with posted values or null if missing for all expected fields
$data = [];
foreach ($fields as $field) {
    $data[$field] = $_POST[$field] ?? null;
}

// Normalize empty date/time strings to null to avoid SQL errors with invalid dates
$dateFields = ['date_admission', 'date_release', 'event_date', 'event_time'];
foreach ($dateFields as $dateField) {
    if (array_key_exists($dateField, $data) && $data[$dateField] === '') {
        $data[$dateField] = null;
    }
}

try {
    if ($action === 'edit' && $id) {
        // Build SET clause for SQL update, e.g. "field1 = ?, field2 = ?"
        $setClause = implode(', ', array_map(fn($f) => "$f = ?", $fields));
        // Prepare SQL statement with WHERE clause to update by id
        $sql = "UPDATE $table SET $setClause WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        // Execute update with all field values followed by the ID
        $stmt->execute([...array_values($data), $id]);

        if ($table === 'events') {
            $animalId = $data['animal_id'];       // Get related animal ID
            $reason = $data['reason_release'];    // Reason for release value from event
            $eventDate = $data['event_date'];     // Event date to use for date_release

            if (in_array($reason, ['Died', 'Euthanised', 'Released'])) {
                // Set animals.date_release to event_date if release reason indicates final release
                $updateSql = "UPDATE animals SET date_release = ? WHERE id = ?";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([$eventDate, $animalId]);
            } elseif ($reason === 'Readmitted') {
                // Clear animals.date_release if animal was readmitted (return to care)
                $updateSql = "UPDATE animals SET date_release = NULL WHERE id = ?";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([$animalId]);
            }

            // === New: Handle medications for the event on edit ===
            if (isset($_POST['medications']) && is_array($_POST['medications'])) {
                $medications = $_POST['medications'];  // medication_id => amount_used

                // Delete existing meds for this event to simplify syncing
                $deleteStmt = $pdo->prepare("DELETE FROM event_medications WHERE event_id = ?");
                $deleteStmt->execute([$id]);

                // Insert meds where amount_used > 0
                $insertStmt = $pdo->prepare("INSERT INTO event_medications (event_id, medication_id, amount_used) VALUES (?, ?, ?)");
                foreach ($medications as $medId => $amount) {
                    $amount = trim($amount);
                    if ($amount !== '' && $amount > 0) {
                        $insertStmt->execute([$id, $medId, $amount]);
                    }
                }
            }
        }

        // Redirect to main index page after successful edit
        header("Location: ../cases/case_detail.php?animal_id=" . $animalId);
        exit;

    } else {
        // For 'add' action (inserts)

        // Before inserting a new finder, check if a finder with the same firstname, lastname, and phone already exists
        if ($table === 'finders') {
            $checkSql = "SELECT id FROM finders WHERE firstname = ? AND lastname = ? AND phone = ?";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([$data['firstname'], $data['lastname'], $data['phone']]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Redirect immediately to add animal for existing finder (to avoid duplicates)
                header("Location: ../cases/new_case_step2_animal.php?finder_id=" . $existing['id']);
                exit;
            }
        }

        // Insert new record since no duplicate finder found (or not a finder insert)
        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));

        if ($table === 'finders') {
            // Get last inserted ID to pass as finder_id to next step
            $lastId = $pdo->lastInsertId();
            header("Location: ../cases/new_case_step2_animal.php?finder_id=$lastId");

        } else if ($table === 'animals') {
            // Redirect to list of active cases after adding animal
            header('Location: ../cases/list_active.php');

        } else if ($table === 'events') {
            $animalId = $data['animal_id'];
            $reason = $data['reason_release'];
            $eventDate = $data['event_date'];

            if (in_array($reason, ['Died', 'Euthanised', 'Released'])) {
                $updateSql = "UPDATE animals SET date_release = ? WHERE id = ?";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([$eventDate, $animalId]);
            } elseif ($reason === 'Readmitted') {
                $updateSql = "UPDATE animals SET date_release = NULL WHERE id = ?";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([$animalId]);
            }

            // === New: Handle medications for the event on add ===
            if (isset($_POST['medications']) && is_array($_POST['medications'])) {
                $medications = $_POST['medications'];  // medication_id => amount_used
                $currentEventId = $pdo->lastInsertId();

                // Insert meds where amount_used > 0
                $insertStmt = $pdo->prepare("INSERT INTO event_medications (event_id, medication_id, amount_used) VALUES (?, ?, ?)");
                foreach ($medications as $medId => $amount) {
                    $amount = trim($amount);
                    if ($amount !== '' && $amount > 0) {
                        $insertStmt->execute([$currentEventId, $medId, $amount]);
                    }
                }
            }

            // Redirect to animal case detail page after event insertion
            header("Location: ../cases/case_detail.php?animal_id=" . $animalId);

        } else {
            // Fallback redirect to main page for any other case (should not normally happen)
            header('Location: ../index.php');
        }

        exit;
    }

} catch (PDOException $e) {
    // Catch and display any database errors
    die("Database error: " . $e->getMessage());
}
