<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account | Apttitudee</title>
  <meta name="description" content="Create a free Apttitudee account to unlock adaptive aptitude practice, placement mock tests, and Speed Coins rewards.">
  <link rel="canonical" href="<?= APP_BASE_URL ?>/index.php?action=register">
  <meta name="robots" content="noindex, follow">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= APP_BASE_URL ?>/index.php?action=register">
  <meta property="og:site_name" content="Apttitudee">
  <meta property="og:title" content="Create Account | Apttitudee">
  <meta property="og:description" content="Create a free Apttitudee account to unlock adaptive aptitude practice, placement mock tests, and Speed Coins rewards.">
  <meta name="twitter:card" content="summary_large_image">
  
  <!-- Bootstrap 5 & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
  <link rel="stylesheet" href="<?= asset_url('css/theme.css') ?>">
  <script>
    (function() {
      const saved = localStorage.getItem('apttitudee_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
</head>
<body class="paper-ruled-bg d-flex align-items-center justify-content-center min-vh-100 py-5">

  <div class="container" style="max-width: 440px;">
    <div class="card card-gamified p-4 p-md-5 text-center">
      
      <!-- Brand Logo & Title -->
      <div class="mb-4">
        <a href="index.php?action=landing" class="text-decoration-none">
          <div class="d-inline-flex align-items-center justify-content-center mb-1">
            <i class="bi bi-book-half me-2 fs-2" style="color: var(--accent);"></i>
            <span class="font-serif fw-bold text-ink fs-2">Apttitud<span style="color: var(--accent);">ee</span></span>
          </div>
          <h1 class="font-serif fw-bold text-ink fs-4 mb-1">Create Your Student Account</h1>
        </a>
        <p class="text-ink-muted small mb-0">
          Start practicing aptitude like a game!
        </p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-exam-danger p-2.5 small mb-3 fw-bold rounded-2 d-flex align-items-center gap-2 text-start" role="alert" style="background-color: var(--danger-tint, #fef2f2); color: var(--wrong-red, #dc2626); border: 1.5px solid var(--wrong-red, #dc2626);">
          <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <!-- Registration Form -->
      <form action="index.php?action=register" method="POST">
        <div class="mb-3 text-start">
          <label class="form-label text-ink-muted small fw-bold" for="regName">Full Name</label>
          <div class="input-clear-wrapper">
            <input type="text" id="regName" name="name" class="form-control pe-4" placeholder="Alan Player" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
            <button type="button" class="input-clear-btn" onclick="document.getElementById('regName').value=''; document.getElementById('regName').focus();" aria-label="Clear name input">&times;</button>
          </div>
        </div>

        <div class="mb-3 text-start">
          <label class="form-label text-ink-muted small fw-bold" for="regEmail">Email Address</label>
          <div class="input-clear-wrapper">
            <input type="email" id="regEmail" name="email" class="form-control pe-4" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
            <button type="button" class="input-clear-btn" onclick="document.getElementById('regEmail').value=''; document.getElementById('regEmail').focus();" aria-label="Clear email input">&times;</button>
          </div>
        </div>

        <div class="mb-3 text-start">
          <label class="form-label text-ink-muted small fw-bold" for="regPass">Password</label>
          <div class="input-clear-wrapper">
            <input type="password" id="regPass" name="password" class="form-control pe-4" placeholder="••••••••" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
            <button type="button" class="input-clear-btn" onclick="document.getElementById('regPass').value=''; document.getElementById('regPass').focus();" aria-label="Clear password input">&times;</button>
          </div>
        </div>

        <div class="mb-4 text-start">
          <label class="form-label text-ink-muted small fw-bold" for="regRef">Referral Code (Optional — Earn +25 Bonus Coins!)</label>
          <div class="input-clear-wrapper">
            <input type="text" id="regRef" name="referral_code" class="form-control pe-4" placeholder="APTT-XXXXXX" value="<?= htmlspecialchars($_POST['referral_code'] ?? ($_GET['ref'] ?? '')) ?>" style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
            <button type="button" class="input-clear-btn" onclick="document.getElementById('regRef').value=''; document.getElementById('regRef').focus();" aria-label="Clear referral code input">&times;</button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary-gamified w-100 py-3 mb-3 fs-6">
          Create Free Account <i class="bi bi-person-plus-fill ms-1"></i>
        </button>
      </form>

      <!-- Log In Redirect Footer -->
      <div class="mt-2 text-ink-muted">
        Already have an account? <a href="index.php?action=login" class="fw-bold text-decoration-none ms-1" style="color: var(--accent);">Log In</a>
      </div>
    </div>
  </div>

</body>
</html>
