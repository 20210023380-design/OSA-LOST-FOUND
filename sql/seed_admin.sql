-- Run this in phpMyAdmin **after** clicking database `osa_lostfound` on the left
-- (or the USE line below selects it for you).

USE `osa_lostfound`;

INSERT INTO users (full_name, email, role, password_hash)
VALUES (
    'OSA Admin',
    'osa@xu.edu.ph',
    'admin',
    '$2y$12$u5z5YISBrmEoP6VaOrbzXeiRwmi1R5V0DZ5kBK2rGfF5G4wcYFnj6'
);

-- Login password for this row: password123
-- (Stored as bcrypt — login.php uses password_verify(), not plain text.)
