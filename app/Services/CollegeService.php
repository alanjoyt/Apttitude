<?php
/**
 * CollegeService: Directory, Verification, 30-Day Limit, and Leaderboard Analytics
 */

require_once __DIR__ . '/../../config/db.php';

class CollegeService {

    /**
     * Get all active colleges for registration and profile dropdowns
     */
    public static function getActiveColleges(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT id, code, name, city, state 
            FROM colleges 
            WHERE status = 'active' 
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Student request for a missing college (creates pending row)
     */
    public static function requestCollege(string $name, string $city, string $state, ?int $userId = null): array {
        $name = trim($name);
        $city = trim($city);
        $state = trim($state);

        if (empty($name) || empty($city) || empty($state)) {
            return ['status' => 'error', 'code' => 400, 'message' => 'College name, city, and state are required.'];
        }

        // Rate limit: max 5 requests per hour. Keyed strictly by user ID for authenticated students
        // to prevent shared campus / NAT proxy IP collisions from throttling other students.
        require_once __DIR__ . '/RateLimiter.php';
        $rateKey = $userId 
            ? 'user_' . $userId 
            : ('sess_' . (session_id() ? substr(hash('sha256', session_id()), 0, 16) : RateLimiter::getClientIp()));
        if (!RateLimiter::check('req_college', $rateKey, 5, 3600)) {
            return ['status' => 'error', 'code' => 429, 'message' => 'Too many college requests. Please try again in an hour.'];
        }

        // Length validation
        if (mb_strlen($name) < 4 || mb_strlen($name) > 120) {
            return ['status' => 'error', 'code' => 400, 'message' => 'College name must be between 4 and 120 characters.'];
        }
        if (mb_strlen($city) < 2 || mb_strlen($city) > 60 || mb_strlen($state) < 2 || mb_strlen($state) > 60) {
            return ['status' => 'error', 'code' => 400, 'message' => 'City and state must be between 2 and 60 characters.'];
        }

        // XSS and illegal characters check
        if (preg_match('/[<>`"\';]/', $name) || stripos($name, 'script') !== false || stripos($name, 'javascript') !== false) {
            return ['status' => 'error', 'code' => 400, 'message' => 'College name contains invalid or disallowed characters.'];
        }

        // Offensive language filter
        $offensiveWords = ['porn', 'fuck', 'shit', 'scam', 'hack', 'bitch', 'asshole', 'cunt', 'dick', 'fake', 'cheat', 'fraud'];
        foreach ($offensiveWords as $bad) {
            if (stripos($name, $bad) !== false) {
                return ['status' => 'error', 'code' => 400, 'message' => 'College name contains inappropriate or prohibited wording.'];
            }
        }

        $db = Database::getConnection();

        // Duplicate check against existing active and pending colleges (case and spacing normalized)
        $normalized = preg_replace('/[^a-z0-9]/', '', strtolower($name));
        $allColleges = $db->query("SELECT name FROM colleges WHERE status IN ('active', 'pending')")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($allColleges as $existingName) {
            $existingNorm = preg_replace('/[^a-z0-9]/', '', strtolower($existingName));
            if ($existingNorm === $normalized) {
                return ['status' => 'error', 'code' => 400, 'message' => "A college with a similar name ('{$existingName}') already exists or is pending review."];
            }
        }

        RateLimiter::hit('req_college', $rateKey);

        // Generate clean unique code
        $baseCode = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '_', trim($name)));
        $baseCode = substr(trim($baseCode, '_'), 0, 40);
        $code = $baseCode;
        $counter = 1;

        while (true) {
            $check = $db->prepare("SELECT COUNT(*) FROM colleges WHERE code = ?");
            $check->execute([$code]);
            if ((int)$check->fetchColumn() === 0) {
                break;
            }
            $code = substr($baseCode, 0, 35) . '_' . (++$counter);
        }

        $stmt = $db->prepare("
            INSERT INTO colleges (code, name, city, state, status, requested_by, created_at)
            VALUES (?, ?, ?, ?, 'pending', ?, NOW())
        ");
        $stmt->execute([$code, $name, $city, $state, $userId]);
        $newId = (int)$db->lastInsertId();

        return [
            'status' => 'success',
            'college_id' => $newId,
            'code' => $code,
            'name' => $name,
            'message' => 'College submitted for administrative review. It will become available once approved.'
        ];
    }

    /**
     * Set student college with 30-day change limitation
     */
    public static function setUserCollege(int $userId, ?int $collegeId, ?bool $showName = null): array {
        $db = Database::getConnection();

        // 1. Fetch user current college settings and elapsed seconds directly from DB clock
        $uStmt = $db->prepare("
            SELECT college_id, college_changed_at,
                   TIMESTAMPDIFF(SECOND, college_changed_at, NOW()) AS elapsed_seconds
            FROM users 
            WHERE id = ? 
            LIMIT 1
        ");
        $uStmt->execute([$userId]);
        $user = $uStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['status' => 'error', 'code' => 404, 'message' => 'User not found.'];
        }

        $currentCollegeId = $user['college_id'] !== null ? (int)$user['college_id'] : null;
        $changedAt = $user['college_changed_at'];

        // If no change, return success immediately
        if ($currentCollegeId === $collegeId) {
            return ['status' => 'success', 'message' => 'College unchanged.', 'college_id' => $collegeId];
        }

        // 2. Validate college exists and is active (CL1: dropdown only, active only)
        if ($collegeId !== null) {
            $cStmt = $db->prepare("SELECT id, name, status FROM colleges WHERE id = ? LIMIT 1");
            $cStmt->execute([$collegeId]);
            $college = $cStmt->fetch(PDO::FETCH_ASSOC);

            if (!$college || $college['status'] !== 'active') {
                return ['status' => 'error', 'code' => 400, 'message' => 'Selected college is invalid or not yet approved.'];
            }
        }

        // 3. Enforce 30-day throttle (CL3: changing college once every 30 days)
        // Only throttle if user is changing from an already existing college
        if ($currentCollegeId !== null && $changedAt !== null) {
            $elapsedSeconds = isset($user['elapsed_seconds']) ? (int)$user['elapsed_seconds'] : 0;
            $remainingSeconds = (30 * 86400) - $elapsedSeconds;

            if ($remainingSeconds > 0) {
                $daysRemaining = (int)ceil($remainingSeconds / 86400);
                return [
                    'status' => 'error',
                    'code' => 400,
                    'message' => "College changes are limited to once every 30 days to prevent leaderboard hopping. You can change your college in {$daysRemaining} day" . ($daysRemaining === 1 ? '' : 's') . ".",
                    'days_remaining' => $daysRemaining
                ];
            }
        }

        // 4. Update user college, record timestamp, and optionally apply explicit show_name choice
        $showClause = "";
        $params = [$collegeId];
        if ($showName !== null) {
            $showClause = ", hide_name_on_boards = ?";
            $params[] = $showName ? 0 : 1;
        }
        $params[] = $userId;

        $update = $db->prepare("
            UPDATE users 
            SET college_id = ?, college_changed_at = NOW() {$showClause}
            WHERE id = ?
        ");
        $update->execute($params);

        if ($currentCollegeId) self::recalculateCollegeStats($currentCollegeId);
        if ($collegeId) self::recalculateCollegeStats($collegeId);

        return [
            'status' => 'success',
            'college_id' => $collegeId,
            'message' => 'College updated successfully.'
        ];
    }

    /**
     * Toggle leaderboard privacy (Hide name on college boards)
     */
    public static function toggleBoardPrivacy(int $userId, bool $hideName): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET hide_name_on_boards = ? WHERE id = ?");
        $stmt->execute([$hideName ? 1 : 0, $userId]);

        return [
            'status' => 'success',
            'hide_name_on_boards' => $hideName,
            'message' => $hideName ? 'Your name will now appear as Anonymous Student on college leaderboards.' : 'Your display name is now visible on college leaderboards.'
        ];
    }

    /**
     * Recalculate and update the college_stats aggregate caching table.
     * Definition of active member:
     * A registered student affiliated with the college who completed at least one practice question or mock test in the last 7 calendar days.
     */
    public static function recalculateCollegeStats(?int $collegeId = null): void {
        $db = Database::getConnection();

        // When refreshing all colleges, prune stats of deleted or inactive colleges
        if ($collegeId === null) {
            $db->exec("DELETE FROM college_stats WHERE college_id NOT IN (SELECT id FROM colleges WHERE status = 'active')");
        }

        $whereClause = $collegeId ? "WHERE c.id = " . (int)$collegeId : "WHERE c.status = 'active'";

        $sql = "
            INSERT INTO college_stats (college_id, total_students, active_members, weekly_xp, avg_xp_per_active, avg_mock_score, last_calculated_at)
            SELECT 
                c.id AS college_id,
                COUNT(DISTINCT u.id) AS total_students,
                COUNT(DISTINCT CASE 
                    WHEN (u.last_active_date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) 
                          OR a.attempted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                          OR COALESCE(ls.period_xp, 0) > 0) 
                    THEN u.id 
                END) AS active_members,
                COALESCE((
                    SELECT SUM(
                        GREATEST(
                            COALESCE(ls2.period_xp, 0),
                            COALESCE((
                                SELECT SUM(CASE WHEN a2.is_correct = 1 THEN 10 ELSE 2 END)
                                FROM attempts a2
                                WHERE a2.user_id = u2.id AND a2.attempted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                            ), 0)
                        )
                    )
                    FROM users u2
                    LEFT JOIN leaderboard_scores ls2 ON ls2.user_id = u2.id AND ls2.period = 'weekly'
                    WHERE u2.college_id = c.id AND u2.role != 'admin'
                ), 0) AS weekly_xp,
                ROUND(
                    COALESCE((
                        SELECT SUM(
                            GREATEST(
                                COALESCE(ls2.period_xp, 0),
                                COALESCE((
                                    SELECT SUM(CASE WHEN a2.is_correct = 1 THEN 10 ELSE 2 END)
                                    FROM attempts a2
                                    WHERE a2.user_id = u2.id AND a2.attempted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                                ), 0)
                            )
                        )
                        FROM users u2
                        LEFT JOIN leaderboard_scores ls2 ON ls2.user_id = u2.id AND ls2.period = 'weekly'
                        WHERE u2.college_id = c.id AND u2.role != 'admin'
                    ), 0) / 
                    GREATEST(1, COUNT(DISTINCT CASE 
                        WHEN (u.last_active_date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) 
                              OR a.attempted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                              OR COALESCE(ls.period_xp, 0) > 0) 
                        THEN u.id 
                    END)), 2) AS avg_xp_per_active,
                COALESCE((
                    SELECT ROUND(AVG(ms.accuracy_percent), 2)
                    FROM mock_sessions ms
                    JOIN users u3 ON ms.user_id = u3.id
                    WHERE u3.college_id = c.id AND ms.status = 'completed'
                ), 0.00) AS avg_mock_score,
                NOW()
            FROM colleges c
            LEFT JOIN users u ON u.college_id = c.id
            LEFT JOIN leaderboard_scores ls ON ls.user_id = u.id AND ls.period = 'weekly'
            LEFT JOIN attempts a ON a.user_id = u.id AND a.attempted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            {$whereClause}
            GROUP BY c.id
            ON DUPLICATE KEY UPDATE 
                total_students = VALUES(total_students),
                active_members = VALUES(active_members),
                weekly_xp = VALUES(weekly_xp),
                avg_xp_per_active = VALUES(avg_xp_per_active),
                avg_mock_score = VALUES(avg_mock_score),
                last_calculated_at = NOW()
        ";

        $db->exec($sql);
    }

    /**
     * College vs College Leaderboard (CL4, CL5)
     * Board math with Fairness Metric:
     * - Ranked primarily by Average XP per Active Member (avg_xp_per_active DESC),
     *   preventing giant colleges from automatically dominating through raw headcount.
     * - Weekly XP Total (attempts/activity in last 7 days)
     * - Average Mock Score (% marks on completed mock sessions)
     * - Active Members (distinct students with activity in last 7 days)
     * Threshold: At least 5 active members required to qualify for public ranking.
     */
    public static function getCollegeVsCollegeBoard(int $limit = 50, bool $forceLive = false): array {
        $db = Database::getConnection();

        // Check staleness: refresh on read if stats table is empty, if newly activated colleges
        // are missing from stats, or if last recalculation is older than 10 minutes (600s).
        // Recalculation is guarded by an atomic non-blocking database lock to prevent concurrency stampedes.
        $meta = $db->query("
            SELECT 
                COUNT(cs.college_id) as stats_count,
                TIMESTAMPDIFF(SECOND, MAX(cs.last_calculated_at), NOW()) as max_age_seconds,
                (SELECT COUNT(*) FROM colleges c WHERE c.status = 'active') as active_colleges_count,
                (SELECT COUNT(*) FROM colleges c LEFT JOIN college_stats cs2 ON c.id = cs2.college_id WHERE c.status = 'active' AND cs2.college_id IS NULL) as missing_active_count
            FROM college_stats cs
            JOIN colleges c ON cs.college_id = c.id AND c.status = 'active'
        ")->fetch(PDO::FETCH_ASSOC);

        $statsCount = (int)($meta['stats_count'] ?? 0);
        $maxAgeSeconds = isset($meta['max_age_seconds']) && $meta['max_age_seconds'] !== null ? (int)$meta['max_age_seconds'] : 999999;
        $missingActive = (int)($meta['missing_active_count'] ?? 0);
        $activeCollegesCount = (int)($meta['active_colleges_count'] ?? 0);

        $isStale = ($statsCount === 0 || $missingActive > 0 || $maxAgeSeconds >= 600);

        if ($isStale || $forceLive) {
            $lockAcquired = (int)$db->query("SELECT GET_LOCK('college_stats_refresh', 0)")->fetchColumn();
            if ($lockAcquired === 1) {
                try {
                    self::recalculateCollegeStats();
                } finally {
                    $db->query("SELECT RELEASE_LOCK('college_stats_refresh')");
                }
            }
        }

        // Query cached aggregate with fairness ranking
        $query = "
            SELECT 
                c.id,
                c.code,
                c.name,
                c.city,
                c.state,
                cs.total_students,
                cs.active_members,
                cs.weekly_xp,
                cs.avg_xp_per_active,
                cs.avg_mock_score
            FROM college_stats cs
            JOIN colleges c ON cs.college_id = c.id
            WHERE c.status = 'active' AND cs.active_members >= 5
            ORDER BY cs.avg_xp_per_active DESC, cs.weekly_xp DESC, cs.avg_mock_score DESC, c.name ASC
            LIMIT ?
        ";

        $stmt = $db->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ranked = [];
        $rank = 1;
        foreach ($colleges as $c) {
            $c['rank'] = $rank++;
            $c['weekly_xp'] = (int)$c['weekly_xp'];
            $c['active_members'] = (int)$c['active_members'];
            $c['total_students'] = (int)$c['total_students'];
            $c['avg_xp_per_active'] = (float)$c['avg_xp_per_active'];
            $c['avg_mock_score'] = (float)$c['avg_mock_score'];
            $ranked[] = $c;
        }

        return $ranked;
    }

    /**
     * Intra-College Student Ranking (CL6, CL7)
     * Ranks students inside a college by weekly XP.
     * Enforces strict privacy: NO email, NO phone, honors hide_name_on_boards ("Anonymous Student").
     */
    public static function getCollegeStudentBoard(int $collegeId, ?int $currentUserId = null, int $limit = 100): array {
        $db = Database::getConnection();

        // Verify college exists
        $cStmt = $db->prepare("SELECT id, name, city, state, code FROM colleges WHERE id = ? AND status = 'active' LIMIT 1");
        $cStmt->execute([$collegeId]);
        $college = $cStmt->fetch(PDO::FETCH_ASSOC);

        if (!$college) {
            return ['status' => 'error', 'code' => 404, 'message' => 'College not found.'];
        }

        // Fetch students in college with weekly XP
        // Privacy: Select strictly name, avatar, tier, total_xp, hide_name_on_boards. Never email or phone!
        $query = "
            SELECT 
                u.id,
                u.name,
                u.avatar_icon,
                u.tier,
                u.hide_name_on_boards,
                GREATEST(
                    COALESCE((
                        SELECT ls.period_xp FROM leaderboard_scores ls 
                        WHERE ls.user_id = u.id AND ls.period = 'weekly'
                    ), 0),
                    COALESCE((
                        SELECT SUM(CASE WHEN a.is_correct = 1 THEN 10 ELSE 2 END)
                        FROM attempts a
                        WHERE a.user_id = u.id AND a.attempted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    ), 0)
                ) AS weekly_xp,
                u.total_xp,
                u.current_streak
            FROM users u
            WHERE u.college_id = ? AND u.role != 'admin'
            ORDER BY weekly_xp DESC, u.total_xp DESC, u.id ASC
            LIMIT ?
        ";

        $stmt = $db->prepare($query);
        $stmt->bindValue(1, $collegeId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $students = [];
        $rank = 1;
        $currentUserRank = null;

        foreach ($rows as $r) {
            $isSelf = ($currentUserId !== null && (int)$r['id'] === $currentUserId);
            $hidden = ((int)$r['hide_name_on_boards'] === 1);

            $displayName = $r['name'];
            if ($hidden) {
                $displayName = $isSelf ? 'Anonymous Student (You)' : 'Anonymous Student';
            }

            $entry = [
                'rank' => $rank++,
                'user_id' => $isSelf ? (int)$r['id'] : null, // Omit raw user_id for others to prevent enumeration
                'display_name' => $displayName,
                'avatar_icon' => $hidden ? 'bi-incognito' : ($r['avatar_icon'] ?: 'bi-person-circle'),
                'tier' => $r['tier'] ?: 'Bronze',
                'weekly_xp' => (int)$r['weekly_xp'],
                'current_streak' => (int)$r['current_streak'],
                'is_self' => $isSelf,
                'is_anonymous' => $hidden
            ];

            if ($isSelf) {
                $currentUserRank = $entry['rank'];
            }
            $students[] = $entry;
        }

        return [
            'status' => 'success',
            'college' => $college,
            'total_ranked' => count($students),
            'current_user_rank' => $currentUserRank,
            'students' => $students
        ];
    }

    /**
     * Admin: Get all colleges (including pending and rejected)
     */
    public static function getAllCollegesForAdmin(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT c.*, 
                   COUNT(u.id) AS student_count,
                   u_req.name AS requester_name,
                   u_req.email AS requester_email
            FROM colleges c
            LEFT JOIN users u ON u.college_id = c.id
            LEFT JOIN users u_req ON c.requested_by = u_req.id
            GROUP BY c.id
            ORDER BY FIELD(c.status, 'pending', 'active', 'rejected'), c.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Admin: Approve a pending college (CL8)
     */
    public static function approveCollege(int $collegeId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE colleges SET status = 'active' WHERE id = ?");
        $stmt->execute([$collegeId]);
        return ['status' => 'success', 'message' => 'College approved and activated.'];
    }

    /**
     * Admin: Reject a pending college (CL8)
     */
    public static function rejectCollege(int $collegeId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE colleges SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$collegeId]);
        return ['status' => 'success', 'message' => 'College rejected.'];
    }

    /**
     * Admin: Merge colleges cleanly (CL8)
     * Moves all users from $fromCollegeId to $toCollegeId, then deletes $fromCollegeId.
     */
    public static function mergeColleges(int $fromCollegeId, int $toCollegeId): array {
        if ($fromCollegeId === $toCollegeId) {
            return ['status' => 'error', 'code' => 400, 'message' => 'Cannot merge a college into itself.'];
        }

        $db = Database::getConnection();

        // Verify target college exists
        $toStmt = $db->prepare("SELECT id, name FROM colleges WHERE id = ? LIMIT 1");
        $toStmt->execute([$toCollegeId]);
        $target = $toStmt->fetch(PDO::FETCH_ASSOC);
        if (!$target) {
            return ['status' => 'error', 'code' => 404, 'message' => 'Target college not found.'];
        }

        $db->beginTransaction();
        try {
            // Re-assign users
            $moveStmt = $db->prepare("UPDATE users SET college_id = ? WHERE college_id = ?");
            $moveStmt->execute([$toCollegeId, $fromCollegeId]);
            $movedUsers = $moveStmt->rowCount();

            // Delete source college
            $delStmt = $db->prepare("DELETE FROM colleges WHERE id = ?");
            $delStmt->execute([$fromCollegeId]);

            $db->commit();
            self::recalculateCollegeStats($toCollegeId);
            return [
                'status' => 'success',
                'moved_students' => $movedUsers,
                'target_college' => $target['name'],
                'message' => "Successfully merged college. Moved {$movedUsers} students to {$target['name']} with zero orphan records."
            ];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
