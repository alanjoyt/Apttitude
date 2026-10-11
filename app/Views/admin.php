<div class="row justify-content-center py-3">
  <div class="col-lg-10">

    <div class="card card-gamified p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-shield-lock-fill" style="color: var(--accent);"></i> Admin Content CMS & Moderation
        </h4>
        <span class="stamp-badge py-1 px-3">Content Ops Portal</span>
      </div>
      <p class="text-ink-muted small mb-2">Manage question banks, bulk import CSV files, and review user content reports.</p>
      <div class="d-inline-flex align-items-center gap-3 p-2 px-3 rounded-pill border small" style="background-color: var(--bg); border-color: var(--border) !important;">
        <span><i class="bi bi-person-fill me-1" style="color: var(--accent);"></i> Admin Email: <strong class="text-ink">admin@apttitudee.com</strong></span>
        <span><i class="bi bi-key-fill me-1" style="color: var(--highlight);"></i> Password: <strong class="text-ink">password123</strong></span>
      </div>
    </div>

    <!-- Developer Mode Control Card -->
    <div class="card card-gamified p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-code-slash fs-4" style="color: var(--accent);"></i> Developer Mode Settings
        </h5>
        <?php $isDevMode = $isDevMode ?? false; ?>
        <span id="devModeBadge" class="badge fs-6 px-3 py-2 rounded-pill" style="background-color: <?= $isDevMode ? 'var(--success)' : 'var(--danger)' ?>; color: white;">
          <i class="bi bi-shield-check me-1"></i> <?= $isDevMode ? 'DEV MODE: ON (Multi-Account Testing)' : 'DEV MODE: OFF (Device Lock Prohibited)' ?>
        </span>
      </div>
      <p class="text-ink-muted small mb-3">
        Control multi-account security locks per device. When turned <strong>OFF</strong>, strict Device Lock prevents multiple accounts from 1 device. When turned <strong>ON</strong>, multi-account testing is enabled for development.
      </p>

      <div class="form-check form-switch fs-5 d-flex align-items-center gap-3">
        <input class="form-check-input" type="checkbox" role="switch" id="devModeSwitch" <?= $isDevMode ? 'checked' : '' ?> onchange="toggleDeveloperMode(this.checked)">
        <label class="form-check-label text-ink fw-semibold small" for="devModeSwitch">
          Enable Multi-Account Testing (Developer Mode)
        </label>
      </div>

      <div id="devModeStatusAlert" class="alert d-none mt-3 mb-0 py-2 small" role="alert"></div>
    </div>

    <!-- Pending Reports Queue Card -->
    <div class="card card-gamified p-4 mb-4">
      <h5 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill" style="color: var(--highlight);"></i> User Question Reports Queue (<?= count($pendingReports) ?>)
      </h5>

      <?php if (empty($pendingReports)): ?>
        <div class="alert border text-ink-muted mb-0 d-flex align-items-center gap-2" style="background-color: var(--bg); border-color: var(--border) !important;">
          <svg class="exam-icon text-success" style="width:16px;height:16px;"><use href="#icon-check"></use></svg> No pending question reports in moderation queue!
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead>
              <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
                <th class="font-serif text-ink">ID</th>
                <th class="font-serif text-ink">Question Excerpt</th>
                <th class="font-serif text-ink">Reporter</th>
                <th class="font-serif text-ink">Reason</th>
                <th class="font-serif text-ink">Comment</th>
                <th class="font-serif text-ink">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pendingReports as $report): ?>
                <tr>
                  <td class="text-ink">#<?= $report['id'] ?></td>
                  <td class="text-truncate text-ink" style="max-width: 200px;"><?= htmlspecialchars($report['question_text']) ?></td>
                  <td class="text-ink"><?= htmlspecialchars($report['reporter_name'] ?? 'Guest') ?></td>
                  <td><span class="badge" style="background-color: var(--danger); color: white;"><?= $report['reason'] ?></span></td>
                  <td class="text-ink-muted"><?= htmlspecialchars($report['comment'] ?? 'None') ?></td>
                  <td>
                    <button class="btn btn-sm btn-primary-gamified px-3" onclick="alert('Report resolved!')">Resolve</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Bulk CSV Import Card -->
    <div class="card card-gamified p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-spreadsheet-fill" style="color: var(--success);"></i> Bulk Question CSV Importer & Company PYQ Pipeline
        </h5>
        <a href="bulk_questions_sample.csv" download class="btn btn-outline-gamified btn-sm px-3">
          <i class="bi bi-download me-1"></i> Sample CSV Template
        </a>
      </div>
      <p class="text-ink-muted small">Upload CSV files matching schema `(topic_code, subtopic_code, difficulty, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, source_tag)`</p>
      
      <form id="csvImportForm" onsubmit="uploadBulkCSV(event)">
        <div class="mb-3">
          <input type="file" id="csvFileInput" name="csv_file" class="form-control" accept=".csv" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
        <button type="submit" id="csvSubmitBtn" class="btn btn-primary-gamified px-4 fw-bold">
          <i class="bi bi-cloud-upload-fill me-1"></i> Upload & Import CSV Questions
        </button>
      </form>
      <div id="csvImportAlert" class="alert d-none mt-3 mb-0 py-2 small" role="alert"></div>
    </div>


  </div>
</div>

<script>
async function toggleDeveloperMode(enabled) {
  const badge = document.getElementById('devModeBadge');
  const alertBox = document.getElementById('devModeStatusAlert');
  
  try {
    const res = await fetch('index.php?action=api_toggle_dev_mode', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ enabled: enabled })
    });
    const data = await res.json();
    
    if (data.status === 'success') {
      if (data.developer_mode) {
        badge.className = 'badge bg-success fs-6 px-3 py-2 rounded-pill';
        badge.innerHTML = '<i class="bi bi-shield-check me-1"></i> DEV MODE: ON (Multi-Account Testing)';
        alertBox.className = 'alert alert-success mt-3 mb-0 py-2 small';
      } else {
        badge.className = 'badge bg-danger fs-6 px-3 py-2 rounded-pill';
        badge.innerHTML = '<i class="bi bi-shield-x me-1"></i> DEV MODE: OFF (Device Lock Prohibited)';
        alertBox.className = 'alert alert-warning mt-3 mb-0 py-2 small';
      }
      alertBox.textContent = data.message;
      alertBox.classList.remove('d-none');
    } else {
      alertBox.className = 'alert alert-danger mt-3 mb-0 py-2 small';
      alertBox.textContent = data.message || 'Failed to update Developer Mode';
      alertBox.classList.remove('d-none');
    }
  } catch (err) {
    alertBox.className = 'alert alert-danger mt-3 mb-0 py-2 small';
    alertBox.textContent = 'Server network error toggling developer mode.';
    alertBox.classList.remove('d-none');
  }
}

async function uploadBulkCSV(e) {
  e.preventDefault();
  const fileInput = document.getElementById('csvFileInput');
  const alertBox = document.getElementById('csvImportAlert');
  const submitBtn = document.getElementById('csvSubmitBtn');

  if (!fileInput.files.length) return;

  const formData = new FormData();
  formData.append('csv_file', fileInput.files[0]);

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Importing Questions...';

  try {
    const res = await fetch('index.php?action=api_import_csv', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (data.status === 'success') {
      alertBox.className = 'alert alert-success mt-3 mb-0 py-2 small';
      alertBox.innerHTML = `<strong>Success!</strong> ${data.message}`;
      alertBox.classList.remove('d-none');
      fileInput.value = '';
    } else {
      alertBox.className = 'alert alert-danger mt-3 mb-0 py-2 small';
      alertBox.textContent = data.message || 'Error uploading CSV file.';
      alertBox.classList.remove('d-none');
    }
  } catch (err) {
    alertBox.className = 'alert alert-danger mt-3 mb-0 py-2 small';
    alertBox.textContent = 'Server network error uploading CSV file.';
    alertBox.classList.remove('d-none');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-cloud-upload-fill me-1"></i> Upload & Import CSV Questions';
  }
}
</script>


