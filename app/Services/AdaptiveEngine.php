<?php
require_once __DIR__ . '/../../config/db.php';

class AdaptiveEngine {

    /**
     * Select next recommended question for a user based on level progression, tier, accuracy, and language
     */
    public static function getNextQuestion(
        ?int $userId, 
        ?int $subtopicId = null, 
        string $preferredDifficulty = 'medium', 
        array $excludeQuestionIds = [], 
        ?string $language = null,
        ?string $forcedDifficulty = null,
        ?string $track = null
    ): ?array {
        $db = Database::getConnection();

        // 1. Determine User Tier, Level & Mastery Profile
        $userTier = 'Bronze';
        $userStreak = 0;
        $userXP = 0;

        if ($userId) {
            $userStmt = $db->prepare("SELECT tier, current_streak, total_xp FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            $userData = $userStmt->fetch();
            if ($userData) {
                $userTier = $userData['tier'] ?? 'Bronze';
                $userStreak = (int)($userData['current_streak'] ?? 0);
                $userXP = (int)($userData['total_xp'] ?? 0);
            }
        }

        // 2. Progressive Difficulty Determination (Avoid simple questions as level increases!)
        $targetDifficulty = $preferredDifficulty;

        if ($forcedDifficulty && in_array(strtolower($forcedDifficulty), ['easy', 'medium', 'hard'])) {
            $targetDifficulty = strtolower($forcedDifficulty);
        } else {
            // Check recent attempt accuracy (either for this subtopic or across recent global attempts)
            $recentAttempts = [];
            if ($userId) {
                if ($subtopicId) {
                    $stmt = $db->prepare("
                        SELECT is_correct FROM attempts 
                        WHERE user_id = ? AND question_id IN (SELECT id FROM questions WHERE subtopic_id = ?)
                        ORDER BY attempted_at DESC LIMIT 6
                    ");
                    $stmt->execute([$userId, $subtopicId]);
                } else {
                    $stmt = $db->prepare("
                        SELECT is_correct FROM attempts 
                        WHERE user_id = ? 
                        ORDER BY attempted_at DESC LIMIT 6
                    ");
                    $stmt->execute([$userId]);
                }
                $recentAttempts = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            $accuracy = 0.5;
            if (count($recentAttempts) >= 3) {
                $correctCount = array_sum($recentAttempts);
                $accuracy = $correctCount / count($recentAttempts);
            }

            // LEVEL PROGRESSION LOGIC:
            // - Diamond/Platinum Tier: Strictly HARD questions (Boss challenge)
            // - Gold Tier: HARD (85% chance) or MEDIUM
            // - Silver Tier: HARD if accuracy >= 0.5, else MEDIUM. NEVER EASY!
            // - Bronze Tier: MEDIUM by default; promotes to HARD when accuracy >= 0.7; only dips to easy if <= 0.25
            if (in_array($userTier, ['Platinum', 'Diamond']) || $userXP >= 8000) {
                $targetDifficulty = 'hard';
            } else if ($userTier === 'Gold' || $userXP >= 3500) {
                $targetDifficulty = ($accuracy >= 0.4) ? 'hard' : 'medium';
            } else if ($userTier === 'Silver' || $userXP >= 1000 || $userStreak >= 5) {
                $targetDifficulty = ($accuracy >= 0.5) ? 'hard' : 'medium';
            } else {
                // Bronze / Beginners: Avoid simple questions by defaulting to medium
                if ($accuracy >= 0.67) {
                    $targetDifficulty = 'hard';
                } else if ($accuracy <= 0.25 && count($recentAttempts) >= 4) {
                    $targetDifficulty = 'easy';
                } else {
                    $targetDifficulty = 'medium';
                }
            }
        }

        // Base query
        $baseSql = "SELECT q.*, s.name as subtopic_name, t.name as topic_name 
                    FROM questions q 
                    JOIN subtopics s ON q.subtopic_id = s.id 
                    JOIN topics t ON s.topic_id = t.id 
                    WHERE 1=1";
        
        $params = [];

        // Track and category filtering
        if ($track === 'technical' || (!empty($language) && in_array(strtolower($language), ['c', 'cpp', 'java', 'python', 'sql', 'dsa', 'php', 'csharp', 'innovature']))) {
            // Strictly Technical questions
            if ($language === 'innovature') {
                $baseSql .= " AND (q.language = 'innovature' OR q.company_name LIKE '%Innovature%' OR s.topic_id = 6)";
            } else if (!empty($language) && $language !== 'all') {
                $baseSql .= " AND q.language = ?";
                $params[] = strtolower($language);
            } else {
                // All technical questions: topic_id 5 (Technical Assessment) or 6 (Company PYQs) or valid technical language
                $baseSql .= " AND (s.topic_id IN (5, 6) OR (q.language IS NOT NULL AND q.language != '' AND q.language NOT IN ('quant', 'lr')))";
            }
        } else if ($language === 'innovature') {
            $baseSql .= " AND (q.language = 'innovature' OR q.company_name LIKE '%Innovature%' OR s.topic_id = 6)";
        } else if (!empty($language) && $language !== 'all') {
            $baseSql .= " AND q.language = ?";
            $params[] = strtolower($language);
        } else if ($subtopicId) {
            $baseSql .= " AND q.subtopic_id = ?";
            $params[] = $subtopicId;
        } else {
            // General Practice: include general questions (topics 1-4) and non-company pyqs
            $baseSql .= " AND (q.is_company_pyq = 0) AND s.topic_id IN (1, 2, 3, 4)";
        }

        // Deduplicate: always select the canonical (lowest ID) record for questions sharing identical text
        $baseSql .= " AND q.id = (SELECT MIN(q_canon.id) FROM questions q_canon WHERE q_canon.question_text = q.question_text)";

        // Avoid simple questions when user is Silver+ tier (filter out easy unless explicitly forced)
        if (!$forcedDifficulty && in_array($userTier, ['Silver', 'Gold', 'Platinum', 'Diamond'])) {
            $baseSql .= " AND q.difficulty != 'easy'";
        }

        // Exclude recent question IDs and identical question texts buffer (Anti-repetition)
        $excludeIds = array_filter(array_map('intval', $excludeQuestionIds));
        $excludeSql = "";
        if (!empty($excludeIds)) {
            $idList = implode(',', $excludeIds);
            $excludeSql = " AND q.id NOT IN ($idList) AND q.question_text NOT IN (SELECT q_dup.question_text FROM questions q_dup WHERE q_dup.id IN ($idList))";
        }

        // 1. Unattempted EVER by user matching target difficulty (excluding recent buffer & text duplicates)
        $sql1 = $baseSql . $excludeSql;
        $params1 = $params;
        if ($userId) {
            $sql1 .= " AND q.id NOT IN (SELECT question_id FROM attempts WHERE user_id = ?) AND q.question_text NOT IN (SELECT q_att.question_text FROM attempts a_att JOIN questions q_att ON a_att.question_id = q_att.id WHERE a_att.user_id = ?)";
            $params1[] = $userId;
            $params1[] = $userId;
        }
        $sql1 .= " AND q.difficulty = ? ORDER BY RAND() LIMIT 1";
        $stmt = $db->prepare($sql1);
        $stmt->execute(array_merge($params1, [$targetDifficulty]));
        $question = $stmt->fetch();

        // 2. Unattempted EVER by user matching any allowed difficulty (excluding recent buffer)
        if (!$question && $userId) {
            $sql2 = $baseSql . $excludeSql . " AND q.id NOT IN (SELECT question_id FROM attempts WHERE user_id = ?) AND q.question_text NOT IN (SELECT q_att.question_text FROM attempts a_att JOIN questions q_att ON a_att.question_id = q_att.id WHERE a_att.user_id = ?) ORDER BY (CASE WHEN q.difficulty = 'hard' THEN 1 WHEN q.difficulty = 'medium' THEN 2 ELSE 3 END), RAND() LIMIT 1";
            $stmt = $db->prepare($sql2);
            $stmt->execute(array_merge($params, [$userId, $userId]));
            $question = $stmt->fetch();
        }

        // 3. Any question in DB matching target difficulty excluding recent buffer
        if (!$question) {
            $sql3 = $baseSql . $excludeSql . " AND q.difficulty = ? ORDER BY RAND() LIMIT 1";
            $stmt = $db->prepare($sql3);
            $stmt->execute(array_merge($params, [$targetDifficulty]));
            $question = $stmt->fetch();
        }

        // 4. Fallback: Spaced repetition (order by least recently attempted so no question repeats immediately)
        if (!$question) {
            if ($userId) {
                $sql4 = $baseSql . $excludeSql . " ORDER BY (
                    SELECT COALESCE(MAX(a_last.attempted_at), '2000-01-01') 
                    FROM attempts a_last 
                    JOIN questions q_last ON a_last.question_id = q_last.id 
                    WHERE q_last.question_text = q.question_text AND a_last.user_id = ?
                ) ASC, RAND() LIMIT 1";
                $stmt = $db->prepare($sql4);
                $stmt->execute(array_merge($params, [$userId]));
            } else {
                $sql4 = $baseSql . $excludeSql . " ORDER BY (CASE WHEN q.difficulty = 'hard' THEN 1 WHEN q.difficulty = 'medium' THEN 2 ELSE 3 END), q.total_attempts_count ASC, RAND() LIMIT 1";
                $stmt = $db->prepare($sql4);
                $stmt->execute($params);
            }
            $question = $stmt->fetch();
        }

        return $question ? self::shuffleOptions($question) : null;
    }

    /**
     * Shuffles options A, B, C, D dynamically on every question serve and remaps correct_option
     */
    public static function shuffleOptions(array $question): array {
        if (empty($question) || !isset($question['correct_option'])) {
            return $question;
        }

        $rawOptions = [
            'A' => $question['option_a'] ?? '',
            'B' => $question['option_b'] ?? '',
            'C' => $question['option_c'] ?? '',
            'D' => $question['option_d'] ?? '',
        ];

        // Sanitize legacy duplicate distractors before shuffling so duplicate choices never appear
        $dupDistractorFixes = [
            174 => ['D' => '80'],
            194 => ['D' => '90'],
            196 => ['B' => '40'],
            203 => ['B' => '65'],
            504 => ['D' => '12 km'],
            508 => ['D' => '12 km'],
            516 => ['D' => '10 km'],
            523 => ['D' => '12 km'],
            525 => ['D' => '12 km'],
            532 => ['D' => '12 km'],
            533 => ['D' => '12 km'],
        ];
        $qId = (int)($question['id'] ?? 0);
        if (isset($dupDistractorFixes[$qId])) {
            foreach ($dupDistractorFixes[$qId] as $k => $v) {
                $rawOptions[$k] = $v;
            }
        }

        $correctKey = strtoupper(trim($question['correct_option']));

        $optionPairs = [
            ['orig_key' => 'A', 'text' => $rawOptions['A']],
            ['orig_key' => 'B', 'text' => $rawOptions['B']],
            ['orig_key' => 'C', 'text' => $rawOptions['C']],
            ['orig_key' => 'D', 'text' => $rawOptions['D']],
        ];

        shuffle($optionPairs);

        $newOptions = [];
        $newCorrectKey = 'A';
        $letters = ['A', 'B', 'C', 'D'];

        foreach ($optionPairs as $index => $pair) {
            $assignedKey = $letters[$index];
            $newOptions[$assignedKey] = $pair['text'];
            if ($pair['orig_key'] === $correctKey) {
                $newCorrectKey = $assignedKey;
            }
        }

        $question['option_a'] = $newOptions['A'];
        $question['option_b'] = $newOptions['B'];
        $question['option_c'] = $newOptions['C'];
        $question['option_d'] = $newOptions['D'];
        $question['correct_option'] = $newCorrectKey;
        $question['shuffled_options'] = $newOptions;

        return $question;
    }
}
