-- Create finders table
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

-- Create animals table
CREATE TABLE IF NOT EXISTS animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    finder_id INT,
    name VARCHAR(100),
    species VARCHAR(100) NOT NULL,
    age VARCHAR(50),
    gender ENUM('Male', 'Female', 'Unknown') DEFAULT 'Unknown',
    description TEXT,
    reason_for_admission TEXT,
    location_found VARCHAR(255),
    date_admission DATE,   
    date_release DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (finder_id) REFERENCES finders(id) ON DELETE SET NULL
);

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT,
    event_date DATE,
    event_time TIME,
    weight DECIMAL(5,2),
    comments TEXT,
    reason_release ENUM('', 'Died', 'Euthanised', 'Released', 'Readmitted') DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

CREATE TABLE medications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  unit VARCHAR(50) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0
);

CREATE TABLE event_medications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  medication_id INT NOT NULL,
  amount_used DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (medication_id) REFERENCES medications(id)
);


--ALTER TABLE finders ADD UNIQUE (firstname, lastname, phone);

ALTER TABLE animals ADD case_code VARCHAR(10);

CREATE TABLE IF NOT EXISTS case_code_seq (
    year INT NOT NULL,
    last_number INT NOT NULL DEFAULT 0,
    PRIMARY KEY (year)
);

DELIMITER //
CREATE TRIGGER animals_before_insert
BEFORE INSERT ON animals
FOR EACH ROW
BEGIN
    DECLARE seq INT;
    DECLARE pad_length INT DEFAULT 3; -- Change to your desired default padding length

    -- Ensure a row for the current year exists
    INSERT INTO case_code_seq (year, last_number)
    VALUES (YEAR(CURDATE()), 0)
    ON DUPLICATE KEY UPDATE year = year;

    -- Increment and get sequence
    UPDATE case_code_seq
    SET last_number = last_number + 1
    WHERE year = YEAR(CURDATE());

    SELECT last_number INTO seq
    FROM case_code_seq
    WHERE year = YEAR(CURDATE());

    -- If case_code not manually provided, generate it
    IF NEW.case_code IS NULL OR NEW.case_code = '' THEN
        SET NEW.case_code = CONCAT(
            LPAD(RIGHT(YEAR(CURDATE()), 2), 2, '0'),
            LPAD(seq, pad_length, '0')
        );
    END IF;
END;
//
DELIMITER ;
