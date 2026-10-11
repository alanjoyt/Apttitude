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
        <a href="index.php?action=diagnostic" class="btn btn-primary-gamified btn-sm fw-bold px-3">
          Start Diagnostic
        </a>
      </div>
    </div>
  <?php endif; ?>
  <!-- Top User Profile & Level Progress Banner -->
  <div class="col-12">
    <div class="card card-gamified hero-gamified-banner p-4">
      <div class="row align-items-center g-3">
        <div class="col-lg-7">
          <div class="d-flex align-items-center gap-3 mb-2">
            <h3 class="font-serif fw-bold text-ink mb-0">Welcome back, <?= htmlspecialchars($user['name']) ?>!</h3>
            <span class="stamp-badge py-1 px-3 fw-bold d-inline-flex align-items-center gap-1">
              <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-crown"></use></svg> <?= $user['tier'] ?> League
            </span>
          </div>
          <p class="text-ink-muted small mb-3">Your placement readiness is active. Complete today's practice session to protect your streak!</p>

          <!-- 7-Day Weekly Streak Activity Calendar -->
          <div class="streak-calendar-card p-3 rounded-4 border bg-surface mb-3" style="border-color: var(--border) !important;">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="small fw-bold text-ink"><i class="bi bi-fire text-danger animate-pulse me-1"></i> 7-Day Practice Streak Tracking</span>
              <span class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: var(--bg); color: var(--ink-muted); border: 1px solid var(--border);">
                <svg class="exam-icon" style="width:13px;height:13px;"><use href="#icon-shield"></use></svg> <?= $user['streak_freezes'] ?? 1 ?> Streak Freezes Active
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
                  <span class="mt-1 d-inline-flex align-items-center justify-content-center"><?= $isActive ? '<svg class="exam-icon text-danger" style="width:14px;height:14px;"><use href="#icon-flame"></use></svg>' : ($isToday ? '<svg class="exam-icon text-primary" style="width:14px;height:14px;"><use href="#icon-target"></use></svg>' : '&bull;') ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="col-lg-5 text-lg-end">
          <div class="d-flex flex-column align-items-lg-end gap-2">
            <div class="d-flex gap-2">
              <div class="stat-pill-gamified py-2 px-3 fs-6 d-inline-flex align-items-center gap-1"><svg class="exam-icon text-danger" style="width:14px;height:14px;"><use href="#icon-flame"></use></svg> <strong><?= $user['current_streak'] ?></strong>d Streak</div>
              <div class="stat-pill-gamified py-2 px-3 fs-6 d-inline-flex align-items-center gap-1"><svg class="exam-icon text-warning" style="width:14px;height:14px;"><use href="#icon-star"></use></svg> <strong><?= number_format($user['total_xp']) ?></strong> XP</div>
              <div class="stat-pill-gamified py-2 px-3 fs-6 d-inline-flex align-items-center gap-1"><svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-coin"></use></svg> <strong><?= number_format($user['coins']) ?></strong></div>
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
          <div class="progress-bar-fill" style="width: <?= $tierProgress['progress_percent'] ?>%;"></div>
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
            <svg class="exam-icon text-warning me-1" style="width:14px;height:14px;"><use href="#icon-alert-triangle"></use></svg> <?= htmlspecialchars($weak['question_text']) ?>
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
            <div class="fs-3 font-serif fw-bold d-flex align-items-center justify-content-center gap-1" style="color: var(--danger);"><svg class="exam-icon" style="width:24px;height:24px;"><use href="#icon-brain"></use></svg> <?= $conceptGap ?></div>
            <div class="text-ink-muted small">Concept Gaps</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold d-flex align-items-center justify-content-center gap-1" style="color: var(--highlight);"><svg class="exam-icon" style="width:24px;height:24px;"><use href="#icon-calculator"></use></svg> <?= $calcErr ?></div>
            <div class="text-ink-muted small">Calculation Errors</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold d-flex align-items-center justify-content-center gap-1" style="color: var(--accent);"><svg class="exam-icon" style="width:24px;height:24px;"><use href="#icon-eye"></use></svg> <?= $misread ?></div>
            <div class="text-ink-muted small">Misread Questions</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold text-ink-muted d-flex align-items-center justify-content-center gap-1"><svg class="exam-icon" style="width:24px;height:24px;"><use href="#icon-clock"></use></svg> <?= $timeOut ?></div>
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
