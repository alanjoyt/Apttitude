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
      Stake Speed Coins in 10-question rapid fire duels! Winner takes double the wager pot + bonus XP.
    </p>
  </div>

  <!-- Create Challenge / Wager Card -->
  <div class="col-12 col-lg-5">
    <div class="card card-gamified p-3 p-sm-4 h-100">
      <h4 class="font-serif fw-bold text-ink mb-2 mb-sm-3 d-flex align-items-center gap-2" style="font-size: clamp(1.1rem, 3.5vw, 1.35rem);">
        <i class="bi bi-plus-circle-fill fs-3" style="color: var(--accent);"></i> Create 1v1 Challenge
      </h4>
      <p class="text-ink-muted small mb-3 mb-sm-4">Stake Speed Coins in PvP duels (min 30 questions required), or test your speed safely against the AI bot.</p>

      <div class="mb-3">
        <label class="form-label small fw-bold text-ink-muted d-block mb-2">Select PvP Wager Stake (Speed Coins)</label>
        <div class="wager-buttons-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; width: 100%;">
          <button class="btn btn-outline-gamified btn-sm wager-btn active d-inline-flex align-items-center justify-content-center gap-1" data-wager="10" style="min-height: 44px; font-size: clamp(0.72rem, 2.4vw, 0.85rem); padding: 6px 4px; white-space: nowrap;"><svg class="exam-icon icon-13"><use href="#icon-coin"></use></svg> 10 Coins</button>
          <button class="btn btn-outline-gamified btn-sm wager-btn d-inline-flex align-items-center justify-content-center gap-1" data-wager="25" style="min-height: 44px; font-size: clamp(0.72rem, 2.4vw, 0.85rem); padding: 6px 4px; white-space: nowrap;"><svg class="exam-icon icon-13"><use href="#icon-coin"></use></svg> 25 Coins</button>
          <button class="btn btn-outline-gamified btn-sm wager-btn d-inline-flex align-items-center justify-content-center gap-1" data-wager="50" style="min-height: 44px; font-size: clamp(0.72rem, 2.4vw, 0.85rem); padding: 6px 4px; white-space: nowrap;"><svg class="exam-icon icon-13"><use href="#icon-coin"></use></svg> 50 Coins</button>
        </div>
        <small class="text-ink-muted d-block mt-1" style="font-size: 0.75rem;">PvP rules: 30 Qs answered required &bull; 5% pot rake &bull; Max 3 duels/day per pair</small>
      </div>

      <button onclick="submitCreateDuel()" class="btn btn-primary-gamified btn-lg fw-bold w-100 py-2.5 shadow-sm d-inline-flex align-items-center justify-content-center gap-2 mb-2" style="min-height: 48px; font-size: clamp(0.88rem, 2.8vw, 1.02rem);">
        <svg class="exam-icon icon-18"><use href="#icon-swords"></use></svg> Stake Coins & Start PvP Duel
      </button>

      <button onclick="submitCreateBotDuel()" class="btn btn-outline-gamified fw-bold w-100 py-2.5 d-inline-flex align-items-center justify-content-center gap-2" style="min-height: 44px; font-size: clamp(0.85rem, 2.6vw, 0.95rem); border-style: dashed;">
        <i class="bi bi-robot fs-5"></i> Practice vs Bot (0 Coins &middot; +10 XP)
      </button>
    </div>
  </div>

  <!-- Open Duels Lobby -->
  <div class="col-12 col-lg-7">
    <div class="card card-gamified p-3 p-sm-4 h-100">
      <h4 class="font-serif fw-bold text-ink mb-2 mb-sm-3 d-flex align-items-center gap-2" style="font-size: clamp(1.1rem, 3.5vw, 1.35rem);">
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
                <span class="small fw-bold d-inline-flex align-items-center gap-1" style="color: var(--highlight);"><svg class="exam-icon icon-13"><use href="#icon-coin"></use></svg> Wager Stake: <?= $open['wager_coins'] ?> Speed Coins</span>
              </div>
              <button onclick="startDuelPlay(<?= $open['id'] ?>, true)" class="btn btn-primary-gamified btn-sm px-4 fw-bold d-inline-flex align-items-center gap-1">
                Accept Challenge <svg class="exam-icon icon-14"><use href="#icon-swords"></use></svg>
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
                <span class="badge text-white d-inline-flex align-items-center gap-1" style="background-color: var(--success);">WON +<?= $d['wager_coins'] * 2 ?> <svg class="exam-icon icon-11"><use href="#icon-coin"></use></svg></span>
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
      <div class="modal-header border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--border) !important;">
        <h5 class="modal-title font-serif fw-bold mb-0" style="color: var(--accent);"><i class="bi bi-lightning-charge-fill me-2"></i> 1v1 Quiz Arena (10 Questions)</h5>
        <button type="button" class="btn btn-outline-danger btn-sm" id="btnForfeitDuel" onclick="confirmForfeitDuel()">
          <i class="bi bi-flag-fill me-1"></i> Forfeit Duel
        </button>
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
let duelAnswers = {};
let isAnsweringDuel = false;

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function formatSafeText(raw) {
    if (!raw) return '';
    const temp = document.createElement('div');
    temp.textContent = raw;
    let safe = temp.innerHTML;
    // Replace `code` with styled inline code
    safe = safe.replace(/`([^`]+)`/g, '<code class="font-mono px-1 py-0.5 rounded bg-surface border">$1</code>');
    return safe;
}

function copyDuelCode(btn) {
    const container = btn.closest('.tech-code-container');
    const codeEl = container ? container.querySelector('code') : null;
    if (codeEl) {
        const text = codeEl.innerText;
        navigator.clipboard.writeText(text).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2 text-success me-1"></i> Copied!';
            setTimeout(() => { btn.innerHTML = originalHtml; }, 1500);
        }).catch(() => {
            alert('Copied code!');
        });
    }
}

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
        body: JSON.stringify({ wager_coins: activeWager, csrf_token: window.CSRF_TOKEN || '' })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') {
            startDuelPlay(res.duel_id, false);
        }
    })
    .catch(err => {
        alert('Error creating duel: ' + err.message);
    });
}

function submitCreateBotDuel() {
    fetch('index.php?action=api_create_bot_duel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ csrf_token: window.CSRF_TOKEN || '' })
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            startDuelPlay(res.duel_id, false);
        } else {
            alert(res.message);
        }
    })
    .catch(err => {
        alert('Error creating bot duel: ' + err.message);
    });
}

function startDuelPlay(duelId, isJoining) {
    activeDuelId = duelId;
    if (isJoining) {
        fetch('index.php?action=api_join_duel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ duel_id: duelId, csrf_token: window.CSRF_TOKEN || '' })
        })
        .then(res => res.json())
        .then(res => {
            if (res.status !== 'success') { alert(res.message); return; }
            launchDuelModal();
        })
        .catch(err => {
            alert('Error joining duel: ' + err.message);
        });
    } else {
        launchDuelModal();
    }
}

function launchDuelModal() {
    const modalEl = document.getElementById('duelArenaModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();

    document.getElementById('duelQuizContainer').innerHTML = `
        <div class="py-4 text-center">
            <div class="spinner-border mb-3" role="status" style="color: var(--accent);"></div>
            <p class="text-ink-muted">Loading duel questions...</p>
        </div>
    `;

    fetch(`index.php?action=api_get_duel&duel_id=${activeDuelId}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.questions && res.questions.length > 0) {
                currentDuelQuestions = res.questions;
                currentDuelIndex = 0;
                duelScore = 0;
                duelAnswers = {};
                isAnsweringDuel = false;
                renderDuelQuestion();
            } else {
                document.getElementById('duelQuizContainer').innerHTML = `
                    <div class="py-4 text-center">
                        <p class="text-danger fw-bold fs-5">Unable to load duel questions.</p>
                        <p class="text-ink-muted small">${escapeHtml(res.message || 'Please refresh and try again.')}</p>
                        <button onclick="window.location.reload()" class="btn btn-outline-gamified mt-2">Close</button>
                    </div>
                `;
            }
        })
        .catch(err => {
            document.getElementById('duelQuizContainer').innerHTML = `
                <div class="py-4 text-center">
                    <p class="text-danger fw-bold fs-5">Connection Error</p>
                    <p class="text-ink-muted small">${escapeHtml(err.message)}</p>
                    <button onclick="window.location.reload()" class="btn btn-outline-gamified mt-2">Close</button>
                </div>
            `;
        });
}

function renderDuelQuestion() {
    if (currentDuelIndex >= currentDuelQuestions.length) {
        // Duel complete - submit results
        document.getElementById('duelQuizContainer').innerHTML = `
            <div class="py-4 text-center">
                <div class="spinner-border mb-3" role="status" style="color: var(--accent);"></div>
                <p class="text-ink font-serif fw-bold fs-5">Finalizing duel results...</p>
            </div>
        `;

        fetch('index.php?action=api_submit_duel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                duel_id: activeDuelId,
                score: duelScore,
                answers: duelAnswers,
                csrf_token: window.CSRF_TOKEN || ''
            })
        })
        .then(res => res.json())
        .then(res => {
            if (typeof SoundFX !== 'undefined' && SoundFX.playFanfare) {
                SoundFX.playFanfare();
            }
            if (typeof launchConfetti === 'function') {
                launchConfetti();
            }
            const totalCount = currentDuelQuestions.length || 10;
            const finalScore = (res.user_score !== undefined) ? res.user_score : duelScore;
            document.getElementById('duelQuizContainer').innerHTML = `
                <div class="py-4 text-center">
                    <h2 class="fw-bold text-success mb-2 font-serif">Duel Round Completed!</h2>
                    <p class="fs-4 text-ink font-serif">Your Score: <strong>${finalScore} / ${totalCount}</strong></p>
                    <p class="text-ink-muted mb-4 fs-6">${escapeHtml(res.message || 'Challenge completed!')}</p>
                    <button onclick="window.location.reload()" class="btn btn-primary-gamified rounded-pill px-5 fw-bold py-2">
                        Back to Arena Lobby
                    </button>
                </div>
            `;
        })
        .catch(err => {
            const totalCount = currentDuelQuestions.length || 10;
            document.getElementById('duelQuizContainer').innerHTML = `
                <div class="py-4 text-center">
                    <h2 class="fw-bold text-success mb-2 font-serif">Duel Round Completed!</h2>
                    <p class="fs-4 text-ink font-serif">Your Score: <strong>${duelScore} / ${totalCount}</strong></p>
                    <p class="text-ink-muted mb-4 fs-6">Results recorded successfully!</p>
                    <button onclick="window.location.reload()" class="btn btn-primary-gamified rounded-pill px-5 fw-bold py-2">
                        Back to Arena Lobby
                    </button>
                </div>
            `;
        });
        return;
    }

    const q = currentDuelQuestions[currentDuelIndex];
    const totalCount = currentDuelQuestions.length;

    let codeBlockHtml = '';
    if (q.code_snippet && q.code_snippet.trim() !== '') {
        const lang = (q.language || 'CODE').toUpperCase();
        codeBlockHtml = `
            <div class="tech-code-container mb-3 text-start">
                <div class="code-terminal-header d-flex justify-content-between align-items-center px-3 py-1.5" style="background-color: var(--surface-muted); border: 1.5px solid var(--border); border-bottom: none; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                    <span class="badge font-mono" style="background-color: var(--accent); color: var(--surface);">${escapeHtml(lang)}</span>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none text-ink-muted p-0" onclick="copyDuelCode(this)" title="Copy code" style="font-size: 0.8rem;">
                        <i class="bi bi-clipboard me-1"></i> Copy
                    </button>
                </div>
                <pre class="m-0 p-3" style="max-height: 240px; overflow-y: auto; background-color: var(--surface); border: 1.5px solid var(--border); border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;" tabindex="0"><code class="tech-code-body font-mono text-ink" style="font-size: 0.88rem; white-space: pre-wrap; word-break: break-word;">${escapeHtml(q.code_snippet)}</code></pre>
            </div>
        `;
    }

    document.getElementById('duelQuizContainer').innerHTML = `
        <div class="text-start">
            <div class="d-flex justify-content-between align-items-center text-ink-muted small mb-3 fw-bold">
                <span class="badge px-2 py-1" style="background-color: var(--surface-muted); color: var(--ink); border: 1px solid var(--border);">Question ${currentDuelIndex + 1} of ${totalCount}</span>
                <span class="badge px-2 py-1" style="background-color: var(--highlight-bg); color: var(--highlight-text); border: 1px solid var(--highlight-border);" id="duelLiveScore">Score: ${duelScore} / ${totalCount}</span>
            </div>
            <h5 class="font-serif fw-bold text-ink mb-3" style="line-height: 1.45;">${formatSafeText(q.question_text)}</h5>
            ${codeBlockHtml}
            <div class="d-flex flex-column gap-2 mb-3" id="duelOptionsList">
                <button type="button" onclick="handleDuelSelect('A', this)" class="option-btn text-start" data-opt="A">
                    <span class="option-key">A</span>
                    <span class="option-text">${formatSafeText(q.option_a)}</span>
                </button>
                <button type="button" onclick="handleDuelSelect('B', this)" class="option-btn text-start" data-opt="B">
                    <span class="option-key">B</span>
                    <span class="option-text">${formatSafeText(q.option_b)}</span>
                </button>
                <button type="button" onclick="handleDuelSelect('C', this)" class="option-btn text-start" data-opt="C">
                    <span class="option-key">C</span>
                    <span class="option-text">${formatSafeText(q.option_c)}</span>
                </button>
                <button type="button" onclick="handleDuelSelect('D', this)" class="option-btn text-start" data-opt="D">
                    <span class="option-key">D</span>
                    <span class="option-text">${formatSafeText(q.option_d)}</span>
                </button>
            </div>
        </div>
    `;
    isAnsweringDuel = false;
}

function handleDuelSelect(selectedOpt, btnEl) {
    if (isAnsweringDuel) return;
    isAnsweringDuel = true;

    // Lock option buttons immediately to prevent multi-clicking
    const allBtns = document.querySelectorAll('#duelOptionsList .option-btn');
    allBtns.forEach(b => {
        b.style.pointerEvents = 'none';
        b.classList.remove('selected', 'correct', 'wrong');
    });

    btnEl.classList.add('selected');

    const q = currentDuelQuestions[currentDuelIndex];
    duelAnswers[q.id] = selectedOpt;

    fetch('index.php?action=api_check_duel_answer', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            duel_id: activeDuelId,
            question_id: q.id,
            selected_option: selectedOpt,
            csrf_token: window.CSRF_TOKEN || ''
        })
    })
    .then(res => res.json())
    .then(res => {
        btnEl.classList.remove('selected');
        const isCorrect = (res.status === 'success' && res.is_correct === true);
        const correctOpt = res.correct_option || '';

        if (isCorrect) {
            btnEl.classList.add('correct');
            if (typeof SoundFX !== 'undefined' && SoundFX.playCorrect) {
                SoundFX.playCorrect();
            }
            duelScore++;
            const liveScore = document.getElementById('duelLiveScore');
            if (liveScore) {
                liveScore.innerText = `Score: ${duelScore} / ${currentDuelQuestions.length}`;
            }
        } else {
            btnEl.classList.add('wrong');
            if (typeof SoundFX !== 'undefined' && SoundFX.playIncorrect) {
                SoundFX.playIncorrect();
            }
            // Highlight the correct option in sea-green
            allBtns.forEach(b => {
                if (b.dataset.opt === correctOpt) {
                    b.classList.add('correct');
                }
            });
        }

        setTimeout(() => {
            currentDuelIndex++;
            renderDuelQuestion();
        }, 750);
    })
    .catch(err => {
        console.error('Error verifying answer:', err);
        // Fallback: advance to next question
        setTimeout(() => {
            currentDuelIndex++;
            renderDuelQuestion();
        }, 500);
    });
}

function confirmForfeitDuel() {
    if (!activeDuelId) return;
    if (confirm('Are you sure you want to forfeit this duel? Your wager will be forfeited and victory awarded to your opponent.')) {
        forfeitActiveDuel();
    }
}

function forfeitActiveDuel() {
    if (!activeDuelId) return;
    const dId = activeDuelId;
    activeDuelId = 0; // prevent repeated triggers
    fetch('index.php?action=api_forfeit_duel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ duel_id: dId, csrf_token: window.CSRF_TOKEN || '' })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message || 'Duel forfeited.');
        window.location.reload();
    })
    .catch(() => {
        window.location.reload();
    });
}

window.addEventListener('beforeunload', () => {
    if (activeDuelId && currentDuelQuestions.length > 0 && currentDuelIndex < currentDuelQuestions.length) {
        const payload = JSON.stringify({ duel_id: activeDuelId, csrf_token: window.CSRF_TOKEN || '' });
        if (navigator.sendBeacon) {
            const blob = new Blob([payload], { type: 'application/json' });
            navigator.sendBeacon('index.php?action=api_forfeit_duel', blob);
        }
    }
});
</script>
