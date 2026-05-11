-- c:/xampp/htdocs/osa_lost_found/sql/schema.sql
-- Run automatically on first request if tables are missing, or import in phpMyAdmin.

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('student', 'admin') DEFAULT 'student',
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    reported_by INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    color VARCHAR(30),
    location_found VARCHAR(100),
    date_reported DATE NOT NULL,
    status ENUM('found', 'claimed', 'unclaimed') DEFAULT 'found',
    image_path VARCHAR(255) NULL,
    CONSTRAINT fk_items_reported_by FOREIGN KEY (reported_by) REFERENCES users (user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS claims (
    claim_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    claimed_by INT NOT NULL,
    description_given TEXT,
    claimed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    CONSTRAINT fk_claims_item FOREIGN KEY (item_id) REFERENCES items (item_id) ON DELETE CASCADE,
    CONSTRAINT fk_claims_user FOREIGN KEY (claimed_by) REFERENCES users (user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
