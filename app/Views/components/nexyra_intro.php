<?php
/**
 * Exam Hall Rubber-Stamp Intro Splash ("Stamp Thud" - Direction A)
 * Replaces the legacy space/neon cinematic intro with a stationery paper & rubber-stamp feel.
 *
 * Sequence:
 *   0ms: Blank answer sheet (paper texture / ruled lines)
 * 250ms: Apttitudee rubber-stamp thuds down (scale 1.18 -> 1, -3deg rotation, ink bleed)
 * 480ms: Screen shake on impact
 * 650ms: "by Nexyra Solutions" byline fades in on a single line
 * 2500ms: Sheet slides up smoothly
 * 3000ms: Overlay removed from DOM; normal app interaction restored (total 3.0s)
 *
 * Guard rails:
 * - Session storage key: 'intro_seen'
 * - Suppressed on quiz solver (action=practice&session=1), mock tests, and login redirects
 * - Respects prefers-reduced-motion (static logo 400ms)
 * - Tapping anywhere, clicking Skip, or pressing Esc/Enter skips instantly
 * - Floating accessibility widget & cookie banner hidden during playback and restored after
 */

// PHP Guard: Never render on quiz sessions, mocks, or login redirects
$suppressIntro = false;
$actionParam = $_GET['action'] ?? 'landing';
$hasSessionParam = isset($_GET['session']) || isset($_GET['subtopic_id']);
$isQuizOrMock = ($actionParam === 'practice' && $hasSessionParam) 
             || in_array($actionParam, ['mock', 'duel', 'time_attack']);

$isRedirected = isset($_GET['redirect']) 
             || (isset($_SESSION['user_id']) && !empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'action=login') !== false);

if ($isQuizOrMock || $isRedirected) {
    $suppressIntro = true;
}
?>

<?php if (!$suppressIntro): ?>
<!-- Exam Hall Rubber Stamp Intro Overlay -->
<div id="examIntroOverlay" class="exam-intro-overlay" onclick="skipExamIntro(event)" role="dialog" aria-modal="true" aria-label="Apttitudee Introduction" style="display: none;">
  <!-- Sheet with warm paper surface & faint ruled lines -->
  <div class="exam-intro-sheet" id="examIntroSheet">
    
    <!-- Single Accessible Skip Control: 44px tap target in top corner -->
    <button type="button" class="exam-intro-skip-btn" onclick="skipExamIntro(event)" aria-label="Skip Introduction">
      Skip <span aria-hidden="true">&rarr;</span>
    </button>

    <!-- Center Stage: Stamp & Byline -->
    <div class="exam-intro-center" id="examIntroCenter">
      
      <!-- Rubber Stamp Badge -->
      <div class="exam-intro-stamp" id="examIntroStamp">
        <div class="stamp-inner-border">
          <div class="stamp-header">OFFICIAL EXAMINATION PLATFORM</div>
          <div class="stamp-brand">
            <svg class="stamp-logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
              <path d="M6 2v20"></path>
              <path d="M4 6h16"></path>
            </svg>
            <span class="stamp-wordmark">APTTITUDEE</span>
          </div>
          <div class="stamp-footer">
            <span class="stamp-star">&#9733;</span>
            <span class="stamp-standard">CAMPUS ASSESSMENT</span>
            <span class="stamp-star">&#9733;</span>
          </div>
        </div>
      </div>

      <!-- Single-line Byline: Normal tracking (<= 0.05em), strictly ONE line at 320px -->
      <div class="exam-intro-byline" id="examIntroByline">
        by Nexyra Solutions
      </div>

    </div>
  </div>
</div>
<?php endif; ?>

<script>
(function() {
  let introTimers = [];

  // Centralized Intro Timing Configuration (Tune all values in one place)
  const INTRO_CONFIG = {
    stampDelay: 250,        // Stamp impacts at 250ms
    shakeDelay: 480,        // Screen shake on stamp impact
    bylineDelay: 650,       // Byline fades in at 650ms
    holdDuration: 1850,     // Hold for ~1.85s (650ms + 1850ms = 2500ms)
    slideUpStart: 2500,     // 2500ms: sheet slide-up starts
    slideUpDuration: 500,   // Slide-up transition takes 500ms
    totalDuration: 3000,    // Overlay completely removed from DOM at exactly 3.0s
    reducedMotionHold: 400  // Static logo hold when prefers-reduced-motion is active
  };

  function clearAllIntroTimers() {
    introTimers.forEach(t => clearTimeout(t));
    introTimers = [];
  }

  function getIntroMarkup() {
    return `
      <div id="examIntroOverlay" class="exam-intro-overlay" onclick="skipExamIntro(event)" role="dialog" aria-modal="true" aria-label="Apttitudee Introduction">
        <div class="exam-intro-sheet" id="examIntroSheet">
          <button type="button" class="exam-intro-skip-btn" onclick="skipExamIntro(event)" aria-label="Skip Introduction">
            Skip <span aria-hidden="true">&rarr;</span>
          </button>
          <div class="exam-intro-center" id="examIntroCenter">
            <div class="exam-intro-stamp" id="examIntroStamp">
              <div class="stamp-inner-border">
                <div class="stamp-header">OFFICIAL EXAMINATION PLATFORM</div>
                <div class="stamp-brand">
                  <svg class="stamp-logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                    <path d="M6 2v20"></path>
                    <path d="M4 6h16"></path>
                  </svg>
                  <span class="stamp-wordmark">APTTITUDEE</span>
                </div>
                <div class="stamp-footer">
                  <span class="stamp-star">&#9733;</span>
                  <span class="stamp-standard">CAMPUS ASSESSMENT</span>
                  <span class="stamp-star">&#9733;</span>
                </div>
              </div>
            </div>
            <div class="exam-intro-byline" id="examIntroByline">
              by Nexyra Solutions
            </div>
          </div>
        </div>
      </div>
    `;
  }

  function cleanupIntro() {
    clearAllIntroTimers();
    document.removeEventListener('keydown', handleIntroKeyDown);
    document.body.classList.remove('intro-active');

    // Restore cookie banner if user has not yet consented
    if (!localStorage.getItem('apptitudee_cookie_consent')) {
      const banner = document.getElementById('cookieConsentBanner');
      if (banner) {
        banner.style.display = 'block';
      }
    }

    // Remove overlay entirely from DOM to prevent clicks or layout blocking
    const overlay = document.getElementById('examIntroOverlay');
    if (overlay) {
      overlay.remove();
    }
  }

  window.skipExamIntro = function(e) {
    if (e) {
      if (typeof e.stopPropagation === 'function') e.stopPropagation();
      if (typeof e.preventDefault === 'function') e.preventDefault();
    }
    clearAllIntroTimers();
    const sheet = document.getElementById('examIntroSheet');
    if (sheet) {
      sheet.classList.add('slide-up');
      setTimeout(cleanupIntro, 180);
    } else {
      cleanupIntro();
    }
  };

  function handleIntroKeyDown(e) {
    if (e.key === 'Escape' || e.key === 'Enter' || e.key === ' ') {
      skipExamIntro(e);
    }
  }

  function runIntroSequence(overlay) {
    document.body.classList.add('intro-active');
    document.addEventListener('keydown', handleIntroKeyDown);

    // Hide cookie banner immediately while intro plays
    const banner = document.getElementById('cookieConsentBanner');
    if (banner) {
      banner.style.display = 'none';
    }

    const sheet = document.getElementById('examIntroSheet');
    const stamp = document.getElementById('examIntroStamp');
    const byline = document.getElementById('examIntroByline');

    if (sheet) {
      sheet.style.setProperty('--intro-slide-duration', INTRO_CONFIG.slideUpDuration + 'ms');
    }

    // Check prefers-reduced-motion
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) {
      if (stamp) {
        stamp.style.opacity = '1';
        stamp.style.transform = 'rotate(-3deg) scale(1)';
        stamp.classList.remove('thud-in');
      }
      if (byline) {
        byline.style.opacity = '1';
        byline.classList.remove('fade-in');
      }
      // Hold static logo for 400ms per requirement, then cleanly finish
      introTimers.push(setTimeout(() => {
        cleanupIntro();
      }, INTRO_CONFIG.reducedMotionHold));
      return;
    }

    // Sequence: ~2.0s total duration
    // 250ms: Stamp thud drops
    introTimers.push(setTimeout(() => {
      if (stamp) stamp.classList.add('thud-in');
    }, INTRO_CONFIG.stampDelay));

    // 480ms: Screen shake on stamp impact
    introTimers.push(setTimeout(() => {
      if (sheet) sheet.classList.add('stamp-shake');
    }, INTRO_CONFIG.shakeDelay));

    // 650ms: Byline fades in smoothly
    introTimers.push(setTimeout(() => {
      if (byline) byline.classList.add('fade-in');
    }, INTRO_CONFIG.bylineDelay));

    // 1450ms (0.8s hold): Paper sheet slides up smoothly (takes 400ms)
    introTimers.push(setTimeout(() => {
      if (sheet) sheet.classList.add('slide-up');
    }, INTRO_CONFIG.slideUpStart));

    // ~2.0s (1950ms): Sequence completes, overlay removed from DOM
    introTimers.push(setTimeout(() => {
      cleanupIntro();
    }, INTRO_CONFIG.totalDuration));
  }

  // Global trigger function to re-play intro anytime from navbar or buttons
  window.playNexyraIntro = function() {
    clearAllIntroTimers();
    let overlay = document.getElementById('examIntroOverlay');
    if (!overlay) {
      document.body.insertAdjacentHTML('beforeend', getIntroMarkup());
      overlay = document.getElementById('examIntroOverlay');
    }
    if (overlay) {
      overlay.style.display = 'flex';
      const sheet = document.getElementById('examIntroSheet');
      const stamp = document.getElementById('examIntroStamp');
      const byline = document.getElementById('examIntroByline');
      if (sheet) sheet.className = 'exam-intro-sheet';
      if (stamp) {
        stamp.className = 'exam-intro-stamp';
        stamp.style.opacity = '0';
      }
      if (byline) {
        byline.className = 'exam-intro-byline';
        byline.style.opacity = '0';
      }
      runIntroSequence(overlay);
    }
  };

  // Auto-play on first session visit
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const forceIntro = urlParams.get('show_intro') === '1';
    const seen = sessionStorage.getItem('intro_seen');

    const action = urlParams.get('action') || 'landing';
    const hasSession = urlParams.get('session') === '1' || urlParams.has('subtopic_id');
    const isQuiz = (action === 'practice' && hasSession) || ['mock', 'duel', 'time_attack'].includes(action);

    // Never auto-play if already seen this session, or on quiz screens, unless forced via ?show_intro=1
    if (!forceIntro && (seen === 'true' || isQuiz)) {
      const existing = document.getElementById('examIntroOverlay');
      if (existing) existing.remove();
      return;
    }

    const overlay = document.getElementById('examIntroOverlay');
    if (overlay) {
      sessionStorage.setItem('intro_seen', 'true');
      overlay.style.display = 'flex';
      runIntroSequence(overlay);
    }
  });
})();
</script>
