<?php
$userId = $_SESSION['user_id'] ?? null;
$items = WalletService::getUnlockables($userId);
$userBalance = $userId ? WalletService::getUserBalance($userId) : 0;
?>

<div class="row justify-content-center py-2 py-md-3">
  <div class="col-12 col-xl-10">

    <!-- Store Header Card with Speed Coins Wallet Balance -->
    <div class="card-gamified card-ruled p-3 p-sm-4 mb-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="min-w-0" style="flex: 1 1 200px;">
          <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <span class="stamp-badge">EXAM STORE</span>
            <span class="stamp-badge-highlight">AUTHORIZED CURRENCY</span>
          </div>
          <h1 class="font-serif fw-bold text-ink mb-1 d-flex align-items-center gap-2 flex-wrap" style="font-size: clamp(1.25rem, 4vw, 1.75rem); overflow-wrap: anywhere;">
            <i class="bi bi-shop fs-3" style="color: var(--marker-orange);"></i> Speed Coins Unlock Ledger
          </h1>
          <p class="text-ink-muted mb-0" style="font-size: 1rem; overflow-wrap: anywhere;">
            Earn Speed Coins by answering timed aptitude questions accurately. Redeem tokens to unlock company mock test packs and career tools.
          </p>
        </div>

        <div>
          <div class="coin-token coin-token-lg shadow-sm" title="Your Available Speed Coins Balance">
            <span class="coin-icon"><svg class="exam-icon text-warning" style="width:18px;height:18px;"><use href="#icon-coin"></use></svg></span>
            <span id="storeUserCoinsDisplay"><?= number_format($userBalance) ?> Coins</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Guest State Banner (Not Logged In) -->
    <?php if (!$userId): ?>
      <div class="stamped-notice-exam mb-4" role="alert" style="background-color: var(--highlight-tint); border-color: var(--border-strong);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
              <span class="stamp-badge-danger">GUEST CANDIDATE</span>
              <strong class="font-serif text-ink fs-5">Student Ledger Required</strong>
            </div>
            <p class="mb-0 text-ink" style="font-size: 1rem;">
              You are currently viewing the store as an unregistered guest. Create an account or log in to accumulate Speed Coins from practice drills and unlock placement packs.
            </p>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="index.php?action=login" class="btn btn-outline-gamified fw-bold text-nowrap" style="min-height: 44px;">
              <i class="bi bi-box-arrow-in-right me-1"></i> Log In
            </a>
            <a href="index.php?action=register" class="btn btn-primary-gamified fw-bold text-nowrap" style="min-height: 44px;">
              <i class="bi bi-person-plus-fill me-1"></i> Register Free
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Premium Subscription Perks Banner -->
    <div class="stamped-notice-exam mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <span class="stamp-badge-premium">PREMIUM PASS</span>
            <strong class="font-serif text-ink fs-5">All-Access Company Mock Pass</strong>
          </div>
          <p class="text-ink-muted mb-0" style="font-size: 1rem;">
            Subscribers receive unlimited access to all company-curated placement mock packs (TCS NQT, Infosys, Wipro, Accenture) with 0 coin cost!
          </p>
        </div>
        <div class="flex-shrink-0">
          <button type="button" 
                  class="btn btn-primary-gamified fw-bold text-nowrap" 
                  style="min-height: 44px; padding: 0.65rem 1.4rem;" 
                  onclick="alert('Premium Pass launching in Phase 2! Free users can earn access today by completing daily practice streaks and earning Speed Coins.')"
                  aria-label="Learn more about Apttitudee Premium Plan">
            <i class="bi bi-star-fill text-warning me-1"></i> Learn More
          </button>
        </div>
      </div>
    </div>

    <!-- Store Catalog Items Responsive Grid (1 col <600px, 2 cols 600-1000px, 3 cols >1000px) -->
    <div class="store-grid-exam">
      <?php foreach ($items as $item): 
        $cost = (int)$item['coin_cost'];
        $isUnlocked = !empty($item['is_unlocked']);
        $hasEnough = ($userBalance >= $cost);
        $diff = max(0, $cost - $userBalance);
      ?>
        <div class="store-card-exam">
          <div>
            <!-- Card Header: Icon & Stamped Price Tag -->
            <div class="store-card-header">
              <div class="store-card-icon-box">
                <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
              </div>
              
              <?php if ($isUnlocked): ?>
                <span class="stamp-badge-success">
                  <i class="bi bi-check2-circle"></i> UNLOCKED
                </span>
              <?php else: ?>
                <div class="coin-token" title="Cost: <?= number_format($cost) ?> Speed Coins">
                  <span class="coin-icon"><svg class="exam-icon text-warning" style="width:14px;height:14px;"><use href="#icon-coin"></use></svg></span>
                  <span><?= number_format($cost) ?></span>
                </div>
              <?php endif; ?>
            </div>

            <!-- Title & Description (min 16px body font) -->
            <h3 class="store-card-title"><?= htmlspecialchars($item['title']) ?></h3>
            <p class="store-card-desc"><?= htmlspecialchars($item['description']) ?></p>
          </div>

          <!-- Card Action States -->
          <div class="store-card-footer">
            <?php if ($isUnlocked): ?>
              <!-- State 1: Unlocked -->
              <?php if ($item['item_type'] === 'company_mock'): ?>
                <a href="index.php?action=mock&pack=<?= urlencode($item['code']) ?>" 
                   class="btn btn-primary-gamified w-100 fw-bold"
                   style="min-height: 44px;">
                  <i class="bi bi-file-earmark-text-fill me-1"></i> Start Mock Test
                </a>
              <?php else: ?>
                <button class="btn btn-outline-gamified w-100 fw-bold" 
                        disabled 
                        style="min-height: 44px; border-color: var(--correct-green) !important; color: var(--correct-green) !important;"
                        aria-label="<?= htmlspecialchars($item['title']) ?> already unlocked">
                  <i class="bi bi-check-circle-fill me-1"></i> Activated & Ready
                </button>
              <?php endif; ?>

            <?php elseif (!$userId): ?>
              <!-- State 2: Guest Not Logged In -->
              <a href="index.php?action=login" 
                 class="btn btn-outline-gamified w-100 fw-bold" 
                 style="min-height: 44px;">
                <i class="bi bi-lock-fill me-1"></i> Log In to Unlock
              </a>

            <?php elseif ($hasEnough): ?>
              <!-- State 3: Logged In & Has Enough Coins -->
              <button type="button" 
                      class="btn btn-primary-gamified w-100 fw-bold unlock-btn" 
                      data-id="<?= $item['id'] ?>"
                      data-type="<?= htmlspecialchars($item['item_type']) ?>"
                      data-cost="<?= $cost ?>"
                      style="min-height: 44px;"
                      aria-label="Unlock <?= htmlspecialchars($item['title']) ?> for <?= number_format($cost) ?> Speed Coins">
                <i class="bi bi-unlock-fill me-1"></i> Unlock (<svg class="exam-icon me-0.5" style="width:13px;height:13px;"><use href="#icon-coin"></use></svg><?= number_format($cost) ?>)
              </button>

            <?php else: ?>
              <!-- State 4: Logged In but Not Enough Coins -->
              <div class="d-flex flex-column gap-1">
                <button type="button" 
                        class="btn btn-exam-disabled w-100 fw-bold" 
                        disabled 
                        aria-disabled="true"
                        style="min-height: 44px;"
                        title="You need <?= number_format($diff) ?> more coins to unlock this pack">
                  <i class="bi bi-lock-fill me-1"></i> Insufficient Coins
                </button>
                <div class="d-flex justify-content-between align-items-center px-1" style="font-size: 0.85rem;">
                  <span class="text-ink-muted">Needs <strong class="text-ink"><?= number_format($diff) ?></strong> more</span>
                  <a href="index.php?action=practice" class="text-decoration-none fw-bold" style="color: var(--marker-orange);">
                    Practice to Earn &rarr;
                  </a>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.unlock-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const unlockableId = btn.dataset.id;
            const itemType = btn.dataset.type;
            const originalContent = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Verifying Ledger...';

            fetch('index.php?action=api_buy_unlockable', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ unlockable_id: unlockableId })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    if (typeof launchConfetti === 'function') {
                        launchConfetti();
                    }
                    
                    if (itemType === 'company_mock') {
                        btn.outerHTML = `<a href="index.php?action=mock" class="btn btn-primary-gamified w-100 fw-bold" style="min-height: 44px;"><i class="bi bi-file-earmark-text-fill me-1"></i> Start Mock Test</a>`;
                    } else {
                        btn.className = 'btn btn-outline-gamified w-100 fw-bold';
                        btn.style.borderColor = 'var(--correct-green)';
                        btn.style.color = 'var(--correct-green)';
                        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Activated & Ready';
                    }

                    // Update all coin displays across header, mobile header and store
                    if (res.new_balance !== undefined) {
                        const displays = [
                            document.getElementById('storeUserCoinsDisplay'),
                            document.getElementById('userCoinsDisplay'),
                            document.getElementById('userCoinsDisplayMobile')
                        ];
                        displays.forEach(el => {
                            if (el) {
                                if (el.id === 'storeUserCoinsDisplay') {
                                    el.innerText = `${res.new_balance.toLocaleString()} Coins`;
                                } else {
                                    el.innerText = res.new_balance.toLocaleString();
                                }
                            }
                        });
                    }

                    alert(res.message);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    alert(res.message);
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('Network ledger error. Please check your connection and try again.');
            });
        });
    });
});
</script>
