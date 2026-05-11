<?php
// c:/xampp/htdocs/osa_lost_found/index.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$conn = getConnection();
$sql = "SELECT item_id, item_name, category, color, location_found, date_reported, status FROM items ORDER BY date_reported DESC, item_id DESC";
$result = $conn->query($sql);
$items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Registry | XU–OSA Lost & Found</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE . '/assets/style.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body data-page="registry">
<?php include __DIR__ . '/partials/header.php'; ?>

<main class="container page-main">
    <h1 class="page-title">Public Registry</h1>
    <p class="page-lead">Search reported items by name, category, or location. No login required to view.</p>

    <div class="search-wrap">
        <label for="registry-search" class="sr-only">Search registry</label>
        <input type="search" id="registry-search" class="input input-search" placeholder="Search by item name, category, or location…" autocomplete="off">
    </div>

    <div class="table-responsive">
        <table class="data-table" id="registry-table">
            <thead>
                <tr>
                    <th>Item name</th>
                    <th>Category</th>
                    <th>Color</th>
                    <th>Location found</th>
                    <th>Date reported</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr class="no-results-row"><td colspan="6">No items in the registry yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $row): ?>
                        <tr
                            data-search="<?= htmlspecialchars(strtolower($row['item_name'] . ' ' . ($row['category'] ?? '') . ' ' . ($row['location_found'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <td><?= htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['color'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['location_found'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['date_reported'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?php
                                $st = $row['status'] ?? 'found';
                                if ($st === 'found') {
                                    echo '<span class="badge badge-found">FOUND</span>';
                                } elseif ($st === 'claimed') {
                                    echo '<span class="badge badge-claimed">CLAIMED</span>';
                                } else {
                                    echo '<span class="badge badge-lost">LOST</span>';
                                }
                            ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= htmlspecialchars(APP_BASE . '/assets/script.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
