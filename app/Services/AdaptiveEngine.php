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

        // Filter condition matching track, subtopic, language, or general topics
        $filterSql = "WHERE q.is_published = 1";
        $params = [];

        // Track and category filtering
        if ($track === 'technical' || (!empty($language) && in_array(strtolower($language), ['c', 'cpp', 'java', 'python', 'typescript', 'llm', 'ai', 'sql', 'dsa', 'php', 'csharp', 'innovature']))) {
            // Strictly Technical questions
            if ($language === 'innovature') {
                $filterSql .= " AND (q.language = 'innovature' OR q.company_name LIKE '%Innovature%' OR s.topic_id = 6)";
            } else if (!empty($language) && $language !== 'all') {
                $filterSql .= " AND q.language = ?";
                $params[] = strtolower($language);
            } else {
                // All technical questions: topic_id 5 (Technical Assessment) or 6 (Company PYQs) or valid technical language
                $filterSql .= " AND (s.topic_id IN (5, 6) OR (q.language IS NOT NULL AND q.language != '' AND q.language NOT IN ('quant', 'lr')))";
            }
        } else if ($language === 'innovature') {
            $filterSql .= " AND (q.language = 'innovature' OR q.company_name LIKE '%Innovature%' OR s.topic_id = 6)";
        } else if (!empty($language) && $language !== 'all') {
            $filterSql .= " AND q.language = ?";
            $params[] = strtolower($language);
        } else if ($subtopicId) {
            $filterSql .= " AND q.subtopic_id = ?";
            $params[] = $subtopicId;
        } else {
            // General Practice & Time Attack: include all aptitude questions (topics 1-4: Quant, Reasoning, Verbal, DI)
            $filterSql .= " AND s.topic_id IN (1, 2, 3, 4)";
        }

        // Fast fetch of candidate IDs and difficulties (indexed, ultra-fast)
        $candStmt = $db->prepare("SELECT q.id, q.difficulty FROM questions q JOIN subtopics s ON q.subtopic_id = s.id {$filterSql}");
        $candStmt->execute($params);
        $candidates = $candStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($candidates)) {
            return null;
        }

        // Fast retrieval of user attempted question IDs (respects topic resets)
        $attemptedIds = [];
        if ($userId) {
            try {
                if ($subtopicId) {
                    $rst = $db->prepare("SELECT reset_at FROM user_topic_resets WHERE user_id = ? AND subtopic_id = ? LIMIT 1");
                    $rst->execute([$userId, $subtopicId]);
                    $resetAt = $rst->fetchColumn();

                    if ($resetAt) {
                        $aStmt = $db->prepare("SELECT a.question_id FROM attempts a JOIN questions q ON a.question_id = q.id WHERE a.user_id = ? AND q.subtopic_id = ? AND a.attempted_at > ?");
                        $aStmt->execute([$userId, $subtopicId, $resetAt]);
                    } else {
                        $aStmt = $db->prepare("SELECT a.question_id FROM attempts a JOIN questions q ON a.question_id = q.id WHERE a.user_id = ? AND q.subtopic_id = ?");
                        $aStmt->execute([$userId, $subtopicId]);
                    }
                } else {
                    $aStmt = $db->prepare("SELECT DISTINCT question_id FROM attempts WHERE user_id = ?");
                    $aStmt->execute([$userId]);
                }
                $attemptedIds = array_map('intval', $aStmt->fetchAll(PDO::FETCH_COLUMN));
            } catch (\Throwable $e) {
                $attemptedIds = [];
            }
        }

        $attemptedLookup = array_flip($attemptedIds);
        $excludeLookup = array_flip(array_filter(array_map('intval', $excludeQuestionIds)));

        $unattempted = [];
        $unattemptedWithoutExclusion = [];

        foreach ($candidates as $c) {
            $qid = (int)$c['id'];
            if (isset($attemptedLookup[$qid])) {
                continue;
            }
            $unattemptedWithoutExclusion[] = $c;
            if (!isset($excludeLookup[$qid])) {
                $unattempted[] = $c;
            }
        }

        // Prefer unattempted excluding recent session buffer; if buffer exhausted all unattempted, fallback to unattempted
        $pool = !empty($unattempted) ? $unattempted : $unattemptedWithoutExclusion;

        $chosenId = null;
        if (!empty($pool)) {
            // Group by difficulty
            $diffGroups = [];
            foreach ($pool as $item) {
                $diffGroups[$item['difficulty']][] = (int)$item['id'];
            }

            if (!empty($diffGroups[$targetDifficulty])) {
                $matched = $diffGroups[$targetDifficulty];
                $chosenId = $matched[array_rand($matched)];
            } else {
                $order = ($targetDifficulty === 'easy') ? ['easy', 'medium', 'hard'] : ['hard', 'medium', 'easy'];
                foreach ($order as $d) {
                    if (!empty($diffGroups[$d])) {
                        $matched = $diffGroups[$d];
                        $chosenId = $matched[array_rand($matched)];
                        break;
                    }
                }
            }
        }

        // When a specific subtopic or language is selected, strictly avoid repeating completed questions!
        if (!$chosenId && $subtopicId) {
            return null;
        }
        if (!$chosenId && !empty($language) && $language !== 'all') {
            return null;
        }

        // General random practice fallback (all topics)
        if (!$chosenId && !empty($candidates)) {
            $allIds = array_column($candidates, 'id');
            $chosenId = (int)$allIds[array_rand($allIds)];
        }

        if (!$chosenId) {
            return null;
        }

        $qStmt = $db->prepare("SELECT q.*, s.name as subtopic_name, t.name as topic_name FROM questions q JOIN subtopics s ON q.subtopic_id = s.id JOIN topics t ON s.topic_id = t.id WHERE q.id = ?");
        $qStmt->execute([$chosenId]);
        $question = $qStmt->fetch(PDO::FETCH_ASSOC);

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
