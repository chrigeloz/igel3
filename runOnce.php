<?php
// Installer for IGEL Wildlife Rescue Database
// Run this once to set up the database and config.php
// Should be deleted after running

$configPath = __DIR__ . '/includes/config.php';

// Stop if config already exists
if (file_exists($configPath) && filesize($configPath) > 0) {
    die("⚠️ Config file already exists at /includes/config.php. Delete it if you want to re-run the installer.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = trim($_POST['db_host']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $db_name = trim($_POST['db_name']);

    if (!$db_name) {
        die("❌ Database name cannot be empty.");
    }

    // Connect to MySQL
    $conn = new mysqli($db_host, $db_user, $db_pass);
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // Create DB
    if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
        die("❌ Error creating database: " . $conn->error);
    }
    echo "✅ Database '$db_name' created or already exists.<br>";

    // Select DB
    $conn->select_db($db_name);

    // Full schema
    $sql = <<<SQL
    SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
    SET time_zone = "+00:00";

    DROP TABLE IF EXISTS animals;
    DROP TABLE IF EXISTS case_code_seq;
    DROP TABLE IF EXISTS events;
    DROP TABLE IF EXISTS event_medications;
    DROP TABLE IF EXISTS finders;
    DROP TABLE IF EXISTS medications;

    CREATE TABLE animals (
      id int(11) NOT NULL AUTO_INCREMENT,
      finder_id int(11) DEFAULT NULL,
      name varchar(100) DEFAULT NULL,
      species varchar(100) NOT NULL,
      age varchar(50) DEFAULT NULL,
      gender enum('Male','Female','Unknown') DEFAULT 'Unknown',
      description text DEFAULT NULL,
      reason_for_admission text DEFAULT NULL,
      location_found varchar(255) DEFAULT NULL,
      date_admission date DEFAULT NULL,
      date_release date DEFAULT NULL,
      created_at timestamp NOT NULL DEFAULT current_timestamp(),
      case_code varchar(10) DEFAULT NULL,
      PRIMARY KEY (id),
      KEY finder_id (finder_id),
      CONSTRAINT animals_ibfk_1 FOREIGN KEY (finder_id) REFERENCES finders (id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE case_code_seq (
      year int(11) NOT NULL,
      last_number int(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (year)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE events (
      id int(11) NOT NULL AUTO_INCREMENT,
      animal_id int(11) DEFAULT NULL,
      event_date date DEFAULT NULL,
      event_time time DEFAULT NULL,
      weight decimal(5,2) DEFAULT NULL,
      comments text DEFAULT NULL,
      created_at timestamp NOT NULL DEFAULT current_timestamp(),
      reason_release enum('','Died','Euthanised','Released','Readmitted') DEFAULT '',
      PRIMARY KEY (id),
      KEY animal_id (animal_id),
      CONSTRAINT events_ibfk_1 FOREIGN KEY (animal_id) REFERENCES animals (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE event_medications (
      id int(11) NOT NULL AUTO_INCREMENT,
      event_id int(11) NOT NULL,
      medication_id int(11) NOT NULL,
      amount_used decimal(10,2) NOT NULL,
      PRIMARY KEY (id),
      KEY event_id (event_id),
      KEY medication_id (medication_id),
      CONSTRAINT event_medications_ibfk_1 FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE,
      CONSTRAINT event_medications_ibfk_2 FOREIGN KEY (medication_id) REFERENCES medications (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE finders (
      id int(11) NOT NULL AUTO_INCREMENT,
      lastname varchar(100) DEFAULT NULL,
      firstname varchar(100) NOT NULL,
      phone varchar(20) DEFAULT NULL,
      email varchar(100) DEFAULT NULL,
      street varchar(255) DEFAULT NULL,
      postcode varchar(20) DEFAULT NULL,
      city varchar(100) DEFAULT NULL,
      state varchar(100) DEFAULT NULL,
      notes text DEFAULT NULL,
      created_at timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (id),
      UNIQUE KEY unique_finder (firstname, lastname, phone)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE medications (
      id int(11) NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      unit varchar(50) NOT NULL,
      total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
      expiry date DEFAULT NULL,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    DELIMITER $$
    CREATE TRIGGER animals_before_insert BEFORE INSERT ON animals FOR EACH ROW BEGIN
        DECLARE seq INT;
        DECLARE pad_length INT DEFAULT 3;
        INSERT INTO case_code_seq (year, last_number)
        VALUES (YEAR(CURDATE()), 0)
        ON DUPLICATE KEY UPDATE year = year;
        UPDATE case_code_seq
        SET last_number = last_number + 1
        WHERE year = YEAR(CURDATE());
        SELECT last_number INTO seq
        FROM case_code_seq
        WHERE year = YEAR(CURDATE());
        IF NEW.case_code IS NULL OR NEW.case_code = '' THEN
            SET NEW.case_code = CONCAT(
                LPAD(RIGHT(YEAR(CURDATE()), 2), 2, '0'),
                LPAD(seq, pad_length, '0')
            );
        END IF;
    END $$
    DELIMITER ;
    SQL;

    if ($conn->multi_query($sql)) {
        do { $conn->store_result(); } while ($conn->next_result());
        echo "✅ Schema installed successfully.<br>";
    } else {
        echo "❌ Error creating schema: " . $conn->error;
    }

    // Write config.php
    $configContent = "<?php\n";
    $configContent .= "// Auto-generated configuration for IGEL database\n";
    $configContent .= "\$db_host = \"" . addslashes($db_host) . "\";\n";
    $configContent .= "\$db_name = \"" . addslashes($db_name) . "\";\n";
    $configContent .= "\$db_user = \"" . addslashes($db_user) . "\";\n";
    $configContent .= "\$db_pass = \"" . addslashes($db_pass) . "\";\n";
    $configContent .= "?>\n";

    if (!is_dir(__DIR__ . '/includes')) {
        mkdir(__DIR__ . '/includes', 0755, true);
    }

    if (file_put_contents($configPath, $configContent) !== false) {
        echo "✅ Config file created at /includes/config.php<br>";
    } else {
        echo "⚠️ Could not write config file. Check permissions.<br>";
    }

    $conn->close();
} else {
    ?>
    <h2>IGEL Database Setup</h2>
    <form method="post">
        <label>MySQL Host:</label>
        <input type="text" name="db_host" value="localhost" required><br><br>
        <label>MySQL Username:</label>
        <input type="text" name="db_user" required><br><br>
        <label>MySQL Password:</label>
        <input type="password" name="db_pass"><br><br>
        <label>Database Name:</label>
        <input type="text" name="db_name" value="igel" required><br><br>
        <button type="submit">Create Database & Save Config</button>
    </form>
    <?php
}
