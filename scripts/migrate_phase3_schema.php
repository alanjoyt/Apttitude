<?php
/**
 * Phase 3 Question Bank & Verification Schema Migration
 * Adds verification_status, round_pattern, and student_question_submissions table.
 */

require_once __DIR__ . '/../config/db.php';

echo "=== Running Phase 3 Question Bank Schema Migration ===\n";

try {
    $db = Database::getConnection();

    // 1. Add verification_status to questions table
    $vCols = $db->query("SHOW COLUMNS FROM questions LIKE 'verification_status'")->fetchAll();
    if (empty($vCols)) {
        $db->exec("ALTER TABLE questions ADD COLUMN verification_status ENUM('verified_pyq', 'student_reported', 'practice_style') DEFAULT 'practice_style' AFTER is_company_pyq");
        echo " [+] Added 'verification_status' column to 'questions' table.\n";
    } else {
        echo " [=] 'verification_status' column already exists in 'questions'.\n";
    }

    // 2. Add round_pattern to questions table
    $rCols = $db->query("SHOW COLUMNS FROM questions LIKE 'round_pattern'")->fetchAll();
    if (empty($rCols)) {
        $db->exec("ALTER TABLE questions ADD COLUMN round_pattern VARCHAR(100) DEFAULT NULL AFTER verification_status");
        echo " [+] Added 'round_pattern' column to 'questions' table.\n";
    } else {
        echo " [=] 'round_pattern' column already exists in 'questions'.\n";
    }

    // 3. Create student_question_submissions table
    $db->exec("
        CREATE TABLE IF NOT EXISTS student_question_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            company_name VARCHAR(50) NOT NULL,
            drive_date DATE NULL,
            round_pattern VARCHAR(100) DEFAULT 'Written Assessment',
            topic_name VARCHAR(100) NOT NULL,
            difficulty ENUM('easy','medium','hard') DEFAULT 'medium',
            question_text TEXT NOT NULL,
            code_snippet TEXT NULL,
            language VARCHAR(30) NULL,
            option_a TEXT NOT NULL,
            option_b TEXT NOT NULL,
            option_c TEXT NOT NULL,
            option_d TEXT NOT NULL,
            correct_option ENUM('A','B','C','D') NOT NULL,
            explanation TEXT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            admin_notes TEXT NULL,
            reviewed_by INT NULL,
            reviewed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_submission_status (status),
            INDEX idx_submission_company (company_name),
            INDEX idx_submission_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo " [+] Created 'student_question_submissions' table.\n";

    // 4. Update existing company questions to 'practice_style' by default
    $db->exec("UPDATE questions SET verification_status = 'practice_style' WHERE verification_status IS NULL");
    echo " [+] Set default 'practice_style' for existing questions.\n";

    echo "\n>>> Phase 3 Schema Migration Completed Successfully! <<<\n";

} catch (Exception $e) {
    echo "\n[ERROR] Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
