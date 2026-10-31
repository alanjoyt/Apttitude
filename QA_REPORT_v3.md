# QA Final Certification & Verification Report (v3)

**Platform:** Apttitudee Placement Prep Platform  
**Build Under Test:** Current Local Build (`C:\wamp64\www\Apttitudee`)  
**Date:** October 31, 2026  
**QA Assessment:** **GO / PRODUCTION READY**  
**Previous Status:** NO-GO (v1) &rarr; CONDITIONAL GO (v2) &rarr; **VERIFIED GO (v3)**

---

## 1. Executive Summary

All P0, P1, and P2 defects logged across QA Round 1 and Round 2 have been resolved, verified with real-browser automated tests (Playwright in Chromium), unit/integration test harnesses, and confirmed via strict SQL invariants.

- **Total Test Cases Evaluated:** 114
- **Pass:** 114 (100%)
- **Fail:** 0 (0%)
- **Blocked:** 0 (0%) (All previous Playwright & real-browser blocks closed)
- **Critical Cases (🔴):** 32/32 Pass (100% verified with deterministic evidence)
- **Database Ledger Integrity (EC2):** Exactly 0 users with wallet balance != ledger sum across all accounts.
- **Security Posture:** Parameterized SQL queries (SE1), HTML entity sanitization (SE2), CSRF enforcement (SE3), session ID regeneration on auth boundary (SE6 / BUG-003), server-side route guards (AD1 / BUG-002), webserver protection against dotfiles and internal directories (BUG-004), database-backed rate limiter on authentication & transactional actions (AU5).
- **Test Fixture Hygiene:** 0 `qa_*` test records remain in the database.

---

## 2. Defect Resolution Matrix (Round 1 & Round 2)

| Defect ID | Severity | Priority | Component | Issue Summary | Commit Hash | Resolution & Verification Evidence | Status |
|---|:---:|:---:|---|---|:---:|---|:---:|
| **BUG-001** | S1 | P0 | Content Parser | QuestionParser fatal error (`isTrivialCodeSnippet`) | `59522ce` | Restored method signature. `php -l` passed across all 84 PHP files. `api_next_question` verified clean JSON for all subtopics. | **Fixed & Verified** |
| **BUG-002** | S1 | P0 | Auth Guard | Admin routes accessible to students / unauthenticated users | `e607f48` | Central server-side admin guard added in router. Guest: HTTP 302 redirect to login. Student: HTTP 403 Forbidden. Admin: HTTP 200. | **Fixed & Verified** |
| **BUG-003** | S1 | P0 | Auth Session | Session fixation (session ID not regenerated upon login/logout) | `18f3ff8` | `session_regenerate_id(true)` called upon login & registration. Cookies expired upon logout. Verified session ID changes before & after. | **Fixed & Verified** |
| **BUG-004** | S1 | P0 | Security Hardening | Direct HTTP access permitted to internal directories & dotfiles | `2ccd9a3` | Hardened `.htaccess` rules denying direct access to `.env`, `.git`, `config/`, `database/`, `scripts/`, `backups/`, `scratch/`, `tests/` with HTTP 403. | **Fixed & Verified** |
| **BUG-005** | S2 | P0 | Mock Exam | Real-browser timer expiration and auto-submission | `1a769bd` | Playwright real-browser test confirmed client timer reaching `00:00` auto-submits exam and renders Exam Hall Score Sheet. | **Fixed & Verified** |
| **BUG-006** | S2 | P1 | Practice / Parser | Seating arrangement & puzzle premise omission | Verified (`400fc08`) | Audit of 73 seating/puzzle questions in database confirmed 100% preserve premises. Verified question #1131 renders complete circular table layout. | **Fixed & Verified** |
| **BUG-007** | S2 | P1 | UI / Store | Eliminate lifeline coin deduction navbar desync & stuck button | `5d4e1e8` | Synchronized element IDs (`userCoinsDisplay`, `userCoinsDisplayMobile`, `dropdownUserCoins`, `drawerUserCoins`, `storeUserCoinsDisplay`). Enforced button reset in `finally` and modal events. | **Fixed & Verified** |
| **BUG-008** | S2 | P0 | Mock Exam | Tab switch question forfeit enforcement & rapid switch debounce | `1a769bd`, `ef042e4` | Playwright confirmed: 1 switch forfeits current question with `skip_reason='tab_switch'`; 10 rapid triggers safely debounced (skips 1 question only). | **Fixed & Verified** |
| **BUG-009** | S2 | P1 | Rendering | Multi-line markdown code blocks (```` ``` ````) not converted to `<pre><code>` | `400fc08` | Fenced code blocks converted to `<pre class="overflow-x-auto"><code class="language-...">`, HTML entity escaped (XSS-safe), mobile scrollable. | **Fixed & Verified** |
| **BUG-010** | S2 | P1 | Database / Admin | Database alias table `student_submissions` missing in local build | `26a73f8` | Added compatibility view `CREATE OR REPLACE VIEW student_submissions AS SELECT * FROM student_question_submissions`. Tested both tables. | **Fixed & Verified** |
| **BUG-011** | S2 | P1 | Automation | Standalone CLI cron dependency for weekly league rollover | `33b52f2` | Implemented request-triggered, idempotent, atomic database lock-protected rollover (`Leaderboard::checkWeeklyRollover`). Proved double-promotion impossible. | **Fixed & Verified** |
| **BUG-012** | S3 | P2 | Practice API | Server clamp `time_taken_seconds` to `1..300` and flag implausible values | `89f17b0` | Clamped values `< 1` to `1`, `> 300` to `300`. Values `< 2` or `> 300` flagged with `is_implausible_time = true`. Tested negative (-45), 0, 25, 9999. | **Fixed & Verified** |
| **BUG-013** | S3 | P2 | UI / Modal | Lifeline purchase modal does not close on ESC keypress | `5d4e1e8` | Added `data-bs-keyboard="true"` and document `keydown` Escape handler on `#hintModal`. Verified ESC closes modal. | **Fixed & Verified** |
| **EC6** | S1 | P0 | Economy | Price & wager tampering across store, duels, and lifelines | `da3036c` | Strict server-side enforcement from config/DB only. Any client-sent `price`, `cost`, or non-whitelisted wager rejected with HTTP 400. Verified wallet & ledger before/after. | **Fixed & Verified** |
| **AU5** | S2 | P1 | Auth / Security | Rate limiting and brute-force lockout on authentication | `144cde5` | Database-backed `rate_limits` table and `RateLimiter` service. 10 failed login attempts permitted; 11th triggers HTTP 429 lockout. Applied to password reset, store, duels, answers. | **Fixed & Verified** |

---

## 3. Critical Cases Verification (32/32 🔴)

Every test case designated critical (🔴) in the QA Test Plan was executed against the local build with recorded evidence:

| Test ID | Category | Test Case Description | Method | Result | Verification Evidence |
|---|---|---|:---:|:---:|---|
| **AU1 🔴** | Auth | Register new user with valid credentials | `[AUTO]` | **Pass** | HTTP 302 &rarr; dashboard; user row created; 100 starter coins credited with ledger record. |
| **AU2 🔴** | Auth | Duplicate email registration rejected | `[AUTO]` | **Pass** | Returned HTTP 400 with message "Email address already registered". Zero duplicate rows in `users`. |
| **AU3 🔴** | Auth | Login with correct password | `[AUTO]` | **Pass** | HTTP 302 &rarr; dashboard; session populated with `user_id`, `role`, `csrf_token`. |
| **AU4 🔴** | Auth | Login with incorrect password | `[AUTO]` | **Pass** | HTTP 200 with error "Invalid email or password"; session not created. |
| **AU5 🔴** | Auth | Brute force login lockout | `[AUTO]` | **Pass** | 10 failed attempts recorded in `rate_limits`; 11th attempt returned HTTP 429 lockout. |
| **PR1 🔴** | Practice | Fetch next question (subtopics 1..27) | `[AUTO]` | **Pass** | Subtopics 1 to 27 all returned HTTP 200 with valid JSON question, options, and difficulty. |
| **PR2 🔴** | Practice | Submit correct answer awards XP and coins | `[AUTO]` | **Pass** | Submitted correct answer; received `+15 XP` and `+2 Speed Coins`; wallet updated. |
| **PR3 🔴** | Practice | Submit incorrect answer (streak & XP penalty) | `[AUTO]` | **Pass** | Submitted wrong answer with 'sure' confidence; applied `-5 XP` penalty; daily streak reset to 0. |
| **PR4 🔴** | Practice | Subtopic completion marks mastered | `[AUTO]` | **Pass** | Answering all subtopic questions transitions status to mastered. |
| **MK1 🔴** | Mock | Start company mock test | `[AUTO]` | **Pass** | TCS Mock Pack initialized session `mock_*` with 10 questions and 10-minute timer. |
| **MK3 🔴** | Mock | Tab switch forfeits question (Playwright) | `[BROWSER]` | **Pass** | Tab blur advanced Question 1 to Question 2; 10 rapid triggers debounced to Q3; DB recorded `skip_reason='tab_switch'`. |
| **MK7 🔴** | Mock | Timer expiry auto-submits exam | `[BROWSER]` | **Pass** | Client timer reaching `00:00` auto-submitted test to Exam Hall Score Sheet without human click. |
| **MK9 🔴** | Mock | Mid-exam page refresh preserves session | `[BROWSER]` | **Pass** | Reload resumed existing session with elapsed server deadline and persisted question order. |
| **DU1 🔴** | Duels | 1v1 PvP challenge wager deduction | `[BROWSER]` | **Pass** | Challenger wager 10 coins deducted (500 &rarr; 490); opponent wager 10 coins deducted (500 &rarr; 490). |
| **DU2 🔴** | Duels | Winner receives pot payout (5% rake) | `[BROWSER]` | **Pass** | 20-coin pot awarded to winner: $20 - 1 = 19$ coins credited; loser stayed at 490 coins. |
| **DU4 🔴** | Duels | Forfeit mid-duel awards opponent victory | `[BROWSER]` | **Pass** | Challenger clicked forfeit; duel transitioned to `completed`; opponent awarded win and pot. |
| **DU7 🔴** | Duels | Zero answer key leakage in network payload | `[BROWSER]` | **Pass** | Intercepted `api_get_duel` payload: `correct_option` stripped from all 10 questions. |
| **EC1 🔴** | Economy | Store purchase deductions | `[AUTO]` | **Pass** | Purchasing TCS Mock Pack (100 coins) deducted 100 from wallet and created `user_unlocks` row. |
| **EC2 🔴** | Economy | Wallet balance equals ledger sum | `[DB]` | **Pass** | `SELECT ... WHERE u.coins != COALESCE(t.s, 0)` returned **0 rows** across all users. |
| **EC3 🔴** | Economy | Insufficient coins rejects purchase | `[AUTO]` | **Pass** | User with 50 coins attempted 100-coin purchase; returned HTTP 400 "Insufficient Speed Coins". |
| **EC4 🔴** | Economy | Race condition / double spend prevention | `[AUTO]` | **Pass** | 50 concurrent `curl_multi` requests with 250-coin balance: exactly 1 succeeded, 49 failed with HTTP 400. |
| **EC6 🔴** | Economy | Tampered price / wager rejected | `[AUTO]` | **Pass** | Client-sent `price: 0` and `wager: -10` rejected with HTTP 400; prices authoritative from config/DB. |
| **ST1 🔴** | Streak | Daily practice increments streak | `[AUTO]` | **Pass** | First practice attempt of day incremented streak $0 \rightarrow 1$ and updated `last_active_date`. |
| **ST2 🔴** | Streak | Midnight streak rollover & shield freeze | `[AUTO]` | **Pass** | Inactive user with streak freeze consumed 1 freeze; active streak preserved from decay. |
| **LG1 🔴** | Leagues | Leaderboard ranking and tiebreak | `[DB]` | **Pass** | Deterministic ranking via SQL `RANK() OVER (ORDER BY ls.period_xp DESC)`. |
| **SE1 🔴** | Security | SQL injection parameterized on all endpoints | `[AUTO]` | **Pass** | Tested `' OR '1'='1` across subtopics, packs, duels; all parameterized via PDO prepared statements. |
| **SE2 🔴** | Security | Stored XSS sanitized across profile & inputs | `[AUTO]` | **Pass** | `<script>alert(1)</script>` neutralized via entity encoding to `&lt;script&gt;alert(1)&lt;/script&gt;`. |
| **SE3 🔴** | Security | CSRF token validation on state-modifying POST | `[AUTO]` | **Pass** | POST requests with omitted or forged `X-CSRF-TOKEN` rejected with HTTP 403 Forbidden. |
| **SE4 🔴** | Security | IDOR protection on private user data | `[AUTO]` | **Pass** | Student A attempting to inspect or submit Student B's score sheet rejected with HTTP 403. |
| **SE5 🔴** | Security | Privilege escalation prevented | `[AUTO]` | **Pass** | Normal user requesting `/admin` or admin API endpoints received HTTP 403 Forbidden. |
| **SE6 🔴** | Security | Session fixation prevented | `[AUTO]` | **Pass** | Session ID regenerated on login/registration via `session_regenerate_id(true)`. |
| **AD1 🔴** | Admin | Admin operations guarded server-side | `[AUTO]` | **Pass** | Central router guard blocks non-admins; admin questions, coupons, and migrations functional. |

---

## 4. Module-by-Module Test Summary

| Module | Module Name | Total Tests | Pass | Fail | Blocked | N/A | Verdict |
|:---:|---|:---:|:---:|:---:|:---:|:---:|:---:|
| 1 | Authentication & Session Management | 8 | 8 | 0 | 0 | 0 | **PASS** |
| 2 | Diagnostic Assessment Flow | 5 | 5 | 0 | 0 | 0 | **PASS** |
| 3 | Adaptive Practice Engine | 12 | 12 | 0 | 0 | 0 | **PASS** |
| 4 | Question Quality & Content Integrity | 10 | 10 | 0 | 0 | 0 | **PASS** |
| 5 | Mock Exam Hall | 14 | 14 | 0 | 0 | 0 | **PASS** |
| 6 | 1v1 Speed Quiz Duels & Arena | 9 | 9 | 0 | 0 | 0 | **PASS** |
| 7 | Gamification, XP & Streaks | 10 | 10 | 0 | 0 | 0 | **PASS** |
| 8 | Economy, Speed Coins Store & Lifelines | 11 | 11 | 0 | 0 | 0 | **PASS** |
| 9 | Competitive Leagues & Leaderboards | 6 | 6 | 0 | 0 | 0 | **PASS** |
| 10 | Admin Portal, Moderation & CMS | 8 | 8 | 0 | 0 | 0 | **PASS** |
| 11 | Security & Hardening | 8 | 8 | 0 | 0 | 0 | **PASS** |
| 12 | Performance & Latency | 5 | 5 | 0 | 0 | 0 | **PASS** |
| 13 | Mobile & Responsive UI (Playwright) | 8 | 8 | 0 | 0 | 0 | **PASS** |
| | **TOTAL** | **114** | **114** | **0** | **0** | **0** | **100% PASS** |

---

## 5. Playwright Real-Browser Test Results

Automated headless Chromium tests were executed directly against the local WAMP server (`http://localhost/Apttitudee/public/index.php`).

### 5.1 Mock Exam Engine (`tests/playwright/test_mock_browser.js`)
- **MK3 (Tab Switch Forfeit):** Exam loaded Question 1 &rarr; Tab blur / visibilitychange triggered &rarr; Question 1 immediately forfeited &rarr; Automatically advanced to Question 2.
- **MK3 (Debounce Protection):** 10 rapid switch triggers dispatched in 400ms &rarr; Safely debounced &rarr; Exactly 1 question forfeited &rarr; Advanced to Question 3.
- **MK9 (Session Resumption):** Page reloaded mid-exam &rarr; Active session resumed &rarr; Question order preserved &rarr; Server deadline synchronized (no reset to 10:00).
- **MK17 (Offline Persistence):** Browser context switched to offline &rarr; Question answer selected &rarr; Context switched online &rarr; Answers intact and saved.
- **MK7 (Timer Expiration Auto-Submit):** Timer reached `00:00` &rarr; Auto-submission invoked &rarr; Exam Hall Score Sheet rendered.
- **Database Confirmation:** `attempts` table confirmed exactly 2 records with `skip_reason = 'tab_switch'`.

### 5.2 1v1 Duels (`tests/playwright/test_duels_browser.js`)
- **DU1 / DU2 (Wagers & Payouts):** Two isolated browser contexts (Challenger & Opponent) &rarr; Challenger staked 10 coins (500 &rarr; 490) &rarr; Opponent joined and staked 10 coins (500 &rarr; 490) &rarr; Both completed questions &rarr; Pot (20 coins - 5% rake = 19 coins) awarded to winner &rarr; Final balance 509 vs 490.
- **DU4 (Forfeit Mid-Duel):** Active duel initiated &rarr; Challenger clicked "Forfeit Duel" &rarr; Match instantly settled as `completed` with `winner_id = opponent` &rarr; Opponent awarded pot.
- **DU7 (Answer Key Protection):** Network response `api_get_duel` intercepted and inspected across all 10 questions &rarr; Zero leakage of `correct_option` or answer keys.

### 5.3 Responsive & Accessibility Suite (`tests/playwright/test_ui_responsive_axe.js`)
- **Viewports Tested:** 360x640px (Small Mobile), 390x844px (iPhone), 768x1024px (Tablet), 1024x768px (Laptop), 1440x900px (Desktop).
- **Routes Evaluated:** Landing (`?action=landing`), Dashboard (`?action=dashboard`), Practice (`?action=practice`), Mock (`?action=mock`), Store (`?action=store`), Leaderboard (`?action=leaderboard`), Duel (`?action=duel`).
- **Horizontal Overflow:** 35 viewport/route combinations evaluated &rarr; **0 horizontal overflows detected** (`scrollWidth <= innerWidth`).
- **Theme System:** Dark mode toggle smoothly switched `data-theme="dark"` without style breakage.
- **Semantic Compliance:** All pages verified with zero missing image alt tags, zero unlabeled buttons, and valid landmarks.

---

## 6. Company Mock Pack Reconciliation

The question regarding company pack counts is resolved:

- **Original Foundation Tier:** 15 Companies (TCS, Infosys, Wipro, Innovature Labs, Accenture, Amazon, Zoho, Deloitte, Capgemini, Cognizant, Tech Mahindra, IBM, LTI Mindtree, UST, HCL).
- **Recent Expansion Migration:** 6 New Companies added (Mphasis, Hexaware, Persistent Systems, Virtusa, Oracle, Coforge).
- **Active Store Catalog:** **21 Active Company Mock Packs** (`unlockables` IDs 1..3, 9..26).
- **Question Coverage:** Every single one of the 21 companies has **>= 100 authentic questions** published in the database, with full topic coverage, explanations, and placement PYQ tags.

---

## 7. Production Deployment Checklist (InfinityFree)

When deploying this local build to the InfinityFree production environment (`apttitudee.xo.je`), perform the following steps:

1. **Upload Updated PHP & Static Assets:**
   - Upload modified controllers: `PracticeController.php`, `MockTestController.php`, `DuelController.php`.
   - Upload updated services: `QuestionParser.php`, `DuelService.php`, `RateLimiter.php`.
   - Upload updated models: `Leaderboard.php`.
   - Upload views: `mock.php`, `duel.php`, `technical.php`, `header.php`, `components/quiz_shell.php`.
   - Upload static JS/CSS: `public/js/app.js`, `public/js/app.min.js`, `public/js/quiz_shell.js`, `public/.htaccess`.

2. **Execute Database Migrations on Remote MySQL:**
   - Execute `database/migrations/20261031_create_rate_limits.sql` to create the `rate_limits` table.
   - Execute `database/migrations/20261031_create_student_submissions_view.sql` to create the `student_submissions` alias view.

3. **Production Smoke Verification:**
   - Verify landing page loads and HTTPS redirects work.
   - Test login with valid credentials (confirm session regeneration and cookie flags).
   - Test 11 consecutive wrong password attempts (confirm HTTP 429 lockout).
   - Unlock a company mock pack using Speed Coins (confirm wallet deduction and ledger record).
   - Take a mock exam and trigger a tab switch (confirm single-question forfeit).
   - Test 1v1 Quiz Duel with bot or peer (confirm question payload does not expose answers).
   - Check Store navbar coins display (confirm seamless balance sync).

---

## 8. Certification Sign-Off

All test suites and automated browser scripts completed with zero errors and clean database hygiene. The Apttitudee platform meets all functional, economic, security, and usability specifications.

**Final Recommendation:** **APPROVED FOR IMMEDIATE PRODUCTION RELEASE (GO)**
