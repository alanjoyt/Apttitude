<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
  
  <!-- PWA & Mobile Web App Capabilities -->
  <link rel="manifest" href="manifest.json">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Apttitudee">
  <link rel="apple-touch-icon" href="assets/icons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="192x192" href="assets/icons/icon-192.png">
  
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
  <script>
    window.CSRF_TOKEN = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>';
    (function() {
      const origFetch = window.fetch;
      window.fetch = function(resource, init) {
        init = init || {};
        const method = (init.method || 'GET').toUpperCase();
        if (method !== 'GET' && method !== 'HEAD') {
          init.headers = init.headers || {};
          if (init.headers instanceof Headers) {
            if (!init.headers.has('X-CSRF-Token')) {
              init.headers.set('X-CSRF-Token', window.CSRF_TOKEN);
            }
          } else if (Array.isArray(init.headers)) {
            if (!init.headers.some(h => h[0].toLowerCase() === 'x-csrf-token')) {
              init.headers.push(['X-CSRF-Token', window.CSRF_TOKEN]);
            }
          } else {
            const hasCsrf = Object.keys(init.headers).some(k => k.toLowerCase() === 'x-csrf-token');
            if (!hasCsrf) {
              init.headers['X-CSRF-Token'] = window.CSRF_TOKEN;
            }
          }
        }
        return origFetch(resource, init);
      };
    })();
  </script>
  
  <?php
    $currentAction = $_GET['action'] ?? 'landing';
    $isQuizSession = ($currentAction === 'practice' && (isset($_GET['session']) || isset($_GET['subtopic_id']) || (isset($_GET['mode']) && $_GET['mode'] === 'quiz')))
                  || ($currentAction === 'mock' && isset($_GET['session']) && $_GET['session'] === 'active')
                  || ($currentAction === 'technical' && (isset($_GET['session']) || isset($_GET['start']) || (isset($_GET['mode']) && $_GET['mode'] === 'quiz')));

    // Unique SEO titles (< 60 chars) and meta descriptions (< 160 chars)
    $defaultTitle = "Apttitudee - Gamified Placement Prep Platform";
    $defaultDesc = "Master Quantitative Aptitude, Logical Reasoning, and Verbal Ability with gamified practice and campus placement tests.";

    $canonicalMap = [
        'landing' => APP_BASE_URL . '/index.php?action=landing',
        'practice' => APP_BASE_URL . '/index.php?action=practice',
        'technical' => APP_BASE_URL . '/index.php?action=technical',
        'store' => APP_BASE_URL . '/index.php?action=store',
        'leaderboard' => APP_BASE_URL . '/index.php?action=leaderboard',
        'privacy' => APP_BASE_URL . '/index.php?action=privacy',
        'terms' => APP_BASE_URL . '/index.php?action=terms',
        'skill_tree' => APP_BASE_URL . '/index.php?action=skill_tree',
        'campaign' => APP_BASE_URL . '/index.php?action=campaign',
        'duel' => APP_BASE_URL . '/index.php?action=duel',
        'time_attack' => APP_BASE_URL . '/index.php?action=time_attack',
        'social' => APP_BASE_URL . '/index.php?action=social',
        'login' => APP_BASE_URL . '/index.php?action=login',
        'register' => APP_BASE_URL . '/index.php?action=register',
    ];

    if ($currentAction === 'practice' && !$isQuizSession) {
        $title = "Aptitude Practice & Syllabus | Apttitudee";
        $desc = "Interactive aptitude practice syllabus covering Quantitative Aptitude, Logical Reasoning, Verbal Ability, and placement PYQs.";
        $canonicalUrl = APP_BASE_URL . '/index.php?action=practice';
    } elseif ($currentAction === 'technical' && !$isQuizSession) {
        $title = "Technical Coding & Placement PYQs | Apttitudee";
        $desc = "Master core programming concepts and placement coding questions in C, C++, Java, Python, SQL, and Innovature Labs PYQs.";
        $canonicalUrl = APP_BASE_URL . '/index.php?action=technical';
    } elseif ($currentAction === 'technical' && $isQuizSession) {
        $title = "Technical Practice Session | Apttitudee";
        $desc = "Solve technical coding and aptitude questions with live timer, instant explanation, and placement analysis.";
        $canonicalUrl = APP_BASE_URL . '/index.php?action=technical';
    } else {
        $title = !empty($pageTitle) ? $pageTitle : $defaultTitle;
        $desc = !empty($metaDescription) ? $metaDescription : $defaultDesc;
        $canonicalUrl = $canonicalMap[$currentAction] ?? (APP_BASE_URL . '/index.php?action=' . urlencode($currentAction));
    }

    $isNoIndex = $isQuizSession || in_array($currentAction, ['login', 'register', 'profile', 'dashboard', 'admin']);
  ?>

  <!-- Primary Meta Tags & SEO Optimization -->
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="title" content="<?= htmlspecialchars($title) ?>">
  <meta name="description" content="<?= htmlspecialchars($desc) ?>">
  <meta name="keywords" content="Aptitude Preparation, Quantitative Aptitude, Logical Reasoning, Verbal Ability, Data Interpretation, TCS NQT, Campus Placement Tests">
  <?php if ($isNoIndex): ?>
  <meta name="robots" content="noindex, follow">
  <?php else: ?>
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <?php endif; ?>
  <meta name="author" content="BOLDBYTE">
  <meta name="theme-color" content="#F6F1E7" id="metaThemeColor">
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

  <!-- OpenGraph / Facebook Meta Tags -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
  <meta property="og:site_name" content="Apttitudee">
  <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($desc) ?>">
  <meta property="og:image" content="<?= APP_BASE_URL ?>/assets/og-preview.png">

  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
  <meta name="twitter:title" content="<?= htmlspecialchars($title) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($desc) ?>">
  <meta name="twitter:image" content="<?= APP_BASE_URL ?>/assets/og-preview.png">

  <!-- Preconnect CDN Performance Optimizations -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net">

  <!-- Bootstrap 5 CSS CDN Preload -->
  <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons (Async font stylesheet to unblock initial paint) -->
  <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" media="print" onload="this.media='all'">
  <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"></noscript>

  <!-- Google Fonts: Bitter, Atkinson Hyperlegible, JetBrains Mono (Non-render-blocking swap) -->
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&family=Bitter:wght@700&family=JetBrains+Mono:wght@400;600&display=swap">
  <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&family=Bitter:wght@700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
  <noscript><link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&family=Bitter:wght@700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet"></noscript>
  
  <?php
    $needsChart = in_array($currentAction, ['dashboard', 'profile', 'diagnostic', 'mock']);
    $needsConfetti = in_array($currentAction, ['dashboard', 'practice', 'technical', 'mock', 'diagnostic', 'trophy_room']);
  ?>
  <!-- Canvas Confetti & Chart.js CDNs (Loaded strictly on pages where used, pinned versions) -->
  <?php if ($needsConfetti): ?>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js" crossorigin="anonymous" defer></script>
  <?php endif; ?>
  <?php if ($needsChart): ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous" defer></script>
  <?php endif; ?>

  <!-- Minified Stylesheets with real-time cache busting -->
  <link rel="stylesheet" href="<?= file_exists(__DIR__ . '/../../public/css/style.min.css') ? asset_url('css/style.min.css') : asset_url('css/style.css') ?>">
  <link rel="stylesheet" href="<?= file_exists(__DIR__ . '/../../public/css/theme.min.css') ? asset_url('css/theme.min.css') : asset_url('css/theme.css') ?>">

  <!-- Early Theme & Accessibility Initialization to Prevent FOUC -->
  <script>
    (function() {
      // 1. Single dark mode system ([data-theme] only)
      const savedTheme = localStorage.getItem('apttitudee_theme');
      if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
      } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-theme', 'dark');
      } else {
        document.documentElement.setAttribute('data-theme', 'light');
      }

      // 2. Color-blind palette
      const savedPalette = localStorage.getItem('apttitudee_palette');
      if (savedPalette === 'colorblind') {
        document.documentElement.setAttribute('data-palette', 'colorblind');
      }

      // 3. Font size (Aa)
      const savedFontSize = localStorage.getItem('apttitudee_font_size');
      if (savedFontSize) {
        document.documentElement.setAttribute('data-font-size', savedFontSize);
      }

      // 4. Reduced motion
      const savedMotion = localStorage.getItem('apttitudee_reduced_motion');
      if (savedMotion === 'reduce' || (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
        document.documentElement.setAttribute('data-reduced-motion', 'reduce');
      }
    })();

    // Defer-Safe Execution Helpers for deferred CDN libraries
    function launchConfetti(opts) {
      const run = () => {
        if (typeof confetti === 'function') {
          confetti(opts || { particleCount: 80, spread: 70, origin: { y: 0.6 } });
        }
      };
      if (typeof confetti === 'function') {
        run();
      } else {
        window.addEventListener('load', run, { once: true });
      }
    }
    function onChartReady(fn) {
      const run = () => {
        if (typeof Chart !== 'undefined') {
          fn();
        } else {
          setTimeout(run, 50);
        }
      };
      if (typeof Chart !== 'undefined') {
        fn();
      } else {
        window.addEventListener('load', run, { once: true });
      }
    }
  </script>

  <!-- Schema.org JSON-LD Structured Data Graph (Organization on Home only, clean verified metadata) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "WebSite",
        "@id": "<?= APP_BASE_URL ?>/#website",
        "url": "<?= APP_BASE_URL ?>/",
        "name": "Apttitudee",
        "description": "Gamified Aptitude & Campus Placement Test Preparation Platform"
      }<?php if ($currentAction === 'landing'): ?>,
      {
        "@type": "EducationalOrganization",
        "@id": "<?= APP_BASE_URL ?>/#organization",
        "name": "Apttitudee",
        "url": "<?= APP_BASE_URL ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?= APP_BASE_URL ?>/assets/logo.png"
        }
      }<?php endif; ?>
    ]
  }
  </script>
<?php
require_once __DIR__ . '/components/icons_sprite.php';
require_once __DIR__ . '/components/nexyra_intro.php';

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $currentUser = User::findById($_SESSION['user_id']);
}
?>
</head>
<body class="<?= $isQuizSession ? 'is-quiz quiz-session-body' : 'has-bottom-nav' ?>">

<!-- Global Offline Status Indicator Banner -->
<div id="offlineBanner" role="status" aria-live="polite">
  <i class="bi bi-wifi-off me-1"></i> You are currently offline. Ongoing practice and progress are cached locally.
</div>

<?php if (!$isQuizSession): ?>
<!-- ======================================================================
     MOBILE SLIM TOP BAR (< 768px, Hub & Syllabus Pages)
     ====================================================================== -->
<header class="top-header-exam d-lg-none">
  <div class="container px-3">
    <!-- Brand Logo -->
    <a href="index.php?action=landing" class="brand-exam" aria-label="Apttitudee Home">
      <div class="brand-icon-square">
        <i class="bi bi-book-half"></i>
      </div>
      <span>Apttitud<span class="brand-accent-text">ee</span></span>
    </a>

    <!-- Mobile Top Right Utility Group: Home (if not on landing), Streak, Coins, Dark Mode, Sound -->
    <div class="header-utility-group">
      <?php if ($currentAction !== 'landing'): ?>
        <!-- Quick Home Navigation Button -->
        <a href="index.php?action=landing" class="btn-exam-icon btn-exam-home text-decoration-none" title="Home" aria-label="Go to Home Page">
          <i class="bi bi-house-door-fill text-accent"></i>
        </a>
      <?php endif; ?>

      <?php if ($currentUser): ?>
        <!-- Daily Practice Streak -->
        <div class="streak-pill-exam" title="Current Daily Streak">
          <span class="flame-icon"><svg class="exam-icon text-danger icon-14"><use href="#icon-flame"></use></svg></span>
          <span id="mobileStreakCount"><?= (int)$currentUser['current_streak'] ?></span><small>d</small>
        </div>

        <!-- Speed Coins Brass Token -->
        <a href="index.php?action=store" class="coin-token text-decoration-none" title="Speed Coins Wallet">
          <span class="coin-icon"><svg class="exam-icon text-warning icon-14"><use href="#icon-coin"></use></svg></span>
          <span id="userCoinsDisplayMobile"><?= number_format((int)$currentUser['coins']) ?></span>
        </a>
      <?php else: ?>
        <a href="index.php?action=login" class="btn btn-outline-gamified btn-login-cta btn-sm px-2.5 py-1 text-decoration-none font-sans fw-bold d-inline-flex align-items-center">
          Log In
        </a>
      <?php endif; ?>

      <!-- Accessibility Font Size (Aa) -->
      <button type="button" class="btn-exam-icon fw-bold" onclick="cycleGlobalTextSize()" title="Adjust Text Size (Aa)" aria-label="Adjust Text Size">
        Aa
      </button>

      <!-- Theme Switcher Toggle (Chalk/Paper) -->
      <button type="button" class="btn-exam-icon" onclick="toggleExamTheme()" title="Toggle Dark/Light Mode" aria-label="Toggle Dark/Light Mode">
        <i class="bi bi-circle-half"></i>
      </button>

      <!-- Sound FX Toggle Button -->
      <button id="soundToggleBtnMobile" class="btn-exam-icon btn-sound-toggle" title="Toggle Sound FX" aria-label="Toggle Sound Effects">
        <i class="bi bi-volume-up-fill text-success"></i>
      </button>
    </div>
  </div>
</header>
<?php endif; ?>

<!-- ======================================================================
     DESKTOP NAVIGATION (>= 768px)
     ====================================================================== -->
<nav class="navbar navbar-expand-lg nav-gamified sticky-top py-2 d-none d-lg-block">
  <div class="container-fluid px-3 px-xl-4 nav-max-bound">
    <!-- Brand Logo -->
    <a class="navbar-brand d-flex align-items-center gap-2 me-lg-2 me-xl-3 flex-shrink-0 text-nowrap" href="index.php?action=landing" aria-label="Apttitudee Homepage">
      <div class="brand-icon-box flex-shrink-0">
        <i class="bi bi-book-half fs-4 text-accent"></i>
      </div>
      <div class="d-flex flex-column leading-tight flex-shrink-0 text-nowrap">
        <span class="font-serif fw-bold fs-4 text-ink brand-title-text flex-shrink-0">Apttitud<span class="brand-accent-text">ee</span></span>
      </div>
      <span class="stamp-badge-highlight brand-hall-pill d-none d-xl-inline-block ms-1 flex-shrink-0 text-nowrap">EXAM HALL</span>
    </a>

    <!-- Desktop Menu Container -->
    <div class="collapse navbar-collapse show" id="navMenu">
      <!-- Primary Nav Items Group -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-2 gap-1 align-items-center">
        <!-- Direct Link: Home -->
        <li class="nav-item">
          <a class="nav-link-gamified <?= ($currentAction === 'landing' || empty($currentAction)) ? 'active' : '' ?>" href="index.php?action=landing">
            <i class="bi bi-house-door-fill"></i> <span>Home</span>
          </a>
        </li>

        <!-- Direct Link: Practice -->
        <li class="nav-item">
          <a class="nav-link-gamified <?= $currentAction === 'practice' ? 'active' : '' ?>" href="index.php?action=practice">
            <i class="bi bi-pen-fill"></i> <span>Practice</span>
          </a>
        </li>

        <!-- Direct Link: Technical Assessment -->
        <li class="nav-item">
          <a class="nav-link-gamified <?= $currentAction === 'technical' ? 'active' : '' ?>" href="index.php?action=technical">
            <i class="bi bi-code-slash text-info"></i> <span>Technical</span>
          </a>
        </li>

        <!-- Dropdown 1: Game Modes -->
        <?php $isModeActive = in_array($currentAction, ['skill_tree', 'campaign', 'duel', 'time_attack']); ?>
        <li class="nav-item dropdown">
          <a class="nav-link-gamified dropdown-toggle <?= $isModeActive ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-controller"></i> <span>Game Modes</span>
          </a>
          <ul class="dropdown-menu nav-dropdown-menu shadow-lg border-0 mt-2 p-2">
            <li>
              <a class="dropdown-item dropdown-item-rich rounded p-2 <?= $currentAction === 'skill_tree' ? 'active' : '' ?>" href="index.php?action=skill_tree">
                <div class="dropdown-icon-box bg-accent-tint text-accent"><i class="bi bi-diagram-3-fill"></i></div>
                <div>
                  <div class="dropdown-item-title">Skill Trees</div>
                  <div class="dropdown-item-desc">Structured topic curriculum & mastery levels</div>
                </div>
              </a>
            </li>
            <li>
              <a class="dropdown-item dropdown-item-rich rounded p-2 <?= $currentAction === 'campaign' ? 'active' : '' ?>" href="index.php?action=campaign">
                <div class="dropdown-icon-box bg-highlight-tint text-warning"><i class="bi bi-map-fill"></i></div>
                <div>
                  <div class="dropdown-item-title">Quest Map</div>
                  <div class="dropdown-item-desc">Gamified stage campaign & boss levels</div>
                </div>
              </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
              <a class="dropdown-item dropdown-item-rich rounded p-2 <?= $currentAction === 'duel' ? 'active' : '' ?>" href="index.php?action=duel">
                <div class="dropdown-icon-box bg-danger-tint text-danger"><i class="bi bi-lightning-charge-fill"></i></div>
                <div>
                  <div class="dropdown-item-title">1v1 Duels</div>
                  <div class="dropdown-item-desc">Real-time multiplayer speed battles</div>
                </div>
              </a>
            </li>
            <li>
              <a class="dropdown-item dropdown-item-rich rounded p-2 <?= $currentAction === 'time_attack' ? 'active' : '' ?>" href="index.php?action=time_attack">
                <div class="dropdown-icon-box bg-success-tint text-success"><i class="bi bi-stopwatch-fill"></i></div>
                <div>
                  <div class="dropdown-item-title">Time Attack</div>
                  <div class="dropdown-item-desc">60-second rapid fire sprint challenge</div>
                </div>
              </a>
            </li>
          </ul>
        </li>

        <!-- Dropdown 2: Community & Rewards -->
        <?php $isSocialActive = in_array($currentAction, ['leaderboard', 'store', 'social', 'trophy_room']); ?>
        <li class="nav-item dropdown">
          <a class="nav-link-gamified dropdown-toggle <?= $isSocialActive ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-trophy-fill"></i> <span>Leagues & Shop</span>
          </a>
          <ul class="dropdown-menu nav-dropdown-menu shadow-lg border-0 mt-2 p-2">
            <li>
              <a class="dropdown-item dropdown-item-rich rounded p-2 <?= $currentAction === 'leaderboard' ? 'active' : '' ?>" href="index.php?action=leaderboard">
                <div class="dropdown-icon-box bg-highlight-tint text-warning"><i class="bi bi-award-fill"></i></div>
                <div>
                  <div class="dropdown-item-title">Rankings & Leagues</div>
                  <div class="dropdown-item-desc">Bronze to Diamond tier leaderboards</div>
                </div>
              </a>
            </li>
            <li>
              <a class="dropdown-item dropdown-item-rich rounded p-2 <?= $currentAction === 'store' ? 'active' : '' ?>" href="index.php?action=store">
                <div class="dropdown-icon-box bg-accent-tint text-primary"><i class="bi bi-shop"></i></div>
                <div>
                  <div class="dropdown-item-title">Speed Coins Store</div>
                  <div class="dropdown-item-desc">Unlock mock test packs & streak freezes</div>
                </div>
              </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
              <a class="dropdown-item dropdown-item-rich rounded p-2 <?= $currentAction === 'social' ? 'active' : '' ?>" href="index.php?action=social">
                <div class="dropdown-icon-box bg-success-tint text-success"><i class="bi bi-people-fill"></i></div>
                <div>
                  <div class="dropdown-item-title">Squads & Study Groups</div>
                  <div class="dropdown-item-desc">Join forces with peers & friends</div>
                </div>
              </a>
            </li>
            <li>
              <a class="dropdown-item dropdown-item-rich rounded p-2 <?= $currentAction === 'trophy_room' ? 'active' : '' ?>" href="index.php?action=trophy_room">
                <div class="dropdown-icon-box bg-accent-tint text-secondary"><i class="bi bi-star-fill"></i></div>
                <div>
                  <div class="dropdown-item-title">Trophy Showcase</div>
                  <div class="dropdown-item-desc">Unlocked achievements & badge collection</div>
                </div>
              </a>
            </li>
          </ul>
        </li>
      </ul>

      <!-- Right Utility Actions & User Stat Bar -->
      <div class="d-flex align-items-center gap-1.5 gap-xl-2 ms-auto flex-nowrap flex-shrink-0">
        <!-- Quick Utility Icon Buttons (Theme, Sound, Intro, Guide) -->
        <div class="nav-utility-bar d-flex align-items-center gap-1 p-1 rounded-pill ms-1 flex-shrink-0">
          <button type="button" class="btn btn-utility-icon" onclick="toggleExamTheme()" title="Toggle Dark/Light Mode" aria-label="Toggle Dark/Light Mode">
            <i class="bi bi-circle-half text-warning"></i>
          </button>
          <button id="soundToggleBtn" class="btn btn-utility-icon btn-sound-toggle" title="Toggle Sound FX" aria-label="Toggle Sound Effects">
            <i class="bi bi-volume-up-fill text-success"></i>
          </button>
          <button type="button" class="btn btn-utility-icon" onclick="playBoldbyteIntro()" title="Play Exam Intro" aria-label="Play Exam Intro">
            <i class="bi bi-play-circle-fill text-info"></i>
          </button>
          <button type="button" class="btn btn-utility-icon" data-bs-toggle="modal" data-bs-target="#featureGuideModal" title="How Apttitudee Works Guide" aria-label="Feature Guide">
            <i class="bi bi-compass-fill text-warning"></i>
          </button>
        </div>

        <?php if ($currentUser): ?>
          <!-- Stat Summary Pill (Shown on large desktop >= 1400px to ensure laptop navbars never overflow) -->
          <div class="stat-pill-gamified d-none d-xxl-inline-flex align-items-center gap-2 py-1 px-3 flex-shrink-0" title="Streak, Total XP & Speed Coins">
            <span class="stat-item" title="Current Daily Practice Streak"><i class="bi bi-fire text-danger animate-pulse"></i> <strong><?= $currentUser['current_streak'] ?></strong><small>d</small></span>
            <span class="stat-divider"></span>
            <span class="stat-item" title="Total XP Earned"><i class="bi bi-star-fill text-warning"></i> <strong id="userXPDisplay"><?= number_format($currentUser['total_xp']) ?></strong></span>
            <span class="stat-divider"></span>
            <span class="stat-item" title="Speed Coins Wallet Balance"><i class="bi bi-coin text-warning"></i> <strong id="userCoinsDisplay"><?= number_format($currentUser['coins']) ?></strong></span>
          </div>

          <!-- User Profile Dropdown -->
          <div class="dropdown flex-shrink-0">
            <button class="btn btn-user-profile dropdown-toggle d-flex align-items-center gap-2 py-1 px-2 px-sm-2.5" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="avatar-circle flex-shrink-0">
                <i class="bi <?= !empty($currentUser['avatar_icon']) ? $currentUser['avatar_icon'] : 'bi-person-circle' ?>"></i>
              </div>
              <div class="d-none d-xl-flex flex-column text-start me-1 flex-shrink-0">
                <span class="fw-bold small text-ink leading-tight text-truncate user-name-text"><?= htmlspecialchars($currentUser['name']) ?></span>
                <span class="text-muted extra-small user-tier-label"><?= htmlspecialchars($currentUser['tier']) ?> Tier</span>
              </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 user-dropdown-card" style="right: 0 !important; left: auto !important; max-width: min(340px, calc(100vw - 20px)) !important;">
              <!-- Header Info -->
              <li class="px-3 py-3 rounded mb-2 user-card-header">
                <div class="d-flex align-items-center gap-3">
                  <div class="avatar-circle-lg">
                    <i class="bi <?= !empty($currentUser['avatar_icon']) ? $currentUser['avatar_icon'] : 'bi-person-circle' ?> fs-3"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-ink fs-6 mb-0"><?= htmlspecialchars($currentUser['name']) ?></div>
                    <div class="text-muted extra-small mb-1"><?= htmlspecialchars($currentUser['email']) ?></div>
                    <span class="stamp-badge-highlight extra-small"><?= $currentUser['tier'] ?> League</span>
                  </div>
                </div>
                <!-- Stats row inside dropdown for smaller screens -->
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary border-opacity-10 text-center">
                  <div>
                    <div class="extra-small text-muted">STREAK</div>
                    <div class="fw-bold text-ink"><svg class="exam-icon text-danger me-1 icon-14"><use href="#icon-flame"></use></svg><?= $currentUser['current_streak'] ?>d</div>
                  </div>
                  <div>
                    <div class="extra-small text-muted">TOTAL XP</div>
                    <div class="fw-bold text-ink"><i class="bi bi-star-fill text-warning me-1"></i><?= number_format($currentUser['total_xp']) ?></div>
                  </div>
                  <div>
                    <div class="extra-small text-muted">COINS</div>
                    <div class="fw-bold text-ink"><svg class="exam-icon text-warning me-1 icon-14"><use href="#icon-coin"></use></svg><span id="dropdownUserCoins"><?= number_format($currentUser['coins']) ?></span></div>
                  </div>
                </div>
              </li>

              <li><a class="dropdown-item dropdown-item-rich rounded py-2 small fw-semibold" href="index.php?action=dashboard"><i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard Analytics</a></li>
              <li><a class="dropdown-item dropdown-item-rich rounded py-2 small fw-semibold" href="index.php?action=profile"><i class="bi bi-person-gear me-2 text-secondary"></i> Edit Profile Settings</a></li>
              <li><a class="dropdown-item dropdown-item-rich rounded py-2 small fw-semibold" href="index.php?action=trophy_room"><i class="bi bi-award-fill me-2 text-warning"></i> Trophy Showcase</a></li>
              <li><a class="dropdown-item dropdown-item-rich rounded py-2 small fw-semibold" href="index.php?action=store"><i class="bi bi-shop me-2 text-success"></i> Speed Coins Store</a></li>
              <li><hr class="dropdown-divider my-1"></li>
              <li><a class="dropdown-item dropdown-item-rich rounded py-2 small fw-semibold text-danger" href="index.php?action=logout"><i class="bi bi-box-arrow-right me-2"></i> Log Out</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="index.php?action=login" class="btn btn-outline-gamified btn-sm px-3 py-1 text-nowrap"><i class="bi bi-box-arrow-in-right me-1"></i>Log In</a>
          <a href="index.php?action=register" class="btn btn-primary-gamified btn-sm px-3 py-1 text-nowrap"><i class="bi bi-person-plus-fill me-1"></i>Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Mobile Drawer / Offcanvas Menu -->
<div class="offcanvas offcanvas-start nav-offcanvas" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
  <div class="offcanvas-header border-bottom border-secondary border-opacity-10 py-3">
    <div class="d-flex align-items-center gap-2" id="offcanvasNavbarLabel">
      <div class="brand-icon-box">
        <i class="bi bi-book-half fs-4 text-accent"></i>
      </div>
      <span class="font-serif fw-bold fs-4 text-ink">Apttitud<span class="text-accent">ee</span></span>
    </div>
    <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body p-4 d-flex flex-column justify-content-between">
    <div>
      <?php if ($currentUser): ?>
        <!-- User Info Card in Drawer -->
        <div class="card card-gamified p-3 mb-4 border-accent border-opacity-30 shadow-sm">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="avatar-circle-lg">
              <i class="bi <?= !empty($currentUser['avatar_icon']) ? $currentUser['avatar_icon'] : 'bi-person-circle' ?> fs-3"></i>
            </div>
            <div>
              <div class="fw-bold text-ink fs-6 mb-0"><?= htmlspecialchars($currentUser['name']) ?></div>
              <span class="stamp-badge-highlight extra-small"><?= $currentUser['tier'] ?> Tier</span>
            </div>
          </div>
          <div class="d-flex justify-content-around align-items-center pt-2 border-top text-center fs-7">
            <div><span class="text-muted extra-small">STREAK</span><br><strong><svg class="exam-icon text-danger me-0.5 icon-13"><use href="#icon-flame"></use></svg><?= $currentUser['current_streak'] ?>d</strong></div>
            <div class="vr opacity-25"></div>
            <div><span class="text-muted extra-small">XP</span><br><strong><i class="bi bi-star-fill text-warning me-0.5"></i><?= number_format($currentUser['total_xp']) ?></strong></div>
            <div class="vr opacity-25"></div>
            <div><span class="text-muted extra-small">COINS</span><br><strong id="drawerUserCoins"><svg class="exam-icon text-warning me-0.5 icon-13"><use href="#icon-coin"></use></svg><?= number_format($currentUser['coins']) ?></strong></div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Group 1: Core Practice -->
      <div class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-2">Core Practice</div>
      <div class="list-group list-group-flush mb-4 rounded-3 overflow-hidden border">
        <a href="index.php?action=practice" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'practice' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-pen-fill text-accent fs-5"></i>
          <div>
            <div class="fw-semibold text-ink">Adaptive Practice</div>
            <div class="extra-small text-muted">Solve questions tailored to your level</div>
          </div>
        </a>
        <a href="index.php?action=technical" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'technical' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-code-slash text-info fs-5"></i>
          <div>
            <div class="fw-semibold text-ink">Technical Assessment</div>
            <div class="extra-small text-muted">C, C++, Java, PHP, C# & Innovature PYQs</div>
          </div>
        </a>
        <a href="index.php?action=skill_tree" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'skill_tree' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-diagram-3-fill text-primary fs-5"></i>
          <div>
            <div class="fw-semibold text-ink">Skill Trees</div>
            <div class="extra-small text-muted">Master topic trees step-by-step</div>
          </div>
        </a>
        <a href="index.php?action=campaign" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'campaign' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-map-fill text-warning fs-5"></i>
          <div>
            <div class="fw-semibold text-ink">Quest Map</div>
            <div class="extra-small text-muted">Campaign stages & boss challenges</div>
          </div>
        </a>
      </div>

      <!-- Group 2: Competitive & Speed -->
      <div class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-2">Competitive Modes</div>
      <div class="list-group list-group-flush mb-4 rounded-3 overflow-hidden border">
        <a href="index.php?action=duel" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'duel' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-lightning-charge-fill text-danger fs-5"></i>
          <div>
            <div class="fw-semibold text-ink">1v1 Duels</div>
            <div class="extra-small text-muted">Real-time head-to-head battles</div>
          </div>
        </a>
        <a href="index.php?action=time_attack" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'time_attack' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-stopwatch-fill text-success fs-5"></i>
          <div>
            <div class="fw-semibold text-ink">Time Attack</div>
            <div class="extra-small text-muted">60-second speed sprint</div>
          </div>
        </a>
      </div>

      <!-- Group 3: Social & Economy -->
      <div class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-2">Community & Store</div>
      <div class="list-group list-group-flush mb-4 rounded-3 overflow-hidden border">
        <a href="index.php?action=leaderboard" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'leaderboard' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-award-fill text-warning fs-5"></i>
          <div><div class="fw-semibold text-ink">Rankings & Leagues</div></div>
        </a>
        <a href="index.php?action=store" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'store' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-shop text-success fs-5"></i>
          <div><div class="fw-semibold text-ink">Speed Coins Store</div></div>
        </a>
        <a href="index.php?action=social" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'social' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-people-fill text-primary fs-5"></i>
          <div><div class="fw-semibold text-ink">Squads & Groups</div></div>
        </a>
        <a href="index.php?action=trophy_room" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'trophy_room' ? 'active-drawer-item' : '' ?>">
          <i class="bi bi-star-fill text-secondary fs-5"></i>
          <div><div class="fw-semibold text-ink">Trophy Showcase</div></div>
        </a>
      </div>

      <?php if ($currentUser): ?>
        <!-- Account Links -->
        <div class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-2">Account</div>
        <div class="list-group list-group-flush mb-4 rounded-3 overflow-hidden border">
          <a href="index.php?action=dashboard" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'dashboard' ? 'active-drawer-item' : '' ?>">
            <i class="bi bi-speedometer2 text-primary fs-5"></i>
            <div><div class="fw-semibold text-ink">Dashboard Analytics</div></div>
          </a>
          <a href="index.php?action=profile" class="list-group-item list-group-item-action py-2.5 px-3 d-flex align-items-center gap-3 <?= $currentAction === 'profile' ? 'active-drawer-item' : '' ?>">
            <i class="bi bi-person-gear text-secondary fs-5"></i>
            <div><div class="fw-semibold text-ink">Profile Settings</div></div>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Drawer Footer Actions -->
    <div class="pt-3 border-top border-secondary border-opacity-10 mt-auto">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="small text-muted fw-semibold">Quick Actions:</span>
        <div class="d-flex gap-2">
          <button onclick="playBoldbyteIntro()" class="btn btn-sm btn-outline-gamified px-3 py-1"><i class="bi bi-play-circle-fill text-info me-1"></i>Intro</button>
          <button data-bs-toggle="modal" data-bs-target="#featureGuideModal" class="btn btn-sm btn-outline-gamified px-3 py-1"><i class="bi bi-compass-fill text-warning me-1"></i>Guide</button>
        </div>
      </div>
      <?php if ($currentUser): ?>
        <a href="index.php?action=logout" class="btn btn-outline-danger w-100 rounded-pill py-2 font-sans fw-semibold"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a>
      <?php else: ?>
        <div class="d-grid gap-2">
          <a href="index.php?action=register" class="btn btn-primary-gamified w-100 py-2"><i class="bi bi-person-plus-fill me-1"></i>Create Free Account</a>
          <a href="index.php?action=login" class="btn btn-outline-gamified w-100 py-2"><i class="bi bi-box-arrow-in-right me-1"></i>Log In</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div><!-- /offcanvasNavbar -->

<!-- Mobile Bottom Sheet for Game Modes -->
<div id="modesBottomSheet" class="exam-bottom-sheet" role="dialog" aria-modal="true" aria-labelledby="modesSheetTitle">
  <div class="exam-bottom-sheet-backdrop" onclick="closeModesBottomSheet()"></div>
  <div class="exam-bottom-sheet-content">
    <div class="bottom-sheet-drag-handle"></div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="font-serif fw-bold text-ink mb-0" id="modesSheetTitle">
        <i class="bi bi-controller me-1" style="color: var(--marker-orange);"></i> Examination Modes
      </h5>
      <button type="button" class="btn-close text-reset shadow-none" onclick="closeModesBottomSheet()" aria-label="Close Examination Modes"></button>
    </div>
    
    <div class="row g-2 mb-3">
      <div class="col-6">
        <a href="index.php?action=skill_tree" class="btn btn-outline-gamified w-100 text-start p-2.5 d-flex flex-column gap-1 text-decoration-none">
          <i class="bi bi-diagram-3-fill fs-4" style="color: var(--ink-navy);"></i>
          <span class="fw-bold fs-7">Skill Trees</span>
          <small class="text-ink-muted" style="font-size: 0.72rem;">Curriculum mastery</small>
        </a>
      </div>
      <div class="col-6">
        <a href="index.php?action=campaign" class="btn btn-outline-gamified w-100 text-start p-2.5 d-flex flex-column gap-1 text-decoration-none">
          <i class="bi bi-map-fill fs-4" style="color: var(--marker-orange);"></i>
          <span class="fw-bold fs-7">Quest Map</span>
          <small class="text-ink-muted" style="font-size: 0.72rem;">Placement campaign</small>
        </a>
      </div>
      <div class="col-6">
        <a href="index.php?action=duel" class="btn btn-outline-gamified w-100 text-start p-2.5 d-flex flex-column gap-1 text-decoration-none">
          <i class="bi bi-lightning-charge-fill fs-4" style="color: var(--wrong-red);"></i>
          <span class="fw-bold fs-7">1v1 Duels</span>
          <small class="text-ink-muted" style="font-size: 0.72rem;">Real-time battle</small>
        </a>
      </div>
      <div class="col-6">
        <a href="index.php?action=time_attack" class="btn btn-outline-gamified w-100 text-start p-2.5 d-flex flex-column gap-1 text-decoration-none">
          <i class="bi bi-stopwatch-fill fs-4" style="color: var(--correct-green);"></i>
          <span class="fw-bold fs-7">Time Attack</span>
          <small class="text-ink-muted" style="font-size: 0.72rem;">60-second sprint</small>
        </a>
      </div>
      <div class="col-6">
        <a href="index.php?action=leaderboard" class="btn btn-outline-gamified w-100 text-start p-2.5 d-flex flex-column gap-1 text-decoration-none">
          <i class="bi bi-award-fill fs-4" style="color: var(--highlighter-yellow);"></i>
          <span class="fw-bold fs-7">Leagues</span>
          <small class="text-ink-muted" style="font-size: 0.72rem;">Rankings ledger</small>
        </a>
      </div>
      <div class="col-6">
        <a href="index.php?action=trophy_room" class="btn btn-outline-gamified w-100 text-start p-2.5 d-flex flex-column gap-1 text-decoration-none">
          <i class="bi bi-star-fill fs-4" style="color: var(--pencil-graphite);"></i>
          <span class="fw-bold fs-7">Trophy Room</span>
          <small class="text-ink-muted" style="font-size: 0.72rem;">Career badges</small>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
function toggleExamTheme() {
  const html = document.documentElement;
  const current = html.getAttribute('data-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  const target = current === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', target);
  localStorage.setItem('apttitudee_theme', target);
  const metaTheme = document.getElementById('metaThemeColor');
  if (metaTheme) metaTheme.setAttribute('content', target === 'dark' ? '#16201B' : '#F6F1E7');
}

function cycleGlobalTextSize() {
  const html = document.documentElement;
  const current = html.getAttribute('data-font-size') || 'md';
  const next = current === 'sm' ? 'md' : (current === 'md' ? 'lg' : 'sm');
  html.setAttribute('data-font-size', next);
  localStorage.setItem('apttitudee_font_size', next);
}

function toggleColorblindMode() {
  const html = document.documentElement;
  const current = html.getAttribute('data-palette');
  const target = current === 'colorblind' ? 'standard' : 'colorblind';
  if (target === 'colorblind') {
    html.setAttribute('data-palette', 'colorblind');
  } else {
    html.removeAttribute('data-palette');
  }
  localStorage.setItem('apttitudee_palette', target);
}

function openModesBottomSheet() {
  const sheet = document.getElementById('modesBottomSheet');
  if (sheet) sheet.classList.add('open');
}

function closeModesBottomSheet() {
  const sheet = document.getElementById('modesBottomSheet');
  if (sheet) sheet.classList.remove('open');
}

// Global Online / Offline detection
window.addEventListener('offline', function() {
  const b = document.getElementById('offlineBanner');
  if (b) b.style.display = 'block';
});
window.addEventListener('online', function() {
  const b = document.getElementById('offlineBanner');
  if (b) b.style.display = 'none';
});
if (!navigator.onLine) {
  const b = document.getElementById('offlineBanner');
  if (b) b.style.display = 'block';
}
</script>

<div class="container py-3 py-md-4 flex-grow-1">
