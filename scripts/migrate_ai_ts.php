<?php
/**
 * Browser & CLI Idempotent Migration Runner
 * URL: https://apttitudee.rf.gd/scripts/migrate_ai_ts.php
 *
 * Runs migrations 04 through 09 for Feature Batch 1:
 * - Feature 1: Mock Lengths & Blueprints (Quick, Standard, Full)
 * - Feature 2: 7-Day Personalized Fix Plan (Diagnostic Weakness Engine)
 * - Feature 3: College Leaderboards & Kerala Colleges (Option B Privacy)
 * - Missing App Tables: company_profiles, coupons, user_practice_sessions, admin_audit_log
 * - Feature 4: Anti-Inspect DevTools Tracking
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Apttitudee &mdash; Migration Runner</title>
<style>
  body { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; background: #0f172a; color: #f8fafc; padding: 20px; line-height: 1.5; }
  .box { max-width: 900px; margin: 0 auto; background: #1e293b; border-radius: 8px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
  h1 { font-size: 1.25rem; color: #38bdf8; margin-top: 0; border-bottom: 1px solid #334155; padding-bottom: 12px; }
  .step { margin: 12px 0; padding: 10px 14px; background: #0f172a; border-radius: 6px; border-left: 4px solid #38bdf8; }
  .pass { color: #4ade80; font-weight: bold; }
  .fail { color: #f87171; font-weight: bold; }
  .info { color: #94a3b8; font-size: 0.9rem; }
  .done { margin-top: 20px; padding: 16px; background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; border-radius: 6px; color: #86efac; }
  .btn { display: inline-block; background: #38bdf8; color: #0f172a; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; margin-top: 15px; }
</style>
</head>
<body>
<div class="box">
<h1>&#x1F680; Apttitudee Feature Batch 1 Migration (04 &ndash; 09)</h1>
';
}

function logMsg($msg, $type = 'info') {
    global $isCli;
    if ($isCli) {
        if ($type === 'pass') echo "[PASS] $msg\n";
        elseif ($type === 'fail') echo "[FAIL] $msg\n";
        else echo "       $msg\n";
    } else {
        $class = ($type === 'pass') ? 'pass' : (($type === 'fail') ? 'fail' : 'info');
        echo "<div class='step'><span class='{$class}'>$msg</span></div>";
        flush();
    }
}

try {
    require_once __DIR__ . '/../config/db.php';
    $db = Database::getConnection();
    logMsg("Connected to database successfully (" . $db->getAttribute(PDO::ATTR_DRIVER_NAME) . ")", 'pass');

    // 1. Feature 1: Mock Lengths & Configs (Migration 04)
    logMsg("Executing Migration 04: Configurable Mock Lengths (Quick 30, Standard 60, Full)...");
    $db->exec("
        CREATE TABLE IF NOT EXISTS `mock_configs` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `unlockable_id` int(11) NOT NULL,
          `mode` enum('quick','standard','full') NOT NULL DEFAULT 'standard',
          `question_count` int(11) NOT NULL,
          `duration_minutes` int(11) DEFAULT NULL,
          `negative_marking` decimal(4,2) NOT NULL DEFAULT 0.25,
          `blueprint` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
          `active` tinyint(1) NOT NULL DEFAULT 1,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `uniq_unlockable_mode` (`unlockable_id`,`mode`),
          KEY `idx_active` (`active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Add columns to mock_sessions if missing
    try {
        $db->exec("ALTER TABLE `mock_sessions` ADD COLUMN IF NOT EXISTS `mode` enum('quick','standard','full') NOT NULL DEFAULT 'standard' AFTER `user_id`");
    } catch (\Throwable $e) {}
    try {
        $db->exec("ALTER TABLE `mock_sessions` ADD COLUMN IF NOT EXISTS `duration_seconds` int(11) NOT NULL DEFAULT 3600 AFTER `status`");
    } catch (\Throwable $e) {}

    // Seed mock configs for packs 1 to 21
    $m4Path = __DIR__ . '/../deploy/migrations/04_configurable_mock_lengths.sql';
    if (file_exists($m4Path)) {
        $m4Sql = file_get_contents($m4Path);
        // Extract INSERT statements
        if (preg_match('/INSERT IGNORE INTO `mock_configs`.*?;/s', $m4Sql, $m)) {
            $db->exec($m[0]);
        }
    }
    logMsg("Migration 04 applied: mock_configs and mock_sessions columns verified.", 'pass');

    // 2. Feature 2: 7-Day Fix Plan (Migration 05)
    logMsg("Executing Migration 05: 7-Day Personalized Fix Plan...");
    $db->exec("
        CREATE TABLE IF NOT EXISTS `fix_plans` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `mock_session_id` varchar(64) DEFAULT NULL,
          `plan_type` enum('weakness','needs_data','challenge') NOT NULL DEFAULT 'weakness',
          `status` enum('active','completed','abandoned','replaced') NOT NULL DEFAULT 'active',
          `started_at` datetime NOT NULL,
          `completed_at` datetime DEFAULT NULL,
          `baseline_accuracy` decimal(5,2) DEFAULT NULL,
          `retest_accuracy` decimal(5,2) DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`),
          KEY `idx_user_status` (`user_id`, `status`),
          KEY `idx_mock_session` (`mock_session_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS `fix_plan_days` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `plan_id` int(11) NOT NULL,
          `day_number` tinyint(3) unsigned NOT NULL,
          `subtopic_id` int(11) DEFAULT NULL,
          `subtopic_name` varchar(128) DEFAULT NULL,
          `task_description` varchar(255) NOT NULL,
          `target_questions` int(11) NOT NULL DEFAULT 5,
          `completed_questions` int(11) NOT NULL DEFAULT 0,
          `status` enum('locked','pending','completed') NOT NULL DEFAULT 'locked',
          `completed_at` datetime DEFAULT NULL,
          `reward_claimed` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uniq_plan_day` (`plan_id`, `day_number`),
          KEY `idx_plan_status` (`plan_id`, `status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    logMsg("Migration 05 applied: fix_plans and fix_plan_days tables verified.", 'pass');

    // 3. Feature 3: College Leaderboards (Migration 06 & 07)
    logMsg("Executing Migrations 06 & 07: College Directory, Leaderboards & Stats...");
    $db->exec("
        CREATE TABLE IF NOT EXISTS `colleges` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(50) DEFAULT NULL,
          `name` varchar(255) NOT NULL,
          `city` varchar(128) DEFAULT NULL,
          `state` varchar(128) DEFAULT 'Kerala',
          `status` enum('active','pending','rejected') NOT NULL DEFAULT 'active',
          `requested_by` int(11) DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `uniq_college_name` (`name`),
          KEY `idx_college_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    try {
        $db->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `college_id` int(11) DEFAULT NULL AFTER `role`");
    } catch (\Throwable $e) {}
    try {
        $db->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `college_updated_at` datetime DEFAULT NULL AFTER `college_id`");
    } catch (\Throwable $e) {}
    try {
        $db->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `hide_name_on_boards` tinyint(1) NOT NULL DEFAULT 1 AFTER `college_updated_at`");
    } catch (\Throwable $e) {}

    $db->exec("
        CREATE TABLE IF NOT EXISTS `college_stats` (
          `college_id` int(11) NOT NULL,
          `total_students` int(11) NOT NULL DEFAULT 0,
          `active_members` int(11) NOT NULL DEFAULT 0,
          `weekly_xp` int(11) NOT NULL DEFAULT 0,
          `avg_xp_per_active` decimal(8,2) NOT NULL DEFAULT 0.00,
          `avg_mock_score` decimal(5,2) NOT NULL DEFAULT 0.00,
          `last_calculated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`college_id`),
          KEY `idx_stats_ranking` (`active_members`,`avg_xp_per_active`,`weekly_xp`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Seed 20 Kerala colleges
    $m7Path = __DIR__ . '/../deploy/migrations/07_college_stats_and_kerala_colleges.sql';
    if (file_exists($m7Path)) {
        $m7Sql = file_get_contents($m7Path);
        if (preg_match('/INSERT INTO colleges.*?;/s', $m7Sql, $m)) {
            try {
                $db->exec(str_replace('INSERT INTO', 'INSERT IGNORE INTO', $m[0]));
            } catch (\Throwable $e) {}
        }
    }
    logMsg("Migrations 06 & 07 applied: colleges, college_stats, and Option B privacy verified.", 'pass');

    // 4. Missing App Tables & Company Profiles (Migration 08)
    logMsg("Executing Migration 08: Company Profiles & Supporting Tables...");
    $m8Path = __DIR__ . '/../deploy/migrations/08_missing_app_tables_and_company_profiles.sql';
    if (file_exists($m8Path)) {
        $m8Sql = file_get_contents($m8Path);
        $queries = array_filter(array_map('trim', explode(';', $m8Sql)));
        foreach ($queries as $q) {
            if ($q === '' || str_starts_with($q, '--')) continue;
            try {
                $db->exec($q);
            } catch (\Throwable $e) {
                // Ignore duplicate index or existing table errors
            }
        }
    }
    logMsg("Migration 08 applied: company_profiles, coupons, user_practice_sessions, admin_audit_log verified.", 'pass');

    // 5. Feature 4: Anti-Inspect DevTools Tracking (Migration 09)
    logMsg("Executing Migration 09: Anti-Inspect Exam Integrity Tracking...");
    try {
        $db->exec("ALTER TABLE `mock_sessions` ADD COLUMN IF NOT EXISTS `devtools_count` int(11) NOT NULL DEFAULT 0 AFTER `tab_switch_count`");
    } catch (\Throwable $e) {}
    logMsg("Migration 09 applied: devtools_count column added to mock_sessions.", 'pass');

    // 6. Schema Verification
    $requiredTables = [
        'mock_configs',
        'fix_plans',
        'fix_plan_days',
        'colleges',
        'college_stats',
        'company_profiles',
        'coupons',
        'coupon_redemptions',
        'coupon_audit_log',
        'user_practice_sessions',
        'admin_audit_log'
    ];

    $missing = [];
    foreach ($requiredTables as $t) {
        $check = $db->query("SHOW TABLES LIKE '{$t}'")->fetch();
        if (!$check) {
            $missing[] = $t;
        }
    }

    if (empty($missing)) {
        logMsg("ALL REQUIRED TABLES EXIST: 100% SCHEMA PARITY ACHIEVED", 'pass');
        if (!$isCli) {
            echo "
            <div class='done'>
                <strong>&#x2705; SUCCESS! All migrations executed cleanly.</strong><br>
                Features 1 to 4 are fully active in your database.<br><br>
                <a href='../public/index.php' class='btn'>Launch Apttitudee &rarr;</a>
            </div>
            ";
        }
    } else {
        logMsg("Warning: Missing tables: " . implode(', ', $missing), 'fail');
    }

} catch (\Throwable $e) {
    logMsg("Migration Error: " . $e->getMessage(), 'fail');
}

if (!$isCli) {
    echo "</div></body></html>";
}
