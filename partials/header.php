<?php
// c:/xampp/htdocs/osa_lost_found/partials/header.php
if (!defined('APP_BASE')) {
    require_once dirname(__DIR__) . '/config/db.php';
}
require_once dirname(__DIR__) . '/config/session.php';
?>
<header class="site-header">
    <div class="site-header-topbar">
        <div class="container topbar-inner">
            <span class="topbar-tagline">Office of Student Affairs &mdash; Xavier University Ateneo de Cagayan</span>
            <div class="topbar-socials">
                <a href="#" aria-label="Facebook" class="topbar-social-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="#" aria-label="Twitter" class="topbar-social-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                </a>
                <a href="#" aria-label="YouTube" class="topbar-social-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.96-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white"/></svg>
                </a>
                <a href="#" aria-label="Instagram" class="topbar-social-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                </a>
            </div>
        </div>
    </div>
    <div class="site-header-main">
        <div class="container header-main-inner">
            <!-- Logo -->
            <a href="<?= htmlspecialchars(APP_BASE . '/index.php', ENT_QUOTES, 'UTF-8') ?>" class="site-logo-wrap" aria-label="XU-OSA Lost and Found Home">
                <img src="<?= htmlspecialchars(APP_BASE . '/assets/logo.svg', ENT_QUOTES, 'UTF-8') ?>" alt="Xavier University Logo" class="site-logo-img">
                <div class="site-logo-text">
                    <span class="site-logo-title">XU–OSA</span>
                    <span class="site-logo-sub">Lost &amp; Found Portal</span>
                </div>
            </a>

            <!-- Nav -->
            <nav class="site-nav" aria-label="Main">
                <div class="site-nav-primary">
                    <a href="<?= htmlspecialchars(APP_BASE . '/index.php', ENT_QUOTES, 'UTF-8') ?>" class="nav-link">Public Registry</a>
                    <a href="<?= htmlspecialchars(APP_BASE . '/report.php', ENT_QUOTES, 'UTF-8') ?>" class="nav-link">Report Item</a>
                    <a href="<?= htmlspecialchars(APP_BASE . '/claim.php', ENT_QUOTES, 'UTF-8') ?>" class="nav-link">Claim Item</a>
                </div>
                <div class="site-nav-end">
                    <?php if (isAdmin()): ?>
                        <?php
                        $adminNameRaw = (string) ($_SESSION['full_name'] ?? 'OSA Admin');
                        $adminDisplayName = htmlspecialchars($adminNameRaw, ENT_QUOTES, 'UTF-8');
                        $adminAria = 'Admin dashboard — signed in as ' . $adminNameRaw;
                        ?>
                        <a href="<?= htmlspecialchars(APP_BASE . '/admin/dashboard.php', ENT_QUOTES, 'UTF-8') ?>" class="nav-admin-merge" aria-label="<?= htmlspecialchars($adminAria, ENT_QUOTES, 'UTF-8') ?>">
                            <span class="nav-admin-merge-icon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                            </span>
                            <span class="nav-admin-merge-body">
                                <span class="nav-admin-merge-title">Admin Dashboard</span>
                                <span class="nav-admin-merge-name"><?= $adminDisplayName ?></span>
                            </span>
                        </a>
                        <a href="<?= htmlspecialchars(APP_BASE . '/logout.php', ENT_QUOTES, 'UTF-8') ?>" class="nav-logout-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars(APP_BASE . '/login.php', ENT_QUOTES, 'UTF-8') ?>" class="nav-link nav-link-staff">OSA Staff Login</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</header>
