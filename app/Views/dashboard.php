<?php
$user = $dashboardData['user'] ?? [
  'name' => 'Student',
  'tier' => 'Bronze',
  'current_streak' => 0,
  'total_xp' => 0,
  'coins' => 0,
  'onboarding_completed' => 0
];
$tierProgress = $dashboardData['tier_progress'] ?? ['current_tier' => 'Bronze', 'next_tier' => 'Silver', 'xp_needed' => 100, 'progress_percent' => 0];
$topicStats = $dashboardData['topic_stats'] ?? [];
$weakDeck = $dashboardData['weak_deck'] ?? [];
?>

<div class="row g-4 py-3">
  <?php 
    $todaySolved = (int)($dashboardData['today_solved'] ?? 0);
    $lastPracticed = $dashboardData['last_practiced'] ?? null;
    $fixPlan = $dashboardData['fix_plan'] ?? null;
  ?>

  <?php if (!empty($fixPlan)): ?>
  <!-- 7-Day Fix Plan Active Card -->
  <div class="col-12">
    <div class="card card-gamified p-3 position-relative overflow-hidden" style="border: 2px solid var(--accent); background: linear-gradient(135deg, rgba(var(--accent-rgb, 43, 90, 237), 0.04) 0%, rgba(var(--surface-rgb, 255, 255, 255), 1) 100%);">
      <?php if (($fixPlan['plan_type'] ?? '') === 'needs_data'): ?>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 rounded-3 bg-primary-tint text-primary fs-4">
              <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
              <h6 class="font-serif fw-bold text-ink mb-1">7-Day Targeted Fix Plan</h6>
              <p class="small text-ink-muted mb-0">More mock data needed to identify your specific weak subtopics. Complete another mock test to unlock your customized plan.</p>
            </div>
          </div>
          <a href="index.php?action=mock" class="btn btn-sm btn-outline-primary fw-bold px-3">Take a Mock</a>
        </div>
      <?php else: 
        $currentDay = $fixPlan['current_day'] ?? null;
        $dayNo = $currentDay['day_no'] ?? 1;
        $dayTitle = $currentDay['title'] ?? 'Targeted Practice';
        $dayStatus = $currentDay['status'] ?? 'pending';
        $completedCount = (int)($fixPlan['completed_count'] ?? 0);
        $percentProgress = round(($completedCount / 7) * 100);
      ?>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 rounded-3 bg-accent-tint text-accent fs-3">
              <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div>
              <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="badge bg-primary text-white small px-2.5 py-0.5 rounded-pill fw-bold">Day <?= (int)$dayNo ?> of 7</span>
                <span class="badge bg-secondary-subtle text-ink small px-2 py-0.5 rounded-pill"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $currentDay['task_type'] ?? 'Practice'))) ?></span>
                <?php if ($dayStatus === 'completed'): ?>
                  <span class="badge bg-success text-white small px-2 py-0.5 rounded-pill"><i class="bi bi-check2"></i> Completed Today</span>
                <?php endif; ?>
              </div>
              <h6 class="font-serif fw-bold text-ink mb-1"><?= htmlspecialchars($dayTitle) ?></h6>
              <p class="small text-ink-muted mb-0">
                Focus on high-leverage subtopics. Retest on Day 7 to review your progress.
                <?php if (!empty($fixPlan['retest_accuracy'])): ?>
                  <strong class="text-accent ms-1">Retest Result: <?= number_format($fixPlan['retest_accuracy'], 1) ?>% (Baseline: <?= number_format($fixPlan['baseline_accuracy'], 1) ?>%)</strong>
                <?php endif; ?>
              </p>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <?php if (!empty($currentDay['subtopic_id'])): ?>
              <a href="index.php?action=practice&subtopic_id=<?= (int)$currentDay['subtopic_id'] ?>" class="btn btn-primary-gamified fw-bold px-3 py-2 text-nowrap d-inline-flex align-items-center gap-1 shadow-sm">
                <?= $dayStatus === 'completed' ? 'Practice Again' : 'Start Day Task' ?> <i class="bi bi-arrow-right"></i>
              </a>
            <?php else: ?>
              <a href="index.php?action=practice" class="btn btn-primary-gamified fw-bold px-3 py-2 text-nowrap d-inline-flex align-items-center gap-1 shadow-sm">
                Start Task <i class="bi bi-arrow-right"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
        <div class="progress mt-3" style="height: 6px;" title="<?= $completedCount ?>/7 Days Completed">
          <div class="progress-bar bg-accent" role="progressbar" aria-label="7-Day Fix Plan completion progress" style="width: <?= $percentProgress ?>%;" aria-valuenow="<?= $percentProgress ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Mobile Quick Action Strip: Daily Goal Ring & 5-Min Blitz -->
  <div class="col-12 col-md-6">
    <div class="card card-gamified p-3 h-100">
      <div class="d-flex align-items-center gap-3">
        <div class="goal-ring-container flex-shrink-0 position-relative" style="width:72px;height:72px;display:flex;align-items:center;justify-content:center;">
          <svg width="72" height="72" viewBox="0 0 72 72" aria-hidden="true">
            <circle cx="36" cy="36" r="30" stroke="var(--border)" stroke-width="6" fill="none" />
            <circle cx="36" cy="36" r="30" stroke="var(--marker-orange)" stroke-width="6" fill="none"
                    stroke-dasharray="188.5" stroke-dashoffset="<?= max(0, round(188.5 - (188.5 * min(10, $todaySolved) / 10))) ?>"
                    stroke-linecap="round" class="goal-ring-circle" />
          </svg>
          <span class="position-absolute font-mono fw-bold fs-7"><?= min(10, $todaySolved) ?>/10</span>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2 mb-1">
            <h6 class="font-serif fw-bold text-ink mb-0">Daily Goal Ring</h6>
            <?php if ($todaySolved >= 10): ?>
              <span class="badge bg-success text-white small px-2 py-0.5 rounded-pill"><i class="bi bi-check-lg"></i> Done</span>
            <?php endif; ?>
          </div>
          <p class="small text-ink-muted mb-0">
            <?= $todaySolved >= 10 
                ? 'Goal achieved! +3 Daily Speed Coins claimed.' 
                : (10 - $todaySolved) . ' more questions needed today for +3 bonus coins.' ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6">
    <div class="card card-gamified p-3 h-100 border-2" style="border-color: var(--marker-orange) !important;">
      <div class="d-flex align-items-center justify-content-between gap-2">
        <div>
          <span class="badge rounded-pill px-2.5 py-1 text-white bg-danger small fw-bold mb-1">
            <i class="bi bi-stopwatch-fill me-1"></i> 5 MIN RAPID SPRINT
          </span>
          <h6 class="font-serif fw-bold text-ink mb-1">Quick Practice Blitz</h6>
          <p class="small text-ink-muted mb-0">5 instant questions tailored to your weakest topic.</p>
        </div>
        <a href="index.php?action=practice&quick=1&mode=quiz" class="btn btn-primary-gamified fw-bold px-3 py-2 text-nowrap d-inline-flex align-items-center gap-1 shadow-sm">
          Start <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
  </div>

  <?php if ($lastPracticed): ?>
  <!-- Continue Where You Left Off Card -->
  <div class="col-12">
    <div class="card card-gamified p-3 d-flex flex-row align-items-center justify-content-between flex-wrap gap-2" style="background-color: var(--surface); border: 2px dashed var(--accent);">
      <div class="d-flex align-items-center gap-3">
        <div class="p-2.5 rounded-3 bg-accent-tint text-accent">
          <i class="bi bi-bookmark-check-fill fs-3"></i>
        </div>
        <div>
          <span class="text-uppercase small fw-bold text-ink-muted">Continue Where You Left Off</span>
          <h6 class="font-serif fw-bold text-ink mb-0"><?= htmlspecialchars($lastPracticed['topic_name']) ?> &bull; <?= htmlspecialchars($lastPracticed['subtopic_name']) ?></h6>
        </div>
      </div>
      <a href="index.php?action=practice&subtopic_id=<?= (int)$lastPracticed['subtopic_id'] ?>&mode=quiz" class="btn btn-outline-gamified fw-bold px-3 py-2 d-inline-flex align-items-center gap-1">
        <i class="bi bi-play-circle-fill text-accent"></i> Resume Practice
      </a>
    </div>
  </div>
  <?php endif; ?>

  <?php if (empty($user['onboarding_completed'])): ?>
    <div class="col-12">
      <div class="exam-notice-banner p-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background-color: var(--notice-bg); color: var(--notice-text); border: 1.5px solid var(--notice-border);">
        <div class="d-flex align-items-center gap-3">
          <i class="bi bi-lightning-charge-fill fs-3" style="color: var(--highlight);"></i>
          <div>
            <strong class="font-serif d-block" style="color: var(--notice-text);">Take your 3-Minute Diagnostic Test!</strong>
            <span class="small" style="color: var(--ink-muted);">Skip level 1 practice by proving your starting topic mastery.</span>
          </div>
        </div>
        <a href="index.php?action=diagnostic" class="btn btn-primary-gamified btn-sm fw-bold px-3" style="background-color: #14213D !important; color: #FFFFFF !important;">
          Start Diagnostic
        </a>
      </div>
    </div>
  <?php endif; ?>

  <!-- Community Contribution: Submit a Placement Question -->
  <div class="col-12 col-md-6">
    <div class="card card-gamified p-3 h-100" style="border: 1.5px dashed var(--accent);">
      <div class="d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-3 min-w-0">
          <div class="p-2.5 rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center" style="background-color: var(--surface); border: 1.5px solid var(--border); width:46px; height:46px;">
            <svg class="exam-icon text-success icon-22"><use href="#icon-rocket"></use></svg>
          </div>
          <div class="min-w-0">
            <span class="badge bg-success-subtle text-success border border-success-subtle fs-8 mb-1">+50 XP &bull; +10 Speed Coins</span>
            <h6 class="font-serif fw-bold text-ink mb-0">Submit a Placement Question</h6>
            <p class="small text-ink-muted mb-0">Contribute questions from campus drives. Earn XP once verified!</p>
          </div>
        </div>
        <button type="button" class="btn btn-primary-gamified btn-sm fw-bold px-3 text-nowrap flex-shrink-0"
          data-bs-toggle="modal" data-bs-target="#submitQuestionModal">
          <svg class="exam-icon me-1 icon-14"><use href="#icon-rocket"></use></svg> Submit Q
        </button>
      </div>
    </div>
  </div>

  <!-- Top User Profile & Level Progress Banner -->
  <div class="col-12">
    <div class="card card-gamified hero-gamified-banner p-4">
      <div class="row align-items-center g-3">
        <div class="col-lg-7">
          <div class="d-flex align-items-center gap-3 mb-2">
            <h3 class="font-serif fw-bold text-ink mb-0">Welcome back, <?= htmlspecialchars($user['name']) ?>!</h3>
            <span class="stamp-badge py-1 px-3 fw-bold d-inline-flex align-items-center gap-1">
              <svg class="exam-icon icon-14"><use href="#icon-crown"></use></svg> <?= $user['tier'] ?> League
            </span>
          </div>
          <p class="text-ink-muted small mb-3">Your placement readiness is active. Complete today's practice session to protect your streak!</p>

          <!-- 7-Day Weekly Streak Activity Calendar -->
          <div class="streak-calendar-card p-3 rounded-4 border bg-surface mb-3" style="border-color: var(--border) !important;">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="small fw-bold text-ink"><i class="bi bi-fire text-danger animate-pulse me-1"></i> 7-Day Practice Streak Tracking</span>
              <span class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: var(--bg); color: var(--ink-muted); border: 1px solid var(--border);">
                <svg class="exam-icon icon-13"><use href="#icon-shield"></use></svg> <?= $user['streak_freezes'] ?? 1 ?> Streak Freezes Active
              </span>
            </div>
            <div class="streak-calendar-grid">
              <?php 
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                $currentDayIdx = date('N') - 1; // 0 for Mon, 6 for Sun
                $streak = (int)$user['current_streak'];
                foreach ($days as $idx => $dayName):
                  $isActive = ($streak > 0 && ($currentDayIdx - $idx >= 0) && ($currentDayIdx - $idx < $streak));
                  $isToday = ($idx === $currentDayIdx);
              ?>
                <div class="streak-day-bubble <?= $isActive ? 'active' : '' ?> <?= $isToday ? 'today' : '' ?>">
                  <span><?= $dayName ?></span>
                  <span class="mt-1 d-inline-flex align-items-center justify-content-center"><?= $isActive ? '<svg class="exam-icon text-danger icon-14"><use href="#icon-flame"></use></svg>' : ($isToday ? '<svg class="exam-icon text-primary icon-14"><use href="#icon-target"></use></svg>' : '&bull;') ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="col-lg-5 text-lg-end">
          <div class="d-flex flex-column align-items-lg-end gap-2">
            <div class="d-flex gap-2">
              <div class="stat-pill-gamified py-2 px-3 fs-6 d-inline-flex align-items-center gap-1"><svg class="exam-icon text-danger icon-14"><use href="#icon-flame"></use></svg> <strong><?= $user['current_streak'] ?></strong>d Streak</div>
              <div class="stat-pill-gamified py-2 px-3 fs-6 d-inline-flex align-items-center gap-1"><svg class="exam-icon text-warning icon-14"><use href="#icon-star"></use></svg> <strong><?= number_format($user['total_xp']) ?></strong> XP</div>
              <div class="stat-pill-gamified py-2 px-3 fs-6 d-inline-flex align-items-center gap-1"><svg class="exam-icon icon-14"><use href="#icon-coin"></use></svg> <strong><?= number_format($user['coins']) ?></strong></div>
            </div>
            
            <a href="index.php?action=practice" class="btn btn-primary-gamified btn-lg fw-bold px-4 py-2.5 mt-2 shadow-sm">
              <i class="bi bi-controller me-1"></i> Continue Placement Practice
            </a>
          </div>
        </div>
      </div>

      <!-- Level Progress Bar to Next Tier -->
      <div class="mt-3 pt-3 border-top" style="border-color: var(--border) !important;">
        <div class="d-flex justify-content-between text-ink-muted small mb-1 fw-bold">
          <span>League Level: <?= $tierProgress['current_tier'] ?></span>
          <span>Next Rank: <?= $tierProgress['next_tier'] ?> (<?= number_format($tierProgress['xp_needed']) ?> XP needed)</span>
        </div>
        <div class="progress progress-gamified" style="height: 10px;">
          <div class="progress-bar-fill" role="progressbar" aria-label="Tier Progress" aria-valuenow="<?= (int)$tierProgress['progress_percent'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $tierProgress['progress_percent'] ?>%;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Daily Quests Board Widget -->
  <div class="col-12">
    <div class="card card-gamified p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-bullseye fs-4" style="color: var(--highlight);"></i> Daily Quests Board
        </h5>
        <span class="badge rounded-pill px-3 py-1 text-uppercase small fw-bold" style="background-color: var(--bg); color: var(--ink-muted); border: 1px solid var(--border);">
          Resets Midnight
        </span>
      </div>
      <div id="dailyQuestsContainer">
        <div class="text-center py-3 text-ink-muted small">Loading today's active daily quests...</div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card card-gamified p-4 h-100">
      <h5 class="font-serif fw-bold d-flex align-items-center gap-2 mb-2" style="color: var(--accent);">
        <i class="bi bi-brain-fill fs-4"></i> Spaced Repetition Revision
      </h5>
      <p class="text-ink-muted small mb-3">Questions you got wrong are weighted by confidence and scheduled for smart revision.</p>
      
      <div class="d-flex justify-content-between align-items-center p-3 rounded-4 mb-3 border" style="background-color: var(--bg); border-color: var(--border) !important;">
        <div>
          <div class="fs-4 font-serif fw-bold" style="color: var(--highlight);"><?= count($weakDeck) ?> Questions</div>
          <div class="text-ink-muted small">Ready in Weak Topics Deck</div>
        </div>
        <a href="index.php?action=practice" class="btn btn-primary-gamified btn-sm px-3">
          Revise Deck <i class="bi bi-arrow-repeat ms-1"></i>
        </a>
      </div>

      <div class="small text-ink-muted">
        <?php foreach (array_slice($weakDeck, 0, 3) as $weak): ?>
          <div class="border-bottom py-1 text-truncate" style="border-color: var(--border) !important;">
            <svg class="exam-icon text-warning me-1 icon-14"><use href="#icon-alert-triangle"></use></svg> <?= htmlspecialchars($weak['question_text']) ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Topic Proficiency Radar Chart Card -->
  <div class="col-md-6">
    <div class="card card-gamified p-4 h-100">
      <h5 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-pie-chart-fill fs-4" style="color: var(--accent);"></i> Topic Proficiency Radar
      </h5>
      <div style="height: 220px; position: relative;">
        <canvas id="topicRadarChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Mistake Breakdown Analytical Card -->
  <div class="col-12">
    <div class="card card-gamified p-4">
      <h5 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-4" style="color: var(--danger);"></i> Mistake Breakdown Analysis
      </h5>
      <div class="row g-3 text-center">
        <?php 
          $breakdown = $dashboardData['mistake_breakdown'] ?? [];
          $conceptGap = $breakdown['concept_gap'] ?? 0;
          $calcErr = $breakdown['calculation_error'] ?? 0;
          $misread = $breakdown['misread_question'] ?? 0;
          $timeOut = $breakdown['ran_out_of_time'] ?? 0;
        ?>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold d-flex align-items-center justify-content-center gap-1" style="color: var(--danger);"><svg class="exam-icon icon-24"><use href="#icon-brain"></use></svg> <?= $conceptGap ?></div>
            <div class="text-ink-muted small">Concept Gaps</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold d-flex align-items-center justify-content-center gap-1" style="color: var(--highlight);"><svg class="exam-icon icon-24"><use href="#icon-calculator"></use></svg> <?= $calcErr ?></div>
            <div class="text-ink-muted small">Calculation Errors</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold d-flex align-items-center justify-content-center gap-1" style="color: var(--accent);"><svg class="exam-icon icon-24"><use href="#icon-eye"></use></svg> <?= $misread ?></div>
            <div class="text-ink-muted small">Misread Questions</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold text-ink-muted d-flex align-items-center justify-content-center gap-1"><svg class="exam-icon icon-24"><use href="#icon-clock"></use></svg> <?= $timeOut ?></div>
            <div class="text-ink-muted small">Timeouts</div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
(window.onChartReady || function(fn) {
    if (typeof Chart !== 'undefined') fn();
    else window.addEventListener('load', fn, { once: true });
})(() => {
    const chartCanvas = document.getElementById('topicRadarChart');
    if (!chartCanvas || typeof Chart === 'undefined') return;
    const ctx = chartCanvas.getContext('2d');
    
    const labels = <?= json_encode(array_column($topicStats, 'topic_name')) ?>;
    const dataAccuracy = <?= json_encode(array_map(function($t) {
        return $t['total_attempts'] > 0 ? round(($t['correct_attempts'] / $t['total_attempts']) * 100) : 50;
    }, $topicStats)) ?>;

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels.length ? labels : ['Quant', 'Logical Reasoning', 'Verbal', 'Data Interpretation'],
            datasets: [{
                label: 'Accuracy %',
                data: dataAccuracy.length ? dataAccuracy : [82, 75, 60, 45],
                backgroundColor: 'rgba(30, 58, 95, 0.15)',
                borderColor: '#1E3A5F',
                pointBackgroundColor: '#1E3A5F',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(31, 42, 36, 0.12)' },
                    grid: { color: 'rgba(31, 42, 36, 0.12)' },
                    pointLabels: { color: '#1F2A24', font: { family: 'Lora, serif', size: 12 } },
                    ticks: { display: false, min: 0, max: 100 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>

<!-- 3-Step First-Run Onboarding Tour Modal -->
<div class="modal fade" id="onboardingTourModal" tabindex="-1" aria-labelledby="onboardingTourTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content exam-sheet-card border-2" style="border-color: var(--border-strong) !important;">
      <div class="modal-header border-bottom">
        <h5 class="modal-title font-serif fw-bold text-ink" id="onboardingTourTitle">
          <i class="bi bi-compass-fill text-warning me-1"></i> Quick Tour: Welcome to Apttitudee
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <!-- Step 1 -->
        <div id="tourStep1" class="tour-step">
          <div class="display-5 mb-2 text-warning"><i class="bi bi-bullseye"></i></div>
          <h5 class="font-serif fw-bold text-ink">1. Placement Question Banks</h5>
          <p class="text-ink-muted small">
            Practice company test patterns (TCS, Infosys, Wipro, Accenture, Innovature). All questions feature full step-by-step explanations and detailed solution keys.
          </p>
          <button type="button" class="btn btn-primary-gamified fw-bold px-4 mt-2" onclick="showTourStep(2)">
            Next: Rewards & Economy <i class="bi bi-arrow-right"></i>
          </button>
        </div>
        <!-- Step 2 -->
        <div id="tourStep2" class="tour-step d-none">
          <div class="display-5 mb-2 text-danger"><i class="bi bi-lightning-charge-fill"></i></div>
          <h5 class="font-serif fw-bold text-ink">2. Speed Floors & Speed Coins</h5>
          <p class="text-ink-muted small">
            Solve accurately within estimated target times to earn Speed Coins and advance leagues. Overconfidence without accuracy loses XP, so calibrate your confidence!
          </p>
          <div class="d-flex justify-content-center gap-2 mt-2">
            <button type="button" class="btn btn-outline-gamified fw-bold" onclick="showTourStep(1)">Back</button>
            <button type="button" class="btn btn-primary-gamified fw-bold px-4" onclick="showTourStep(3)">
              Next: 1v1 Duels <i class="bi bi-arrow-right"></i>
            </button>
          </div>
        </div>
        <!-- Step 3 -->
        <div id="tourStep3" class="tour-step d-none">
          <div class="display-5 mb-2 text-success"><i class="bi bi-trophy-fill"></i></div>
          <h5 class="font-serif fw-bold text-ink">3. Duels, Store & Community</h5>
          <p class="text-ink-muted small">
            Challenge fellow students in 1v1 Duels, redeem streak shields in the Speed Coins store, and submit campus questions to earn bonus coins!
          </p>
          <button type="button" class="btn btn-success fw-bold px-4 py-2 mt-2" onclick="finishTour()">
            <i class="bi bi-check-circle-fill me-1"></i> Get Started
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function showTourStep(step) {
  document.querySelectorAll('.tour-step').forEach(el => el.classList.add('d-none'));
  const target = document.getElementById('tourStep' + step);
  if (target) target.classList.remove('d-none');
}

function finishTour() {
  localStorage.setItem('apttitudee_tour_completed', 'true');
  const modalEl = document.getElementById('onboardingTourModal');
  if (modalEl && typeof bootstrap !== 'undefined') {
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  if (!localStorage.getItem('apttitudee_tour_completed')) {
    setTimeout(() => {
      const modalEl = document.getElementById('onboardingTourModal');
      if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    }, 1000);
  }
});
</script>

<!-- Submit Placement Question Modal (moved from quiz session) -->
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
                <option value="hard">Hard</option>
              </select>
            </div>
          </div>
          <div class="row g-2 mb-3">
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
