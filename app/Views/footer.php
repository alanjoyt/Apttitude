</div> <!-- Close container -->

<?php require_once __DIR__ . '/components/cookie_banner.php'; ?>

<?php
$isQuizSession = ($currentAction === 'practice' && (isset($_GET['session']) || isset($_GET['subtopic_id']) || (isset($_GET['mode']) && $_GET['mode'] === 'quiz')))
              || in_array($currentAction, ['mock', 'duel', 'time_attack']);
?>

<?php if (!$isQuizSession): ?>
<!-- ======================================================================
     MOBILE STICKY BOTTOM TAB BAR (< 768px)
     5 Primary Actions: Practice, Tech, Modes, Shop, Profile
     ====================================================================== -->
<nav class="bottom-tab-bar d-md-none" aria-label="Mobile Bottom Navigation">
  <!-- 1. Practice -->
  <a href="index.php?action=practice" class="bottom-tab-item <?= ($currentAction === 'practice') ? 'active' : '' ?>">
    <span class="bottom-tab-icon-chip"><i class="bi bi-pen-fill"></i></span>
    <span>Practice</span>
  </a>

  <!-- 2. Tech -->
  <a href="index.php?action=technical" class="bottom-tab-item <?= $currentAction === 'technical' ? 'active' : '' ?>">
    <span class="bottom-tab-icon-chip"><i class="bi bi-code-slash"></i></span>
    <span>Tech</span>
  </a>

  <!-- 3. Modes (Opens Bottom Sheet) -->
  <?php $isModesPage = in_array($currentAction, ['skill_tree', 'campaign', 'duel', 'time_attack', 'leaderboard', 'trophy_room']); ?>
  <button type="button" class="bottom-tab-item <?= $isModesPage ? 'active' : '' ?>" onclick="openModesBottomSheet()" aria-label="Open Game Modes Sheet">
    <span class="bottom-tab-icon-chip"><i class="bi bi-grid-fill"></i></span>
    <span>Modes</span>
  </button>

  <!-- 4. Shop / Speed Coins Store -->
  <a href="index.php?action=store" class="bottom-tab-item <?= $currentAction === 'store' ? 'active' : '' ?>">
    <span class="bottom-tab-icon-chip"><i class="bi bi-shop"></i></span>
    <span>Shop</span>
  </a>

  <!-- 5. Profile / Account -->
  <?php $isProfilePage = in_array($currentAction, ['profile', 'dashboard', 'login', 'register']); ?>
  <a href="<?= isset($_SESSION['user_id']) ? 'index.php?action=profile' : 'index.php?action=login' ?>" class="bottom-tab-item <?= $isProfilePage ? 'active' : '' ?>">
    <span class="bottom-tab-icon-chip"><i class="bi bi-person-circle"></i></span>
    <span><?= isset($_SESSION['user_id']) ? 'Profile' : 'Log In' ?></span>
  </a>
</nav>
<?php endif; ?>

<footer class="footer mt-auto text-center text-ink-muted fs-7 <?= $isQuizSession ? 'd-none' : '' ?>">
  <div class="container">
    <!-- MOBILE COMPACT COLOPHON STRIP (< 768px, <= 120px height) -->
    <div class="d-md-none footer-mobile-colophon">
      <!-- Row 1: Four text links in ONE row: Home, Privacy, Terms, Contact (44px min touch height) -->
      <nav class="footer-colophon-nav" aria-label="Mobile Footer Links">
        <a href="index.php?action=landing" class="footer-colophon-link">Home</a>
        <span class="footer-colophon-sep" aria-hidden="true">&middot;</span>
        <a href="index.php?action=privacy" class="footer-colophon-link">Privacy</a>
        <span class="footer-colophon-sep" aria-hidden="true">&middot;</span>
        <a href="index.php?action=terms" class="footer-colophon-link">Terms</a>
        <span class="footer-colophon-sep" aria-hidden="true">&middot;</span>
        <button type="button" class="footer-colophon-link btn-contact-trigger" onclick="openContactModal(event)" aria-label="Open Contact Details">Contact</button>
      </nav>

      <!-- Row 2: Developer Credit with Instagram Link -->
      <div class="footer-colophon-dev small text-ink">
        Dev: <a href="https://www.instagram.com/mr.ignite/?utm_source=ig_web_button_share_sheet" target="_blank" rel="noopener noreferrer" class="link-tertiary-exam fw-bold">Alan Joy ~ mr.ignite</a>
      </div>

      <!-- Row 3: Small centered copyright line -->
      <div class="footer-colophon-copy small text-ink-muted">
        &copy; 2026 Apttitudee &middot; by Nexyra Solutions
      </div>
    </div>

    <!-- DESKTOP FULL FOOTER (>= 768px) -->
    <div class="row align-items-center gy-3 d-none d-md-flex py-4">
      <div class="col-md-5 text-md-start">
        <div class="fw-bold text-ink mb-1">
          <i class="bi bi-book-half me-1" style="color: var(--marker-orange);"></i> Apttitudee Placement Prep
        </div>
        <p class="small text-ink-muted mb-0">
          Independent gamified aptitude examination platform. Company patterns (TCS, Infosys, Wipro, Accenture) curated from community recall.
        </p>
      </div>

      <div class="col-md-4 text-center">
        <div class="d-flex justify-content-center gap-3 fw-bold small">
          <a href="index.php?action=landing" class="text-decoration-none text-ink-muted">Home</a>
          <span>&bull;</span>
          <a href="index.php?action=privacy" class="text-decoration-none text-ink-muted">Privacy Policy</a>
          <span>&bull;</span>
          <a href="index.php?action=terms" class="text-decoration-none text-ink-muted">Terms &amp; Disclaimer</a>
          <span>&bull;</span>
          <a href="#" onclick="openContactModal(event)" class="text-decoration-none text-ink-muted">Contact</a>
          <span>&bull;</span>
          <a href="index.php?action=practice" class="text-decoration-none text-ink-muted">Practice Engine</a>
        </div>
        <div class="small text-ink-muted mt-1 footer-keyboard-hint">
          <i class="bi bi-keyboard me-1"></i> Keys: <kbd>A</kbd> <kbd>B</kbd> <kbd>C</kbd> <kbd>D</kbd> pick, <kbd>Enter</kbd> submit
        </div>
      </div>

      <div class="col-md-3 text-md-end">
        <span class="badge rounded-pill px-3 py-1.5 border" style="background-color: var(--surface-bg); color: var(--surface-text); border-color: var(--border-strong) !important; box-shadow: var(--shadow-hard-sm);">
          <i class="bi bi-pen-fill text-warning me-1"></i> <strong>Nexyra Solutions</strong>
        </span>
        <div class="mt-1.5 small">
          <a href="https://www.instagram.com/mr.ignite/?utm_source=ig_web_button_share_sheet" target="_blank" rel="noopener noreferrer" class="text-ink fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
            <i class="bi bi-instagram text-danger"></i> Alan Joy ~ mr.ignite
          </a>
        </div>
        <div class="small text-ink-muted mt-0.5" style="font-size: 0.75rem;">
          <a href="mailto:alanjoy103@gmail.com" class="text-ink-muted text-decoration-none">alanjoy103@gmail.com</a> &bull;
          <a href="mailto:asknexyra@gmail.com" class="text-ink-muted text-decoration-none">asknexyra@gmail.com</a>
        </div>
        <div class="mt-1 d-flex justify-content-md-end align-items-center gap-2">
          <button type="button" class="btn btn-outline-gamified btn-sm py-0.5 px-2" style="font-size: 0.72rem;" onclick="openContactModal(event)">
            <i class="bi bi-chat-dots me-1"></i> Contact Details
          </button>
          <a href="index.php?action=admin" class="text-ink-muted text-decoration-none small"><i class="bi bi-shield-lock me-1"></i> Admin Portal</a>
        </div>
      </div>
    </div>
  </div>
</footer>

<?php require_once __DIR__ . '/components/feature_guide_modal.php'; ?>
<?php require_once __DIR__ . '/components/contact_modal.php'; ?>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Shared Quiz Shell JS Library -->
<script src="<?= asset_url('js/quiz_shell.js') ?>"></script>
<!-- Gamified App JS with Cache Buster -->
<script src="<?= asset_url('js/app.js') ?>"></script>

</body>
</html>


