<?php
// c:/xampp/htdocs/osa_lost_found/config/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Only OSA staff keep a session; clear legacy student logins. */
if (isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    $_SESSION = [];
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_BASE . '/login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . APP_BASE . '/login.php');
        exit();
    }
}
