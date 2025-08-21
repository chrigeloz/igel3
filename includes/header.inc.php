<?php
// === Optional: Display SQL errors in development ===
// Uncomment the following lines only in your development environment
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

// Include DB connection and utilities reliably no matter where this header is included from
require_once __DIR__ . '/../includes/dbh.inc.php';
require_once __DIR__ . '/../includes/utils.php';

?>

<?php
// Example: load user's timezone from DB or session
$user_timezone = "Europe/Zurich";  

// Create DateTime in user's timezone
$dt = new DateTime("now", new DateTimeZone($user_timezone));

// echo $dt->format("H:i");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Igel Station</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <p class="topLine">Allison's Igel Station 🦔</p>
