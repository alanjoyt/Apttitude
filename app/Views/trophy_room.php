<?php
$db = Database::getConnection();
$userId = $_SESSION['user_id'] ?? null;

$user = User::findById($userId);
if (!$user) {
    $user = [
        'id' => 0,
        'name' => 'Guest Student',
        'email' => 'guest@example.com',
        'tier' => 'Bronze',
        'current_streak' => 0,
        'coins' => 0,
        'total_xp' => 0,
        'avatar_icon' => 'bi-person-circle'
    ];
}

$mascot = GamificationEngine::getMascotForTier($user['tier'] ?? 'Bronze');

// Fetch unlocked badges
$badgesStmt = $db->prepare("
    SELECT b.*, ub.earned_at
    FROM badges b
    JOIN user_badges ub ON b.id = ub.badge_id
    WHERE ub.user_id = ?
");
$badgesStmt->execute([$userId]);
$earnedBadges = $badgesStmt->fetchAll();

// Fetch all catalog badges
$allBadges = $db->query("SELECT * FROM badges ORDER BY id ASC")->fetchAll();
$earnedBadgeIds = array_column($earnedBadges, 'id');

// Fetch unlocked store items
$unlocksStmt = $db->prepare("
    SELECT u.*, uu.unlocked_at
    FROM unlockables u
    JOIN user_unlocks uu ON u.id = uu.unlockable_id
    WHERE uu.user_id = ?
");
$unlocksStmt->execute([$userId]);
$unlockedItems = $unlocksStmt->fetchAll();
?>

<div class="row g-4 justify-content-center py-3">
  <div class="col-12 text-center mb-2">
    <div class="stamp-badge mb-2">
      <i class="bi bi-award-fill me-1"></i> Student Profile Showcase
    </div>
    <h2 class="font-serif fw-bold fs-2 text-ink">Trophy Room & Mascot Identity</h2>
    <p class="text-ink-muted">Displaying your evolving mascot avatar, earned badges, unlocked items, and milestone statistics.</p>
  </div>

  <!-- Evolving Mascot Avatar Showcase Card -->
  <div class="col-md-5 col-lg-4">
    <div class="card card-gamified p-4 text-center">
      <div class="display-1 my-3" style="color: var(--highlight);">
        <?= $mascot['emoji'] ?>
      </div>
      <h3 class="font-serif fw-bold text-ink mb-1"><?= htmlspecialchars($mascot['name']) ?></h3>
      <div class="d-flex justify-content-center">
        <span class="stamp-badge py-1 px-3 mb-3">
          <?= $user['tier'] ?> Tier Mascot
        </span>
      </div>
      <p class="text-ink-muted small mb-4">
        "<?= $mascot['desc'] ?>"
      </p>

      <div class="border-top pt-3" style="border-color: var(--border) !important;">
        <div class="d-flex justify-content-between text-ink-muted small mb-2">
          <span>Student Name</span>
          <strong class="text-ink font-serif"><?= htmlspecialchars($user['name']) ?></strong>
        </div>
        <div class="d-flex justify-content-between text-ink-muted small mb-2">
          <span>Active Streak</span>
          <strong class="d-inline-flex align-items-center gap-1" style="color: var(--highlight);"><svg class="exam-icon text-danger" style="width:13px;height:13px;"><use href="#icon-flame"></use></svg> <?= $user['current_streak'] ?> Days</strong>
        </div>
        <div class="d-flex justify-content-between text-ink-muted small">
          <span>Speed Coins Wallet</span>
          <strong class="d-inline-flex align-items-center gap-1" style="color: var(--accent);"><svg class="exam-icon" style="width:13px;height:13px;"><use href="#icon-coin"></use></svg> <?= number_format($user['coins']) ?> Coins</strong>
        </div>
      </div>
    </div>
  </div>

  <!-- Badges & Achievements Grid -->
  <div class="col-md-7 col-lg-8">
    <div class="card card-gamified p-4 mb-4">
      <h4 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-trophy-fill fs-3" style="color: var(--highlight);"></i> Badges Showcase (<?= count($earnedBadges) ?> / <?= count($allBadges) ?>)
      </h4>

      <div class="row g-3">
        <?php foreach ($allBadges as $badge): 
          $isEarned = in_array($badge['id'], $earnedBadgeIds);
        ?>
          <div class="col-6 col-sm-4">
            <div class="p-3 text-center rounded-4 border" style="background-color: <?= $isEarned ? 'var(--highlight-bg)' : 'var(--bg)' ?>; border-color: <?= $isEarned ? 'var(--highlight-border)' : 'var(--border)' ?> !important;">
              <i class="bi <?= $badge['icon'] ?> display-5 mb-2 d-block" style="color: <?= $isEarned ? 'var(--highlight-border)' : 'var(--ink-muted)' ?>;"></i>
              <h6 class="font-serif fw-bold mb-1" style="color: <?= $isEarned ? 'var(--highlight-text)' : 'var(--ink-muted)' ?>;"><?= htmlspecialchars($badge['title']) ?></h6>
              <small class="text-ink-muted d-block fs-8"><?= htmlspecialchars($badge['description']) ?></small>
              <?php if ($isEarned): ?>
                <span class="badge text-white mt-2 rounded-pill small d-inline-flex align-items-center gap-1" style="background-color: var(--success);"><svg class="exam-icon" style="width:10px;height:10px;"><use href="#icon-check"></use></svg> Unlocked</span>
              <?php else: ?>
                <span class="badge rounded-pill small mt-2" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border);">+<?= $badge['xp_reward'] ?> XP Reward</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Unlocked Store Packs -->
    <div class="card card-gamified p-4">
      <h4 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-bag-check-fill fs-3" style="color: var(--accent);"></i> Unlocked Speed Store Items
      </h4>

      <?php if (empty($unlockedItems)): ?>
        <p class="text-ink-muted small mb-0">No store items unlocked yet. Earn Speed Coins in practice to buy company mock packs!</p>
      <?php else: ?>
        <div class="d-flex flex-column gap-2">
          <?php foreach ($unlockedItems as $item): ?>
            <div class="d-flex justify-content-between align-items-center p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
              <div class="d-flex align-items-center gap-2">
                <i class="bi <?= $item['icon'] ?> fs-4" style="color: var(--accent);"></i>
                <div>
                  <strong class="d-block text-ink font-serif"><?= htmlspecialchars($item['title']) ?></strong>
                  <span class="text-ink-muted small"><?= htmlspecialchars($item['description']) ?></span>
                </div>
              </div>
              <span class="stamp-badge py-1 px-3">Active</span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>
