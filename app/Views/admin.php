<?php
require_once __DIR__ . '/../Services/CouponService.php';
require_once __DIR__ . '/../Services/WalletService.php';

try { $allCompanyProfiles = AdminController::getAllCompanyProfiles(); } catch (\Throwable $e) { $allCompanyProfiles = []; }
try { $allStoreItems = WalletService::getUnlockables(); } catch (\Throwable $e) { $allStoreItems = []; }
try { $initialCoupons = CouponService::listCoupons(); } catch (\Throwable $e) { $initialCoupons = []; }
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<div class="row justify-content-center py-3">
  <div class="col-lg-10">

    <div class="card card-gamified p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-shield-lock-fill" style="color: var(--accent);"></i> Admin Content CMS & Moderation
        </h4>
        <span class="stamp-badge py-1 px-3">Content Ops Portal</span>
      </div>
      <p class="text-ink-muted small mb-0">Manage question banks, company recruitment patterns, bulk imports, and review user content reports.</p>
    </div>

    <input type="hidden" id="adminCsrfToken" value="<?= htmlspecialchars($csrfToken) ?>">

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

    <!-- Admin Coupon Codes & Promotions Management Card -->
    
    <!-- User Management & Speed Coin Gifting Hub Card -->
    <div class="card card-gamified p-3 p-md-4 mb-4" id="userAdminCard">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <span class="stamp-badge">COMMUNITY &amp; ECONOMY</span>
            <span class="stamp-badge-highlight" style="background-color: var(--accent-light); color: var(--accent);">USER OPERATIONS</span>
          </div>
          <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-people-fill fs-4" style="color: var(--accent);"></i> User Management &amp; Coin Gifting Hub
          </h5>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button type="button" class="btn btn-outline-gamified fw-bold" style="min-height: 44px;" onclick="loadAdminUsers(1)">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
          </button>
          <button type="button" class="btn btn-primary-gamified fw-bold" style="min-height: 44px;" onclick="openBulkGiftModal()">
            <i class="bi bi-gift-fill me-1"></i> Bulk Gift Coins
          </button>
        </div>
      </div>
      <p class="text-ink-muted small mb-3">
        Manage registered students and administrators, audit user activity, gift Speed Coins, adjust account tiers/streaks, or reset student passwords.
      </p>

      <!-- KPI Summary Row -->
      <div class="row g-2 mb-4" id="userKpiRow">
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-3 border" style="background-color: var(--surface); border-color: var(--border) !important;">
            <div class="text-ink-muted extra-small text-uppercase fw-bold mb-1"><i class="bi bi-people me-1"></i> Total Users</div>
            <div class="h4 fw-bold text-ink mb-0" id="kpiTotalUsers">--</div>
            <div class="extra-small text-ink-muted" id="kpiAdminsCount">-- admins</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-3 border" style="background-color: var(--surface); border-color: var(--border) !important;">
            <div class="text-ink-muted extra-small text-uppercase fw-bold mb-1"><i class="bi bi-mortarboard me-1"></i> Students</div>
            <div class="h4 fw-bold text-ink mb-0" id="kpiTotalStudents">--</div>
            <div class="extra-small text-ink-muted">Registered learners</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-3 border" style="background-color: var(--surface); border-color: var(--border) !important;">
            <div class="text-ink-muted extra-small text-uppercase fw-bold mb-1"><i class="bi bi-coin me-1" style="color: #f59e0b;"></i> Coins in Circulation</div>
            <div class="h4 fw-bold mb-0" style="color: #f59e0b;" id="kpiTotalCoins">--</div>
            <div class="extra-small text-ink-muted">Speed Coins held</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-3 border" style="background-color: var(--surface); border-color: var(--border) !important;">
            <div class="text-ink-muted extra-small text-uppercase fw-bold mb-1"><i class="bi bi-fire me-1" style="color: #ef4444;"></i> Active Streaks</div>
            <div class="h4 fw-bold text-danger mb-0" id="kpiActiveStreaks">--</div>
            <div class="extra-small text-ink-muted">Daily active learners</div>
          </div>
        </div>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="row g-2 mb-3 align-items-center">
        <div class="col-12 col-md-5">
          <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-ink-muted" style="border-color: var(--border) !important;">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control form-control-gamified border-start-0 ps-0" id="userSearchInput" placeholder="Search by name, email, or user ID..." oninput="debounceUserSearch()" style="min-height: 42px;">
          </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
          <select class="form-select form-control-gamified" id="userRoleFilter" onchange="loadAdminUsers(1)" style="min-height: 42px;">
            <option value="">All Roles</option>
            <option value="user">Students Only</option>
            <option value="admin">Admins Only</option>
          </select>
        </div>
        <div class="col-6 col-sm-4 col-md-3">
          <select class="form-select form-control-gamified" id="userTierFilter" onchange="loadAdminUsers(1)" style="min-height: 42px;">
            <option value="">All Tiers</option>
            <option value="Bronze">Bronze Tier</option>
            <option value="Silver">Silver Tier</option>
            <option value="Gold">Gold Tier</option>
            <option value="Platinum">Platinum Tier</option>
            <option value="Diamond">Diamond Tier</option>
          </select>
        </div>
        <div class="col-12 col-sm-4 col-md-2">
          <select class="form-select form-control-gamified" id="userPlanFilter" onchange="loadAdminUsers(1)" style="min-height: 42px;">
            <option value="">All Plans</option>
            <option value="free">Free Plan</option>
            <option value="premium">Premium Plan</option>
          </select>
        </div>
      </div>

      <!-- Users Table -->
      <div class="table-responsive rounded-3 border" style="border-color: var(--border) !important;">
        <table class="table table-hover mb-0 align-middle">
          <thead>
            <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
              <th class="font-serif text-ink">User</th>
              <th class="font-serif text-ink">Role &amp; Plan</th>
              <th class="font-serif text-ink">Tier</th>
              <th class="font-serif text-ink">Speed Coins</th>
              <th class="font-serif text-ink">XP &amp; Streak</th>
              <th class="font-serif text-ink">Joined</th>
              <th class="font-serif text-ink text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="userTableBody">
            <tr>
              <td colspan="7" class="text-center py-4 text-ink-muted">
                <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div> Loading users...
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination & Status Footer -->
      <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="extra-small text-ink-muted" id="userPaginationInfo">Showing users...</div>
        <div class="d-flex gap-1" id="userPaginationControls"></div>
      </div>
    </div>

    <div class="card card-gamified p-3 p-md-4 mb-4" id="couponAdminCard">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <span class="stamp-badge">SHOP PROMOTIONS</span>
            <span class="stamp-badge-highlight">SPEED COIN DISCOUNTS</span>
          </div>
          <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-ticket-perforated-fill fs-4" style="color: var(--marker-orange);"></i> Admin Coupon Codes &amp; Promotion Manager
          </h5>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button type="button" class="btn btn-outline-gamified fw-bold" style="min-height: 44px;" onclick="toggleCreateCouponForm()">
            <i class="bi bi-plus-circle-fill me-1"></i> <span id="toggleCreateFormText">Create Coupon</span>
          </button>
          <button type="button" class="btn btn-primary-gamified fw-bold" style="min-height: 44px;" data-bs-toggle="modal" data-bs-target="#bulkCouponModal">
            <i class="bi bi-boxes me-1"></i> Bulk Generate
          </button>
          <button type="button" class="btn btn-outline-gamified fw-bold" style="min-height: 44px;" onclick="exportCouponsToCSV()">
            <i class="bi bi-download me-1"></i> Export CSV
          </button>
        </div>
      </div>
      <p class="text-ink-muted small mb-3">
        Generate custom or secure random promo codes for the Speed Coin store. Configure discount caps, minimum item thresholds, per-user limits, and targeted user restrictions with atomic transaction safety.
      </p>

      <!-- Collapsible Create Single Coupon Form -->
      <div id="createCouponCollapse" class="d-none p-3 rounded-3 mb-4 border" style="background-color: var(--surface); border-color: var(--border) !important;">
        <h6 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-pencil-square" style="color: var(--marker-orange);"></i> Create Single Promotion Code
        </h6>
        <form id="createCouponForm" onsubmit="handleCreateCoupon(event)">
          <div class="row g-3">
            
            <!-- Code Field + Generate Button -->
            <div class="col-12 col-md-6">
              <label for="couponCode" class="form-label fw-bold text-ink small mb-1">Coupon Code</label>
              <div class="input-group">
                <input type="text" class="form-control form-control-gamified text-uppercase font-mono" id="couponCode" name="code" placeholder="Leave blank to auto-generate" maxlength="20" style="min-height: 44px; font-size: 16px;">
                <button type="button" class="btn btn-outline-gamified fw-bold" style="min-height: 44px;" onclick="document.getElementById('couponCode').value = generateSecureClientCode('APT-')">
                  <i class="bi bi-magic me-1"></i> Generate
                </button>
              </div>
              <div class="form-text extra-small text-ink-muted">3-20 characters (A-Z, 0-9, dashes). Case-insensitive.</div>
            </div>

            <!-- Discount Type -->
            <div class="col-12 col-sm-6 col-md-3">
              <label for="couponDiscountType" class="form-label fw-bold text-ink small mb-1">Discount Type</label>
              <select class="form-select form-control-gamified" id="couponDiscountType" name="discount_type" style="min-height: 44px; font-size: 16px;" onchange="updateDiscountLabel(this.value)">
                <option value="percent">Percentage (%)</option>
                <option value="fixed">Fixed Coins</option>
              </select>
            </div>

            <!-- Discount Value -->
            <div class="col-12 col-sm-6 col-md-3">
              <label for="couponDiscountValue" class="form-label fw-bold text-ink small mb-1" id="lblDiscountValue">Discount Percentage (%)</label>
              <input type="number" class="form-control form-control-gamified" id="couponDiscountValue" name="discount_value" placeholder="e.g. 20" min="1" max="100" required style="min-height: 44px; font-size: 16px;">
            </div>

            <!-- Max Discount Cap (for percent) -->
            <div class="col-12 col-sm-6 col-md-3" id="colMaxDiscount">
              <label for="couponMaxDiscount" class="form-label fw-bold text-ink small mb-1">Max Discount Cap (Coins)</label>
              <input type="number" class="form-control form-control-gamified" id="couponMaxDiscount" name="max_discount" placeholder="e.g. 100 (optional)" min="1" style="min-height: 44px; font-size: 16px;">
            </div>

            <!-- Minimum Item Price -->
            <div class="col-12 col-sm-6 col-md-3">
              <label for="couponMinPrice" class="form-label fw-bold text-ink small mb-1">Min Item Price (Coins)</label>
              <input type="number" class="form-control form-control-gamified" id="couponMinPrice" name="min_price" placeholder="0 = No minimum" min="0" value="0" style="min-height: 44px; font-size: 16px;">
            </div>

            <!-- Applicable Store Items -->
            <div class="col-12 col-md-6">
              <label for="couponApplicableItems" class="form-label fw-bold text-ink small mb-1">Applicable Store Items</label>
              <select class="form-select form-control-gamified" id="couponApplicableItems" name="applicable_items" style="min-height: 44px; font-size: 16px;">
                <option value="all">All Store Items (Full Catalog)</option>
                <option value="streak_freeze">Streak Freeze Safety Shields Only</option>
                <option value="company_mock">Company Placement Mock Test Packs Only</option>
                <option value="cosmetic_badge">Profile Badges &amp; Cosmetics Only</option>
              </select>
            </div>

            <!-- Total Usage Limit -->
            <div class="col-12 col-sm-6 col-md-3">
              <label for="couponTotalLimit" class="form-label fw-bold text-ink small mb-1">Total Global Uses</label>
              <input type="number" class="form-control form-control-gamified" id="couponTotalLimit" name="total_limit" placeholder="Blank = Unlimited" min="1" style="min-height: 44px; font-size: 16px;">
            </div>

            <!-- Per-User Limit -->
            <div class="col-12 col-sm-6 col-md-3">
              <label for="couponPerUserLimit" class="form-label fw-bold text-ink small mb-1">Per-User Limit</label>
              <input type="number" class="form-control form-control-gamified" id="couponPerUserLimit" name="per_user_limit" value="1" min="1" required style="min-height: 44px; font-size: 16px;">
            </div>

            <!-- Starts At -->
            <div class="col-12 col-sm-6 col-md-3">
              <label for="couponStartsAt" class="form-label fw-bold text-ink small mb-1">Start Date &amp; Time</label>
              <input type="datetime-local" class="form-control form-control-gamified" id="couponStartsAt" name="starts_at" style="min-height: 44px; font-size: 16px;">
            </div>

            <!-- Expires At -->
            <div class="col-12 col-sm-6 col-md-3">
              <label for="couponExpiresAt" class="form-label fw-bold text-ink small mb-1">Expiry Date &amp; Time</label>
              <input type="datetime-local" class="form-control form-control-gamified" id="couponExpiresAt" name="expires_at" style="min-height: 44px; font-size: 16px;">
            </div>

            <!-- Locked to Specific Users -->
            <div class="col-12 col-md-6">
              <label for="couponAllowedUsers" class="form-label fw-bold text-ink small mb-1">Lock to Specific User(s)</label>
              <input type="text" class="form-control form-control-gamified" id="couponAllowedUsers" name="allowed_users" placeholder="e.g. john@exam.com, alex (optional)" style="min-height: 44px; font-size: 16px;">
              <div class="form-text extra-small text-ink-muted">Comma-separated student emails or usernames. Leave blank for all students.</div>
            </div>

            <!-- Active Switch & Submit -->
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
              <div class="form-check form-switch fs-6">
                <input class="form-check-input" type="checkbox" id="couponIsActive" name="is_active" value="1" checked style="cursor: pointer;">
                <label class="form-check-label text-ink fw-semibold" for="couponIsActive">Activate Coupon Immediately</label>
              </div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-gamified fw-bold" onclick="toggleCreateCouponForm()" style="min-height: 44px;">Cancel</button>
                <button type="submit" class="btn btn-primary-gamified fw-bold px-4" id="btnSubmitCoupon" style="min-height: 44px;">
                  <i class="bi bi-check2-circle me-1"></i> Save Coupon
                </button>
              </div>
            </div>

          </div>
        </form>
        <div id="createCouponAlert" class="alert d-none mt-3 mb-0 py-2 small" role="alert"></div>
      </div>

      <!-- Search & Filters Toolbar -->
      <div class="row g-2 mb-3 align-items-center">
        <div class="col-12 col-md-6">
          <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--border) !important;">
              <i class="bi bi-search text-ink-muted"></i>
            </span>
            <input type="text" class="form-control form-control-gamified border-start-0" id="couponSearchInput" placeholder="Search by coupon code..." oninput="handleCouponFilter()" style="min-height: 44px; font-size: 16px;">
          </div>
        </div>
        <div class="col-12 col-md-4">
          <select class="form-select form-control-gamified" id="couponStatusFilter" onchange="handleCouponFilter()" style="min-height: 44px; font-size: 16px;">
            <option value="">All Statuses (Active, Expired, Exhausted)</option>
            <option value="active">Active Only</option>
            <option value="expired">Expired Only</option>
            <option value="exhausted">Used Up / Exhausted Only</option>
            <option value="inactive">Inactive (Deactivated) Only</option>
          </select>
        </div>
        <div class="col-12 col-md-2 text-md-end text-ink-muted small">
          <span id="couponCountDisplay"><?= count($initialCoupons) ?> Coupons</span>
        </div>
      </div>

      <!-- Responsive Coupons View: Desktop Table + Mobile Cards -->
      <div class="d-none d-md-block table-responsive border rounded-3 mb-2" style="border-color: var(--border) !important;">
        <table class="table table-hover mb-0 align-middle">
          <thead style="background-color: var(--surface);">
            <tr>
              <th class="font-serif text-ink" style="width: 200px;">Coupon Code</th>
              <th class="font-serif text-ink" style="width: 140px;">Discount</th>
              <th class="font-serif text-ink" style="width: 120px;">Status</th>
              <th class="font-serif text-ink" style="width: 130px;">Uses / Cap</th>
              <th class="font-serif text-ink" style="width: 160px;">Expiry</th>
              <th class="font-serif text-ink text-end" style="width: 180px;">Actions</th>
            </tr>
          </thead>
          <tbody id="couponsTableBody">
            <!-- Dynamically populated or server rendered -->
            <?php if (empty($initialCoupons)): ?>
              <tr><td colspan="6" class="text-center py-4 text-ink-muted">No coupons created yet. Click "Create Coupon" to issue your first promo code.</td></tr>
            <?php else: foreach ($initialCoupons as $c): 
              $now = date('Y-m-d H:i:s');
              $isExpired = ($c['expires_at'] && $c['expires_at'] <= $now);
              $isExhausted = ($c['total_limit'] !== null && (int)$c['times_used'] >= (int)$c['total_limit']);
              $isActive = ((int)$c['is_active'] === 1 && !$isExpired && !$isExhausted);
            ?>
              <tr id="coupon-row-<?= $c['id'] ?>">
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="font-mono fw-bold text-ink fs-6"><?= htmlspecialchars($c['code']) ?></span>
                    <button type="button" class="btn btn-sm btn-outline-gamified p-1" style="min-height: 32px; min-width: 32px;" title="Copy Code" onclick="navigator.clipboard.writeText('<?= addslashes($c['code']) ?>'); alert('Copied <?= addslashes($c['code']) ?> to clipboard!');">
                      <i class="bi bi-copy"></i>
                    </button>
                  </div>
                  <?php if (!empty($c['allowed_users'])): ?>
                    <div class="extra-small text-ink-muted"><i class="bi bi-person-lock me-1"></i> Locked: <?= htmlspecialchars($c['allowed_users']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-bold text-ink">
                    <?= ($c['discount_type'] === 'percent') ? $c['discount_value'] . '%' : $c['discount_value'] . ' Coins' ?>
                  </div>
                  <?php if ($c['max_discount']): ?>
                    <div class="extra-small text-ink-muted">Cap: <?= $c['max_discount'] ?> Coins</div>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!$c['is_active']): ?>
                    <span class="badge bg-secondary-subtle text-secondary border">Inactive</span>
                  <?php elseif ($isExpired): ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Expired</span>
                  <?php elseif ($isExhausted): ?>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Used Up</span>
                  <?php else: ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="font-mono fw-semibold text-ink"><?= (int)$c['times_used'] ?></span> / <span class="text-ink-muted"><?= $c['total_limit'] !== null ? (int)$c['total_limit'] : '&infin;' ?></span>
                </td>
                <td>
                  <div class="extra-small text-ink">
                    <?= $c['expires_at'] ? date('M d, Y H:i', strtotime($c['expires_at'])) : '<span class="text-ink-muted">Never</span>' ?>
                  </div>
                </td>
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-1">
                    <button type="button" class="btn btn-sm btn-outline-gamified fw-semibold" style="min-height: 44px;" onclick="viewCouponRedemptions(<?= $c['id'] ?>, '<?= addslashes($c['code']) ?>')">
                      <i class="bi bi-clock-history me-1"></i> Uses
                    </button>
                    <button type="button" class="btn btn-sm <?= (int)$c['is_active'] === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> fw-semibold" style="min-height: 44px;" onclick="handleToggleCoupon(<?= $c['id'] ?>)">
                      <?= (int)$c['is_active'] === 1 ? '<i class="bi bi-pause-fill"></i> Disable' : '<i class="bi bi-play-fill"></i> Enable' ?>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card List (<768px): Zero Horizontal Scroll, 44px Tap Targets -->
      <div class="d-md-none d-flex flex-column gap-3 mb-2" id="couponsMobileContainer">
        <?php if (empty($initialCoupons)): ?>
          <div class="p-3 text-center text-ink-muted border rounded-3" style="background-color: var(--surface);">
            No coupons created yet. Click "Create Coupon" to issue your first promo code.
          </div>
        <?php else: foreach ($initialCoupons as $c): 
          $now = date('Y-m-d H:i:s');
          $isExpired = ($c['expires_at'] && $c['expires_at'] <= $now);
          $isExhausted = ($c['total_limit'] !== null && (int)$c['times_used'] >= (int)$c['total_limit']);
          $isActive = ((int)$c['is_active'] === 1 && !$isExpired && !$isExhausted);
        ?>
          <div class="p-3 border rounded-3" style="background-color: var(--surface); border-color: var(--border) !important;" id="coupon-mcard-<?= $c['id'] ?>">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="font-mono fw-bold text-ink fs-5"><?= htmlspecialchars($c['code']) ?></span>
              <?php if (!$c['is_active']): ?>
                <span class="badge bg-secondary-subtle text-secondary border">Inactive</span>
              <?php elseif ($isExpired): ?>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Expired</span>
              <?php elseif ($isExhausted): ?>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Used Up</span>
              <?php else: ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
              <?php endif; ?>
            </div>
            <div class="row g-1 small mb-2">
              <div class="col-6">
                <span class="text-ink-muted">Discount:</span> 
                <strong class="text-ink"><?= ($c['discount_type'] === 'percent') ? $c['discount_value'] . '%' : $c['discount_value'] . ' Coins' ?></strong>
              </div>
              <div class="col-6 text-end">
                <span class="text-ink-muted">Used:</span> 
                <strong class="font-mono text-ink"><?= (int)$c['times_used'] ?> / <?= $c['total_limit'] !== null ? (int)$c['total_limit'] : '&infin;' ?></strong>
              </div>
              <div class="col-12">
                <span class="text-ink-muted">Expiry:</span> 
                <span class="text-ink"><?= $c['expires_at'] ? date('M d, Y H:i', strtotime($c['expires_at'])) : 'Never' ?></span>
              </div>
              <?php if (!empty($c['allowed_users'])): ?>
                <div class="col-12 extra-small text-ink-muted">
                  <i class="bi bi-person-lock me-1"></i> Locked to: <?= htmlspecialchars($c['allowed_users']) ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-gamified fw-semibold flex-grow-1" style="min-height: 44px;" onclick="viewCouponRedemptions(<?= $c['id'] ?>, '<?= addslashes($c['code']) ?>')">
                <i class="bi bi-clock-history me-1"></i> Redemptions
              </button>
              <button type="button" class="btn <?= (int)$c['is_active'] === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> fw-semibold flex-grow-1" style="min-height: 44px;" onclick="handleToggleCoupon(<?= $c['id'] ?>)">
                <?= (int)$c['is_active'] === 1 ? '<i class="bi bi-pause-fill"></i> Disable' : '<i class="bi bi-play-fill"></i> Enable' ?>
              </button>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>

    </div>

    <!-- Web-Safe Database Migration & Rollback Management Card -->
    <div class="card card-gamified p-3 p-md-4 mb-4" id="migrationAdminCard">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-database-fill-gear fs-4" style="color: var(--accent);"></i> Web Database Migrations &amp; Rollback Controller
        </h5>
        <span class="stamp-badge py-1 px-3">Non-CLI Web-Safe</span>
      </div>
      <p class="text-ink-muted small mb-3">
        Execute idempotent master database schema migrations or selectively roll back table migrations directly via web API (fully CSRF and admin protected, no server terminal required).
      </p>

      <div class="row g-3 align-items-end mb-3">
        <div class="col-12 col-md-5">
          <button type="button" class="btn btn-primary-gamified fw-bold w-100" id="btnRunMigration" style="min-height: 44px;" onclick="runWebMigration()">
            <i class="bi bi-play-circle-fill me-1"></i> Run Master Idempotent Migration
          </button>
        </div>
        <div class="col-12 col-md-4">
          <label for="rollbackStepSelect" class="form-label fw-bold text-ink extra-small mb-1">Target Rollback Step</label>
          <select class="form-select form-control-gamified" id="rollbackStepSelect" style="min-height: 44px; font-size: 16px;" onchange="updateRollbackCodeTarget()">
            <option value="coupons">coupons (Tables: coupons, coupon_redemptions, audit)</option>
            <option value="user_practice_sessions">user_practice_sessions (Auto-save Table)</option>
            <option value="company_profiles">company_profiles (Company Recruitment Patterns)</option>
            <option value="student_question_submissions">student_question_submissions (Student Questions)</option>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <button type="button" class="btn btn-outline-danger fw-bold w-100" id="btnRunRollback" style="min-height: 44px;" onclick="runWebRollback()">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Roll Back Step
          </button>
        </div>
      </div>

      <!-- Safety Rollback Confirmation Guard Box -->
      <div class="p-3 rounded-3 mb-3 border border-danger border-opacity-25" id="rollbackSafetyBox" style="background-color: var(--bg);">
        <div class="d-flex align-items-center gap-2 mb-2 text-danger fw-bold extra-small">
          <i class="bi bi-exclamation-triangle-fill fs-6"></i>
          <span>CRITICAL DATA LOSS WARNING: Rolling back drops target tables and permanently deletes all redemption history, logs, and state. Disabled in production unless ALLOW_WEB_ROLLBACK=true.</span>
        </div>
        <div class="row g-2 align-items-center">
          <div class="col-12 col-md-6">
            <label for="typedRollbackStep" class="form-label extra-small text-ink fw-bold mb-1">
              Type <code id="targetStepCode" class="text-danger fw-bold">coupons</code> to confirm table drop:
            </label>
            <input type="text" class="form-control form-control-gamified" id="typedRollbackStep" placeholder="Type step name exactly" style="min-height: 44px; font-size: 14px;">
          </div>
          <div class="col-12 col-md-6 pt-md-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="confirmRollbackDataLoss">
              <label class="form-check-label extra-small text-ink fw-bold" for="confirmRollbackDataLoss">
                I understand this permanently drops tables and erases history.
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Live Terminal Output -->
      <div class="rounded-3 p-3 font-mono extra-small border" id="migrationLogContainer" style="background-color: #1e1e1e; color: #4af626; min-height: 90px; max-height: 240px; overflow-y: auto;">
        <span class="text-secondary">&gt; Ready. Click "Run Master Idempotent Migration" or select a rollback step to execute.</span>
      </div>
    </div>

    <!-- Pending Reports Queue Card -->
    <div class="card card-gamified p-4 mb-4">
      <h5 class="font-serif fw-bold text-ink mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill" style="color: var(--highlight);"></i> User Question Reports Queue (<?= count($pendingReports) ?>)
      </h5>

      <?php if (empty($pendingReports)): ?>
        <div class="alert border text-ink-muted mb-0 d-flex align-items-center gap-2" style="background-color: var(--bg); border-color: var(--border) !important;">
          <svg class="exam-icon text-success icon-16"><use href="#icon-check"></use></svg> No pending question reports in moderation queue!
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

    <!-- College Directory & Campus Management Card (Feature 3) -->
    <?php $adminColleges = $adminColleges ?? []; ?>
    <div class="card card-gamified p-3 p-md-4 mb-4" id="collegeAdminCard">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <span class="stamp-badge">CAMPUS NETWORK</span>
            <span class="stamp-badge-highlight" style="background-color: var(--accent-light); color: var(--accent);">COLLEGE DIRECTORY</span>
          </div>
          <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-building fs-4" style="color: var(--accent);"></i> College Directory &amp; Moderation
          </h5>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button type="button" class="btn btn-outline-gamified btn-sm fw-bold" style="min-height: 40px;" onclick="loadAdminColleges()">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Colleges
          </button>
        </div>
      </div>
      <p class="text-ink-muted small mb-3">
        Approve or reject student-requested colleges, verify campus location details, or merge duplicate colleges to reassign students cleanly.
      </p>

      <div class="d-flex gap-2 mb-3 align-items-center flex-wrap">
        <label class="form-label extra-small fw-bold text-ink-muted mb-0" for="collegeStatusFilter">Filter Status:</label>
        <select id="collegeStatusFilter" class="form-select form-select-sm w-auto" onchange="filterAdminCollegesTable(this.value)">
          <option value="all">All Statuses</option>
          <option value="pending">Pending Review Only</option>
          <option value="active">Active Only</option>
          <option value="rejected">Rejected Only</option>
        </select>
      </div>

      <div class="table-responsive rounded-3 border" style="border-color: var(--border) !important;">
        <table class="table table-hover mb-0 align-middle">
          <thead>
            <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
              <th class="ps-3 font-serif text-ink" style="width: 60px;">ID</th>
              <th class="font-serif text-ink">Code</th>
              <th class="font-serif text-ink">College Name</th>
              <th class="font-serif text-ink">City &amp; State</th>
              <th class="font-serif text-ink text-center">Status</th>
              <th class="font-serif text-ink text-center">Students</th>
              <th class="pe-3 text-end font-serif text-ink" style="min-width: 170px;">Actions</th>
            </tr>
          </thead>
          <tbody id="collegesTableBody">
            <?php foreach ($adminColleges as $col): ?>
              <tr data-status="<?= htmlspecialchars($col['status']) ?>">
                <td class="ps-3 text-ink-muted small">#<?= (int)$col['id'] ?></td>
                <td><code><?= htmlspecialchars($col['code']) ?></code></td>
                <td class="fw-semibold text-ink"><?= htmlspecialchars($col['name']) ?></td>
                <td class="text-ink-muted small"><?= htmlspecialchars($col['city']) ?>, <?= htmlspecialchars($col['state']) ?></td>
                <td class="text-center">
                  <?php if ($col['status'] === 'active'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Active</span>
                  <?php elseif ($col['status'] === 'pending'): ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Pending Review</span>
                  <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">Rejected</span>
                  <?php endif; ?>
                </td>
                <td class="text-center fw-bold text-ink"><?= (int)($col['student_count'] ?? 0) ?></td>
                <td class="pe-3 text-end">
                  <div class="d-inline-flex gap-1">
                    <?php if ($col['status'] === 'pending'): ?>
                      <button type="button" class="btn btn-sm btn-outline-success px-2 py-1" onclick="handleAdminApproveCollege(<?= (int)$col['id'] ?>)">
                        <i class="bi bi-check-lg"></i> Approve
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="handleAdminRejectCollege(<?= (int)$col['id'] ?>)">
                        <i class="bi bi-x-lg"></i> Reject
                      </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-outline-gamified px-2 py-1" onclick="openMergeCollegeModal(<?= (int)$col['id'] ?>, '<?= htmlspecialchars(addslashes($col['name'])) ?>')">
                      <i class="bi bi-arrows-collapse"></i> Merge
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Student Placement Submissions Review Queue Card -->
    <?php $pendingSubmissions = $pendingSubmissions ?? []; ?>
    <div class="card card-gamified p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-mortarboard-fill" style="color: var(--accent);"></i> Student Placement Submissions Queue (<?= count($pendingSubmissions) ?>)
        </h5>
        <span class="badge py-1 px-3 rounded-pill" style="background-color: var(--accent-light); color: var(--accent);">Crowdsourced Question Moderation</span>
      </div>
      <p class="text-ink-muted small mb-3">
        Review real campus placement questions contributed by students from recent drive rounds. Approving a submission rewards the student with <strong>+50 XP</strong> and <strong>+10 Speed Coins</strong> and publishes it live with the <em>Reported by Students</em> badge.
      </p>

      <?php if (empty($pendingSubmissions)): ?>
        <div class="alert border text-ink-muted mb-0 d-flex align-items-center gap-2" style="background-color: var(--bg); border-color: var(--border) !important;">
          <svg class="exam-icon text-success icon-16"><use href="#icon-check"></use></svg> All student placement submissions have been reviewed!
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead>
              <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
                <th class="font-serif text-ink">ID</th>
                <th class="font-serif text-ink">Company & Round</th>
                <th class="font-serif text-ink">Topic / Diff</th>
                <th class="font-serif text-ink">Question & Options</th>
                <th class="font-serif text-ink">Submitter</th>
                <th class="font-serif text-ink text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pendingSubmissions as $sub): ?>
                <tr id="submission-row-<?= $sub['id'] ?>">
                  <td class="text-ink fw-bold">#<?= $sub['id'] ?></td>
                  <td>
                    <div class="fw-bold text-ink"><?= htmlspecialchars($sub['company_name']) ?></div>
                    <div class="text-ink-muted small"><i class="bi bi-calendar-event me-1"></i><?= htmlspecialchars($sub['drive_date']) ?> &bull; <?= htmlspecialchars($sub['round_pattern']) ?></div>
                  </td>
                  <td>
                    <span class="badge bg-secondary mb-1"><?= htmlspecialchars($sub['topic_name']) ?></span><br>
                    <span class="badge <?= $sub['difficulty'] === 'hard' ? 'bg-danger' : ($sub['difficulty'] === 'easy' ? 'bg-success' : 'bg-warning text-dark') ?>">
                      <?= ucfirst($sub['difficulty']) ?>
                    </span>
                  </td>
                  <td>
                    <div class="text-ink fw-medium mb-1" style="max-width: 320px;"><?= htmlspecialchars(mb_strimwidth($sub['question_text'], 0, 100, '...')) ?></div>
                    <div class="small text-ink-muted">
                      <strong>Ans: [<?= $sub['correct_option'] ?>]</strong>
                      <?= htmlspecialchars($sub['option_' . strtolower($sub['correct_option'])]) ?>
                    </div>
                  </td>
                  <td>
                    <div class="text-ink small"><?= htmlspecialchars($sub['submitter_name'] ?? 'Anonymous') ?></div>
                    <div class="text-ink-muted small"><?= htmlspecialchars($sub['submitter_email'] ?? '') ?></div>
                  </td>
                  <td class="text-end">
                    <div class="d-flex gap-2 justify-content-end">
                      <button class="btn btn-sm btn-success px-3 fw-bold" onclick="approveSubmission(<?= $sub['id'] ?>)">
                        <i class="bi bi-check-lg me-1"></i> Approve (+50 XP)
                      </button>
                      <button class="btn btn-sm btn-outline-danger px-2" onclick="rejectSubmission(<?= $sub['id'] ?>)">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Bulk Batch JSON / CSV Import Card -->
    <div class="card card-gamified p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-arrow-up-fill" style="color: var(--success);"></i> Batch Question Importer (JSON & CSV)
        </h5>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Idempotent with Duplicate Check</span>
      </div>
      <p class="text-ink-muted small">
        Bulk upload placement question banks in <strong>JSON</strong> or <strong>CSV</strong> format. The system automatically detects and skips duplicates without breaking unique indexes or overwriting existing questions.
      </p>
      
      <form id="batchImportForm" onsubmit="uploadBatchFile(event)">
        <div class="mb-3">
          <label class="form-label text-ink fw-semibold small">Choose JSON or CSV Question Bank File</label>
          <input type="file" id="batchFileInput" name="batch_file" class="form-control" accept=".json,.csv" required style="background-color: var(--bg); border-color: var(--border); color: var(--ink);">
        </div>
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="dryRunCheckbox" value="1">
          <label class="form-check-label text-ink small fw-semibold" for="dryRunCheckbox">
            Dry Run Mode <span class="text-ink-muted fw-normal">(Validate syntax, math & duplicates without saving to database)</span>
          </label>
        </div>
        <div class="d-flex align-items-center gap-3">
          <button type="submit" id="batchSubmitBtn" class="btn btn-primary-gamified px-4 fw-bold">
            <i class="bi bi-cloud-upload-fill me-1"></i> Upload & Process Question Bank
          </button>
          <span class="text-ink-muted small">Max 50 items/request ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¢ Automatic verification & deduplication</span>
        </div>
      </form>
      
      <!-- Progress Bar Container -->
      <div id="batchProgressContainer" class="d-none mt-3">
        <div class="d-flex justify-content-between text-ink small mb-1">
          <span id="batchProgressStatus">Processing batch...</span>
          <span id="batchProgressPercent">0%</span>
        </div>
        <div class="progress" style="height: 10px; background-color: var(--bg-hover);">
          <div id="batchProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
        </div>
      </div>

      <div id="batchImportAlert" class="alert d-none mt-3 mb-0 py-2 small" role="alert"></div>
    </div>

    <!-- Company Placement Profiles Management Card -->
    <div class="card card-gamified p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-buildings-fill" style="color: var(--accent);"></i> Company Recruitment Patterns &amp; Syllabi (<?= count($allCompanyProfiles) ?>)
        </h5>
        <span class="badge py-1 px-3 rounded-pill" style="background-color: var(--accent-light); color: var(--accent);">Pattern Verification Manager</span>
      </div>
      <p class="text-ink-muted small mb-3">
        Edit selection rounds, exam assessment pattern, syllabus topics, difficulty tier, and <strong>last checked date</strong>. All patterns automatically display <em>"Reported pattern, please verify"</em> to ensure factual transparency with students.
      </p>

      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead>
            <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
              <th class="font-serif text-ink">Company</th>
              <th class="font-serif text-ink">Category / Tier</th>
              <th class="font-serif text-ink">Test Pattern (Reported)</th>
              <th class="font-serif text-ink">Last Checked</th>
              <th class="font-serif text-ink text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allCompanyProfiles as $cp): ?>
              <tr>
                <td>
                  <strong class="text-ink"><?= htmlspecialchars($cp['name']) ?></strong>
                  <div class="text-ink-muted extra-small"><?= htmlspecialchars($cp['slug']) ?></div>
                </td>
                <td>
                  <span class="badge bg-secondary"><?= htmlspecialchars($cp['category']) ?></span><br>
                  <span class="badge bg-light text-dark border"><?= ucfirst($cp['difficulty_tier']) ?></span>
                </td>
                <td>
                  <div class="text-ink small text-truncate" style="max-width: 320px;"><?= htmlspecialchars($cp['test_pattern']) ?></div>
                  <div class="text-ink-muted extra-small text-truncate" style="max-width: 320px;">Focus: <?= htmlspecialchars($cp['syllabus']) ?></div>
                </td>
                <td>
                  <span class="badge bg-body-tertiary text-ink border"><?= htmlspecialchars($cp['last_checked'] ?? '2026-10-18') ?></span>
                </td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" onclick='openEditCompanyModal(<?= json_encode($cp, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                    <i class="bi bi-pencil-square me-1"></i> Edit
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>

    <!-- Human Review Queue Card for Conceptual Questions -->
    <?php 
      $uncheckedList = AdminController::getUncheckedQuestions('', 15);
      $totalUnchecked = (int)Database::getConnection()->query("SELECT COUNT(*) FROM questions WHERE verification_level = 'unchecked' AND is_published = 1")->fetchColumn();
    ?>
    <div class="card card-gamified p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-serif fw-bold text-ink mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-clipboard-check-fill" style="color: var(--accent);"></i> Human Verification Review Queue (<?= $totalUnchecked ?> Unreviewed)
        </h5>
        <span class="badge py-1 px-3 rounded-pill bg-warning text-dark border">Pending Human Audit</span>
      </div>
      <p class="text-ink-muted small mb-3">
        Conceptual questions (OS, DBMS, networking, OOP, and verbal comprehension) cannot be executed via compiler or solved by arithmetic formulas. They are labeled <code>unchecked</code> until audited by an administrator. Download the per-company CSV review files below or mark questions reviewed directly from this queue.
      </p>

      <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
        <span class="fw-semibold small text-ink me-1"><i class="bi bi-file-earmark-spreadsheet me-1"></i> Download Review CSVs:</span>
        <?php foreach ($allCompanyProfiles as $cp): 
          $slug = strtolower(preg_replace('/[^a-z0-9]+/', '_', trim($cp['name'])));
          $csvPath = "exports/needs_human_review_{$slug}.csv";
        ?>
          <a href="<?= $csvPath ?>" download class="badge bg-light text-ink text-decoration-none border p-2" title="Download <?= htmlspecialchars($cp['name']) ?> Review CSV">
            <i class="bi bi-download me-1"></i><?= htmlspecialchars($cp['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div id="reviewAlert" class="alert d-none py-2 small mb-3"></div>

      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead>
            <tr style="background-color: var(--surface); border-bottom: 1px solid var(--border);">
              <th class="font-serif text-ink" style="width: 80px;">ID</th>
              <th class="font-serif text-ink" style="width: 140px;">Company</th>
              <th class="font-serif text-ink">Question &amp; Answer</th>
              <th class="font-serif text-ink" style="width: 130px;">Verification</th>
              <th class="font-serif text-ink text-end" style="width: 120px;">Action</th>
            </tr>
          </thead>
          <tbody id="reviewTableBody">
            <?php if (empty($uncheckedList)): ?>
              <tr><td colspan="5" class="text-center py-4 text-ink-muted">All published questions have been verified or reviewed! Zero pending questions in queue.</td></tr>
            <?php else: ?>
              <?php foreach ($uncheckedList as $uq): ?>
                <tr id="review-row-<?= $uq['id'] ?>">
                  <td><span class="font-mono small text-ink fw-bold">#<?= $uq['id'] ?></span></td>
                  <td>
                    <span class="badge bg-body-tertiary text-ink border"><?= htmlspecialchars($uq['company_name'] ?: 'General') ?></span>
                    <div class="extra-small text-ink-muted"><?= htmlspecialchars($uq['subtopic_name'] ?? 'General') ?></div>
                  </td>
                  <td>
                    <div class="text-ink small fw-medium"><?= htmlspecialchars($uq['question_text']) ?></div>
                    <div class="text-ink-muted extra-small mt-1">
                      <strong>Correct (<?= $uq['correct_option'] ?>):</strong> <?= htmlspecialchars($uq['option_' . strtolower($uq['correct_option'])] ?? '') ?> &bull; 
                      <em><?= htmlspecialchars(substr($uq['explanation'] ?? '', 0, 90)) ?>...</em>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-warning text-dark border">unchecked</span>
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-success fw-semibold" onclick="markReviewed(<?= $uq['id'] ?>)">
                      <i class="bi bi-check-lg me-1"></i> Verify
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>


    <!-- Company Profile Edit Modal -->
    <div class="modal fade" id="companyEditModal" tabindex="-1" aria-labelledby="companyEditModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: var(--surface-bg); color: var(--surface-text); border: 1px solid var(--border);">
          <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
            <h5 class="modal-title font-serif fw-bold text-ink" id="companyEditModalLabel">Edit Company Placement Pattern</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editCompanyForm">
              <input type="hidden" id="editCompanyId" name="id">
              <div class="mb-3">
                <label class="form-label text-ink fw-semibold">Company Name</label>
                <input type="text" id="editCompanyName" class="form-control" readonly disabled>
              </div>
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label text-ink fw-semibold">Category</label>
                  <input type="text" id="editCompanyCategory" class="form-control" required>
                </div>
                <div class="col-md-3">
                  <label class="form-label text-ink fw-semibold">Difficulty Tier</label>
                  <select id="editCompanyTier" class="form-select">
                    <option value="standard">Standard</option>
                    <option value="elevated">Elevated</option>
                    <option value="premium">Premium</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label text-ink fw-semibold">Last Checked Date</label>
                  <input type="date" id="editCompanyLastChecked" class="form-control" value="2026-10-18" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label text-ink fw-semibold">Assessment Test Pattern</label>
                <textarea id="editCompanyPattern" class="form-control" rows="3" required></textarea>
                <div class="form-text extra-small">"(Reported pattern, please verify)" will be appended automatically if omitted.</div>
              </div>
              <div class="mb-3">
                <label class="form-label text-ink fw-semibold">Syllabus & Focus Areas</label>
                <textarea id="editCompanySyllabus" class="form-control" rows="2" required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label text-ink fw-semibold">Selection Rounds (One per line)</label>
                <textarea id="editCompanyRounds" class="form-control" rows="3" placeholder="Round 1&#10;Round 2&#10;Round 3"></textarea>
              </div>
              <div id="companyEditAlert" class="alert d-none py-2 small mb-0"></div>
            </form>
          </div>
          <div class="modal-footer border-top" style="border-color: var(--border) !important;">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm fw-bold px-3" onclick="saveCompanyProfile()">Save Changes</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Coupons Modal -->
    <div class="modal fade" id="bulkCouponModal" tabindex="-1" aria-labelledby="bulkCouponModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content card-gamified border-2 p-0 overflow-hidden" style="background-color: var(--surface-bg, #fff);">
          <div class="modal-header border-bottom p-3" style="border-color: var(--border) !important;">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-boxes fs-4" style="color: var(--marker-orange);"></i>
              <h5 class="modal-title font-serif fw-bold text-ink mb-0" id="bulkCouponModalLabel">Bulk Generate Coupons</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="min-height: 44px; min-width: 44px;"></button>
          </div>
          <div class="modal-body p-3 p-sm-4">
            <form id="bulkCouponForm" onsubmit="handleBulkGenerate(event)">
              <div class="row g-3 mb-3">
                <div class="col-6 col-md-3">
                  <label class="form-label fw-bold text-ink small mb-1">Quantity</label>
                  <input type="number" class="form-control form-control-gamified" id="bulkQuantity" name="quantity" value="10" min="1" max="500" required style="min-height: 44px; font-size: 16px;">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label fw-bold text-ink small mb-1">Code Prefix</label>
                  <input type="text" class="form-control form-control-gamified text-uppercase font-mono" id="bulkPrefix" name="prefix" value="APT-" maxlength="8" style="min-height: 44px; font-size: 16px;">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label fw-bold text-ink small mb-1">Discount Type</label>
                  <select class="form-select form-control-gamified" id="bulkDiscountType" name="discount_type" style="min-height: 44px; font-size: 16px;">
                    <option value="percent">Percent (%)</option>
                    <option value="fixed">Fixed Coins</option>
                  </select>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label fw-bold text-ink small mb-1">Discount Value</label>
                  <input type="number" class="form-control form-control-gamified" id="bulkDiscountValue" name="discount_value" value="25" min="1" required style="min-height: 44px; font-size: 16px;">
                </div>
                <div class="col-6 col-md-4">
                  <label class="form-label fw-bold text-ink small mb-1">Max Cap (Coins)</label>
                  <input type="number" class="form-control form-control-gamified" id="bulkMaxDiscount" name="max_discount" placeholder="Optional" min="1" style="min-height: 44px; font-size: 16px;">
                </div>
                <div class="col-6 col-md-4">
                  <label class="form-label fw-bold text-ink small mb-1">Total Uses / Code</label>
                  <input type="number" class="form-control form-control-gamified" id="bulkTotalLimit" name="total_limit" value="1" min="1" style="min-height: 44px; font-size: 16px;">
                </div>
                <div class="col-12 col-md-4">
                  <label class="form-label fw-bold text-ink small mb-1">Expiry Date</label>
                  <input type="datetime-local" class="form-control form-control-gamified" id="bulkExpiresAt" name="expires_at" style="min-height: 44px; font-size: 16px;">
                </div>
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-gamified fw-bold" data-bs-dismiss="modal" style="min-height: 44px;">Cancel</button>
                <button type="submit" class="btn btn-primary-gamified fw-bold px-4" id="btnSubmitBulk" style="min-height: 44px;">
                  <i class="bi bi-magic me-1"></i> Generate N Codes
                </button>
              </div>
            </form>

            <div id="bulkResultArea" class="mt-4 d-none">
              <hr style="border-color: var(--border);">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-ink font-serif"><i class="bi bi-check-circle-fill text-success me-1"></i> Generated Codes (<span id="bulkGeneratedCount">0</span>):</strong>
                <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" style="min-height: 44px;" id="btnDownloadBulkCSV">
                  <i class="bi bi-download me-1"></i> Download CSV
                </button>
              </div>
              <div class="p-2 border rounded-3 font-mono extra-small" style="max-height: 180px; overflow-y: auto; background-color: var(--surface);" id="bulkCodesList">
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Redemption History Modal -->
    <div class="modal fade" id="couponRedemptionsModal" tabindex="-1" aria-labelledby="couponRedemptionsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content card-gamified border-2 p-0 overflow-hidden" style="background-color: var(--surface-bg, #fff);">
          <div class="modal-header border-bottom p-3" style="border-color: var(--border) !important;">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-clock-history fs-4" style="color: var(--marker-orange);"></i>
              <h5 class="modal-title font-serif fw-bold text-ink mb-0" id="couponRedemptionsModalLabel">Redemption History</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="min-height: 44px; min-width: 44px;"></button>
          </div>
          <div class="modal-body p-3 p-sm-4">
            <div class="table-responsive border rounded-3" style="max-height: 350px; overflow-y: auto;">
              <table class="table table-hover mb-0 align-middle">
                <thead style="background-color: var(--surface);">
                  <tr>
                    <th class="font-serif text-ink">User</th>
                    <th class="font-serif text-ink">Item</th>
                    <th class="font-serif text-ink">Saved</th>
                    <th class="font-serif text-ink">Paid</th>
                    <th class="font-serif text-ink text-end">Time</th>
                  </tr>
                </thead>
                <tbody id="redemptionsTableBody">
                  <tr><td colspan="5" class="text-center py-4 text-ink-muted">Loading redemptions...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  
    <!-- Modal: Gift Speed Coins (Single User) -->
    <div class="modal fade" id="giftCoinsModal" tabindex="-1" aria-labelledby="giftCoinsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-gamified border-0">
          <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
            <div class="d-flex align-items-center gap-2">
              <div class="p-2 rounded-circle" style="background-color: #fef3c7; color: #d97706;">
                <i class="bi bi-gift-fill fs-5"></i>
              </div>
              <div>
                <h5 class="font-serif fw-bold text-ink mb-0" id="giftCoinsModalLabel">Gift Speed Coins</h5>
                <div class="extra-small text-ink-muted">Award complimentary coins directly to user wallet</div>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="giftCoinsForm" onsubmit="handleGiftCoinsSubmit(event)">
            <input type="hidden" id="giftUserId" name="user_id">
            <input type="hidden" id="giftCurrentCoins" value="0">
            <div class="modal-body p-3 p-sm-4">
              
              <!-- Recipient Card -->
              <div class="p-3 rounded-3 mb-3 border d-flex justify-content-between align-items-center" style="background-color: var(--surface); border-color: var(--border) !important;">
                <div>
                  <div class="fw-bold text-ink" id="giftUserName">User Name</div>
                  <div class="extra-small text-ink-muted" id="giftUserMeta">ID #0 &bull; user@example.com</div>
                </div>
                <div class="text-end">
                  <div class="extra-small text-ink-muted text-uppercase fw-bold">Current Balance</div>
                  <div class="fw-bold" style="color: #f59e0b;" id="giftUserCurrentDisplay"><i class="bi bi-coin me-1"></i>0 Coins</div>
                </div>
              </div>

              <!-- Quick Presets -->
              <label class="form-label fw-bold text-ink small mb-1">Select Gift Amount</label>
              <div class="d-flex flex-wrap gap-2 mb-2">
                <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="setGiftCoinAmount(50)">+50</button>
                <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="setGiftCoinAmount(100)">+100</button>
                <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="setGiftCoinAmount(250)">+250</button>
                <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="setGiftCoinAmount(500)">+500</button>
                <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="setGiftCoinAmount(1000)">+1,000</button>
              </div>

              <!-- Custom Amount Input -->
              <div class="input-group mb-3">
                <span class="input-group-text bg-transparent text-ink-muted" style="border-color: var(--border) !important;">
                  <i class="bi bi-coin" style="color: #f59e0b;"></i>
                </span>
                <input type="number" class="form-control form-control-gamified" id="giftCoinAmount" name="amount" min="1" max="10000" placeholder="Custom amount (e.g. 150)" required oninput="updateGiftPreview()" style="min-height: 44px; font-size: 16px;">
                <span class="input-group-text bg-transparent text-ink-muted" style="border-color: var(--border) !important;">Coins</span>
              </div>

              <!-- Reason Presets & Note -->
              <label class="form-label fw-bold text-ink small mb-1">Reason / Note for User</label>
              <select class="form-select form-control-gamified mb-2" id="giftReasonSelect" onchange="setGiftReason(this.value)" style="min-height: 42px;">
                <option value="">Select reason template...</option>
                <option value="Weekly Challenge Prize">Weekly Challenge Prize</option>
                <option value="Contest / Mock Exam Winner">Contest / Mock Exam Winner</option>
                <option value="Platform Bug Bounty / Good Samaritan">Platform Bug Bounty / Good Samaritan</option>
                <option value="Technical Support Compensation">Technical Support Compensation</option>
                <option value="Community Appreciation Gift">Community Appreciation Gift</option>
                <option value="custom">Other / Custom note...</option>
              </select>
              <input type="text" class="form-control form-control-gamified" id="giftReasonCustom" name="reason" placeholder="Type reason for crediting coins (required)" required maxlength="255" style="min-height: 42px;">

              <!-- Live Balance Calculator Banner -->
              <div class="alert mt-3 mb-0 p-2.5 rounded-3 d-flex justify-content-between align-items-center extra-small border" style="background-color: var(--surface); border-color: var(--border) !important;">
                <span class="text-ink-muted">New Balance Preview:</span>
                <span class="fw-bold" style="color: #10b981;" id="giftNewBalancePreview">0 Coins (+0)</span>
              </div>

            </div>
            <div class="modal-footer border-top p-3" style="border-color: var(--border) !important;">
              <button type="button" class="btn btn-outline-gamified" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" id="btnSubmitGift" class="btn btn-primary-gamified fw-bold px-4">
                <i class="bi bi-send-fill me-1"></i> Confirm &amp; Gift Coins
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal: Bulk Gift Speed Coins -->
    <div class="modal fade" id="bulkGiftCoinsModal" tabindex="-1" aria-labelledby="bulkGiftCoinsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-gamified border-0">
          <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
            <div class="d-flex align-items-center gap-2">
              <div class="p-2 rounded-circle" style="background-color: #dbeafe; color: #2563eb;">
                <i class="bi bi-boxes fs-5"></i>
              </div>
              <div>
                <h5 class="font-serif fw-bold text-ink mb-0" id="bulkGiftCoinsModalLabel">Bulk Gift Speed Coins</h5>
                <div class="extra-small text-ink-muted">Reward groups of users by tier or batch list</div>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="bulkGiftCoinsForm" onsubmit="handleBulkGiftSubmit(event)">
            <div class="modal-body p-3 p-sm-4">
              
              <!-- Recipient Mode -->
              <div class="mb-3">
                <label class="form-label fw-bold text-ink small mb-1">Target Group</label>
                <div class="row g-2">
                  <div class="col-6">
                    <div class="form-check p-2 border rounded-3" style="background-color: var(--surface); border-color: var(--border) !important;">
                      <input class="form-check-input ms-1" type="radio" name="bulk_target_type" id="targetTierRadio" value="tier" checked onchange="toggleBulkTargetMode('tier')">
                      <label class="form-check-label ms-1 small fw-semibold text-ink" for="targetTierRadio">
                        By Tier Group
                      </label>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-check p-2 border rounded-3" style="background-color: var(--surface); border-color: var(--border) !important;">
                      <input class="form-check-input ms-1" type="radio" name="bulk_target_type" id="targetListRadio" value="list" onchange="toggleBulkTargetMode('list')">
                      <label class="form-check-label ms-1 small fw-semibold text-ink" for="targetListRadio">
                        By User IDs / Emails
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tier Selection -->
              <div id="bulkTierContainer" class="mb-3">
                <label class="form-label fw-bold text-ink small mb-1">Target User Tier</label>
                <select class="form-select form-control-gamified" id="bulkTargetTier" style="min-height: 42px;">
                  <option value="Diamond">Diamond Tier (Top performers)</option>
                  <option value="Platinum">Platinum Tier</option>
                  <option value="Gold">Gold Tier</option>
                  <option value="Silver">Silver Tier</option>
                  <option value="Bronze">Bronze Tier</option>
                </select>
              </div>

              <!-- List Selection -->
              <div id="bulkListContainer" class="mb-3 d-none">
                <label class="form-label fw-bold text-ink small mb-1">Recipient User IDs or Emails</label>
                <textarea class="form-control form-control-gamified" id="bulkTargetList" rows="3" placeholder="e.g. 101, 102, john@example.com, alice@example.com"></textarea>
                <div class="form-text extra-small text-ink-muted">Comma, space, or newline separated.</div>
              </div>

              <!-- Coins Amount -->
              <div class="mb-3">
                <label class="form-label fw-bold text-ink small mb-1">Coins Per Recipient</label>
                <div class="d-flex flex-wrap gap-2 mb-2">
                  <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="document.getElementById('bulkCoinAmount').value = 50">+50</button>
                  <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="document.getElementById('bulkCoinAmount').value = 100">+100</button>
                  <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="document.getElementById('bulkCoinAmount').value = 250">+250</button>
                  <button type="button" class="btn btn-sm btn-outline-gamified fw-bold" onclick="document.getElementById('bulkCoinAmount').value = 500">+500</button>
                </div>
                <input type="number" class="form-control form-control-gamified" id="bulkCoinAmount" min="1" max="10000" placeholder="e.g. 100" required style="min-height: 44px; font-size: 16px;">
              </div>

              <!-- Reason -->
              <div class="mb-2">
                <label class="form-label fw-bold text-ink small mb-1">Campaign Note / Reason</label>
                <input type="text" class="form-control form-control-gamified" id="bulkReason" placeholder="e.g. Weekend Leaderboard Milestone Grant" required maxlength="255" style="min-height: 42px;">
              </div>

            </div>
            <div class="modal-footer border-top p-3" style="border-color: var(--border) !important;">
              <button type="button" class="btn btn-outline-gamified" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" id="btnSubmitBulkGift" class="btn btn-primary-gamified fw-bold px-4">
                <i class="bi bi-send-check-fill me-1"></i> Execute Bulk Gift
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal: Edit User & Manage Attributes -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-gamified border-0">
          <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
            <div class="d-flex align-items-center gap-2">
              <div class="p-2 rounded-circle" style="background-color: var(--accent-light); color: var(--accent);">
                <i class="bi bi-person-gear fs-5"></i>
              </div>
              <div>
                <h5 class="font-serif fw-bold text-ink mb-0" id="editUserModalLabel">Manage User Profile</h5>
                <div class="extra-small text-ink-muted">Configure role, plan, tier, and credentials</div>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="editUserForm" onsubmit="handleEditUserSubmit(event)">
            <input type="hidden" id="editUserId" name="user_id">
            <div class="modal-body p-3 p-sm-4">
              
              <div class="p-3 rounded-3 mb-3 border" style="background-color: var(--surface); border-color: var(--border) !important;">
                <div class="fw-bold text-ink" id="editUserName">User Name</div>
                <div class="extra-small text-ink-muted" id="editUserEmail">user@example.com</div>
              </div>

              <div class="row g-3">
                <div class="col-6">
                  <label class="form-label fw-bold text-ink small mb-1">Account Role</label>
                  <select class="form-select form-control-gamified" id="editUserRole" name="role" style="min-height: 42px;">
                    <option value="user">Student (User)</option>
                    <option value="admin">Administrator</option>
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label fw-bold text-ink small mb-1">Subscription Plan</label>
                  <select class="form-select form-control-gamified" id="editUserPlan" name="plan" style="min-height: 42px;">
                    <option value="free">Free Tier</option>
                    <option value="premium">Premium Pro</option>
                  </select>
                </div>
                <div class="col-12 col-sm-6">
                  <label class="form-label fw-bold text-ink small mb-1">Mastery Tier</label>
                  <select class="form-select form-control-gamified" id="editUserTier" name="tier" style="min-height: 42px;">
                    <option value="Bronze">Bronze</option>
                    <option value="Silver">Silver</option>
                    <option value="Gold">Gold</option>
                    <option value="Platinum">Platinum</option>
                    <option value="Diamond">Diamond</option>
                  </select>
                </div>
                <div class="col-6 col-sm-3">
                  <label class="form-label fw-bold text-ink small mb-1">Freezes</label>
                  <input type="number" class="form-control form-control-gamified" id="editUserFreezes" name="streak_freezes" min="0" max="20" style="min-height: 42px;">
                </div>
                <div class="col-6 col-sm-3">
                  <label class="form-label fw-bold text-ink small mb-1">Streak</label>
                  <input type="number" class="form-control form-control-gamified" id="editUserStreak" name="current_streak" min="0" max="1000" style="min-height: 42px;">
                </div>
              </div>

              <!-- Password Reset Accordion / Section -->
              <div class="mt-4 pt-3 border-top" style="border-color: var(--border) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="fw-bold small text-ink"><i class="bi bi-key-fill me-1 text-warning"></i> Reset Password</span>
                  <button type="button" class="btn btn-sm btn-outline-warning fw-semibold" onclick="handleResetPassword()">
                    Generate Temporary Password
                  </button>
                </div>
                <div id="resetPasswordResult" class="alert alert-warning extra-small mb-0 d-none py-2"></div>
              </div>

            </div>
            <div class="modal-footer border-top p-3" style="border-color: var(--border) !important;">
              <button type="button" class="btn btn-outline-gamified" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" id="btnSubmitEditUser" class="btn btn-primary-gamified fw-bold px-4">
                <i class="bi bi-check2-circle me-1"></i> Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal: Inspect User Details & Activity -->
    <div class="modal fade" id="inspectUserModal" tabindex="-1" aria-labelledby="inspectUserModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content card-gamified border-0">
          <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
            <div class="d-flex align-items-center gap-2">
              <div class="p-2 rounded-circle" style="background-color: var(--accent-light); color: var(--accent);">
                <i class="bi bi-person-badge fs-5"></i>
              </div>
              <div>
                <h5 class="font-serif fw-bold text-ink mb-0" id="inspectUserModalLabel">User Audit &amp; Activity Inspector</h5>
                <div class="extra-small text-ink-muted">Detailed account history, quiz logs, and coin transactions</div>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-3 p-sm-4">
            
            <!-- Quick Profile Header -->
            <div class="p-3 rounded-3 mb-3 border" style="background-color: var(--surface); border-color: var(--border) !important;" id="inspectHeaderBox">
              <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading user profile...
            </div>

            <!-- Tabs: Recent Quiz Attempts & Coin Transactions -->
            <ul class="nav nav-pills mb-3 gap-2" id="inspectTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-3 py-1.5 extra-small fw-bold" id="tab-attempts" data-bs-toggle="pill" data-bs-target="#content-attempts" type="button" role="tab">
                  <i class="bi bi-journal-check me-1"></i> Recent Attempts (<span id="inspectAttemptsCount">0</span>)
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-1.5 extra-small fw-bold" id="tab-coins" data-bs-toggle="pill" data-bs-target="#content-coins" type="button" role="tab">
                  <i class="bi bi-coin me-1"></i> Coin Ledger (<span id="inspectCoinsCount">0</span>)
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-1.5 extra-small fw-bold" id="tab-unlocks" data-bs-toggle="pill" data-bs-target="#content-unlocks" type="button" role="tab">
                  <i class="bi bi-bag-check me-1"></i> Unlocked Items (<span id="inspectUnlocksCount">0</span>)
                </button>
              </li>
            </ul>

            <div class="tab-content" id="inspectTabsContent">
              <!-- Attempts Tab -->
              <div class="tab-pane fade show active" id="content-attempts" role="tabpanel">
                <div class="table-responsive border rounded-3" style="max-height: 280px; overflow-y: auto;">
                  <table class="table table-hover mb-0 align-middle extra-small">
                    <thead style="background-color: var(--surface);">
                      <tr>
                        <th class="font-serif text-ink">Q#</th>
                        <th class="font-serif text-ink">Question</th>
                        <th class="font-serif text-ink">Result</th>
                        <th class="font-serif text-ink">Rewards</th>
                        <th class="font-serif text-ink text-end">Date</th>
                      </tr>
                    </thead>
                    <tbody id="inspectAttemptsTableBody">
                      <tr><td colspan="5" class="text-center py-3 text-ink-muted">No attempts logged yet.</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Coin Transactions Tab -->
              <div class="tab-pane fade" id="content-coins" role="tabpanel">
                <div class="table-responsive border rounded-3" style="max-height: 280px; overflow-y: auto;">
                  <table class="table table-hover mb-0 align-middle extra-small">
                    <thead style="background-color: var(--surface);">
                      <tr>
                        <th class="font-serif text-ink">Amount</th>
                        <th class="font-serif text-ink">Type</th>
                        <th class="font-serif text-ink">Description</th>
                        <th class="font-serif text-ink text-end">Date</th>
                      </tr>
                    </thead>
                    <tbody id="inspectCoinsTableBody">
                      <tr><td colspan="4" class="text-center py-3 text-ink-muted">No coin transactions yet.</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Unlocked Items Tab -->
              <div class="tab-pane fade" id="content-unlocks" role="tabpanel">
                <div class="table-responsive border rounded-3" style="max-height: 280px; overflow-y: auto;">
                  <table class="table table-hover mb-0 align-middle extra-small">
                    <thead style="background-color: var(--surface);">
                      <tr>
                        <th class="font-serif text-ink">Code</th>
                        <th class="font-serif text-ink">Title</th>
                        <th class="font-serif text-ink">Type</th>
                        <th class="font-serif text-ink text-end">Unlocked At</th>
                      </tr>
                    </thead>
                    <tbody id="inspectUnlocksTableBody">
                      <tr><td colspan="4" class="text-center py-3 text-ink-muted">No unlocked store items.</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
          <div class="modal-footer border-top p-3" style="border-color: var(--border) !important;">
            <button type="button" class="btn btn-outline-gamified" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Merge College into Another (Feature 3) -->
    <div class="modal fade" id="mergeCollegeModal" tabindex="-1" aria-labelledby="mergeCollegeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border-color: var(--border) !important;">
          <div class="modal-header border-bottom" style="border-color: var(--border) !important;">
            <h5 class="modal-title font-serif fw-bold" id="mergeCollegeModalLabel">
              <i class="bi bi-arrows-collapse me-2" style="color: var(--accent);"></i> Merge Duplicate College
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="small text-ink-muted mb-3">
              Merging moves all enrolled students from the source college into a target destination college, recalculating leaderboards with zero orphan records, then permanently removes the source duplicate.
            </p>
            <div class="p-2.5 rounded-3 mb-3 border bg-body-tertiary">
              <div class="extra-small fw-bold text-ink-muted text-uppercase mb-1">Source College (To Be Removed)</div>
              <div class="fw-bold text-danger" id="mergeSourceCollegeName">--</div>
              <input type="hidden" id="mergeSourceCollegeId" value="0">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold text-ink-muted" for="mergeTargetCollegeSelect">Select Destination Target College</label>
              <select id="mergeTargetCollegeSelect" class="form-select form-control-gamified" style="min-height: 42px;">
                <option value="">-- Choose Target College --</option>
                <?php foreach ($adminColleges as $ac): ?>
                  <option value="<?= (int)$ac['id'] ?>"><?= htmlspecialchars($ac['name']) ?> (<?= htmlspecialchars($ac['city']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer border-top" style="border-color: var(--border) !important;">
            <button type="button" class="btn btn-outline-gamified btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger btn-sm fw-bold px-3" id="btnConfirmMerge" onclick="submitAdminMergeColleges()">
              Execute Merge <i class="bi bi-arrow-right-short ms-1"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
</div>
</div>

<script>
function filterAdminCollegesTable(status) {
    const rows = document.querySelectorAll('#collegesTableBody tr');
    rows.forEach(r => {
        if (status === 'all' || r.getAttribute('data-status') === status) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

function handleAdminApproveCollege(collegeId) {
    if (!confirm('Approve this college and make it available in registration/profile dropdowns?')) return;
    fetch('index.php?action=api_admin_approve_college', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ college_id: collegeId, csrf_token: window.CSRF_TOKEN || '' })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') loadAdminColleges();
    });
}

function handleAdminRejectCollege(collegeId) {
    if (!confirm('Reject this college request?')) return;
    fetch('index.php?action=api_admin_reject_college', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ college_id: collegeId, csrf_token: window.CSRF_TOKEN || '' })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') loadAdminColleges();
    });
}

function openMergeCollegeModal(collegeId, collegeName) {
    document.getElementById('mergeSourceCollegeId').value = collegeId;
    document.getElementById('mergeSourceCollegeName').innerText = collegeName;
    const select = document.getElementById('mergeTargetCollegeSelect');
    Array.from(select.options).forEach(opt => {
        opt.disabled = (parseInt(opt.value) === collegeId);
    });
    select.value = '';
    const modal = new bootstrap.Modal(document.getElementById('mergeCollegeModal'));
    modal.show();
}

function submitAdminMergeColleges() {
    const fromId = parseInt(document.getElementById('mergeSourceCollegeId').value);
    const toId = parseInt(document.getElementById('mergeTargetCollegeSelect').value);
    if (!fromId || !toId) {
        alert('Please select a valid destination target college.');
        return;
    }
    if (!confirm('Confirm permanent college merge? All student accounts will be reassigned immediately.')) return;

    fetch('index.php?action=api_admin_merge_colleges', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ from_college_id: fromId, to_college_id: toId, csrf_token: window.CSRF_TOKEN || '' })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            const modalEl = document.getElementById('mergeCollegeModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            loadAdminColleges();
        }
    });
}

function loadAdminColleges() {
    fetch('index.php?action=api_admin_get_colleges')
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'success' || !data.colleges) return;
        const tbody = document.getElementById('collegesTableBody');
        tbody.innerHTML = '';
        data.colleges.forEach(col => {
            const tr = document.createElement('tr');
            tr.setAttribute('data-status', col.status);
            let statusBadge = '<span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">Rejected</span>';
            if (col.status === 'active') statusBadge = '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Active</span>';
            if (col.status === 'pending') statusBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Pending Review</span>';

            let actions = '';
            if (col.status === 'pending') {
                actions += '<button type="button" class="btn btn-sm btn-outline-success px-2 py-1" onclick="handleAdminApproveCollege(' + col.id + ')"><i class="bi bi-check-lg"></i> Approve</button> ';
                actions += '<button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="handleAdminRejectCollege(' + col.id + ')"><i class="bi bi-x-lg"></i> Reject</button> ';
            }
            const safeName = (col.name || '').replace(/'/g, "\\'");
            actions += '<button type="button" class="btn btn-sm btn-outline-gamified px-2 py-1" onclick="openMergeCollegeModal(' + col.id + ', \'' + safeName + '\')"><i class="bi bi-arrows-collapse"></i> Merge</button>';

            tr.innerHTML = `
                <td class="ps-3 text-ink-muted small">#${col.id}</td>
                <td><code>${col.code || ''}</code></td>
                <td class="fw-semibold text-ink">${col.name || ''}</td>
                <td class="text-ink-muted small">${col.city || ''}, ${col.state || ''}</td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center fw-bold text-ink">${col.student_count || 0}</td>
                <td class="pe-3 text-end"><div class="d-inline-flex gap-1">${actions}</div></td>
            `;
            tbody.appendChild(tr);
        });
    });
}

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

async function uploadBatchFile(e) {
  e.preventDefault();
  const fileInput = document.getElementById('batchFileInput');
  const dryRunCheck = document.getElementById('dryRunCheckbox');
  const alertBox = document.getElementById('batchImportAlert');
  const submitBtn = document.getElementById('batchSubmitBtn');
  const progressContainer = document.getElementById('batchProgressContainer');
  const progressBar = document.getElementById('batchProgressBar');
  const progressStatus = document.getElementById('batchProgressStatus');
  const progressPercent = document.getElementById('batchProgressPercent');

  if (!fileInput.files.length) return;
  const file = fileInput.files[0];
  const isDryRun = dryRunCheck.checked;

  submitBtn.disabled = true;
  alertBox.classList.add('d-none');
  progressContainer.classList.remove('d-none');
  progressBar.style.width = '0%';
  progressPercent.textContent = '0%';
  progressStatus.textContent = 'Reading question bank file...';

  const batchId = 'BATCH_' + Date.now().toString(36).toUpperCase() + '_' + Math.random().toString(36).substring(2, 6).toUpperCase();

  try {
    // If JSON file, chunk client-side in batches of 50
    if (file.name.endsWith('.json')) {
      const text = await file.text();
      let questions = JSON.parse(text);
      if (!Array.isArray(questions)) {
        throw new Error('Invalid JSON: Root element must be an array of question objects.');
      }

      const total = questions.length;
      const chunkSize = 50;
      let totalImported = 0;
      let totalPublished = 0;
      let totalDraft = 0;
      let totalDuplicates = 0;

      for (let i = 0; i < total; i += chunkSize) {
        const chunk = questions.slice(i, i + chunkSize);
        const chunkNum = Math.floor(i / chunkSize) + 1;
        const totalChunks = Math.ceil(total / chunkSize);

        progressStatus.textContent = `Processing chunk ${chunkNum} of ${totalChunks} (${chunk.length} questions)...`;
        const pct = Math.round((i / total) * 100);
        progressBar.style.width = pct + '%';
        progressPercent.textContent = pct + '%';

        const formData = new FormData();
        const blob = new Blob([JSON.stringify(chunk)], { type: 'application/json' });
        formData.append('batch_file', blob, 'chunk.json');
        formData.append('batch_id', batchId);
        if (isDryRun) formData.append('dry_run', '1');

        const res = await fetch('index.php?action=api_admin_import_batch', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (data.status !== 'success') {
          throw new Error(data.message || `Error processing chunk ${chunkNum}`);
        }

        totalImported += (data.imported_count || 0);
        totalPublished += (data.published_count || 0);
        totalDraft += (data.draft_count || 0);
        totalDuplicates += (data.skipped_duplicates || 0);
      }

      progressBar.style.width = '100%';
      progressPercent.textContent = '100%';
      progressStatus.textContent = 'Complete!';

      alertBox.className = 'alert ' + (isDryRun ? 'alert-info' : 'alert-success') + ' mt-3 mb-0 py-2 small';
      alertBox.innerHTML = `<strong>${isDryRun ? '[DRY RUN RESULT]' : 'Batch Ingested Successfully!'}</strong><br>` +
        `Batch ID: <code>${batchId}</code><br>` +
        `Processed: <strong>${total}</strong> | Published: <strong>${totalPublished}</strong> | Draft: <strong>${totalDraft}</strong> | Duplicates Skipped: <strong>${totalDuplicates}</strong>` +
        (!isDryRun ? `<br><span class="text-ink-muted">To rollback this batch run: <code>php scripts/rollback_batch.php ${batchId}</code></span>` : '');
      alertBox.classList.remove('d-none');
      if (!isDryRun) fileInput.value = '';
    } else {
      // CSV file upload directly
      progressStatus.textContent = 'Uploading and processing CSV...';
      progressBar.style.width = '50%';
      progressPercent.textContent = '50%';

      const formData = new FormData();
      formData.append('batch_file', file);
      formData.append('batch_id', batchId);
      if (isDryRun) formData.append('dry_run', '1');

      const res = await fetch('index.php?action=api_admin_import_batch', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();

      progressBar.style.width = '100%';
      progressPercent.textContent = '100%';
      progressStatus.textContent = 'Complete!';

      if (data.status === 'success') {
        alertBox.className = 'alert ' + (isDryRun ? 'alert-info' : 'alert-success') + ' mt-3 mb-0 py-2 small';
        alertBox.innerHTML = `<strong>${isDryRun ? '[DRY RUN RESULT]' : 'Batch Ingested Successfully!'}</strong><br>` +
          `Batch ID: <code>${batchId}</code><br>` +
          `Processed: <strong>${data.total_submitted}</strong> | Published: <strong>${data.published_count || 0}</strong> | Draft: <strong>${data.draft_count || 0}</strong> | Duplicates: <strong>${data.skipped_duplicates}</strong>` +
          (!isDryRun ? `<br><span class="text-ink-muted">To rollback: <code>php scripts/rollback_batch.php ${batchId}</code></span>` : '');
        alertBox.classList.remove('d-none');
        if (!isDryRun) fileInput.value = '';
      } else {
        throw new Error(data.message || 'Error processing CSV');
      }
    }
  } catch (err) {
    alertBox.className = 'alert alert-danger mt-3 mb-0 py-2 small';
    alertBox.textContent = err.message || 'Error uploading file.';
    alertBox.classList.remove('d-none');
  } finally {
    submitBtn.disabled = false;
  }
}

async function approveSubmission(id) {
  const notes = prompt('Enter optional moderator feedback or leave default:', 'Approved by moderator - verified question.');
  if (notes === null) return;

  try {
    const res = await fetch('index.php?action=api_admin_approve_submission', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ submission_id: id, admin_notes: notes })
    });
    const data = await res.json();

    if (data.status === 'success') {
      alert(data.message);
      const row = document.getElementById('submission-row-' + id);
      if (row) {
        row.style.transition = 'all 0.4s ease';
        row.style.opacity = '0';
        setTimeout(() => row.remove(), 400);
      }
    } else {
      alert('Error approving submission: ' + data.message);
    }
  } catch (err) {
    alert('Server network error while approving submission.');
  }
}

async function rejectSubmission(id) {
  const reason = prompt('Enter reason for rejecting this submission (e.g. duplicate, incomplete options, unclear question):');
  if (!reason) return;

  try {
    const res = await fetch('index.php?action=api_admin_reject_submission', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ submission_id: id, admin_notes: reason })
    });
    const data = await res.json();

    if (data.status === 'success') {
      alert('Submission rejected.');
      const row = document.getElementById('submission-row-' + id);
      if (row) {
        row.style.transition = 'all 0.4s ease';
        row.style.opacity = '0';
        setTimeout(() => row.remove(), 400);
      }
    } else {
      alert('Error rejecting submission: ' + data.message);
    }
  } catch (err) {
    alert('Server network error while rejecting submission.');
  }
}

let companyEditModalInstance = null;
function openEditCompanyModal(cp) {
  document.getElementById('editCompanyId').value = cp.id;
  document.getElementById('editCompanyName').value = cp.name;
  document.getElementById('editCompanyCategory').value = cp.category || '';
  document.getElementById('editCompanyTier').value = cp.difficulty_tier || 'standard';
  document.getElementById('editCompanyLastChecked').value = cp.last_checked || '2026-10-18';
  document.getElementById('editCompanyPattern').value = cp.test_pattern || '';
  document.getElementById('editCompanySyllabus').value = cp.syllabus || '';
  
  let rounds = [];
  try {
    rounds = typeof cp.rounds_json === 'string' ? JSON.parse(cp.rounds_json) : (cp.rounds_json || []);
  } catch(e) {
    rounds = [];
  }
  document.getElementById('editCompanyRounds').value = Array.isArray(rounds) ? rounds.join('\n') : '';

  const alertBox = document.getElementById('companyEditAlert');
  alertBox.className = 'alert d-none py-2 small mb-0';

  if (!companyEditModalInstance) {
    companyEditModalInstance = new bootstrap.Modal(document.getElementById('companyEditModal'));
  }
  companyEditModalInstance.show();
}

async function saveCompanyProfile() {
  const alertBox = document.getElementById('companyEditAlert');
  const payload = {
    id: document.getElementById('editCompanyId').value,
    category: document.getElementById('editCompanyCategory').value,
    difficulty_tier: document.getElementById('editCompanyTier').value,
    last_checked: document.getElementById('editCompanyLastChecked').value,
    test_pattern: document.getElementById('editCompanyPattern').value,
    syllabus: document.getElementById('editCompanySyllabus').value,
    rounds: document.getElementById('editCompanyRounds').value
  };

  try {
    const res = await fetch('index.php?action=api_admin_update_company_profile', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.status === 'success') {
      alertBox.className = 'alert alert-success py-2 small mb-0';
      alertBox.textContent = data.message;
      setTimeout(() => {
        companyEditModalInstance.hide();
        location.reload();
      }, 800);
    } else {
      alertBox.className = 'alert alert-danger py-2 small mb-0';
      alertBox.textContent = data.message || 'Failed to update profile';
    }
  } catch (err) {
    alertBox.className = 'alert alert-danger py-2 small mb-0';
    alertBox.textContent = 'Network or server error while updating company profile.';
  }
}

async function markReviewed(questionId) {
  const alertBox = document.getElementById('reviewAlert');
  try {
    const res = await fetch('index.php?action=api_admin_mark_reviewed', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ question_id: questionId })
    });
    const data = await res.json();
    if (data.status === 'success') {
      alertBox.className = 'alert alert-success py-2 small mb-3';
      alertBox.textContent = data.message;
      const row = document.getElementById('review-row-' + questionId);
      if (row) {
        row.style.transition = 'all 0.4s ease';
        row.style.backgroundColor = 'var(--accent-light)';
        row.style.opacity = '0';
        setTimeout(() => row.remove(), 400);
      }
    } else {
      alertBox.className = 'alert alert-danger py-2 small mb-3';
      alertBox.textContent = data.message || 'Error marking question reviewed';
    }
  } catch (err) {
    alertBox.className = 'alert alert-danger py-2 small mb-3';
    alertBox.textContent = 'Network or server error while marking question reviewed.';
  }
}

// ==========================================
// Admin Coupon Management & Promotions JS
// ==========================================

function toggleCreateCouponForm() {
  const form = document.getElementById('createCouponCollapse');
  const txt = document.getElementById('toggleCreateFormText');
  if (form.classList.contains('d-none')) {
    form.classList.remove('d-none');
    txt.textContent = 'Hide Form';
  } else {
    form.classList.add('d-none');
    txt.textContent = 'Create Coupon';
  }
}

function generateSecureClientCode(prefix = 'APT-') {
  const charset = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
  let random = '';
  for (let i = 0; i < 8; i++) {
    random += charset.charAt(Math.floor(Math.random() * charset.length));
    if (i === 3) random += '-';
  }
  return prefix + random;
}

function updateDiscountLabel(type) {
  const lbl = document.getElementById('lblDiscountValue');
  const val = document.getElementById('couponDiscountValue');
  const maxCol = document.getElementById('colMaxDiscount');
  if (type === 'percent') {
    lbl.textContent = 'Discount Percentage (%)';
    val.placeholder = 'e.g. 20';
    val.max = '100';
    if (maxCol) maxCol.classList.remove('d-none');
  } else {
    lbl.textContent = 'Fixed Discount (Coins)';
    val.placeholder = 'e.g. 50';
    val.removeAttribute('max');
    if (maxCol) maxCol.classList.add('d-none');
  }
}

async function handleCreateCoupon(e) {
  e.preventDefault();
  const alertBox = document.getElementById('createCouponAlert');
  const submitBtn = document.getElementById('btnSubmitCoupon');
  const csrfToken = document.getElementById('adminCsrfToken').value;

  const form = e.target;
  const payload = {
    code: form.code.value.trim(),
    discount_type: form.discount_type.value,
    discount_value: parseInt(form.discount_value.value, 10),
    max_discount: form.max_discount.value ? parseInt(form.max_discount.value, 10) : null,
    min_price: form.min_price.value ? parseInt(form.min_price.value, 10) : 0,
    applicable_items: form.applicable_items.value,
    total_limit: form.total_limit.value ? parseInt(form.total_limit.value, 10) : null,
    per_user_limit: parseInt(form.per_user_limit.value, 10) || 1,
    starts_at: form.starts_at.value ? form.starts_at.value.replace('T', ' ') + ':00' : null,
    expires_at: form.expires_at.value ? form.expires_at.value.replace('T', ' ') + ':00' : null,
    allowed_users: form.allowed_users.value.trim() || null,
    is_active: form.is_active.checked ? 1 : 0,
    csrf_token: csrfToken
  };

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

  try {
    const res = await fetch('index.php?action=api_admin_create_coupon', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Save Coupon';

    if (res.status === 200 && data.status === 'success') {
      alertBox.className = 'alert alert-success py-2 small mt-3 mb-0';
      alertBox.textContent = data.message;
      alertBox.classList.remove('d-none');
      setTimeout(() => location.reload(), 900);
    } else {
      alertBox.className = 'alert alert-danger py-2 small mt-3 mb-0';
      alertBox.textContent = data.message || 'Failed to create coupon';
      alertBox.classList.remove('d-none');
    }
  } catch (err) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Save Coupon';
    alertBox.className = 'alert alert-danger py-2 small mt-3 mb-0';
    alertBox.textContent = 'Network or server error creating coupon';
    alertBox.classList.remove('d-none');
  }
}

let lastGeneratedBulkCodes = [];

async function handleBulkGenerate(e) {
  e.preventDefault();
  const form = e.target;
  const submitBtn = document.getElementById('btnSubmitBulk');
  const csrfToken = document.getElementById('adminCsrfToken').value;

  const payload = {
    quantity: parseInt(form.quantity.value, 10),
    prefix: form.prefix.value.trim(),
    discount_type: form.discount_type.value,
    discount_value: parseInt(form.discount_value.value, 10),
    max_discount: form.max_discount.value ? parseInt(form.max_discount.value, 10) : null,
    total_limit: form.total_limit.value ? parseInt(form.total_limit.value, 10) : 1,
    per_user_limit: 1,
    expires_at: form.expires_at.value ? form.expires_at.value.replace('T', ' ') + ':00' : null,
    csrf_token: csrfToken
  };

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating...';

  try {
    const res = await fetch('index.php?action=api_admin_bulk_coupons', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Generate N Codes';

    if (res.status === 200 && data.status === 'success') {
      lastGeneratedBulkCodes = data.codes || [];
      document.getElementById('bulkGeneratedCount').textContent = lastGeneratedBulkCodes.length;
      document.getElementById('bulkCodesList').innerHTML = lastGeneratedBulkCodes.map(c => `<div>${c}</div>`).join('');
      document.getElementById('bulkResultArea').classList.remove('d-none');

      document.getElementById('btnDownloadBulkCSV').onclick = () => {
        const rows = [["Coupon Code", "Discount Type", "Discount Value", "Uses Allowed"]];
        lastGeneratedBulkCodes.forEach(code => {
          rows.push([code, payload.discount_type, payload.discount_value, payload.total_limit]);
        });
        downloadCSVFile("apttitudee_bulk_coupons_" + Date.now() + ".csv", rows);
      };
    } else {
      alert(data.message || 'Failed to bulk generate coupons');
    }
  } catch (err) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Generate N Codes';
    alert('Network error bulk generating coupons.');
  }
}

function downloadCSVFile(filename, rows) {
  const processRow = function (row) {
    let finalVal = '';
    for (let j = 0; j < row.length; j++) {
      let innerValue = row[j] === null ? '' : row[j].toString();
      let result = innerValue.replace(/"/g, '""');
      if (result.search(/("|,|\n)/g) >= 0) result = '"' + result + '"';
      if (j > 0) finalVal += ',';
      finalVal += result;
    }
    return finalVal + '\n';
  };

  let csvFile = '';
  for (let i = 0; i < rows.length; i++) {
    csvFile += processRow(rows[i]);
  }

  const blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement("a");
  const url = URL.createObjectURL(blob);
  link.setAttribute("href", url);
  link.setAttribute("download", filename);
  link.style.visibility = 'hidden';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function exportCouponsToCSV() {
  const rows = [["ID", "Code", "Discount Type", "Discount Value", "Times Used", "Total Limit", "Is Active", "Expires At"]];
  const trs = document.querySelectorAll('#couponsTableBody tr');
  trs.forEach(tr => {
    const code = tr.querySelector('.font-mono')?.innerText || '';
    if (!code) return;
    const tds = tr.querySelectorAll('td');
    rows.push([
      tr.id.replace('coupon-row-', ''),
      code.trim(),
      tds[1]?.innerText.replace(/\n.*/s, '').trim() || '',
      tds[1]?.innerText || '',
      tds[3]?.innerText.split('/')[0]?.trim() || '0',
      tds[3]?.innerText.split('/')[1]?.trim() || '',
      tds[2]?.innerText.trim() || '',
      tds[4]?.innerText.trim() || ''
    ]);
  });
  downloadCSVFile("apttitudee_coupons_export_" + Date.now() + ".csv", rows);
}

async function handleToggleCoupon(couponId) {
  const csrfToken = document.getElementById('adminCsrfToken').value;
  try {
    const res = await fetch('index.php?action=api_admin_toggle_coupon', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ coupon_id: couponId, csrf_token: csrfToken })
    });
    const data = await res.json();
    if (data.status === 'success') {
      location.reload();
    } else {
      alert(data.message || 'Failed to toggle coupon status');
    }
  } catch (err) {
    alert('Network error toggling coupon status');
  }
}

async function viewCouponRedemptions(couponId, code) {
  const modalEl = document.getElementById('couponRedemptionsModal');
  document.getElementById('couponRedemptionsModalLabel').textContent = `Redemption History: ${code}`;
  const tbody = document.getElementById('redemptionsTableBody');
  tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-ink-muted">Loading redemptions...</td></tr>';

  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();

  try {
    const res = await fetch(`index.php?action=api_admin_coupon_redemptions&coupon_id=${couponId}`);
    const data = await res.json();
    if (data.status === 'success' && data.data) {
      if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-ink-muted">No redemptions recorded yet for this coupon.</td></tr>';
      } else {
        tbody.innerHTML = data.data.map(r => `
          <tr>
            <td>
              <strong class="text-ink">${r.user_name}</strong>
              <div class="extra-small text-ink-muted">${r.user_email}</div>
            </td>
            <td><span class="badge bg-body-tertiary text-ink border">${r.item_key}</span></td>
            <td><strong class="text-success font-mono">-${r.discount_amount}</strong></td>
            <td><span class="font-mono text-ink">${r.final_price}</span></td>
            <td class="text-end extra-small text-ink-muted">${r.redeemed_at}</td>
          </tr>
        `).join('');
      }
    } else {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load redemption history.</td></tr>';
    }
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger">Network error loading redemptions.</td></tr>';
  }
}

function handleCouponFilter() {
  const search = document.getElementById('couponSearchInput').value.trim().toLowerCase();
  const status = document.getElementById('couponStatusFilter').value.toLowerCase();

  const desktopRows = document.querySelectorAll('#couponsTableBody tr');
  const mobileCards = document.querySelectorAll('#couponsMobileContainer > div');

  let visibleCount = 0;

  const matchFilter = (code, badgeText) => {
    const matchesSearch = !search || code.includes(search);
    let matchesStatus = true;
    if (status === 'active') matchesStatus = badgeText.includes('active');
    else if (status === 'expired') matchesStatus = badgeText.includes('expired');
    else if (status === 'exhausted') matchesStatus = badgeText.includes('used up');
    else if (status === 'inactive') matchesStatus = badgeText.includes('inactive');
    return matchesSearch && matchesStatus;
  };

  desktopRows.forEach(tr => {
    const code = tr.querySelector('.font-mono')?.innerText.toLowerCase() || '';
    const badge = tr.querySelector('.badge')?.innerText.toLowerCase() || '';
    if (matchFilter(code, badge)) {
      tr.style.display = '';
      visibleCount++;
    } else {
      tr.style.display = 'none';
    }
  });

  mobileCards.forEach(card => {
    const code = card.querySelector('.font-mono')?.innerText.toLowerCase() || '';
    const badge = card.querySelector('.badge')?.innerText.toLowerCase() || '';
    card.style.display = matchFilter(code, badge) ? '' : 'none';
  });

  const countDisplay = document.getElementById('couponCountDisplay');
  if (countDisplay) {
    countDisplay.textContent = `${visibleCount} Coupons`;
  }
}

// ==========================================
// Web-Safe Database Migrations & Rollback JS
// ==========================================

async function runWebMigration() {
  const btn = document.getElementById('btnRunMigration');
  const logBox = document.getElementById('migrationLogContainer');
  const csrfToken = document.getElementById('adminCsrfToken').value;

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Migrating...';
  logBox.innerHTML = '<span class="text-warning">&gt; Starting Master Idempotent Migration...</span>\n';

  try {
    const res = await fetch('index.php?action=api_admin_migrate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ csrf_token: csrfToken })
    });
    const data = await res.json();
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-play-circle-fill me-1"></i> Run Master Idempotent Migration';

    if (data.status === 'success') {
      logBox.innerHTML += `<span class="text-success">${data.log || data.message}</span>\n<span class="text-success">&gt; [OK] Database schema up to date!</span>`;
    } else {
      logBox.innerHTML += `<span class="text-danger">&gt; [ERROR] ${data.message}</span>`;
    }
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-play-circle-fill me-1"></i> Run Master Idempotent Migration';
    logBox.innerHTML += `<span class="text-danger">&gt; [NETWORK ERROR] Could not reach migration endpoint.</span>`;
  }
}

function updateRollbackCodeTarget() {
  const select = document.getElementById('rollbackStepSelect');
  const targetCode = document.getElementById('targetStepCode');
  if (select && targetCode) {
    targetCode.innerText = select.value;
  }
}

async function runWebRollback() {
  const step = document.getElementById('rollbackStepSelect').value;
  const typedStep = document.getElementById('typedRollbackStep').value.trim();
  const confirmed = document.getElementById('confirmRollbackDataLoss').checked;

  if (!confirmed) {
    alert('Security Warning: You must check the confirmation checkbox acknowledging permanent table and data loss.');
    return;
  }

  if (typedStep !== step) {
    alert(`Security Warning: Confirmation failed. You must type "${step}" exactly into the confirmation field.`);
    return;
  }

  if (!confirm(`FINAL WARNING: Are you absolutely certain you want to roll back "${step}"? This action will permanently drop the database table(s) and delete all history.`)) {
    return;
  }

  const btn = document.getElementById('btnRunRollback');
  const logBox = document.getElementById('migrationLogContainer');
  const csrfToken = document.getElementById('adminCsrfToken').value;

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Rolling back...';
  logBox.innerHTML = `<span class="text-warning">&gt; Initiating rollback for step: ${step}...</span>\n`;

  try {
    const res = await fetch('index.php?action=api_admin_rollback', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        step: step,
        csrf_token: csrfToken,
        confirmed: true,
        typed_table: typedStep
      })
    });
    const data = await res.json();
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i> Roll Back Step';

    if (data.status === 'success') {
      logBox.innerHTML += `<span class="text-success">${data.log || data.message}</span>\n<span class="text-success">&gt; [OK] Rollback finished successfully!</span>`;
    } else {
      logBox.innerHTML += `<span class="text-danger">&gt; [ERROR] ${data.message}</span>`;
    }
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i> Roll Back Step';
    logBox.innerHTML += `<span class="text-danger">&gt; [NETWORK ERROR] Could not reach rollback endpoint.</span>`;
  }
}

// --- USER MANAGEMENT & SPEED COIN GIFTING ---
let userSearchTimeout = null;
let currentUsersPage = 1;
let currentEditingUserId = null;

function debounceUserSearch() {
  clearTimeout(userSearchTimeout);
  userSearchTimeout = setTimeout(() => {
    loadAdminUsers(1);
  }, 350);
}

async function loadAdminUsers(page = 1) {
  currentUsersPage = page;
  const tbody = document.getElementById('userTableBody');
  const q = document.getElementById('userSearchInput')?.value.trim() || '';
  const role = document.getElementById('userRoleFilter')?.value || '';
  const tier = document.getElementById('userTierFilter')?.value || '';
  const plan = document.getElementById('userPlanFilter')?.value || '';

  tbody.innerHTML = `
    <tr>
      <td colspan="7" class="text-center py-4 text-ink-muted">
        <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div> Loading users...
      </td>
    </tr>
  `;

  try {
    const params = new URLSearchParams({
      action: 'api_admin_users',
      q: q,
      role: role,
      tier: tier,
      plan: plan,
      page: page,
      limit: 25
    });

    const res = await fetch(`index.php?${params.toString()}`);
    const data = await res.json();

    if (data.status !== 'success') {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">${data.message || 'Failed to load users'}</td></tr>`;
      return;
    }

    // Update KPI counters
    if (data.kpi) {
      document.getElementById('kpiTotalUsers').textContent = Number(data.kpi.total_users).toLocaleString();
      document.getElementById('kpiAdminsCount').textContent = `${data.kpi.total_admins} admins`;
      document.getElementById('kpiTotalStudents').textContent = Number(data.kpi.total_students).toLocaleString();
      document.getElementById('kpiTotalCoins').textContent = Number(data.kpi.total_coins).toLocaleString();
      document.getElementById('kpiActiveStreaks').textContent = Number(data.kpi.active_streaks).toLocaleString();
    }

    const users = data.users || [];
    if (users.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-ink-muted">No users found matching your filters.</td></tr>`;
      document.getElementById('userPaginationInfo').textContent = 'Showing 0 users';
      document.getElementById('userPaginationControls').innerHTML = '';
      return;
    }

    const tierColors = {
      Bronze: '#b45309',
      Silver: '#64748b',
      Gold: '#d97706',
      Platinum: '#0284c7',
      Diamond: '#7c3aed'
    };

    let html = '';
    users.forEach(u => {
      const uJson = JSON.stringify(u).replace(/"/g, '&quot;');
      const isAdmin = u.role === 'admin';
      const roleBadge = isAdmin
        ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>`
        : `<span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="bi bi-person me-1"></i>Student</span>`;
      
      const planBadge = u.plan === 'premium'
        ? `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5 extra-small">PRO</span>`
        : `<span class="badge bg-light text-muted border px-1.5 py-0.5 extra-small">FREE</span>`;

      const userTier = u.tier || 'Bronze';
      const tColor = tierColors[userTier] || '#64748b';
      const safeName = (u.name || 'Anonymous').replace(/'/g, "\\'");
      const safeEmail = (u.email || '').replace(/'/g, "\\'");

      html += `
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white extra-small" style="width: 34px; height: 34px; background: linear-gradient(135deg, ${tColor}, #1e293b); flex-shrink: 0;">
                ${(u.name || 'U').charAt(0).toUpperCase()}
              </div>
              <div class="overflow-hidden">
                <div class="fw-bold text-ink text-truncate" style="max-width: 180px;">${escapeHtml(u.name || 'Anonymous')}</div>
                <div class="extra-small text-ink-muted text-truncate" style="max-width: 180px;">
                  <span class="badge bg-body-tertiary text-ink border me-1">#${u.id}</span>${escapeHtml(u.email || '')}
                </div>
              </div>
            </div>
          </td>
          <td>
            <div class="d-flex flex-column gap-1">
              <div>${roleBadge}</div>
              <div>${planBadge}</div>
            </div>
          </td>
          <td>
            <span class="badge px-2 py-1" style="background-color: ${tColor}20; color: ${tColor}; border: 1px solid ${tColor}40;">
              <i class="bi bi-award-fill me-1"></i>${userTier}
            </span>
          </td>
          <td>
            <div class="fw-bold d-flex align-items-center gap-1" style="color: #f59e0b;">
              <i class="bi bi-coin"></i>
              <span id="user-coins-${u.id}">${Number(u.coins || 0).toLocaleString()}</span>
            </div>
          </td>
          <td>
            <div class="small fw-semibold text-ink">${Number(u.total_xp || 0).toLocaleString()} <span class="extra-small text-ink-muted">XP</span></div>
            <div class="extra-small ${u.current_streak > 0 ? 'text-danger fw-bold' : 'text-ink-muted'}">
              <i class="bi bi-fire me-0.5"></i>${u.current_streak || 0}d streak
            </div>
          </td>
          <td>
            <div class="extra-small text-ink-muted">${(u.created_at || '').substring(0, 10)}</div>
          </td>
          <td class="text-end">
            <div class="d-flex gap-1 justify-content-end flex-wrap">
              <button type="button" class="btn btn-sm btn-primary-gamified px-2 py-1 fw-bold" title="Gift Coins" onclick="openGiftCoinsModal(${u.id}, '${safeName}', ${u.coins || 0}, '${safeEmail}')">
                <i class="bi bi-gift-fill me-1"></i>Gift
              </button>
              <button type="button" class="btn btn-sm btn-outline-gamified px-2 py-1" title="Manage User" onclick="openEditUserModal(${uJson})">
                <i class="bi bi-gear-fill"></i>
              </button>
              <button type="button" class="btn btn-sm btn-outline-gamified px-2 py-1" title="Inspect History" onclick="openInspectUserModal(${u.id})">
                <i class="bi bi-eye-fill"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    });

    tbody.innerHTML = html;

    // Pagination info
    const total = data.total_count || users.length;
    const startIdx = (page - 1) * (data.limit || 25) + 1;
    const endIdx = Math.min(total, page * (data.limit || 25));
    document.getElementById('userPaginationInfo').textContent = `Showing ${startIdx}-${endIdx} of ${total} users`;

    // Pagination controls
    const totalPages = Math.ceil(total / (data.limit || 25));
    let pagHtml = '';
    if (totalPages > 1) {
      pagHtml += `<button class="btn btn-sm btn-outline-gamified py-0.5 px-2" ${page <= 1 ? 'disabled' : ''} onclick="loadAdminUsers(${page - 1})"><i class="bi bi-chevron-left"></i></button>`;
      pagHtml += `<span class="px-2 small text-ink-muted align-self-center">Page ${page} of ${totalPages}</span>`;
      pagHtml += `<button class="btn btn-sm btn-outline-gamified py-0.5 px-2" ${page >= totalPages ? 'disabled' : ''} onclick="loadAdminUsers(${page + 1})"><i class="bi bi-chevron-right"></i></button>`;
    }
    document.getElementById('userPaginationControls').innerHTML = pagHtml;

  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Network error connecting to users endpoint.</td></tr>`;
  }
}

// --- COIN GIFTING (SINGLE USER) ---
function openGiftCoinsModal(userId, userName, currentCoins, userEmail) {
  document.getElementById('giftUserId').value = userId;
  document.getElementById('giftCurrentCoins').value = currentCoins;
  document.getElementById('giftUserName').textContent = userName;
  document.getElementById('giftUserMeta').textContent = `ID #${userId} â€¢ ${userEmail}`;
  document.getElementById('giftUserCurrentDisplay').innerHTML = `<i class="bi bi-coin me-1"></i>${Number(currentCoins).toLocaleString()} Coins`;
  document.getElementById('giftCoinAmount').value = 100;
  document.getElementById('giftReasonSelect').value = 'Weekly Challenge Prize';
  document.getElementById('giftReasonCustom').value = 'Weekly Challenge Prize';
  updateGiftPreview();

  const modal = new bootstrap.Modal(document.getElementById('giftCoinsModal'));
  modal.show();
}

function setGiftCoinAmount(amount) {
  document.getElementById('giftCoinAmount').value = amount;
  updateGiftPreview();
}

function setGiftReason(reason) {
  const customInput = document.getElementById('giftReasonCustom');
  if (reason === 'custom') {
    customInput.value = '';
    customInput.focus();
  } else if (reason) {
    customInput.value = reason;
  }
}

function updateGiftPreview() {
  const current = parseInt(document.getElementById('giftCurrentCoins').value) || 0;
  const gift = parseInt(document.getElementById('giftCoinAmount').value) || 0;
  const next = current + gift;
  document.getElementById('giftNewBalancePreview').textContent = `${Number(next).toLocaleString()} Coins (+${Number(gift).toLocaleString()})`;
}

async function handleGiftCoinsSubmit(e) {
  e.preventDefault();
  const btn = document.getElementById('btnSubmitGift');
  const userId = document.getElementById('giftUserId').value;
  const amount = parseInt(document.getElementById('giftCoinAmount').value) || 0;
  const reason = document.getElementById('giftReasonCustom').value.trim();
  const csrfToken = document.getElementById('adminCsrfToken').value;

  if (amount <= 0) {
    alert('Please enter a valid coin amount greater than 0.');
    return;
  }
  if (!reason) {
    alert('Please provide a reason or note for crediting coins.');
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

  try {
    const res = await fetch('index.php?action=api_admin_credit_coins', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        user_id: userId,
        amount: amount,
        reason: reason,
        csrf_token: csrfToken
      })
    });
    const data = await res.json();

    if (data.status === 'success') {
      alert(data.message || 'Speed Coins gifted successfully!');
      bootstrap.Modal.getInstance(document.getElementById('giftCoinsModal')).hide();
      const coinCell = document.getElementById(`user-coins-${userId}`);
      if (coinCell && data.new_balance !== undefined) {
        coinCell.textContent = Number(data.new_balance).toLocaleString();
      }
      loadAdminUsers(currentUsersPage);
    } else {
      alert(data.message || 'Error gifting coins.');
    }
  } catch (err) {
    alert('Network error communicating with server.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Confirm &amp; Gift Coins';
  }
}

// --- BULK COIN GIFTING ---
function openBulkGiftModal() {
  document.getElementById('bulkCoinAmount').value = 100;
  document.getElementById('bulkReason').value = 'Campaign Bonus Grant';
  toggleBulkTargetMode('tier');
  const modal = new bootstrap.Modal(document.getElementById('bulkGiftCoinsModal'));
  modal.show();
}

function toggleBulkTargetMode(mode) {
  const tierContainer = document.getElementById('bulkTierContainer');
  const listContainer = document.getElementById('bulkListContainer');
  if (mode === 'tier') {
    tierContainer.classList.remove('d-none');
    listContainer.classList.add('d-none');
  } else {
    tierContainer.classList.add('d-none');
    listContainer.classList.remove('d-none');
  }
}

async function handleBulkGiftSubmit(e) {
  e.preventDefault();
  const btn = document.getElementById('btnSubmitBulkGift');
  const mode = document.querySelector('input[name="bulk_target_type"]:checked').value;
  const amount = parseInt(document.getElementById('bulkCoinAmount').value) || 0;
  const reason = document.getElementById('bulkReason').value.trim();
  const targetTier = document.getElementById('bulkTargetTier').value;
  const userIds = document.getElementById('bulkTargetList').value.trim();
  const csrfToken = document.getElementById('adminCsrfToken').value;

  if (amount <= 0) {
    alert('Please enter a valid coin amount.');
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Executing Bulk Gift...';

  try {
    const res = await fetch('index.php?action=api_admin_credit_coins', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        mode: 'bulk',
        amount: amount,
        reason: reason,
        target_tier: mode === 'tier' ? targetTier : '',
        user_ids: mode === 'list' ? userIds : '',
        csrf_token: csrfToken
      })
    });
    const data = await res.json();

    if (data.status === 'success') {
      alert(data.message);
      bootstrap.Modal.getInstance(document.getElementById('bulkGiftCoinsModal')).hide();
      loadAdminUsers(currentUsersPage);
    } else {
      alert(data.message || 'Bulk gift failed.');
    }
  } catch (err) {
    alert('Network error while processing bulk coins.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-send-check-fill me-1"></i> Execute Bulk Gift';
  }
}

// --- EDIT USER ATTRIBUTES ---
function openEditUserModal(user) {
  currentEditingUserId = user.id;
  document.getElementById('editUserId').value = user.id;
  document.getElementById('editUserName').textContent = user.name || 'Anonymous';
  document.getElementById('editUserEmail').textContent = `ID #${user.id} â€¢ ${user.email || ''}`;
  document.getElementById('editUserRole').value = user.role || 'user';
  document.getElementById('editUserPlan').value = user.plan || 'free';
  document.getElementById('editUserTier').value = user.tier || 'Bronze';
  document.getElementById('editUserFreezes').value = user.streak_freezes || 0;
  document.getElementById('editUserStreak').value = user.current_streak || 0;
  document.getElementById('resetPasswordResult').classList.add('d-none');

  const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
  modal.show();
}

async function handleEditUserSubmit(e) {
  e.preventDefault();
  const btn = document.getElementById('btnSubmitEditUser');
  const userId = document.getElementById('editUserId').value;
  const role = document.getElementById('editUserRole').value;
  const plan = document.getElementById('editUserPlan').value;
  const tier = document.getElementById('editUserTier').value;
  const freezes = document.getElementById('editUserFreezes').value;
  const streak = document.getElementById('editUserStreak').value;
  const csrfToken = document.getElementById('adminCsrfToken').value;

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

  try {
    const statsRes = await fetch('index.php?action=api_admin_update_user_stats', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
      body: JSON.stringify({
        user_id: userId,
        plan: plan,
        tier: tier,
        streak_freezes: freezes,
        current_streak: streak,
        csrf_token: csrfToken
      })
    });
    const statsData = await statsRes.json();

    const roleRes = await fetch('index.php?action=api_admin_update_user_role', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
      body: JSON.stringify({
        user_id: userId,
        role: role,
        csrf_token: csrfToken
      })
    });
    const roleData = await roleRes.json();

    if (roleData.status === 'error') {
      alert(`Role update notice: ${roleData.message}`);
    } else {
      alert('User attributes updated successfully!');
    }

    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
    loadAdminUsers(currentUsersPage);
  } catch (err) {
    alert('Network error updating user attributes.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Save Changes';
  }
}

async function handleResetPassword() {
  if (!currentEditingUserId) return;
  const csrfToken = document.getElementById('adminCsrfToken').value;
  const resultBox = document.getElementById('resetPasswordResult');

  if (!confirm('Are you sure you want to reset this user\'s password to a temporary generated password?')) {
    return;
  }

  resultBox.className = 'alert alert-info extra-small mb-0 py-2';
  resultBox.textContent = 'Generating temporary credentials...';
  resultBox.classList.remove('d-none');

  try {
    const res = await fetch('index.php?action=api_admin_reset_user_password', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
      body: JSON.stringify({
        user_id: currentEditingUserId,
        csrf_token: csrfToken
      })
    });
    const data = await res.json();

    if (data.status === 'success') {
      resultBox.className = 'alert alert-success extra-small mb-0 py-2';
      resultBox.innerHTML = `<strong>Password Reset:</strong> Temporary password is: <code>${data.temp_password}</code> (Share with the student)`;
    } else {
      resultBox.className = 'alert alert-danger extra-small mb-0 py-2';
      resultBox.textContent = data.message || 'Failed to reset password.';
    }
  } catch (err) {
    resultBox.className = 'alert alert-danger extra-small mb-0 py-2';
    resultBox.textContent = 'Network error communicating with server.';
  }
}

// --- INSPECT USER DETAILS ---
async function openInspectUserModal(userId) {
  const modal = new bootstrap.Modal(document.getElementById('inspectUserModal'));
  modal.show();

  const headerBox = document.getElementById('inspectHeaderBox');
  const attemptsBody = document.getElementById('inspectAttemptsTableBody');
  const coinsBody = document.getElementById('inspectCoinsTableBody');
  const unlocksBody = document.getElementById('inspectUnlocksTableBody');

  headerBox.innerHTML = `<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading user profile...`;
  attemptsBody.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-ink-muted"><div class="spinner-border spinner-border-sm me-1"></div> Loading...</td></tr>`;
  coinsBody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-ink-muted"><div class="spinner-border spinner-border-sm me-1"></div> Loading...</td></tr>`;
  unlocksBody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-ink-muted"><div class="spinner-border spinner-border-sm me-1"></div> Loading...</td></tr>`;

  try {
    const res = await fetch(`index.php?action=api_admin_user_details&user_id=${userId}`);
    const data = await res.json();

    if (data.status !== 'success') {
      headerBox.innerHTML = `<span class="text-danger">${data.message || 'Error loading profile'}</span>`;
      return;
    }

    const u = data.user;
    headerBox.innerHTML = `
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white fs-5" style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--accent), #1e293b);">
            ${(u.name || 'U').charAt(0).toUpperCase()}
          </div>
          <div>
            <h6 class="font-serif fw-bold text-ink mb-0">${escapeHtml(u.name || 'Anonymous')} <span class="badge bg-secondary extra-small ms-1">#${u.id}</span></h6>
            <div class="extra-small text-ink-muted">${escapeHtml(u.email || '')} &bull; Joined ${(u.created_at || '').substring(0, 10)}</div>
          </div>
        </div>
        <div class="d-flex gap-3 text-end">
          <div>
            <div class="extra-small text-ink-muted fw-bold">SPEED COINS</div>
            <div class="fw-bold" style="color: #f59e0b;"><i class="bi bi-coin me-1"></i>${Number(u.coins || 0).toLocaleString()}</div>
          </div>
          <div>
            <div class="extra-small text-ink-muted fw-bold">TOTAL XP</div>
            <div class="fw-bold text-ink">${Number(u.total_xp || 0).toLocaleString()}</div>
          </div>
          <div>
            <div class="extra-small text-ink-muted fw-bold">TIER</div>
            <span class="badge bg-light text-dark border">${u.tier || 'Bronze'}</span>
          </div>
        </div>
      </div>
    `;

    // Attempts
    const attempts = data.recent_attempts || [];
    document.getElementById('inspectAttemptsCount').textContent = attempts.length;
    if (attempts.length === 0) {
      attemptsBody.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-ink-muted">No practice attempts recorded yet.</td></tr>`;
    } else {
      let attHtml = '';
      attempts.forEach(a => {
        const isCor = parseInt(a.is_correct) === 1;
        attHtml += `
          <tr>
            <td class="fw-bold">#${a.question_id}</td>
            <td class="text-truncate" style="max-width: 280px;" title="${escapeHtml(a.question_text || '')}">${escapeHtml(a.question_text || 'Question #' + a.question_id)}</td>
            <td>
              <span class="badge ${isCor ? 'bg-success' : 'bg-danger'}">${isCor ? 'Correct' : 'Incorrect'}</span>
            </td>
            <td>+${a.xp_earned || 0} XP, +${a.coins_earned || 0} Coins</td>
            <td class="text-end text-ink-muted">${(a.attempted_at || '').substring(5, 16)}</td>
          </tr>
        `;
      });
      attemptsBody.innerHTML = attHtml;
    }

    // Coin transactions
    const txs = data.coin_transactions || [];
    document.getElementById('inspectCoinsCount').textContent = txs.length;
    if (txs.length === 0) {
      coinsBody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-ink-muted">No coin transactions found.</td></tr>`;
    } else {
      let txHtml = '';
      txs.forEach(t => {
        const isPlus = parseInt(t.amount) >= 0;
        txHtml += `
          <tr>
            <td class="fw-bold" style="color: ${isPlus ? '#10b981' : '#ef4444'};">
              ${isPlus ? '+' : ''}${t.amount} Coins
            </td>
            <td><span class="badge bg-light text-dark border">${t.transaction_type}</span></td>
            <td class="text-truncate" style="max-width: 250px;">${escapeHtml(t.reference_description || '')}</td>
            <td class="text-end text-ink-muted">${(t.created_at || '').substring(0, 16)}</td>
          </tr>
        `;
      });
      coinsBody.innerHTML = txHtml;
    }

    // Unlocks
    const unlocks = data.unlocks || [];
    document.getElementById('inspectUnlocksCount').textContent = unlocks.length;
    if (unlocks.length === 0) {
      unlocksBody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-ink-muted">No unlocked store items.</td></tr>`;
    } else {
      let unHtml = '';
      unlocks.forEach(item => {
        unHtml += `
          <tr>
            <td><code>${escapeHtml(item.code)}</code></td>
            <td class="fw-bold">${escapeHtml(item.title)}</td>
            <td><span class="badge bg-secondary">${escapeHtml(item.item_type)}</span></td>
            <td class="text-end text-ink-muted">${(item.unlocked_at || '').substring(0, 10)}</td>
          </tr>
        `;
      });
      unlocksBody.innerHTML = unHtml;
    }

  } catch (err) {
    headerBox.innerHTML = `<span class="text-danger">Failed to load user details due to a network error.</span>`;
  }
}

// Auto-load users on page load
document.addEventListener('DOMContentLoaded', () => {
  loadAdminUsers(1);
});
</script>



