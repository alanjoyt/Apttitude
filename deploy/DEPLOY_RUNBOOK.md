# Apttitudee Deployment Runbook & Operational Guide

**Target Environment:** InfinityFree Shared Hosting (LAMP / Apache 2.4 / MariaDB / PHP 8.2+)  
**Staging Host:** `http://apttitudee.rf.gd`  
**Production Host:** `http://apttitudee.xo.je`  
**Last Updated:** November 2026

---

## Phase 1: Pre-Deployment Safety & Preservation

> [!IMPORTANT]
> Consult [`deploy/PRESERVE.md`](file:///c:/wamp64/www/Apttitudee/deploy/PRESERVE.md) before performing any remote transfer.
> - **`config/db.php` MUST NEVER BE OVERWRITTEN.** It contains remote database credentials (`sql210.infinityfree.com`).
> - **`public/uploads/` MUST NEVER BE DELETED OR OVERWRITTEN.**
> - Never drag-and-drop the local root wholesale.

1. **Verify Local Working Tree Cleanliness:**
   ```bash
   git diff config/db.php
   # Expected output: empty (0 diff)
   ```
2. **Confirm Manifest & Exclusion Integrity:**
   Ensure `deploy/manifest.json` contains only production artifacts (0 `scratch/` files, 0 test fixtures).

---

## Phase 2: Database Migration Sequence (phpMyAdmin)

> [!IMPORTANT]
> **Production Structure Verification Pre-Flight:**
> Before running any migrations on production or staging, the operator MUST export the live production structure from phpMyAdmin (following [`deploy/PRODUCTION_EXPORT_CHECKLIST.md`](PRODUCTION_EXPORT_CHECKLIST.md)) and execute:
> ```bash
> php deploy/scripts/diff_production_schema.php <path_to_production_schema_export.sql>
> ```
> All `CREATE TABLE IF NOT EXISTS` and `ALTER TABLE` migrations (01 through 08) MUST be verified against the production export prior to execution to confirm column names, datatypes, and existing constraints align with zero clashing definitions.

Execute the following migrations in `deploy/migrations/` **strictly in ascending order**:

| Step | Migration File | Description | Idempotent |
| :---: | :--- | :--- | :---: |
| **01** | `01_schema_extensions_and_tables.sql` | Extends `duels.is_bot`, `duels.status` enum (`cancelled`), `coin_transactions.transaction_type` extensions, missing indexes. | ✅ `IF NOT EXISTS` |
| **02** | `02_company_practice_patterns_and_unlockables.sql` | Company placement patterns, unlockables, blueprints. | ✅ `INSERT IGNORE` |
| **03** | `03_full_company_questions_import.sql` | Core placement questions pool for company packs. | ✅ `INSERT IGNORE` |
| **04** | `04_configurable_mock_lengths.sql` | Mock length modes (Quick 30, Standard 60, Full-length), `mock_configs`, `duration_seconds` in `mock_sessions`. | ✅ `IF NOT EXISTS` |
| **05** | `05_seven_day_fix_plan.sql` | Diagnostic 7-day personalized fix plan schema (`fix_plans`, `fix_plan_days`). | ✅ `IF NOT EXISTS` |
| **06** | `06_college_leaderboards.sql` | College directory (`colleges`), user affiliations, `hide_name_on_boards` default 1 (Option B). | ✅ `IF NOT EXISTS` |
| **07** | `07_college_stats_and_kerala_colleges.sql` | Aggregated rankings cache (`college_stats`), cascading foreign keys, seeds 20 Kerala colleges. | ✅ `IF NOT EXISTS` |
| **08** | `08_missing_app_tables_and_company_profiles.sql` | Application tables (`company_profiles`, `coupons`, `coupon_redemptions`, `coupon_audit_log`, `user_practice_sessions`, `admin_audit_log`). | ✅ `IF NOT EXISTS` |

> [!NOTE]
> If phpMyAdmin enforces a request timeout on `03_full_company_questions_import.sql` (~574 KB), import it in 2-3 chunked blocks or via the phpMyAdmin compressed `.sql.zip` upload option.

---

## Phase 3: Selective File Upload via FTPS

Using FileZilla or WinSCP with FTPS (Explicit TLS):
1. **Target Directory:** `/htdocs/`
2. **Deploy Application Code:**
   - `app/` &rarr; upload modified `Controllers/`, `Models/`, `Services/`, `Views/`.
   - `config/` &rarr; upload `economy.php` and `app.php`. **DO NOT UPLOAD `config/db.php`**.
   - `public/` &rarr; upload `index.php`, `css/`, `js/`, `assets/`, `manifest.json`, `sw.js`, `offline.html`. **DO NOT UPLOAD `public/uploads/`**.
   - `.htaccess` &rarr; merge security rules into production root `.htaccess`.

---

## Phase 4: Staging Smoke Checklist (`apttitudee.rf.gd`)

Immediately after deploying to staging:

1. **Environment Confirmation:**
   - Execute verification endpoint or view footer debug comment:
     - Check `phpversion()` (verify compatibility).
     - Check MariaDB version (`SELECT VERSION();`).
     - Inspect `$_SERVER['REMOTE_ADDR']` vs proxy headers (`HTTP_X_FORWARDED_FOR`) to verify user IP detection.
2. **Core Feature Sanity:**
   - [ ] **Registration:** Register test student with and without college affiliation; verify Option B checkbox appears and defaults to unchecked (anonymous).
   - [ ] **Login & CSRF:** Authenticate with test student credentials; verify session cookie set.
   - [ ] **Configurable Mock:** Launch Quick (30), Standard (60), or Full-length mock; verify timer equals question count; verify pool warning badge if pool < target.
   - [ ] **Exam Mechanics:** Switch tabs & verify warning recorded; reload & verify resume; verify auto-submit on countdown expiry.
   - [ ] **College Leaderboard:** View Global, Colleges, and Campus tabs; verify students default to `"Anonymous Student"` unless opted in; verify $\ge 5$ member threshold.
   - [ ] **Admin Moderation:** Access `index.php?action=admin`; verify college moderation card; test approve/reject/merge actions with CSRF protection.
   - [ ] **Coin Ledger (EC2):** Verify wallet balance equals transaction sum for test user (`WalletService`).
