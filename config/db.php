<?php
// c:/xampp/htdocs/osa_lost_found/config/db.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'osa_lostfound');
/** Web path to app root (no trailing slash), e.g. /osa_lost_found — change if your folder name differs */
define('APP_BASE', '/osa_lost_found');
/** Internal user row for public report/claim forms (no student login). */
define('PUBLIC_FORM_USER_EMAIL', 'public-form@osa.local');

function lostFoundSchemaIsReady(mysqli $conn): bool {
    foreach (['users', 'items', 'claims'] as $table) {
        $r = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($table) . "'");
        if (!$r || $r->num_rows === 0) {
            return false;
        }
    }
    $e = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
    if (!$e || $e->num_rows === 0) {
        return false;
    }
    $r = $conn->query("SHOW COLUMNS FROM items LIKE 'reported_by'");
    if (!$r || $r->num_rows === 0) {
        return false;
    }
    $c = $conn->query("SHOW COLUMNS FROM claims LIKE 'item_id'");
    return $c && $c->num_rows > 0;
}

function applyLostFoundSchema(mysqli $conn): void {
    $path = dirname(__DIR__) . '/sql/schema.sql';
    if (!is_readable($path)) {
        die('Schema file missing: sql/schema.sql');
    }
    $sql = file_get_contents($path);
    if ($sql === false || trim($sql) === '') {
        die('Could not read sql/schema.sql');
    }
    if (!$conn->multi_query($sql)) {
        die('Could not create tables: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
    }
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->next_result());
}

/**
 * Ensures database and core tables match the OSA Lost & Found spec.
 * If an old `users` table exists without `email`, tables are rebuilt (dev-friendly).
 * Schema file: sql/schema.sql
 */
function ensureDatabaseAndSchema(mysqli $conn): void {
    $db = DB_NAME;
    if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        die('Could not create database: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
    }
    if (!$conn->select_db($db)) {
        die('Could not select database: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
    }

    if (lostFoundSchemaIsReady($conn)) {
        ensurePublicGuestUser($conn);
        return;
    }

    if (!$conn->query('SET FOREIGN_KEY_CHECKS=0')) {
        die('Could not adjust foreign key checks: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
    }
    $conn->query('DROP TABLE IF EXISTS claims');
    $conn->query('DROP TABLE IF EXISTS items');
    $conn->query('DROP TABLE IF EXISTS users');
    if (!$conn->query('SET FOREIGN_KEY_CHECKS=1')) {
        die('Could not restore foreign key checks: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
    }

    applyLostFoundSchema($conn);
    ensurePublicGuestUser($conn);
}

/**
 * One shared `users` row satisfies FKs for anonymous report/claim submissions.
 */
function ensurePublicGuestUser(mysqli $conn): void {
    $email = PUBLIC_FORM_USER_EMAIL;
    $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) {
        return;
    }
    $full = 'Public form (not a login account)';
    $role = 'student';
    $hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
    $ins = $conn->prepare('INSERT INTO users (full_name, email, role, password_hash) VALUES (?, ?, ?, ?)');
    $ins->bind_param('ssss', $full, $email, $role, $hash);
    $ins->execute();
    $ins->close();
}

function getPublicGuestUserId(mysqli $conn): int {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    ensurePublicGuestUser($conn);
    $email = PUBLIC_FORM_USER_EMAIL;
    $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) {
        die('Could not resolve public form user id.');
    }
    $cache = (int) $row['user_id'];
    return $cache;
}

function getConnection(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($conn->connect_error) {
        die('Connection failed: ' . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
    }
    $conn->set_charset('utf8mb4');
    ensureDatabaseAndSchema($conn);
    return $conn;
}
