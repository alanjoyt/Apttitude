<?php
$db = Database::getConnection();
$userId = $_SESSION['user_id'] ?? null;

// Fetch campaign nodes
$nodes = $db->query("
    SELECT c.*, s.name as subtopic_name, t.name as topic_name
    FROM campaign_nodes c
    JOIN subtopics s ON c.subtopic_id = s.id
    JOIN topics t ON s.topic_id = t.id
    ORDER BY c.chapter_number ASC, c.node_order ASC
")->fetchAll();

// Group by chapter
$chapters = [];
foreach ($nodes as $n) {
    $chapters[$n['chapter_number']][] = $n;
}
?>

<div class="row justify-content-center py-3">
  <div class="col-12 text-center mb-4">
    <div class="stamp-badge mb-2">
      <i class="bi bi-map-fill me-1"></i> Story & Campaign Mode
    </div>
    <h1 class="font-serif fw-bold fs-2 text-ink">Aptitude Quest &amp; Placement Campaign</h1>
    <p class="text-ink-muted mx-auto" style="max-width: 600px;">
      Embark on a chapter-based journey across Quant Outposts and Reasoning Labyrinths. Earn 3 stars per node to unlock legendary rewards!
    </p>
  </div>

  <?php foreach ($chapters as $chapterNum => $chapterNodes): ?>
    <div class="col-lg-10 mb-5">
      <div class="card card-gamified p-4">
        
        <div class="d-flex align-items-center gap-3 mb-4">
          <div class="feature-icon-box mb-0">
            <i class="bi bi-compass-fill"></i>
          </div>
          <div>
            <h4 class="font-serif fw-bold text-ink mb-0">Chapter <?= $chapterNum ?>: Aptitude Realm</h4>
            <span class="text-ink-muted small"><?= count($chapterNodes) ?> Campaign Outposts</span>
          </div>
        </div>

        <!-- Board Game Level Path -->
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 py-3">
          <?php foreach ($chapterNodes as $idx => $node): ?>
            
            <div class="card card-gamified p-4 text-center" style="min-width: 220px; max-width: 240px;">
              <div class="fs-1 mb-2" style="color: var(--highlight);">
                <svg class="exam-icon" style="width:36px;height:36px;"><use href="#icon-shield"></use></svg>
              </div>
              <h6 class="font-serif fw-bold text-ink mb-1"><?= htmlspecialchars($node['title']) ?></h6>
              <span class="badge rounded-pill mb-2" style="background-color: var(--surface); color: var(--ink-muted); border: 1px solid var(--border);"><?= htmlspecialchars($node['subtopic_name']) ?></span>

              <div class="mb-3" style="color: var(--highlight);">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
              </div>

              <div class="small text-ink-muted mb-3">
                <span>Reward: +<?= $node['reward_xp'] ?> XP &bull; <svg class="exam-icon" style="width:14px;height:14px;vertical-align:-2px;"><use href="#icon-coin"></use></svg> <?= $node['reward_coins'] ?></span>
              </div>

              <a href="index.php?action=practice&subtopic_id=<?= $node['subtopic_id'] ?>" class="btn btn-primary-gamified btn-sm px-4 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1">
                Play Outpost <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-swords"></use></svg>
              </a>
            </div>

            <?php if ($idx < count($chapterNodes) - 1): ?>
              <div class="d-none d-md-block text-ink-muted fs-3">
                <i class="bi bi-arrow-right-short"></i>
              </div>
            <?php endif; ?>

          <?php endforeach; ?>
        </div>

      </div>
    </div>
  <?php endforeach; ?>
</div>
