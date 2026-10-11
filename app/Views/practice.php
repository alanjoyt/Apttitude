<?php
$isQuizSession = isset($_GET['session']) || isset($_GET['subtopic_id']) || (isset($_GET['mode']) && $_GET['mode'] === 'quiz');

if (!$isQuizSession):
    // --------------------------------------------------------------------------
    // PAGE 2: SYLLABUS BOARD & PRACTICE HOME (Exam Hall Timetable)
    // --------------------------------------------------------------------------
    $db = Database::getConnection();
    $userId = $_SESSION['user_id'] ?? null;

    $topics = $db->query("SELECT * FROM topics ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $subtopics = $db->query("SELECT s.*, t.name as topic_name, t.code as topic_code FROM subtopics s JOIN topics t ON s.topic_id = t.id ORDER BY s.topic_id ASC, s.id ASC")->fetchAll(PDO::FETCH_ASSOC);

    $userProgressMap = [];
    $userData = [
        'streak' => 0,
        'coins' => 0,
        'xp' => 0,
        'tier' => 'Bronze',
        'streak_freezes' => 1
    ];
    $practicedDates = [];

    if ($userId) {
        // Fetch real subtopic progress
        $stmt = $db->prepare("SELECT * FROM user_progress WHERE user_id = ?");
        $stmt->execute([$userId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $userProgressMap[$row['subtopic_id']] = $row;
        }

        // Fetch real user streak & stats from users table
        $uStmt = $db->prepare("SELECT current_streak, coins, total_xp, tier, streak_freezes FROM users WHERE id = ?");
        $uStmt->execute([$userId]);
        $uRow = $uStmt->fetch(PDO::FETCH_ASSOC);
        if ($uRow) {
            $userData = array_merge($userData, $uRow);
        }

        // Fetch real 7-day attempt history from attempts table for current week
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        $cStmt = $db->prepare("
            SELECT DISTINCT DATE(attempted_at) 
            FROM attempts 
            WHERE user_id = ? AND DATE(attempted_at) BETWEEN ? AND ?
        ");
        $cStmt->execute([$userId, $startOfWeek, $endOfWeek]);
        $practicedDates = $cStmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Determine target subtopic for "Continue where you left off" card
    $continueSubtopic = null;
    if (!empty($userProgressMap)) {
        foreach ($subtopics as $sub) {
            $p = $userProgressMap[$sub['id']] ?? null;
            if ($p && (int)$p['current_mastery_level'] < 5) {
                $continueSubtopic = $sub;
                break;
            }
        }
    }
    if (!$continueSubtopic && !empty($subtopics)) {
        $continueSubtopic = $subtopics[0];
    }
    $continueProgress = $continueSubtopic ? ($userProgressMap[$continueSubtopic['id']] ?? null) : null;
    $continueMastery = $continueProgress ? (int)$continueProgress['current_mastery_level'] : 1;
    $continueAttempted = $continueProgress ? (int)$continueProgress['total_attempted'] : 0;
    $continueCorrect = $continueProgress ? (int)$continueProgress['total_correct'] : 0;
    $continueAcc = $continueAttempted > 0 ? round(($continueCorrect / $continueAttempted) * 100) : null;

    // Separate topics:
    // Masonry (3-col desktop): Quantitative (1), Data Interpretation (4), Verbal (3), Company Placement PYQs (6), Logical (2)
    // Full-width card below masonry: Technical Assessment (5)
    $masonryOrder = [1, 4, 3, 6, 2];
    $masonryTopics = [];
    $techTopic = null;
    foreach ($topics as $t) {
        if ((int)$t['id'] === 5) {
            $techTopic = $t;
        }
    }
    foreach ($masonryOrder as $targetId) {
        foreach ($topics as $t) {
            if ((int)$t['id'] === $targetId) {
                $masonryTopics[] = $t;
                break;
            }
        }
    }
?>

<div class="syllabus-board-container" id="syllabusBoardContainer">
  <!-- Visible Exam Hall Page Title -->
  <div class="syllabus-page-header mb-3">
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
      <div>
        <h1 class="font-serif fw-bold text-ink mb-1 fs-3">Practice Syllabus</h1>
        <p class="text-ink-muted small mb-0">Master topic-wise aptitude modules with adaptive exam hall practice.</p>
      </div>
      <div class="d-none d-md-flex align-items-center gap-2">
        <span class="badge-stamp-sm"><svg class="exam-icon me-1" style="width:12px;height:12px;"><use href="#icon-award"></use></svg> EXAM HALL PREP</span>
      </div>
    </div>
  </div>

  <!-- Slim Guest Practice Notice (Readable in both themes, 1 slim line) -->
  <?php if (!$userId): ?>
    <div class="syllabus-guest-banner" role="status">
      <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1" style="min-width: 0; max-width: 100%;">
        <span class="badge-stamp-sm flex-shrink-0">TRIAL</span>
        <span class="small fw-semibold text-truncate" style="min-width: 0; max-width: 100%;">Guest Practice &bull; Free placement questions</span>
      </div>
      <a href="index.php?action=register" class="link-tertiary-exam small fw-bold text-nowrap flex-shrink-0">Sign Up &rarr;</a>
    </div>
  <?php endif; ?>

  <!-- Continue Where You Left Off Card (Top of page, compact on mobile, side-by-side on desktop) -->
  <div class="syllabus-continue-card">
    <div class="continue-target-side d-flex flex-column justify-content-between gap-3">
      <div>
        <span class="stamp-badge py-0.5 px-2.5 mb-1.5 d-inline-block fs-8">
          <svg class="exam-icon me-1" style="width:12px;height:12px;"><use href="#icon-bookmark"></use></svg> ACTIVE SYLLABUS TARGET
        </span>
        <h2 class="font-serif fw-bold text-ink mb-1 fs-4 text-truncate">
          <?= htmlspecialchars($continueSubtopic['name'] ?? 'Speed, Distance & Time') ?>
        </h2>
        <div class="text-ink-muted small d-flex align-items-center gap-2 flex-wrap mb-2">
          <span><?= htmlspecialchars($continueSubtopic['topic_name'] ?? 'Quantitative Aptitude') ?></span>
          <span>&bull;</span>
          <span>Mastery Level <?= $continueMastery ?>/5</span>
          <?php if ($continueAcc !== null): ?>
            <span>&bull;</span>
            <span class="topic-accuracy-mono text-ink fw-bold"><?= $continueAcc ?>% ACC</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Single Primary Button on the Screen -->
      <div>
        <a href="index.php?action=practice&subtopic_id=<?= $continueSubtopic['id'] ?? 1 ?>&session=1" class="btn btn-primary-gamified px-4 py-2.5 fw-bold d-inline-flex align-items-center" id="continuePracticeBtn">
          Continue Practice <svg class="exam-icon ms-1" style="width:14px;height:14px;"><use href="#icon-arrow-right"></use></svg>
        </a>
      </div>
    </div>

    <!-- 7-Day Paper Calendar Strip (Stationery calendar, real user attempts or guest prompt) -->
    <div class="syllabus-calendar-strip">
      <div class="d-flex align-items-center justify-content-between gap-2 mb-2 w-100">
        <span class="fw-bold small text-ink"><svg class="exam-icon me-1" style="width:14px;height:14px;"><use href="#icon-clock"></use></svg> 7-Day Practice Calendar</span>
        <?php if ($userId): ?>
          <span class="badge badge-stamp-sm" style="background-color: var(--surface-bg); color: var(--surface-text); border: 1px solid var(--border-strong);">
            <svg class="exam-icon text-danger me-1" style="width:12px;height:12px;"><use href="#icon-flame"></use></svg><?= $userData['streak'] ?>d Streak
          </span>
        <?php else: ?>
          <span class="badge badge-stamp-sm" style="background-color: var(--surface-bg); color: var(--ink-muted); border: 1px solid var(--border-strong);">
            Guest View
          </span>
        <?php endif; ?>
      </div>

      <div class="calendar-days-row justify-content-between w-100">
        <?php 
          $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          $todayDate = date('Y-m-d');
          foreach ($days as $idx => $dName):
            $dayDate = date('Y-m-d', strtotime("monday this week +$idx days"));
            $isActive = $userId && in_array($dayDate, $practicedDates);
            $isToday = ($dayDate === $todayDate);
        ?>
          <div class="calendar-day-cell">
            <span class="text-ink-muted"><?= $dName ?></span>
            <span class="calendar-day-bubble <?= $isActive ? 'active' : ($isToday ? 'today' : 'pending') ?>" title="<?= $dName ?> (<?= $dayDate ?>): <?= $isActive ? 'Practiced' : ($isToday ? 'Today' : 'Pending') ?>">
              <?php if ($isActive): ?>
                <svg class="exam-icon" style="width:10px;height:10px;stroke-width:3;"><use href="#icon-check"></use></svg>
              <?php elseif ($isToday): ?>
                ●
              <?php else: ?>
                ○
              <?php endif; ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (!$userId): ?>
        <div class="w-100 mt-2 small text-ink-muted" style="font-size:0.75rem;">
          <a href="index.php?action=register" class="link-tertiary-exam fw-bold">Sign up</a> to log your daily questions and streak on this 7-day calendar.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Compact Filter & Topic Selection Controls Bar -->
  <div class="syllabus-filter-bar">
    <!-- Single Continuous Live Search Input Box (No double borders) -->
    <div class="syllabus-search-box flex-grow-1" style="min-width: 220px; max-width: 480px;">
      <svg class="exam-icon text-ink-muted flex-shrink-0" style="width:16px;height:16px;"><use href="#icon-search"></use></svg>
      <input type="text" id="syllabusSearchInput" placeholder="Filter topics (e.g. Profit, Blood Relations, C++)..." aria-label="Filter syllabus topics">
    </div>

    <!-- Controls: Difficulty Mode Dropdown (Secondary Button) + Topic Tree Link -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div class="dropdown">
        <button class="btn btn-outline-gamified btn-sm dropdown-toggle py-2 px-3 fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="syllabusDiffDropdownBtn">
          <svg class="exam-icon me-1" style="width:14px;height:14px;"><use href="#icon-sliders"></use></svg> Mode: <span id="syllabusDiffText">Adaptive</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow p-1 fs-7">
          <li><a class="dropdown-item py-1.5" href="#" onclick="setSyllabusDifficulty('adaptive')">Adaptive Scaling</a></li>
          <li><a class="dropdown-item py-1.5 text-danger fw-bold" href="#" onclick="setSyllabusDifficulty('hard')">Challenger (Hard)</a></li>
          <li><a class="dropdown-item py-1.5 text-primary" href="#" onclick="setSyllabusDifficulty('medium')">Standard (Medium)</a></li>
        </ul>
      </div>

      <a href="index.php?action=skill_tree" class="link-tertiary-exam small fw-bold d-none d-sm-inline-flex align-items-center gap-1">
        <svg class="exam-icon" style="width:14px;height:14px;"><use href="#icon-diagram-3"></use></svg> Node Map
      </a>
    </div>
  </div>

  <!-- Syllabus Board Masonry Grid (3 Col Desktop, 2 Col Tablet, 1 Col Mobile Accordion) -->
  <?php $continueTopicId = $continueSubtopic ? (int)$continueSubtopic['topic_id'] : 1; ?>
  <div class="syllabus-grid" id="syllabusTopicsGrid">
    <?php foreach ($masonryTopics as $topic): 
      $groupSubtopics = array_values(array_filter($subtopics, fn($s) => $s['topic_id'] == $topic['id']));
      if (empty($groupSubtopics)) continue;
      $subCount = count($groupSubtopics);
      $topicLabel = ($subCount === 1) ? '1 topic' : "$subCount topics";
      $topicBadgeLabel = ($subCount === 1) ? '1 Topic' : "$subCount Topics";

      $masteredCount = 0;
      foreach ($groupSubtopics as $s) {
          if (isset($userProgressMap[$s['id']]) && (int)$userProgressMap[$s['id']]['current_mastery_level'] >= 5) {
              $masteredCount++;
          }
      }
      $isDefaultOpen = ((int)$topic['id'] === $continueTopicId);
    ?>
      <div class="syllabus-group-card <?= $isDefaultOpen ? 'is-open' : '' ?>" id="syllabusGroupCard_<?= $topic['id'] ?>" data-topic-code="<?= htmlspecialchars($topic['code']) ?>" data-topic-id="<?= $topic['id'] ?>">
        <!-- Stamped Heading & Accessible Mobile Accordion Button -->
        <div class="syllabus-group-header p-0">
          <h2 class="syllabus-group-header-wrap m-0 p-0 w-100">
            <button type="button" class="syllabus-group-header-btn" id="syllabusHeaderBtn_<?= $topic['id'] ?>" aria-expanded="<?= $isDefaultOpen ? 'true' : 'false' ?>" aria-controls="syllabusPanel_<?= $topic['id'] ?>" onclick="toggleSyllabusGroup(<?= $topic['id'] ?>)">
              <span class="syllabus-header-main">
                <span class="syllabus-group-title text-ink">
                  <i class="bi <?= $topic['icon'] ?: 'bi-journal-bookmark' ?>" style="color: var(--marker-orange);"></i>
                  <span><?= htmlspecialchars($topic['name']) ?></span>
                </span>
                <span class="syllabus-mobile-summary text-ink-muted">
                  <?= $topicLabel ?> &middot; <?= $userId ? "$masteredCount/$subCount mastered" : "0 mastered" ?>
                </span>
              </span>
              <span class="syllabus-header-end">
                <span class="badge badge-stamp-sm syllabus-desktop-badge">
                  <?= $topicBadgeLabel ?>
                </span>
                <svg class="exam-icon syllabus-chevron" style="width:16px;height:16px;" aria-hidden="true"><use href="#icon-chevron-down"></use></svg>
              </span>
            </button>
          </h2>
        </div>

        <!-- Ruled Topic Rows (CSS Grid Accordion Collapse) -->
        <div class="syllabus-accordion-collapse" id="syllabusPanel_<?= $topic['id'] ?>" role="region" aria-labelledby="syllabusHeaderBtn_<?= $topic['id'] ?>">
          <div class="syllabus-accordion-inner">
            <div class="syllabus-topics-list">
              <?php 
                $subIdx = 0;
                foreach ($groupSubtopics as $sub): 
                  $subIdx++;
                  $prog = $userProgressMap[$sub['id']] ?? null;
                  $mastery = $prog ? (int)$prog['current_mastery_level'] : 1;
                  $attempted = $prog ? (int)$prog['total_attempted'] : 0;
                  $correct = $prog ? (int)$prog['total_correct'] : 0;
                  $acc = $attempted > 0 ? round(($correct / $attempted) * 100) : null;
                  
                  if ($mastery >= 5) {
                      $statusClass = 'badge-status-mastered';
                      $statusIcon = '#icon-shield-check';
                      $statusText = 'Mastered';
                  } elseif ($attempted > 0) {
                      $statusClass = 'badge-status-progress';
                      $statusIcon = '#icon-hourglass';
                      $statusText = 'Lvl ' . $mastery;
                  } else {
                      $statusClass = 'badge-status-ready';
                      $statusIcon = '#icon-play-circle';
                      $statusText = 'Ready';
                  }
              ?>
                <a href="index.php?action=practice&subtopic_id=<?= $sub['id'] ?>&session=1" class="syllabus-topic-row" data-topic-name="<?= strtolower(htmlspecialchars($sub['name'])) ?>" aria-label="Practice <?= htmlspecialchars($sub['name']) ?>, <?= $statusText ?>" style="--row-idx: <?= $subIdx ?>;">
                  <div class="topic-row-info">
                    <span class="topic-row-name text-ink fw-bold d-block">
                      <?= htmlspecialchars($sub['name']) ?>
                    </span>
                    <div class="topic-row-meta">
                      <span class="badge-status-tag <?= $statusClass ?>">
                        <svg class="exam-icon" style="width:11px;height:11px;"><use href="<?= $statusIcon ?>"></use></svg> <?= $statusText ?>
                      </span>
                    </div>
                  </div>

                  <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <div class="topic-omr-progress" title="Mastery Level <?= $mastery ?>/5" aria-hidden="true">
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="omr-level-bubble <?= $i <= $mastery ? 'filled' : '' ?>"></span>
                      <?php endfor; ?>
                    </div>

                    <span class="topic-accuracy-mono">
                      <?= $acc !== null ? $acc . '%' : '--%' ?>
                    </span>

                    <i class="bi bi-chevron-right text-ink-muted small" aria-hidden="true"></i>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Full-Width Technical Assessment Card below Masonry (Accordion Item on Mobile) -->
  <?php if ($techTopic): 
    $techSubtopics = array_values(array_filter($subtopics, fn($s) => $s['topic_id'] == $techTopic['id']));
    if (!empty($techSubtopics)):
      $techCount = count($techSubtopics);
      $techLabel = ($techCount === 1) ? '1 topic' : "$techCount topics";
      $techBadgeLabel = "$techCount Programming Languages";
      $techMasteredCount = 0;
      foreach ($techSubtopics as $s) {
          if (isset($userProgressMap[$s['id']]) && (int)$userProgressMap[$s['id']]['current_mastery_level'] >= 5) {
              $techMasteredCount++;
          }
      }
      $isTechOpen = ((int)$techTopic['id'] === $continueTopicId);
  ?>
    <div class="syllabus-tech-fullwidth syllabus-group-card <?= $isTechOpen ? 'is-open' : '' ?>" id="syllabusGroupCard_<?= $techTopic['id'] ?>" data-topic-code="<?= htmlspecialchars($techTopic['code']) ?>" data-topic-id="<?= $techTopic['id'] ?>">
      <div class="syllabus-group-header p-0">
        <h2 class="syllabus-group-header-wrap m-0 p-0 w-100">
          <button type="button" class="syllabus-group-header-btn" id="syllabusHeaderBtn_<?= $techTopic['id'] ?>" aria-expanded="<?= $isTechOpen ? 'true' : 'false' ?>" aria-controls="syllabusPanel_<?= $techTopic['id'] ?>" onclick="toggleSyllabusGroup(<?= $techTopic['id'] ?>)">
            <span class="syllabus-header-main">
              <span class="syllabus-group-title text-ink">
                <svg class="exam-icon me-1 text-marker" style="width:16px;height:16px;"><use href="#icon-terminal"></use></svg>
                <span><?= htmlspecialchars($techTopic['name']) ?></span>
              </span>
              <span class="syllabus-mobile-summary text-ink-muted">
                <?= $techLabel ?> &middot; <?= $userId ? "$techMasteredCount/$techCount mastered" : "0 mastered" ?>
              </span>
            </span>
            <span class="syllabus-header-end">
              <span class="badge badge-stamp-sm syllabus-desktop-badge">
                <?= $techBadgeLabel ?>
              </span>
              <svg class="exam-icon syllabus-chevron" style="width:16px;height:16px;" aria-hidden="true"><use href="#icon-chevron-down"></use></svg>
            </span>
          </button>
        </h2>
      </div>

      <div class="syllabus-accordion-collapse" id="syllabusPanel_<?= $techTopic['id'] ?>" role="region" aria-labelledby="syllabusHeaderBtn_<?= $techTopic['id'] ?>">
        <div class="syllabus-accordion-inner">
          <div class="syllabus-topics-inner-grid">
            <?php 
              $techIdx = 0;
              foreach ($techSubtopics as $sub): 
                $techIdx++;
                $prog = $userProgressMap[$sub['id']] ?? null;
                $mastery = $prog ? (int)$prog['current_mastery_level'] : 1;
                $attempted = $prog ? (int)$prog['total_attempted'] : 0;
                $correct = $prog ? (int)$prog['total_correct'] : 0;
                $acc = $attempted > 0 ? round(($correct / $attempted) * 100) : null;
                
                if ($mastery >= 5) {
                    $statusClass = 'badge-status-mastered';
                    $statusIcon = '#icon-shield-check';
                    $statusText = 'Mastered';
                } elseif ($attempted > 0) {
                    $statusClass = 'badge-status-progress';
                    $statusIcon = '#icon-hourglass';
                    $statusText = 'Lvl ' . $mastery;
                } else {
                    $statusClass = 'badge-status-ready';
                    $statusIcon = '#icon-play-circle';
                    $statusText = 'Ready';
                }
            ?>
              <a href="index.php?action=practice&subtopic_id=<?= $sub['id'] ?>&session=1" class="syllabus-topic-row" data-topic-name="<?= strtolower(htmlspecialchars($sub['name'])) ?>" aria-label="Practice <?= htmlspecialchars($sub['name']) ?>, <?= $statusText ?>" style="--row-idx: <?= $techIdx ?>;">
                <div class="topic-row-info">
                  <span class="topic-row-name text-ink fw-bold d-block">
                    <?= htmlspecialchars($sub['name']) ?>
                  </span>
                  <div class="topic-row-meta">
                    <span class="badge-status-tag <?= $statusClass ?>">
                      <svg class="exam-icon" style="width:11px;height:11px;"><use href="<?= $statusIcon ?>"></use></svg> <?= $statusText ?>
                    </span>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                  <div class="topic-omr-progress" title="Mastery Level <?= $mastery ?>/5" aria-hidden="true">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <span class="omr-level-bubble <?= $i <= $mastery ? 'filled' : '' ?>"></span>
                    <?php endfor; ?>
                  </div>

                  <span class="topic-accuracy-mono">
                    <?= $acc !== null ? $acc . '%' : '--%' ?>
                  </span>

                  <i class="bi bi-chevron-right text-ink-muted small" aria-hidden="true"></i>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; endif; ?>

</div>

<script>
// Syllabus Board Accordion, Topic Filter & Difficulty Mode
let preSearchOpenGroupId = null;

function isMobileView() {
    return window.innerWidth < 768;
}

function initSyllabusAccordion() {
    const container = document.getElementById('syllabusBoardContainer');
    if (!container) return;

    if (isMobileView()) {
        container.classList.add('syllabus-accordion-enabled');
        
        // Session memory: only use saved state if already visited in this session
        const savedGroup = sessionStorage.getItem('syllabus_accordion_group');
        const allCards = container.querySelectorAll('.syllabus-group-card');

        if (savedGroup !== null) {
            allCards.forEach(card => {
                const groupId = card.getAttribute('data-topic-id');
                const btn = card.querySelector('.syllabus-group-header-btn');
                const panel = card.querySelector('.syllabus-accordion-collapse');
                if (savedGroup !== 'none' && groupId === savedGroup) {
                    card.classList.add('is-open');
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                    if (panel) panel.inert = false;
                } else {
                    card.classList.remove('is-open');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                    if (panel) panel.inert = true;
                }
            });
        } else {
            // First load of session: respect server default (Continue group or Quant)
            let defaultFound = null;
            allCards.forEach(card => {
                if (card.classList.contains('is-open')) {
                    defaultFound = card.getAttribute('data-topic-id');
                }
            });
            // If none found (or on session clear), default to first group (Topic 1)
            if (!defaultFound && allCards.length > 0) {
                defaultFound = allCards[0].getAttribute('data-topic-id') || '1';
            }
            allCards.forEach(card => {
                const groupId = card.getAttribute('data-topic-id');
                const btn = card.querySelector('.syllabus-group-header-btn');
                const panel = card.querySelector('.syllabus-accordion-collapse');
                if (groupId === defaultFound) {
                    card.classList.add('is-open');
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                    if (panel) panel.inert = false;
                } else {
                    card.classList.remove('is-open');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                    if (panel) panel.inert = true;
                }
            });
            if (defaultFound) {
                sessionStorage.setItem('syllabus_accordion_group', defaultFound);
            }
        }

        // Attach keyboard navigation listeners (Enter and Space)
        allCards.forEach(card => {
            const groupId = card.getAttribute('data-topic-id');
            const btn = card.querySelector('.syllabus-group-header-btn');
            if (btn && !btn._hasA11yKeyHandler) {
                btn._hasA11yKeyHandler = true;
                btn.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ' || e.code === 'Space' || e.code === 'Enter') {
                        e.preventDefault();
                        toggleSyllabusGroup(groupId);
                    }
                });
            }
        });
    } else {
        // Desktop: remove accordion enabled class and clear inert from all panels
        container.classList.remove('syllabus-accordion-enabled');
        container.querySelectorAll('.syllabus-accordion-collapse').forEach(panel => {
            panel.inert = false;
        });
    }
}

function toggleSyllabusGroup(groupId) {
    if (!isMobileView()) return; // Never collapse on desktop

    const searchInput = document.getElementById('syllabusSearchInput');
    if (searchInput && searchInput.value.trim() !== '') {
        return; // Ignore manual toggles during active search
    }

    const container = document.getElementById('syllabusBoardContainer');
    const targetCard = document.getElementById('syllabusGroupCard_' + groupId);
    if (!targetCard || !container) return;

    const allCards = container.querySelectorAll('.syllabus-group-card');
    const isCurrentlyOpen = targetCard.classList.contains('is-open');

    // Close all groups (Single-open constraint)
    allCards.forEach(card => {
        card.classList.remove('is-open');
        const btn = card.querySelector('.syllabus-group-header-btn');
        const panel = card.querySelector('.syllabus-accordion-collapse');
        if (btn) btn.setAttribute('aria-expanded', 'false');
        if (panel) panel.inert = true;
    });

    if (!isCurrentlyOpen) {
        // Open the target card
        targetCard.classList.add('is-open');
        const btn = targetCard.querySelector('.syllabus-group-header-btn');
        const panel = targetCard.querySelector('.syllabus-accordion-collapse');
        if (btn) {
            btn.setAttribute('aria-expanded', 'true');
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        if (panel) panel.inert = false;
        sessionStorage.setItem('syllabus_accordion_group', groupId.toString());
    } else {
        // Tapping the open group toggles it closed (all collapsed state)
        sessionStorage.setItem('syllabus_accordion_group', 'none');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initSyllabusAccordion();

    window.addEventListener('resize', () => {
        initSyllabusAccordion();
    });

    const searchInput = document.getElementById('syllabusSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const container = document.getElementById('syllabusBoardContainer');
            if (!container) return;
            const cards = container.querySelectorAll('.syllabus-group-card');

            if (query) {
                // Save state before search if not already recorded
                if (preSearchOpenGroupId === null) {
                    const currentlyOpen = container.querySelector('.syllabus-group-card.is-open');
                    preSearchOpenGroupId = currentlyOpen ? currentlyOpen.getAttribute('data-topic-id') : 'none';
                }

                cards.forEach(card => {
                    let matchCount = 0;
                    card.querySelectorAll('.syllabus-topic-row').forEach(row => {
                        const name = row.getAttribute('data-topic-name') || '';
                        if (name.includes(query)) {
                            row.style.display = 'flex';
                            matchCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    const btn = card.querySelector('.syllabus-group-header-btn');
                    const panel = card.querySelector('.syllabus-accordion-collapse');

                    if (matchCount > 0) {
                        card.style.display = '';
                        card.classList.add('is-open');
                        if (btn) btn.setAttribute('aria-expanded', 'true');
                        if (panel) panel.inert = false;
                    } else {
                        card.style.display = 'none';
                        card.classList.remove('is-open');
                        if (btn) btn.setAttribute('aria-expanded', 'false');
                        if (panel) panel.inert = true;
                    }
                });
            } else {
                // Search cleared: restore all rows and restore previous open/closed state
                cards.forEach(card => {
                    card.style.display = '';
                    card.querySelectorAll('.syllabus-topic-row').forEach(row => {
                        row.style.display = 'flex';
                    });
                });

                if (isMobileView()) {
                    cards.forEach(card => {
                        const groupId = card.getAttribute('data-topic-id');
                        const btn = card.querySelector('.syllabus-group-header-btn');
                        const panel = card.querySelector('.syllabus-accordion-collapse');

                        if (preSearchOpenGroupId && preSearchOpenGroupId !== 'none' && groupId === preSearchOpenGroupId) {
                            card.classList.add('is-open');
                            if (btn) btn.setAttribute('aria-expanded', 'true');
                            if (panel) panel.inert = false;
                        } else {
                            card.classList.remove('is-open');
                            if (btn) btn.setAttribute('aria-expanded', 'false');
                            if (panel) panel.inert = true;
                        }
                    });
                }
                preSearchOpenGroupId = null;
            }
        });
    }

    // Load saved difficulty mode
    const savedMode = localStorage.getItem('practice_diff_mode');
    if (savedMode) {
        setSyllabusDifficulty(savedMode);
    }
});

function setSyllabusDifficulty(mode) {
    const textEl = document.getElementById('syllabusDiffText');
    if (textEl) {
        textEl.innerText = mode === 'hard' ? 'Challenger' : (mode.charAt(0).toUpperCase() + mode.slice(1));
    }
    localStorage.setItem('practice_diff_mode', mode);
    if (typeof PracticeEngine !== 'undefined') {
        PracticeEngine.difficultyMode = mode;
    }
}
</script>

<?php else: ?>
  <!-- --------------------------------------------------------------------------
       PAGE 3: QUESTION / QUIZ TEST-TAKING SCREEN
       -------------------------------------------------------------------------- -->
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">

      <!-- Guest Practice Warning Banner if not logged in (Collapsed 1-line strip, max 40px) -->
      <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="guest-strip-exam mb-3" role="status">
          <div class="d-flex align-items-center gap-1.5 min-w-0 flex-grow-1">
            <span class="badge-stamp-sm flex-shrink-0">TRIAL</span>
            <span class="small fw-semibold text-truncate">Guest trial &middot; Sign up to save progress</span>
          </div>
          <a href="index.php?action=register" class="link-tertiary-exam small fw-bold text-nowrap flex-shrink-0">Sign Up &rarr;</a>
        </div>
      <?php endif; ?>

      <!-- Desktop Breadcrumb (Hidden on Mobile where slim quiz bar sits at top) -->
      <div class="d-none d-md-flex justify-content-between align-items-center mb-2 px-1">
        <a href="index.php?action=practice" class="link-tertiary-exam text-decoration-none fs-7 d-inline-flex align-items-center gap-1">
          <i class="bi bi-arrow-left"></i>
          <span>Syllabus</span>
        </a>
        <div class="d-flex align-items-center gap-2">
          <a href="index.php?action=skill_tree" class="link-tertiary-exam text-decoration-none d-inline-flex align-items-center gap-1 fs-7">
            <i class="bi bi-diagram-3"></i>
            <span>Topics Tree</span>
          </a>
          <span class="small text-ink-muted d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
            <i class="bi bi-shield-check text-success"></i> Auto-saved
          </span>
        </div>
      </div>

      <!-- Main Gamified Question Card (Shared Quiz Shell Partial) -->
      <?php
      $quizContext = 'practice';
      include __DIR__ . '/components/quiz_shell.php';
      ?>

    </div>
  </div>

  <!-- Guest Practice Limit Modal -->
  <div class="modal fade" id="guestLimitModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-ink border rounded-4 p-2 text-center" style="background-color: var(--surface); border: 2px solid var(--border-strong) !important;">
        <div class="modal-body p-4">
          <div class="mb-3 text-accent">
            <svg class="exam-icon" style="width:56px;height:56px;"><use href="#icon-rocket"></use></svg>
          </div>
          <h4 class="font-serif fw-bold mb-2" style="color: var(--marker-orange);">5 Free Trial Questions Completed!</h4>
          <p class="text-ink-muted small mb-4">
            Great job! Create a free account now to save your XP, Speed Coins & join the Weekly Leaderboard.
          </p>
          <div class="d-flex flex-column gap-2">
            <a href="index.php?action=register" class="btn btn-primary-gamified py-2 fw-bold fs-6">
              Create Free Account
            </a>
            <a href="index.php?action=login" class="btn btn-outline-gamified py-2">
              Log In Existing Account
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
      // Parse subtopic_id from URL if present
      const urlParams = new URLSearchParams(window.location.search);
      const subtopicId = urlParams.get('subtopic_id');
      const diffMode = localStorage.getItem('practice_diff_mode') || 'adaptive';
      
      PracticeEngine.difficultyMode = diffMode;
      PracticeEngine.init(subtopicId);
  });
  </script>
<?php endif; ?>
