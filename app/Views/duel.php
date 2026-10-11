<?php
$userId = $_SESSION['user_id'] ?? null;
$duelsData = DuelService::getDuels($userId);
$myDuels = $duelsData['my_duels'];
$openDuels = $duelsData['open_duels'];
?>

<div class="row g-4 justify-content-center py-3">
  <div class="col-12 text-center mb-2">
    <div class="stamp-badge mb-2">
      <i class="bi bi-lightning-charge-fill me-1"></i> Competitive Arena
    </div>
    <h1 class="font-serif fw-bold fs-2 text-ink">1v1 Real-Time Aptitude Duels</h1>
    <p class="text-ink-muted mx-auto" style="max-width: 600px;">
      Stake Speed Coins in 5-question rapid fire duels! Winner takes double the wager pot + bonus XP.
    </p>
  </div>

  <!-- Create Challenge / Wager Card -->
  <div class="col-md-5">
    <div class="card card-gamified p-4 h-100">
      <h4 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle-fill fs-3" style="color: var(--accent);"></i> Create 1v1 Challenge
      </h4>
      <p class="text-ink-muted small mb-4">Set your Speed Coins stake and wait for an opponent to join, or challenge a friend directly.</p>

      <div class="mb-4">
        <label class="form-label small fw-bold text-ink-muted">Select Wager Stake (Speed Coins)</label>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-gamified btn-sm wager-btn active d-inline-flex align-items-center gap-1" data-wager="10"><svg class="exam-icon" style="width:13px;height:13px;"><use href="#icon-coin"></use></svg> 10 Coins</button>
          <button class="btn btn-outline-gamified btn-sm wager-btn d-inline-flex align-items-center gap-1" data-wager="25"><svg class="exam-icon" style="width:13px;height:13px;"><use href="#icon-coin"></use></svg> 25 Coins</button>
          <button class="btn btn-outline-gamified btn-sm wager-btn d-inline-flex align-items-center gap-1" data-wager="50"><svg class="exam-icon" style="width:13px;height:13px;"><use href="#icon-coin"></use></svg> 50 Coins</button>
        </div>
      </div>

      <button onclick="submitCreateDuel()" class="btn btn-primary-gamified btn-lg fw-bold w-100 py-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
        <svg class="exam-icon" style="width:18px;height:18px;"><use href="#icon-swords"></use></svg> Stake Coins & Start Duel Arena
      </button>
    </div>
  </div>

  <!-- Open Duels Lobby -->
  <div class="col-md-7">
    <div class="card card-gamified p-4 h-100">
      <h4 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-door-open-fill fs-3" style="color: var(--highlight);"></i> Open Wager Challenges Lobby
      </h4>

      <?php if (empty($openDuels)): ?>
        <p class="text-ink-muted small mb-0">No open wager challenges in lobby right now. Create a new duel challenge on the left!</p>
      <?php else: ?>
        <div class="d-flex flex-column gap-2 mb-4">
          <?php foreach ($openDuels as $open): ?>
            <div class="d-flex justify-content-between align-items-center p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
              <div>
                <strong class="d-block text-ink font-serif"><?= htmlspecialchars($open['challenger_name']) ?> (<?= $open['challenger_tier'] ?>)</strong>
                <span class="small fw-bold d-inline-flex align-items-center gap-1" style="color: var(--highlight);"><svg class="exam-icon" style="width:13px;height:13px;"><use href="#icon-coin"></use></svg> Wager Stake: <?= $open['wager_coins'] ?> Speed Coins</span>
              </div>
              <button onclick="startDuelPlay(<?= $open['id'] ?>, true)" class="btn btn-primary-gamified btn-sm px-4 fw-bold d-inline-flex align-items-center gap-1">
                Accept Challenge <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-swords"></use></svg>
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Recent Duel History -->
      <h6 class="font-serif fw-bold text-ink mb-2">My Duel History</h6>
      <div class="d-flex flex-column gap-2">
        <?php foreach ($myDuels as $d): 
          $isWinner = ($d['winner_id'] == $userId);
          $isDraw = ($d['status'] === 'completed' && $d['winner_id'] === null);
        ?>
          <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div>
              <span class="small fw-bold text-ink font-serif">Vs <?= htmlspecialchars($d['opponent_name'] ?? 'Open Opponent') ?></span>
              <small class="text-ink-muted d-block fs-8">Score: <?= $d['challenger_score'] ?> - <?= $d['opponent_score'] ?></small>
            </div>
            <div>
              <?php if ($d['status'] === 'waiting'): ?>
                <span class="badge" style="background-color: var(--highlight-bg); color: var(--highlight-text); border: 1.5px solid var(--highlight-border);">Waiting...</span>
              <?php elseif ($isWinner): ?>
                <span class="badge text-white d-inline-flex align-items-center gap-1" style="background-color: var(--success);">WON +<?= $d['wager_coins'] * 2 ?> <svg class="exam-icon" style="width:11px;height:11px;"><use href="#icon-coin"></use></svg></span>
              <?php elseif ($isDraw): ?>
                <span class="badge text-white" style="background-color: var(--ink-muted);">DRAW</span>
              <?php else: ?>
                <span class="badge text-white" style="background-color: var(--danger);">LOST</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</div>

<!-- Modal: Interactive 1v1 Duel Arena -->
<div class="modal fade" id="duelArenaModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content text-ink border rounded-4 p-3" style="background-color: var(--surface); border-color: var(--border) !important;">
      <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
        <h5 class="modal-title font-serif fw-bold" style="color: var(--accent);"><i class="bi bi-lightning-charge-fill me-2"></i> 1v1 Quiz Arena (5 Questions)</h5>
      </div>
      <div class="modal-body text-center p-4">
        <div id="duelQuizContainer">
          <div class="spinner-border mb-3" role="status" style="color: var(--accent);"></div>
          <p class="text-ink-muted">Loading duel questions...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let activeWager = 10;
let currentDuelQuestions = [];
let currentDuelIndex = 0;
let activeDuelId = 0;
let duelScore = 0;

document.querySelectorAll('.wager-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.wager-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeWager = parseInt(btn.dataset.wager);
    });
});

function submitCreateDuel() {
    fetch('index.php?action=api_create_duel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ wager_coins: activeWager })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') {
            startDuelPlay(res.duel_id, false);
        }
    });
}

function startDuelPlay(duelId, isJoining) {
    activeDuelId = duelId;
    if (isJoining) {
        fetch('index.php?action=api_join_duel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ duel_id: duelId })
        })
        .then(res => res.json())
        .then(res => {
            if (res.status !== 'success') { alert(res.message); return; }
            launchDuelModal();
        });
    } else {
        launchDuelModal();
    }
}

function launchDuelModal() {
    const modal = new bootstrap.Modal(document.getElementById('duelArenaModal'));
    modal.show();

    fetch(`index.php?action=api_get_duel&duel_id=${activeDuelId}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.questions) {
                currentDuelQuestions = res.questions;
                currentDuelIndex = 0;
                duelScore = 0;
                renderDuelQuestion();
            }
        });
}

function renderDuelQuestion() {
    if (currentDuelIndex >= currentDuelQuestions.length) {
        // Finish duel
        fetch('index.php?action=api_submit_duel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ duel_id: activeDuelId, score: duelScore })
        })
        .then(res => res.json())
        .then(res => {
            SoundFX.playFanfare();
            launchConfetti();
            document.getElementById('duelQuizContainer').innerHTML = `
                <div class="py-4">
                    <h2 class="fw-bold text-success mb-2">Duel Round Completed!</h2>
                    <p class="fs-4 text-ink font-serif">Your Score: <strong>${duelScore} / 5</strong></p>
                    <p class="text-ink-muted mb-4">${res.message}</p>
                    <button onclick="window.location.reload()" class="btn btn-primary-gamified rounded-pill px-5 fw-bold py-2">
                        Back to Arena Lobby
                    </button>
                </div>
            `;
        });
        return;
    }

    const q = currentDuelQuestions[currentDuelIndex];
    document.getElementById('duelQuizContainer').innerHTML = `
        <div class="text-start">
            <div class="d-flex justify-content-between text-ink-muted small mb-3 fw-bold">
                <span>Question ${currentDuelIndex + 1} of 5</span>
                <span>Score: ${duelScore}</span>
            </div>
            <h5 class="font-serif fw-bold text-ink mb-4">${q.question_text}</h5>
            <div class="d-flex flex-column gap-2 mb-4">
                <button onclick="answerDuel('${q.correct_option}', 'A')" class="option-btn">
                    <span class="option-key">A</span>
                    <span class="option-text">${q.option_a}</span>
                </button>
                <button onclick="answerDuel('${q.correct_option}', 'B')" class="option-btn">
                    <span class="option-key">B</span>
                    <span class="option-text">${q.option_b}</span>
                </button>
                <button onclick="answerDuel('${q.correct_option}', 'C')" class="option-btn">
                    <span class="option-key">C</span>
                    <span class="option-text">${q.option_c}</span>
                </button>
                <button onclick="answerDuel('${q.correct_option}', 'D')" class="option-btn">
                    <span class="option-key">D</span>
                    <span class="option-text">${q.option_d}</span>
                </button>
            </div>
        </div>
    `;
}

function answerDuel(correctOpt, selectedOpt) {
    if (selectedOpt === correctOpt) {
        SoundFX.playCorrect();
        duelScore++;
    } else {
        SoundFX.playIncorrect();
    }
    currentDuelIndex++;
    renderDuelQuestion();
}
</script>
