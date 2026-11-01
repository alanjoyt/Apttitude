<?php
// Technical Assessment Practice Hub
$activeLang = $_GET['lang'] ?? 'all';
$isQuizSession = isset($_GET['session']) || isset($_GET['start']) || (isset($_GET['mode']) && $_GET['mode'] === 'quiz');

$db = Database::getConnection();
$techSummary = $db->query("
    SELECT COUNT(*) as total_questions, COUNT(DISTINCT q.language) as total_langs
    FROM questions q
    JOIN subtopics s ON q.subtopic_id = s.id
    WHERE q.is_published = 1 AND (s.topic_id IN (5, 6) OR (q.language IS NOT NULL AND q.language != '' AND q.language NOT IN ('quant', 'lr')))
")->fetch(PDO::FETCH_ASSOC);

// Dynamic counts per language
$langCountsStmt = $db->query("
    SELECT LOWER(language) as lang, COUNT(*) as cnt 
    FROM questions 
    WHERE is_published = 1 AND language IS NOT NULL AND language != ''
    GROUP BY LOWER(language)
");
$countsByLang = [];
if ($langCountsStmt) {
    while ($row = $langCountsStmt->fetch(PDO::FETCH_ASSOC)) {
        $countsByLang[$row['lang']] = (int)$row['cnt'];
    }
}
$cCount = $countsByLang['c'] ?? 59;
$pyCount = $countsByLang['python'] ?? 37;
$tsCount = $countsByLang['typescript'] ?? 25;
$llmCount = $countsByLang['llm'] ?? 25;
$aiCount = $countsByLang['ai'] ?? 25;
$innoCount = $countsByLang['innovature'] ?? 8;

$totalTechQuestions = (int)($techSummary['total_questions'] ?? ($cCount + $pyCount + $tsCount + $llmCount + $aiCount + $innoCount));
$totalTechLanguages = (int)($techSummary['total_langs'] ?? count($countsByLang));

?>

<?php if (!$isQuizSession): ?>
<div class="container py-3">
  <!-- Tech Hub Header & Category Carousel (Visible on Hub overview) -->
  <div class="tech-hub-content mb-4">
    <!-- Page Header & Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-2 mb-3">
      <div class="min-w-0 flex-grow-1">
        <h1 class="font-serif fw-bold text-ink mb-1 tech-hero-heading" style="font-size: clamp(1.4rem, 4vw, 1.85rem);">Technical Coding &amp; Concept Mastery</h1>
        <!-- Single plain text dynamic stat line matching DB -->
        <p class="text-ink-muted small mb-0 fw-semibold">
          <?= $totalTechQuestions ?> questions &middot; <?= $totalTechLanguages ?> categories
        </p>
      </div>

      <!-- Innovature Mock Quick Link -->
      <div class="d-flex align-items-center gap-2 flex-shrink-0 mt-1 mt-md-0">
        <a href="index.php?action=mock&pack=INNOVATURE_MOCK_PACK" class="link-tertiary-exam small fw-bold text-nowrap">
          Innovature Mock Pack &rarr;
        </a>
      </div>
    </div>

    <!-- Category Filter Carousel: Programming Languages & IT Company Placement PYQs -->
    <div class="tech-chips-wrapper">
      <div class="tech-chips-scroll" id="languageFilterBar">
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'all' ? 'active' : '' ?>" data-lang="all">
          <i class="bi bi-grid-fill me-1"></i> All (<?= $totalTechQuestions ?>)
        </button>

        <!-- Core Programming Languages -->
        <span class="chip-divider-text">LANGUAGES:</span>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'typescript' ? 'active' : '' ?>" data-lang="typescript">
          <i class="bi bi-filetype-tsx me-1 text-primary"></i> TypeScript (<?= $tsCount ?>)
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'python' ? 'active' : '' ?>" data-lang="python">
          <i class="bi bi-filetype-py me-1 text-success"></i> Python (<?= $pyCount ?>)
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'c' ? 'active' : '' ?>" data-lang="c">
          <i class="bi bi-terminal me-1 text-info"></i> C Language (<?= $cCount ?>)
        </button>

        <!-- AI & Modern Tech Interview Tracks -->
        <span class="chip-divider-text">AI &amp; EMERGING TECH:</span>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'llm' ? 'active' : '' ?>" data-lang="llm">
          <i class="bi bi-chat-square-quote-fill me-1" style="color: #8b5cf6;"></i> LLM &amp; RAG (<?= $llmCount ?>)
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'ai' ? 'active' : '' ?>" data-lang="ai">
          <i class="bi bi-robot me-1 text-info"></i> AI &amp; GenAI (<?= $aiCount ?>)
        </button>

        <button type="button" class="btn btn-sm btn-lang-tab opacity-50" disabled title="Draft Track (Quarantined until compiler verification)">
          <i class="bi bi-cpu me-1 text-primary"></i> C++ <span class="badge bg-secondary extra-small ms-1">Draft</span>
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab opacity-50" disabled title="Draft Track (Quarantined until compiler verification)">
          <i class="bi bi-cup-hot-fill me-1 text-danger"></i> Java <span class="badge bg-secondary extra-small ms-1">Draft</span>
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab opacity-50" disabled title="Draft Track (Quarantined until compiler verification)">
          <i class="bi bi-filetype-php me-1" style="color: #7c3aed;"></i> PHP <span class="badge bg-secondary extra-small ms-1">Draft</span>
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab opacity-50" disabled title="Draft Track (Quarantined until compiler verification)">
          <i class="bi bi-microsoft me-1 text-primary"></i> C# (.NET) <span class="badge bg-secondary extra-small ms-1">Draft</span>
        </button>

        <!-- IT Companies & Placement Drives -->
        <span class="chip-divider-text">COMPANY TRACKS:</span>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'innovature' ? 'active' : '' ?>" data-lang="innovature">
          <i class="bi bi-building-fill-gear me-1 text-success"></i> Innovature Labs (<?= $innoCount ?>)
        </button>
      </div>
    </div>
  </div>


  <div class="row g-4">
    <!-- Left Column: Start Technical Questions Overview Card -->
    <div class="col-lg-8">

      <!-- Guest Warning Banner if not logged in (1-line strip, max 40px) -->
      <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="guest-strip-exam mb-3" role="status">
          <div class="d-flex align-items-center gap-1.5 min-w-0 flex-grow-1">
            <span class="badge-stamp-sm flex-shrink-0">TRIAL</span>
            <span class="small fw-semibold text-truncate">Guest trial &middot; Sign up to track technical mastery</span>
          </div>
          <a href="index.php?action=register" class="link-tertiary-exam small fw-bold text-nowrap flex-shrink-0">Sign Up &rarr;</a>
        </div>
      <?php endif; ?>

      <!-- Start Technical Questions Landing Card -->
      <div class="card card-gamified p-4 mb-3 shadow-sm" style="background-color: var(--surface-bg); color: var(--surface-text); border: 2px solid var(--border-strong) !important; border-radius: var(--radius-lg);">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="text-white d-flex align-items-center justify-content-center" id="startTrackIconWrap" style="width: 52px; height: 52px; border-radius: 14px; background: var(--ink); flex-shrink: 0; box-shadow: var(--shadow-hard-sm);">
            <i class="bi bi-grid-fill fs-3" id="startTrackIcon" style="color: var(--marker-orange);"></i>
          </div>
          <div class="min-w-0">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
              <span class="badge badge-stamp-sm" id="startTrackBadge" style="background: var(--marker-tint); color: var(--marker-orange); border: 1px solid var(--marker-orange);">FULL STACK</span>
              <span class="badge bg-secondary-subtle text-ink extra-small" id="startTrackCountBadge"><?= $totalTechQuestions ?> Questions</span>
            </div>
            <h3 class="font-serif fw-bold text-ink m-0" id="startTrackTitle" style="font-size: clamp(1.2rem, 3vw, 1.55rem);">
              All Technical Categories
            </h3>
          </div>
        </div>

        <p class="text-ink-muted small mb-3 lh-base" id="startTrackDescription">
          Master core programming concepts, pointer logic, recursion, control structures, and code output prediction with adaptive difficulty.
        </p>

        <!-- Assessment Perks & Rules Grid -->
        <div class="row g-2 mb-4">
          <div class="col-6 col-sm-3">
            <div class="p-2.5 rounded-3 border text-center h-100" style="background: var(--surface); border-color: var(--border) !important;">
              <div class="text-primary mb-1"><i class="bi bi-speedometer2 fs-5"></i></div>
              <div class="fw-bold text-ink extra-small">Adaptive Engine</div>
              <div class="text-ink-muted" style="font-size: 0.68rem;">Auto-adjusting level</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div class="p-2.5 rounded-3 border text-center h-100" style="background: var(--surface); border-color: var(--border) !important;">
              <div class="text-warning mb-1"><i class="bi bi-stopwatch fs-5"></i></div>
              <div class="fw-bold text-ink extra-small">Live Question Timer</div>
              <div class="text-ink-muted" style="font-size: 0.68rem;">Speed tracking</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div class="p-2.5 rounded-3 border text-center h-100" style="background: var(--surface); border-color: var(--border) !important;">
              <div class="text-danger mb-1"><i class="bi bi-fire fs-5"></i></div>
              <div class="fw-bold text-ink extra-small">Boss Questions</div>
              <div class="text-ink-muted" style="font-size: 0.68rem;">2x XP & Coins</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div class="p-2.5 rounded-3 border text-center h-100" style="background: var(--surface); border-color: var(--border) !important;">
              <div class="text-success mb-1"><i class="bi bi-journal-code fs-5"></i></div>
              <div class="fw-bold text-ink extra-small">Detailed Analysis</div>
              <div class="text-ink-muted" style="font-size: 0.68rem;">Line-by-line solution</div>
            </div>
          </div>
        </div>

        <!-- Big Start CTA Button -->
        <a href="index.php?action=technical&session=1&lang=<?= urlencode($activeLang) ?>" id="startTechnicalQuestionsBtn" class="btn btn-primary-gamified btn-lg w-100 py-3 fw-bold fs-6 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="letter-spacing: 0.02em; color: #ffffff !important;">
          <i class="bi bi-play-circle-fill fs-5"></i>
          <span style="color: #ffffff !important;">Start Technical Questions</span>
          <i class="bi bi-arrow-right ms-1"></i>
        </a>
        <div class="text-center mt-2.5">
          <span class="text-ink-muted extra-small">
            <i class="bi bi-shield-lock me-1 text-success"></i> Auto-saves your XP, coins, and streak after every question
          </span>
        </div>
      </div>
    </div>

    <!-- Right Column: Language Mastery & Track Overview -->
    <div class="col-lg-4 tech-hub-content">
      <!-- IT Company Placement Practice Spotlight Card (Innovature Labs) -->
      <div class="card card-gamified p-3 mb-3 shadow-sm" style="background-color: var(--surface-bg); color: var(--surface-text); border: 1.5px solid var(--border-strong) !important;">
        <div class="d-flex align-items-center gap-2.5 mb-2">
          <div class="avatar-circle-sm bg-success text-white">
            <i class="bi bi-building-fill-gear"></i>
          </div>
          <div>
            <h6 class="fw-bold text-ink mb-0">Innovature Labs (IT Company Drive)</h6>
            <span class="badge bg-success text-white extra-small">Placement Practice Track</span>
          </div>
        </div>
        <p class="small text-muted mb-2">Practice questions modeled on Innovature Labs reported pattern (C pointers, Java OOP, SQL, logical coding).</p>
        <div id="innovatureProgressDisplay" class="mb-2.5">
          <!-- Dynamically populated with live solve stats -->
        </div>
        <div class="d-flex gap-2">
          <a href="index.php?action=technical&session=1&lang=innovature" class="btn btn-primary-gamified btn-sm rounded-pill flex-grow-1 fw-bold py-1.5 text-center text-decoration-none">
            <i class="bi bi-play-circle-fill me-1"></i> Practice Track
          </a>
          <a href="index.php?action=mock&pack=INNOVATURE_MOCK_PACK" class="btn btn-outline-gamified btn-sm rounded-pill px-3 py-1.5 text-nowrap">
            Mock Test
          </a>
        </div>
      </div>

      <!-- Language Mastery Progress Card -->
      <div class="card card-gamified p-3 shadow-sm mb-3">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h6 class="fw-bold text-ink mb-0"><i class="bi bi-bar-chart-fill text-primary me-1.5"></i> Language Mastery</h6>
          <span class="extra-small text-muted" id="userMasteryStatus">Accuracy by Language</span>
        </div>
        <div class="d-flex flex-column gap-2.5" id="languageStatsList">
          <!-- Dynamically populated by JS -->
          <div class="text-center py-3 text-muted small">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Loading language progress...
          </div>
        </div>
      </div>

      <!-- Pro-Tip Card -->
      <div class="card card-gamified p-3 text-center" style="background-color: var(--surface-bg); color: var(--surface-text); border: 1.5px solid var(--border-strong) !important;">
        <i class="bi bi-lightbulb-fill fs-3 text-warning mb-1"></i>
        <h6 class="fw-bold text-ink mb-1">Placement Strategy Tip</h6>
        <p class="extra-small text-muted mb-0">Product companies and technical tests heavily penalize guessing. Tagging "Sure" on answers you know awards bonus Speed XP!</p>
      </div>
    </div>
  </div>
</div>

<?php else: ?>
<!-- --------------------------------------------------------------------------
     ACTIVE TECHNICAL QUIZ SESSION (Focus Mode on Mobile, Clean Card on Desktop)
     -------------------------------------------------------------------------- -->
<div class="container py-2 py-sm-3">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">

      <!-- Guest Warning Banner if not logged in (1-line strip, max 40px) -->
      <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="guest-strip-exam mb-3" role="status">
          <div class="d-flex align-items-center gap-1.5 min-w-0 flex-grow-1">
            <span class="badge-stamp-sm flex-shrink-0">TRIAL</span>
            <span class="small fw-semibold text-truncate">Guest trial &middot; Sign up to track technical mastery</span>
          </div>
          <a href="index.php?action=register" class="link-tertiary-exam small fw-bold text-nowrap flex-shrink-0">Sign Up &rarr;</a>
        </div>
      <?php endif; ?>

      <!-- Desktop Breadcrumb (Hidden on Mobile where slim quiz bar sits at top) -->
      <div class="d-none d-md-flex justify-content-between align-items-center mb-2 px-1">
        <a href="index.php?action=technical" class="link-tertiary-exam text-decoration-none fs-7 d-inline-flex align-items-center gap-1">
          <i class="bi bi-arrow-left"></i>
          <span>Technical Hub</span>
        </a>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-secondary-subtle text-ink extra-small px-2 py-1 rounded-pill">
            Track: <?= strtoupper(htmlspecialchars($activeLang)) ?>
          </span>
          <span class="small text-ink-muted d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
            <i class="bi bi-shield-check text-success"></i> Auto-saved
          </span>
        </div>
      </div>

      <!-- Shared Quiz Shell Partial -->
      <?php
      $quizContext = 'technical';
      include __DIR__ . '/components/quiz_shell.php';
      ?>
    </div>
  </div>
</div>
<?php endif; ?>

<style>
/* Developer Terminal Code Snippet Container */
.tech-code-container {
  background: #1e1e2e;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
.code-terminal-header {
  background: #181825;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.terminal-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}
.dot-red { background: #f38ba8; }
.dot-yellow { background: #f9e2af; }
.dot-green { background: #a6e3a1; }
.code-lang-label {
  font-family: 'Chakra Petch', monospace;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.tech-code-body {
  font-family: 'Courier New', Courier, monospace, 'Fira Code';
  font-size: 0.95rem;
  line-height: 1.55;
  color: #cdd6f4;
  white-space: pre-wrap;
  word-break: break-word;
}
.btn-lang-tab {
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--ink);
  font-size: 0.8rem;
  font-weight: 600;
  border-radius: 20px;
  padding: 5px 12px;
  transition: all 0.2s ease;
}
.btn-lang-tab:hover, .btn-lang-tab.active {
  background: var(--marker-orange) !important;
  color: #14213D !important;
  border-color: var(--border-strong) !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}
.btn-lang-tab.active i {
  color: #14213D !important;
}
</style>

<script>
// Technical Practice Engine
const TechnicalEngine = {
  isQuizSession: <?= $isQuizSession ? 'true' : 'false' ?>,
  activeLanguage: '<?= htmlspecialchars($activeLang) ?>',
  activeDifficulty: 'adaptive',
  currentQuestion: null,
  selectedOption: null,
  confidence: 'sure',
  startTime: null,
  timerInterval: null,
  elapsedSeconds: 0,
  countdownInterval: null,
  countdownSeconds: 10,
  seenQuestionIds: [],
  hintUnlockedQuestionId: null,
  eliminatedOption: null,

  tracks: {
    'all': {
      title: 'All Technical Categories',
      icon: 'bi-grid-fill',
      badge: 'FULL STACK',
      count: '<?= $totalTechQuestions ?> Questions',
      desc: 'Master comprehensive programming logic across C, Python, and Innovature placement tests with adaptive difficulty.'
    },
    'c': {
      title: 'C Programming Language',
      icon: 'bi-terminal',
      badge: 'LOW-LEVEL CORE',
      count: '<?= $cCount ?> Questions',
      desc: 'Pointers, pointer arithmetic, memory management, bitwise operations, recursion, and tricky code output prediction.'
    },
    'python': {
      title: 'Python Core & Scripting',
      icon: 'bi-filetype-py',
      badge: 'SCRIPTING & AI',
      count: '<?= $pyCount ?> Questions',
      desc: 'Data structures, list comprehensions, slicing, scope rules, lambda functions, OOP concepts, and algorithmic reasoning.'
    },
    'typescript': {
      title: 'TypeScript & Modern JS',
      icon: 'bi-filetype-tsx',
      badge: 'TYPES & WEB ARCHITECTURE',
      count: '<?= $tsCount ?> Questions',
      desc: 'Master TypeScript interview essentials: utility types, generics, conditional & mapped types, structural typing, type narrowing, and strict compiler flags.'
    },
    'llm': {
      title: 'Large Language Models (LLM) & RAG',
      icon: 'bi-chat-square-quote-fill',
      badge: 'GEN AI & PROMPT ENG',
      count: '<?= $llmCount ?> Questions',
      desc: 'Deep dive into RAG pipelines, chunking, vector embeddings, sampling parameters (temperature, top-p), KV cache, LoRA fine-tuning, and hallucination reduction.'
    },
    'ai': {
      title: 'Artificial Intelligence & Generative AI',
      icon: 'bi-robot',
      badge: 'AGENTS & TRANSFORMERS',
      count: '<?= $aiCount ?> Questions',
      desc: 'Master AI agents (ReAct, Plan-and-Solve), tool/function calling, self-attention & transformers, vector databases (HNSW), diffusion models, and model evaluation.'
    },
    'innovature': {
      title: 'Innovature Labs Placement Track',
      icon: 'bi-building-fill-gear',
      badge: 'CAMPUS DRIVE PYQ',
      count: '<?= $innoCount ?> Questions',
      desc: 'Practice previous year campus recruitment style questions from Innovature Labs (C pointers, Java OOP, SQL, and logical code).'
    }
  },

  updateStartCard(lang) {
    const meta = this.tracks[lang] || this.tracks['all'];
    const titleEl = document.getElementById('startTrackTitle');
    const iconEl = document.getElementById('startTrackIcon');
    const badgeEl = document.getElementById('startTrackBadge');
    const countEl = document.getElementById('startTrackCountBadge');
    const descEl = document.getElementById('startTrackDescription');
    const ctaBtn = document.getElementById('startTechnicalQuestionsBtn');

    if (titleEl) titleEl.innerText = meta.title;
    if (iconEl) iconEl.className = `bi ${meta.icon} fs-3`;
    if (badgeEl) badgeEl.innerText = meta.badge;
    if (countEl) countEl.innerText = meta.count;
    if (descEl) descEl.innerText = meta.desc;
    if (ctaBtn) {
      ctaBtn.href = `index.php?action=technical&session=1&lang=${encodeURIComponent(lang)}`;
    }
  },

  init() {
    this.bindEvents();
    this.loadLanguageStats();
    if (this.isQuizSession) {
      this.loadNextQuestion();
    } else {
      this.updateStartCard(this.activeLanguage);
    }
  },

  applyEliminatedOption(optKey, optText) {
    if (!optKey) return;
    const btn = document.querySelector(`.option-btn[data-option="${optKey}"], .tech-opt-btn[data-option="${optKey}"]`);
    if (btn) {
      btn.classList.add('eliminated');
      btn.classList.remove('selected');
      btn.disabled = true;
      btn.setAttribute('aria-disabled', 'true');

      if (this.selectedOption === optKey) {
        this.selectedOption = null;
        const submitBtn = document.getElementById('submitBtn') || document.getElementById('techSubmitBtn');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.setAttribute('aria-disabled', 'true');
        }
      }

      if (!btn.querySelector('.eliminated-badge')) {
        const badge = document.createElement('span');
        badge.className = 'badge bg-danger-subtle text-danger extra-small ms-auto font-monospace eliminated-badge';
        badge.style.fontSize = '0.72rem';
        badge.style.letterSpacing = '0.04em';
        badge.innerHTML = '❌ ELIMINATED';
        btn.appendChild(badge);
      }
    }
  },

  openHint() {
    if (!this.currentQuestion) {
      alert('Question is loading, please wait a moment.');
      return;
    }

    if (this.hintUnlockedQuestionId === this.currentQuestion.id && this.eliminatedOption) {
      this.applyEliminatedOption(this.eliminatedOption);
      const hintModalEl = document.getElementById('hintModal');
      if (hintModalEl) {
        bootstrap.Modal.getOrCreateInstance(hintModalEl).show();
      }
      return;
    }

    const hintBtn = document.getElementById('formulaHintBtn');
    const origText = hintBtn ? hintBtn.innerHTML : '';
    if (hintBtn) {
      hintBtn.disabled = true;
      hintBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Eliminating...';
    }

    const visibleOpts = {};
    document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(btn => {
      const opt = btn.dataset.option;
      if (opt) {
        const textSpan = btn.querySelector('.tech-opt-text') || btn.querySelector('.option-text') || btn;
        let txt = textSpan.innerText || '';
        txt = txt.replace(/^[A-D]\s*/, '').replace(/❌\s*ELIMINATED.*/i, '').replace(/✔\s*CORRECT.*/i, '').replace(/✖\s*INCORRECT.*/i, '').trim();
        visibleOpts[opt] = txt;
      }
    });

    fetch('index.php?action=api_use_hint', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ question_id: this.currentQuestion.id, visible_options: visibleOpts, csrf_token: window.CSRF_TOKEN || '' })
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'insufficient_coins') {
          alert(data.message || 'You need at least 5 Speed Coins to eliminate a wrong option!');
          return;
        }
        if (data.status !== 'success') {
          alert(data.message || 'Unable to eliminate option.');
          return;
        }

        if (typeof data.new_coin_balance !== 'undefined') {
          const formatted = Number(data.new_coin_balance).toLocaleString();
          ['userCoinsDisplay', 'userCoinsDisplayMobile', 'dropdownUserCoins', 'drawerUserCoins', 'navCoinsDisplay', 'storeUserCoinsDisplay'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerText = formatted;
          });
        }

        this.hintUnlockedQuestionId = this.currentQuestion.id;
        this.eliminatedOption = data.eliminated_option;
        this.applyEliminatedOption(data.eliminated_option, data.eliminated_text);

        if (typeof SoundFX !== 'undefined' && SoundFX.playCoin) {
          SoundFX.playCoin();
        }

        const headingEl = document.getElementById('hintModalEliminatedHeading');
        const textEl = document.getElementById('hintModalEliminatedText');
        const tipEl = document.getElementById('hintModalTip');
        const badgeEl = document.getElementById('hintModalStatusBadge');

        if (headingEl) {
          headingEl.innerHTML = `❌ Option ${data.eliminated_option} Eliminated!`;
        }
        if (textEl && data.eliminated_text) {
          textEl.innerText = `"${data.eliminated_text}" is incorrect.`;
        }
        if (tipEl && data.hint && data.hint.tip) {
          tipEl.innerText = data.hint.tip;
        }
        if (badgeEl) {
          badgeEl.innerHTML = `<svg class="exam-icon me-0.5 icon-11"><use href="#icon-check"></use></svg> Option ${data.eliminated_option} Eliminated`;
        }

        const hintModalEl = document.getElementById('hintModal');
        if (hintModalEl) {
          bootstrap.Modal.getOrCreateInstance(hintModalEl).show();
        }
      })
      .catch(err => {
        console.error(err);
        alert('Failed to connect to server. Please try again.');
      })
      .finally(() => {
        if (hintBtn) {
          hintBtn.disabled = false;
          hintBtn.innerHTML = origText;
        }
      });
  },

  bindEvents() {
    // Language tabs
    document.querySelectorAll('.btn-lang-tab').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const lang = btn.dataset.lang;
        if (!lang) return;
        document.querySelectorAll('.btn-lang-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.activeLanguage = lang;
        if (this.isQuizSession) {
          this.loadNextQuestion();
        } else {
          this.updateStartCard(lang);
        }
      });
    });

    // Options selection
    document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const expCard = document.getElementById('explanationCard') || document.getElementById('techExplanationCard');
        if (expCard && expCard.style.display === 'block') return;
        this.selectOption(btn.dataset.option);
      });
    });

    // Confidence selection
    document.querySelectorAll('.confidence-pill-col, #techConfidenceBox .confidence-pill').forEach(pill => {
      pill.addEventListener('click', () => {
        const expCard = document.getElementById('explanationCard') || document.getElementById('techExplanationCard');
        if (expCard && expCard.style.display === 'block') return;
        document.querySelectorAll('.confidence-pill-col, #techConfidenceBox .confidence-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        this.confidence = pill.dataset.confidence || 'sure';
      });
    });

    // Submit answer button (Check)
    const submitBtn = document.getElementById('submitBtn') || document.getElementById('techSubmitBtn');
    if (submitBtn) {
      submitBtn.addEventListener('click', () => this.submitAnswer());
    }

    // Next question button
    const nextBtn = document.getElementById('nextBtn') || document.getElementById('techNextBtn');
    if (nextBtn) {
      nextBtn.addEventListener('click', () => this.loadNextQuestion());
    }

    // Keyboard navigation (A, B, C, D to pick, Enter to submit/next)
    document.addEventListener('keydown', (e) => {
      if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
      const key = e.key.toUpperCase();
      if (['A', '1'].includes(key)) this.selectOption('A');
      else if (['B', '2'].includes(key)) this.selectOption('B');
      else if (['C', '3'].includes(key)) this.selectOption('C');
      else if (['D', '4'].includes(key)) this.selectOption('D');
      else if (key === 'ENTER') {
        const sBtn = document.getElementById('submitBtn');
        const nBtn = document.getElementById('nextBtn');
        if (nBtn && nBtn.style.display !== 'none') {
          this.loadNextQuestion();
        } else if (sBtn && !sBtn.disabled && sBtn.style.display !== 'none') {
          this.submitAnswer();
        }
      }
    });
  },

  selectOption(optionKey) {
    const expCard = document.getElementById('explanationCard') || document.getElementById('techExplanationCard');
    if (expCard && expCard.style.display === 'block') return;
    const btn = document.querySelector(`.option-btn[data-option="${optionKey}"]`);
    if (!btn || btn.disabled) return;

    document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    this.selectedOption = optionKey;
    if (typeof SoundFX !== 'undefined' && SoundFX.vibrate) SoundFX.vibrate(15);

    const submitBtn = document.getElementById('submitBtn') || document.getElementById('techSubmitBtn');
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.removeAttribute('aria-disabled');
      submitBtn.style.display = 'inline-flex';
    }
    const hint = document.getElementById('quizCheckHint');
    if (hint) hint.style.display = 'none';

    const nextBtn = document.getElementById('nextBtn') || document.getElementById('techNextBtn');
    if (nextBtn) nextBtn.style.display = 'none';
  },

  startTimer() {
    clearInterval(this.timerInterval);
    this.elapsedSeconds = 0;
    const timeDisplays = document.querySelectorAll('#techTimeDisplay, #practiceTimeDisplay, #sharedQuizTopTimeText');
    timeDisplays.forEach(el => el.innerText = '0s');

    this.timerInterval = setInterval(() => {
      this.elapsedSeconds++;
      const mins = Math.floor(this.elapsedSeconds / 60);
      const secs = this.elapsedSeconds % 60;
      const str = mins > 0 ? `${mins}m ${secs}s` : `${secs}s`;
      timeDisplays.forEach(el => el.innerText = str);
    }, 1000);
  },

  stopTimer() {
    clearInterval(this.timerInterval);
  },

  loadNextQuestion() {
    clearInterval(this.countdownInterval);
    this.countdownInterval = null;
    const autoCd = document.getElementById('autoNextCountdownBadge') || document.getElementById('techAutoCountdown');
    if (autoCd) autoCd.style.display = 'none';

    this.selectedOption = null;
    const submitBtn = document.getElementById('submitBtn') || document.getElementById('techSubmitBtn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.setAttribute('aria-disabled', 'true');
      submitBtn.innerHTML = '<span class="btn-quiz-primary-text">Check</span>';
      submitBtn.classList.remove('d-none');
      submitBtn.style.display = 'inline-flex';
    }
    const hint = document.getElementById('quizCheckHint');
    if (hint) hint.style.display = '';

    const nextBtn = document.getElementById('nextBtn') || document.getElementById('techNextBtn');
    if (nextBtn) {
      nextBtn.classList.add('d-none');
      nextBtn.style.display = 'none';
    }

    const expCard = document.getElementById('explanationCard');
    if (expCard) expCard.style.display = 'none';
    const techExp = document.getElementById('techExplanationCard');
    if (techExp) techExp.style.display = 'none';

    const optsContainer = document.getElementById('optionsContainer') || document.getElementById('techOptionsContainer');
    if (optsContainer) optsContainer.style.display = 'flex';
    const confBox = document.getElementById('techConfidenceBox');
    if (confBox) confBox.style.display = 'block';

    // Reset confidence to sure by default and update UI
    this.confidence = 'sure';
    document.querySelectorAll('.confidence-pill-col, #techConfidenceBox .confidence-pill').forEach(p => {
      if (p.dataset.confidence === 'sure') {
        p.classList.add('active');
      } else {
        p.classList.remove('active');
      }
    });

    this.hintUnlockedQuestionId = null;
    this.eliminatedOption = null;
    document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(b => {
      b.classList.remove('selected', 'correct', 'wrong', 'eliminated');
      b.disabled = false;
      b.removeAttribute('aria-disabled');
      const bBadge = b.querySelector('.eliminated-badge');
      if (bBadge) bBadge.remove();
    });

    let url = 'index.php?action=api_next_question&track=technical';
    if (this.activeLanguage && this.activeLanguage !== 'all') {
      url += `&language=${encodeURIComponent(this.activeLanguage)}`;
    }
    if (this.activeDifficulty && this.activeDifficulty !== 'adaptive') {
      url += `&difficulty=${encodeURIComponent(this.activeDifficulty)}`;
    }
    if (this.seenQuestionIds && this.seenQuestionIds.length > 0) {
      url += `&exclude=${encodeURIComponent(this.seenQuestionIds.slice(-200).join(','))}`;
    }

    fetch(url)
      .then(r => r.json())
      .then(res => {
        if (res.status === 'guest_limit_reached') {
          alert(res.message);
          window.location.href = 'index.php?action=register';
          return;
        }
        if (res.status === 'empty') {
          const qText = document.getElementById('questionText') || document.getElementById('techQuestionText');
          if (qText) {
            qText.innerHTML = `
              <div class="text-center py-4">
                <h4 class="text-success fw-bold mb-2"><svg class="exam-icon text-success me-1 icon-20"><use href="#icon-sparkles"></use></svg> Category Completed!</h4>
                <p class="text-muted mb-3">You have mastered all currently available questions in this technical track.</p>
                <button class="btn btn-primary rounded-pill px-4" onclick="filterByLanguage('all')">Practice All Categories</button>
              </div>
            `;
          }
          const codeBox = document.getElementById('practiceCodeContainer') || document.getElementById('techCodeContainer');
          if (codeBox) codeBox.style.display = 'none';
          if (optsContainer) optsContainer.style.display = 'none';
          if (confBox) confBox.style.display = 'none';
          if (submitBtn) submitBtn.style.display = 'none';
          return;
        }

        const data = res.data;
        if (data && data.id && !this.seenQuestionIds.includes(data.id)) {
          this.seenQuestionIds.push(data.id);
        }
        this.currentQuestion = data;
        this.startTime = Date.now();
        this.startTimer();

        // Update topic stamps
        const topicBadge = document.getElementById('topicBadge');
        if (topicBadge) topicBadge.innerText = `${data.topic_name} > ${data.subtopic_name}`;
        const diffBadge = document.getElementById('difficultyBadge') || document.getElementById('techDifficultyBadge');
        if (diffBadge) diffBadge.innerText = data.difficulty.toUpperCase();

        // Check Boss Banner for Hard Questions
        const bossBanner = document.getElementById('bossQuestionBanner') || document.getElementById('techBossBanner');
        if (bossBanner) {
          bossBanner.style.display = (data.difficulty === 'hard') ? 'block' : 'none';
        }

        // Question parsing: source tag stamp, full clean question prompt
        const parsed = data.parsed_question || (window.QuizShell ? QuizShell.parseQuestion(data.question_text) : { prompt: data.question_text, fullText: data.question_text });
        const sourceTagText = parsed.sourceTag || parsed.source_tag || (data.company_name && data.company_name !== 'General' ? data.company_name : null);
        const tagWrap = document.getElementById('quizSourceTagWrap');
        const tagStamp = document.getElementById('quizSourceTag');
        if (sourceTagText && sourceTagText.trim() !== '' && tagWrap && tagStamp) {
          tagWrap.style.display = 'flex';
          tagStamp.style.display = 'inline-block';
          tagStamp.innerText = sourceTagText.trim();
        } else {
          if (tagStamp) tagStamp.style.display = 'none';
          if (tagWrap) tagWrap.style.display = 'none';
        }

        const fullQuestionText = parsed.fullText || parsed.full_text || parsed.prompt || data.question_text || '';
        const setupWrap = document.getElementById('quizQuestionSetup');
        const qTextEl = document.getElementById('questionText');
        if (setupWrap) setupWrap.style.display = 'none';
        if (qTextEl) {
          qTextEl.classList.remove('is-prompt');
          qTextEl.innerHTML = (window.QuizShell && QuizShell.formatInlineCode) ? QuizShell.formatInlineCode(fullQuestionText) : fullQuestionText;
        }

        // Render Code Snippet ONLY if non-trivial
        const codeBox = document.getElementById('practiceCodeContainer') || document.getElementById('techCodeContainer');
        const codeEl = document.getElementById('practiceCodeSnippet') || document.getElementById('techCodeSnippet');
        const langLabel = document.getElementById('practiceCodeLang') || document.getElementById('codeSnippetLang');
        const isTrivial = data.is_trivial_code !== undefined ? data.is_trivial_code : QuizShell.isTrivialCode(data.code_snippet, data.question_text);

        if (data.code_snippet && data.code_snippet.trim() !== '' && !isTrivial) {
          if (codeBox) codeBox.style.display = 'block';
          if (codeEl) codeEl.innerText = data.code_snippet;
          if (langLabel) langLabel.innerText = (data.language || 'Code').toUpperCase();
        } else {
          if (codeBox) codeBox.style.display = 'none';
        }

        // Populate options with safe inline code formatting
        ['A', 'B', 'C', 'D'].forEach(opt => {
          const optEl = document.querySelector(`.option-btn[data-option="${opt}"] .option-text, .tech-opt-btn[data-option="${opt}"] .option-text`);
          if (optEl && data.options && data.options[opt]) {
            optEl.innerHTML = QuizShell.formatInlineCode(data.options[opt]);
          }
        });
      })
      .catch(err => {
        console.error('Error fetching technical question:', err);
        const qText = document.getElementById('questionText');
        if (qText) {
          qText.innerHTML = `
            <div class="text-center py-4">
              <div class="mb-3 text-warning">
                <svg class="exam-icon" style="width:36px;height:36px;color:var(--marker-orange);"><use href="#icon-clock"></use></svg>
              </div>
              <h4 class="text-ink fw-bold mb-2 font-serif">Unable to load question</h4>
              <p class="text-ink-muted mb-4 fs-7" style="max-width: 440px; margin: 0 auto;">
                We encountered a connection issue or the session timed out. Please check your connection and try again.
              </p>
              <div class="d-flex justify-content-center flex-wrap gap-2">
                <button type="button" class="btn btn-primary-gamified px-4 py-2 fw-bold d-inline-flex align-items-center" onclick="TechnicalEngine.loadNextQuestion()">
                  <svg class="exam-icon me-1 icon-14"><use href="#icon-arrow-repeat"></use></svg> Retry
                </button>
                <a href="index.php?action=technical" class="btn btn-outline-gamified px-4 py-2 fw-bold d-inline-flex align-items-center">
                  <svg class="exam-icon me-1 icon-14"><use href="#icon-grid"></use></svg> Back to Hub
                </a>
              </div>
            </div>
          `;
        }
      });
  },

  submitAnswer() {
    if (!this.selectedOption || !this.currentQuestion) return;
    this.stopTimer();

    const submitBtn = document.getElementById('submitBtn') || document.getElementById('techSubmitBtn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.setAttribute('aria-disabled', 'true');
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Checking...';
    }

    const payload = {
      question_id: this.currentQuestion.id,
      selected_option: this.selectedOption,
      confidence: this.confidence,
      time_taken_seconds: this.elapsedSeconds,
      csrf_token: window.CSRF_TOKEN || ''
    };

    fetch('index.php?action=api_submit_answer', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success') {
          const isCorrect = res.is_correct;
          const correctOpt = res.correct_option;

          // Sound effects & confetti
          if (isCorrect) {
            if (typeof launchConfetti === 'function') launchConfetti();
            if (window.SoundFX) SoundFX.playCorrect();
          } else {
            if (window.SoundFX) SoundFX.playIncorrect();
          }

          // Highlight buttons
          document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(btn => {
            btn.disabled = true;
            const opt = btn.dataset.option;
            if (opt === correctOpt) {
              btn.classList.add('correct');
              btn.classList.remove('eliminated');
              const elimBadge = btn.querySelector('.eliminated-badge');
              if (elimBadge) elimBadge.remove();
            } else if (opt === this.selectedOption && !isCorrect) {
              btn.classList.add('wrong');
            }
          });

          // Show explanation card
          const expCard = document.getElementById('explanationCard') || document.getElementById('techExplanationCard');
          const resultIcon = document.getElementById('techResultIcon');
          const resultTitle = document.getElementById('techResultTitle');
          const rewardsEl = document.getElementById('techRewardsEarned');

          if (isCorrect) {
            if (typeof SoundFX !== 'undefined' && SoundFX.vibrate) SoundFX.vibrate([20, 40, 20]);
          } else {
            if (typeof SoundFX !== 'undefined' && SoundFX.vibrate) SoundFX.vibrate(60);
          }

          if (resultIcon) {
            resultIcon.innerHTML = isCorrect ? '<svg class="exam-icon text-success icon-24"><use href="#icon-check"></use></svg>' : '<svg class="exam-icon text-danger icon-24"><use href="#icon-x"></use></svg>';
          }
          if (resultTitle) {
            resultTitle.innerText = isCorrect ? 'Correct! Brilliant Code Analysis' : 'Incorrect Analysis';
            resultTitle.className = isCorrect ? 'fw-bold m-0 font-serif text-success' : 'fw-bold m-0 font-serif text-danger';
          }

          if (rewardsEl) {
            let xpBadge = '';
            if (res.xp_earned > 0) {
              xpBadge = `<span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill">+${res.xp_earned} XP</span>`;
            } else if (res.xp_earned < 0) {
              xpBadge = `<span class="badge bg-danger text-white px-2.5 py-1 rounded-pill">${res.xp_earned} XP (Overconfidence)</span>`;
            }
            let coinBadge = (res.coins_earned > 0) ? `<span class="badge bg-success text-white px-2.5 py-1 rounded-pill">+${res.coins_earned} Coins</span>` : '';
            rewardsEl.innerHTML = `${xpBadge} ${coinBadge}`;
          }

          if (res.xp_earned < 0 && typeof showFloatingXP === 'function') {
            showFloatingXP(res.xp_earned, submitBtn);
          }

          const expText = document.getElementById('explanationText') || document.getElementById('techExplanationBody');
          if (expText) {
            expText.innerHTML = QuizShell.formatInlineCode(res.explanation);
          }
          if (expCard) {
            expCard.style.display = 'block';
            setTimeout(() => {
              expCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
          }

          // Toggle buttons: hide submit, show Next
          if (submitBtn) {
            submitBtn.classList.add('d-none');
            submitBtn.style.display = 'none';
          }
          const nextBtn = document.getElementById('nextBtn') || document.getElementById('techNextBtn');
          if (nextBtn) {
            nextBtn.classList.remove('d-none');
            nextBtn.style.display = 'inline-flex';
            nextBtn.innerHTML = '<span class="btn-quiz-primary-text">Next</span> <svg class="exam-icon ms-1 icon-14"><use href="#icon-arrow-right"></use></svg>';
            nextBtn.focus();
          }

          // Start 10s auto-countdown
          this.startAutoNextCountdown();

          // Refresh Language Stats
          this.loadLanguageStats();
        }
      })
      .catch(err => {
        console.error('Error submitting answer:', err);
        if (submitBtn) submitBtn.disabled = false;
      });
  },

  startAutoNextCountdown() {
    clearInterval(this.countdownInterval);
    this.countdownSeconds = 4;
    const badge = document.getElementById('autoNextCountdownBadge') || document.getElementById('techAutoCountdown');
    const text = document.getElementById('autoNextSecondsText') || document.getElementById('techCountdownSecs');
    if (badge) badge.style.display = 'inline-flex';
    if (text) text.innerText = '4';

    this.countdownInterval = setInterval(() => {
      this.countdownSeconds--;
      if (text) text.innerText = this.countdownSeconds;
      if (this.countdownSeconds <= 0) {
        clearInterval(this.countdownInterval);
        this.countdownInterval = null;
        this.loadNextQuestion();
      }
    }, 1000);
  },

  loadLanguageStats() {
    fetch('index.php?action=api_technical_stats')
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success' && res.data) {
          const list = document.getElementById('languageStatsList');
          if (!list) return;

          let html = '';
          const stats = res.data;
          for (const key in stats) {
            if (key === 'all') continue;
            const item = stats[key];

            // If it's Innovature IT Company, display in dedicated IT Company card
            if (key === 'innovature' || item.category === 'company') {
              const innoDisplay = document.getElementById('innovatureProgressDisplay');
              if (innoDisplay) {
                innoDisplay.innerHTML = `
                  <div class="d-flex justify-content-between align-items-center extra-small mb-1">
                    <span class="fw-semibold text-ink"><i class="bi ${item.icon} me-1" style="color: ${item.color};"></i> ${item.name}</span>
                    <span class="text-muted extra-small">${item.user_correct} / ${item.total_questions} (${item.progress_percent}%)</span>
                  </div>
                  <div class="progress" style="height: 6px; border-radius: 4px; background: var(--border);">
                    <div class="progress-bar bg-success" role="progressbar" aria-label="Innovature Labs Practice Progress" aria-valuenow="${item.progress_percent}" aria-valuemin="0" aria-valuemax="100" style="width: ${item.progress_percent}%;"></div>
                  </div>
                `;
              }
              continue; // Do NOT include in Language Mastery
            }

            // Real Programming Languages
            html += `
              <div>
                <div class="d-flex justify-content-between align-items-center small mb-1">
                  <span class="fw-semibold text-ink"><i class="bi ${item.icon} me-1" style="color: ${item.color};"></i> ${item.name}</span>
                  <span class="text-muted extra-small">${item.user_correct} / ${item.total_questions} Solved (${item.progress_percent}%)</span>
                </div>
                <div class="progress" style="height: 6px; border-radius: 4px; background: var(--border);">
                  <div class="progress-bar" role="progressbar" aria-label="${item.name} Mastery Progress" aria-valuenow="${item.progress_percent}" aria-valuemin="0" aria-valuemax="100" style="width: ${item.progress_percent}%; background-color: ${item.color};"></div>
                </div>
              </div>
            `;
          }
          list.innerHTML = html;
        }
      })
      .catch(err => console.warn('Could not load technical stats', err));
  }
};

function filterByLanguage(lang) {
  TechnicalEngine.activeLanguage = lang;
  document.querySelectorAll('.btn-lang-tab').forEach(b => {
    b.classList.toggle('active', b.dataset.lang === lang);
  });
  if (TechnicalEngine.isQuizSession) {
    TechnicalEngine.loadNextQuestion();
  } else {
    TechnicalEngine.updateStartCard(lang);
  }
}

function setTechDifficulty(mode) {
  TechnicalEngine.activeDifficulty = mode;
  const labelMap = {
    'adaptive': 'Difficulty: Adaptive',
    'hard': 'Challenger (Hard Only)',
    'medium': 'Difficulty: Medium'
  };
  document.getElementById('currentDiffModeText').innerText = labelMap[mode] || 'Difficulty: Adaptive';
  TechnicalEngine.loadNextQuestion();
}

function copySnippetCode() {
  const code = document.getElementById('techCodeSnippet').innerText;
  navigator.clipboard.writeText(code).then(() => {
    alert('Code snippet copied to clipboard!');
  });
}

window.TechnicalEngine = TechnicalEngine;

document.addEventListener('DOMContentLoaded', () => {
  TechnicalEngine.init();
});
</script>
