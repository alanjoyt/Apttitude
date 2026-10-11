<div class="row justify-content-center py-3">
  <div class="col-lg-8 col-md-10">

    <!-- Leaderboard Header Card -->
    <div class="card card-gamified p-4 mb-4 text-center">
      <h1 class="font-serif fw-bold text-ink mb-2 d-flex align-items-center justify-content-center gap-2 fs-3">
        <i class="bi bi-trophy-fill fs-2" style="color: var(--highlight);"></i> Global Leaderboard
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
          <a href="index.php?action=leaderboard&tier=<?= $tierName ?>" 
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
          <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-zap"></use></svg> <?= htmlspecialchars($userRank['message'] ?? '') ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Top 3 Podium Visual (if rankings available) -->
    <?php if (count($rankings) >= 3): ?>
      <div class="row g-3 text-center mb-4 align-items-end">
        <!-- 2nd Place -->
        <div class="col-4">
          <div class="card card-gamified p-3">
            <div class="fs-1"><svg class="exam-icon" style="width:36px;height:36px;color:#94a3b8;"><use href="#icon-medal"></use></svg></div>
            <h6 class="font-serif fw-bold text-ink mb-1"><?= htmlspecialchars($rankings[1]['name']) ?></h6>
            <span class="badge rounded-pill" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border);"><?= number_format($rankings[1]['period_xp']) ?> XP</span>
          </div>
        </div>
        <!-- 1st Place -->
        <div class="col-4">
          <div class="card card-gamified p-4 shadow-sm" style="transform: scale(1.05); background-color: var(--highlight-bg); border: 2px solid var(--highlight-border) !important;">
            <div class="fs-1"><svg class="exam-icon" style="width:42px;height:42px;color:var(--highlight);"><use href="#icon-trophy"></use></svg></div>
            <h5 class="font-serif fw-bold mb-1" style="color: var(--highlight-text);"><?= htmlspecialchars($rankings[0]['name']) ?></h5>
            <span class="badge fw-bold rounded-pill" style="background-color: var(--highlight-border); color: #14213D;"><?= number_format($rankings[0]['period_xp']) ?> XP</span>
          </div>
        </div>
        <!-- 3rd Place -->
        <div class="col-4">
          <div class="card card-gamified p-3">
            <div class="fs-1"><svg class="exam-icon" style="width:36px;height:36px;color:#b45309;"><use href="#icon-medal"></use></svg></div>
            <h6 class="font-serif fw-bold text-ink mb-1"><?= htmlspecialchars($rankings[2]['name']) ?></h6>
            <span class="badge rounded-pill" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border);"><?= number_format($rankings[2]['period_xp']) ?> XP</span>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Full Leaderboard Table with League Zones -->
    <div class="card card-gamified p-0 overflow-hidden mb-4">
      <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-surface" style="border-color: var(--border) !important;">
        <div class="d-flex align-items-center gap-2">
          <span class="promotion-zone-badge d-inline-flex align-items-center gap-1"><svg class="exam-icon text-success" style="width:12px;height:12px;"><use href="#icon-arrow-right"></use></svg> Ranks 1-3: League Promotion Zone</span>
        </div>
        <span class="text-ink-muted small">Weekly Reset: Sunday Midnight</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead>
            <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
              <th class="ps-4 font-serif text-ink">Rank</th>
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
                <svg class="exam-icon text-warning me-0.5" style="width:12px;height:12px;"><use href="#icon-star"></use></svg> <?= number_format($row['period_xp']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>

  </div>
</div>
