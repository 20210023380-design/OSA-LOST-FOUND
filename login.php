<?php
// c:/xampp/htdocs/osa_lost_found/login.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

if (isAdmin()) {
    header('Location: ' . APP_BASE . '/admin/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare('SELECT user_id, full_name, role, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();

        if (!$row || !password_verify($password, $row['password_hash'])) {
            $error = 'Invalid email or password.';
        } elseif (($row['role'] ?? '') !== 'admin') {
            $error = 'Only OSA staff may sign in. Students use Report / Claim without logging in.';
        } else {
            $_SESSION['user_id'] = (int) $row['user_id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['full_name'] = $row['full_name'];
            header('Location: ' . APP_BASE . '/admin/dashboard.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSA staff login | XU–OSA Lost & Found</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE . '/assets/style.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body data-page="login">
<?php include __DIR__ . '/partials/header.php'; ?>

<main class="container page-main page-main-narrow">
    <h1 class="page-title">OSA staff login</h1>
    <p class="page-lead">This page is only for administrators. Report Item and Claim Item are open to everyone without an account.</p>

    <form class="form-card" method="post" action="" id="login-form" novalidate autocomplete="on">
        <div class="field">
            <label for="email">Email <span class="req">*</span></label>
            <input class="input" type="email" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <span class="field-error" id="err-email"></span>
        </div>
        <div class="field">
            <label for="password">Password <span class="req">*</span></label>
            <div class="password-row">
                <input class="input" type="password" name="password" id="password" required autocomplete="current-password">
                <button type="button" class="btn btn-ghost" id="toggle-password" aria-label="Show password">Show</button>
            </div>
            <span class="field-error" id="err-password"></span>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-error" id="login-server-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Log in</button>
    </form>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= htmlspecialchars(APP_BASE . '/assets/script.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
