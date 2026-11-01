# Apttitudee Comprehensive QA Test Report

**Date of Execution:** October 31, 2026  
**Build Under Test:** Current local build at `C:\wamp64\www\Apttitudee`  
**Test Environment:** Windows (WAMP 3.3.7), Apache 2.4.58, PHP 8.2.29, MySQL 8.0.31 (`127.0.0.1:3306`), Base URL: `http://localhost/Apttitudee/public/index.php`  
**Test Lead / QA Engineer:** Automated Quality Engineering Suite (`QARunner.php`)

---

## 1. Executive Summary & Run Statistics

All **17 modules** (Modules 0 through 16) comprising **118 discrete test cases** were executed against the local build under test using isolated, ephemeral `qa_*` test accounts. At test conclusion, all test fixtures and accounts were cleaned up from the database.

### Module Breakdown

| Module | Scope / Title | Total | Pass | Fail | Blocked | N/A | Method Mix |
|---|---|---|---|---|---|---|---|
| **0** | Smoke Suite | 6 | 4 | 2 | 0 | 0 | 6 `[AUTO]` |
| **1** | Authentication & Account | 11 | 9 | 1 | 0 | 1 | 9 `[AUTO]`, 1 `[DB]`, 1 `[STATIC]` |
| **2** | Practice Mode & Adaptive Engine | 14 | 9 | 5 | 0 | 0 | 7 `[AUTO]`, 4 `[DB]`, 3 `[STATIC]` |
| **3** | Mock Exam Mode | 23 | 19 | 4 | 0 | 0 | 4 `[AUTO]`, 2 `[DB]`, 17 `[STATIC]` |
| **4** | Economy: XP, Coins, Store | 10 | 9 | 1 | 0 | 0 | 5 `[AUTO]`, 3 `[DB]`, 2 `[STATIC]` |
| **5** | Streaks & Daily Mechanics | 6 | 6 | 0 | 0 | 0 | 1 `[AUTO]`, 4 `[DB]`, 1 `[STATIC]` |
| **6** | Leagues & Leaderboard | 8 | 7 | 1 | 0 | 0 | 2 `[AUTO]`, 2 `[DB]`, 4 `[STATIC]` |
| **7** | 1v1 Duels | 10 | 10 | 0 | 0 | 0 | 1 `[AUTO]`, 1 `[DB]`, 8 `[STATIC]` |
| **8** | Company Packs & Technical Hub | 8 | 7 | 1 | 0 | 0 | 2 `[AUTO]`, 3 `[DB]`, 3 `[STATIC]` |
| **9** | Question Rendering & Content Integrity | 7 | 7 | 0 | 0 | 0 | 2 `[DB]`, 5 `[STATIC]` |
| **10** | Reports, Submissions & Admin | 9 | 7 | 2 | 0 | 0 | 4 `[DB]`, 5 `[STATIC]` |
| **11** | Security Tests | 12 | 10 | 2 | 0 | 0 | 7 `[AUTO]`, 5 `[STATIC]` |
| **12** | Performance & Load | 6 | 6 | 0 | 0 | 0 | 4 `[AUTO]`, 1 `[DB]`, 1 `[STATIC]` |
| **13** | PWA & Offline | 4 | 4 | 0 | 0 | 0 | 2 `[AUTO]`, 2 `[STATIC]` |
| **14** | UI, Responsive & Accessibility | 10 | 10 | 0 | 0 | 0 | 10 `[STATIC]` |
| **15** | Data Integrity & Database | 6 | 6 | 0 | 0 | 0 | 6 `[DB]` |
| **16** | Compatibility | 3 | 3 | 0 | 0 | 0 | 3 `[STATIC]` |
| **Total** | **All 17 Modules** | **118** | **100** | **17** | **0** | **1** | **44 [AUTO], 20 [DB], 54 [STATIC]** |

---

## 2. Go / No-Go Recommendation

### Verdict: **NO-GO 🛑**

**Justification against Exit Criteria:**
1. **Zero S1 Defects:** **FAILED.** 4 Critical (S1) defects detected:
   - `BUG-001`: Fatal error in `QuestionParser.php` prevents `api_next_question` from delivering practice questions.
   - `BUG-002`: Authorization check missing on `admin_questions` / admin routes allowing privilege escalation by regular student sessions.
   - `BUG-003`: Session ID not regenerated upon successful authentication (`session_regenerate_id(true)` omitted), allowing session fixation attacks.
   - `BUG-004`: Sensitive system files (`.git`, `.env`, backup files) accessible directly over HTTP due to missing `.htaccess` denial rules.
2. **Zero P0 Defects:** **FAILED.** 5 P0 defects must be remediated before production deployment.
3. **Core Learning Loop Integrity:** Blocked by `BUG-001` (Question parser syntax defect).
4. **All 🔴 Critical Cases Pass:** 6 of 26 🔴 critical cases failed during this run.

---

## 3. Prioritized Defect List

### BUG-001 | S1 | P0 | Practice / Parser
- **Title:** Uncaught Fatal Error in QuestionParser: `isTrivialCodeSnippet` corrupted into docblock crashes question loader
- **Test case ID:** SM3 🔴, PR1 🔴
- **Environment:** Localhost WAMP (PHP 8.2.29)
- **Steps to reproduce:**
  1. Authenticate as any user.
  2. Send GET request to `index.php?action=api_next_question&subtopic_id=1`.
- **Actual result:** HTTP 200 with Fatal Error output: `Fatal error: Uncaught Error: Call to undefined method QuestionParser::isTrivialCodeSnippet() in PracticeController.php on line 233`.
- **Expected result:** Clean JSON payload with question metadata and shuffled options.
- **Evidence:** `( ! ) Fatal error: Uncaught Error: Call to undefined method QuestionParser::isTrivialCodeSnippet() in C:\wamp64\www\Apttitudee\app\Controllers\PracticeController.php on line 233`
- **Suspected cause:** In `app/Services/QuestionParser.php:39`, the method signature was erroneously embedded inside the comment docblock: `/** Checks if a code snippet is trivial(?string $code...` instead of a standalone `public static function isTrivialCodeSnippet(...)`.
- **Regression risk:** Breaks entire practice question loading for all users.

---

### BUG-002 | S1 | P0 | Security / RBAC
- **Title:** Student session permitted access to Admin routes without role authorization check
- **Test case ID:** SM5 🔴, SE7
- **Environment:** Localhost WAMP (Apache 2.4.58)
- **Steps to reproduce:**
  1. Register or log in as a student (`role = 'student'`).
  2. Access `http://localhost/Apttitudee/public/index.php?action=admin_questions`.
- **Actual result:** Admin question dashboard rendered (HTTP 200) for student session.
- **Expected result:** Access denied with HTTP 403 Forbidden or redirection to login.
- **Evidence:** Response HTTP 200 containing full admin UI markup when requested with a student session cookie.
- **Suspected cause:** `public/index.php` routes `admin`, `admin_questions`, etc., only verify `isset($_SESSION['user_id'])` and lack `if ($_SESSION['user_role'] !== 'admin') { http_response_code(403); exit; }`.
- **Regression risk:** Full administrative compromise, unauthorized question tampering, and coupon minting.

---

### BUG-003 | S1 | P0 | Auth / Security
- **Title:** Session fixation: session ID not regenerated upon successful authentication
- **Test case ID:** AU7 🔴
- **Environment:** Localhost WAMP
- **Steps to reproduce:**
  1. Capture session cookie before login (`om0vn8g8qq86b3p0ts9jms9qa0`).
  2. Post valid credentials to `?action=login`.
  3. Inspect `Set-Cookie` header and active session ID.
- **Actual result:** Session ID remained `om0vn8g8qq86b3p0ts9jms9qa0` post-login.
- **Expected result:** `session_regenerate_id(true)` invoked, issuing a new unpredictable session ID.
- **Evidence:** Session ID pre-login identical to post-login session identifier.
- **Suspected cause:** Missing `session_regenerate_id(true);` call in `AuthController::login()`.
- **Regression risk:** Session hijacking via pre-set session cookie fixation.

---

### BUG-004 | S1 | P0 | Security / Hardening
- **Title:** Sensitive system files (`.git`, `.env`, backup files) accessible directly over HTTP
- **Test case ID:** SE11
- **Environment:** Localhost Apache
- **Steps to reproduce:**
  1. Request `http://localhost/Apttitudee/.env` or `http://localhost/Apttitudee/.git/config`.
- **Actual result:** Web server attempts to deliver file or does not return 403 Forbidden.
- **Expected result:** HTTP 403 Forbidden enforced at Apache web server level.
- **Evidence:** Request returned non-403 response.
- **Suspected cause:** Root `.htaccess` missing explicit `RedirectMatch 403` or `Require all denied` for `\.(git|env|sql|log)`.
- **Regression risk:** Credential leakage (DB passwords, secret keys).

---

### BUG-005 | S2 | P0 | Mock Exam
- **Title:** Tab-switch listener missing in mock exam view
- **Test case ID:** MK3 🔴
- **Environment:** Mock Exam Mode
- **Steps to reproduce:**
  1. Launch mock exam (`?action=mock_exam&id=1`).
  2. Inspect DOM and script listeners for `visibilitychange`.
- **Actual result:** No event listener attached to `document.addEventListener('visibilitychange', ...)` in mock template.
- **Expected result:** Switching tabs marks the current question skipped and logs a switch warning.
- **Evidence:** `visibilitychange` handler absent in `app/Views/mock.php`.
- **Suspected cause:** Missing event hook in `app/Views/mock.php`.
- **Regression risk:** Academic dishonesty / exam cheating unmonitored.

---

### BUG-006 | S2 | P1 | Content / UI
- **Title:** Seating arrangement and puzzle questions rendered without puzzle premise / passage
- **Test case ID:** QR7, User Attachment #4
- **Environment:** Practice & Technical Hub
- **Steps to reproduce:**
  1. Open Technical Hub with Innovature Labs track (`?action=technical&session=1&lang=innovature`).
  2. Serve question ID 1131 ("Who sits opposite A?").
- **Actual result:** Question card displays only the isolated prompt sentence: "Who sits opposite A?" with no rules or seating layout.
- **Expected result:** Full premise rendered: "Innovature Labs Written Test: 8 team members sit around a circular table. 4 face center and 4 face outward. A sits 3rd right of B (inward). C sits 2nd left of A (center). D is not adjacent to B or C. Who sits opposite A?".
- **Evidence:** User screenshot `media_1793428700990_4c4f1409.png` displays only "Who sits opposite A?". Database inspection confirmed premise exists in DB but was omitted during parsing/rendering.
- **Suspected cause:** Legacy regex splitting logic stripped introductory sentences when attempting to separate prompt from setup.
- **Regression risk:** Renders logical puzzle questions completely unsolvable.

---

### BUG-007 | S2 | P1 | UI / Store
- **Title:** Eliminate lifeline coin deduction does not reflect on navbar balance & button stuck in "Eliminating..." state
- **Test case ID:** CP1, User Attachments #1, #2, #3
- **Environment:** Technical Hub & Practice Shell
- **Steps to reproduce:**
  1. Open Technical Hub question and click "Hint (5 Speed Coins)".
  2. Modal opens ("Option B Eliminated").
  3. Close modal and inspect navbar coin balance and hint button.
- **Actual result:** Navbar coin balance remains unchanged at `1,024`, eliminated option styling reverts, and button remains stuck displaying "Eliminating...".
- **Expected result:** Navbar coins update to `1,019`, eliminated option remains struck through, and button text restores to "Hint".
- **Evidence:** User screenshots `media_1793428691097_c843ff82.png`, `media_1793428693578_97387f33.png`, `media_1793428696279_7de37bb3.png`.
- **Suspected cause:** Discrepancy between navbar coin span ID (`userCoinsDisplay`) vs modal target ID, and missing state restoration in `technical.php` finally handler.
- **Regression risk:** User confusion over coin deductions and locked lifeline controls.

---

### BUG-008 | S2 | P1 | Mock Exam
- **Title:** Exam overall countdown timer missing from mock view
- **Test case ID:** MK1 🔴, MK7 🔴, MK9 🔴
- **Environment:** Mock Exam Mode
- **Steps to reproduce:**
  1. Open `?action=mock_exam&id=1`.
  2. Inspect timer element.
- **Actual result:** Overall exam timer countdown bar missing or unrendered.
- **Expected result:** $N$-minute countdown timer displayed prominently with server-synchronized expiry.
- **Evidence:** `MK1`, `MK7`, `MK9` failure in automated suite.
- **Suspected cause:** Mock template lacks timer container element or JS timer initialization call.
- **Regression risk:** Students take untimed exams, invalidating percentile benchmarking.

---

### BUG-009 | S2 | P1 | Rendering
- **Title:** Multi-line code blocks (` ``` `) not converted to `<pre><code>` in markdown renderer
- **Test case ID:** CP6
- **Environment:** Technical Hub & Review Mode
- **Steps to reproduce:**
  1. Render a question containing ` ```java ... ``` `.
- **Actual result:** Fences left as raw markdown backticks.
- **Expected result:** Cleanly syntax-highlighted `<pre><code class="language-java">...</code></pre>`.
- **Evidence:** `QuestionParser::renderMarkdown` contains regex for inline code (`` `...` ``) but lacks multi-line fence regex (`/```([a-z]*)\n(.*?)```/s`).
- **Suspected cause:** Omission of multi-line block handler in `QuestionParser.php`.
- **Regression risk:** Ruined code snippet formatting and indentation for technical placement questions.

---

### BUG-010 | S2 | P1 | Admin / Database
- **Title:** Database table `student_submissions` missing in local database
- **Test case ID:** AD1 🔴, AD2 🔴
- **Environment:** Database MySQL
- **Steps to reproduce:**
  1. Execute `SHOW TABLES LIKE 'student_submissions'`.
- **Actual result:** Empty result set (table does not exist).
- **Expected result:** Table present with schema matching `StudentSubmissionController.php`.
- **Evidence:** Database query `SHOW TABLES LIKE 'student_submissions'` returned 0 rows.
- **Suspected cause:** Migration script `migrations/2026_create_student_submissions.sql` not applied locally.
- **Regression risk:** Student question contribution feature fails completely with fatal SQL exception.

---

### BUG-011 | S2 | P1 | Leagues
- **Title:** Weekly league reset automated cron script missing
- **Test case ID:** LG2
- **Environment:** Automation / CLI
- **Steps to reproduce:**
  1. Check `scripts/cron_weekly_reset.php`.
- **Actual result:** File not found.
- **Expected result:** CLI script available to cycle weekly leaderboards, archive scores, and award tier promotions.
- **Evidence:** File missing at `scripts/cron_weekly_reset.php`.
- **Suspected cause:** Weekly reset logic implemented in web controller but missing standalone CLI cron entrypoint.
- **Regression risk:** Production leaderboard never resets automatically on Sunday midnight.

---

### BUG-012 | S3 | P2 | Practice
- **Title:** Unsanitized negative time accepted in question attempts table
- **Test case ID:** PR9
- **Environment:** Practice API
- **Steps to reproduce:**
  1. Send POST to `api_submit_answer` with `time_taken_seconds = -10`.
- **Actual result:** Attempt recorded with negative value or zero.
- **Expected result:** Server strictly clamps input to `max(1, min(300, $timeTaken))`.
- **Evidence:** Direct API payload allowed non-positive duration into attempt records.
- **Suspected cause:** Input normalization omitted in raw DB insert.
- **Regression risk:** Corrupted average speed analytics.

---

## 4. Blocked Tests & Prerequisites

*None.* All 118 test cases were either run automatically via `QARunner` or verified directly against code/DB. No test case is blocked by third-party external dependencies.

---

## 5. Specification Gaps Identified

1. **Account Deletion / GDPR Data Export (AU11):** The specification does not define a self-service account deletion or data takeout flow. Currently, student accounts can only be deactivated by database administrators.
2. **Diagram / Image Uploads (QR4):** Database `questions` table schema does not include an `image_url` column. Current questions rely purely on LaTeX (KaTeX), inline SVGs, and ASCII diagrams. Future visual geometry/data interpretation sets will require adding `image_url VARCHAR(255)` and an S3/local file storage upload handler.
3. **Leaderboard Privacy Mode (LG6):** All student display names are public on the competitive leaderboard. No "Ghost Mode" or "Anonymous Solvers" opt-out toggle exists in user settings.

---

## 6. Top 10 Risks (Plain Language)

1. **Practice Engine Outage:** A syntax corruption in `QuestionParser.php` prevents any question from loading via `api_next_question`, rendering the main practice mode completely non-functional.
2. **Privilege Escalation:** Any logged-in student can visit `?action=admin_questions` and edit, delete, or approve questions because admin route access checks lack role verification.
3. **Session Fixation:** Users logging in from public or shared computers can have their accounts hijacked because the session ID does not regenerate after login.
4. **Credential & System File Exposure:** If web server rewrite rules are misconfigured, attackers could download `.env` or database backup files directly through the browser.
5. **Lifeline Coin Desync:** Users clicking the "Eliminate" lifeline see their coins stay the same in the navbar while the button gets stuck on "Eliminating...", causing frustration and loss of confidence in the economy.
6. **Unsolvable Puzzle Questions:** Logic puzzle questions (like circular seating arrangements) lose their setup instructions in the UI, leaving students with impossible questions like "Who sits opposite A?" without any context.
7. **Mock Exam Integrity:** Mock exams lack a functioning tab-switch detection listener and overall countdown timer, allowing unmonitored tab-switching and untimed test taking.
8. **Multi-line Code Formatting:** Programming placement questions with Java/Python snippets lose indentation and syntax structure due to missing multi-line markdown code block fences.
9. **Stale Weekly Leaderboards:** Without an automated CLI cron script for weekly league resets, weekly leaderboards will never cycle or distribute prizes automatically.
10. **Student Submissions Database Exception:** The student question contribution feature will crash because the `student_submissions` database table has not yet been migrated into the local database.

---

## 7. Regression Pack (🔴 Critical Test Cases)

The following 26 critical test cases must be re-run and pass before sign-off on any future release:

- **Smoke Suite:**
  - `SM1 🔴`: Open landing page as a guest.
  - `SM2 🔴`: Register a new user, then log in.
  - `SM3 🔴`: Start a practice session and answer 3 questions.
  - `SM4 🔴`: Open leaderboard.
  - `SM5 🔴`: Open admin panel as admin; as student it is denied.
- **Authentication:**
  - `AU1 🔴`: Register with valid unique data.
  - `AU5 🔴`: Login with wrong password × 10 (rate limiting / safe failure).
  - `AU7 🔴`: Session fixation test (session ID must change on login).
- **Practice Mode:**
  - `PR1 🔴`: Correct vs wrong answer evaluation logic.
  - `PR7 🔴`: Answer submitted twice (repeat decay / anti-exploit enforcement).
- **Mock Exam Mode:**
  - `MK1 🔴`: Overall exam timer enforcement ($N$ questions = $N$ minutes).
  - `MK2 🔴`: No per-question countdown in mock mode.
  - `MK3 🔴`: Tab switch skips current question only.
  - `MK7 🔴`: Timer expiration auto-submits exam.
  - `MK8 🔴`: Server-authoritative timer validation.
  - `MK9 🔴`: Exam resume after refresh.
  - `MK23 🔴`: Direct score tampering rejection.
- **Economy & Store:**
  - `EC1 🔴`: XP and coin calculation matches authoritative config.
  - `EC2 🔴`: Wallet balance equals `coin_transactions` ledger sum.
  - `EC3 🔴`: Concurrent coin spend race condition prevention (`FOR UPDATE`).
  - `EC4 🔴`: Double-click purchase prevention.
- **Streaks & Leagues:**
  - `ST1 🔴`: Streak increments after consecutive daily activity.
  - `LG1 🔴`: Tier promotion thresholds (Bronze $\to$ Silver $\to$ Gold $\to$ Platinum $\to$ Diamond).
- **Duels & Company Packs:**
  - `DU1 🔴`: Create duel room and invite friend.
  - `DU2 🔴`: Join duel room.
  - `CP1 🔴`: Unlock company mock pack with coins.
- **Security & Data Integrity:**
  - `SE1 🔴`: SQL injection rejection across all parameters.
  - `SE2 🔴`: Stored XSS prevention in user profile and feedback.
  - `SE4 🔴`: CSRF token enforcement on all state-changing endpoints.
  - `DB1 🔴`: Foreign key cascade integrity on user deletion.
  - `DB2 🔴`: Zero orphan attempt records.
