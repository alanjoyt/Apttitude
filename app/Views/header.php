<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  
  <?php
    $currentAction = $_GET['action'] ?? 'landing';
    $isQuizSession = ($currentAction === 'practice' && (isset($_GET['session']) || isset($_GET['subtopic_id']) || (isset($_GET['mode']) && $_GET['mode'] === 'quiz')))
                  || ($currentAction === 'technical' && (!isset($_GET['view']) || $_GET['view'] !== 'hub'))
                  || in_array($currentAction, ['mock', 'duel', 'time_attack']);

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
    } elseif ($currentAction === 'technical' && isset($_GET['view']) && $_GET['view'] === 'hub') {
        $title = "Technical Coding & Placement PYQs | Apttitudee";
        $desc = "Master core programming concepts and placement coding questions in C, C++, Java, Python, SQL, and Innovature Labs PYQs.";
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
  <meta name="author" content="Nexyra Solutions">
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

  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts: Bitter (Headings), Atkinson Hyperlegible (Body & Stem), JetBrains Mono (Code) -->
  <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&family=Bitter:wght@700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  
  <!-- Canvas Confetti & Chart.js CDNs (Deferred to avoid blocking initial render) -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

  <!-- Legacy Gamified CSS -->
  <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
  <!-- Exam Hall Theme CSS (Design Tokens, Exam Components, Mobile-First Overrides) -->
  <link rel="stylesheet" href="<?= asset_url('css/theme.css') ?>">

  <!-- Early Theme Initialization to Prevent FOUC -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('apttitudee_theme');
      if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
      } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-theme', 'dark');
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
      if (typeof Chart !== 'undefined') {
        fn();
      } else {
        window.addEventListener('load', fn, { once: true });
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

<?php if (!$isQuizSession): ?>
<!-- ======================================================================
     MOBILE SLIM TOP BAR (< 768px, Hub & Syllabus Pages)
     ====================================================================== -->
<header class="top-header-exam d-md-none">
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
          <span class="flame-icon"><svg class="exam-icon text-danger" style="width:14px;height:14px;"><use href="#icon-flame"></use></svg></span>
          <span id="mobileStreakCount"><?= (int)$currentUser['current_streak'] ?></span><small>d</small>
        </div>

        <!-- Speed Coins Brass Token -->
        <a href="index.php?action=store" class="coin-token text-decoration-none" title="Speed Coins Wallet">
          <span class="coin-icon"><svg class="exam-icon text-warning" style="width:14px;height:14px;"><use href="#icon-coin"></use></svg></span>
          <span id="userCoinsDisplayMobile"><?= number_format((int)$currentUser['coins']) ?></span>
        </a>
      <?php else: ?>
        <a href="index.php?action=login" class="btn btn-outline-gamified btn-sm px-2.5 py-1 text-decoration-none font-sans fw-bold" style="font-size: 0.85rem !important; min-height: 36px;">
          Log In
        </a>
      <?php endif; ?>

      <!-- Theme Switcher Toggle (Chalk/Paper) -->
      <button type="button" class="btn-exam-icon" onclick="toggleExamTheme()" title="Toggle Dark/Light Mode" aria-label="Toggle Dark/Light Mode">
        <i class="bi bi-circle-half"></i>
      </button>

      <!-- Sound FX Toggle Button -->
      <button id="soundToggleBtn" class="btn-exam-icon" title="Toggle Sound FX" aria-label="Toggle Sound Effects">
        <i class="bi bi-volume-up-fill text-success"></i>
      </button>
    </div>
  </div>
</header>
<?php endif; ?>

<!-- ======================================================================
     DESKTOP NAVIGATION (>= 768px)
     ====================================================================== -->
<nav class="navbar navbar-expand-md nav-gamified sticky-top py-2 d-none d-md-block">
  <div class="container-fluid px-3 px-xl-4" style="max-width: 1440px;">
    <!-- Brand Logo -->
    <a class="navbar-brand d-flex align-items-center gap-2 me-lg-3" href="index.php?action=landing" aria-label="Apttitudee Homepage">
      <div class="brand-icon-box">
        <i class="bi bi-book-half fs-4 text-accent"></i>
      </div>
      <div class="d-flex flex-column leading-tight">
        <span class="font-serif fw-bold fs-4 text-ink" style="letter-spacing: -0.02em;">Apttitud<span class="brand-accent-text">ee</span></span>
      </div>
      <span class="stamp-badge-highlight d-none d-sm-inline-block ms-1" style="font-size:0.65rem; padding: 2px 6px;">EXAM HALL</span>
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
      <div class="d-flex align-items-center gap-1.5 gap-xl-2 ms-auto flex-nowrap">
        <!-- Quick Utility Icon Buttons (Theme, Sound, Intro, Guide) -->
        <div class="nav-utility-bar d-flex align-items-center gap-1 p-1 rounded-pill ms-1">
          <button type="button" class="btn btn-utility-icon" onclick="toggleExamTheme()" title="Toggle Dark/Light Mode" aria-label="Toggle Dark/Light Mode">
            <i class="bi bi-circle-half text-warning"></i>
          </button>
          <button id="soundToggleBtn" class="btn btn-utility-icon" title="Toggle Sound FX" aria-label="Toggle Sound Effects">
            <i class="bi bi-volume-up-fill text-success"></i>
          </button>
          <button type="button" class="btn btn-utility-icon" onclick="playNexyraIntro()" title="Play Exam Intro" aria-label="Play Exam Intro">
            <i class="bi bi-play-circle-fill text-info"></i>
          </button>
          <button type="button" class="btn btn-utility-icon" data-bs-toggle="modal" data-bs-target="#featureGuideModal" title="How Apttitudee Works Guide" aria-label="Feature Guide">
            <i class="bi bi-compass-fill text-warning"></i>
          </button>
        </div>

        <?php if ($currentUser): ?>
          <!-- Stat Summary Pill (Shown on large desktop >= 1400px to ensure laptop navbars never overflow) -->
          <div class="stat-pill-gamified d-none d-xxl-inline-flex align-items-center gap-2 py-1 px-3" title="Streak, Total XP & Speed Coins">
            <span class="stat-item" title="Current Daily Practice Streak"><i class="bi bi-fire text-danger animate-pulse"></i> <strong><?= $currentUser['current_streak'] ?></strong><small>d</small></span>
            <span class="stat-divider"></span>
            <span class="stat-item" title="Total XP Earned"><i class="bi bi-star-fill text-warning"></i> <strong id="userXPDisplay"><?= number_format($currentUser['total_xp']) ?></strong></span>
            <span class="stat-divider"></span>
            <span class="stat-item" title="Speed Coins Wallet Balance"><i class="bi bi-coin text-warning"></i> <strong id="userCoinsDisplay"><?= number_format($currentUser['coins']) ?></strong></span>
          </div>

          <!-- User Profile Dropdown -->
          <div class="dropdown">
            <button class="btn btn-user-profile dropdown-toggle d-flex align-items-center gap-2 py-1 px-2 px-sm-2.5" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
              <div class="avatar-circle">
                <i class="bi <?= !empty($currentUser['avatar_icon']) ? $currentUser['avatar_icon'] : 'bi-person-circle' ?>"></i>
              </div>
              <div class="d-none d-md-flex flex-column text-start me-1">
                <span class="fw-bold small text-ink leading-tight text-truncate" style="max-width: 110px;"><?= htmlspecialchars($currentUser['name']) ?></span>
                <span class="text-muted extra-small" style="font-size:0.7rem;"><?= htmlspecialchars($currentUser['tier']) ?> Tier</span>
              </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 user-dropdown-card">
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
                    <div class="fw-bold text-ink"><svg class="exam-icon text-danger me-1" style="width:14px;height:14px;"><use href="#icon-flame"></use></svg><?= $currentUser['current_streak'] ?>d</div>
                  </div>
                  <div>
                    <div class="extra-small text-muted">TOTAL XP</div>
                    <div class="fw-bold text-ink"><i class="bi bi-star-fill text-warning me-1"></i><?= number_format($currentUser['total_xp']) ?></div>
                  </div>
                  <div>
                    <div class="extra-small text-muted">COINS</div>
                    <div class="fw-bold text-ink"><svg class="exam-icon text-warning me-1" style="width:14px;height:14px;"><use href="#icon-coin"></use></svg><?= number_format($currentUser['coins']) ?></div>
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
            <div><span class="text-muted extra-small">STREAK</span><br><strong><svg class="exam-icon text-danger me-0.5" style="width:13px;height:13px;"><use href="#icon-flame"></use></svg><?= $currentUser['current_streak'] ?>d</strong></div>
            <div class="vr opacity-25"></div>
            <div><span class="text-muted extra-small">XP</span><br><strong><i class="bi bi-star-fill text-warning me-0.5"></i><?= number_format($currentUser['total_xp']) ?></strong></div>
            <div class="vr opacity-25"></div>
            <div><span class="text-muted extra-small">COINS</span><br><strong><svg class="exam-icon text-warning me-0.5" style="width:13px;height:13px;"><use href="#icon-coin"></use></svg><?= number_format($currentUser['coins']) ?></strong></div>
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
          <button onclick="playNexyraIntro()" class="btn btn-sm btn-outline-gamified px-3 py-1"><i class="bi bi-play-circle-fill text-info me-1"></i>Intro</button>
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
  if (metaTheme) metaTheme.setAttribute('content', target === 'dark' ? '#1B2420' : '#F6F1E7');
}

function openModesBottomSheet() {
  const sheet = document.getElementById('modesBottomSheet');
  if (sheet) sheet.classList.add('open');
}

function closeModesBottomSheet() {
  const sheet = document.getElementById('modesBottomSheet');
  if (sheet) sheet.classList.remove('open');
}
</script>

<div class="container py-3 py-md-4 flex-grow-1">
