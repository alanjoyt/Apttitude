<?php
// Automatic Migration Runner for Apttitudee Phase 2
require_once __DIR__ . '/config/db.php';

try {
    $db = Database::getConnection();
    $sql = file_get_contents(__DIR__ . '/phase2_schema.sql');
    
    // Split SQL statements safely by semicolon
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $stmtSql) {
        if (!empty($stmtSql)) {
            $db->exec($stmtSql);
        }
    }
    
    echo "SUCCESS: Phase 2 Database Schema Migration Completed!\n";
} catch (PDOException $e) {
    echo "ERROR during migration: " . $e->getMessage() . "\n";
}
