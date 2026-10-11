-- ============================================================================
-- Migration: Add Performance Indexes for Lightning Fast Queries
-- File: database/migrations/20261010_performance_indexes.sql
--
-- Optimization Targets:
-- 1. questions: Filter by subtopic & difficulty (Adaptive Engine)
-- 2. questions: Filter by company PYQ flag and company name
-- 3. attempts: Filter by user and question (anti-duplication buffer & history)
-- 4. attempts: Filter by user and timestamp (recent streak & activity)
-- 5. leaderboard_scores: Fast index scan for top weekly/alltime rankings
-- 6. user_progress: Fast lookup for user mastery levels
-- ============================================================================

-- 1. Speed up Adaptive Engine question lookup by subtopic and difficulty
ALTER TABLE `questions` 
ADD INDEX IF NOT EXISTS `idx_questions_subtopic_diff` (`subtopic_id`, `difficulty`);

-- 2. Speed up Company PYQ searches (e.g. Innovature, TCS, Infosys)
ALTER TABLE `questions` 
ADD INDEX IF NOT EXISTS `idx_questions_pyq_company` (`is_company_pyq`, `company_name`);

-- 3. Speed up attempts lookup by user and question
ALTER TABLE `attempts` 
ADD INDEX IF NOT EXISTS `idx_attempts_user_question` (`user_id`, `question_id`);

-- 4. Speed up attempts sorting by recent timestamp
ALTER TABLE `attempts` 
ADD INDEX IF NOT EXISTS `idx_attempts_user_date` (`user_id`, `attempted_at`);

-- 5. Speed up Leaderboard ranking queries (eliminates full table scans & filesorts)
ALTER TABLE `leaderboard_scores` 
ADD INDEX IF NOT EXISTS `idx_leaderboard_period_xp` (`period`, `period_xp` DESC);

-- 6. Speed up user progress by mastery level
ALTER TABLE `user_progress` 
ADD INDEX IF NOT EXISTS `idx_user_progress_mastery` (`user_id`, `current_mastery_level`);
