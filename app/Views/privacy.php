<?php require_once __DIR__ . '/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">

            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?action=landing" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
                </ol>
            </nav>

            <div class="card card-gamified p-4 p-md-5 mb-5 shadow-sm">
                <div class="border-bottom pb-4 mb-4">
                    <span class="stamp-badge mb-2"><i class="bi bi-shield-check me-1"></i> Data Transparency & Compliance</span>
                    <h1 class="font-serif fw-bold display-5 text-ink">Privacy Policy</h1>
                    <p class="text-ink-muted mb-0">Last updated: September 30, 2026 | Effective Date: October 1, 2026</p>
                </div>

                <div class="content-body text-ink lh-lg fs-6">
                    <h4 class="font-serif fw-bold text-accent mt-4 mb-3">1. Introduction</h4>
                    <p>
                        Welcome to <strong>Apttitudee Platform</strong> ("we", "our", or "us"). We respect your privacy and are committed to protecting the personal data of our students and placement candidates. This Privacy Policy explains what user data we collect, how it is processed, stored, and protected in compliance with applicable local data protection laws, including the <em>Indian Information Technology Act, 2000</em>, the <em>Digital Personal Data Protection Act (DPDP), 2023</em>, and standard global privacy framework principles (GDPR/CCPA).
                    </p>

                    <h4 class="font-serif fw-bold text-accent mt-4 mb-3">2. Information We Collect</h4>
                    <p>We collect only the essential data required to deliver an adaptive, gamified learning experience:</p>
                    <ul class="mb-4">
                        <li><strong>Account Registration Data:</strong> Username, Email address, and encrypted (hashed) password.</li>
                        <li><strong>Performance & Attempt Metrics:</strong> Practice answers submitted, response times, topic accuracy percentage, mistake classification tags (e.g., concept gap, calculation error), XP earned, and daily streak logs.</li>
                        <li><strong>Technical & Session Data:</strong> IP address, device user-agent string, session tokens, and local cookie preferences.</li>
                        <li><strong>Financial Transaction Records:</strong> Virtual Speed Coins earned or spent within the platform store (no real-world monetary credit card info is stored).</li>
                    </ul>

                    <h4 class="font-serif fw-bold text-accent mt-4 mb-3">3. How We Use Your Data</h4>
                    <p>Your data is strictly utilized for the following learning support purposes:</p>
                    <ul class="mb-4">
                        <li>Powering our Spaced Repetition Engine to queue weak topics for targeted revision.</li>
                        <li>Maintaining your account progress, levels, unlockables, and public leaderboard ranks.</li>
                        <li>Preventing automated bot abuse and securing user accounts against unauthorized access.</li>
                    </ul>
                    <div class="alert alert-info border rounded-3 p-3 my-3">
                        <i class="bi bi-info-circle-fill me-2"></i> <strong>Zero Data Monetization Guarantee:</strong> We do NOT sell, rent, or trade user email addresses or personal metrics to third-party advertisers or recruitment agencies.
                    </div>

                    <h4 class="font-serif fw-bold text-accent mt-4 mb-3">4. Cookies & Local Storage</h4>
                    <p>
                        We use essential session cookies and browser <code>localStorage</code> to keep you logged in, save your sound preferences, store your cookie consent choice, and preserve trial practice session state. You can manage or clear cookie preferences via your browser settings at any time.
                    </p>

                    <h4 class="font-serif fw-bold text-accent mt-4 mb-3">5. Data Security & Storage</h4>
                    <p>
                        All user passwords are stored using one-way <code>BCRYPT</code> cryptographic hashing algorithms. Our web server enforces strict parameterized SQL statements across all database transactions to guard against unauthorized data extraction.
                    </p>

                    <h4 class="font-serif fw-bold text-accent mt-4 mb-3">6. Contact Us</h4>
                    <p>
                        If you have questions regarding this Privacy Policy or wish to request data deletion, contact our team directly at <a href="mailto:alanjoy103@gmail.com" class="link-tertiary-exam fw-semibold">alanjoy103@gmail.com</a> or <a href="mailto:asknexyra@gmail.com" class="link-tertiary-exam fw-semibold">asknexyra@gmail.com</a>.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
