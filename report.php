<?php
// c:/xampp/htdocs/osa_lost_found/report.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$success = '';
$error = '';

$categories = ['Electronics', 'Bags & Pouches', 'School Supplies', 'Clothing & Accessories', 'Drinkware', 'IDs & Cards', 'Keys', 'Others'];
$colors = ['Black', 'White', 'Gray', 'Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Pink', 'Brown', 'Purple', 'Multi-color', 'Other'];
$locations = ['Magis Building', 'Faber Hall', 'Library', 'Canteen', 'Gymnasium', 'Classroom', 'Hallway', 'Restroom', 'Campus Grounds', 'Others'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location_found = trim($_POST['location_found'] ?? '');
    $location_detail = trim($_POST['location_detail'] ?? '');
    $date_reported = trim($_POST['date_reported'] ?? '');
    $time_found = trim($_POST['time_found'] ?? '');
    $reporter_name = trim($_POST['reporter_name'] ?? '');
    $reporter_email = trim($_POST['reporter_email'] ?? '');

    if ($item_name === '' || $category === '' || $color === '' || $location_found === '' || $date_reported === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!in_array($category, $categories, true) || !in_array($color, $colors, true) || !in_array($location_found, $locations, true)) {
        $error = 'Invalid selection in dropdown fields.';
    } else {
        $locCombined = $location_found;
        if ($location_detail !== '') {
            $locCombined = $location_found . ' — ' . $location_detail;
        }
        if (strlen($locCombined) > 100) {
            $locCombined = substr($locCombined, 0, 100);
        }

        $descParts = [];
        if ($reporter_name !== '' || $reporter_email !== '') {
            $contact = "Reporter contact (optional):\n";
            if ($reporter_name !== '') {
                $contact .= 'Name: ' . $reporter_name . "\n";
            }
            if ($reporter_email !== '') {
                $contact .= 'Email: ' . $reporter_email;
            }
            $descParts[] = trim($contact);
        }
        if ($time_found !== '') {
            $descParts[] = 'Approximate time found: ' . $time_found;
        }
        if ($description !== '') {
            $descParts[] = $description;
        }
        $fullDescription = implode("\n\n", $descParts);

        $image_path = null;
        if (!empty($_FILES['photo']['name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
            $allowed = ['jpg' => true, 'jpeg' => true, 'png' => true, 'gif' => true];
            $orig = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            if (!isset($allowed[$ext])) {
                $error = 'Photo must be JPG, JPEG, PNG, or GIF.';
            } elseif ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
                $error = 'Photo must be 2MB or smaller.';
            } else {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($orig, PATHINFO_FILENAME));
                if ($safeBase === '') {
                    $safeBase = 'item';
                }
                $filename = $safeBase . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destFs = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $destFs)) {
                    $image_path = 'uploads/' . $filename;
                } else {
                    $error = 'Could not save uploaded image.';
                }
            }
        }

        if ($error === '') {
            $conn = getConnection();
            $uid = getPublicGuestUserId($conn);
            $stmt = $conn->prepare(
                'INSERT INTO items (reported_by, item_name, category, description, color, location_found, date_reported, status, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $status = 'found';
            $stmt->bind_param(
                'issssssss',
                $uid,
                $item_name,
                $category,
                $fullDescription,
                $color,
                $locCombined,
                $date_reported,
                $status,
                $image_path
            );
            if ($stmt->execute()) {
                $success = 'Your report was submitted successfully. Please surrender the item physically at the OSA office.';
            } else {
                $error = 'Could not save report. Please try again.';
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
    <title>Report Item | XU–OSA Lost & Found</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE . '/assets/style.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body data-page="report">
<?php include __DIR__ . '/partials/header.php'; ?>

<main class="container page-main">
    <h1 class="page-title">I found something</h1>
    <p class="page-lead">Report a found item. No login required. All required fields must be completed.</p>

    <?php if ($success): ?>
        <div class="alert alert-success" id="report-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <p class="note-osa"><strong>Note:</strong> Please surrender the item physically at the OSA office.</p>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form class="form-card" method="post" action="" enctype="multipart/form-data" id="report-form" novalidate>
        <fieldset class="fieldset-muted">
            <legend>Your contact (optional)</legend>
            <div class="field">
                <label for="reporter_name">Your name</label>
                <input class="input" type="text" name="reporter_name" id="reporter_name" maxlength="100" autocomplete="name">
            </div>
            <div class="field">
                <label for="reporter_email">Your email</label>
                <input class="input" type="email" name="reporter_email" id="reporter_email" maxlength="100" autocomplete="email">
            </div>
        </fieldset>
        <div class="field">
            <label for="item_name">Item name <span class="req">*</span></label>
            <input class="input" type="text" name="item_name" id="item_name" maxlength="100" required>
            <span class="field-error" id="err-item_name"></span>
        </div>
        <div class="field">
            <label for="category">Category <span class="req">*</span></label>
            <select class="input" name="category" id="category" required>
                <option value="">— Select —</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-category"></span>
        </div>
        <div class="field">
            <label for="color">Primary color <span class="req">*</span></label>
            <select class="input" name="color" id="color" required>
                <option value="">— Select —</option>
                <?php foreach ($colors as $c): ?>
                    <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-color"></span>
        </div>
        <div class="field">
            <label for="description">Description / distinguishing features</label>
            <textarea class="input" name="description" id="description" rows="4"></textarea>
            <span class="field-error" id="err-description"></span>
        </div>
        <div class="field">
            <label for="location_found">Location found <span class="req">*</span></label>
            <select class="input" name="location_found" id="location_found" required>
                <option value="">— Select —</option>
                <?php foreach ($locations as $l): ?>
                    <option value="<?= htmlspecialchars($l, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($l, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-location_found"></span>
        </div>
        <div class="field">
            <label for="location_detail">Specific location detail (optional)</label>
            <input class="input" type="text" name="location_detail" id="location_detail" maxlength="200">
        </div>
        <div class="field-row">
            <div class="field">
                <label for="date_reported">Date found <span class="req">*</span></label>
                <input class="input" type="date" name="date_reported" id="date_reported" required>
                <span class="field-error" id="err-date_reported"></span>
            </div>
            <div class="field">
                <label for="time_found">Approximate time found</label>
                <input class="input" type="time" name="time_found" id="time_found">
            </div>
        </div>
        <div class="field">
            <label for="photo">Photo (optional)</label>
            <input class="input" type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif">
            <div id="image-preview-wrap" class="image-preview-wrap hidden">
                <img src="" alt="Preview" id="image-preview" class="image-preview">
            </div>
            <span class="field-error" id="err-photo"></span>
        </div>
        <button type="submit" class="btn btn-primary">Submit report</button>
    </form>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= htmlspecialchars(APP_BASE . '/assets/script.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
