<?php
// c:/xampp/htdocs/osa_lost_found/admin/dashboard.php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/session.php';
requireAdmin();

$conn = getConnection();

$sqlClaims = "SELECT c.claim_id, c.description_given, c.claimed_at, c.status AS claim_status,
    u.full_name, u.student_id, i.item_name, i.item_id
    FROM claims c
    INNER JOIN users u ON c.claimed_by = u.user_id
    INNER JOIN items i ON c.item_id = i.item_id
    WHERE c.status = 'pending'
    ORDER BY c.claimed_at ASC";
$claimsRes = $conn->query($sqlClaims);
$pendingClaims = $claimsRes ? $claimsRes->fetch_all(MYSQLI_ASSOC) : [];

$sqlItems = "SELECT it.item_id, it.item_name, it.category, it.location_found, it.date_reported, it.status,
    u.full_name AS reporter_name
    FROM items it
    INNER JOIN users u ON it.reported_by = u.user_id
    ORDER BY it.date_reported DESC, it.item_id DESC";
$itemsRes = $conn->query($sqlItems);
$allItems = $itemsRes ? $itemsRes->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | XU–OSA Lost & Found</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE . '/assets/style.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body data-page="admin-dashboard">
<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<main class="container page-main">
    <h1 class="page-title">Admin Dashboard</h1>
    <p class="page-lead">Review pending claims and manage inventory status.</p>

    <section class="dash-section" id="section-pending-claims">
        <h2 class="section-title">Pending claims</h2>
        <p class="dash-hint">Claims use a shared system user for login purposes. Read <strong>Description given</strong> from the top for the claimant’s real name, email, and optional student ID.</p>
        <div class="table-responsive">
            <table class="data-table" id="claims-table">
                <thead>
                    <tr>
                        <th>Claim ID</th>
                        <th>Linked account</th>
                        <th>Profile student ID</th>
                        <th>Item claimed</th>
                        <th>Description given</th>
                        <th>Date submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingClaims)): ?>
                        <tr><td colspan="8">No pending claims.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pendingClaims as $pc): ?>
                            <tr class="row-pending">
                                <td><?= (int) $pc['claim_id'] ?></td>
                                <td><?= htmlspecialchars($pc['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($pc['student_id'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($pc['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="cell-pre"><?= htmlspecialchars($pc['description_given'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($pc['claimed_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><span class="badge badge-pending">PENDING</span></td>
                                <td class="cell-actions">
                                    <form method="post" action="<?= htmlspecialchars(APP_BASE . '/admin/update_status.php', ENT_QUOTES, 'UTF-8') ?>" class="inline-form">
                                        <input type="hidden" name="action" value="approve_claim">
                                        <input type="hidden" name="claim_id" value="<?= (int) $pc['claim_id'] ?>">
                                        <button type="submit" class="btn btn-admin">Approve</button>
                                    </form>
                                    <form method="post" action="<?= htmlspecialchars(APP_BASE . '/admin/update_status.php', ENT_QUOTES, 'UTF-8') ?>" class="inline-form">
                                        <input type="hidden" name="action" value="reject_claim">
                                        <input type="hidden" name="claim_id" value="<?= (int) $pc['claim_id'] ?>">
                                        <button type="submit" class="btn btn-admin btn-admin-muted">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dash-section">
        <h2 class="section-title">All items</h2>
        <p class="dash-hint">Public reports also use the shared system user; optional reporter contact is stored in the item <strong>description</strong> in the database.</p>
        <div class="table-responsive">
            <table class="data-table" id="items-table">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item name</th>
                        <th>Category</th>
                        <th>Reported by</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allItems)): ?>
                        <tr><td colspan="8">No items.</td></tr>
                    <?php else: ?>
                        <?php foreach ($allItems as $it): ?>
                            <tr>
                                <td><?= (int) $it['item_id'] ?></td>
                                <td><?= htmlspecialchars($it['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($it['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($it['reporter_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($it['location_found'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($it['date_reported'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?php
                                    $st = $it['status'] ?? 'found';
                                    if ($st === 'found') {
                                        echo '<span class="badge badge-found">FOUND</span>';
                                    } elseif ($st === 'claimed') {
                                        echo '<span class="badge badge-claimed">CLAIMED</span>';
                                    } else {
                                        echo '<span class="badge badge-lost">LOST</span>';
                                    }
                                ?></td>
                                <td class="cell-actions">
                                    <form method="post" action="<?= htmlspecialchars(APP_BASE . '/admin/update_status.php', ENT_QUOTES, 'UTF-8') ?>" class="inline-form flex-wrap">
                                        <input type="hidden" name="action" value="update_item_status">
                                        <input type="hidden" name="item_id" value="<?= (int) $it['item_id'] ?>">
                                        <select name="item_status" class="input input-inline" aria-label="Status for item <?= (int) $it['item_id'] ?>">
                                            <option value="found" <?= $st === 'found' ? 'selected' : '' ?>>found</option>
                                            <option value="claimed" <?= $st === 'claimed' ? 'selected' : '' ?>>claimed</option>
                                            <option value="unclaimed" <?= $st === 'unclaimed' ? 'selected' : '' ?>>unclaimed</option>
                                        </select>
                                        <button type="submit" class="btn btn-admin">Update</button>
                                    </form>
                                    <form method="post" action="<?= htmlspecialchars(APP_BASE . '/admin/delete_item.php', ENT_QUOTES, 'UTF-8') ?>" class="inline-form form-delete-item">
                                        <input type="hidden" name="item_id" value="<?= (int) $it['item_id'] ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include dirname(__DIR__) . '/partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= htmlspecialchars(APP_BASE . '/assets/script.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
