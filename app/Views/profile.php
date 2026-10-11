<?php
require_once __DIR__ . '/../Services/ReferralService.php';
require_once __DIR__ . '/../Services/ProgressService.php';

$userId = $_SESSION['user_id'] ?? null;

$user = User::findById($userId);
$mascot = GamificationEngine::getMascotForTier($user['tier']);
$refStats = ReferralService::getReferralStats($userId);
$progressData = ProgressService::getProgressReport($userId, 30);

$avatarIcon = !empty($user['avatar_icon']) ? $user['avatar_icon'] : 'bi-person-circle';
$bio = !empty($user['bio']) ? $user['bio'] : 'Apttitudee Scholar';
?>

<div class="row g-3 g-md-4 justify-content-center py-2 py-md-3">
  
  <!-- Profile Summary Header Card -->
  <div class="col-12">
    <div class="card card-gamified p-3 p-md-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3 gap-md-4 flex-wrap flex-sm-nowrap min-w-0" style="flex: 1 1 240px;">
          <div class="p-3 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 72px; height: 72px; background-color: var(--surface); border: 1.5px solid var(--border); color: var(--accent);">
            <i class="bi <?= $avatarIcon ?> fs-2"></i>
          </div>
          <div class="min-w-0" style="overflow-wrap: anywhere;">
            <h1 class="font-serif fw-bold text-ink mb-1 fs-3"><?= htmlspecialchars($user['name']) ?> &bull; Student Profile</h1>
            <p class="text-ink-muted small mb-2"><i class="bi bi-quote me-1"></i> <?= htmlspecialchars($bio) ?></p>
            <div class="d-flex gap-2 flex-wrap">
              <span class="stamp-badge py-1 px-3 fw-bold d-inline-flex align-items-center gap-1">
                <svg class="exam-icon" style="width:14px;height:14px;"><use href="#<?= $mascot['icon'] ?? 'icon-shield' ?>"></use></svg> <?= $user['tier'] ?> Tier (<?= $mascot['name'] ?>)
              </span>
              <span class="badge rounded-pill px-3 py-1 font-weight-bold d-inline-flex align-items-center gap-1" style="background-color: var(--surface); color: var(--accent); border: 1px solid var(--border);">
                <svg class="exam-icon" style="width:13px;height:13px;"><use href="#icon-coin"></use></svg> <?= number_format($user['coins']) ?> Speed Coins
              </span>
            </div>
          </div>
        </div>

        <div>
          <button class="btn btn-outline-gamified px-3 px-sm-4 fw-bold" data-bs-toggle="modal" data-bs-target="#editProfileModal" style="min-height: 44px;">
            <i class="bi bi-pencil-square me-1"></i> Edit Profile & Avatar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================
       PART B: PROFILE PROGRESS REPORT SECTION (Tabs on mobile: Overview, Topics, Mocks, Time)
       ======================================================================== -->
  <div class="col-12">
    <section class="card card-gamified p-3 p-md-4" aria-labelledby="progressReportHeading">
      
      <!-- Progress Report Header Bar -->
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 pb-2 border-bottom" style="border-color: var(--surface-border) !important;">
        <div>
          <span class="badge-stamp-sm mb-1 d-inline-block">ACADEMIC AUDIT</span>
          <h2 class="font-serif fw-bold text-ink fs-4 mb-0" id="progressReportHeading">Your Progress Report</h2>
        </div>
        
        <!-- Range Filter Segmented Control (7d / 30d / 90d / All) -->
        <div class="range-filter-control" role="group" aria-label="Select performance date range">
          <button type="button" class="range-filter-btn" data-days="7" onclick="switchProgressRange(7, this)">7D</button>
          <button type="button" class="range-filter-btn active" data-days="30" onclick="switchProgressRange(30, this)">30D</button>
          <button type="button" class="range-filter-btn" data-days="90" onclick="switchProgressRange(90, this)">90D</button>
          <button type="button" class="range-filter-btn" data-days="0" onclick="switchProgressRange(0, this)">ALL</button>
        </div>
      </div>

      <!-- Segmented Tab Navigation for Mobile & Desktop -->
      <nav class="progress-segmented-nav mb-4" role="tablist" aria-label="Progress report sections">
        <button class="progress-nav-btn active" id="tabBtn-overview" role="tab" aria-selected="true" aria-controls="pane-overview" onclick="showProgressTab('overview')">
          <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-grid"></use></svg> Overview
        </button>
        <button class="progress-nav-btn" id="tabBtn-topics" role="tab" aria-selected="false" aria-controls="pane-topics" onclick="showProgressTab('topics')">
          <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-award"></use></svg> Topics
        </button>
        <button class="progress-nav-btn" id="tabBtn-mocks" role="tab" aria-selected="false" aria-controls="pane-mocks" onclick="showProgressTab('mocks')">
          <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-shield"></use></svg> Mocks (<?= $progressData['mocks']['count'] ?? 0 ?>)
        </button>
        <button class="progress-nav-btn" id="tabBtn-time" role="tab" aria-selected="false" aria-controls="pane-time" onclick="showProgressTab('time')">
          <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-clock"></use></svg> Time
        </button>
      </nav>

      <!-- TAB 1: OVERVIEW PANE -->
      <div id="pane-overview" class="progress-tab-pane" role="tabpanel" aria-labelledby="tabBtn-overview">
        <!-- Overview Stats Grid (<dl>) -->
        <dl class="mock-stats-dl mb-4">
          <div class="mock-stat-box">
            <dt>Questions Attempted</dt>
            <dd class="text-ink" id="ovAttempts"><?= number_format($progressData['overview']['range_attempts'] ?? 0) ?></dd>
          </div>
          <div class="mock-stat-box">
            <dt>Overall Accuracy</dt>
            <dd class="text-accent" id="ovAccuracy"><?= $progressData['overview']['range_accuracy'] ?? 0 ?>%</dd>
          </div>
          <div class="mock-stat-box">
            <dt>Current Streak</dt>
            <dd class="text-success" id="ovStreak"><?= $progressData['overview']['current_streak'] ?? 0 ?> Days</dd>
          </div>
          <div class="mock-stat-box">
            <dt>Days Practiced (Mo.)</dt>
            <dd class="text-highlight" id="ovDaysMonth"><?= $progressData['overview']['active_days_this_month'] ?? 0 ?> Days</dd>
          </div>
        </dl>

        <!-- Accuracy Trend (Inline SVG Chart) -->
        <div class="card card-gamified p-3 p-md-4 mb-4" style="background-color: var(--surface-bg);">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="font-serif fw-bold text-ink fs-5 mb-0">Accuracy Trend Over Time</h3>
            <span class="small font-mono text-ink-muted">Daily performance curve</span>
          </div>

          <?php if (!empty($progressData['trend']['has_enough_data'])): ?>
            <?php
              $points = $progressData['trend']['points'];
              $numPoints = count($points);
              $svgWidth = 500;
              $svgHeight = 160;
              $paddingX = 35;
              $paddingY = 25;
              $chartW = $svgWidth - ($paddingX * 2);
              $chartH = $svgHeight - ($paddingY * 2);

              $coordList = [];
              foreach ($points as $idx => $pt) {
                  $x = $paddingX + ($numPoints > 1 ? ($idx / ($numPoints - 1)) * $chartW : $chartW / 2);
                  $y = ($svgHeight - $paddingY) - (($pt['accuracy'] / 100) * $chartH);
                  $coordList[] = ['x' => round($x, 1), 'y' => round($y, 1), 'pt' => $pt];
              }

              $polylinePoints = implode(' ', array_map(fn($c) => "{$c['x']},{$c['y']}", $coordList));
            ?>
            <div class="accuracy-trend-svg-wrap">
              <svg viewBox="0 0 <?= $svgWidth ?> <?= $svgHeight ?>" class="accuracy-trend-svg" role="img" aria-label="Daily accuracy trend chart">
                <!-- Background Gridlines -->
                <line x1="<?= $paddingX ?>" y1="<?= $paddingY ?>" x2="<?= $svgWidth - $paddingX ?>" y2="<?= $paddingY ?>" stroke="var(--surface-border)" stroke-dasharray="3,3" />
                <line x1="<?= $paddingX ?>" y1="<?= $paddingY + ($chartH / 2) ?>" x2="<?= $svgWidth - $paddingX ?>" y2="<?= $paddingY + ($chartH / 2) ?>" stroke="var(--surface-border)" stroke-dasharray="3,3" />
                <line x1="<?= $paddingX ?>" y1="<?= $svgHeight - $paddingY ?>" x2="<?= $svgWidth - $paddingX ?>" y2="<?= $svgHeight - $paddingY ?>" stroke="var(--surface-border)" />

                <!-- Y-Axis Labels -->
                <text x="5" y="<?= $paddingY + 4 ?>" font-size="10" font-family="var(--font-mono)" fill="var(--ink-muted)">100%</text>
                <text x="12" y="<?= $paddingY + ($chartH / 2) + 4 ?>" font-size="10" font-family="var(--font-mono)" fill="var(--ink-muted)">50%</text>
                <text x="18" y="<?= $svgHeight - $paddingY + 4 ?>" font-size="10" font-family="var(--font-mono)" fill="var(--ink-muted)">0%</text>

                <!-- Trend Line -->
                <polyline fill="none" stroke="var(--accent-bg)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="<?= $polylinePoints ?>" />

                <!-- Data Points & Labels -->
                <?php foreach ($coordList as $c): ?>
                  <circle cx="<?= $c['x'] ?>" cy="<?= $c['y'] ?>" r="4.5" fill="var(--surface-bg)" stroke="var(--accent-bg)" stroke-width="2.5" />
                  <text x="<?= $c['x'] ?>" y="<?= $svgHeight - 8 ?>" font-size="9" font-family="var(--font-mono)" text-anchor="middle" fill="var(--ink-muted)"><?= $c['pt']['label'] ?></text>
                <?php endforeach; ?>
              </svg>
            </div>

            <!-- Accessible Text Alternative -->
            <table class="visually-hidden" aria-label="Historical accuracy data table">
              <thead><tr><th>Date</th><th>Attempts</th><th>Correct</th><th>Accuracy</th></tr></thead>
              <tbody>
                <?php foreach ($points as $pt): ?>
                  <tr><td><?= $pt['date'] ?></td><td><?= $pt['attempts'] ?></td><td><?= $pt['correct'] ?></td><td><?= $pt['accuracy'] ?>%</td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>

          <?php else: ?>
            <!-- Friendly Empty State for Insufficient Data -->
            <div class="text-center py-4 px-3" style="background-color: var(--surface-muted-bg); border-radius: var(--radius-sm);">
              <svg class="exam-icon text-ink-muted mb-2" style="width:32px;height:32px;"><use href="#icon-clock"></use></svg>
              <h4 class="font-serif fw-bold text-ink fs-6 mb-1">Answer 10 more questions to see your trend</h4>
              <p class="text-ink-muted small mb-3">As you complete practice questions, your adaptive accuracy curve will graph here automatically.</p>
              <a href="index.php?action=practice" class="btn btn-primary-gamified btn-sm px-4 fw-bold">Start Practicing Now</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- 7-Day & 30-Day Activity Calendar Strip -->
        <div class="card card-gamified p-3 p-md-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="font-serif fw-bold text-ink fs-5 mb-0">Study Activity Calendar</h3>
            <span class="small font-mono text-ink-muted">Past 30 Days</span>
          </div>
          <p class="small text-ink-muted mb-3">Green marks days where you practiced questions or attempted a mock.</p>

          <div class="activity-cal-grid activity-cal-grid-month">
            <?php
              $activeDatesSet = array_flip($progressData['calendar']['active_dates'] ?? []);
              for ($i = 29; $i >= 0; $i--):
                  $dStr = date('Y-m-d', strtotime("-$i days"));
                  $dLabel = date('j', strtotime("-$i days"));
                  $isActive = isset($activeDatesSet[$dStr]);
            ?>
              <div class="cal-day-cell <?= $isActive ? 'active-day' : '' ?>" title="<?= $dStr ?>: <?= $isActive ? 'Practiced' : 'No activity' ?>">
                <?= $dLabel ?>
              </div>
            <?php endfor; ?>
          </div>
        </div>
      </div>

      <!-- TAB 2: TOPICS PANE (Topic Strength Map sorted weakest first) -->
      <div id="pane-topics" class="progress-tab-pane d-none" role="tabpanel" aria-labelledby="tabBtn-topics">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="font-serif fw-bold text-ink fs-5 mb-0">Topic Mastery & Strength Map</h3>
          <span class="small font-mono text-ink-muted">Sorted Weakest First</span>
        </div>
        <p class="small text-ink-muted mb-3">Topics with the lowest accuracy or highest doubt are surfaced at the top for focused revision.</p>

        <div id="topicsContainer">
          <?php 
            $weakestCount = 0;
            foreach ($progressData['topics'] as $topic): 
              $isWeak = ($topic['attempts'] > 0 && ($topic['accuracy'] ?? 0) < 70 && $weakestCount < 3);
              if ($isWeak) $weakestCount++;
          ?>
            <div class="topic-strength-card">
              <div class="d-flex align-items-center gap-3">
                <div class="d-flex flex-column align-items-start">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <strong class="text-ink font-serif fs-6"><?= htmlspecialchars($topic['name']) ?></strong>
                    <span class="badge-stamp-sm py-0.5 px-2 fs-8"><?= htmlspecialchars($topic['code']) ?></span>
                    <?php if ($topic['is_low_sample']): ?>
                      <span class="small text-ink-muted fs-8 font-mono">(<?= $topic['attempts'] ?> attempts &bull; Low Sample)</span>
                    <?php endif; ?>
                  </div>
                  
                  <div class="d-flex align-items-center gap-3 font-mono small text-ink-muted">
                    <span>Accuracy: <strong class="<?= ($topic['accuracy'] ?? 0) >= 60 ? 'text-success' : 'text-danger' ?>"><?= $topic['accuracy'] !== null ? $topic['accuracy'] . '%' : 'Unattempted' ?></strong></span>
                    <span>&bull;</span>
                    <span>Avg Time: <?= $topic['avg_time'] ? $topic['avg_time'] . 's' : 'N/A' ?></span>
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                <!-- OMR Mastery Bubbles (1 to 5) -->
                <div class="d-flex flex-column align-items-sm-end">
                  <span class="small text-ink-muted mb-1 fs-8">Mastery Level <?= $topic['mastery'] ?>/5</span>
                  <div class="omr-bubble-row" aria-label="Mastery Level <?= $topic['mastery'] ?> of 5">
                    <?php for ($b = 1; $b <= 5; $b++): ?>
                      <span class="omr-bubble <?= ($b <= $topic['mastery']) ? 'filled' : '' ?>"></span>
                    <?php endfor; ?>
                  </div>
                </div>

                <?php if ($isWeak || $topic['attempts'] === 0): ?>
                  <a href="index.php?action=practice&subtopic_id=<?= $topic['default_subtopic_id'] ?>&session=1" class="btn btn-outline-gamified btn-sm fw-bold px-3 text-nowrap" style="min-height: 40px;">
                    Practice This &rarr;
                  </a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- TAB 3: MOCKS PANE (Mock Examination History) -->
      <div id="pane-mocks" class="progress-tab-pane d-none" role="tabpanel" aria-labelledby="tabBtn-mocks">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="font-serif fw-bold text-ink fs-5 mb-0">Past Mock Examination Scorecards</h3>
          <?php if (!empty($progressData['mocks']['best_score'])): ?>
            <span class="badge rounded-pill px-3 py-1 font-mono fw-bold" style="background-color: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border);">
              Best Score: <?= $progressData['mocks']['best_score'] ?> Marks
            </span>
          <?php endif; ?>
        </div>

        <?php if (!empty($progressData['mocks']['list'])): ?>
          <div class="d-flex flex-column gap-2 mb-3">
            <?php foreach ($progressData['mocks']['list'] as $mock): ?>
              <div class="mock-history-item d-flex justify-content-between align-items-center flex-wrap gap-2" onclick="openPastMockScorecard('<?= htmlspecialchars($mock['session_key'], ENT_QUOTES) ?>')">
                <div class="d-flex align-items-center gap-3">
                  <div class="p-2 rounded-circle" style="background-color: var(--surface-muted-bg); border: 1px solid var(--surface-border);">
                    <svg class="exam-icon text-accent" style="width:20px;height:20px;"><use href="#icon-shield"></use></svg>
                  </div>
                  <div>
                    <strong class="font-serif text-ink d-block"><?= htmlspecialchars($mock['title']) ?></strong>
                    <span class="small font-mono text-ink-muted"><?= $mock['date'] ?> &bull; <?= $mock['has_time'] ? floor($mock['total_time_seconds']/60) . 'm ' . ($mock['total_time_seconds']%60) . 's' : 'Time not recorded' ?></span>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                  <div class="text-end">
                    <span class="font-serif fw-bold fs-5 text-ink d-block"><?= $mock['score_marks'] ?> Marks</span>
                    <span class="small font-mono text-ink-muted"><?= $mock['accuracy'] ?>% Acc &bull; <?= $mock['correct_count'] ?>C / <?= $mock['wrong_count'] ?>W</span>
                  </div>
                  <span class="badge rounded-pill px-2.5 py-1 fw-bold fs-8" style="background-color: var(--surface-muted-bg); color: var(--surface-muted-text); border: 1px solid var(--surface-border);">
                    <?= $mock['band'] ?>
                  </span>
                  <span class="btn btn-outline-gamified btn-sm py-1 px-2">&rarr;</span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-4 px-3" style="background-color: var(--surface-muted-bg); border-radius: var(--radius-sm);">
            <svg class="exam-icon text-ink-muted mb-2" style="width:36px;height:36px;"><use href="#icon-award"></use></svg>
            <h4 class="font-serif fw-bold text-ink fs-6 mb-1">No Mock Examinations Attempted Yet</h4>
            <p class="text-ink-muted small mb-3">Test your exam hall stamina with authentic corporate placement patterns.</p>
            <a href="index.php?action=mock" class="btn btn-primary-gamified btn-sm px-4 fw-bold">Take Placement Mock Test</a>
          </div>
        <?php endif; ?>
      </div>

      <!-- TAB 4: TIME PANE (Pacing & Speed Analysis) -->
      <div id="pane-time" class="progress-tab-pane d-none" role="tabpanel" aria-labelledby="tabBtn-time">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="font-serif fw-bold text-ink fs-5 mb-0">Pacing & Solving Speed Analysis</h3>
          <span class="badge-stamp-sm py-0.5 px-2"><?= htmlspecialchars($progressData['time_analysis']['speed_trend_label'] ?? 'Pacing Baseline') ?></span>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-sm-6">
            <div class="p-3 rounded border h-100" style="background-color: var(--surface-muted-bg); border-color: var(--surface-border) !important;">
              <div class="small text-ink-muted mb-1">Current 14-Day Average Speed</div>
              <div class="font-serif fw-bold fs-3 text-ink">
                <?= $progressData['time_analysis']['current_avg_seconds'] ? $progressData['time_analysis']['current_avg_seconds'] . 's' : 'Pending Data' ?>
              </div>
              <p class="small text-ink-muted mb-0 mt-2">Target benchmark for campus tests: <strong>45s to 60s</strong> per question.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-3 rounded border h-100" style="background-color: var(--surface-muted-bg); border-color: var(--surface-border) !important;">
              <div class="small text-ink-muted mb-1">Prior Period Comparison</div>
              <div class="font-serif fw-bold fs-3 text-accent">
                <?= $progressData['time_analysis']['speed_trend_label'] ?>
              </div>
              <p class="small text-ink-muted mb-0 mt-2">Calculated from genuine question active time via Page Visibility API.</p>
            </div>
          </div>
        </div>

        <h4 class="font-serif fw-bold text-ink fs-6 mb-2">Topic Pacing Breakdown</h4>
        <div class="table-responsive">
          <table class="mock-section-table" aria-label="Topic Pacing Breakdown">
            <thead>
              <tr>
                <th>Topic</th>
                <th>Attempts</th>
                <th>Avg Seconds</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($progressData['topics'] as $t): ?>
                <tr>
                  <td class="fw-bold"><?= htmlspecialchars($t['name']) ?></td>
                  <td class="font-mono"><?= $t['attempts'] ?></td>
                  <td class="font-mono fw-bold"><?= $t['avg_time'] ? $t['avg_time'] . 's' : 'N/A' ?></td>
                  <td>
                    <?php if ($t['avg_time'] === null): ?>
                      <span class="text-ink-muted small">No timing</span>
                    <?php elseif ($t['avg_time'] <= 45): ?>
                      <span class="badge" style="background-color: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border);">Fast</span>
                    <?php elseif ($t['avg_time'] <= 75): ?>
                      <span class="badge" style="background-color: var(--surface-muted-bg); color: var(--ink); border: 1px solid var(--surface-border);">Balanced</span>
                    <?php else: ?>
                      <span class="badge" style="background-color: var(--danger-bg); color: var(--danger-text); border: 1px solid var(--danger-border);">Pacing Risk</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </section>
  </div>

  <!-- Referral Program Card (+25 Speed Coins) -->
  <div class="col-md-6">
    <div class="card card-gamified p-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-gift-fill fs-3" style="color: var(--success);"></i> Referral Rewards (+25 Coins)
        </h4>
        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background-color: var(--success-bg); color: var(--success-text); border: 1.5px solid var(--success-border);">
          25 Coins / Referral
        </span>
      </div>

      <p class="text-ink-muted small mb-4">
        Share your unique referral code with friends! When they register, **both you and your friend earn +25 Speed Coins** instantly.
      </p>

      <div class="p-3 rounded-4 border mb-4" style="background-color: var(--bg); border-color: var(--border) !important;">
        <label class="small fw-bold d-block mb-1 text-ink-muted">Your Unique Referral Code</label>
        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border" style="background-color: var(--surface); border-color: var(--border) !important;">
          <code class="fs-4 font-serif fw-bold tracking-wider" style="color: var(--accent);" id="refCodeText"><?= $refStats['referral_code'] ?></code>
          <button onclick="copyReferralLink()" class="btn btn-primary-gamified btn-sm px-3 fw-bold">
            <i class="bi bi-clipboard-check me-1"></i> Copy Link
          </button>
        </div>
        <small class="text-ink-muted d-block mt-2 fs-8" id="refLinkUrl"><?= $refStats['referral_link'] ?></small>
      </div>

      <div class="row g-2 text-center mb-3">
        <div class="col-6">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold" style="color: var(--success);"><?= $refStats['total_invited'] ?></div>
            <div class="text-ink-muted small">Friends Invited</div>
          </div>
        </div>
        <div class="col-6">
          <div class="p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
            <div class="fs-3 font-serif fw-bold d-flex align-items-center justify-content-center gap-1" style="color: var(--highlight);"><svg class="exam-icon" style="width:20px;height:20px;"><use href="#icon-coin"></use></svg> <?= number_format($refStats['coins_earned']) ?></div>
            <div class="text-ink-muted small">Coins Earned</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Account Security & Device Locking -->
  <div class="col-md-6">
    <div class="card card-gamified p-4 h-100">
      <h4 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-shield-lock-fill fs-3" style="color: var(--accent);"></i> Device Lock Security
      </h4>

      <div class="exam-notice-banner p-3 mb-4" style="background-color: var(--notice-bg); color: var(--notice-text); border: 1.5px solid var(--notice-border);">
        <div class="d-flex align-items-center gap-2 fw-bold mb-1">
          <i class="bi bi-shield-check fs-5" style="color: var(--success);"></i>
          <span>Single Session Device Lock Active</span>
        </div>
        <p class="small mb-0 text-ink-muted">
          Your account is secured with single-device locking. If an attempt is made to log in on another browser or device, your current session will automatically lock out for protection.
        </p>
      </div>

      <!-- Password Modification Form -->
      <h6 class="font-serif fw-bold text-ink mb-3"><i class="bi bi-key-fill me-1" style="color: var(--highlight);"></i> Change Password</h6>
      <form id="changePasswordForm">
        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">Current Password</label>
          <input type="password" id="oldPassword" class="form-control" placeholder="••••••••" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">New Password</label>
          <input type="password" id="newPassword" class="form-control" placeholder="••••••••" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">Confirm New Password</label>
          <input type="password" id="confirmPassword" class="form-control" placeholder="••••••••" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
        <button type="button" onclick="submitChangePassword()" class="btn btn-primary-gamified px-4 fw-bold w-100 py-2">
          Update Password <i class="bi bi-check-circle-fill ms-1"></i>
        </button>
      </form>

    </div>
  </div>

</div>

<!-- Modal: Edit Profile & Avatar Picker -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border-color: var(--border) !important;">
      <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
        <h5 class="modal-title font-serif fw-bold"><i class="bi bi-pencil-square me-2" style="color: var(--accent);"></i> Edit Profile & Choose Avatar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        
        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">Full Name</label>
          <input type="text" id="editNameInput" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">Short Bio / Status</label>
          <input type="text" id="editBioInput" class="form-control" value="<?= htmlspecialchars($bio) ?>" style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold d-block text-ink-muted">Select Profile Avatar Emblem</label>
          <div class="d-flex flex-wrap gap-3 justify-content-center py-2">
            <?php 
              $avatars = ['bi-person-circle', 'bi-robot', 'bi-lightning-charge-fill', 'bi-controller', 'bi-rocket-takeoff-fill', 'bi-shield-shaded', 'bi-gem', 'bi-fire', 'bi-award-fill', 'bi-cpu-fill'];
              foreach ($avatars as $av):
            ?>
              <div class="avatar-pick-option p-3 rounded-circle border <?= $avatarIcon === $av ? 'border-primary bg-primary-subtle text-primary' : 'border bg-body-tertiary text-secondary' ?>" style="cursor: pointer; font-size: 1.8rem;" onclick="selectAvatarIcon('<?= $av ?>', this)">
                <i class="bi <?= $av ?>"></i>
              </div>
            <?php endforeach; ?>
          </div>
          <input type="hidden" id="selectedAvatarIcon" value="<?= $avatarIcon ?>">
        </div>

      </div>
      <div class="modal-footer border-top" style="border-color: var(--border) !important;">
        <button type="button" class="btn btn-outline-gamified btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary-gamified btn-sm fw-bold" onclick="submitUpdateProfile()">Save Changes</button>
      </div>
    </div>
  </div>
</div>

<script>
function showProgressTab(tabName) {
    document.querySelectorAll('.progress-nav-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
    });
    document.querySelectorAll('.progress-tab-pane').forEach(pane => {
        pane.classList.add('d-none');
    });

    const targetBtn = document.getElementById(`tabBtn-${tabName}`);
    const targetPane = document.getElementById(`pane-${tabName}`);
    if (targetBtn && targetPane) {
        targetBtn.classList.add('active');
        targetBtn.setAttribute('aria-selected', 'true');
        targetPane.classList.remove('d-none');
    }
}

function switchProgressRange(days, el) {
    document.querySelectorAll('.range-filter-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');

    fetch(`index.php?action=api_profile_progress&days=${days}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.data && res.data.overview) {
                const ov = res.data.overview;
                document.getElementById('ovAttempts').innerText = Number(ov.range_attempts).toLocaleString();
                document.getElementById('ovAccuracy').innerText = `${ov.range_accuracy}%`;
            }
        })
        .catch(err => console.error('Failed to update range:', err));
}

function openPastMockScorecard(sessionKey) {
    // Navigate to mock with past session key or alert review
    window.location.href = `index.php?action=mock&session=${encodeURIComponent(sessionKey)}&view=scorecard`;
}

function selectAvatarIcon(iconClass, el) {
    document.getElementById('selectedAvatarIcon').value = iconClass;
    document.querySelectorAll('.avatar-pick-option').forEach(opt => {
        opt.classList.remove('border-primary', 'bg-primary-subtle', 'text-primary');
        opt.classList.add('border', 'bg-body-tertiary', 'text-secondary');
    });
    el.classList.remove('border', 'bg-body-tertiary', 'text-secondary');
    el.classList.add('border-primary', 'bg-primary-subtle', 'text-primary');
}

function submitUpdateProfile() {
    const name = document.getElementById('editNameInput').value;
    const bio = document.getElementById('editBioInput').value;
    const avatar = document.getElementById('selectedAvatarIcon').value;

    fetch('index.php?action=api_update_profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: name, bio: bio, avatar_icon: avatar })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') window.location.reload();
    });
}

function submitChangePassword() {
    const oldPass = document.getElementById('oldPassword').value;
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;

    fetch('index.php?action=api_change_password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ old_password: oldPass, new_password: newPass, confirm_password: confirmPass })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') {
            document.getElementById('changePasswordForm').reset();
        }
    });
}

function copyReferralLink() {
    const url = document.getElementById('refLinkUrl').innerText;
    navigator.clipboard.writeText(url).then(() => {
        alert('Referral link copied to clipboard! Share it with friends to earn +25 Speed Coins per referral.');
    });
}
</script>
