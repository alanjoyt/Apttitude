<?php
/**
 * Schema Coverage Verification Script
 * Lists every database table referenced in app/ (Controllers, Models, Services, Views)
 * and compares against tables created in infinityfree_database_setup.sql plus migrations 01 to 08.
 */

$appDir = realpath(__DIR__ . '/../../app');
$baseDir = realpath(__DIR__ . '/../..');

$appTables = [];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appDir));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    
    $content = file_get_contents($file->getPathname());
    
    $patterns = [
        '/\bFROM\s+[`"]?([a-zA-Z0-9_]+)[`"]?(?:\s+(?:AS\s+)?([a-zA-Z0-9_]+))?(?:\s+WHERE|\s+JOIN|\s+ORDER|\s+GROUP|\s+LIMIT|\s+LEFT|\s+INNER|\s+RIGHT|\s+ON|\s+SET|\s*;|\s*\)|\s*"|\s*\')/i',
        '/\bJOIN\s+[`"]?([a-zA-Z0-9_]+)[`"]?(?:\s+(?:AS\s+)?([a-zA-Z0-9_]+))?\s+ON/i',
        '/\bINSERT\s+INTO\s+[`"]?([a-zA-Z0-9_]+)[`"]?/i',
        '/\bUPDATE\s+[`"]?([a-zA-Z0-9_]+)[`"]?\s+SET/i',
        '/\bDELETE\s+FROM\s+[`"]?([a-zA-Z0-9_]+)[`"]?/i',
    ];
    
    $rel = str_replace($appDir . DIRECTORY_SEPARATOR, '', $file->getPathname());

    foreach ($patterns as $p) {
        if (preg_match_all($p, $content, $matches)) {
            foreach ($matches[1] as $tbl) {
                $tblLower = strtolower($tbl);
                $ignore = ['select', 'where', 'dual', 'information_schema', 'set', 'values', 'table', 'index', 'null', 'int', 'as', 'dom', 'question', 'quiz', 'session', 'silver', 'system', 'config'];
                if (!in_array($tblLower, $ignore) && !empty($tblLower)) {
                    if (!isset($appTables[$tblLower])) {
                        $appTables[$tblLower] = [];
                    }
                    $appTables[$tblLower][] = $rel;
                }
            }
        }
    }
}

// Find created tables in setup + migrations 01-08
$createdTables = [];
$sqlFiles = [
    $baseDir . '/infinityfree_database_setup.sql',
    $baseDir . '/deploy/migrations/01_schema_extensions_and_tables.sql',
    $baseDir . '/deploy/migrations/02_company_practice_patterns_and_unlockables.sql',
    $baseDir . '/deploy/migrations/03_full_company_questions_import.sql',
    $baseDir . '/deploy/migrations/04_configurable_mock_lengths.sql',
    $baseDir . '/deploy/migrations/05_seven_day_fix_plan.sql',
    $baseDir . '/deploy/migrations/06_college_leaderboards.sql',
    $baseDir . '/deploy/migrations/07_college_stats_and_kerala_colleges.sql',
    $baseDir . '/deploy/migrations/08_missing_app_tables_and_company_profiles.sql',
];

foreach ($sqlFiles as $sqlPath) {
    if (!file_exists($sqlPath)) continue;
    $sql = file_get_contents($sqlPath);
    if (preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:`?[a-zA-Z0-9_]+`?\.)?`?([a-zA-Z0-9_]+)`?/i', $sql, $matches)) {
        foreach ($matches[1] as $t) {
            $createdTables[strtolower($t)] = basename($sqlPath);
        }
    }
}

ksort($appTables);
ksort($createdTables);

echo "Database tables referenced across app/ code (" . count($appTables) . " tables):\n";
foreach (array_keys($appTables) as $t) {
    echo " - $t (" . count(array_unique($appTables[$t])) . " files)\n";
}

echo "\nTotal tables defined in setup + migrations 01-08: " . count($createdTables) . " tables.\n";

$missing = [];
foreach ($appTables as $t => $files) {
    if (!isset($createdTables[$t])) {
        $missing[$t] = array_unique($files);
    }
}

echo "\nMissing Tables: " . count($missing) . "\n";
if (empty($missing)) {
    echo "SUCCESS: 100% of tables referenced in app/ are defined in database setup + migrations 01 to 08.\n";
} else {
    foreach ($missing as $t => $files) {
        echo " - $t: " . implode(', ', $files) . "\n";
    }
}
