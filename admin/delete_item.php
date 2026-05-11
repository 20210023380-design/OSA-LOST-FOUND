<?php
// c:/xampp/htdocs/osa_lost_found/admin/delete_item.php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/session.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE . '/admin/dashboard.php');
    exit();
}

$item_id = (int) ($_POST['item_id'] ?? 0);
if ($item_id > 0) {
    $conn = getConnection();
    $sel = $conn->prepare('SELECT image_path FROM items WHERE item_id = ? LIMIT 1');
    $sel->bind_param('i', $item_id);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    $sel->close();

    $del = $conn->prepare('DELETE FROM items WHERE item_id = ?');
    $del->bind_param('i', $item_id);
    $del->execute();
    $del->close();
    $conn->close();

    if (!empty($row['image_path'])) {
        $rel = $row['image_path'];
        $rel = str_replace(['..', '\\'], '', $rel);
        $fs = dirname(__DIR__) . '/' . ltrim($rel, '/');
        if (is_file($fs)) {
            @unlink($fs);
        }
    }
}

header('Location: ' . APP_BASE . '/admin/dashboard.php');
exit();
