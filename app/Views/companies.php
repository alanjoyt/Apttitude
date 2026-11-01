<?php
// Company Recruitment Hub
$companies = CompanyController::getAllCompanies();
$totalVerifiedQuestions = array_sum(array_column($companies, 'published_questions_count'));
?>

<div class="container py-3">
  <!-- Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-2 mb-4">
    <div class="min-w-0 flex-grow-1">
      <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
        <h1 class="font-serif fw-bold text-ink mb-0" style="font-size: clamp(1.4rem, 4vw, 1.85rem);">Company Placement Hub</h1>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">15 Top Recruiters</span>
      </div>
      <p class="text-ink-muted small mb-0 fw-semibold">
        Over <?= number_format($totalVerifiedQuestions) ?> practice questions mapped to reported campus placement patterns
      </p>

    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
      <a href="index.php?action=mock" class="btn btn-sm btn-outline-secondary fw-semibold">
        <i class="bi bi-clock-history me-1"></i> Simulated Mocks
      </a>
      <a href="index.php?action=practice" class="btn btn-sm btn-primary fw-semibold">
        <i class="bi bi-play-fill me-1"></i> Quick Practice
      </a>
    </div>
  </div>

  <!-- Search & Filter Controls -->
  <div class="card p-3 mb-4 border-0 shadow-sm surface-card">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-6">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-transparent border-end-0 text-ink-muted"><i class="bi bi-search"></i></span>
          <input type="text" id="companySearchInput" class="form-control border-start-0" placeholder="Search company by name, syllabus, or pattern..." onkeyup="filterCompanies()">
        </div>
      </div>
      <div class="col-6 col-md-3">
        <select id="tierFilter" class="form-select form-select-sm" onchange="filterCompanies()" aria-label="Filter companies by difficulty tier">
          <option value="all">All Difficulty Tiers</option>
          <option value="standard">Standard Placement</option>
          <option value="elevated">Elevated / High Bar</option>
          <option value="premium">Premium / Big Tech</option>
        </select>
      </div>
      <div class="col-6 col-md-3 text-end text-ink-muted small fw-semibold" id="companyCountDisplay">
        Showing <?= count($companies) ?> Companies
      </div>
    </div>
  </div>

  <!-- Company Cards Grid -->
  <div class="row g-3" id="companyGrid">
    <?php foreach ($companies as $c): 
      $tierBadge = match($c['difficulty_tier']) {
        'premium' => 'bg-danger-subtle text-danger-emphasis border-danger-subtle',
        'elevated' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
        default => 'bg-primary-subtle text-primary border-primary-subtle'
      };
      $tierLabel = match($c['difficulty_tier']) {
        'premium' => 'Premium Tier',
        'elevated' => 'Elevated Bar',
        default => 'Standard'
      };
      $rounds = !empty($c['rounds_json']) ? json_decode($c['rounds_json'], true) : [];
      $roundCount = is_array($rounds) ? count($rounds) : 0;
    ?>
    <div class="col-12 col-md-6 col-lg-4 company-card-item" 
         data-name="<?= htmlspecialchars(strtolower($c['name'])) ?>"
         data-tier="<?= htmlspecialchars($c['difficulty_tier']) ?>"
         data-syllabus="<?= htmlspecialchars(strtolower($c['syllabus'] . ' ' . $c['test_pattern'])) ?>">
      <div class="card h-100 border surface-card shadow-sm hover-shadow transition-all d-flex flex-column">
        <div class="card-body p-3 d-flex flex-column flex-grow-1">
          <!-- Top Row: Icon, Name, Tier -->
          <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
            <div class="d-flex align-items-center gap-2 min-w-0">
              <div class="avatar-icon-sm bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center p-2" style="width: 36px; height: 36px;">
                <i class="bi bi-<?= htmlspecialchars($c['logo_icon'] ?: 'building') ?> fs-6"></i>
              </div>
              <div class="min-w-0">
                <h2 class="h6 fw-bold mb-0 text-truncate text-ink"><?= htmlspecialchars($c['name']) ?></h2>
                <span class="text-ink-muted small" style="font-size: 0.75rem;"><?= htmlspecialchars($c['category']) ?></span>
              </div>
            </div>
            <div class="d-flex flex-column align-items-end gap-1">
              <span class="badge border <?= $tierBadge ?> text-nowrap" style="font-size: 0.7rem;">
                <?= $tierLabel ?>
              </span>
              <span class="badge bg-light text-secondary border extra-small">Practice Pattern</span>
            </div>
          </div>

          <!-- Description -->
          <p class="text-ink-muted small mb-3 flex-grow-1 line-clamp-2" style="font-size: 0.825rem; line-height: 1.4;">
            <?= htmlspecialchars($c['description']) ?>
          </p>

          <!-- Pattern Pill & Stats -->
          <div class="p-2 rounded bg-body-tertiary mb-3 small" style="font-size: 0.775rem;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-ink-muted"><i class="bi bi-diagram-3 me-1"></i> Selection Process:</span>
              <span class="fw-semibold text-ink"><?= $roundCount ?> Rounds</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-ink-muted"><i class="bi bi-collection-play-fill text-primary me-1"></i> Published Questions:</span>
              <div class="d-flex align-items-center gap-1">
                <span class="fw-bold text-ink"><?= (int)$c['published_questions_count'] ?> Qs</span>
                <?php if ((int)$c['published_questions_count'] < 30): ?>
                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small" title="Pool has fewer than 30 questions; mock will cap at pool size">Limited Pool (&lt; 30)</span>
                <?php elseif ((int)$c['published_questions_count'] < 60): ?>
                  <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle extra-small" title="Pool has fewer than 60 questions; standard mock will cap at pool size">Limited Pool (&lt; 60)</span>
                <?php endif; ?>
              </div>
            </div>

          </div>

          <!-- Syllabus Focus Tags -->
          <div class="mb-3 text-truncate small text-ink-muted" style="font-size: 0.75rem;">
            <i class="bi bi-tags me-1"></i> <strong>Focus:</strong> <?= htmlspecialchars($c['syllabus']) ?>
          </div>

          <!-- Actions -->
          <div class="d-flex gap-2 mt-auto pt-2 border-top">
            <a href="index.php?action=company&slug=<?= urlencode($c['slug']) ?>" class="btn btn-sm btn-outline-secondary flex-grow-1 fw-semibold text-nowrap">
              Pattern Details
            </a>
            <a href="index.php?action=practice&company=<?= urlencode($c['name']) ?>" class="btn btn-sm btn-primary flex-grow-1 fw-semibold text-nowrap">
              Practice (<?= (int)$c['published_questions_count'] ?>)
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
function filterCompanies() {
  const query = document.getElementById('companySearchInput').value.toLowerCase().trim();
  const tier = document.getElementById('tierFilter').value;
  const items = document.querySelectorAll('.company-card-item');
  let visible = 0;

  items.forEach(el => {
    const name = el.getAttribute('data-name') || '';
    const elTier = el.getAttribute('data-tier') || '';
    const syllabus = el.getAttribute('data-syllabus') || '';

    const matchQuery = !query || name.includes(query) || syllabus.includes(query);
    const matchTier = tier === 'all' || elTier === tier;

    if (matchQuery && matchTier) {
      el.style.display = '';
      visible++;
    } else {
      el.style.display = 'none';
    }
  });

  const countDisplay = document.getElementById('companyCountDisplay');
  if (countDisplay) {
    countDisplay.textContent = `Showing ${visible} of ${items.length} Companies`;
  }
}
</script>
