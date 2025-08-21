<?php
// Installer for IGEL Wildlife Rescue Database
// Run this once to set up the database and config.php
// Should be deleted after running

// Path to /includes/config.php (relative to this script)
$configPath = __DIR__ . '/includes/config.php';

// Optional: Stop if config already exists
if (file_exists($configPath) && filesize($configPath) > 0) {
    die("⚠️ Config file already exists at /includes/config.php. Delete it if you want to re-run the installer.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = trim($_POST['db_host']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);

    // Step 1: Connect to MySQL (no DB selected yet)
    $conn = new mysqli($db_host, $db_user, $db_pass);
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // Step 2: Create database if not exists
    if (!$conn->query("CREATE DATABASE IF NOT EXISTS igel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        die("❌ Error creating database: " . $conn->error);
    }
    echo "✅ Database 'igel' created or already exists.<br>";

    // Step 3: Select database
    $conn->select_db("igel");

    // Step 4: Create tables
    $sql = "
    CREATE TABLE IF NOT EXISTS finders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lastname VARCHAR(100),
        firstname VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        email VARCHAR(100),
        street VARCHAR(255),
        postcode VARCHAR(20),
        city VARCHAR(100),
        state VARCHAR(100),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_finder (firstname, lastname, phone)
    );

    CREATE TABLE IF NOT EXISTS animals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        finder_id INT,
        name VARCHAR(100),
        species VARCHAR(100) NOT NULL,
        age VARCHAR(50),
        gender ENUM('Male', 'Female', 'Unknown') DEFAULT 'Unknown',
        description TEXT,
        location_found VARCHAR(255),
        date_found DATE,   
        date_returned DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (finder_id) REFERENCES finders(id) ON DELETE SET NULL
    );

    CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        animal_id INT,
        event_date DATE,
        event_time TIME,
        weight DECIMAL(5,2),
        comments TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
    );
    ";

    if ($conn->multi_query($sql)) {
        do {
            $conn->store_result();
        } while ($conn->next_result());
        echo "✅ Tables created successfully.<br>";
    } else {
        echo "❌ Error creating tables: " . $conn->error;
    }

    // Step 5: Write includes/config.php
    $configContent = "<?php\n";
    $configContent .= "// Auto-generated configuration for IGEL database\n";
    $configContent .= "\$db_host = \"" . addslashes($db_host) . "\";\n";
    $configContent .= "\$db_name = \"igel\";\n";
    $configContent .= "\$db_user = \"" . addslashes($db_user) . "\";\n";
    $configContent .= "\$db_pass = \"" . addslashes($db_pass) . "\";\n";
    $configContent .= "?>\n";

    // Ensure includes folder exists
    if (!is_dir(__DIR__ . '/includes')) {
        mkdir(__DIR__ . '/includes', 0755, true);
    }

    if (file_put_contents($configPath, $configContent) !== false) {
        echo "✅ Config file created at /includes/config.php<br>";
    } else {
        echo "⚠️ Could not write config file. Check folder permissions.<br>";
    }

    $conn->close();
} else {
    // HTML form
    ?>
    <h2>IGEL Database Setup</h2>
    <form method="post">
        <label>MySQL Host:</label>
        <input type="text" name="db_host" value="localhost" required><br><br>
        <label>MySQL Username:</label>
        <input type="text" name="db_user" required><br><br>
        <label>MySQL Password:</label>
        <input type="password" name="db_pass"><br><br>
        <button type="submit">Create Database & Save Config</button>
    </form>
    <?php
}
