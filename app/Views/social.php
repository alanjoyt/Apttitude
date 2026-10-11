<?php
$db = Database::getConnection();
$userId = $_SESSION['user_id'] ?? null;

$friends = SocialService::getFriends($userId);
$pendingRequests = SocialService::getPendingRequests($userId);
$guild = SocialService::getUserGuild($userId);
$allGuilds = $db->query("SELECT g.*, COUNT(gm.user_id) as member_count FROM guilds g LEFT JOIN guild_members gm ON g.id = gm.guild_id GROUP BY g.id ORDER BY g.current_weekly_xp DESC")->fetchAll();
?>

<div class="row g-4 py-3">
  <div class="col-12 text-center mb-2">
    <div class="stamp-badge mb-2">
      <i class="bi bi-people-fill me-1"></i> Social Competition & Study Squads
    </div>
    <h1 class="font-serif fw-bold fs-2 text-ink">Friends, Rivals &amp; Study Squads</h1>
    <p class="text-ink-muted mx-auto" style="max-width: 600px;">
      Add friends, monitor live rank rivalries, and pool XP together in 5-student Study Squads to unlock collective Speed Coins rewards!
    </p>
  </div>

  <!-- Study Squads Section -->
  <div class="col-md-6">
    <div class="card card-gamified p-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-shield-shaded fs-3" style="color: var(--accent);"></i> My Study Squad
        </h4>
        <?php if ($guild): ?>
          <span class="badge rounded-pill px-3 py-1 font-weight-bold" style="background-color: var(--accent); color: white;">[<?= htmlspecialchars($guild['tag']) ?>]</span>
        <?php endif; ?>
      </div>

      <?php if ($guild): 
        $targetXP = (int)$guild['weekly_xp_target'];
        $currentXP = (int)$guild['current_weekly_xp'];
        $percent = min(100, round(($currentXP / max(1, $targetXP)) * 100));
      ?>
        <div class="p-3 rounded-4 border mb-3" style="background-color: var(--bg); border-color: var(--border) !important;">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <strong class="font-serif fs-5 text-ink"><?= htmlspecialchars($guild['name']) ?></strong>
            <span class="small fw-bold" style="color: var(--highlight);">Goal: <?= number_format($currentXP) ?> / <?= number_format($targetXP) ?> XP</span>
          </div>
          <p class="text-ink-muted small mb-3"><?= htmlspecialchars($guild['description']) ?></p>
          <div class="progress" style="height: 10px; background-color: var(--border);">
            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?= $percent ?>%; background-color: var(--accent);"></div>
          </div>
        </div>

        <h6 class="font-serif fw-bold text-ink mb-2">Squad Roster (<?= count($guild['members']) ?> / 5)</h6>
        <div class="d-flex flex-column gap-2 mb-3">
          <?php foreach ($guild['members'] as $m): ?>
            <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border" style="background-color: var(--bg); border-color: var(--border) !important;">
              <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-circle px-2 py-1" style="background-color: var(--highlight); color: white;"><?= substr($m['tier'], 0, 1) ?></span>
                <div>
                  <strong class="d-block small text-ink"><?= htmlspecialchars($m['name']) ?> <?= $m['role'] === 'leader' ? '<svg class="exam-icon text-warning ms-1" style="width:12px;height:12px;vertical-align:-1px;"><use href="#icon-crown"></use></svg>' : '' ?></strong>
                  <span class="text-ink-muted fs-8"><?= number_format($m['total_xp']) ?> Total XP</span>
                </div>
              </div>
              <span class="badge px-2 py-1 fs-8" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border);">+<?= number_format($m['contributed_xp']) ?> XP</span>
            </div>
          <?php endforeach; ?>
        </div>

      <?php else: ?>
        <p class="text-ink-muted small mb-4">You are not currently in a Study Squad. Join an existing squad or form a new squad with your friends!</p>
        
        <div class="d-flex gap-2 mb-4">
          <button class="btn btn-primary-gamified px-4 fw-bold w-100" data-bs-toggle="modal" data-bs-target="#createSquadModal">
            <i class="bi bi-plus-circle me-1"></i> Create Squad
          </button>
        </div>

        <h6 class="font-serif fw-bold text-ink mb-2">Available Squads to Join</h6>
        <div class="d-flex flex-column gap-2">
          <?php foreach ($allGuilds as $g): ?>
            <div class="d-flex justify-content-between align-items-center p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
              <div>
                <strong class="d-block text-ink font-serif">[<?= htmlspecialchars($g['tag']) ?>] <?= htmlspecialchars($g['name']) ?></strong>
                <span class="text-ink-muted fs-8"><?= $g['member_count'] ?>/5 Members &bull; <?= number_format($g['current_weekly_xp']) ?> Weekly XP</span>
              </div>
              <?php if ($g['member_count'] < 5): ?>
                <button onclick="joinSquad(<?= $g['id'] ?>)" class="btn btn-outline-gamified btn-sm px-3 fw-bold">
                  Join Squad
                </button>
              <?php else: ?>
                <span class="badge" style="background-color: var(--border); color: var(--ink-muted);">Full</span>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- Friends & Rivals Section -->
  <div class="col-md-6">
    <div class="card card-gamified p-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-person-heart fs-3" style="color: var(--accent);"></i> Friends & Rivals
        </h4>
        <button class="btn btn-primary-gamified btn-sm px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addFriendModal">
          <i class="bi bi-person-plus-fill me-1"></i> Add Friend
        </button>
      </div>

      <!-- Pending Requests -->
      <?php if (!empty($pendingRequests)): ?>
        <div class="exam-notice-banner p-3 mb-3" style="background-color: var(--notice-bg); color: var(--notice-text); border: 1.5px solid var(--notice-border);">
          <h6 class="font-serif fw-bold mb-2"><i class="bi bi-bell-fill me-1" style="color: var(--accent);"></i> Pending Friend Requests</h6>
          <?php foreach ($pendingRequests as $req): ?>
            <div class="d-flex justify-content-between align-items-center py-1">
              <span class="small fw-bold text-ink"><?= htmlspecialchars($req['name']) ?> (<?= $req['tier'] ?>)</span>
              <button onclick="acceptFriend(<?= $req['request_id'] ?>)" class="btn btn-primary-gamified btn-sm px-3 py-1 fs-8 fw-bold">
                Accept Request
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Friends List -->
      <h6 class="font-serif fw-bold text-ink mb-2">My Friends Roster (<?= count($friends) ?>)</h6>
      <?php if (empty($friends)): ?>
        <p class="text-ink-muted small mb-0">No friends added yet. Click "Add Friend" to connect with study partners!</p>
      <?php else: ?>
        <div class="d-flex flex-column gap-2">
          <?php foreach ($friends as $f): ?>
            <div class="d-flex justify-content-between align-items-center p-3 rounded-4 border" style="background-color: var(--bg); border-color: var(--border) !important;">
              <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-circle px-2 py-1" style="background-color: var(--highlight); color: white;"><?= substr($f['tier'], 0, 1) ?></span>
                <div>
                  <strong class="d-block small text-ink font-serif"><?= htmlspecialchars($f['name']) ?></strong>
                  <span class="text-ink-muted fs-8 d-inline-flex align-items-center gap-1"><svg class="exam-icon text-warning" style="width:11px;height:11px;"><use href="#icon-star"></use></svg> <?= number_format($f['total_xp']) ?> XP &bull; <svg class="exam-icon text-danger" style="width:11px;height:11px;"><use href="#icon-flame"></use></svg> <?= $f['current_streak'] ?>d streak</span>
                </div>
              </div>
              <a href="index.php?action=duel" class="btn btn-outline-gamified btn-sm px-3 fs-8 fw-bold d-inline-flex align-items-center gap-1">
                <svg class="exam-icon" style="width:12px;height:12px;"><use href="#icon-swords"></use></svg> Challenge 1v1
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<!-- Modal: Add Friend -->
<div class="modal fade" id="addFriendModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border-color: var(--border) !important;">
      <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
        <h5 class="modal-title font-serif fw-bold"><i class="bi bi-person-plus-fill me-2" style="color: var(--accent);"></i> Add Friend / Rival</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">Friend Email Address</label>
          <input type="email" id="friendEmailInput" class="form-control" placeholder="friend@example.com" style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
      </div>
      <div class="modal-footer border-top" style="border-color: var(--border) !important;">
        <button type="button" class="btn btn-outline-gamified btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary-gamified btn-sm fw-bold" onclick="submitAddFriend()">Send Request</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Create Squad -->
<div class="modal fade" id="createSquadModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border-color: var(--border) !important;">
      <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
        <h5 class="modal-title font-serif fw-bold"><i class="bi bi-plus-circle-fill me-2" style="color: var(--accent);"></i> Create Study Squad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">Squad Name</label>
          <input type="text" id="squadNameInput" class="form-control" placeholder="Code Ninjas" style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">Squad Tag (3-5 Characters)</label>
          <input type="text" id="squadTagInput" class="form-control" placeholder="NINJA" style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold text-ink-muted">Description / Goal</label>
          <input type="text" id="squadDescInput" class="form-control" placeholder="Targeting TCS NQT 2026!" style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
      </div>
      <div class="modal-footer border-top" style="border-color: var(--border) !important;">
        <button type="button" class="btn btn-outline-gamified btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary-gamified btn-sm fw-bold" onclick="submitCreateSquad()">Create Squad</button>
      </div>
    </div>
  </div>
</div>

<script>
function submitAddFriend() {
    const email = document.getElementById('friendEmailInput').value;
    fetch('index.php?action=api_add_friend', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') window.location.reload();
    });
}

function acceptFriend(requestId) {
    fetch('index.php?action=api_accept_friend', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ request_id: requestId })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') window.location.reload();
    });
}

function submitCreateSquad() {
    const name = document.getElementById('squadNameInput').value;
    const tag = document.getElementById('squadTagInput').value;
    const desc = document.getElementById('squadDescInput').value;

    fetch('index.php?action=api_create_squad', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: name, tag: tag, description: desc })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') window.location.reload();
    });
}

function joinSquad(squadId) {
    fetch('index.php?action=api_join_squad', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ squad_id: squadId })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') window.location.reload();
    });
}
</script>
