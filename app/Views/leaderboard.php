<?php
/**
 * Leaderboards View: Global Leagues, College vs College, and Campus Students
 */
$tab = $tab ?? 'global';
?>
<style>
.promotion-zone-badge {
  background-color: var(--success-bg, #EDF7F0) !important;
  border: 1px solid var(--success-border, #1A6338) !important;
  color: var(--success-text, #1A6338) !important;
}
.demotion-zone-badge {
  background-color: var(--danger-bg, #FDF0EE) !important;
  border: 1px solid var(--danger-border, #A82218) !important;
  color: var(--danger-text, #A82218) !important;
}
</style>
<div class="row justify-content-center py-3">
  <div class="col-lg-8 col-md-10">

    <!-- Top Level Leaderboard Navigation -->
    <div class="card card-gamified p-3 mb-4 text-center">
      <div class="d-flex justify-content-center gap-2 flex-wrap mb-2">
        <a href="index.php?action=leaderboard&tab=global" 
           class="btn btn-sm px-3 <?= $tab === 'global' ? 'btn-primary-gamified fw-bold' : 'btn-outline-gamified' ?>"
           id="tabGlobalBtn">
          <i class="bi bi-trophy-fill me-1"></i> Global Leagues
        </a>
        <a href="index.php?action=leaderboard&tab=colleges" 
           class="btn btn-sm px-3 <?= $tab === 'colleges' ? 'btn-primary-gamified fw-bold' : 'btn-outline-gamified' ?>"
           id="tabCollegesBtn">
          <i class="bi bi-building me-1"></i> College vs College
        </a>
        <a href="index.php?action=leaderboard&tab=campus" 
           class="btn btn-sm px-3 <?= $tab === 'campus' ? 'btn-primary-gamified fw-bold' : 'btn-outline-gamified' ?>"
           id="tabCampusBtn">
          <i class="bi bi-mortarboard-fill me-1"></i> Campus Students
        </a>
      </div>
      <p class="text-ink-muted extra-small mb-0">
        <?php if ($tab === 'global'): ?>
          Compete across weekly leagues and advance from Bronze to Diamond.
        <?php elseif ($tab === 'colleges'): ?>
          Campus rivalry! Colleges rank by weekly student XP and average mock test scores (&ge;5 active members).
        <?php else: ?>
          Rankings within your college community. Display names only; anonymous mode respected.
        <?php endif; ?>
      </p>
    </div>

    <?php if ($tab === 'global'): ?>
      <!-- ====================================================================
           GLOBAL TIERS LEADERBOARD
           ==================================================================== -->
      <div class="card card-gamified p-4 mb-4 text-center">
        <h1 class="font-serif fw-bold text-ink mb-2 d-flex align-items-center justify-content-center gap-2 fs-3">
          <i class="bi bi-trophy-fill fs-2" style="color: var(--highlight);"></i> Global Tier League
        </h1>
        <p class="text-ink-muted small mb-1">Rankings update in real-time with live rank change indicators</p>
        <div class="d-flex justify-content-center">
          <span class="stamp-badge py-1 px-3 mb-3">
            <i class="bi bi-broadcast me-1"></i> Live Ticking Active
          </span>
        </div>

        <!-- Tier Navigation Tabs -->
        <div class="d-flex justify-content-center gap-2 flex-wrap mb-2">
          <?php foreach (GamificationEngine::$TIERS as $tierName => $data): ?>
            <a href="index.php?action=leaderboard&tab=global&tier=<?= $tierName ?>" 
               class="btn btn-sm <?= ($tier === $tierName || (!$tier && $tierName === 'Gold')) ? 'btn-primary-gamified fw-bold' : 'btn-outline-gamified' ?>">
              <?= $data['badge'] ?> <?= $tierName ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- User Relative Rank Bar (if logged in) -->
      <?php if (!empty($userRank)): ?>
        <div class="alert border rounded-4 p-3 mb-4 d-flex justify-content-between align-items-center" style="background-color: var(--surface); border-color: var(--accent) !important; color: var(--ink);">
          <div>
            <span class="badge rounded-pill px-3 py-2 me-2" style="background-color: var(--accent); color: white;">Your Rank: #<?= $userRank['rank'] ?></span>
            <strong class="text-ink font-serif"><?= number_format((int)($userRank['period_xp'] ?? 0)) ?> XP</strong>
          </div>
          <div class="fw-bold small d-inline-flex align-items-center gap-1" style="color: var(--accent);">
            <svg class="exam-icon icon-14"><use href="#icon-zap"></use></svg> <?= htmlspecialchars($userRank['message'] ?? '') ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Top 3 Podium Visual (if rankings available) -->
      <?php if (count($rankings) >= 3): ?>
        <div class="row g-2 g-sm-3 text-center mb-4 align-items-end justify-content-center">
          <!-- 2nd Place -->
          <div class="col-4 min-w-0 px-1 px-sm-2">
            <div class="card card-gamified p-2 p-sm-3 h-100 d-flex flex-column justify-content-between">
              <div class="fs-2 mb-1"><svg class="exam-icon" style="width:30px;height:30px;color:#94a3b8;"><use href="#icon-medal"></use></svg></div>
              <h6 class="font-serif fw-bold text-ink mb-1 text-truncate w-100" style="font-size: clamp(0.8rem, 2.8vw, 1.05rem); line-height: 1.25;" title="<?= htmlspecialchars($rankings[1]['name']) ?>"><?= htmlspecialchars($rankings[1]['name']) ?></h6>
              <span class="badge rounded-pill text-truncate" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border); font-size: clamp(0.7rem, 2.2vw, 0.8rem);"><?= number_format($rankings[1]['period_xp']) ?> XP</span>
            </div>
          </div>
          <!-- 1st Place -->
          <div class="col-4 min-w-0 px-1 px-sm-2">
            <div class="card card-gamified p-2.5 p-sm-4 shadow-sm h-100 d-flex flex-column justify-content-between" style="transform: scale(1.04); background-color: var(--highlight-bg); border: 2px solid var(--highlight-border) !important;">
              <div class="fs-1 mb-1"><svg class="exam-icon" style="width:38px;height:38px;color:var(--highlight);"><use href="#icon-trophy"></use></svg></div>
              <h5 class="font-serif fw-bold mb-1 text-truncate w-100" style="color: var(--highlight-text); font-size: clamp(0.85rem, 3.2vw, 1.15rem); line-height: 1.25;" title="<?= htmlspecialchars($rankings[0]['name']) ?>"><?= htmlspecialchars($rankings[0]['name']) ?></h5>
              <span class="badge fw-bold rounded-pill text-truncate" style="background-color: var(--highlight-border); color: #14213D; font-size: clamp(0.72rem, 2.4vw, 0.85rem);"><?= number_format($rankings[0]['period_xp']) ?> XP</span>
            </div>
          </div>
          <!-- 3rd Place -->
          <div class="col-4 min-w-0 px-1 px-sm-2">
            <div class="card card-gamified p-2 p-sm-3 h-100 d-flex flex-column justify-content-between">
              <div class="fs-2 mb-1"><svg class="exam-icon" style="width:30px;height:30px;color:#b45309;"><use href="#icon-medal"></use></svg></div>
              <h6 class="font-serif fw-bold text-ink mb-1 text-truncate w-100" style="font-size: clamp(0.8rem, 2.8vw, 1.05rem); line-height: 1.25;" title="<?= htmlspecialchars($rankings[2]['name']) ?>"><?= htmlspecialchars($rankings[2]['name']) ?></h6>
              <span class="badge rounded-pill text-truncate" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border); font-size: clamp(0.7rem, 2.2vw, 0.8rem);"><?= number_format($rankings[2]['period_xp']) ?> XP</span>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Full Leaderboard Table with League Zones -->
      <div class="card card-gamified p-0 overflow-hidden mb-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center p-3 border-bottom bg-surface gap-2" style="border-color: var(--border) !important;">
          <div class="d-flex align-items-center gap-2">
            <span class="promotion-zone-badge d-inline-flex align-items-center gap-1"><svg class="exam-icon text-success icon-12"><use href="#icon-arrow-right"></use></svg> Ranks 1-3: League Promotion Zone</span>
          </div>
          <span class="text-ink-muted small">Weekly Reset: Sunday Midnight</span>
        </div>

        <!-- DESKTOP TABLE VIEW (>= 768px) -->
        <div class="table-responsive d-none d-md-block">
          <table class="table table-hover mb-0 align-middle">
            <thead>
              <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
                <th class="ps-4 font-serif text-ink" style="width: 85px;">Rank</th>
                <th class="font-serif text-ink">Player</th>
                <th class="font-serif text-ink">League Tier</th>
                <th class="font-serif text-ink text-center">Zone Status</th>
                <th class="pe-4 text-end font-serif text-ink">Period XP</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $totalCount = count($rankings);
                foreach ($rankings as $index => $row): 
                  $rankNum = (int)$row['period_rank'];
                  $isUser = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']);
                  $isPromoted = ($rankNum <= 3);
                  $isDemoted = ($totalCount > 5 && $rankNum > $totalCount - 2);
              ?>
                <tr style="<?= $isUser ? 'background-color: rgba(30, 58, 95, 0.08); font-weight: bold;' : '' ?>">
                  <td class="ps-4 text-ink">
                    <?php if ($rankNum == 1): ?> <svg class="exam-icon me-1" style="width:14px;height:14px;color:var(--highlight);"><use href="#icon-trophy"></use></svg> <strong>#1</strong>
                    <?php elseif ($rankNum == 2): ?> <svg class="exam-icon me-1" style="width:14px;height:14px;color:#94a3b8;"><use href="#icon-medal"></use></svg> <strong>#2</strong>
                    <?php elseif ($rankNum == 3): ?> <svg class="exam-icon me-1" style="width:14px;height:14px;color:#b45309;"><use href="#icon-medal"></use></svg> <strong>#3</strong>
                    <?php else: ?> <strong>#<?= $rankNum ?></strong>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="fw-semibold text-ink"><?= htmlspecialchars($row['name']) ?></span>
                    <?= $isUser ? '<span class="badge bg-accent text-white ms-1" style="font-size:0.65rem;">YOU</span>' : '' ?>
                  </td>
                  <td>
                    <span class="badge rounded-pill" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border);"><?= $row['tier'] ?></span>
                  </td>
                  <td class="text-center">
                    <?php if ($isPromoted): ?>
                      <span class="promotion-zone-badge">Promoted</span>
                    <?php elseif ($isDemoted): ?>
                      <span class="demotion-zone-badge">Danger Zone</span>
                    <?php else: ?>
                      <span class="text-ink-muted extra-small">Safe Tier</span>
                    <?php endif; ?>
                  </td>
                  <td class="pe-4 text-end fw-bold" style="color: var(--accent);">
                    <svg class="exam-icon text-warning me-0.5 icon-12"><use href="#icon-star"></use></svg> <?= number_format($row['period_xp']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- MOBILE COMPACT CARD LIST (< 768px) -->
        <div class="d-md-none p-2 p-sm-3 d-flex flex-column gap-2">
          <?php 
            $totalCount = count($rankings);
            foreach ($rankings as $index => $row): 
              $rankNum = (int)$row['period_rank'];
              $isUser = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']);
              $isPromoted = ($rankNum <= 3);
              $isDemoted = ($totalCount > 5 && $rankNum > $totalCount - 2);
          ?>
            <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between gap-2" style="background-color: <?= $isUser ? 'rgba(30, 58, 95, 0.08)' : 'var(--bg)' ?>; border-color: <?= $isUser ? 'var(--accent)' : 'var(--border)' ?> !important; min-height: 52px;">
              <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                <!-- Rank Badge -->
                <div class="flex-shrink-0 text-center" style="min-width: 36px;">
                  <?php if ($rankNum == 1): ?>
                    <span class="badge p-1 d-inline-flex align-items-center justify-content-center" style="background-color: var(--highlight-bg); border: 1.5px solid var(--highlight-border); color: #14213D; width: 32px; height: 32px; border-radius: 8px;"><svg class="exam-icon" style="width:16px;height:16px;color:var(--highlight);"><use href="#icon-trophy"></use></svg></span>
                  <?php elseif ($rankNum == 2): ?>
                    <span class="badge p-1 d-inline-flex align-items-center justify-content-center" style="background-color: var(--surface); border: 1.5px solid #94a3b8; color: #64748b; width: 32px; height: 32px; border-radius: 8px;"><svg class="exam-icon" style="width:16px;height:16px;color:#94a3b8;"><use href="#icon-medal"></use></svg></span>
                  <?php elseif ($rankNum == 3): ?>
                    <span class="badge p-1 d-inline-flex align-items-center justify-content-center" style="background-color: var(--surface); border: 1.5px solid #b45309; color: #b45309; width: 32px; height: 32px; border-radius: 8px;"><svg class="exam-icon" style="width:16px;height:16px;color:#b45309;"><use href="#icon-medal"></use></svg></span>
                  <?php else: ?>
                    <span class="fw-bold fs-7 text-ink-muted">#<?= $rankNum ?></span>
                  <?php endif; ?>
                </div>

                <!-- Player Details -->
                <div class="min-w-0 flex-grow-1">
                  <div class="d-flex align-items-center gap-1.5 mb-0.5">
                    <span class="fw-bold text-ink text-truncate d-inline-block" style="font-size: 0.92rem; max-width: 140px;"><?= htmlspecialchars($row['name']) ?></span>
                    <?php if ($isUser): ?>
                      <span class="badge bg-accent text-white px-1.5 py-0.5" style="font-size: 0.65rem;">YOU</span>
                    <?php endif; ?>
                  </div>
                  <div class="d-flex align-items-center gap-1.5">
                    <span class="badge rounded-pill px-2 py-0.5" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border); font-size: 0.68rem;"><?= $row['tier'] ?></span>
                    <?php if ($isPromoted): ?>
                      <span class="promotion-zone-badge py-0.5 px-1.5" style="font-size: 0.65rem;">Promoted</span>
                    <?php elseif ($isDemoted): ?>
                      <span class="demotion-zone-badge py-0.5 px-1.5" style="font-size: 0.65rem;">Danger</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <!-- XP Total -->
              <div class="text-end flex-shrink-0">
                <span class="fw-bold d-inline-flex align-items-center gap-0.5" style="color: var(--accent); font-size: 0.95rem;">
                  <svg class="exam-icon text-warning icon-13"><use href="#icon-star"></use></svg>
                  <?= number_format($row['period_xp']) ?>
                </span>
                <small class="d-block text-ink-muted" style="font-size: 0.68rem;">XP</small>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    <?php elseif ($tab === 'colleges'): ?>
      <!-- ====================================================================
           COLLEGE VS COLLEGE LEADERBOARD (CL4, CL5)
           ==================================================================== -->
      <div class="card card-gamified p-4 mb-4 text-center">
        <h1 class="font-serif fw-bold text-ink mb-2 d-flex align-items-center justify-content-center gap-2 fs-3">
          <i class="bi bi-building fs-2" style="color: var(--accent);"></i> College vs College Ranks
        </h1>
        <p class="text-ink-muted small mb-2">
          Ranked by <strong>Average XP per Active Student</strong> to reward per-capita academic engagement fairly.
        </p>
        <div class="d-flex justify-content-center flex-column align-items-center gap-1">
          <span class="badge rounded-pill px-3 py-1.5 extra-small fw-bold" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border);">
            <i class="bi bi-shield-check me-1 text-success"></i> Minimum 5 active members required to qualify
          </span>
          <span class="text-ink-muted extra-small">
            <em>Active member: A registered student who completed &ge;1 practice question or mock test in the last 7 days.</em>
          </span>
        </div>
      </div>

      <?php if (empty($collegeBoard)): ?>
        <div class="card card-gamified p-5 text-center mb-4">
          <div class="fs-1 mb-2 text-ink-muted"><i class="bi bi-mortarboard"></i></div>
          <h4 class="font-serif fw-bold text-ink mb-2">No Colleges Qualified Yet This Week</h4>
          <p class="text-ink-muted small mb-3">
            To appear on the public college leaderboard, a campus must have at least <strong>5 active students</strong> participating in practice or mock tests in the last 7 days.
          </p>
          <div>
            <a href="index.php?action=profile" class="btn btn-primary-gamified btn-sm fw-bold px-3">
              <i class="bi bi-person-badge me-1"></i> Update Your College in Profile
            </a>
          </div>
        </div>
      <?php else: ?>
        <div class="card card-gamified p-0 overflow-hidden mb-4">
          <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center p-3 border-bottom bg-surface gap-2" style="border-color: var(--border) !important;">
            <span class="fw-bold small text-ink">
              <i class="bi bi-trophy text-warning me-1"></i> Top Campus Communities (<?= count($collegeBoard) ?> Qualified)
            </span>
            <span class="text-ink-muted extra-small">Weekly Reset: Sunday Midnight</span>
          </div>

          <!-- Desktop Table -->
          <div class="table-responsive d-none d-md-block">
            <table class="table table-hover mb-0 align-middle">
              <thead>
                <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
                  <th class="ps-4 font-serif text-ink" style="width: 75px;">Rank</th>
                  <th class="font-serif text-ink">College &amp; Location</th>
                  <th class="font-serif text-ink text-center">Active Students</th>
                  <th class="font-serif text-ink text-center">Avg XP / Student</th>
                  <th class="font-serif text-ink text-center">Avg Mock Score</th>
                  <th class="pe-4 text-end font-serif text-ink">Weekly XP Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($collegeBoard as $c): ?>
                  <tr>
                    <td class="ps-4 text-ink">
                      <?php if ($c['rank'] == 1): ?>
                        <svg class="exam-icon me-1" style="width:16px;height:16px;color:var(--highlight);"><use href="#icon-trophy"></use></svg> <strong>#1</strong>
                      <?php elseif ($c['rank'] == 2): ?>
                        <svg class="exam-icon me-1" style="width:16px;height:16px;color:#94a3b8;"><use href="#icon-medal"></use></svg> <strong>#2</strong>
                      <?php elseif ($c['rank'] == 3): ?>
                        <svg class="exam-icon me-1" style="width:16px;height:16px;color:#b45309;"><use href="#icon-medal"></use></svg> <strong>#3</strong>
                      <?php else: ?>
                        <strong>#<?= (int)$c['rank'] ?></strong>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="index.php?action=leaderboard&tab=campus&college_id=<?= (int)$c['id'] ?>" class="text-decoration-none fw-semibold text-ink d-block">
                        <?= htmlspecialchars($c['name']) ?>
                      </a>
                      <small class="text-ink-muted d-inline-flex align-items-center gap-1 extra-small">
                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($c['city']) ?>, <?= htmlspecialchars($c['state']) ?> &bull; <code><?= htmlspecialchars($c['code']) ?></code>
                      </small>
                    </td>
                    <td class="text-center">
                      <span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle px-2.5 py-1">
                        <i class="bi bi-people-fill me-1"></i> <?= (int)$c['active_members'] ?> active
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold">
                        <?= number_format((float)($c['avg_xp_per_active'] ?? 0), 1) ?> XP/student
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="fw-bold font-mono text-ink"><?= number_format((float)$c['avg_mock_score'], 1) ?>%</span>
                    </td>
                    <td class="pe-4 text-end fw-bold" style="color: var(--accent);">
                      <svg class="exam-icon text-warning me-0.5 icon-12"><use href="#icon-star"></use></svg> <?= number_format((int)$c['weekly_xp']) ?> XP
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="d-md-none p-2 p-sm-3 d-flex flex-column gap-2">
            <?php foreach ($collegeBoard as $c): ?>
              <div class="p-3 rounded-3 border d-flex flex-column gap-2" style="background-color: var(--bg); border-color: var(--border) !important;">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge px-2 py-1 bg-accent text-white fw-bold">#<?= (int)$c['rank'] ?></span>
                    <a href="index.php?action=leaderboard&tab=campus&college_id=<?= (int)$c['id'] ?>" class="text-decoration-none fw-bold text-ink text-truncate" style="max-width: 200px;">
                      <?= htmlspecialchars($c['name']) ?>
                    </a>
                  </div>
                  <span class="fw-bold text-accent font-mono" style="color: var(--accent);">
                    <?= number_format((float)($c['avg_xp_per_active'] ?? 0), 1) ?> <small>XP/st</small>
                  </span>
                </div>
                <div class="d-flex align-items-center justify-content-between extra-small text-ink-muted">
                  <span><i class="bi bi-geo-alt me-0.5"></i> <?= htmlspecialchars($c['city']) ?>, <?= htmlspecialchars($c['state']) ?></span>
                  <span><i class="bi bi-people me-0.5"></i> <?= (int)$c['active_members'] ?> active &bull; Total <?= number_format((int)$c['weekly_xp']) ?> XP &bull; <?= number_format((float)$c['avg_mock_score'], 1) ?>%</span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    <?php elseif ($tab === 'campus'): ?>
      <!-- ====================================================================
           INTRA-COLLEGE STUDENT RANKING (CL6, CL7)
           ==================================================================== -->
      <div class="card card-gamified p-4 mb-4 text-center">
        <h1 class="font-serif fw-bold text-ink mb-2 d-flex align-items-center justify-content-center gap-2 fs-3">
          <i class="bi bi-mortarboard-fill fs-2" style="color: var(--accent);"></i> Campus Student Ranks
        </h1>
        <p class="text-ink-muted small mb-3">
          Compare weekly study points with fellow classmates. Only display names are visible; student privacy is protected.
        </p>

        <!-- College Selector Dropdown -->
        <div class="d-flex justify-content-center">
          <form method="GET" action="index.php" class="d-flex align-items-center gap-2 flex-wrap justify-content-center" style="max-width: 500px;">
            <input type="hidden" name="action" value="leaderboard">
            <input type="hidden" name="tab" value="campus">
            <label class="form-label extra-small fw-bold text-ink-muted mb-0 me-1" for="campusCollegeSelect">Campus:</label>
            <select name="college_id" id="campusCollegeSelect" class="form-select form-select-sm" onchange="this.form.submit()" style="background-color: var(--bg); border-color: var(--border); color: var(--ink); min-width: 250px;">
              <?php foreach ($activeColleges as $ac): ?>
                <option value="<?= (int)$ac['id'] ?>" <?= ((int)$selectedCollegeId === (int)$ac['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($ac['name']) ?> (<?= htmlspecialchars($ac['city']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>
      </div>

      <?php if (!$campusBoard || empty($campusBoard['students'])): ?>
        <div class="card card-gamified p-5 text-center mb-4">
          <div class="fs-1 mb-2 text-ink-muted"><i class="bi bi-person-x"></i></div>
          <h4 class="font-serif fw-bold text-ink mb-2">No Students Ranked Yet</h4>
          <p class="text-ink-muted small mb-3">
            No active students are currently registered under this college. Be the first to join and earn points for your campus!
          </p>
          <div>
            <a href="index.php?action=profile" class="btn btn-primary-gamified btn-sm fw-bold px-3">
              <i class="bi bi-person-badge me-1"></i> Update Your College in Profile
            </a>
          </div>
        </div>
      <?php else: ?>
        <!-- User Relative Rank Bar if user is in this college -->
        <?php if (!empty($campusBoard['current_user_rank'])): ?>
          <div class="alert border rounded-4 p-3 mb-4 d-flex justify-content-between align-items-center" style="background-color: var(--surface); border-color: var(--accent) !important; color: var(--ink);">
            <div>
              <span class="badge rounded-pill px-3 py-2 me-2" style="background-color: var(--accent); color: white;">Your Campus Rank: #<?= $campusBoard['current_user_rank'] ?></span>
              <strong class="text-ink font-serif"><?= htmlspecialchars($campusBoard['college']['name']) ?></strong>
            </div>
            <div class="fw-bold small d-inline-flex align-items-center gap-1" style="color: var(--accent);">
              <svg class="exam-icon icon-14"><use href="#icon-zap"></use></svg> Ranked <?= $campusBoard['current_user_rank'] ?> of <?= $campusBoard['total_ranked'] ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="card card-gamified p-0 overflow-hidden mb-4">
          <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center p-3 border-bottom bg-surface gap-2" style="border-color: var(--border) !important;">
            <div class="d-flex align-items-center gap-2">
              <span class="fw-bold small text-ink"><i class="bi bi-mortarboard text-accent me-1"></i> <?= htmlspecialchars($campusBoard['college']['name']) ?></span>
              <span class="badge rounded-pill px-2 py-0.5 extra-small" style="background-color: var(--surface); border: 1px solid var(--border); color: var(--ink-muted);"><?= (int)$campusBoard['total_ranked'] ?> Students</span>
            </div>
            <span class="text-ink-muted extra-small">Rankings update with student practice</span>
          </div>

          <!-- Desktop Table -->
          <div class="table-responsive d-none d-md-block">
            <table class="table table-hover mb-0 align-middle">
              <thead>
                <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
                  <th class="ps-4 font-serif text-ink" style="width: 80px;">Rank</th>
                  <th class="font-serif text-ink">Student</th>
                  <th class="font-serif text-ink text-center">League Tier</th>
                  <th class="font-serif text-ink text-center">Streak</th>
                  <th class="pe-4 text-end font-serif text-ink">Weekly XP</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($campusBoard['students'] as $s): ?>
                  <tr style="<?= !empty($s['is_self']) ? 'background-color: rgba(30, 58, 95, 0.08); font-weight: bold;' : '' ?>">
                    <td class="ps-4 text-ink">
                      <?php if ($s['rank'] == 1): ?>
                        <svg class="exam-icon me-1" style="width:16px;height:16px;color:var(--highlight);"><use href="#icon-trophy"></use></svg> <strong>#1</strong>
                      <?php elseif ($s['rank'] == 2): ?>
                        <svg class="exam-icon me-1" style="width:16px;height:16px;color:#94a3b8;"><use href="#icon-medal"></use></svg> <strong>#2</strong>
                      <?php elseif ($s['rank'] == 3): ?>
                        <svg class="exam-icon me-1" style="width:16px;height:16px;color:#b45309;"><use href="#icon-medal"></use></svg> <strong>#3</strong>
                      <?php else: ?>
                        <strong>#<?= (int)$s['rank'] ?></strong>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi <?= htmlspecialchars($s['avatar_icon']) ?> fs-5" style="color: var(--accent);"></i>
                        <span class="fw-semibold text-ink"><?= htmlspecialchars($s['display_name']) ?></span>
                        <?php if (!empty($s['is_self'])): ?>
                          <span class="badge bg-accent text-white ms-1" style="font-size:0.65rem;">YOU</span>
                        <?php endif; ?>
                        <?php if (!empty($s['is_anonymous'])): ?>
                          <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-1.5 py-0.5" style="font-size: 0.6rem;">
                            <i class="bi bi-incognito"></i> Hidden
                          </span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="badge rounded-pill" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border);">
                        <?= htmlspecialchars($s['tier']) ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <?php if (!empty($s['current_streak']) && $s['current_streak'] > 0): ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 extra-small">
                          <i class="bi bi-fire"></i> <?= (int)$s['current_streak'] ?>d
                        </span>
                      <?php else: ?>
                        <span class="text-ink-muted extra-small">--</span>
                      <?php endif; ?>
                    </td>
                    <td class="pe-4 text-end fw-bold" style="color: var(--accent);">
                      <svg class="exam-icon text-warning me-0.5 icon-12"><use href="#icon-star"></use></svg> <?= number_format((int)$s['weekly_xp']) ?> XP
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Mobile Compact Cards -->
          <div class="d-md-none p-2 p-sm-3 d-flex flex-column gap-2">
            <?php foreach ($campusBoard['students'] as $s): ?>
              <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between gap-2" style="background-color: <?= !empty($s['is_self']) ? 'rgba(30, 58, 95, 0.08)' : 'var(--bg)' ?>; border-color: <?= !empty($s['is_self']) ? 'var(--accent)' : 'var(--border)' ?> !important; min-height: 52px;">
                <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                  <div class="flex-shrink-0 text-center" style="min-width: 32px;">
                    <span class="fw-bold fs-7 text-ink-muted">#<?= (int)$s['rank'] ?></span>
                  </div>
                  <div class="min-w-0 flex-grow-1">
                    <div class="d-flex align-items-center gap-1.5 mb-0.5">
                      <i class="bi <?= htmlspecialchars($s['avatar_icon']) ?> fs-6" style="color: var(--accent);"></i>
                      <span class="fw-bold text-ink text-truncate d-inline-block" style="font-size: 0.92rem; max-width: 140px;"><?= htmlspecialchars($s['display_name']) ?></span>
                      <?php if (!empty($s['is_self'])): ?>
                        <span class="badge bg-accent text-white px-1.5 py-0.5" style="font-size: 0.65rem;">YOU</span>
                      <?php endif; ?>
                    </div>
                    <div class="d-flex align-items-center gap-1.5 extra-small">
                      <span class="badge rounded-pill px-2 py-0.5" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border); font-size: 0.68rem;"><?= htmlspecialchars($s['tier']) ?></span>
                      <?php if (!empty($s['current_streak']) && $s['current_streak'] > 0): ?>
                        <span class="text-danger fw-bold"><i class="bi bi-fire"></i> <?= (int)$s['current_streak'] ?>d</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div class="text-end flex-shrink-0">
                  <span class="fw-bold d-inline-flex align-items-center gap-0.5" style="color: var(--accent); font-size: 0.95rem;">
                    <svg class="exam-icon text-warning icon-13"><use href="#icon-star"></use></svg>
                    <?= number_format((int)$s['weekly_xp']) ?>
                  </span>
                  <small class="d-block text-ink-muted" style="font-size: 0.68rem;">XP</small>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</div>
