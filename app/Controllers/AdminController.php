<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/CouponService.php';
require_once __DIR__ . '/../../scripts/migrate_all.php';

class AdminController {

    /**
     * Submit question report from user via AJAX
     */
    public static function submitReportJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $questionId = (int)($input['question_id'] ?? 0);
        $reason = $input['reason'] ?? 'wrong_answer';
        $comment = trim($input['comment'] ?? '');

        if (!$questionId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid question ID']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO question_reports (question_id, user_id, reason, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$questionId, $userId, $reason, $comment]);

        echo json_encode(['status' => 'success', 'message' => 'Thank you! The content team has received your report.']);
        exit;
    }

    /**
     * Fetch pending question reports for Admin CMS
     */
    public static function getPendingReports(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT r.*, q.question_text, u.name as reporter_name 
            FROM question_reports r
            JOIN questions q ON r.question_id = q.id
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Fetch pending student placement submissions for Admin Review Queue
     */
    public static function getPendingSubmissions(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT s.*, u.name as submitter_name, u.email as submitter_email
            FROM student_question_submissions s
            LEFT JOIN users u ON s.user_id = u.id
            WHERE s.status = 'pending'
            ORDER BY s.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    private static function checkAdminAuth(bool $returnArray = false): ?array {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $err = ['status' => 'error', 'message' => 'Unauthorized access. Login required.'];
            if ($returnArray) return $err;
            http_response_code(401);
            echo json_encode($err);
            exit;
        }
        $user = User::findById((int)$userId);
        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            $err = ['status' => 'error', 'message' => 'Forbidden. Admin privileges required.'];
            if ($returnArray) return $err;
            http_response_code(403);
            echo json_encode($err);
            exit;
        }
        return null;
    }

    private static function verifyAdminCsrf(array $input): void {
        $expected = $_SESSION['csrf_token'] ?? null;
        if (!$expected) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'CSRF token missing from session']);
            exit;
        }

        // 1. Check HTTP Header (handles dual/comma-separated headers if forwarded through proxies)
        $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if ($headerToken) {
            $tokens = array_map('trim', explode(',', $headerToken));
            foreach ($tokens as $t) {
                if ($t && hash_equals($expected, $t)) {
                    return;
                }
            }
        }

        // 2. Check JSON payload / POST field fallback
        $bodyToken = $input['csrf_token'] ?? ($_POST['csrf_token'] ?? null);
        if ($bodyToken && hash_equals($expected, $bodyToken)) {
            return;
        }

        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF token']);
        exit;
    }

    /**
     * Approve student submission and publish to live question bank
     */
    public static function approveSubmissionJSON(bool $returnArray = false) {
        if (!$returnArray && !headers_sent()) header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $authErr = self::checkAdminAuth($returnArray);
        if ($authErr) return $authErr;

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $submissionId = (int)($input['submission_id'] ?? 0);
        $adminNotes = trim($input['admin_notes'] ?? 'Approved by moderator.');

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM student_question_submissions WHERE id = ? AND status = 'pending'");
        $stmt->execute([$submissionId]);
        $sub = $stmt->fetch();

        if (!$sub) {
            $err = ['status' => 'error', 'message' => 'Submission not found or already reviewed.'];
            if ($returnArray) return $err;
            echo json_encode($err);
            exit;
        }

        // Find or map subtopic
        $subtopicStmt = $db->prepare("SELECT id FROM subtopics WHERE name LIKE ? LIMIT 1");
        $subtopicStmt->execute(['%' . $sub['topic_name'] . '%']);
        $subtopicId = (int)$subtopicStmt->fetchColumn() ?: 1; // Default to Quant SDT if unmapped

        // Allow inline editor overrides to correct typos before approving
        $questionText = trim($input['question_text'] ?? $sub['question_text']);
        $optA = trim($input['option_a'] ?? $sub['option_a']);
        $optB = trim($input['option_b'] ?? $sub['option_b']);
        $optC = trim($input['option_c'] ?? $sub['option_c']);
        $optD = trim($input['option_d'] ?? $sub['option_d']);
        $correctOpt = strtoupper(trim($input['correct_option'] ?? $sub['correct_option']));
        $explanation = trim($input['explanation'] ?? $sub['explanation']);
        $companyName = trim($input['company_name'] ?? $sub['company_name']);
        $roundPattern = trim($input['round_pattern'] ?? $sub['round_pattern']);
        $difficulty = trim($input['difficulty'] ?? $sub['difficulty']);

        // Check for duplicates in questions
        $dupCheck = $db->prepare("SELECT COUNT(*) FROM questions WHERE question_text = ?");
        $dupCheck->execute([$questionText]);
        if ((int)$dupCheck->fetchColumn() === 0) {
            $insertStmt = $db->prepare("
                INSERT INTO questions 
                (subtopic_id, difficulty, question_text, code_snippet, language, option_a, option_b, option_c, option_d, correct_option, explanation, est_time_seconds, source_tag, is_company_pyq, company_name, verification_status, round_pattern)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 60, ?, 1, ?, 'student_reported', ?)
            ");
            $insertStmt->execute([
                $subtopicId,
                $difficulty,
                $questionText,
                $sub['code_snippet'],
                $sub['language'],
                $optA,
                $optB,
                $optC,
                $optD,
                $correctOpt,
                $explanation,
                $companyName . ' Student Report',
                $companyName,
                $roundPattern
            ]);
        }

        // Update submission status
        $reviewerId = (int)($_SESSION['user_id'] ?? 0);
        $db->prepare("
            UPDATE student_question_submissions 
            SET status = 'approved', admin_notes = ?, reviewed_by = ?, reviewed_at = NOW() 
            WHERE id = ?
        ")->execute([$adminNotes, $reviewerId, $submissionId]);

        self::logAdminAction($reviewerId, 'approve_submission', 'submission', $submissionId, "Approved submission #{$submissionId} for {$companyName}");

        // Reward the contributing student with +50 XP and +10 Speed Coins
        if (!empty($sub['user_id'])) {
            require_once __DIR__ . '/../Services/WalletService.php';
            User::addXP((int)$sub['user_id'], 50);
            WalletService::addCoins((int)$sub['user_id'], 10, 'admin_grant', "Community Contributor: Approved Question for " . $sub['company_name']);
        }

        $res = [
            'status' => 'success',
            'reward_xp' => 50,
            'reward_coins' => 10,
            'message' => "Submission #{$submissionId} approved and published to live question bank!"
        ];
        if ($returnArray) return $res;
        echo json_encode($res);
        exit;
    }

    /**
     * Reject student submission with note
     */
    public static function rejectSubmissionJSON(bool $returnArray = false) {
        if (!$returnArray && !headers_sent()) header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $authErr = self::checkAdminAuth($returnArray);
        if ($authErr) return $authErr;

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $submissionId = (int)($input['submission_id'] ?? 0);
        $adminNotes = trim($input['admin_notes'] ?? 'Does not meet question bank quality standards.');

        $db = Database::getConnection();
        $reviewerId = (int)($_SESSION['user_id'] ?? 0);
        $db->prepare("
            UPDATE student_question_submissions 
            SET status = 'rejected', admin_notes = ?, reviewed_by = ?, reviewed_at = NOW() 
            WHERE id = ? AND status = 'pending'
        ")->execute([$adminNotes, $reviewerId, $submissionId]);

        $res = [
            'status' => 'success',
            'message' => "Submission #{$submissionId} marked as rejected."
        ];
        if ($returnArray) return $res;
        echo json_encode($res);
        exit;
    }

    /**
     * Toggle Developer Mode state via Admin AJAX
     */
    public static function toggleDevModeJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::checkAdminAuth();

        require_once __DIR__ . '/../Services/DeviceLockService.php';

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $enabled = isset($input['enabled']) ? (bool)$input['enabled'] : !DeviceLockService::isDeveloperMode();

        $success = DeviceLockService::setDeveloperMode($enabled);
        $currentState = DeviceLockService::isDeveloperMode();

        if ($success) {
            echo json_encode([
                'status' => 'success',
                'developer_mode' => $currentState,
                'message' => $currentState 
                    ? 'Developer Mode ENABLED: Multi-account testing unlocked on 1 device.' 
                    : 'Developer Mode DISABLED: Strict single-device lock active.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update system settings.']);
        }
        exit;
    }

    /**
     * Idempotent Batch Importer for Question Banks (JSON & CSV) with duplicate detection
     */
    public static function importBatchQuestionsJSON(bool $returnArray = false) {
        if (!$returnArray && !headers_sent()) header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $authErr = self::checkAdminAuth($returnArray);
        if ($authErr) return $authErr;

        $db = Database::getConnection();

        // 1. Gather input payload: either uploaded file (JSON/CSV) or raw JSON string
        $questionsData = [];

        $uploadedFile = $_FILES['batch_file'] ?? $_FILES['import_file'] ?? $_FILES['csv_file'] ?? null;
        if (!empty($uploadedFile['tmp_name'])) {
            $tmpPath = $uploadedFile['tmp_name'];
            $origName = strtolower($uploadedFile['name'] ?? '');

            if (str_ends_with($origName, '.json')) {
                $raw = file_get_contents($tmpPath);
                $questionsData = json_decode($raw, true) ?? [];
            } elseif (str_ends_with($origName, '.csv')) {
                $questionsData = self::parseCSVToQuestionArray($tmpPath);
            }
        } elseif (!empty($_POST['json_payload'])) {
            $questionsData = json_decode($_POST['json_payload'], true) ?? [];
        } else {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            if (!empty($input)) {
                if (isset($input[0]) && is_array($input[0])) {
                    $questionsData = $input;
                } elseif (!empty($input['questions'])) {
                    $questionsData = $input['questions'];
                }
            }
        }

        if (empty($questionsData) || !is_array($questionsData)) {
            $err = [
                'status' => 'error',
                'message' => 'No valid question data detected. Provide a JSON array or CSV file.'
            ];
            if ($returnArray) return $err;
            echo json_encode($err);
            exit;
        }

        // Subtopic resolution map (by code and by name)
        $subtopics = $db->query("SELECT s.id, s.code, s.name, t.name as topic_name FROM subtopics s JOIN topics t ON s.topic_id = t.id")->fetchAll();
        $subtopicMap = [];
        foreach ($subtopics as $s) {
            $subtopicMap[strtoupper($s['code'])] = (int)$s['id'];
            $subtopicMap[strtolower($s['name'])] = (int)$s['id'];
        }

        // Existing questions cache for fast duplicate detection
        $existingTexts = $db->query("SELECT question_text FROM questions")->fetchAll(PDO::FETCH_COLUMN);
        $existingLookup = array_flip(array_map('trim', $existingTexts));

        // Batch ID & Dry Run support
        $batchId = trim($_POST['batch_id'] ?? '') ?: ('BATCH_' . date('Ymd_His') . '_' . substr(bin2hex(random_bytes(3)), 0, 6));
        $isDryRun = !empty($_POST['dry_run']);

        require_once __DIR__ . '/../../scripts/verify_and_ingest.php';

        $insertStmt = $db->prepare("
            INSERT INTO questions 
            (subtopic_id, difficulty, question_text, code_snippet, language, option_a, option_b, option_c, option_d, correct_option, explanation, est_time_seconds, source_tag, is_company_pyq, company_name, verification_status, round_pattern, is_published, verified_by_script, import_batch_id, verification_notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $importedCount = 0;
        $publishedCount = 0;
        $draftCount = 0;
        $skippedDuplicates = 0;
        $errors = [];

        foreach ($questionsData as $idx => $q) {
            $rawText = trim($q['question_text'] ?? $q['question'] ?? '');
            if (empty($rawText)) {
                continue;
            }

            // Duplicate detection check
            if (isset($existingLookup[$rawText])) {
                $skippedDuplicates++;
                continue;
            }

            // Resolve subtopic
            $subCode = strtoupper(trim($q['subtopic_code'] ?? ''));
            $cat = strtolower(trim($q['category'] ?? $q['topic_name'] ?? ''));
            $subId = $subtopicMap[$subCode] ?? null;

            if (!$subId && !empty($cat)) {
                foreach ($subtopicMap as $k => $v) {
                    if (str_contains($cat, $k) || str_contains($k, $cat)) {
                        $subId = $v;
                        break;
                    }
                }
            }
            if (!$subId) {
                $subId = 1; // QUANT_SDT default
            }

            $difficulty = strtolower(trim($q['difficulty'] ?? 'medium'));
            if (!in_array($difficulty, ['easy', 'medium', 'hard'])) $difficulty = 'medium';

            // Options parsing (supports associative array 'options' => ['A' => ..., 'B' => ...] or flat 'option_a')
            if (isset($q['options']) && is_array($q['options'])) {
                $optA = trim($q['options']['A'] ?? '');
                $optB = trim($q['options']['B'] ?? '');
                $optC = trim($q['options']['C'] ?? '');
                $optD = trim($q['options']['D'] ?? '');
            } else {
                $optA = trim($q['option_a'] ?? '');
                $optB = trim($q['option_b'] ?? '');
                $optC = trim($q['option_c'] ?? '');
                $optD = trim($q['option_d'] ?? '');
            }

            $correctOpt = strtoupper(trim($q['correct_option'] ?? 'A'));
            if (!in_array($correctOpt, ['A', 'B', 'C', 'D'])) $correctOpt = 'A';

            $codeSnippet = !empty($q['code_snippet']) ? trim($q['code_snippet']) : null;
            $language = !empty($q['language']) ? strtolower(trim($q['language'])) : null;
            if ($codeSnippet && !$language) {
                if (str_contains($codeSnippet, '#include') || str_contains($codeSnippet, 'printf')) $language = 'c';
                elseif (str_contains($codeSnippet, 'System.out') || str_contains($codeSnippet, 'public class')) $language = 'java';
                elseif (str_contains($codeSnippet, 'def ') || str_contains($codeSnippet, 'print(')) $language = 'python';
                elseif (str_contains($codeSnippet, 'SELECT ') || str_contains($codeSnippet, 'DELETE ')) $language = 'sql';
                else $language = 'c';
            }

            $explanation = trim($q['explanation'] ?? 'Step-by-step solution.');
            $estTime = max(20, min(180, (int)($q['est_time_seconds'] ?? 60)));

            $company = trim($q['company_name'] ?? $q['company'] ?? 'General');
            $isCompanyPYQ = ($company !== 'General') ? 1 : 0;
            $sourceTag = trim($q['source_tag'] ?? ($company . ' Placement Paper'));

            // Verification status: strictly 'practice_style' for synthetic questions
            $vStatus = trim($q['verification_status'] ?? 'practice_style');
            if (!in_array($vStatus, ['verified_pyq', 'student_reported', 'practice_style'])) {
                $vStatus = 'practice_style';
            }

            $roundPattern = trim($q['round_pattern'] ?? ($company . ' Written Assessment'));

            // Run Verification Engine Gate
            $vRes = VerificationEngine::verifyQuestion([
                'question_text' => $rawText,
                'option_a' => $optA,
                'option_b' => $optB,
                'option_c' => $optC,
                'option_d' => $optD,
                'correct_option' => $correctOpt,
                'explanation' => $explanation,
                'code_snippet' => $codeSnippet,
                'language' => $language
            ]);

            $isPublished = $vRes['passed'] ? 1 : 0;
            $isVerified = $vRes['passed'] ? 1 : 0;
            $vNotes = $vRes['reason'];

            if ($vRes['passed']) {
                $publishedCount++;
            } else {
                $draftCount++;
            }

            if (!$isDryRun) {
                try {
                    $insertStmt->execute([
                        $subId,
                        $difficulty,
                        $rawText,
                        $codeSnippet,
                        $language,
                        $optA,
                        $optB,
                        $optC,
                        $optD,
                        $correctOpt,
                        $explanation,
                        $estTime,
                        $sourceTag,
                        $isCompanyPYQ,
                        $company,
                        $vStatus,
                        $roundPattern,
                        $isPublished,
                        $isVerified,
                        $batchId,
                        $vNotes
                    ]);
                    $existingLookup[$rawText] = true;
                    $importedCount++;
                } catch (Exception $e) {
                    $errors[] = "Row #{$idx}: " . $e->getMessage();
                }
            } else {
                $importedCount++;
            }
        }

        $res = [
            'status' => 'success',
            'batch_id' => $batchId,
            'dry_run' => $isDryRun,
            'imported_count' => $importedCount,
            'published_count' => $publishedCount,
            'draft_count' => $draftCount,
            'skipped_duplicates' => $skippedDuplicates,
            'total_submitted' => count($questionsData),
            'errors' => array_slice($errors, 0, 5),
            'message' => ($isDryRun ? "[DRY RUN] " : "") . "Batch Import Complete: {$importedCount} questions processed ({$publishedCount} published, {$draftCount} in draft), {$skippedDuplicates} duplicates safely skipped."
        ];
        if ($returnArray) return $res;
        echo json_encode($res);
        exit;
    }

    /**
     * Helper to parse CSV format into uniform question array
     */
    private static function parseCSVToQuestionArray(string $filePath): array {
        $handle = fopen($filePath, 'r');
        if (!$handle) return [];

        $headers = fgetcsv($handle);
        $questions = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) continue;
            $questions[] = [
                'company_name' => trim($row[0] ?? 'General'),
                'subtopic_code' => trim($row[1] ?? 'QUANT_SDT'),
                'difficulty' => trim($row[2] ?? 'medium'),
                'question_text' => trim($row[3] ?? ''),
                'option_a' => trim($row[4] ?? ''),
                'option_b' => trim($row[5] ?? ''),
                'option_c' => trim($row[6] ?? ''),
                'option_d' => trim($row[7] ?? ''),
                'correct_option' => trim($row[8] ?? 'A'),
                'explanation' => trim($row[9] ?? ''),
                'code_snippet' => trim($row[10] ?? ''),
                'language' => trim($row[11] ?? ''),
                'round_pattern' => trim($row[12] ?? 'Written Assessment'),
                'verification_status' => trim($row[13] ?? 'practice_style')
            ];
        }
        fclose($handle);
        return $questions;
    }

    /**
     * Legacy CSV import handler forwarding to unified batch importer
     */
    public static function importCSVQuestionsJSON(): void {
        self::importBatchQuestionsJSON();
    }

    /**
     * Get all company profiles for Admin CMS
     */
    public static function getAllCompanyProfiles(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM company_profiles ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Admin endpoint to update company profile
     */
    public static function updateCompanyProfileJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId || !User::isAdmin($userId)) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int)($input['id'] ?? 0);
        $testPattern = trim($input['test_pattern'] ?? '');
        $syllabus = trim($input['syllabus'] ?? '');
        $category = trim($input['category'] ?? '');
        $difficultyTier = trim($input['difficulty_tier'] ?? 'standard');
        $lastChecked = trim($input['last_checked'] ?? date('Y-m-d'));
        $rounds = $input['rounds'] ?? [];
        if (is_string($rounds)) {
            $rounds = array_values(array_filter(array_map('trim', explode("\n", $rounds))));
        }
        $roundsJson = json_encode($rounds);

        if (!str_contains($testPattern, 'Reported pattern, please verify')) {
            $testPattern .= " (Reported pattern, please verify)";
        }
        if (!str_contains($syllabus, 'Reported pattern, please verify')) {
            $syllabus .= " (Reported pattern, please verify)";
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE company_profiles
            SET test_pattern = ?, syllabus = ?, category = ?, difficulty_tier = ?, last_checked = ?, rounds_json = ?
            WHERE id = ?
        ");
        $stmt->execute([$testPattern, $syllabus, $category, $difficultyTier, $lastChecked, $roundsJson, $id]);

        echo json_encode(['status' => 'success', 'message' => 'Company profile updated successfully!']);
        exit;
    }

    /**
     * Get questions needing human review for the Admin Review Queue
     */
    public static function getUncheckedQuestions(string $company = '', int $limit = 50): array {
        $db = Database::getConnection();
        $params = [];
        $sql = "
            SELECT q.id, q.company_name, q.question_text, q.difficulty, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.explanation, q.verification_level, s.name as subtopic_name
            FROM questions q
            LEFT JOIN subtopics s ON q.subtopic_id = s.id
            WHERE q.verification_level = 'unchecked' AND q.is_published = 1
        ";
        if ($company !== '') {
            $sql .= " AND q.company_name = ? ";
            $params[] = $company;
        }
        $sql .= " ORDER BY q.id ASC LIMIT " . (int)$limit;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark a question as reviewed by admin
     */
    public static function markQuestionReviewedJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId || !User::isAdmin($userId)) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int)($input['question_id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid question ID']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE questions 
            SET verification_level = 'reviewed', reviewed_by = ?, reviewed_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$userId, $id]);

        echo json_encode([
            'status' => 'success', 
            'message' => "Question #{$id} marked as Human Reviewed!",
            'question_id' => $id,
            'verification_level' => 'reviewed'
        ]);
        exit;
    }

    /**
     * Admin: Create Single Coupon
     */
    public static function createCouponJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $adminId = (int)$_SESSION['user_id'];
        $res = CouponService::createCoupon($input, $adminId);

        if ($res['status'] === 'error') {
            http_response_code(400);
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Admin: Bulk Generate Coupons
     */
    public static function bulkCouponsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $adminId = (int)$_SESSION['user_id'];
        $quantity = (int)($input['quantity'] ?? 10);
        $res = CouponService::bulkGenerate($input, $quantity, $adminId);

        if ($res['status'] === 'error') {
            http_response_code(400);
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Admin: Toggle Active State
     */
    public static function toggleCouponJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $couponId = (int)($input['coupon_id'] ?? 0);
        if (!$couponId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid coupon ID']);
            exit;
        }

        $ok = CouponService::toggleActive($couponId);
        echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $ok ? 'Coupon status toggled' : 'Failed to toggle status']);
        exit;
    }

    /**
     * Admin: List Coupons (Filtered)
     */
    public static function listCouponsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        self::checkAdminAuth();

        $filters = [];
        if (!empty($_GET['search'])) $filters['search'] = trim($_GET['search']);
        if (!empty($_GET['status'])) $filters['status'] = trim($_GET['status']);

        $coupons = CouponService::listCoupons($filters);
        echo json_encode(['status' => 'success', 'data' => $coupons]);
        exit;
    }

    /**
     * Admin: Get Coupon Redemption History
     */
    public static function couponRedemptionsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        self::checkAdminAuth();

        $couponId = (int)($_GET['coupon_id'] ?? ($_POST['coupon_id'] ?? 0));
        if (!$couponId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid coupon ID']);
            exit;
        }

        $history = CouponService::getRedemptionHistory($couponId);
        echo json_encode(['status' => 'success', 'data' => $history]);
        exit;
    }

    /**
     * Web-Safe Database Migration
     */
    public static function migrateDatabaseJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        ob_start();
        try {
            runMasterMigration();
            $log = ob_get_clean();
            echo json_encode(['status' => 'success', 'message' => 'Migration ran successfully', 'log' => $log]);
        } catch (\Throwable $e) {
            ob_end_clean();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Migration failed: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Web-Safe Database Rollback
     * Strictly gated to admin + CSRF + APP_ENV=local (or ALLOW_WEB_ROLLBACK=true) + typed table name confirmation.
     */
    public static function rollbackDatabaseJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        // 1. Environment Guard: disable unless APP_ENV=local or ALLOW_WEB_ROLLBACK=true
        $isLocal = (defined('APP_ENV') && APP_ENV === 'local') || getenv('APP_ENV') === 'local';
        $allowRollback = getenv('ALLOW_WEB_ROLLBACK') === 'true' || ($_ENV['ALLOW_WEB_ROLLBACK'] ?? '') === 'true';
        if (!$isLocal && !$allowRollback) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Web rollback console is disabled in non-local environments. Set ALLOW_WEB_ROLLBACK=true in .env or run via CLI: php scripts/migrate_all.php rollback <step>.'
            ]);
            exit;
        }

        $step = trim($input['step'] ?? 'coupons');

        // 2. Checkbox confirmation guard
        if (empty($input['confirmed'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Rollback confirmation required. You must check the confirmation box acknowledging data loss.'
            ]);
            exit;
        }

        // 3. Typed table name matching guard
        $typedTable = trim($input['typed_table'] ?? '');
        if ($typedTable !== $step) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => "Confirmation failed: You must type '{$step}' into the confirmation field to proceed with dropping this table."
            ]);
            exit;
        }

        ob_start();
        try {
            rollbackMasterMigration(null, $step);
            $log = ob_get_clean();
            self::logAdminAction((int)($_SESSION['user_id'] ?? 0), 'database_rollback', 'table', null, "Rolled back step '{$step}'");
            echo json_encode(['status' => 'success', 'message' => "Rollback for step '{$step}' completed", 'log' => $log]);
        } catch (\Throwable $e) {
            ob_end_clean();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Rollback failed: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Audit Logging Helper
     */
    public static function logAdminAction(int $adminId, string $action, string $targetType, ?int $targetId, string $details): void {
        try {
            $db = Database::getConnection();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmt = $db->prepare("
                INSERT INTO admin_audit_log (admin_id, action, target_type, target_id, details, ip_address)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$adminId, $action, $targetType, $targetId, $details, $ip]);
        } catch (\Throwable $e) {
            error_log("Failed to log admin action: " . $e->getMessage());
        }
    }

    /**
     * KPI Dashboard Statistics
     */
    public static function getKpiStats(): array {
        $db = Database::getConnection();
        
        // Active students (attempted in last 7 days)
        $activeUsers = (int)$db->query("
            SELECT COUNT(DISTINCT user_id) 
            FROM attempts 
            WHERE attempted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ")->fetchColumn();

        // Total published questions
        $totalQuestions = (int)$db->query("SELECT COUNT(*) FROM questions WHERE is_published = 1")->fetchColumn();

        // Economy Flow: Coins minted vs spent
        $coinsFlow = $db->query("
            SELECT 
                COALESCE(SUM(CASE WHEN delta > 0 THEN delta ELSE 0 END), 0) as minted,
                COALESCE(SUM(CASE WHEN delta < 0 THEN ABS(delta) ELSE 0 END), 0) as spent
            FROM coin_transactions
        ")->fetch(PDO::FETCH_ASSOC);

        // Pending submissions & reports
        $pendingSubmissions = (int)$db->query("SELECT COUNT(*) FROM student_question_submissions WHERE status = 'pending'")->fetchColumn();
        $pendingReports = (int)$db->query("SELECT COUNT(*) FROM question_reports WHERE status = 'pending'")->fetchColumn();

        // Mock test attempts
        $mockAttempts = (int)$db->query("SELECT COUNT(*) FROM attempts WHERE mode = 'mock'")->fetchColumn();

        return [
            'active_students' => $activeUsers,
            'total_questions' => $totalQuestions,
            'coins_minted' => (int)($coinsFlow['minted'] ?? 0),
            'coins_spent' => (int)($coinsFlow['spent'] ?? 0),
            'pending_submissions' => $pendingSubmissions,
            'pending_reports' => $pendingReports,
            'mock_attempts' => $mockAttempts
        ];
    }

    /**
     * User Search JSON Endpoint
     */
    /**
     * User Search JSON Endpoint with Advanced Filters & Aggregates
     */
    public static function searchUsersJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();

        $query = trim($_GET['q'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $tier = trim($_GET['tier'] ?? '');
        $plan = trim($_GET['plan'] ?? '');
        $limit = min(100, max(10, (int)($_GET['limit'] ?? 50)));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $db = Database::getConnection();

        // 1. Calculate overall KPI metrics
        $kpi = [
            'total_users' => 0,
            'total_admins' => 0,
            'total_students' => 0,
            'total_coins' => 0,
            'active_streaks' => 0
        ];
        try {
            $stats = $db->query("
                SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins,
                    SUM(CASE WHEN role != 'admin' OR role IS NULL THEN 1 ELSE 0 END) as total_students,
                    COALESCE(SUM(coins), 0) as total_coins,
                    SUM(CASE WHEN current_streak > 0 THEN 1 ELSE 0 END) as active_streaks
                FROM users
            ")->fetch(PDO::FETCH_ASSOC);
            if ($stats) {
                $kpi = [
                    'total_users' => (int)$stats['total_users'],
                    'total_admins' => (int)$stats['total_admins'],
                    'total_students' => (int)$stats['total_students'],
                    'total_coins' => (int)$stats['total_coins'],
                    'active_streaks' => (int)$stats['active_streaks']
                ];
            }
        } catch (\Throwable $e) {}

        // 2. Build filtered query
        $where = [];
        $params = [];

        if (strlen($query) > 0) {
            if (is_numeric($query)) {
                $where[] = "(id = ? OR name LIKE ? OR email LIKE ?)";
                $params[] = (int)$query;
                $params[] = '%' . $query . '%';
                $params[] = '%' . $query . '%';
            } else {
                $where[] = "(name LIKE ? OR email LIKE ?)";
                $params[] = '%' . $query . '%';
                $params[] = '%' . $query . '%';
            }
        }

        if (!empty($role) && in_array($role, ['admin', 'user'])) {
            if ($role === 'user') {
                $where[] = "(role = 'user' OR role IS NULL OR role = '')";
            } else {
                $where[] = "role = 'admin'";
            }
        }

        if (!empty($tier) && in_array($tier, ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond'])) {
            $where[] = "tier = ?";
            $params[] = $tier;
        }

        if (!empty($plan) && in_array($plan, ['free', 'premium'])) {
            $where[] = "plan = ?";
            $params[] = $plan;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // Total matching count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM users {$whereClause}");
        $countStmt->execute($params);
        $totalCount = (int)$countStmt->fetchColumn();

        // Paginated rows
        $sql = "
            SELECT id, name, email, role, plan, coins, total_xp, tier, current_streak, streak_freezes, created_at, last_active_date
            FROM users
            {$whereClause}
            ORDER BY id DESC
            LIMIT {$limit} OFFSET {$offset}
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'users' => $users,
            'total_count' => $totalCount,
            'page' => $page,
            'limit' => $limit,
            'kpi' => $kpi
        ]);
        exit;
    }

    /**
     * User Detail JSON Endpoint (Balances, Mistakes, Unlocks, Coin Transactions)
     */
    public static function getUserDetailsJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();

        $userId = (int)($_GET['user_id'] ?? 0);
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT id, name, email, role, plan, coins, total_xp, tier, current_streak, streak_freezes, created_at, last_active_date, bio, avatar_icon, referral_code FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        // Recent attempts & mistakes
        $attStmt = $db->prepare("
            SELECT a.id, a.question_id, a.is_correct, a.selected_option, a.time_taken_seconds, a.xp_earned, a.coins_earned, a.attempted_at, q.question_text
            FROM attempts a
            LEFT JOIN questions q ON a.question_id = q.id
            WHERE a.user_id = ?
            ORDER BY a.attempted_at DESC LIMIT 10
        ");
        $attStmt->execute([$userId]);
        $recentAttempts = $attStmt->fetchAll(PDO::FETCH_ASSOC);

        // Coin Transactions History
        $txStmt = $db->prepare("
            SELECT id, amount, transaction_type, reference_description, created_at
            FROM coin_transactions
            WHERE user_id = ?
            ORDER BY id DESC LIMIT 15
        ");
        $txStmt->execute([$userId]);
        $coinTransactions = $txStmt->fetchAll(PDO::FETCH_ASSOC);

        // Unlocked items
        $unlockedStmt = $db->prepare("
            SELECT u.id, u.code, u.title, u.item_type, uu.unlocked_at
            FROM user_unlocks uu
            JOIN unlockables u ON uu.unlockable_id = u.id
            WHERE uu.user_id = ?
            ORDER BY uu.unlocked_at DESC
        ");
        $unlockedStmt->execute([$userId]);
        $unlocks = $unlockedStmt->fetchAll(PDO::FETCH_ASSOC);

        // Saved quiz session if any
        $sessStmt = $db->prepare("SELECT * FROM user_practice_sessions WHERE user_id = ?");
        $sessStmt->execute([$userId]);
        $savedSession = $sessStmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'user' => $user,
            'recent_attempts' => $recentAttempts,
            'coin_transactions' => $coinTransactions,
            'unlocks' => $unlocks,
            'saved_session' => $savedSession
        ]);
        exit;
    }

    /**
     * Credit / Gift User Coins (Admin-only + CSRF + Reason + Audit Log)
     * Supports single user gifting or bulk gifting by user_ids / tier
     */
    public static function creditUserCoinsJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $adminId = (int)($_SESSION['user_id'] ?? 0);
        $amount = (int)($input['amount'] ?? 0);
        $reason = trim($input['reason'] ?? '');
        $mode = $input['mode'] ?? 'single'; // 'single' or 'bulk'

        if ($amount <= 0 || $amount > 10000) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => "Amount must be between 1 and 10,000 Speed Coins"]);
            exit;
        }

        if (strlen($reason) < 3) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'A specific reason is required for coin gifting']);
            exit;
        }

        $db = Database::getConnection();

        // Handle Bulk Gifting
        if ($mode === 'bulk') {
            $targetTier = trim($input['target_tier'] ?? '');
            $rawUserIds = trim($input['user_ids'] ?? '');

            $targetUserIds = [];

            if (!empty($targetTier) && in_array($targetTier, ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond'])) {
                $stmt = $db->prepare("SELECT id FROM users WHERE tier = ?");
                $stmt->execute([$targetTier]);
                $targetUserIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            } elseif (!empty($rawUserIds)) {
                $parts = preg_split('/[\s,]+/', $rawUserIds);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (is_numeric($p) && (int)$p > 0) {
                        $targetUserIds[] = (int)$p;
                    } elseif (filter_var($p, FILTER_VALIDATE_EMAIL)) {
                        $uStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                        $uStmt->execute([$p]);
                        $foundId = $uStmt->fetchColumn();
                        if ($foundId) $targetUserIds[] = (int)$foundId;
                    }
                }
                $targetUserIds = array_unique($targetUserIds);
            }

            if (empty($targetUserIds)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'No valid recipients found for bulk coin gifting']);
                exit;
            }

            $db->beginTransaction();
            try {
                $awardedCount = 0;
                $updateStmt = $db->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
                $txStmt = $db->prepare("
                    INSERT INTO coin_transactions (user_id, amount, transaction_type, reference_description, created_at)
                    VALUES (?, ?, 'admin_grant', ?, NOW())
                ");

                foreach ($targetUserIds as $uId) {
                    $updateStmt->execute([$amount, $uId]);
                    $txStmt->execute([$uId, $amount, "Bulk Gift: " . $reason]);
                    $awardedCount++;
                }

                self::logAdminAction($adminId, 'bulk_credit_coins', 'users', null, "Bulk gifted {$amount} coins to {$awardedCount} users. Reason: {$reason}");

                $db->commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => "Successfully gifted {$amount} Speed Coins to {$awardedCount} users!",
                    'awarded_count' => $awardedCount
                ]);
            } catch (\Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Bulk credit failed: ' . $e->getMessage()]);
            }
            exit;
        }

        // Single User Gifting
        $userId = (int)($input['user_id'] ?? 0);
        if ($userId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Valid user ID is required']);
            exit;
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare("SELECT id, name, coins FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$u) {
                $db->rollBack();
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                exit;
            }

            $newBalance = (int)$u['coins'] + $amount;
            $db->prepare("UPDATE users SET coins = ? WHERE id = ?")->execute([$newBalance, $userId]);

            // Insert coin transaction with proper schema
            $db->prepare("
                INSERT INTO coin_transactions (user_id, amount, transaction_type, reference_description, created_at)
                VALUES (?, ?, 'admin_grant', ?, NOW())
            ")->execute([$userId, $amount, $reason]);

            // Insert admin audit log
            self::logAdminAction($adminId, 'credit_coins', 'user', $userId, "Gifted {$amount} coins to {$u['name']} (#{$userId}). Reason: {$reason}. New balance: {$newBalance}");

            $db->commit();
            echo json_encode([
                'status' => 'success',
                'message' => "Successfully gifted {$amount} Speed Coins to {$u['name']}.",
                'new_balance' => $newBalance
            ]);
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to gift coins: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Update User Role (Promote to Admin / Demote to User)
     */
    public static function updateUserRoleJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $userId = (int)($input['user_id'] ?? 0);
        $newRole = trim($input['role'] ?? '');
        $adminId = (int)($_SESSION['user_id'] ?? 0);

        if ($userId <= 0 || !in_array($newRole, ['admin', 'user'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid user ID or role']);
            exit;
        }

        // Prevent self-demotion
        if ($userId === $adminId && $newRole !== 'admin') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'You cannot demote your own administrator account.']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$targetUser) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        // Prevent demoting if this is the last admin
        if ($newRole === 'user') {
            $adminCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
            if ($adminCount <= 1 && ($targetUser['role'] ?? '') === 'admin') {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Cannot demote the only remaining administrator.']);
                exit;
            }
        }

        $db->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$newRole, $userId]);
        User::clearCache($userId);
        if ($userId === $adminId) {
            $_SESSION['user_role'] = $newRole;
            session_regenerate_id(true);
        }
        self::logAdminAction($adminId, 'change_role', 'user', $userId, "Changed role of {$targetUser['name']} (#{$userId}) from '{$targetUser['role']}' to '{$newRole}'");

        echo json_encode([
            'status' => 'success',
            'message' => "Role updated to " . ucfirst($newRole) . " successfully.",
            'new_role' => $newRole
        ]);
        exit;
    }

    /**
     * Update User Stats (Plan, Tier, Streaks, Streak Freezes)
     */
    public static function updateUserStatsJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $userId = (int)($input['user_id'] ?? 0);
        $adminId = (int)($_SESSION['user_id'] ?? 0);

        if ($userId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Valid user ID is required']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, plan, tier, current_streak, streak_freezes FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$u) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        $updates = [];
        $params = [];
        $logDetails = [];

        if (isset($input['plan']) && in_array($input['plan'], ['free', 'premium'])) {
            $updates[] = "plan = ?";
            $params[] = $input['plan'];
            $logDetails[] = "Plan -> " . $input['plan'];
        }

        if (isset($input['tier']) && in_array($input['tier'], ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond'])) {
            $updates[] = "tier = ?";
            $params[] = $input['tier'];
            $logDetails[] = "Tier -> " . $input['tier'];
        }

        if (isset($input['streak_freezes']) && is_numeric($input['streak_freezes'])) {
            $freezes = max(0, min(20, (int)$input['streak_freezes']));
            $updates[] = "streak_freezes = ?";
            $params[] = $freezes;
            $logDetails[] = "Streak Freezes -> " . $freezes;
        }

        if (isset($input['current_streak']) && is_numeric($input['current_streak'])) {
            $streak = max(0, min(1000, (int)$input['current_streak']));
            $updates[] = "current_streak = ?";
            $params[] = $streak;
            $logDetails[] = "Current Streak -> " . $streak;
        }

        if (empty($updates)) {
            echo json_encode(['status' => 'success', 'message' => 'No modifications specified.']);
            exit;
        }

        $params[] = $userId;
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $db->prepare($sql)->execute($params);

        self::logAdminAction($adminId, 'update_user_stats', 'user', $userId, "Updated stats for {$u['name']} (#{$userId}): " . implode(', ', $logDetails));

        echo json_encode([
            'status' => 'success',
            'message' => 'User attributes updated successfully.'
        ]);
        exit;
    }

    /**
     * Admin Temporary Password Reset
     */
    public static function resetUserPasswordAdminJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $userId = (int)($input['user_id'] ?? 0);
        $adminId = (int)($_SESSION['user_id'] ?? 0);
        $customPassword = trim($input['password'] ?? '');

        if ($userId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Valid user ID is required']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$u) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        // Generate temporary password if not provided
        $tempPass = !empty($customPassword) ? $customPassword : ('AptPass' . random_int(1000, 9999) . '!');
        $hash = password_hash($tempPass, PASSWORD_BCRYPT);

        $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $userId]);
        self::logAdminAction($adminId, 'reset_password', 'user', $userId, "Admin reset temporary password for {$u['name']} (#{$userId})");

        echo json_encode([
            'status' => 'success',
            'message' => "Password reset successfully for {$u['name']}.",
            'temp_password' => $tempPass
        ]);
        exit;
    }

    /**
     * Reset / Restore Streak Freeze (Admin-only + CSRF + Audit Log)
     */
    public static function resetUserStreakFreezeJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $userId = (int)($input['user_id'] ?? 0);
        $adminId = (int)($_SESSION['user_id'] ?? 0);

        if ($userId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Valid user ID is required']);
            exit;
        }

        $db = Database::getConnection();
        // Clear recent failed session or reset streak freeze shield
        $db->prepare("DELETE FROM user_practice_sessions WHERE user_id = ?")->execute([$userId]);
        
        self::logAdminAction($adminId, 'reset_session', 'user', $userId, "Cleared locked/stale practice session for user #{$userId}");

        echo json_encode([
            'status' => 'success',
            'message' => "Practice session cache reset successfully for user #{$userId}"
        ]);
        exit;
    }

    /**
     * List Questions for Admin Management (Filtering by Company, Status, Search)
     */
    public static function listQuestionsAdminJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();

        $company = trim($_GET['company'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $search = trim($_GET['search'] ?? '');
        $limit = min(100, max(10, (int)($_GET['limit'] ?? 25)));
        $offset = max(0, (int)($_GET['offset'] ?? 0));

        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($company)) {
            $where[] = "(q.company_name LIKE ? OR q.source_tag LIKE ?)";
            $params[] = "%{$company}%";
            $params[] = "%{$company}%";
        }
        if ($status === 'published') {
            $where[] = "q.is_published = 1";
        } elseif ($status === 'unpublished') {
            $where[] = "q.is_published = 0";
        }
        if (!empty($search)) {
            $where[] = "(q.question_text LIKE ? OR q.id = ?)";
            $params[] = "%{$search}%";
            $params[] = is_numeric($search) ? (int)$search : 0;
        }

        $whereClause = implode(" AND ", $where);

        $countStmt = $db->prepare("SELECT COUNT(*) FROM questions q WHERE {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "
            SELECT q.id, q.question_text, q.difficulty, q.company_name, q.correct_option, q.is_published, q.verification_status, s.name as subtopic_name
            FROM questions q
            LEFT JOIN subtopics s ON q.subtopic_id = s.id
            WHERE {$whereClause}
            ORDER BY q.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'total' => $total,
            'questions' => $questions
        ]);
        exit;
    }

    /**
     * Bulk Question Actions with Required Typed Confirmation ("CONFIRM")
     */
    public static function bulkQuestionActionJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $actionType = trim($input['action_type'] ?? '');
        $questionIds = array_map('intval', (array)($input['question_ids'] ?? []));
        $typedConfirmation = trim($input['typed_confirmation'] ?? '');
        $adminId = (int)($_SESSION['user_id'] ?? 0);

        if ($typedConfirmation !== 'CONFIRM') {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Typed confirmation "CONFIRM" is required to execute bulk question actions.'
            ]);
            exit;
        }

        if (empty($questionIds)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No questions selected for bulk action']);
            exit;
        }

        $db = Database::getConnection();
        $inClause = implode(',', $questionIds);

        if ($actionType === 'bulk_depublish') {
            $db->exec("UPDATE questions SET is_published = 0 WHERE id IN ($inClause)");
            $msg = "Depublished " . count($questionIds) . " questions.";
        } elseif ($actionType === 'bulk_publish') {
            $db->exec("UPDATE questions SET is_published = 1 WHERE id IN ($inClause)");
            $msg = "Published " . count($questionIds) . " questions.";
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid bulk action type']);
            exit;
        }

        self::logAdminAction($adminId, $actionType, 'questions', null, "{$msg} IDs: {$inClause}");

        echo json_encode([
            'status' => 'success',
            'message' => $msg,
            'affected_count' => count($questionIds)
        ]);
        exit;
    }

    /**
     * Audit Log Viewer JSON Endpoint
     */
    public static function getAuditLogsJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();

        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT al.*, u.name as admin_name, u.email as admin_email
            FROM admin_audit_log al
            LEFT JOIN users u ON al.admin_id = u.id
            ORDER BY al.created_at DESC
            LIMIT 50
        ");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'logs' => $logs]);
        exit;
    }

    /**
     * Get Mock Blueprints & Lengths Configuration
     */
    public static function getMockConfigsJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();

        $db = Database::getConnection();
        $unlockableId = isset($_GET['unlockable_id']) ? (int)$_GET['unlockable_id'] : 0;

        $query = "
            SELECT mc.*, u.code as pack_code, u.title as pack_title
            FROM mock_configs mc
            JOIN unlockables u ON mc.unlockable_id = u.id
        ";
        $params = [];
        if ($unlockableId > 0) {
            $query .= " WHERE mc.unlockable_id = ?";
            $params[] = $unlockableId;
        }
        $query .= " ORDER BY u.id ASC, FIELD(mc.mode, 'quick', 'standard', 'full')";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($configs as &$cfg) {
            $cfg['blueprint_data'] = json_decode($cfg['blueprint'], true);
        }
        unset($cfg);

        // Also fetch list of available topic codes for blueprint builder
        $topics = $db->query("SELECT id, name, code FROM topics ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
        $packs = $db->query("SELECT id, code, title FROM unlockables WHERE item_type = 'company_mock' OR code LIKE '%MOCK%' ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'configs' => $configs,
            'topics' => $topics,
            'packs' => $packs
        ]);
        exit;
    }

    /**
     * Save Mock Blueprint & Length Configuration with Rigorous Validation (ML9)
     */
    public static function saveMockConfigJSON(): void {
        header('Content-Type: application/json');
        self::checkAdminAuth();

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyAdminCsrf($input);

        $unlockableId = (int)($input['unlockable_id'] ?? 0);
        $mode = strtolower(trim($input['mode'] ?? ''));

        if ($unlockableId <= 0 && !empty($input['config_id'])) {
            $dbTemp = Database::getConnection();
            $cfgStmt = $dbTemp->prepare("SELECT unlockable_id, mode FROM mock_configs WHERE id = ?");
            $cfgStmt->execute([(int)$input['config_id']]);
            $cfgRow = $cfgStmt->fetch(PDO::FETCH_ASSOC);
            if ($cfgRow) {
                $unlockableId = (int)$cfgRow['unlockable_id'];
                if (empty($mode)) $mode = $cfgRow['mode'];
            }
        }
        $questionCount = (int)($input['question_count'] ?? 0);
        $durationMinutes = !empty($input['duration_minutes']) ? (int)$input['duration_minutes'] : null;
        $negativeMarking = isset($input['negative_marking']) ? (float)$input['negative_marking'] : 0.25;
        $blueprintRaw = $input['blueprint'] ?? null;

        if ($unlockableId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Valid company pack unlockable ID is required']);
            exit;
        }

        if (!in_array($mode, ['quick', 'standard', 'full'], true)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid mode. Mode must be quick, standard, or full']);
            exit;
        }

        if ($questionCount <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Question count must be greater than zero']);
            exit;
        }

        if ($negativeMarking < 0.0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Negative marking penalty cannot be negative']);
            exit;
        }

        // Validate blueprint structure
        $blueprintData = is_array($blueprintRaw) ? $blueprintRaw : json_decode((string)$blueprintRaw, true);
        if (!is_array($blueprintData) || empty($blueprintData['sections']) || !is_array($blueprintData['sections'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Blueprint must be a valid JSON object containing a non-empty "sections" array']);
            exit;
        }

        $db = Database::getConnection();

        // Check unlockable exists
        $uStmt = $db->prepare("SELECT id, code, title FROM unlockables WHERE id = ?");
        $uStmt->execute([$unlockableId]);
        $pack = $uStmt->fetch(PDO::FETCH_ASSOC);
        if (!$pack) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Company pack not found']);
            exit;
        }

        // Fetch valid topic codes from database
        $validTopicCodes = $db->query("SELECT code FROM topics")->fetchAll(PDO::FETCH_COLUMN);

        $totalSectionCount = 0;
        foreach ($blueprintData['sections'] as $idx => $sec) {
            if (empty($sec['section_name'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Section #" . ($idx + 1) . " requires a non-empty section_name"]);
                exit;
            }

            $topicCode = strtoupper(trim($sec['topic_code'] ?? ''));
            if (!in_array($topicCode, $validTopicCodes, true)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Invalid section topic_code '{$topicCode}' in section #" . ($idx + 1) . ". Section topic must exist in topics table"]);
                exit;
            }

            $secCount = (int)($sec['count'] ?? 0);
            if ($secCount <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Section '{$sec['section_name']}' question count must be greater than zero"]);
                exit;
            }

            $totalSectionCount += $secCount;

            // Validate difficulty mix
            if (isset($sec['difficulty_mix']) && is_array($sec['difficulty_mix'])) {
                $e = (int)($sec['difficulty_mix']['easy'] ?? 0);
                $m = (int)($sec['difficulty_mix']['medium'] ?? 0);
                $h = (int)($sec['difficulty_mix']['hard'] ?? 0);
                if (($e + $m + $h) !== $secCount) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Section '{$sec['section_name']}' difficulty mix (easy: $e + med: $m + hard: $h = " . ($e + $m + $h) . ") must add up exactly to section count ($secCount)"]);
                    exit;
                }
            }
        }

        // Validate that section counts add up to total question count
        if ($totalSectionCount !== $questionCount) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => "Blueprint section counts ({$totalSectionCount}) do not add up to total question count ({$questionCount})"
            ]);
            exit;
        }

        $blueprintJson = json_encode($blueprintData, JSON_UNESCAPED_SLASHES);

        $stmt = $db->prepare("
            INSERT INTO mock_configs 
            (unlockable_id, mode, question_count, duration_minutes, blueprint, negative_marking, active)
            VALUES (?, ?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE 
                question_count = VALUES(question_count),
                duration_minutes = VALUES(duration_minutes),
                blueprint = VALUES(blueprint),
                negative_marking = VALUES(negative_marking),
                active = VALUES(active)
        ");
        $stmt->execute([
            $unlockableId,
            $mode,
            $questionCount,
            $durationMinutes,
            $blueprintJson,
            $negativeMarking
        ]);

        $adminId = (int)($_SESSION['user_id'] ?? 0);
        self::logAdminAction($adminId, 'update_mock_blueprint', 'mock_configs', $unlockableId, "Updated mock blueprint for {$pack['title']} mode: {$mode}, count: {$questionCount}");

        echo json_encode([
            'status' => 'success',
            'message' => "Mock blueprint and lengths updated successfully for {$pack['title']} ({$mode} mode).",
            'config' => [
                'unlockable_id' => $unlockableId,
                'pack_code' => $pack['code'],
                'mode' => $mode,
                'question_count' => $questionCount,
                'duration_minutes' => $durationMinutes,
                'negative_marking' => $negativeMarking
            ]
        ]);
        exit;
    }
}


