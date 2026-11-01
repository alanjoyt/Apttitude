<?php
$slug = $_GET['slug'] ?? '';
$company = CompanyController::getCompanyBySlug($slug);

if (!$company) {
    echo "<div class='container py-5 text-center'><h3>Company Not Found</h3><a href='index.php?action=companies' class='btn btn-primary mt-3'>Back to Company Hub</a></div>";
    return;
}

$rounds = $company['rounds'] ?? [];
$tierBadge = match($company['difficulty_tier']) {
    'premium' => 'bg-danger-subtle text-danger border-danger-subtle',
    'elevated' => 'bg-warning-subtle text-warning border-warning-subtle',
    default => 'bg-info-subtle text-info border-info-subtle'
};
$tierLabel = match($company['difficulty_tier']) {
    'premium' => 'Premium Tier (Big Tech Bar)',
    'elevated' => 'Elevated Bar',
    default => 'Standard Placement'
};
?>

<div class="container py-3">
  <!-- Breadcrumb & Back -->
  <div class="mb-3">
    <a href="index.php?action=companies" class="text-ink-muted small fw-semibold text-decoration-none">
      <i class="bi bi-arrow-left me-1"></i> Back to Company Hub
    </a>
  </div>

  <!-- Hero Header -->
  <div class="card border-0 shadow-sm surface-card p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar-icon bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center p-3" style="width: 54px; height: 54px;">
          <i class="bi bi-<?= htmlspecialchars($company['logo_icon'] ?: 'building') ?> fs-3"></i>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
            <h1 class="h3 fw-bold text-ink mb-0"><?= htmlspecialchars($company['name']) ?></h1>
            <span class="badge border <?= $tierBadge ?>"><?= $tierLabel ?></span>
          </div>
          <p class="text-ink-muted small mb-0 fw-semibold"><?= htmlspecialchars($company['category']) ?></p>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="index.php?action=practice&company=<?= urlencode($company['name']) ?>" class="btn btn-primary fw-semibold">
          <i class="bi bi-play-circle-fill me-1"></i> Practice <?= (int)$company['published_questions_count'] ?> Questions
        </a>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Left Column: Rounds & Structure -->
    <div class="col-12 col-lg-7">
      <!-- Reported Pattern Disclaimer & Verification Date -->
      <div class="alert alert-info py-2.5 px-3 small border-0 shadow-sm d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-4" style="background-color: var(--surface-bg); color: var(--surface-text); border-left: 4px solid var(--accent) !important; border: 1px solid var(--border);">
        <div>
          <i class="bi bi-info-circle-fill text-primary me-1"></i>
          <strong>Reported pattern, please verify:</strong> Campus recruitment patterns, round structures, and syllabi reflect aggregated student reports and do not claim official exam specifications.
        </div>
        <span class="badge bg-body-tertiary text-ink-muted text-nowrap align-self-start align-self-sm-center border">
          <i class="bi bi-calendar-check me-1"></i> Checked: <?= htmlspecialchars($company['last_checked'] ?? '2026-10-18') ?>
        </span>
      </div>

      <!-- Hiring Process & Rounds -->
      <div class="card border-0 shadow-sm surface-card p-4 mb-4">
        <h2 class="h5 fw-bold text-ink mb-3"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Selection Process &amp; Rounds</h2>
        <div class="timeline-stepper">
          <?php foreach ($rounds as $idx => $round): ?>
          <div class="d-flex gap-3 mb-3 position-relative">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.85rem;">
              <?= $idx + 1 ?>
            </div>
            <div class="pt-1 flex-grow-1">
              <h3 class="h6 fw-bold text-ink mb-1"><?= htmlspecialchars($round) ?></h3>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Test Pattern Breakdown -->
      <div class="card border-0 shadow-sm surface-card p-4 mb-4">
        <h2 class="h5 fw-bold text-ink mb-3"><i class="bi bi-clock-history text-warning me-2"></i>Assessment Pattern &amp; Structure</h2>
        <p class="text-ink mb-0" style="line-height: 1.6;">
          <?= nl2br(htmlspecialchars($company['test_pattern'])) ?>
        </p>
      </div>
    </div>

    <!-- Right Column: Syllabus & Key Focus Areas -->
    <div class="col-12 col-lg-5">
      <div class="card border-0 shadow-sm surface-card p-4 mb-4">
        <h2 class="h5 fw-bold text-ink mb-3"><i class="bi bi-journal-bookmark-fill text-success me-2"></i>Syllabus &amp; Focus Topics</h2>
        <div class="p-3 rounded bg-body-tertiary mb-3">
          <p class="small text-ink mb-0" style="line-height: 1.6;">
            <?= htmlspecialchars($company['syllabus']) ?>
          </p>
        </div>
        <p class="text-ink-muted small mb-0">
          <i class="bi bi-info-circle me-1"></i> <em>Reported pattern from candidate feedback and past recruitment drives.</em>
        </p>
      </div>

      <div class="card border-0 shadow-sm surface-card p-4 text-center">
        <h3 class="h6 fw-bold text-ink mb-2">Ready to test your skills?</h3>
        <p class="text-ink-muted small mb-3">Solve curated practice questions matching <?= htmlspecialchars($company['name']) ?>'s reported evaluation style.</p>
        <a href="index.php?action=practice&company=<?= urlencode($company['name']) ?>" class="btn btn-primary w-100 fw-semibold">

          Start Practicing Now
        </a>
      </div>
    </div>
  </div>
</div>
