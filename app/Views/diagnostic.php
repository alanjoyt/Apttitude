<div class="row justify-content-center py-3">
  <div class="col-lg-8 col-md-10">

    <!-- Diagnostic Quiz Card -->
    <div class="card card-gamified p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="stamp-badge d-inline-flex align-items-center gap-1">
          <svg class="exam-icon text-warning" style="width:14px;height:14px;"><use href="#icon-zap"></use></svg> Onboarding Diagnostic Test
        </span>
        <span class="fw-bold text-ink" id="diagnosticProgress">Question 1 of 12</span>
      </div>

      <div class="mb-4">
        <h4 class="font-serif fw-bold text-ink lh-base" id="diagnosticQuestionText">
          Loading diagnostic questions...
        </h4>
      </div>

      <div class="options-grid d-flex flex-column gap-3 mb-4" id="diagnosticOptions">
        <button class="option-btn diag-option" data-option="A">
          <span class="option-key">A</span>
          <span class="option-text">Option A</span>
        </button>
        <button class="option-btn diag-option" data-option="B">
          <span class="option-key">B</span>
          <span class="option-text">Option B</span>
        </button>
        <button class="option-btn diag-option" data-option="C">
          <span class="option-key">C</span>
          <span class="option-text">Option C</span>
        </button>
        <button class="option-btn diag-option" data-option="D">
          <span class="option-key">D</span>
          <span class="option-text">Option D</span>
        </button>
      </div>

      <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-outline-gamified px-3" id="prevDiagBtn" disabled>
          Previous
        </button>
        <button type="button" class="btn btn-primary-gamified px-4 fw-bold" id="nextDiagBtn">
          Save & Next <i class="bi bi-chevron-right ms-1"></i>
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Scorecard Modal -->
<div class="modal fade" id="diagnosticModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-ink border rounded-4 text-center p-3" style="background-color: var(--surface); border-color: var(--border) !important;">
      <div class="modal-body p-4">
        <div class="mb-2"><svg class="exam-icon text-warning" style="width:56px;height:56px;"><use href="#icon-trophy"></use></svg></div>
        <h4 class="font-serif fw-bold mb-2" style="color: var(--highlight);">Diagnostic Test Complete!</h4>
        <p class="text-ink-muted small mb-4" id="diagScoreText">
          Your topic starting mastery levels have been calibrated!
        </p>
        <div class="d-flex justify-content-center gap-3 mb-4">
          <div class="p-3 rounded-3 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-4 font-serif fw-bold" style="color: var(--accent);" id="diagCorrect">0/12</div>
            <div class="text-ink-muted small">Correct</div>
          </div>
          <div class="p-3 rounded-3 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-4 font-serif fw-bold" style="color: var(--highlight);">+250 XP</div>
            <div class="text-ink-muted small">Bonus Earned</div>
          </div>
        </div>
        <a href="index.php?action=dashboard" class="btn btn-primary-gamified w-100 py-2 fw-bold">
          Go to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<script>
let diagQuestions = [];
let diagIndex = 0;
let diagAnswers = {};

document.addEventListener('DOMContentLoaded', () => {
    fetchDiagnostic();

    document.querySelectorAll('.diag-option').forEach(btn => {
        btn.addEventListener('click', () => {
            const opt = btn.dataset.option;
            const q = diagQuestions[diagIndex];
            if (q) diagAnswers[q.id] = opt;

            document.querySelectorAll('.diag-option').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
        });
    });

    document.getElementById('nextDiagBtn').addEventListener('click', () => {
        if (diagIndex < diagQuestions.length - 1) {
            diagIndex++;
            renderDiagQuestion();
        } else {
            submitDiag();
        }
    });

    document.getElementById('prevDiagBtn').addEventListener('click', () => {
        if (diagIndex > 0) {
            diagIndex--;
            renderDiagQuestion();
        }
    });
});

function fetchDiagnostic() {
    fetch('index.php?action=api_get_diagnostic')
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                diagQuestions = res.questions;
                renderDiagQuestion();
            }
        });
}

function renderDiagQuestion() {
    const q = diagQuestions[diagIndex];
    if (!q) return;

    document.getElementById('diagnosticProgress').innerText = `Question ${diagIndex + 1} of ${diagQuestions.length}`;
    document.getElementById('diagnosticQuestionText').innerText = q.question_text;

    document.querySelectorAll('.diag-option').forEach(btn => {
        const opt = btn.dataset.option;
        btn.querySelector('.option-text').innerText = q.options[opt];
        btn.classList.toggle('selected', diagAnswers[q.id] === opt);
    });

    document.getElementById('prevDiagBtn').disabled = (diagIndex === 0);
    document.getElementById('nextDiagBtn').innerHTML = (diagIndex === diagQuestions.length - 1) ? 'Submit Quiz' : 'Save & Next <i class="bi bi-chevron-right ms-1"></i>';
}

function submitDiag() {
    fetch('index.php?action=api_submit_diagnostic', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ answers: diagAnswers })
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            document.getElementById('diagCorrect').innerText = `${res.correct_count}/${res.total_questions}`;
            document.getElementById('diagScoreText').innerText = `Accuracy: ${res.accuracy_percent}%. Your starting topic mastery levels have been calculated!`;
            launchConfetti();
            const modal = new bootstrap.Modal(document.getElementById('diagnosticModal'));
            modal.show();
        }
    });
}
</script>
