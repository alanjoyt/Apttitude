<?php
$packCode = $_GET['pack'] ?? null;
$packTitles = [
    'TCS_MOCK_PACK' => 'TCS NQT Pattern Mock Pack',
    'INFOSYS_MOCK_PACK' => 'Infosys Placement Test Pack',
    'WIPRO_MOCK_PACK' => 'Wipro NLTH Challenge Pack',
    'INNOVATURE_MOCK_PACK' => 'Innovature Labs Placement Mock Pack',
    'ACCENTURE_MOCK_PACK' => 'Accenture Placement Mock Pack',
    'AMAZON_MOCK_PACK' => 'Amazon SDE & AWS Placement Pack',
    'ZOHO_MOCK_PACK' => 'Zoho Advanced Coding & Aptitude Pack',
    'DELOITTE_MOCK_PACK' => 'Deloitte National Aptitude Assessment Pack',
    'CAPGEMINI_MOCK_PACK' => 'Capgemini Exceller Placement Pack',
    'COGNIZANT_MOCK_PACK' => 'Cognizant GenC & GenC Next Challenge Pack',
    'TECH_MAHINDRA_MOCK_PACK' => 'Tech Mahindra Recruitment Mock Pack',
    'IBM_MOCK_PACK' => 'IBM Cognitive & Entry-Level Developer Pack',
    'LTI_MINDTREE_MOCK_PACK' => 'LTI Mindtree Placement Mock Pack',
    'UST_MOCK_PACK' => 'UST Global Software Developer Pack',
    'HCL_MOCK_PACK' => 'HCL Tech First Careers Mock Pack',
    'MPHASIS_MOCK_PACK' => 'Mphasis Placement Challenge Pack',
    'HEXAWARE_MOCK_PACK' => 'Hexaware PGET Placement Pack',
    'PERSISTENT_MOCK_PACK' => 'Persistent Systems Tech Mock Pack',
    'VIRTUSA_MOCK_PACK' => 'Virtusa NeuralHack & Placement Pack',
    'ORACLE_MOCK_PACK' => 'Oracle Server Technology Placement Pack',
    'COFORGE_MOCK_PACK' => 'Coforge Tech Talent Placement Pack',
];
$packTitle = $packTitles[$packCode] ?? 'Company Placement Pattern Mock';
?>

<div class="row justify-content-center py-2 py-md-3" id="mockMainContainer">
  <div class="col-lg-9 col-md-11" id="mockActiveQuizZone">

    <?php if ($packCode): ?>
      <div class="exam-notice-banner p-3 mb-3 d-flex justify-content-between align-items-center" style="background-color: var(--success-bg); color: var(--success-text); border: 1.5px solid var(--success-border);">
        <div class="d-flex align-items-center gap-2">
          <svg class="exam-icon" style="width:20px;height:20px;fill:currentColor;"><use href="#icon-shield"></use></svg>
          <div>
            <strong class="font-serif d-block" style="color: var(--success-text);"><?= htmlspecialchars($packTitle) ?> (Active)</strong>
            <span class="small" style="color: var(--ink-muted);">Curated company-pattern timed placement examination.</span>
          </div>
        </div>
        <a href="index.php?action=store" class="btn btn-primary-gamified btn-sm fw-bold">
          <i class="bi bi-shop me-1"></i> Store
        </a>
      </div>
    <?php endif; ?>

    <!-- Mock Test Header Control Bar -->
    <div class="card card-gamified p-3 mb-3 mock-header-card d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div class="d-flex align-items-center gap-2 gap-sm-3 min-w-0">
        <span class="badge rounded-pill px-2.5 px-sm-3 py-2 fs-6 fw-bold" style="background-color: var(--surface); color: var(--danger); border: 1px solid var(--border);">
          <svg class="exam-icon me-1 icon-14"><use href="#icon-clock"></use></svg> <span id="timerDisplay">10:00</span>
        </span>
        <span class="fw-bold text-ink fs-6" id="mockProgressText">Question 1 of 10</span>
      </div>

      <div class="d-flex align-items-center gap-2">
        <a href="#questionGridCard" class="btn btn-outline-gamified btn-sm px-2.5 px-sm-3 d-md-none" title="Jump to Status Grid">
          <svg class="exam-icon text-accent me-1 icon-14"><use href="#icon-grid"></use></svg> <span class="d-none d-sm-inline">Grid</span>
        </a>
        <button type="button" class="btn btn-primary-gamified btn-sm px-3 px-sm-4 fw-bold" id="finishTestBtn">
          Submit Test
        </button>
      </div>
    </div>

    <div class="row g-3 g-md-4">
      <!-- Main Question Display Card -->
      <div class="col-md-8">
        <div class="card card-gamified p-3 p-sm-4 h-100 d-flex flex-column" id="mockQuestionCard">
          <div class="d-flex justify-content-between align-items-center mb-2.5 gap-2 min-w-0">
            <span class="text-ink-muted small fw-bold text-truncate" id="mockTopicBadge">Topic: Loading...</span>
            <span class="badge-stamp-sm flex-shrink-0" id="mockSectionBadge">MOCK EXAM</span>
          </div>

          <!-- Dedicated Company / PYQ Source Tag Pill (Positioned outside question stem for maximum readability) -->
          <div class="quiz-source-tag-wrap mb-2" id="mockSourceTagWrap" style="display: none;">
            <span class="quiz-source-tag-stamp" id="mockSourceTag">
              <svg class="exam-icon me-1" style="width:11px;height:11px;opacity:0.75;"><use href="#icon-bookmark"></use></svg>
              <span id="mockSourceTagText"></span>
            </span>
          </div>

          <div class="mb-3">
            <h2 class="font-serif fw-bold text-ink lh-base fs-5" id="mockQuestionText">
              Loading mock test set...
            </h2>
          </div>

          <!-- Code Snippet Card for Programming & Technical Questions -->
          <div id="mockCodeSnippetCard" class="tech-code-container mb-3 d-none">
            <div class="code-terminal-header d-flex justify-content-between align-items-center px-3 py-1.5">
              <span class="code-lang-label" id="mockCodeLangBadge">CODE</span>
              <button type="button" class="btn-copy-code" onclick="navigator.clipboard.writeText(document.getElementById('mockCodeSnippetText').innerText); alert('Copied code!');" aria-label="Copy code" title="Copy code">
                <svg class="exam-icon copy-icon icon-16"><use href="#icon-copy"></use></svg>
              </button>
            </div>
            <pre class="m-0 p-3" tabindex="0" aria-label="Code snippet"><code class="tech-code-body font-mono" id="mockCodeSnippetText"></code></pre>
          </div>

          <div class="options-grid d-flex flex-column gap-2 gap-sm-3 mb-4" id="mockOptionsContainer">
            <button class="option-btn mock-option" data-option="A">
              <span class="option-key">A</span>
              <span class="option-text">Option A</span>
            </button>
            <button class="option-btn mock-option" data-option="B">
              <span class="option-key">B</span>
              <span class="option-text">Option B</span>
            </button>
            <button class="option-btn mock-option" data-option="C">
              <span class="option-key">C</span>
              <span class="option-text">Option C</span>
            </button>
            <button class="option-btn mock-option" data-option="D">
              <span class="option-key">D</span>
              <span class="option-text">Option D</span>
            </button>
          </div>

          <div class="mock-actions-container d-flex justify-content-between align-items-center gap-2 mt-auto pt-2">
            <button type="button" class="btn btn-outline-gamified px-3 px-sm-4" id="prevMockBtn" disabled>
              &larr; Previous
            </button>
            <button type="button" class="btn btn-primary-gamified px-3 px-sm-4 fw-bold" id="nextMockBtn">
              Save & Next &rarr;
            </button>
          </div>
        </div>
      </div>

      <!-- Question Palette Drawer -->
      <div class="col-md-4" id="questionGridCard">
        <div class="card card-gamified p-3 h-100">
          <h6 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
            <svg class="exam-icon text-accent icon-16"><use href="#icon-grid"></use></svg> Question Status Grid
          </h6>
          <div class="palette-grid mb-3" id="paletteGrid">
            <!-- Palette numbers populated dynamically -->
          </div>

          <div class="border-top pt-3 mt-auto fs-7 text-ink-muted" style="border-color: var(--border) !important;">
            <div class="d-flex align-items-center gap-2 mb-2">
              <span class="palette-legend-dot" style="background-color: var(--success-bg); border: 1.5px solid var(--success-border);"></span> 
              <span>Answered</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
              <span class="palette-legend-dot" style="background-color: var(--surface); border: 1.5px solid var(--border);"></span> 
              <span>Unattempted</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="palette-legend-dot" style="background-color: var(--surface); border: 2px solid var(--accent); box-shadow: 0 0 0 2px rgba(255, 126, 54, 0.35);"></span> 
              <span>Current Question</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Dedicated Full-Screen Exam Hall Mock Score Sheet (In-place replacement) -->
  <div class="col-12 d-none" id="mockScoreSheetContainer" role="region" aria-labelledby="scoreSheetTitle">
    <div class="mock-scoresheet-wrap">
      
      <!-- Visible H1 Landmark -->
      <header class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
          <span class="badge-stamp-sm mb-1 d-inline-block">OFFICIAL MARKSHEET</span>
          <h1 class="font-serif fw-bold text-ink fs-2 mb-0" id="scoreSheetTitle">Mock Test Result</h1>
          <p class="text-ink-muted small mb-0" id="scoreSheetSubtitle"><?= htmlspecialchars($packTitle) ?></p>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
          <button type="button" class="btn btn-outline-gamified btn-sm fw-bold" onclick="window.print()" title="Print Marksheet">
            <svg class="exam-icon me-1 icon-14"><use href="#icon-copy"></use></svg> Print PDF
          </button>
          <button type="button" class="btn btn-outline-gamified btn-sm fw-bold" id="downloadCardBtn" onclick="downloadScorecardImage()" title="Download Share Card Image">
            <svg class="exam-icon me-1 icon-14"><use href="#icon-save"></use></svg> Card Image
          </button>
          <button type="button" class="btn btn-primary-gamified btn-sm fw-bold" id="shareResultBtn" title="Share your score">
            <svg class="exam-icon me-1 icon-14"><use href="#icon-share"></use></svg> Share
          </button>
        </div>
      </header>

      <!-- Row 1: Score Stamp & Semantic Stats Grid -->
      <div class="row g-3 mb-4">
        <!-- Score Stamp (Left Column on Desktop) -->
        <div class="col-md-5">
          <div class="mock-score-stamp h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stamp-badge py-0.5 px-2 fs-8 fw-bold">EXAMINATION MARKS</span>
                <span class="badge rounded-pill px-2.5 py-1 fw-bold fs-7" id="scoreBandBadge">Pass</span>
              </div>
              <div class="mock-score-marks-display" aria-label="Final score">
                <span id="scoreMarksVal">0.00</span> <span class="fs-4 text-ink-muted fw-normal">/ <span id="scoreTotalMarksVal">10.00</span></span>
              </div>
              <div class="mock-score-formula-line mt-1">
                <svg class="exam-icon me-1 icon-12"><use href="#icon-bookmark"></use></svg> <span id="scoreFormulaText">+1 per correct, -0.25 per wrong</span>
              </div>
            </div>

            <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-center" style="border-color: var(--surface-border) !important;">
              <span class="small text-ink-muted">Net Score Percentage</span>
              <span class="font-mono fw-bold fs-5 text-ink" id="scorePercentageVal">0.0%</span>
            </div>
          </div>
        </div>

        <!-- Semantic Stats Chips (<dl>) (Right Column on Desktop) -->
        <div class="col-md-7">
          <dl class="mock-stats-dl h-100">
            <div class="mock-stat-box">
              <dt>Correct</dt>
              <dd class="text-success" id="statCorrect">0</dd>
            </div>
            <div class="mock-stat-box">
              <dt>Wrong</dt>
              <dd class="text-danger" id="statWrong">0</dd>
            </div>
            <div class="mock-stat-box">
              <dt>Skipped</dt>
              <dd class="text-ink-muted" id="statSkipped">0</dd>
            </div>
            <div class="mock-stat-box">
              <dt>Accuracy</dt>
              <dd class="text-accent" id="statAccuracy">0%</dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Row 2: Timing Analysis Strip -->
      <section class="card card-gamified p-3 p-sm-4 mb-4" aria-labelledby="timeAnalysisHeading">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <h2 class="font-serif fw-bold text-ink fs-5 mb-0" id="timeAnalysisHeading">
            <svg class="exam-icon me-1 text-accent icon-16"><use href="#icon-clock"></use></svg> Pacing & Time Breakdown
          </h2>
          <span class="small font-mono text-ink-muted" id="timeSummaryText">Total: -- / 10m 00s</span>
        </div>

        <div class="row g-2 mb-3 text-center">
          <div class="col-6 col-sm-3">
            <div class="p-2.5 rounded border" style="background-color: var(--surface-muted-bg); border-color: var(--surface-border) !important;">
              <div class="small text-ink-muted">Avg / Question</div>
              <div class="fw-bold font-mono text-ink fs-6" id="avgTimePerQText">--</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div class="p-2.5 rounded border" style="background-color: var(--surface-muted-bg); border-color: var(--surface-border) !important;">
              <div class="small text-ink-muted">Fastest Question</div>
              <div class="fw-bold font-mono text-success fs-6" id="fastestQText">--</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div class="p-2.5 rounded border" style="background-color: var(--surface-muted-bg); border-color: var(--surface-border) !important;">
              <div class="small text-ink-muted">Slowest Question</div>
              <div class="fw-bold font-mono text-danger fs-6" id="slowestQText">--</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div class="p-2.5 rounded border" style="background-color: var(--surface-muted-bg); border-color: var(--surface-border) !important;">
              <div class="small text-ink-muted">Correct vs Wrong</div>
              <div class="fw-bold font-mono text-ink fs-6" id="correctVsWrongTimeText">--</div>
            </div>
          </div>
        </div>

        <!-- Question Bubble Strip (Wrapped into rows, 10 per row on mobile, tap readout) -->
        <div class="mb-2">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small fw-bold text-ink">Question Pacing Strip (Tap a bubble to inspect):</span>
            <span class="small text-ink-muted d-none d-sm-inline">&bull; Green = Correct &bull; Red = Wrong &bull; Dashed = Skipped</span>
          </div>
          <div class="mock-bubble-strip" id="mockBubbleStrip" role="toolbar" aria-label="Question response bubbles">
            <!-- Populated via JavaScript -->
          </div>
        </div>

        <!-- Interactive Bubble Readout Banner -->
        <div class="mock-bubble-readout-card mt-2" id="bubbleReadoutCard">
          <span id="bubbleReadoutText">Tap any question bubble above to view its speed and section details.</span>
          <a href="#detailedReviewAccordion" class="small fw-bold text-accent text-nowrap" id="jumpToQuestionLink" style="display:none;">View in Review &darr;</a>
        </div>
      </section>

      <!-- Row 3: Section Breakdown & Performance Insights -->
      <div class="row g-3 mb-4">
        <!-- Section Breakdown Table -->
        <div class="col-md-7">
          <section class="card card-gamified p-3 p-sm-4 h-100" aria-labelledby="sectionBreakdownHeading">
            <h2 class="font-serif fw-bold text-ink fs-5 mb-3" id="sectionBreakdownHeading">
              <svg class="exam-icon me-1 text-accent icon-16"><use href="#icon-award"></use></svg> Sectional Score Breakdown
            </h2>
            <div class="table-responsive">
              <table class="mock-section-table" aria-label="Section Breakdown Table">
                <thead>
                  <tr>
                    <th>Section</th>
                    <th>Questions</th>
                    <th>Score</th>
                    <th>Accuracy</th>
                    <th>Avg Time</th>
                  </tr>
                </thead>
                <tbody id="sectionTableBody">
                  <!-- Populated dynamically -->
                </tbody>
              </table>
            </div>
          </section>
        </div>

        <!-- Rule-Based Insights Card -->
        <div class="col-md-5">
          <section class="card card-gamified p-3 p-sm-4 h-100" aria-labelledby="insightsHeading">
            <h2 class="font-serif fw-bold text-ink fs-5 mb-3" id="insightsHeading">
              <svg class="exam-icon me-1 text-highlight icon-16"><use href="#icon-bookmark"></use></svg> Test Insights & Advice
            </h2>
            <ul class="mock-insights-list" id="mockInsightsList">
              <!-- Populated dynamically -->
            </ul>
          </section>
        </div>
      </div>

      <!-- Row 4: Review Answers Accordion -->
      <section class="card card-gamified p-3 p-sm-4 mb-4" aria-labelledby="reviewAnswersHeading">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <div>
            <h2 class="font-serif fw-bold text-ink fs-5 mb-0" id="reviewAnswersHeading">
              <svg class="exam-icon me-1 text-accent icon-16"><use href="#icon-bookmark"></use></svg> Question Solutions & Explanations
            </h2>
            <span class="small text-ink-muted">Tap any question to reveal options, explanations, and exact time taken.</span>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gamified btn-sm" id="toggleAllReviewBtn">Expand All</button>
            <button type="button" class="btn btn-outline-gamified btn-sm text-danger" id="filterMistakesBtn">Show Mistakes Only</button>
          </div>
        </div>

        <div id="detailedReviewAccordion">
          <!-- Accordion items populated dynamically -->
        </div>
      </section>

      <!-- Row 5: Action Buttons Bar -->
      <div class="mock-action-bar no-print">
        <a href="index.php?action=mock<?= $packCode ? '&pack=' . urlencode($packCode) : '' ?>" class="btn btn-primary-gamified px-4 py-2.5 fw-bold" style="min-height: 48px;" id="retakeMockBtn">
          Retake Mock Test &rarr;
        </a>
        <div class="d-flex gap-2 flex-wrap">
          <a href="index.php?action=store" class="btn btn-outline-gamified px-4 py-2.5" style="min-height: 48px;">
            Back to Store
          </a>
          <a href="index.php?action=profile" class="btn btn-outline-gamified px-4 py-2.5" style="min-height: 48px;">
            View Profile & Progress
          </a>
        </div>
      </div>

    </div>
  </div>

  <!-- Legacy Scorecard Modal retained for backwards compatibility -->
  <div class="modal fade" id="scorecardModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border-color: var(--border) !important;">
        <div class="modal-body p-4 text-center">
          <div id="scoreCorrect" class="d-none">0/10</div>
          <div id="scoreAccuracy" class="d-none">0%</div>
          <div id="scoreXP" class="d-none">+0 XP</div>
          <div id="detailedReviewContainer" class="d-none"></div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
let mockQuestions = [];
let currentIndex = 0;
let userAnswers = {};
let questionTimes = {}; // question_id => seconds
let activeQuestionStartTime = null;
let testStartTime = null;
let timerSeconds = 600;
let serverDeadlineTs = null;
let timerInterval = null;
let currentScorecardData = null;
let currentMockSessionId = null;
const currentPackCode = '<?= htmlspecialchars($packCode ?? 'GENERAL_MOCK', ENT_QUOTES) ?>';
const currentPackTitle = '<?= htmlspecialchars($packTitle, ENT_QUOTES) ?>';

// Tab-Switch Rules (BUG-008): Tab switch forfeits and skips current question only (reason: tab_switch).
// Elapsed exam timer is NOT paused.
let tabSwitchSkipped = [];
let isTabSwitchProcessing = false;

function handleTabSwitchViolation() {
    if (isTabSwitchProcessing) return;
    const scoreSheet = document.getElementById('mockScoreSheetContainer');
    if (!mockQuestions || !mockQuestions.length || (scoreSheet && !scoreSheet.classList.contains('d-none'))) {
        return;
    }

    isTabSwitchProcessing = true;
    setTimeout(() => { isTabSwitchProcessing = false; }, 1000);

    const currentQ = mockQuestions[currentIndex];
    if (currentQ) {
        if (!tabSwitchSkipped.includes(currentQ.id)) {
            tabSwitchSkipped.push(currentQ.id);
        }
        if (currentMockSessionId) {
            try {
                sessionStorage.setItem('tab_switches_' + currentMockSessionId, JSON.stringify(tabSwitchSkipped));
            } catch(e) {}
        }
        delete userAnswers[currentQ.id];
    }

    recordActiveQuestionTime();

    if (window.showToast) {
        showToast('Tab switch detected! Current question skipped per exam rules.', 'warning');
    } else {
        alert('Tab switch detected! Current question forfeited and skipped per exam rules.');
    }

    if (currentIndex < mockQuestions.length - 1) {
        currentIndex++;
        renderQuestion();
        renderPalette();
    } else {
        submitMockTest();
    }
}

document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        handleTabSwitchViolation();
    }
});
window.addEventListener('blur', () => {
    handleTabSwitchViolation();
});

function recordActiveQuestionTime() {
    if (activeQuestionStartTime && mockQuestions[currentIndex]) {
        const qId = mockQuestions[currentIndex].id;
        const elapsedSeconds = Math.round((Date.now() - activeQuestionStartTime) / 1000);
        const cappedSeconds = Math.min(Math.max(0, elapsedSeconds), 300);
        questionTimes[qId] = (questionTimes[qId] || 0) + cappedSeconds;
    }
    activeQuestionStartTime = Date.now();
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const viewParam = urlParams.get('view');
    const sessionParam = urlParams.get('session');

    if (viewParam === 'scorecard' && sessionParam) {
        fetch(`index.php?action=api_get_mock_scorecard&session_id=${encodeURIComponent(sessionParam)}&date=${encodeURIComponent(sessionParam)}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    currentScorecardData = data;
                    displayExamHallScorecard(data);
                } else {
                    fetchMockTest();
                }
            })
            .catch(() => fetchMockTest());
    } else {
        fetchMockTest();
    }

    document.getElementById('nextMockBtn')?.addEventListener('click', handleNextOrSubmit);

    document.getElementById('prevMockBtn')?.addEventListener('click', () => {
        if (currentIndex > 0) {
            recordActiveQuestionTime();
            currentIndex--;
            renderQuestion();
        }
    });

    document.getElementById('finishTestBtn')?.addEventListener('click', () => {
        handleDirectOrConfirmSubmit();
    });

    document.getElementById('shareResultBtn')?.addEventListener('click', shareMockResult);
});

function handleNextOrSubmit() {
    recordActiveQuestionTime();
    if (currentIndex < mockQuestions.length - 1) {
        currentIndex++;
        renderQuestion();
    } else {
        handleDirectOrConfirmSubmit();
    }
}

function handleDirectOrConfirmSubmit() {
    recordActiveQuestionTime();
    const unattempted = mockQuestions.filter(q => !userAnswers[q.id]);

    if (unattempted.length === 0) {
        submitMockTest();
    } else {
        const msg = `You have ${unattempted.length} unattempted question(s) left.\n\nDo you want to submit your mock test now?`;
        if (confirm(msg)) {
            submitMockTest();
        }
    }
}

async function fetchMockTest() {
    const qBox = document.getElementById('mockQuestionText');
    const url = 'index.php?action=api_get_mock<?= $packCode ? '&pack=' . urlencode($packCode) : '' ?>';
    
    if (qBox) {
        qBox.innerHTML = '<div class="text-center py-4 text-ink-muted"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading exam questions...</div>';
    }

    try {
        const res = await fetch(url);
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (jsonErr) {
            throw new Error(`Invalid server response format (HTTP ${res.status}): ${text.substring(0, 100)}`);
        }

        if (!res.ok || data.status !== 'success') {
            const errorMsg = data.message || `Server responded with status ${res.status}`;
            throw new Error(errorMsg);
        }

        if (data.questions && data.questions.length > 0) {
            mockQuestions = data.questions;
            currentMockSessionId = data.session_id || null;
            const totalSec = (data.duration_seconds || (data.questions.length * 60));
            timerSeconds = (data.remaining_seconds !== undefined && data.remaining_seconds !== null)
                ? parseInt(data.remaining_seconds, 10)
                : totalSec;

            if (data.deadline) {
                const deadlineMs = new Date(data.deadline.replace(' ', 'T')).getTime();
                if (!isNaN(deadlineMs)) {
                    serverDeadlineTs = deadlineMs;
                } else {
                    serverDeadlineTs = Date.now() + (timerSeconds * 1000);
                }
            } else {
                serverDeadlineTs = Date.now() + (timerSeconds * 1000);
            }

            currentIndex = 0;
            userAnswers = (data.saved_answers && typeof data.saved_answers === 'object') ? Object.assign({}, data.saved_answers) : {};
            questionTimes = {};
            let storedSwitches = [];
            if (currentMockSessionId) {
                try {
                    storedSwitches = JSON.parse(sessionStorage.getItem('tab_switches_' + currentMockSessionId) || '[]');
                } catch(e) {}
            }
            tabSwitchSkipped = Array.isArray(storedSwitches) ? storedSwitches : [];
            testStartTime = Date.now();
            activeQuestionStartTime = Date.now();

            renderQuestion();
            renderPalette();
            startTimer();
        } else {
            if (qBox) {
                qBox.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-ink-muted mb-2 d-block"></i>
                        <h5 class="fw-bold text-ink">No Questions Found</h5>
                        <p class="text-ink-muted mb-3">No mock questions are currently available for this pack.</p>
                        <a href="index.php?action=store" class="btn btn-outline-gamified fw-bold">Back to Store</a>
                    </div>
                `;
            }
        }
    } catch (err) {
        console.error('Failed to load mock questions:', err);
        if (qBox) {
            qBox.innerHTML = `
                <div class="alert alert-danger p-3 my-2" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <strong class="fs-6">Unable to Load Mock Examination</strong>
                    </div>
                    <p class="small mb-3 text-break">${err.message}</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary-gamified btn-sm fw-bold px-3" onclick="fetchMockTest()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Retry
                        </button>
                        <a href="index.php?action=store" class="btn btn-primary-gamified btn-sm fw-bold"><i class="bi bi-shop me-1"></i> Visit Store</a>
                    </div>
                </div>
            `;
        }
    }
}

function renderQuestion() {
    if (!mockQuestions.length) return;
    const q = mockQuestions[currentIndex];

    activeQuestionStartTime = Date.now();

    document.getElementById('mockProgressText').innerText = `Question ${currentIndex + 1} of ${mockQuestions.length}`;
    const displayTopic = (q.topic_name === 'Company Placement PYQs' && q.subtopic_name)
        ? q.subtopic_name
        : (q.subtopic_name ? `${q.topic_name} > ${q.subtopic_name}` : q.topic_name);
    document.getElementById('mockTopicBadge').innerText = `Topic: ${displayTopic}`;
    document.getElementById('mockTopicBadge').title = displayTopic;

    // Question parsing: extract company / source tag and clean question prompt for maximum readability
    const parsed = (window.QuizShell && QuizShell.parseQuestion) 
        ? QuizShell.parseQuestion(q.question_text) 
        : { prompt: q.question_text, sourceTag: null };

    const sourceTagWrap = document.getElementById('mockSourceTagWrap');
    const sourceTagText = document.getElementById('mockSourceTagText');
    const sourceTag = parsed.sourceTag || q.source_tag || null;

    if (sourceTag && sourceTagWrap && sourceTagText) {
        sourceTagWrap.style.display = 'block';
        sourceTagText.innerText = sourceTag;
    } else if (sourceTagWrap) {
        sourceTagWrap.style.display = 'none';
    }

    const cleanQuestionText = parsed.fullText || parsed.full_text || parsed.prompt || q.question_text;
    document.getElementById('mockQuestionText').innerHTML = formatSafeText(cleanQuestionText);

    const codeCard = document.getElementById('mockCodeSnippetCard');
    const codeText = document.getElementById('mockCodeSnippetText');
    const codeLang = document.getElementById('mockCodeLangBadge');
    if (q.code_snippet && q.code_snippet.trim() !== '') {
        codeCard.classList.remove('d-none');
        codeText.innerText = q.code_snippet;
        codeLang.innerText = (q.language || 'CODE').toUpperCase();
    } else {
        codeCard.classList.add('d-none');
    }

    const prevBtn = document.getElementById('prevMockBtn');
    const nextBtn = document.getElementById('nextMockBtn');
    prevBtn.disabled = (currentIndex === 0);

    if (currentIndex === mockQuestions.length - 1) {
        nextBtn.innerHTML = 'Submit Test <svg class="exam-icon ms-1 icon-14"><use href="#icon-shield"></use></svg>';
    } else {
        nextBtn.innerHTML = 'Save & Next &rarr;';
    }

    document.querySelectorAll('.mock-option').forEach(btn => {
        const opt = btn.dataset.option;
        btn.classList.remove('selected');
        const textSpan = btn.querySelector('.option-text');
        textSpan.innerHTML = formatSafeText(q.options[opt] || '');

        if (userAnswers[q.id] === opt) {
            btn.classList.add('selected');
        }

        btn.onclick = () => {
            if (tabSwitchSkipped.includes(q.id)) {
                if (window.showToast) {
                    showToast('This question was forfeited due to tab switch violation.', 'warning');
                }
                return;
            }
            userAnswers[q.id] = opt;
            document.querySelectorAll('.mock-option').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            renderPalette();
        };
    });

    renderPalette();
}

function renderPalette() {
    const grid = document.getElementById('paletteGrid');
    if (!grid) return;
    grid.innerHTML = '';

    mockQuestions.forEach((q, idx) => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'palette-item';
        item.innerText = idx + 1;
        item.setAttribute('aria-label', `Question ${idx + 1}`);

        const isAnswered = !!userAnswers[q.id];
        const isCurrent = (idx === currentIndex);

        if (isAnswered) item.classList.add('answered');
        if (isCurrent) item.classList.add('current');

        item.onclick = () => {
            recordActiveQuestionTime();
            currentIndex = idx;
            renderQuestion();
        };

        grid.appendChild(item);
    });
}

function startTimer() {
    const display = document.getElementById('timerDisplay');
    clearInterval(timerInterval);

    const updateTimerDisplay = () => {
        if (serverDeadlineTs) {
            const now = Date.now();
            timerSeconds = Math.max(0, Math.round((serverDeadlineTs - now) / 1000));
        } else {
            timerSeconds--;
        }

        if (timerSeconds <= 0) {
            clearInterval(timerInterval);
            timerSeconds = 0;
            if (display) display.innerText = '00:00';
            recordActiveQuestionTime();
            alert("Time has expired! Submitting your mock examination now.");
            submitMockTest();
            return;
        }

        const m = Math.floor(timerSeconds / 60);
        const s = timerSeconds % 60;
        if (display) {
            display.innerText = `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
        }
    };

    updateTimerDisplay();
    timerInterval = setInterval(updateTimerDisplay, 1000);
}

function submitMockTest() {
    clearInterval(timerInterval);
    recordActiveQuestionTime();

    const finishBtn = document.getElementById('finishTestBtn');
    const nextBtn = document.getElementById('nextMockBtn');
    if (finishBtn) { finishBtn.disabled = true; finishBtn.innerText = 'Submitting...'; }
    if (nextBtn) { nextBtn.disabled = true; nextBtn.innerText = 'Submitting...'; }

    const questionIds = mockQuestions.map(q => q.id);
    const totalDurationSeconds = testStartTime ? Math.round((Date.now() - testStartTime) / 1000) : (600 - timerSeconds);

    fetch('index.php?action=api_submit_mock', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            session_id: currentMockSessionId,
            question_ids: questionIds,
            answers: userAnswers,
            tab_switch_skipped: tabSwitchSkipped,
            question_times: questionTimes,
            total_time_seconds: totalDurationSeconds,
            pack_code: currentPackCode,
            pack_title: currentPackTitle,
            csrf_token: window.CSRF_TOKEN || ''
        })
    })
    .then(res => res.json())
    .then(res => {
        if (finishBtn) { finishBtn.disabled = false; finishBtn.innerText = 'Submit Test'; }
        if (nextBtn) { nextBtn.disabled = false; }

        if (res.status === 'success') {
            currentScorecardData = res;
            displayExamHallScorecard(res);
        } else {
            alert(res.message || 'Error submitting test.');
        }
    })
    .catch(err => {
        console.error(err);
        if (finishBtn) { finishBtn.disabled = false; finishBtn.innerText = 'Submit Test'; }
        if (nextBtn) { nextBtn.disabled = false; }
        alert('Network error submitting test. Please try again.');
    });
}

/**
 * Display the Exam Hall Score Sheet in focus mode
 */
function displayExamHallScorecard(data) {
    if (data.session_id && window.history && window.history.replaceState) {
        const newUrl = `index.php?action=mock&session=${encodeURIComponent(data.session_id)}&view=scorecard`;
        window.history.replaceState({ mockSessionId: data.session_id }, '', newUrl);
    }

    // 1. Hide active quiz container and reveal score sheet
    document.getElementById('mockActiveQuizZone').classList.add('d-none');
    const scoreSheetEl = document.getElementById('mockScoreSheetContainer');
    scoreSheetEl.classList.remove('d-none');
    document.body.classList.add('is-mock-scorecard');

    window.scrollTo({ top: 0, behavior: 'smooth' });

    // 2. Score Stamp & Marks
    const marksEl = document.getElementById('scoreMarksVal');
    const totalMarksEl = document.getElementById('scoreTotalMarksVal');
    const formulaEl = document.getElementById('scoreFormulaText');
    const pctEl = document.getElementById('scorePercentageVal');
    const bandBadge = document.getElementById('scoreBandBadge');

    totalMarksEl.innerText = Number(data.score.total_marks).toFixed(2);
    formulaEl.innerText = data.score.formula || '+1 per correct, -0.25 per wrong';
    pctEl.innerText = `${data.score.percentage}%`;
    bandBadge.innerText = data.score.band;

    if (data.score.band === 'Distinction' || data.score.band === 'First Class') {
        bandBadge.style.backgroundColor = 'var(--success-bg)';
        bandBadge.style.color = 'var(--success-text)';
        bandBadge.style.border = '1px solid var(--success-border)';
    } else {
        bandBadge.style.backgroundColor = 'var(--surface-muted-bg)';
        bandBadge.style.color = 'var(--ink-muted)';
        bandBadge.style.border = '1px solid var(--surface-border)';
    }

    // Animated count-up (800ms) with pre-set HTML value for zero CLS and screen readers
    marksEl.innerText = Number(data.score.marks).toFixed(2);
    marksEl.parentElement.setAttribute('aria-label', `Final score: ${data.score.marks} out of ${data.score.total_marks} marks`);
    animateCountUp(marksEl, Number(data.score.marks), 800);

    // 3. Stats Grid
    document.getElementById('statCorrect').innerText = data.counts.correct;
    document.getElementById('statWrong').innerText = data.counts.wrong;
    document.getElementById('statSkipped').innerText = data.counts.skipped;
    document.getElementById('statAccuracy').innerText = `${data.counts.accuracy_percent}%`;

    // 4. Timing Breakdown
    const totalSec = data.time.total_taken_seconds || 0;
    const allowedSec = data.time.time_allowed_seconds || 600;
    const formatTime = (sec) => {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return `${m}m ${s < 10 ? '0' : ''}${s}s`;
    };

    if (data.time.has_time_data) {
        document.getElementById('timeSummaryText').innerText = `Total: ${formatTime(totalSec)} / ${formatTime(allowedSec)}`;
        document.getElementById('avgTimePerQText').innerText = `${data.time.avg_time_per_question}s / Q`;
        document.getElementById('fastestQText').innerText = data.time.fastest_question ? `Q${data.time.fastest_question.question_number} (${data.time.fastest_question.time_seconds}s)` : '--';
        document.getElementById('slowestQText').innerText = data.time.slowest_question ? `Q${data.time.slowest_question.question_number} (${data.time.slowest_question.time_seconds}s)` : '--';
        document.getElementById('correctVsWrongTimeText').innerText = `${data.time.avg_time_correct}s vs ${data.time.avg_time_wrong}s`;
    } else {
        document.getElementById('timeSummaryText').innerText = 'Time not recorded';
        document.getElementById('avgTimePerQText').innerText = 'N/A';
        document.getElementById('fastestQText').innerText = 'N/A';
        document.getElementById('slowestQText').innerText = 'N/A';
        document.getElementById('correctVsWrongTimeText').innerText = 'N/A';
    }

    // 5. Question Bubble Strip (10 items per row on mobile, accessible tap readout)
    const bubbleStrip = document.getElementById('mockBubbleStrip');
    bubbleStrip.innerHTML = '';
    const readoutCard = document.getElementById('bubbleReadoutCard');
    const readoutText = document.getElementById('bubbleReadoutText');
    const jumpLink = document.getElementById('jumpToQuestionLink');

    data.bubbles.forEach((b, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `mock-bubble-btn ${b.status}`;
        btn.id = `bubbleBtn_${b.question_number}`;
        btn.tabIndex = 0;
        
        let iconSvg = '';
        if (b.status === 'correct') {
            iconSvg = `<span style="font-size:11px;">&#10003;</span>`;
        } else if (b.status === 'wrong') {
            iconSvg = `<span style="font-size:11px;">&#10005;</span>`;
        } else {
            iconSvg = `<span style="font-size:11px;">&ndash;</span>`;
        }
        btn.innerHTML = `<span style="font-size:10px;line-height:1;margin-bottom:1px;">${b.question_number}</span>${iconSvg}`;
        btn.setAttribute('aria-label', `Question ${b.question_number}: ${b.status}, time ${b.time_seconds}s`);

        const handleSelectBubble = () => {
            document.querySelectorAll('.mock-bubble-btn').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');
            const stateWord = b.status.charAt(0).toUpperCase() + b.status.slice(1);
            const timeWord = b.time_seconds > 0 ? `${b.time_seconds}s` : 'Time not recorded';
            readoutText.innerText = `Q${b.question_number} &bull; ${stateWord} &bull; ${timeWord}`;
            jumpLink.style.display = 'inline-block';
            jumpLink.href = `#reviewItem_${b.question_number}`;
        };

        btn.addEventListener('click', () => {
            handleSelectBubble();
            // Open review item if collapsed
            const targetDetails = document.getElementById(`reviewItem_${b.question_number}`);
            if (targetDetails) {
                targetDetails.open = true;
                targetDetails.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
        btn.addEventListener('focus', handleSelectBubble);

        bubbleStrip.appendChild(btn);
    });

    // 6. Section Breakdown Table
    const secTableBody = document.getElementById('sectionTableBody');
    secTableBody.innerHTML = '';
    (data.sections || []).forEach(sec => {
        const tr = document.createElement('tr');
        const avgTimeStr = sec.has_time ? `${sec.avg_time_seconds}s` : 'Time not recorded';
        tr.innerHTML = `
            <td class="fw-bold">${sec.name}</td>
            <td>${sec.total} <span class="text-ink-muted small">(${sec.correct}C / ${sec.wrong}W)</span></td>
            <td class="fw-bold font-mono">${sec.score_marks}</td>
            <td class="font-mono text-accent fw-bold">${sec.accuracy_percent}%</td>
            <td class="font-mono">${avgTimeStr}</td>
        `;
        secTableBody.appendChild(tr);
    });

    // 7. Rule-Based Insights
    const insightsList = document.getElementById('mockInsightsList');
    insightsList.innerHTML = '';
    (data.insights || []).forEach(ins => {
        const li = document.createElement('li');
        li.className = 'mock-insight-item';
        li.innerText = ins;
        insightsList.appendChild(li);
    });

    // 8. Detailed Review Answers Accordion
    const reviewAccordion = document.getElementById('detailedReviewAccordion');
    reviewAccordion.innerHTML = '';
    data.detailed_results.forEach((r) => {
        const details = document.createElement('details');
        details.className = 'mock-review-item';
        details.id = `reviewItem_${r.question_number}`;
        details.dataset.status = r.status;

        let statusBadge = '';
        if (r.status === 'correct') {
            statusBadge = `<span class="badge" style="background-color: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border);">&#10003; Correct</span>`;
        } else if (r.status === 'wrong') {
            statusBadge = `<span class="badge" style="background-color: var(--danger-bg); color: var(--danger-text); border: 1px solid var(--danger-border);">&#10005; Wrong</span>`;
        } else {
            statusBadge = `<span class="badge" style="background-color: var(--surface-muted-bg); color: var(--ink-muted); border: 1px dashed var(--surface-border);">&ndash; Skipped</span>`;
        }

        const timeStr = r.has_time ? `${r.time_seconds}s` : 'Time not recorded';

        const parsedR = (window.QuizShell && QuizShell.parseQuestion) 
            ? QuizShell.parseQuestion(r.question_text) 
            : { prompt: r.question_text, sourceTag: r.source_tag || null };
        const rSourceTag = parsedR.sourceTag || r.source_tag || null;
        const rTagHtml = rSourceTag 
            ? `<div class="mb-2"><span class="quiz-source-tag-stamp"><svg class="exam-icon me-1" style="width:11px;height:11px;opacity:0.75;"><use href="#icon-bookmark"></use></svg>${rSourceTag}</span></div>` 
            : '';
        const rPrompt = parsedR.fullText || parsedR.full_text || parsedR.prompt || r.question_text;

        details.innerHTML = `
            <summary>
                <div class="d-flex align-items-center gap-2 text-truncate" style="min-width:0;">
                    <span class="font-mono fw-bold text-ink">Q${r.question_number}.</span>
                    <span class="text-truncate">${r.topic_name}</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="small font-mono text-ink-muted d-none d-sm-inline">${timeStr}</span>
                    ${statusBadge}
                </div>
            </summary>
            <div class="mock-review-body">
                ${rTagHtml}
                <p class="fw-semibold text-ink mb-3">${formatSafeText(rPrompt)}</p>
                ${r.code_snippet ? `<pre class="p-3 rounded bg-surface border mb-3 font-mono small"><code>${r.code_snippet}</code></pre>` : ''}
                <div class="p-2.5 rounded border mb-3" style="background-color: var(--surface-muted-bg); border-color: var(--surface-border) !important;">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Your Answer: <strong class="${r.is_correct ? 'text-success' : 'text-danger'}">${r.user_answer}</strong></span>
                        <span>Correct Answer: <strong class="text-success">${r.correct_option}</strong></span>
                    </div>
                    <div class="small text-ink-muted">Time taken: <strong>${timeStr}</strong></div>
                </div>
                <div class="small">
                    <strong class="text-ink d-block mb-1">Explanation:</strong>
                    <div class="text-ink-muted">${formatSafeText(r.explanation)}</div>
                </div>
            </div>
        `;
        reviewAccordion.appendChild(details);
    });

    // Expand / Collapse toggles
    let isAllExpanded = false;
    document.getElementById('toggleAllReviewBtn').onclick = () => {
        isAllExpanded = !isAllExpanded;
        document.querySelectorAll('.mock-review-item').forEach(el => el.open = isAllExpanded);
        document.getElementById('toggleAllReviewBtn').innerText = isAllExpanded ? 'Collapse All' : 'Expand All';
    };

    let isMistakesOnly = false;
    document.getElementById('filterMistakesBtn').onclick = () => {
        isMistakesOnly = !isMistakesOnly;
        document.querySelectorAll('.mock-review-item').forEach(el => {
            if (isMistakesOnly) {
                el.style.display = (el.dataset.status === 'wrong' || el.dataset.status === 'skipped') ? 'block' : 'none';
                if (el.dataset.status === 'wrong') el.open = true;
            } else {
                el.style.display = 'block';
            }
        });
        document.getElementById('filterMistakesBtn').innerText = isMistakesOnly ? 'Show All Questions' : 'Show Mistakes Only';
    };

    // Confetti on qualifying score
    if (data.score.qualifies_confetti && typeof launchConfetti === 'function') {
        const isReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!isReduced) {
            launchConfetti();
        }
    }
}

/**
 * Animated number count-up helper
 */
function animateCountUp(element, finalValue, duration = 800) {
    const isReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (isReduced) {
        element.innerText = finalValue.toFixed(2);
        return;
    }

    let startTime = null;
    function step(timestamp) {
        if (!startTime) startTime = timestamp;
        const progress = Math.min((timestamp - startTime) / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        const current = ease * finalValue;
        element.innerText = current.toFixed(2);
        if (progress < 1) {
            requestAnimationFrame(step);
        } else {
            element.innerText = finalValue.toFixed(2);
        }
    }
    requestAnimationFrame(step);
}

/**
 * Safe Text Formatter (Escapes HTML, parses backticks to code tags)
 */
function formatSafeText(raw) {
    if (!raw) return '';
    const temp = document.createElement('div');
    temp.textContent = raw;
    let safe = temp.innerHTML;
    // Replace `code` with <code>
    safe = safe.replace(/`([^`]+)`/g, '<code class="font-mono px-1 py-0.5 rounded bg-surface border">$1</code>');
    return safe;
}

/**
 * Dynamic Canvas Scorecard Image Generator
 */
function generateScorecardCanvas(score, packTitle) {
    const canvas = document.createElement('canvas');
    canvas.width = 1200;
    canvas.height = 630;
    const ctx = canvas.getContext('2d');
    if (!ctx) return null;

    // Background gradient
    const grad = ctx.createLinearGradient(0, 0, 1200, 630);
    grad.addColorStop(0, '#0f172a');
    grad.addColorStop(0.6, '#1e293b');
    grad.addColorStop(1, '#090d16');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 1200, 630);

    // Decorative glow
    const radial = ctx.createRadialGradient(950, 140, 40, 950, 140, 400);
    radial.addColorStop(0, 'rgba(249, 115, 22, 0.28)');
    radial.addColorStop(1, 'rgba(249, 115, 22, 0)');
    ctx.fillStyle = radial;
    ctx.fillRect(0, 0, 1200, 630);

    // Border
    ctx.strokeStyle = '#334155';
    ctx.lineWidth = 4;
    ctx.strokeRect(36, 36, 1128, 558);

    // Header brand
    ctx.fillStyle = '#f97316';
    ctx.font = 'bold 36px system-ui, -apple-system, sans-serif';
    ctx.fillText('Apttitudee', 80, 110);

    ctx.fillStyle = '#94a3b8';
    ctx.font = '500 20px system-ui, -apple-system, sans-serif';
    ctx.fillText('Placement Preparation Suite', 270, 110);

    // Assessment title
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 40px Georgia, serif';
    const displayTitle = (packTitle || 'Mock Assessment Report').length > 38 ? 
        (packTitle || 'Mock Assessment Report').slice(0, 35) + '...' : 
        (packTitle || 'Mock Assessment Report');
    ctx.fillText(displayTitle, 80, 195);

    // Score Hero
    ctx.fillStyle = '#f8fafc';
    ctx.font = 'bold 78px system-ui, -apple-system, sans-serif';
    const scoreStr = `${score.marks} / ${score.total_marks}`;
    ctx.fillText(scoreStr, 80, 310);

    ctx.fillStyle = '#38bdf8';
    ctx.font = 'bold 38px system-ui, -apple-system, sans-serif';
    ctx.fillText(`${score.percentage}% Overall Score`, 80, 375);

    // Stat cards
    const statCards = [
        { label: 'CORRECT', val: `${score.correct || 0} Qs`, color: '#10b981' },
        { label: 'ACCURACY', val: `${score.accuracy || score.percentage}%`, color: '#38bdf8' },
        { label: 'TIME SPENT', val: `${Math.round((score.time_taken_seconds || 180)/60)}m`, color: '#f59e0b' }
    ];

    statCards.forEach((c, idx) => {
        const x = 80 + idx * 260;
        ctx.fillStyle = '#1e293b';
        ctx.fillRect(x, 420, 230, 95);
        ctx.strokeStyle = '#334155';
        ctx.lineWidth = 2;
        ctx.strokeRect(x, 420, 230, 95);

        ctx.fillStyle = '#94a3b8';
        ctx.font = '600 15px system-ui, sans-serif';
        ctx.fillText(c.label, x + 20, 452);

        ctx.fillStyle = c.color;
        ctx.font = 'bold 30px system-ui, sans-serif';
        ctx.fillText(c.val, x + 20, 492);
    });

    // Footer note
    ctx.fillStyle = '#64748b';
    ctx.font = '500 17px system-ui, sans-serif';
    ctx.fillText('Practice verified company placement papers at apttitudee.online', 80, 560);

    return canvas;
}

function downloadScorecardImage() {
    if (!currentScorecardData) return;
    const canvas = generateScorecardCanvas(currentScorecardData.score, currentPackTitle);
    if (!canvas) return;

    canvas.toBlob((blob) => {
        if (!blob) return;
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Apttitudee_Scorecard_${Date.now()}.png`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }, 'image/png');
}

/**
 * Text & Image Web Share API helper
 */
async function shareMockResult() {
    if (!currentScorecardData) return;
    const score = currentScorecardData.score;
    const shareText = `I completed the ${currentPackTitle} on Apttitudee and scored ${score.marks}/${score.total_marks} marks (${score.percentage}%)! Prepare for campus placements with adaptive tests.`;
    
    // Attempt to share image file if supported
    const canvas = generateScorecardCanvas(score, currentPackTitle);
    if (canvas && navigator.canShare) {
        try {
            const blob = await new Promise(res => canvas.toBlob(res, 'image/png'));
            if (blob) {
                const file = new File([blob], 'apttitudee_result.png', { type: 'image/png' });
                if (navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        title: 'My Apttitudee Mock Test Score',
                        text: shareText,
                        files: [file]
                    });
                    return;
                }
            }
        } catch (e) {
            // fallback to text share below
        }
    }

    if (navigator.share) {
        navigator.share({
            title: 'My Apttitudee Mock Test Score',
            text: shareText
        }).catch(() => {});
    } else {
        navigator.clipboard.writeText(shareText).then(() => {
            alert('Score summary copied to clipboard!');
        }).catch(() => {
            alert(shareText);
        });
    }
}
</script>
