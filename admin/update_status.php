<?php
// c:/xampp/htdocs/osa_lost_found/admin/update_status.php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/session.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE . '/admin/dashboard.php');
    exit();
}

$action = $_POST['action'] ?? '';
$conn = getConnection();

if ($action === 'approve_claim') {
    $claim_id = (int) ($_POST['claim_id'] ?? 0);
    if ($claim_id > 0) {
        $q = $conn->prepare('SELECT item_id FROM claims WHERE claim_id = ? AND status = ? LIMIT 1');
        $pend = 'pending';
        $q->bind_param('is', $claim_id, $pend);
        $q->execute();
        $crow = $q->get_result()->fetch_assoc();
        $q->close();
        if ($crow) {
            $item_id = (int) $crow['item_id'];
            $conn->begin_transaction();
            try {
                $u1 = $conn->prepare("UPDATE claims SET status = 'approved' WHERE claim_id = ? AND status = 'pending'");
                $u1->bind_param('i', $claim_id);
                $u1->execute();
                $u1->close();

                $u2 = $conn->prepare("UPDATE items SET status = 'claimed' WHERE item_id = ?");
                $u2->bind_param('i', $item_id);
                $u2->execute();
                $u2->close();

                $conn->commit();
            } catch (Throwable $e) {
                $conn->rollback();
            }
        }
    }
} elseif ($action === 'reject_claim') {
    $claim_id = (int) ($_POST['claim_id'] ?? 0);
    if ($claim_id > 0) {
        $stmt = $conn->prepare("UPDATE claims SET status = 'rejected' WHERE claim_id = ? AND status = 'pending'");
        $stmt->bind_param('i', $claim_id);
        $stmt->execute();
        $stmt->close();
    }
} elseif ($action === 'update_item_status') {
    $item_id = (int) ($_POST['item_id'] ?? 0);
    $item_status = $_POST['item_status'] ?? '';
    $allowed = ['found' => true, 'claimed' => true, 'unclaimed' => true];
    if ($item_id > 0 && isset($allowed[$item_status])) {
        $stmt = $conn->prepare('UPDATE items SET status = ? WHERE item_id = ?');
        $stmt->bind_param('si', $item_status, $item_id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
header('Location: ' . APP_BASE . '/admin/dashboard.php');
exit();
