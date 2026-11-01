<?php
$userId = $_SESSION['user_id'] ?? null;
$items = WalletService::getUnlockables($userId);
$userBalance = $userId ? WalletService::getUserBalance($userId) : 0;
$csrfToken = $_SESSION['csrf_token'] ?? '';
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
            <span class="coin-icon"><svg class="exam-icon text-warning icon-18"><use href="#icon-coin"></use></svg></span>
            <span id="storeUserCoinsDisplay"><?= number_format($userBalance) ?> Coins</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Hidden CSRF Token for Secure AJAX Calls -->
    <input type="hidden" id="csrfToken" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" id="currentUserCoins" value="<?= (int)$userBalance ?>">

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
            Subscribers receive unlimited access to all 21 company-curated placement mock packs (TCS NQT, Infosys, Amazon, Oracle, Persistent, Zoho, Wipro, Mphasis, Hexaware, Virtusa, Coforge & more) with 0 coin cost!
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
        <div class="store-card-exam" id="store-item-card-<?= $item['id'] ?>">
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
                  <span class="coin-icon"><svg class="exam-icon text-warning icon-14"><use href="#icon-coin"></use></svg></span>
                  <span><?= number_format($cost) ?></span>
                </div>
              <?php endif; ?>
            </div>

            <!-- Title & Description (min 16px body font) -->
            <h3 class="store-card-title"><?= htmlspecialchars($item['title']) ?></h3>
            <p class="store-card-desc"><?= htmlspecialchars($item['description']) ?></p>
          </div>

          <!-- Card Action States -->
          <div class="store-card-footer" id="store-action-container-<?= $item['id'] ?>">
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

            <?php else: ?>
              <!-- State 3: Logged In (Eligible to open Unlock & Coupon modal) -->
              <button type="button" 
                      class="btn btn-primary-gamified w-100 fw-bold unlock-trigger-btn" 
                      data-id="<?= $item['id'] ?>"
                      data-title="<?= htmlspecialchars($item['title']) ?>"
                      data-type="<?= htmlspecialchars($item['item_type']) ?>"
                      data-code="<?= htmlspecialchars($item['code'] ?? '') ?>"
                      data-cost="<?= $cost ?>"
                      data-icon="<?= htmlspecialchars($item['icon']) ?>"
                      style="min-height: 44px;"
                      aria-label="Unlock <?= htmlspecialchars($item['title']) ?> for <?= number_format($cost) ?> Speed Coins">
                <i class="bi bi-unlock-fill me-1"></i> Unlock (<svg class="exam-icon me-0.5 icon-13"><use href="#icon-coin"></use></svg><?= number_format($cost) ?>)
              </button>
              <?php if (!$hasEnough): ?>
                <div class="d-flex justify-content-between align-items-center px-1 mt-1" style="font-size: 0.85rem;">
                  <span class="text-ink-muted">Needs <strong class="text-ink"><?= number_format($diff) ?></strong> more</span>
                  <span class="text-decoration-none fw-bold" style="color: var(--marker-orange); cursor: pointer;" onclick="openCouponUnlockModal(<?= $item['id'] ?>, '<?= addslashes($item['title']) ?>', '<?= addslashes($item['item_type']) ?>', <?= $cost ?>, '<?= addslashes($item['icon']) ?>', '<?= addslashes($item['code'] ?? '') ?>')">
                    Apply Coupon &rarr;
                  </span>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</div>

<!-- Mobile-Friendly Unlock & Coupon Modal -->
<div class="modal fade" id="unlockCouponModal" tabindex="-1" aria-labelledby="unlockCouponModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered store-modal-card">
    <div class="modal-content card-gamified border-2 p-0 overflow-hidden" style="background-color: var(--card-bg, #fff);">
      
      <!-- Modal Header -->
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background-color: var(--surface, #f8fafc); border-color: var(--border) !important;">
        <div class="d-flex align-items-center gap-2">
          <div class="p-2 rounded-3 border" style="background: var(--bg); border-color: var(--border) !important;">
            <i id="modalItemIcon" class="bi bi-gift fs-4" style="color: var(--marker-orange);"></i>
          </div>
          <div>
            <h5 class="modal-title font-serif fw-bold text-ink mb-0" id="unlockCouponModalLabel" style="font-size: 1.15rem;">Unlock Store Item</h5>
            <span class="extra-small text-ink-muted" id="modalItemTypeBadge">Placement Mock Pack</span>
          </div>
        </div>
        <button type="button" class="btn-close modal-close-44" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-3 p-sm-4">
        
        <!-- Item Title -->
        <h4 class="font-serif fw-bold text-ink mb-3" id="modalItemTitle" style="font-size: 1.25rem;">-</h4>

        <!-- Pricing Summary Box -->
        <div class="p-3 rounded-3 mb-3 border" style="background-color: var(--bg); border-color: var(--border) !important;">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-ink-muted">Catalog Price:</span>
            <span class="fw-bold font-mono text-ink fs-6" id="modalOriginalPriceDisplay">250 Coins</span>
          </div>
          <div id="modalDiscountRow" class="d-flex justify-content-between align-items-center mb-1 d-none">
            <span class="text-success fw-semibold"><i class="bi bi-tag-fill me-1"></i> Coupon Discount:</span>
            <span class="fw-bold font-mono text-success" id="modalDiscountDisplay">-50 Coins</span>
          </div>
          <hr class="my-2" style="border-color: var(--border);">
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold text-ink fs-6">Final Cost:</span>
            <div class="d-flex align-items-center gap-2">
              <span id="modalOriginalStrikethrough" class="text-decoration-line-through text-ink-muted font-mono d-none">250</span>
              <span class="fw-bold font-mono fs-5" style="color: var(--marker-orange);" id="modalFinalPriceDisplay">250 Coins</span>
            </div>
          </div>
        </div>

        <!-- Have a coupon? Toggle / Input -->
        <div class="mb-3" id="couponSection">
          <label for="couponCodeInput" class="form-label fw-bold text-ink small mb-1 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-ticket-perforated-fill me-1" style="color: var(--marker-orange);"></i> Have a Coupon Code? <span class="text-ink-muted fw-normal">(Optional)</span></span>
            <span id="appliedBadge" class="badge bg-success-subtle text-success border border-success-subtle d-none">Applied</span>
          </label>
          <div class="input-group">
            <input type="text" 
                   id="couponCodeInput" 
                   class="form-control form-control-gamified text-uppercase font-mono coupon-input-44" 
                   placeholder="e.g. APT-7K3Q-9XMD (Optional)" 
                   autocomplete="off"
                   maxlength="30">
            <button class="btn btn-outline-gamified fw-bold coupon-apply-btn" 
                    type="button" 
                    id="btnApplyCoupon" 
                    onclick="handleApplyCoupon()">
              Apply
            </button>
            <button class="btn btn-outline-danger fw-bold store-min-btn d-none" 
                    type="button" 
                    id="btnRemoveCoupon" 
                    onclick="handleRemoveCoupon()">
              <i class="bi bi-x-lg me-1"></i> Remove
            </button>
          </div>
          <div class="form-text extra-small text-ink-muted mt-1">
            <i class="bi bi-info-circle me-1"></i> Optional: Promo coupons grant extra coin discounts. You can unlock directly with your Speed Coins without one!
          </div>
          <!-- Coupon Feedback Message -->
          <div id="couponFeedback" class="small mt-1 d-none" role="alert"></div>
        </div>

        <!-- Wallet Balance Note -->
        <div class="d-flex justify-content-between align-items-center px-1 mb-2 small">
          <span class="text-ink-muted">Your Balance:</span>
          <span class="font-mono fw-bold text-ink" id="modalUserBalanceDisplay"><?= number_format($userBalance) ?> Coins</span>
        </div>

        <!-- Error Warning Box -->
        <div id="modalErrorAlert" class="alert alert-danger py-2 small mb-3 d-none" role="alert"></div>

      </div>

      <!-- Modal Footer -->
      <div class="p-3 border-top d-flex gap-2 justify-content-end" style="background-color: var(--surface, #f8fafc); border-color: var(--border) !important;">
        <button type="button" class="btn btn-outline-gamified fw-bold store-min-btn" data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="button" class="btn btn-primary-gamified fw-bold px-4 store-min-btn" id="btnConfirmUnlock" onclick="handleConfirmPurchase()">
          <i class="bi bi-check2-circle me-1"></i> Confirm Unlock
        </button>
      </div>

    </div>
  </div>
</div>

<script>
let currentItem = {
    id: 0,
    title: '',
    type: '',
    code: '',
    cost: 0,
    icon: ''
};
let appliedCoupon = null;
let unlockModalInstance = null;

function openCouponUnlockModal(id, title, type, cost, icon, code = '') {
    currentItem = { id, title, type, cost, icon, code };
    appliedCoupon = null;

    document.getElementById('modalItemTitle').textContent = title;
    document.getElementById('modalItemIcon').className = icon || 'bi bi-gift';
    document.getElementById('modalItemTypeBadge').textContent = (type === 'streak_freeze') ? 'Safety Shield' : (type === 'company_mock' ? 'Placement Mock Pack' : 'Store Reward');
    document.getElementById('modalOriginalPriceDisplay').textContent = `${cost.toLocaleString()} Coins`;
    document.getElementById('modalFinalPriceDisplay').textContent = `${cost.toLocaleString()} Coins`;
    document.getElementById('btnConfirmUnlock').innerHTML = `<i class="bi bi-check2-circle me-1"></i> Confirm Unlock (${cost.toLocaleString()} Coins)`;
    
    // Reset coupon UI
    document.getElementById('couponCodeInput').value = '';
    document.getElementById('couponCodeInput').disabled = false;
    document.getElementById('modalDiscountRow').classList.add('d-none');
    document.getElementById('modalOriginalStrikethrough').classList.add('d-none');
    document.getElementById('appliedBadge').classList.add('d-none');
    document.getElementById('btnApplyCoupon').classList.remove('d-none');
    document.getElementById('btnRemoveCoupon').classList.add('d-none');
    document.getElementById('couponFeedback').classList.add('d-none');
    document.getElementById('modalErrorAlert').classList.add('d-none');

    const modalEl = document.getElementById('unlockCouponModal');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        unlockModalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        unlockModalInstance.show();
    }
}

async function handleApplyCoupon() {
    const code = document.getElementById('couponCodeInput').value.trim();
    const feedback = document.getElementById('couponFeedback');
    const applyBtn = document.getElementById('btnApplyCoupon');
    const csrfToken = document.getElementById('csrfToken').value;

    if (!code) {
        feedback.className = 'small mt-1 text-danger';
        feedback.textContent = 'Please enter a coupon code.';
        feedback.classList.remove('d-none');
        return;
    }

    applyBtn.disabled = true;
    applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>';

    try {
        const res = await fetch('index.php?action=api_preview_coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                unlockable_id: currentItem.id,
                coupon_code: code,
                csrf_token: csrfToken
            })
        });

        const data = await res.json();
        applyBtn.disabled = false;
        applyBtn.textContent = 'Apply';

        if (res.status === 200 && data.status === 'success') {
            appliedCoupon = data;
            document.getElementById('couponCodeInput').disabled = true;
            document.getElementById('btnApplyCoupon').classList.add('d-none');
            document.getElementById('btnRemoveCoupon').classList.remove('d-none');
            document.getElementById('appliedBadge').classList.remove('d-none');

            feedback.className = 'small mt-1 text-success fw-semibold';
            feedback.innerHTML = `<i class="bi bi-check-circle me-1"></i> Coupon <strong>${data.code}</strong> applied! You save ${data.discount_amount} coins.`;
            feedback.classList.remove('d-none');

            // Update price breakdown
            document.getElementById('modalDiscountRow').classList.remove('d-none');
            document.getElementById('modalDiscountDisplay').textContent = `-${data.discount_amount.toLocaleString()} Coins`;
            document.getElementById('modalOriginalStrikethrough').classList.remove('d-none');
            document.getElementById('modalOriginalStrikethrough').textContent = `${data.original_price}`;
            document.getElementById('modalFinalPriceDisplay').textContent = `${data.final_price.toLocaleString()} Coins`;
            document.getElementById('btnConfirmUnlock').innerHTML = `<i class="bi bi-check2-circle me-1"></i> Confirm Unlock (${data.final_price.toLocaleString()} Coins)`;
            document.getElementById('modalErrorAlert').classList.add('d-none');
        } else {
            appliedCoupon = null;
            document.getElementById('btnConfirmUnlock').innerHTML = `<i class="bi bi-check2-circle me-1"></i> Confirm Unlock (${currentItem.cost.toLocaleString()} Coins)`;
            feedback.className = 'small mt-1 text-danger';
            feedback.textContent = data.message || 'Invalid or ineligible coupon code.';
            feedback.classList.remove('d-none');
        }
    } catch (err) {
        applyBtn.disabled = false;
        applyBtn.textContent = 'Apply';
        feedback.className = 'small mt-1 text-danger';
        feedback.textContent = 'Network error verifying coupon code.';
        feedback.classList.remove('d-none');
    }
}

function handleRemoveCoupon() {
    appliedCoupon = null;
    document.getElementById('couponCodeInput').value = '';
    document.getElementById('couponCodeInput').disabled = false;
    document.getElementById('btnApplyCoupon').classList.remove('d-none');
    document.getElementById('btnRemoveCoupon').classList.add('d-none');
    document.getElementById('appliedBadge').classList.add('d-none');
    document.getElementById('couponFeedback').classList.add('d-none');

    // Reset price display to original
    document.getElementById('modalDiscountRow').classList.add('d-none');
    document.getElementById('modalOriginalStrikethrough').classList.add('d-none');
    document.getElementById('modalFinalPriceDisplay').textContent = `${currentItem.cost.toLocaleString()} Coins`;
    document.getElementById('btnConfirmUnlock').innerHTML = `<i class="bi bi-check2-circle me-1"></i> Confirm Unlock (${currentItem.cost.toLocaleString()} Coins)`;
}

async function handleConfirmPurchase() {
    const confirmBtn = document.getElementById('btnConfirmUnlock');
    const alertBox = document.getElementById('modalErrorAlert');
    const csrfToken = document.getElementById('csrfToken').value;
    const userCoins = parseInt(document.getElementById('currentUserCoins').value || '0', 10);

    const neededCoins = appliedCoupon ? appliedCoupon.final_price : currentItem.cost;
    if (userCoins < neededCoins) {
        alertBox.className = 'alert alert-danger py-2 small mb-3';
        alertBox.textContent = `Insufficient Speed Coins! You have ${userCoins.toLocaleString()} coins, but this item requires ${neededCoins.toLocaleString()} coins.`;
        alertBox.classList.remove('d-none');
        return;
    }

    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Unlocking...';

    try {
        const payload = {
            unlockable_id: currentItem.id,
            csrf_token: csrfToken
        };
        if (appliedCoupon && appliedCoupon.code) {
            payload.coupon_code = appliedCoupon.code;
        }

        const res = await fetch('index.php?action=api_buy_unlockable', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        const displayCoins = (appliedCoupon ? appliedCoupon.final_price : currentItem.cost).toLocaleString();
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = `<i class="bi bi-check2-circle me-1"></i> Confirm Unlock (${displayCoins} Coins)`;

        if (res.status === 200 && data.status === 'success') {
            if (typeof launchConfetti === 'function') {
                launchConfetti();
            }

            // Update user balance everywhere
            if (data.new_balance !== undefined) {
                document.getElementById('currentUserCoins').value = data.new_balance;
                const displays = [
                    document.getElementById('storeUserCoinsDisplay'),
                    document.getElementById('userCoinsDisplay'),
                    document.getElementById('userCoinsDisplayMobile'),
                    document.getElementById('modalUserBalanceDisplay')
                ];
                displays.forEach(el => {
                    if (el) {
                        if (el.id === 'storeUserCoinsDisplay' || el.id === 'modalUserBalanceDisplay') {
                            el.innerText = `${data.new_balance.toLocaleString()} Coins`;
                        } else {
                            el.innerText = data.new_balance.toLocaleString();
                        }
                    }
                });
            }

            // Update store card
            const actionContainer = document.getElementById('store-action-container-' + currentItem.id);
            if (actionContainer) {
                if (currentItem.type === 'company_mock') {
                    const packParam = currentItem.code ? `&pack=${encodeURIComponent(currentItem.code)}` : '';
                    actionContainer.innerHTML = `<a href="index.php?action=mock${packParam}" class="btn btn-primary-gamified w-100 fw-bold store-min-btn"><i class="bi bi-file-earmark-text-fill me-1"></i> Start Mock Test</a>`;
                } else {
                    actionContainer.innerHTML = `<button class="btn btn-outline-gamified w-100 fw-bold store-min-btn" disabled style="border-color: var(--correct-green) !important; color: var(--correct-green) !important;"><i class="bi bi-check-circle-fill me-1"></i> Activated & Ready</button>`;
                }
            }

            if (unlockModalInstance) {
                unlockModalInstance.hide();
            }
            alert(data.message);
        } else {
            alertBox.className = 'alert alert-danger py-2 small mb-3';
            alertBox.textContent = data.message || 'Purchase could not be completed.';
            alertBox.classList.remove('d-none');
        }
    } catch (err) {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Confirm Unlock';
        alertBox.className = 'alert alert-danger py-2 small mb-3';
        alertBox.textContent = 'Network or ledger error. Please check your connection and try again.';
        alertBox.classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.unlock-trigger-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = parseInt(btn.dataset.id, 10);
            const title = btn.dataset.title;
            const type = btn.dataset.type;
            const cost = parseInt(btn.dataset.cost, 10);
            const icon = btn.dataset.icon;
            const code = btn.dataset.code || '';
            openCouponUnlockModal(id, title, type, cost, icon, code);
        });
    });
});
</script>
