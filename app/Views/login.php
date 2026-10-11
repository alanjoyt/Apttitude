<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In | Apttitudee</title>
  <meta name="description" content="Log in to Apttitudee to track your aptitude practice streak, Speed Coins wallet, and tier progression.">
  <link rel="canonical" href="<?= APP_BASE_URL ?>/index.php?action=login">
  <meta name="robots" content="noindex, follow">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= APP_BASE_URL ?>/index.php?action=login">
  <meta property="og:site_name" content="Apttitudee">
  <meta property="og:title" content="Log In | Apttitudee">
  <meta property="og:description" content="Log in to Apttitudee to track your aptitude practice streak, Speed Coins wallet, and tier progression.">
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
          <h1 class="font-serif fw-bold text-ink fs-4 mb-1">Log In to Your Student Account</h1>
        </a>
        <p class="text-ink-muted small mb-0">
          Continue your practice streak & earn Speed Coins!
        </p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-exam-danger p-2.5 small mb-3 fw-bold rounded-2 d-flex align-items-center gap-2 text-start" role="alert" style="background-color: var(--danger-tint, #fef2f2); color: var(--wrong-red, #dc2626); border: 1.5px solid var(--wrong-red, #dc2626);">
          <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form action="index.php?action=login" method="POST">
        <div class="mb-3 text-start">
          <label class="form-label text-ink-muted small fw-bold" for="loginEmail">Email Address</label>
          <div class="input-clear-wrapper">
            <input type="email" id="loginEmail" name="email" class="form-control pe-4" placeholder="name@example.com" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
            <button type="button" class="input-clear-btn" onclick="document.getElementById('loginEmail').value=''; document.getElementById('loginEmail').focus();" aria-label="Clear email input">&times;</button>
          </div>
        </div>

        <div class="mb-4 text-start">
          <label class="form-label text-ink-muted small fw-bold" for="loginPass">Password</label>
          <div class="input-clear-wrapper">
            <input type="password" id="loginPass" name="password" class="form-control pe-4" placeholder="••••••••" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
            <button type="button" class="input-clear-btn" onclick="document.getElementById('loginPass').value=''; document.getElementById('loginPass').focus();" aria-label="Clear password input">&times;</button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary-gamified w-100 py-3 mb-3 fs-6">
          Log In <i class="bi bi-box-arrow-in-right ms-1"></i>
        </button>
      </form>

      <!-- Registration Redirect Footer -->
      <div class="mt-2 text-ink-muted">
        Don't have an account? <a href="index.php?action=register" class="fw-bold text-decoration-none ms-1" style="color: var(--accent);">Sign Up Free</a>
      </div>
    </div>
  </div>

</body>
</html>
