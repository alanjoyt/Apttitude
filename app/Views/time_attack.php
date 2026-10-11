<?php
$db = Database::getConnection();
$userId = $_SESSION['user_id'] ?? null;

// Fetch user high scores
$highScores = [];
if ($userId) {
    $stmt = $db->prepare("SELECT * FROM time_attack_runs WHERE user_id = ? ORDER BY score DESC LIMIT 5");
    $stmt->execute([$userId]);
    $highScores = $stmt->fetchAll();
}
?>

<div class="row g-4 justify-content-center py-3">
  <div class="col-12 text-center mb-2">
    <div class="stamp-badge mb-2">
      <i class="bi bi-stopwatch-fill me-1"></i> Rapid Fire Mode
    </div>
    <h1 class="font-serif fw-bold fs-2 text-ink">60-Second Aptitude Time Attack</h1>
    <p class="text-ink-muted mx-auto" style="max-width: 600px;">
      60 Seconds on the clock! Answer as many questions as possible. Each correct answer adds **+3 Seconds** to the timer!
    </p>
  </div>

  <!-- Time Attack Arena -->
  <div class="col-lg-8 col-md-10">
    <div class="card card-gamified p-4 text-center" id="taStartCard">
      <div class="my-3" style="color: var(--accent);"><svg class="exam-icon" style="width:56px;height:56px;"><use href="#icon-clock"></use></svg></div>
      <h3 class="font-serif fw-bold text-ink mb-2">Ready for the Sprint?</h3>
      <p class="text-ink-muted mb-4">Questions keep coming until the timer runs out. Build combo streaks for maximum score multipliers!</p>
      <button onclick="startTimeAttack()" class="btn btn-primary-gamified btn-lg fw-bold px-5 py-3 shadow-sm fs-5">
        Start 60s Time Attack <i class="bi bi-play-circle-fill ms-1"></i>
      </button>
    </div>

    <!-- Active Time Attack Gameplay Workspace (Hidden initially) -->
    <div class="card card-gamified p-4" id="taPlayCard" style="display: none;">
      
      <!-- Top Timer & Multiplier Bar -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <span class="badge rounded-pill px-4 py-2 fs-5 d-inline-flex align-items-center gap-1" id="taTimerDisplay" style="background-color: var(--surface); color: var(--danger); border: 1px solid var(--border);">
          <svg class="exam-icon" style="width:18px;height:18px;"><use href="#icon-clock"></use></svg> <span id="taTimerSecs">60s</span>
        </span>
        <span class="badge rounded-pill px-3 py-2 fs-6 fw-bold" id="taComboDisplay" style="background-color: var(--surface); color: var(--highlight); border: 1px solid var(--border);">
          1x Multiplier
        </span>
        <span class="badge rounded-pill px-4 py-2 fs-5" id="taScoreDisplay" style="background-color: var(--surface); color: var(--success); border: 1px solid var(--border);">
          Score: 0
        </span>
      </div>

      <!-- Question Box -->
      <div class="mb-4">
        <h4 class="font-serif fw-bold text-ink lh-base text-start" id="taQuestionText">Loading question...</h4>
      </div>

      <!-- Options -->
      <div class="d-flex flex-column gap-3 mb-3" id="taOptionsContainer">
        <!-- Buttons rendered dynamically -->
      </div>
    </div>
  </div>

  <!-- High Scores Sidebar -->
  <?php if (!empty($highScores)): ?>
    <div class="col-lg-4 col-md-10">
      <div class="card card-gamified p-4">
        <h5 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-trophy-fill fs-4" style="color: var(--highlight);"></i> Personal High Scores
        </h5>
        <div class="d-flex flex-column gap-2">
          <?php foreach ($highScores as $hs): ?>
            <div class="d-flex justify-content-between align-items-center p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
              <div>
                <strong class="font-serif fs-5 d-block" style="color: var(--highlight);"><?= number_format($hs['score']) ?> Pts</strong>
                <span class="text-ink-muted fs-8"><?= $hs['correct_count'] ?> Correct &bull; Max Combo <?= $hs['max_combo'] ?>x</span>
              </div>
              <small class="text-ink-muted"><?= date('M d', strtotime($hs['created_at'])) ?></small>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
let taTimer = 60;
let taInterval = null;
let taScore = 0;
let taCorrect = 0;
let taCombo = 1;
let taMaxCombo = 1;
let currentTaQuestion = null;

function startTimeAttack() {
    document.getElementById('taStartCard').style.display = 'none';
    document.getElementById('taPlayCard').style.display = 'block';
    
    taTimer = 60;
    taScore = 0;
    taCorrect = 0;
    taCombo = 1;
    taMaxCombo = 1;
    
    updateTaUI();
    loadTaQuestion();

    clearInterval(taInterval);
    taInterval = setInterval(() => {
        taTimer--;
        updateTaUI();
        if (taTimer <= 0) {
            endTaGame();
        }
    }, 1000);
}

function updateTaUI() {
    const sEl = document.getElementById('taTimerSecs');
    if (sEl) sEl.innerText = `${taTimer}s`;
    document.getElementById('taScoreDisplay').innerText = `Score: ${taScore}`;
    document.getElementById('taComboDisplay').innerText = `${taCombo}x Multiplier`;
}

function loadTaQuestion() {
    fetch('index.php?action=api_next_question')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                currentTaQuestion = res.data;
                document.getElementById('taQuestionText').innerText = currentTaQuestion.question_text;
                
                const opts = currentTaQuestion.options;
                document.getElementById('taOptionsContainer').innerHTML = `
                    <button onclick="submitTaAnswer('A')" class="btn btn-outline-secondary text-start p-3 rounded-3 fw-semibold">A. ${opts.A}</button>
                    <button onclick="submitTaAnswer('B')" class="btn btn-outline-secondary text-start p-3 rounded-3 fw-semibold">B. ${opts.B}</button>
                    <button onclick="submitTaAnswer('C')" class="btn btn-outline-secondary text-start p-3 rounded-3 fw-semibold">C. ${opts.C}</button>
                    <button onclick="submitTaAnswer('D')" class="btn btn-outline-secondary text-start p-3 rounded-3 fw-semibold">D. ${opts.D}</button>
                `;
            }
        });
}

function submitTaAnswer(selectedOpt) {
    if (!currentTaQuestion) return;

    fetch('index.php?action=api_submit_answer', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            question_id: currentTaQuestion.id,
            selected_option: selectedOpt,
            confidence: 'sure',
            time_taken_seconds: 5
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.is_correct) {
            SoundFX.playCorrect();
            taCorrect++;
            taTimer += 3;
            taScore += (100 * taCombo);
            taCombo++;
            if (taCombo > taMaxCombo) taMaxCombo = taCombo;
            showFloatingXP(100 * taCombo, document.getElementById('taScoreDisplay'));
        } else {
            SoundFX.playIncorrect();
            taCombo = 1;
        }
        updateTaUI();
        loadTaQuestion();
    })
    .catch(err => {
        console.error(err);
        loadTaQuestion();
    });
}

function endTaGame() {
    clearInterval(taInterval);
    SoundFX.playFanfare();
    launchConfetti();

    document.getElementById('taPlayCard').innerHTML = `
        <div class="py-4 text-center">
            <h2 class="fw-bold text-warning mb-2">TIME EXPIRED!</h2>
            <div class="display-3 fw-bold text-dark mb-2">${taScore} Pts</div>
            <p class="text-secondary fs-5 mb-4">Total Correct: ${taCorrect} &bull; Max Combo: ${taMaxCombo}x</p>
            <div class="d-flex justify-content-center gap-3">
                <button onclick="startTimeAttack()" class="btn btn-primary-gamified rounded-pill px-4 fw-bold">Play Again</button>
                <a href="index.php?action=dashboard" class="btn btn-outline-secondary rounded-pill px-4">Dashboard</a>
            </div>
        </div>
    `;
}
</script>
