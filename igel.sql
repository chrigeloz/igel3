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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
    location_found VARCHAR(255),
    date_found DATE,   
    date_returned DATE,
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

ALTER TABLE finders ADD UNIQUE (firstname, lastname, phone);