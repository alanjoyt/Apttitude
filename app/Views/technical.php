<?php
// Technical Assessment Practice Hub
$activeLang = $_GET['lang'] ?? 'all';
$db = Database::getConnection();
$techSummary = $db->query("
    SELECT COUNT(*) as total_questions, COUNT(DISTINCT q.language) as total_langs
    FROM questions q
    JOIN subtopics s ON q.subtopic_id = s.id
    WHERE s.topic_id IN (5, 6) OR (q.language IS NOT NULL AND q.language != '' AND q.language NOT IN ('quant', 'lr'))
")->fetch(PDO::FETCH_ASSOC);
$totalTechQuestions = (int)($techSummary['total_questions'] ?? 216);
$totalTechLanguages = (int)($techSummary['total_langs'] ?? 9);
?>

<div class="container py-3">
  <!-- Tech Hub Header & Category Carousel (Hidden in mobile focus mode, visible on desktop) -->
  <div class="tech-hub-content mb-4">
    <!-- Page Header & Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-2 mb-3">
      <div class="min-w-0 flex-grow-1">
        <h1 class="font-serif fw-bold text-ink mb-1 tech-hero-heading" style="font-size: clamp(1.4rem, 4vw, 1.85rem);">Technical Coding &amp; Concept Mastery</h1>
        <!-- Single plain text dynamic stat line matching DB -->
        <p class="text-ink-muted small mb-0 fw-semibold">
          <?= $totalTechQuestions ?> questions &middot; <?= $totalTechLanguages ?> languages
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
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'c' ? 'active' : '' ?>" data-lang="c">
          <i class="bi bi-terminal me-1 text-info"></i> C Language
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'cpp' ? 'active' : '' ?>" data-lang="cpp">
          <i class="bi bi-cpu me-1 text-primary"></i> C++
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'java' ? 'active' : '' ?>" data-lang="java">
          <i class="bi bi-cup-hot-fill me-1 text-danger"></i> Java
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'php' ? 'active' : '' ?>" data-lang="php">
          <i class="bi bi-filetype-php me-1" style="color: #7c3aed;"></i> PHP
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'csharp' ? 'active' : '' ?>" data-lang="csharp">
          <i class="bi bi-microsoft me-1 text-primary"></i> C# (.NET)
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'python' ? 'active' : '' ?>" data-lang="python">
          <i class="bi bi-filetype-py me-1 text-success"></i> Python
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'sql' ? 'active' : '' ?>" data-lang="sql">
          <i class="bi bi-database-fill me-1 text-warning"></i> SQL &amp; DBMS
        </button>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'dsa' ? 'active' : '' ?>" data-lang="dsa">
          <i class="bi bi-diagram-3-fill me-1 text-danger"></i> DSA
        </button>

        <!-- IT Companies & Placement Drives -->
        <span class="chip-divider-text">COMPANY PYQS:</span>
        <button type="button" class="btn btn-sm btn-lang-tab <?= $activeLang === 'innovature' ? 'active' : '' ?>" data-lang="innovature">
          <i class="bi bi-building-fill-gear me-1 text-success"></i> Innovature Labs
        </button>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Left Column: Live Technical Question Solver -->
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

      <!-- Shared Quiz Shell Partial -->
      <?php
      $quizContext = 'technical';
      include __DIR__ . '/components/quiz_shell.php';
      ?>
    </div>

    <!-- Right Column: Language Mastery & PYQ Overview (Hidden in mobile focus mode, visible on desktop) -->
    <div class="col-lg-4 tech-hub-content">
      <!-- IT Company Placement PYQ Spotlight Card (Innovature Labs) -->
      <div class="card card-gamified p-3 mb-3 shadow-sm" style="background-color: var(--surface-bg); color: var(--surface-text); border: 1.5px solid var(--border-strong) !important;">
        <div class="d-flex align-items-center gap-2.5 mb-2">
          <div class="avatar-circle-sm bg-success text-white">
            <i class="bi bi-building-fill-gear"></i>
          </div>
          <div>
            <h6 class="fw-bold text-ink mb-0">Innovature Labs (IT Company Drive)</h6>
            <span class="badge bg-success text-white extra-small">Placement PYQ Track</span>
          </div>
        </div>
        <p class="small text-muted mb-2">Authentic technical challenges (C pointers, Java OOP, SQL, logical coding) asked in Innovature Labs IT recruitment rounds.</p>
        <div id="innovatureProgressDisplay" class="mb-2.5">
          <!-- Dynamically populated with live solve stats -->
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-primary-gamified btn-sm rounded-pill flex-grow-1 fw-bold py-1.5" onclick="filterByLanguage('innovature')">
            <i class="bi bi-play-circle-fill me-1"></i> Practice PYQs
          </button>
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

  init() {
    this.bindEvents();
    this.loadLanguageStats();
    this.loadNextQuestion();
  },

  bindEvents() {
    // Language tabs
    document.querySelectorAll('.btn-lang-tab').forEach(btn => {
      btn.addEventListener('click', (e) => {
        document.querySelectorAll('.btn-lang-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.activeLanguage = btn.dataset.lang;
        this.loadNextQuestion();
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

    document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(b => {
      b.classList.remove('selected', 'correct', 'wrong');
      b.disabled = false;
    });

    let url = 'index.php?action=api_next_question&track=technical';
    if (this.activeLanguage && this.activeLanguage !== 'all') {
      url += `&language=${encodeURIComponent(this.activeLanguage)}`;
    }
    if (this.activeDifficulty && this.activeDifficulty !== 'adaptive') {
      url += `&difficulty=${encodeURIComponent(this.activeDifficulty)}`;
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
                <h4 class="text-success fw-bold mb-2"><svg class="exam-icon text-success me-1" style="width:20px;height:20px;"><use href="#icon-sparkles"></use></svg> Category Completed!</h4>
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

        // Question parsing: source tag stamp, setup sentences, prompt
        const parsed = data.parsed_question || QuizShell.parseQuestion(data.question_text);
        const tagWrap = document.getElementById('quizSourceTagWrap');
        const tagStamp = document.getElementById('quizSourceTag');
        if (parsed.sourceTag && tagWrap && tagStamp) {
          tagWrap.style.display = 'block';
          tagStamp.innerText = parsed.sourceTag;
        } else if (tagWrap) {
          tagWrap.style.display = 'none';
        }

        const setupWrap = document.getElementById('quizQuestionSetup');
        const qTextEl = document.getElementById('questionText');
        if (parsed.isSplit && parsed.setupSentences && parsed.setupSentences.length > 0) {
          if (setupWrap) {
            setupWrap.style.display = 'flex';
            setupWrap.innerHTML = parsed.setupSentences.map(s => `<p class="setup-sentence mb-0">${QuizShell.formatInlineCode(s)}</p>`).join('');
          }
          if (qTextEl) {
            qTextEl.classList.add('is-prompt');
            qTextEl.innerHTML = QuizShell.formatInlineCode(parsed.prompt);
          }
        } else {
          if (setupWrap) setupWrap.style.display = 'none';
          if (qTextEl) {
            qTextEl.classList.remove('is-prompt');
            qTextEl.innerHTML = QuizShell.formatInlineCode(parsed.prompt || parsed.fullText || data.question_text);
          }
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
      time_taken_seconds: this.elapsedSeconds
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
            } else if (opt === this.selectedOption && !isCorrect) {
              btn.classList.add('wrong');
            }
          });

          // Show explanation card
          const expCard = document.getElementById('explanationCard') || document.getElementById('techExplanationCard');
          const resultIcon = document.getElementById('techResultIcon');
          const resultTitle = document.getElementById('techResultTitle');
          const rewardsEl = document.getElementById('techRewardsEarned');

          if (resultIcon) {
            resultIcon.innerHTML = isCorrect ? '<svg class="exam-icon text-success" style="width:24px;height:24px;"><use href="#icon-check"></use></svg>' : '<svg class="exam-icon text-danger" style="width:24px;height:24px;"><use href="#icon-x"></use></svg>';
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
            nextBtn.innerHTML = '<span class="btn-quiz-primary-text">Next</span> <svg class="exam-icon ms-1" style="width:14px;height:14px;"><use href="#icon-arrow-right"></use></svg>';
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
    this.countdownSeconds = 10;
    const badge = document.getElementById('autoNextCountdownBadge') || document.getElementById('techAutoCountdown');
    const text = document.getElementById('autoNextSecondsText') || document.getElementById('techCountdownSecs');
    if (badge) badge.style.display = 'inline-flex';
    if (text) text.innerText = '10';

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
                    <div class="progress-bar bg-success" role="progressbar" style="width: ${item.progress_percent}%;"></div>
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
                  <div class="progress-bar" role="progressbar" style="width: ${item.progress_percent}%; background-color: ${item.color};"></div>
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
  TechnicalEngine.loadNextQuestion();
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
