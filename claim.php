<?php
// c:/xampp/htdocs/osa_lost_found/claim.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$success = '';
$error = '';

$categories = ['Electronics', 'Bags & Pouches', 'School Supplies', 'Clothing & Accessories', 'Drinkware', 'IDs & Cards', 'Keys', 'Others'];
$colors = ['Black', 'White', 'Gray', 'Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Pink', 'Brown', 'Purple', 'Multi-color', 'Other'];
$locations = ['Magis Building', 'Faber Hall', 'Library', 'Canteen', 'Gymnasium', 'Classroom', 'Hallway', 'Restroom', 'Campus Grounds', 'Others'];
$sizes = ['Small', 'Medium', 'Large', 'Extra Large'];

$foundItems = [];
$conn = getConnection();
$res = $conn->query("SELECT item_id, item_name, category, location_found, date_reported FROM items WHERE status = 'found' ORDER BY date_reported DESC");
if ($res) {
    $foundItems = $res->fetch_all(MYSQLI_ASSOC);
}
$conn->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int) ($_POST['item_id'] ?? 0);
    $claim_category = trim($_POST['claim_category'] ?? '');
    $claim_color = trim($_POST['claim_color'] ?? '');
    $item_brand = trim($_POST['item_brand'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $last_location = trim($_POST['last_location'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $date_lost = trim($_POST['date_lost'] ?? '');
    $time_lost = trim($_POST['time_lost'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $claimant_name = trim($_POST['claimant_name'] ?? '');
    $claimant_email = trim($_POST['claimant_email'] ?? '');
    $claimant_student_id = trim($_POST['claimant_student_id'] ?? '');

    if ($item_id <= 0) {
        $error = 'Please select the found item from the registry that you are claiming.';
    } elseif ($claimant_name === '' || $claimant_email === '') {
        $error = 'Please enter your full name and school email so OSA can contact you.';
    } elseif (!filter_var($claimant_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($claim_category === '' || $claim_color === '' || $item_brand === '' || $last_location === '' || $size === '' || $date_lost === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!in_array($claim_category, $categories, true) || !in_array($claim_color, $colors, true) || !in_array($last_location, $locations, true) || !in_array($size, $sizes, true)) {
        $error = 'Invalid selection in dropdown fields.';
    } else {
        $conn = getConnection();
        $chk = $conn->prepare('SELECT item_id FROM items WHERE item_id = ? AND status = ? LIMIT 1');
        $stFound = 'found';
        $chk->bind_param('is', $item_id, $stFound);
        $chk->execute();
        $exists = $chk->get_result()->fetch_assoc();
        $chk->close();
        if (!$exists) {
            $error = 'That item is no longer available for claims.';
            $conn->close();
        } else {
            $lines = [
                'Claimant contact:',
                'Full name: ' . $claimant_name,
                'Email: ' . $claimant_email,
            ];
            if ($claimant_student_id !== '') {
                $lines[] = 'Student ID: ' . $claimant_student_id;
            }
            $lines[] = '';
            $lines = array_merge($lines, [
                'Category (as you describe it): ' . $claim_category,
                'Primary color: ' . $claim_color,
                'Item name & brand/model: ' . $item_brand,
                'Distinguishing features: ' . $features,
                'Last known location: ' . $last_location,
                'Estimated size: ' . $size,
                'Date lost: ' . $date_lost,
            ]);
            if ($time_lost !== '') {
                $lines[] = 'Approximate time lost: ' . $time_lost;
            }
            if ($notes !== '') {
                $lines[] = 'Additional notes: ' . $notes;
            }
            $description_given = implode("\n", $lines);

            $uid = getPublicGuestUserId($conn);
            $stmt = $conn->prepare('INSERT INTO claims (item_id, claimed_by, description_given, status) VALUES (?, ?, ?, ?)');
            $pending = 'pending';
            $stmt->bind_param('iiss', $item_id, $uid, $description_given, $pending);
            if ($stmt->execute()) {
                $success = 'Your claim has been submitted. OSA staff will review and email you. If approved, you will receive an E-Ticket.';
            } else {
                $error = 'Could not submit claim. You may have already submitted a claim for this item.';
            }
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Item | XU–OSA Lost & Found</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE . '/assets/style.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body data-page="claim">
<?php include __DIR__ . '/partials/header.php'; ?>

<main class="container page-main">
    <h1 class="page-title">I lost something</h1>
    <p class="page-lead">Match your loss to an item already reported as found, then provide details so OSA can verify your claim. No login required.</p>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (empty($foundItems) && !$success): ?>
        <div class="alert alert-info">There are no items with status <strong>FOUND</strong> in the registry right now. Check the <a href="<?= htmlspecialchars(APP_BASE . '/index.php', ENT_QUOTES, 'UTF-8') ?>">Public Registry</a> again later.</div>
    <?php endif; ?>

    <form class="form-card" method="post" action="" id="claim-form" novalidate <?= empty($foundItems) ? 'hidden' : '' ?>>
        <fieldset class="fieldset-muted">
            <legend>Your contact <span class="req">*</span></legend>
            <div class="field">
                <label for="claimant_name">Full name <span class="req">*</span></label>
                <input class="input" type="text" name="claimant_name" id="claimant_name" maxlength="100" required autocomplete="name">
                <span class="field-error" id="err-claimant_name"></span>
            </div>
            <div class="field">
                <label for="claimant_email">Email <span class="req">*</span></label>
                <input class="input" type="email" name="claimant_email" id="claimant_email" maxlength="100" required autocomplete="email">
                <span class="field-error" id="err-claimant_email"></span>
            </div>
            <div class="field">
                <label for="claimant_student_id">Student ID (optional)</label>
                <input class="input" type="text" name="claimant_student_id" id="claimant_student_id" maxlength="20" autocomplete="off">
            </div>
        </fieldset>
        <div class="field">
            <label for="item_id">Found item you are claiming <span class="req">*</span></label>
            <select class="input" name="item_id" id="item_id" required>
                <option value="">— Select a found item —</option>
                <?php foreach ($foundItems as $it): ?>
                    <option value="<?= (int) $it['item_id'] ?>">
                        <?= htmlspecialchars('#' . $it['item_id'] . ' — ' . $it['item_name'] . ' (' . ($it['category'] ?? '') . ')', ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-item_id"></span>
        </div>
        <div class="field">
            <label for="claim_category">Item category <span class="req">*</span></label>
            <select class="input" name="claim_category" id="claim_category" required>
                <option value="">— Select —</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-claim_category"></span>
        </div>
        <div class="field">
            <label for="claim_color">Primary color <span class="req">*</span></label>
            <select class="input" name="claim_color" id="claim_color" required>
                <option value="">— Select —</option>
                <?php foreach ($colors as $c): ?>
                    <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-claim_color"></span>
        </div>
        <div class="field">
            <label for="item_brand">Item name &amp; brand/model <span class="req">*</span></label>
            <input class="input" type="text" name="item_brand" id="item_brand" maxlength="200" required>
            <span class="field-error" id="err-item_brand"></span>
        </div>
        <div class="field">
            <label for="features">Distinguishing features</label>
            <textarea class="input" name="features" id="features" rows="4" maxlength="2000" data-counter="counter-features"></textarea>
            <div class="char-counter"><span id="counter-features">0</span> / 2000</div>
            <span class="field-error" id="err-features"></span>
        </div>
        <div class="field">
            <label for="last_location">Last known location <span class="req">*</span></label>
            <select class="input" name="last_location" id="last_location" required>
                <option value="">— Select —</option>
                <?php foreach ($locations as $l): ?>
                    <option value="<?= htmlspecialchars($l, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($l, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-last_location"></span>
        </div>
        <div class="field">
            <label for="size">Estimated size <span class="req">*</span></label>
            <select class="input" name="size" id="size" required>
                <option value="">— Select —</option>
                <?php foreach ($sizes as $s): ?>
                    <option value="<?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-size"></span>
        </div>
        <div class="field-row">
            <div class="field">
                <label for="date_lost">Date lost <span class="req">*</span></label>
                <input class="input" type="date" name="date_lost" id="date_lost" required>
                <span class="field-error" id="err-date_lost"></span>
            </div>
            <div class="field">
                <label for="time_lost">Approximate time lost</label>
                <input class="input" type="time" name="time_lost" id="time_lost">
            </div>
        </div>
        <div class="field">
            <label for="notes">Additional notes</label>
            <textarea class="input" name="notes" id="notes" rows="3" maxlength="2000" data-counter="counter-notes"></textarea>
            <div class="char-counter"><span id="counter-notes">0</span> / 2000</div>
            <span class="field-error" id="err-notes"></span>
        </div>
        <button type="submit" class="btn btn-primary">Submit claim</button>
    </form>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= htmlspecialchars(APP_BASE . '/assets/script.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
