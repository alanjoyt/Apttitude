<?php
$db = Database::getConnection();
$userId = $_SESSION['user_id'] ?? null;

// Fetch all topics and subtopics
$topics = $db->query("SELECT * FROM topics ORDER BY id ASC")->fetchAll();
$subtopics = $db->query("SELECT s.*, t.name as topic_name FROM subtopics s JOIN topics t ON s.topic_id = t.id ORDER BY s.topic_id ASC, s.id ASC")->fetchAll();

// Fetch user progress mapping
$userProgressMap = [];
if ($userId) {
    $stmt = $db->prepare("SELECT * FROM user_progress WHERE user_id = ?");
    $stmt->execute([$userId]);
    foreach ($stmt->fetchAll() as $row) {
        $userProgressMap[$row['subtopic_id']] = $row;
    }
}
?>

<div class="row justify-content-center py-3">
  <div class="col-12 text-center mb-4">
    <div class="stamp-badge mb-2">
      <i class="bi bi-diagram-3-fill me-1"></i> Interactive Skill Tree Node-Map
    </div>
    <h1 class="font-serif fw-bold fs-2 text-ink">Aptitude Mastery Skill Trees</h1>
    <p class="text-ink-muted mx-auto" style="max-width: 600px;">
      Unlock subtopic nodes by reaching higher mastery levels (Level 1 → Level 5). Click any node to start targeted adaptive practice.
    </p>
  </div>

  <?php foreach ($topics as $topic): ?>
    <div class="col-12 mb-5">
      <div class="card card-gamified p-4">
        
        <!-- Topic Header -->
        <div class="d-flex align-items-center gap-3 mb-4">
          <div class="feature-icon-box mb-0">
            <i class="bi <?= $topic['icon'] ?: 'bi-journal-text' ?>"></i>
          </div>
          <div>
            <h4 class="font-serif fw-bold text-ink mb-0"><?= htmlspecialchars($topic['name']) ?></h4>
            <span class="text-ink-muted small">Topic Code: <?= htmlspecialchars($topic['code']) ?></span>
          </div>
        </div>

        <!-- Node Map Layout -->
        <div class="skill-tree-grid d-flex flex-wrap justify-content-center align-items-center gap-4 py-3">
          <?php 
            $topicSubtopics = array_filter($subtopics, fn($s) => $s['topic_id'] == $topic['id']);
            $nodeIndex = 0;
            foreach ($topicSubtopics as $sub):
              $nodeIndex++;
              $prog = $userProgressMap[$sub['id']] ?? null;
              $masteryLevel = $prog ? (int)$prog['current_mastery_level'] : 1;
              $totalAttempted = $prog ? (int)$prog['total_attempted'] : 0;
              $totalCorrect = $prog ? (int)$prog['total_correct'] : 0;
              $isLocked = ($nodeIndex > 1 && !$userId && $nodeIndex > 2); // Sample unlock condition
          ?>

            <!-- Node Card -->
            <div class="skill-node-card text-center p-3.5 <?= $isLocked ? 'is-locked' : '' ?>" style="min-width: 230px; max-width: 250px; position: relative;">
              
              <!-- Node Level Badge -->
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="node-badge-level">
                  Mastery Level <?= $masteryLevel ?>/5
                </span>
                <span class="badge rounded-pill bg-surface text-ink-muted border" style="font-size: 0.7rem;">
                  <?= $totalAttempted > 0 ? round(($totalCorrect / $totalAttempted) * 100) : 0 ?>% Acc
                </span>
              </div>

              <!-- Node Title -->
              <h6 class="font-serif fw-bold text-ink mb-2"><?= htmlspecialchars($sub['name']) ?></h6>

              <!-- Mastery Stars -->
              <div class="mb-2" style="color: var(--highlight);">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <i class="bi <?= $i <= $masteryLevel ? 'bi-star-fill' : 'bi-star' ?>"></i>
                <?php endfor; ?>
              </div>

              <!-- Subtopic Progress Bar -->
              <div class="progress mb-3" style="height: 6px; background-color: var(--border);">
                <div class="progress-bar-fill" style="width: <?= min(100, $masteryLevel * 20) ?>%;"></div>
              </div>

              <!-- Stats & Action -->
              <div class="text-ink-muted extra-small mb-3">
                <span><?= $totalCorrect ?> Solved of <?= $totalAttempted ?> Attempted</span>
              </div>

              <a href="index.php?action=practice&subtopic_id=<?= $sub['id'] ?>" class="btn btn-sm btn-primary-gamified px-3 w-100 fw-bold shadow-sm">
                <i class="bi bi-controller me-1"></i> Practice Node
              </a>
            </div>

            <?php if ($nodeIndex < count($topicSubtopics)): ?>
              <div class="d-none d-md-flex align-items-center justify-content-center text-ink-muted fs-3 px-1">
                <i class="bi bi-chevron-right text-accent"></i>
              </div>
            <?php endif; ?>

          <?php endforeach; ?>
        </div>

      </div>
    </div>
  <?php endforeach; ?>
</div>
