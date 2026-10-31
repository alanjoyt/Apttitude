<?php
/**
 * Shared Quiz Shell Component
 * Unified layout, typography, options, code blocks, confidence row, and action bar.
 * Used across Practice, Technical, and adaptable for Mock/Challenge quizzes.
 *
 * Variables passed in:
 * - $quizContext: 'practice' | 'technical' | 'mock'
 */
$isTech = ($quizContext ?? '') === 'technical';
?>
<!-- QUIZ FOCUS WRAPPER (100dvh flex column on mobile <768px, clean card on desktop >=768px) -->
<div class="quiz-focus-wrapper" id="quizFocusWrapper">

  <!-- ======================================================================
       1. SLIM QUIZ TOP BAR (Sticky, 48px height)
       [X Exit] [Progress Bar] [Timer] [Aa Text Size]
       ====================================================================== -->
  <div class="quiz-top-bar" id="sharedQuizTopBar">
    <div class="quiz-top-bar-inner d-flex align-items-center justify-content-between w-100">
      <!-- [Exit & Home Navigation] -->
      <div class="d-flex align-items-center gap-1 flex-shrink-0">
        <button type="button" class="btn-quiz-bar-exit" id="quizExitBtn" onclick="handleQuizExit(event)" aria-label="Exit Quiz" title="Exit Quiz">
          <i class="bi bi-x-lg"></i>
        </button>
        <button type="button" class="btn-quiz-bar-home" id="quizHomeBtn" onclick="handleQuizHome(event)" aria-label="Home" title="Back to Home Page">
          <i class="bi bi-house-door-fill"></i>
        </button>
      </div>

      <!-- [Progress Bar] -->
      <div class="quiz-bar-progress-container flex-grow-1 mx-2 mx-sm-3">
        <div class="progress" style="height: 6px; background-color: var(--border-strong); border-radius: 999px; overflow: hidden;">
          <div class="progress-bar" id="mobileQuizProgressBar" role="progressbar" style="width: 25%; background-color: var(--marker-orange); transition: width 0.3s ease;"></div>
        </div>
      </div>

      <!-- [Timer] -->
      <div class="quiz-bar-timer d-flex align-items-center gap-1 me-2" id="sharedQuizTopTimer">
        <svg class="exam-icon text-danger icon-14"><use href="#icon-clock"></use></svg>
        <span class="quiz-time-display font-mono fw-bold fs-7" id="sharedQuizTopTimeText">0s</span>
      </div>

      <!-- [Aa Text Size Reading Control] -->
      <button type="button" class="btn-quiz-text-size" id="quizTextSizeBtn" onclick="cycleQuizTextSize()" aria-label="Text size" title="Cycle question text size (Small, Medium, Large)">
        Aa
      </button>
    </div>
  </div>

  <!-- ======================================================================
       2. SCROLLING CONTENT ZONE (flex: 1, overflow-y: auto)
       Contains Topic Strip, Boss Banner, Question, Code, Options, Confidence, Explanation
       ====================================================================== -->
  <div class="quiz-scroll-zone" id="quizScrollZone">
    <div class="quiz-sheet-card card practice-sheet-card p-3 p-sm-4" id="practiceCard">
      <!-- Hidden technical card ID alias for backwards compatibility -->
      <span id="technicalQuestionCard" class="d-none"></span>

      <!-- Boss Question Event Banner -->
      <div class="alert border rounded-3 p-2 mb-2 text-center" id="bossQuestionBanner" style="display: none; background-color: var(--surface); border: 2px solid var(--marker-orange) !important; color: var(--ink);">
        <div class="d-flex align-items-center justify-content-center gap-2 fw-bold fs-7">
          <i class="bi bi-lightning-charge-fill text-accent"></i>
          <span>BOSS QUESTION! Double Rewards (2x XP + 2x Coins)</span>
          <svg class="exam-icon text-accent icon-16"><use href="#icon-flame"></use></svg>
        </div>
      </div>
      <span id="techBossBanner" class="d-none"></span>

      <!-- Compact Unified Status Row (Topic stamp, difficulty selector, live timer) -->
      <div class="practice-status-row d-flex align-items-center justify-content-between gap-1 mb-3">
        <div class="d-flex align-items-center gap-1.5 min-w-0" style="max-width: 65%;">
          <span class="stamp-topic-truncated text-truncate" id="topicBadge" title="Topic">
            Loading Topic...
          </span>
          <!-- Tech Subtopic badge alias -->
          <span id="techSubtopicBadge" class="d-none"></span>

          <span class="badge bg-danger text-white px-2 py-0.5 fs-8" id="sessionComboDisplay" style="display: none;">
            <svg class="exam-icon icon-12"><use href="#icon-flame"></use></svg> 2x
          </span>
          <span id="techComboBadge" class="d-none"></span>
        </div>

        <div class="status-pill-group d-flex align-items-center gap-1 flex-shrink-0">
          <!-- Difficulty Selector -->
          <div class="dropdown">
            <button class="btn-status-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="practiceDiffDropdownBtn">
              <i class="bi bi-sliders me-0.5"></i> <span id="practiceDiffModeText">Adaptive</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow p-1 fs-7">
              <li><a class="dropdown-item py-1" href="#" onclick="setQuizDifficulty('adaptive')">Adaptive</a></li>
              <li><a class="dropdown-item py-1 text-danger fw-bold" href="#" onclick="setQuizDifficulty('hard')">Challenger</a></li>
              <li><a class="dropdown-item py-1 text-primary" href="#" onclick="setQuizDifficulty('medium')">Medium</a></li>
            </ul>
          </div>
          <span id="diffModeDropdownBtn" class="d-none"></span>
          <span id="currentDiffModeText" class="d-none"></span>

          <!-- Live Practice Timer -->
          <span class="status-pill-timer" id="practiceTimerPill" title="Time Spent on Current Question">
            <svg class="exam-icon text-danger me-0.5 icon-12"><use href="#icon-clock"></use></svg> 
            <span class="practice-time-display font-mono" id="practiceTimeDisplay">0s</span>
          </span>
          <!-- Tech timer aliases -->
          <span id="techTimerBadge" class="d-none"><span id="techTimeDisplay">0s</span></span>

          <span class="status-pill-diff" id="difficultyBadge">
            MED
          </span>
          <span id="techDifficultyBadge" class="d-none">MED</span>
        </div>
      </div>

      <!-- ==================================================================
           QUESTION STEM CONTAINER
           - Source tag stamp (e.g. "Innovature Labs · Logic PYQ")
           - Setup context sentences (with 8px spacing)
           - Prompt question (semibold, Atkinson Hyperlegible, top dashed rule if split)
           ================================================================== -->
      <div class="quiz-question-container mb-3" id="quizQuestionContainer">
        <!-- Leading Bracket Prefix Source Tag Stamp & Verification Badges -->
        <div class="quiz-source-tag-wrap mb-2 d-flex flex-wrap align-items-center gap-1.5" id="quizSourceTagWrap" style="display: none;">
          <span class="quiz-source-tag-stamp" id="quizSourceTag"></span>
          <span id="verificationBadgeWrap"></span>
          <span id="roundPatternBadge" class="badge rounded-pill border px-2 py-1 fs-8" style="display: none; background-color: var(--surface); color: var(--ink-muted); border-color: var(--border) !important;"></span>
          <span id="techSourceTag" class="d-none"></span>
        </div>

        <!-- Setup context sentences for puzzles/long questions -->
        <div class="quiz-question-setup mb-2" id="quizQuestionSetup" style="display: none;"></div>

        <!-- Main Question Text / Prompt -->
        <h4 class="question-stem-text m-0" id="questionText">
          Loading your question...
        </h4>
        <!-- Tech question text alias -->
        <span id="techQuestionText" class="d-none"></span>

        <!-- Code Snippet Box (Token colors, horizontal scroll, 44x44 copy button, NO macOS dots) -->
        <div class="tech-code-container mt-3 mb-2" id="practiceCodeContainer" style="display: none;">
          <div class="code-terminal-header d-flex justify-content-between align-items-center px-3 py-1.5">
            <span class="code-lang-label" id="practiceCodeLang">CODE</span>
            <span id="codeSnippetLang" class="d-none"></span>

            <button type="button" class="btn-copy-code" id="codeCopyBtn" onclick="copySnippetCode()" aria-label="Copy code" title="Copy code">
              <svg class="exam-icon copy-icon icon-16"><use href="#icon-copy"></use></svg>
              <svg class="exam-icon check-icon d-none" style="width:16px;height:16px;color:var(--success);"><use href="#icon-check"></use></svg>
            </button>
          </div>
          <pre class="m-0 p-3" tabindex="0" aria-label="Code snippet"><code class="tech-code-body font-mono" id="practiceCodeSnippet"></code></pre>
          <span id="techCodeSnippet" class="d-none"></span>
        </div>
        <!-- Tech code container alias -->
        <span id="techCodeContainer" class="d-none"></span>
      </div>

      <!-- ==================================================================
           OMR OPTIONS GRID (28px circle bubble, min 56px tap row, 24px right padding)
           ================================================================== -->
      <div class="options-grid mb-3" id="optionsContainer">
        <!-- Tech options container alias -->
        <span id="techOptionsContainer" class="d-none"></span>

        <button class="option-btn tech-opt-btn" data-option="A" type="button">
          <span class="option-key">A</span>
          <span class="option-text">Option A</span>
        </button>
        <button class="option-btn tech-opt-btn" data-option="B" type="button">
          <span class="option-key">B</span>
          <span class="option-text">Option B</span>
        </button>
        <button class="option-btn tech-opt-btn" data-option="C" type="button">
          <span class="option-key">C</span>
          <span class="option-text">Option C</span>
        </button>
        <button class="option-btn tech-opt-btn" data-option="D" type="button">
          <span class="option-key">D</span>
          <span class="option-text">Option D</span>
        </button>
      </div>

      <!-- ==================================================================
           CONFIDENCE ROW (3 Equal Columns, 44px tall, SVG icons, optional)
           ================================================================== -->
      <div class="confidence-container mb-3" id="techConfidenceBox">
        <label class="small fw-bold d-block text-center mb-1.5 text-uppercase tracking-wider text-ink-muted" style="font-size: 0.72rem;">Confidence Level (Optional)</label>
        <div class="confidence-group-3col">
          <button type="button" class="confidence-pill-col active" data-confidence="sure" aria-label="Sure +5 / -5 XP">
            <div class="conf-btn-content">
              <span class="conf-label-row d-inline-flex align-items-center justify-content-center gap-1">
                <svg class="exam-icon icon-13"><use href="#icon-target"></use></svg>
                <span class="conf-title">Sure</span>
              </span>
              <span class="conf-xp-sub">+5 / -5 XP</span>
            </div>
          </button>
          <button type="button" class="confidence-pill-col" data-confidence="not_sure" aria-label="Unsure 0 XP risk">
            <div class="conf-btn-content">
              <span class="conf-label-row d-inline-flex align-items-center justify-content-center gap-1">
                <svg class="exam-icon icon-13"><use href="#icon-help"></use></svg>
                <span class="conf-title">Unsure</span>
              </span>
              <span class="conf-xp-sub">0 XP risk</span>
            </div>
          </button>
          <button type="button" class="confidence-pill-col" data-confidence="guessing" aria-label="Guess 0 XP risk">
            <div class="conf-btn-content">
              <span class="conf-label-row d-inline-flex align-items-center justify-content-center gap-1">
                <svg class="exam-icon icon-13"><use href="#icon-dice"></use></svg>
                <span class="conf-title">Guess</span>
              </span>
              <span class="conf-xp-sub">0 XP risk</span>
            </div>
          </button>
        </div>
      </div>

      <!-- ==================================================================
           INSTANT EXPLANATION CARD (Hidden initially)
           ================================================================== -->
      <div class="explanation-card mt-3 p-3 border rounded-4" id="explanationCard" style="display: none; background-color: var(--surface); border-color: var(--border) !important;">
        <span id="techExplanationCard" class="d-none"></span>
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <span id="techResultIcon" class="fs-5"></span>
            <h6 class="font-serif fw-bold d-flex align-items-center gap-2 mb-0" id="techResultTitle" style="color: var(--accent);">
              <svg class="exam-icon text-warning icon-16"><use href="#icon-lightbulb"></use></svg> Step-by-Step Explanation
            </h6>
          </div>
          <div class="d-flex align-items-center gap-2">
            <div id="techRewardsEarned"></div>
            <span class="badge rounded-pill px-2.5 py-1 small fw-bold" id="autoNextCountdownBadge" style="display: none; background-color: var(--highlight-tint); color: #8c6317; border: 1px solid var(--highlight);">
              <i class="bi bi-clock-history me-1"></i> Next in <span id="autoNextSecondsText">10</span>s
            </span>
            <span id="techAutoCountdown" class="d-none"><span id="techCountdownSecs">10</span></span>
          </div>
        </div>
        <div class="mb-0 fs-6 lh-relaxed text-ink" id="explanationText" style="white-space: pre-line;">
          Detailed walkthrough will appear here after checking.
        </div>
        <span id="techExplanationBody" class="d-none"></span>
      </div>

      <!-- 1-Tap Mistake Categorization Tagger (Shown on wrong answer) -->
      <div class="mistake-tagger-card mt-3 p-3 border rounded-3" id="mistakeTagCard" style="display: none; background-color: var(--danger-tint); border: 1.5px solid var(--danger) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <label class="small fw-bold mb-0" style="color: var(--danger);">
            <i class="bi bi-exclamation-octagon-fill me-1"></i> What caused this miss? Tag to prioritize revision:
          </label>
          <span class="badge text-white small rounded-pill px-2 py-1" id="mistakeTaggedBadge" style="display: none; background-color: var(--success);"><svg class="exam-icon me-1 icon-11"><use href="#icon-check"></use></svg> Tag Saved</span>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2" id="mistakeTagButtons">
          <button type="button" class="btn btn-outline-gamified btn-sm mistake-tag-btn" data-type="concept_gap">
            <svg class="exam-icon me-1 icon-14"><use href="#icon-brain"></use></svg> Concept Gap
          </button>
          <button type="button" class="btn btn-outline-gamified btn-sm mistake-tag-btn" data-type="calculation_error">
            <svg class="exam-icon me-1 icon-14"><use href="#icon-calculator"></use></svg> Calculation Error
          </button>
          <button type="button" class="btn btn-outline-gamified btn-sm mistake-tag-btn" data-type="misread_question">
            <svg class="exam-icon me-1 icon-14"><use href="#icon-eye"></use></svg> Misread Question
          </button>
          <button type="button" class="btn btn-outline-gamified btn-sm mistake-tag-btn" data-type="ran_out_of_time">
            <svg class="exam-icon me-1 icon-14"><use href="#icon-hourglass"></use></svg> Ran Out of Time
          </button>
        </div>
      </div>

    </div>
  </div>

  <!-- ======================================================================
       3. PINNED BOTTOM ACTION BAR
       Fixed height, always visible, safe-area padding bottom
       [Skip] [Hint / Report] [Check -> Next]
       ====================================================================== -->
  <div class="quiz-bottom-action-bar" id="quizBottomActionBar">
    <div class="quiz-action-bar-inner d-flex align-items-center justify-content-between w-100">
      <!-- Quiet Skip Button on Left -->
      <button type="button" class="btn-quiz-skip" id="quizSkipBtn" onclick="handleQuizSkip()" title="Skip Question">
        Skip
      </button>
      <span id="techSkipBtn" class="d-none"></span>

      <!-- Secondary Utilities (Hint, Report, Save, Exit) -->
      <div class="quiz-utility-tools d-flex align-items-center gap-1 gap-sm-1.5">
        <!-- 1. Hint Button: Lifeline (Eliminate 1 Wrong Option) -->
        <button type="button" class="btn-quiz-util" id="formulaHintBtn" onclick="openQuizHint()" title="Eliminate 1 Wrong Option (5 Speed Coins)" aria-label="Hint (5¢) - Eliminate 1 Wrong Option">
          <svg class="exam-icon text-warning icon-16"><use href="#icon-lightbulb"></use></svg>
          <span class="btn-quiz-util-label">Hint (5¢)</span>
        </button>

        <!-- 2. Report Question Button -->
        <button type="button" class="btn-quiz-util" id="reportQuestionBtn" data-bs-toggle="modal" data-bs-target="#reportModal" title="Report Question" aria-label="Report Question">
          <svg class="exam-icon text-danger icon-15"><use href="#icon-flag"></use></svg>
          <span class="btn-quiz-util-label">Report</span>
        </button>

        <!-- 3. Save Progress Button -->
        <button type="button" class="btn-quiz-util" id="saveProgressBtn" onclick="saveQuizProgress()" title="Save Progress" aria-label="Save Progress">
          <svg class="exam-icon text-accent icon-15"><use href="#icon-save"></use></svg>
          <span class="btn-quiz-util-label">Save</span>
        </button>

        <!-- 5. Exit Button -->
        <button type="button" class="btn-quiz-util btn-quiz-util-exit" id="quizBottomExitBtn" onclick="handleQuizExit(event)" title="Save & Exit Quiz" aria-label="Save & Exit Quiz">
          <svg class="exam-icon text-ink-muted icon-15"><use href="#icon-exit"></use></svg>
          <span class="btn-quiz-util-label">Exit</span>
        </button>
      </div>

      <!-- ONE Primary Action on Right -->
      <div class="quiz-primary-action-wrap position-relative">
        <!-- Before submitting: Check button with aria-disabled reason text -->
        <button type="button" class="btn btn-primary-gamified btn-quiz-primary" id="submitBtn" disabled aria-disabled="true" data-reason="Pick an answer">
          <span class="btn-quiz-primary-text">Check</span>
        </button>
        <span id="techSubmitBtn" class="d-none"></span>

        <!-- After submitting: Next button -->
        <button type="button" class="btn btn-primary-gamified btn-quiz-primary d-none" id="nextBtn" onclick="handleQuizNext()">
          <span class="btn-quiz-primary-text">Next</span>
          <svg class="exam-icon ms-1 icon-14"><use href="#icon-arrow-right"></use></svg>
        </button>
        <span id="techNextBtn" class="d-none"></span>

        <!-- Accessible tooltip reason when disabled -->
        <div class="quiz-check-hint" id="quizCheckHint" role="status" aria-live="polite">Pick an answer</div>
      </div>
    </div>
  </div>

</div>

<!-- ======================================================================
     SHARED QUIZ MODALS (Report & Formula Hint)
     Available across all routes (Practice, Technical, Mock)
     ====================================================================== -->
<!-- Report Question Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border: 2px solid var(--border-strong) !important;">
      <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
        <h5 class="modal-title font-serif fw-bold" id="reportModalLabel">
          <svg class="exam-icon text-danger me-2 icon-18"><use href="#icon-flag"></use></svg> Report Question
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="reportForm" onsubmit="event.preventDefault(); submitReport();">
          <div class="mb-3">
            <label class="form-label small fw-bold text-ink">Reason for Report</label>
            <select class="form-select" id="reportReason" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
              <option value="wrong_answer">Incorrect Answer Marked</option>
              <option value="typo">Typo or Formatting Error</option>
              <option value="vague_explanation">Unclear Explanation</option>
              <option value="duplicate_options">Duplicate / Ambiguous Options</option>
              <option value="other">Other Issue</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-ink">Additional Details (Optional)</label>
            <textarea class="form-control" id="reportComment" rows="3" placeholder="Describe what looks wrong so we can fix it immediately..." style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer border-top d-flex justify-content-between" style="border-color: var(--border) !important;">
        <button type="button" class="btn btn-outline-gamified btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary-gamified btn-sm fw-bold px-3" id="reportSubmitBtn" onclick="submitReport()">
          <svg class="exam-icon me-1 icon-13"><use href="#icon-check"></use></svg> Submit Report
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Eliminate 1 Wrong Option Lifeline Modal -->
<div class="modal fade" id="hintModal" tabindex="-1" aria-labelledby="hintModalTitle" aria-hidden="true" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border: 2px solid var(--border-strong) !important;">
      <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
        <h5 class="modal-title font-serif fw-bold d-flex align-items-center gap-2" id="hintModalTitle">
          <svg class="exam-icon text-warning icon-20"><use href="#icon-sparkles"></use></svg>
          <span>Eliminate 1 Wrong Option</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="border rounded-3 p-2 mb-3 d-flex align-items-center justify-content-between small" style="background-color: var(--highlight-tint); border: 1.5px solid var(--border-strong); color: var(--ink);">
          <span><svg class="exam-icon text-warning me-1 icon-14"><use href="#icon-coin"></use></svg> Cost: <strong>5 Speed Coins</strong></span>
          <span class="badge bg-success text-white" id="hintModalStatusBadge"><svg class="exam-icon me-0.5 icon-11"><use href="#icon-check"></use></svg> 1 Option Eliminated</span>
        </div>
        <div class="p-3 rounded-3 mb-3 border text-center" style="background-color: rgba(197, 48, 48, 0.08); border: 1.5px dashed var(--danger, #c53030) !important;">
          <div class="text-danger fw-bold fs-5 mb-1" id="hintModalEliminatedHeading">
            ❌ Option Eliminated
          </div>
          <p class="text-ink-muted small mb-0 font-monospace" id="hintModalEliminatedText" style="word-break: break-word;">
            An incorrect option has been removed from this question.
          </p>
        </div>
        <div class="p-3 rounded-3 border" style="background-color: var(--surface); border: 1.5px solid var(--border) !important;">
          <h6 class="font-serif fw-bold text-ink mb-1 d-flex align-items-center gap-1.5">
            <svg class="exam-icon text-success icon-15"><use href="#icon-target"></use></svg>
            <span>3 Options Remaining</span>
          </h6>
          <p class="text-ink-muted small mb-0 lh-base" id="hintModalTip">
            The eliminated option is crossed out and disabled on your question card. You now have a 1-in-3 chance to pick the correct answer!
          </p>
        </div>
      </div>
      <div class="modal-footer border-top" style="border-color: var(--border) !important;">
        <button type="button" class="btn btn-primary-gamified btn-sm px-4 fw-bold w-100" data-bs-dismiss="modal">Continue with 3 Options &rarr;</button>
      </div>
    </div>
  </div>
</div>

<!-- Student Question Contribution Modal -->
<div class="modal fade" id="submitQuestionModal" tabindex="-1" aria-labelledby="submitQuestionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border: 2px solid var(--border-strong) !important;">
      <div class="modal-header border-bottom align-items-start" style="border-color: var(--border) !important;">
        <div class="min-w-0 pe-2">
          <h5 class="modal-title font-serif fw-bold d-flex align-items-center gap-1.5 fs-6 mb-1 text-wrap" id="submitQuestionModalLabel">
            <svg class="exam-icon text-success flex-shrink-0 icon-18"><use href="#icon-rocket"></use></svg>
            <span>Submit Placement Question</span>
          </h5>
          <span class="badge bg-success-subtle text-success border border-success-subtle fs-8">+50 XP &bull; +10 Speed Coins</span>
        </div>
        <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <p class="text-ink-muted small mb-3">
          Encountered a memorable question in a recent campus placement drive (e.g. TCS NQT, Infosys, Innovature Labs)? Contribute it to our peer community! Once verified by content moderators, you earn <strong>50 XP</strong> and <strong>10 Speed Coins</strong>.
        </p>
        <form id="studentQuestionForm" onsubmit="event.preventDefault(); submitStudentQuestion(event);">
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-ink">Company Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-sm" id="subCompany" required placeholder="e.g. Innovature Labs, TCS, Infosys" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-ink">Drive Date</label>
              <input type="date" class="form-control form-control-sm" id="subDriveDate" value="<?= date('Y-m-d') ?>" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-ink">Difficulty</label>
              <select class="form-select form-select-sm" id="subDifficulty" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
                <option value="easy">Easy</option>
                <option value="medium" selected>Medium</option>
                <option value="hard">Hard (Boss)</option>
              </select>
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-ink">Round / Exam Pattern</label>
              <input type="text" class="form-control form-control-sm" id="subRoundPattern" placeholder="e.g. Round 1 Online Aptitude, Technical MCQ" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-ink">Topic / Category</label>
              <input type="text" class="form-control form-control-sm" id="subTopicName" placeholder="e.g. Speed Distance Time, Pointers in C, Syllogisms" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-ink">Question Stem / Problem Statement <span class="text-danger">*</span></label>
            <textarea class="form-control" id="subQuestionText" rows="3" required placeholder="Type the question as asked in the placement test..." style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-ink">Optional Code Snippet (For Technical Questions)</label>
            <textarea class="form-control font-mono fs-8" id="subCodeSnippet" rows="2" placeholder="#include <stdio.h>&#10;int main() { ... }" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);"></textarea>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-ink">Option A <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-sm" id="subOptA" required placeholder="Option A" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-ink">Option B <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-sm" id="subOptB" required placeholder="Option B" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-ink">Option C</label>
              <input type="text" class="form-control form-control-sm" id="subOptC" placeholder="Option C" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-ink">Option D</label>
              <input type="text" class="form-control form-control-sm" id="subOptD" placeholder="Option D" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-4">
              <label class="form-label small fw-bold text-ink">Correct Option <span class="text-danger">*</span></label>
              <select class="form-select form-select-sm" id="subCorrectOpt" style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
                <option value="A">Option A</option>
                <option value="B">Option B</option>
                <option value="C">Option C</option>
                <option value="D">Option D</option>
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label small fw-bold text-ink">Explanation / Solution Steps</label>
              <input type="text" class="form-control form-control-sm" id="subExplanation" placeholder="Brief step-by-step logic or derivation..." style="background-color: var(--surface); color: var(--ink); border: 1.5px solid var(--border-strong);">
            </div>
          </div>

          <div id="subAlertBox" class="alert d-none py-2 small mb-3" role="alert"></div>

          <div class="modal-footer border-top p-0 pt-3 d-flex justify-content-between" style="border-color: var(--border) !important;">
            <button type="button" class="btn btn-outline-gamified btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-gamified btn-sm fw-bold px-4" id="subSubmitBtn">
              <svg class="exam-icon me-1 icon-14"><use href="#icon-rocket"></use></svg> Submit to Moderation (+50 XP)
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script id="hintModalEscListener">
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' || e.keyCode === 27) {
    const hintModalEl = document.getElementById('hintModal');
    if (hintModalEl && hintModalEl.classList.contains('show')) {
      const modal = bootstrap.Modal.getInstance(hintModalEl);
      if (modal) modal.hide();
    }
  }
});
</script>
