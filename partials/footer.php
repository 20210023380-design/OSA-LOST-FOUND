<?php
// c:/xampp/htdocs/osa_lost_found/partials/footer.php
?>
<footer class="site-footer">
    <div class="footer-main">
        <div class="container footer-main-inner">
            <!-- Brand col -->
            <div class="footer-col footer-col-brand">
                <img src="<?= htmlspecialchars(APP_BASE . '/assets/logo.svg', ENT_QUOTES, 'UTF-8') ?>" alt="Xavier University Logo" class="footer-logo">
                <p class="footer-brand-name">Xavier University<br><span>Ateneo de Cagayan</span></p>
                <p class="footer-tagline">"Men and Women for Others"</p>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook" class="footer-social">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" aria-label="Twitter" class="footer-social">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                    </a>
                    <a href="#" aria-label="YouTube" class="footer-social">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.96-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="footer-social">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h4 class="footer-heading">Portal</h4>
                <ul class="footer-links">
                    <li><a href="<?= htmlspecialchars(APP_BASE . '/index.php', ENT_QUOTES, 'UTF-8') ?>">Public Registry</a></li>
                    <li><a href="<?= htmlspecialchars(APP_BASE . '/report.php', ENT_QUOTES, 'UTF-8') ?>">Report a Found Item</a></li>
                    <li><a href="<?= htmlspecialchars(APP_BASE . '/claim.php', ENT_QUOTES, 'UTF-8') ?>">Claim a Lost Item</a></li>
                    <li><a href="<?= htmlspecialchars(APP_BASE . '/login.php', ENT_QUOTES, 'UTF-8') ?>">OSA Staff Login</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="footer-col">
                <h4 class="footer-heading">Office of Student Affairs</h4>
                <ul class="footer-contact">
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Corrales Avenue, Cagayan de Oro City, Philippines
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.59 3.47 2 2 0 0 1 3.56 1.29h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.37a16 16 0 0 0 6 6l.87-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                        (088) 858-3116 local 2222
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        osa@xu.edu.ph
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Mon–Fri, 8:00 AM – 5:00 PM
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>&copy; <?= date('Y') ?> Xavier University – Ateneo de Cagayan. All rights reserved.</p>
            <p class="footer-bottom-right">Corrales Avenue, Cagayan de Oro City, Philippines</p>
        </div>
    </div>
</footer>
