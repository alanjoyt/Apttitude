<?php
/**
 * Automated Production Schema Diff Tool
 *
 * Compares an exported production schema SQL dump (e.g. from phpMyAdmin)
 * against deploy/schema_expected.sql.
 *
 * Usage:
 *   php deploy/scripts/diff_production_schema.php [path/to/production_schema.sql]
 */

$expectedFile = __DIR__ . '/../schema_expected.sql';
if (!file_exists($expectedFile)) {
    die("Error: Expected schema file not found at: {$expectedFile}\n");
}

$prodFile = $argv[1] ?? null;

if (!$prodFile) {
    echo "=================================================================\n";
    echo "AUTOMATED SCHEMA PARITY DIFF TOOL\n";
    echo "=================================================================\n";
    echo "No production dump file specified.\n";
    echo "Usage: php deploy/scripts/diff_production_schema.php <path_to_production_schema.sql>\n\n";
    echo "Performing self-check: Validating deploy/schema_expected.sql parsing...\n";
    $expectedTables = parseSqlSchema(file_get_contents($expectedFile));
    echo "Parsed " . count($expectedTables) . " tables from deploy/schema_expected.sql successfully.\n";
    echo "List of expected tables:\n";
    foreach (array_keys($expectedTables) as $t) {
        echo "  - {$t} (" . count($expectedTables[$t]['columns']) . " columns)\n";
    }
    echo "=================================================================\n";
    exit(0);
}

if (!file_exists($prodFile)) {
    die("Error: Specified production file not found: {$prodFile}\n");
}

$expectedTables = parseSqlSchema(file_get_contents($expectedFile));
$prodTables = parseSqlSchema(file_get_contents($prodFile));

echo "=================================================================\n";
echo "SCHEMA PARITY COMPARISON REPORT\n";
echo "Expected Schema: " . basename($expectedFile) . " (" . count($expectedTables) . " tables)\n";
echo "Production Dump: " . basename($prodFile) . " (" . count($prodTables) . " tables)\n";
echo "=================================================================\n\n";

$missingTables = [];
$extraTables = [];
$columnDiscrepancies = [];

// 1. Check for missing tables
foreach ($expectedTables as $tName => $tDef) {
    if (!isset($prodTables[$tName])) {
        $missingTables[] = $tName;
        continue;
    }

    $prodCols = $prodTables[$tName]['columns'];
    $expCols = $tDef['columns'];

    // Check columns
    foreach ($expCols as $cName => $cDef) {
        if (!isset($prodCols[$cName])) {
            $columnDiscrepancies[$tName]['missing'][] = "Column `{$cName}` {$cDef['type']}";
        } else {
            // Compare types loosely
            $expType = strtolower(preg_replace('/\s+/', '', $cDef['type']));
            $prodType = strtolower(preg_replace('/\s+/', '', $prodCols[$cName]['type']));
            if ($expType !== $prodType) {
                $columnDiscrepancies[$tName]['type_mismatch'][] = "Column `{$cName}`: Expected [{$expType}] vs Found [{$prodType}]";
            }
        }
    }

    foreach ($prodCols as $cName => $cDef) {
        if (!isset($expCols[$cName])) {
            $columnDiscrepancies[$tName]['extra'][] = "Column `{$cName}` {$cDef['type']}";
        }
    }
}

foreach ($prodTables as $tName => $tDef) {
    if (!isset($expectedTables[$tName])) {
        $extraTables[] = $tName;
    }
}

// Print results
if (empty($missingTables) && empty($extraTables) && empty($columnDiscrepancies)) {
    echo "[PASS] 100% SCHEMA PARITY ACHIEVED!\n";
    echo "All expected tables and columns match the production structure exactly.\n";
} else {
    if (!empty($missingTables)) {
        echo "[!] MISSING TABLES IN PRODUCTION (" . count($missingTables) . "):\n";
        foreach ($missingTables as $t) {
            echo "  - {$t}\n";
        }
        echo "\n";
    }

    if (!empty($extraTables)) {
        echo "[i] EXTRA TABLES IN PRODUCTION (" . count($extraTables) . "):\n";
        foreach ($extraTables as $t) {
            echo "  - {$t}\n";
        }
        echo "\n";
    }

    if (!empty($columnDiscrepancies)) {
        echo "[!] COLUMN DISCREPANCIES IN EXISTING TABLES:\n";
        foreach ($columnDiscrepancies as $tName => $disc) {
            echo "  Table `{$tName}`:\n";
            if (!empty($disc['missing'])) {
                foreach ($disc['missing'] as $m) echo "    - [MISSING] {$m}\n";
            }
            if (!empty($disc['type_mismatch'])) {
                foreach ($disc['type_mismatch'] as $m) echo "    - [DIFF] {$m}\n";
            }
            if (!empty($disc['extra'])) {
                foreach ($disc['extra'] as $m) echo "    - [EXTRA] {$m}\n";
            }
        }
        echo "\n";
    }
}

echo "=================================================================\n";

/**
 * Parses CREATE TABLE statements from raw SQL text
 */
function parseSqlSchema(string $sql): array {
    $tables = [];
    
    // Normalize newlines
    $sql = str_replace(["\r\n", "\r"], "\n", $sql);

    // Strip comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

    // Split on CREATE TABLE
    $chunks = preg_split('/CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s+/i', $sql);
    array_shift($chunks); // Drop preamble

    foreach ($chunks as $chunk) {
        if (!preg_match('/^[`"]?([a-zA-Z0-9_]+)[`"]?\s*\((.*)/s', $chunk, $m)) {
            continue;
        }

        $tableName = $m[1];
        $rest = $m[2];

        // Find matching closing parenthesis of table definition
        $level = 1;
        $len = strlen($rest);
        $body = '';
        for ($i = 0; $i < $len; $i++) {
            $char = $rest[$i];
            if ($char === '(') {
                $level++;
            } elseif ($char === ')') {
                $level--;
                if ($level === 0) {
                    $body = substr($rest, 0, $i);
                    break;
                }
            }
        }

        $columns = [];
        $lines = explode("\n", $body);
        foreach ($lines as $line) {
            $line = trim($line, " \t\r\n,");
            if (empty($line)) continue;

            // Skip constraints and indexes
            if (preg_match('/^(?:PRIMARY\s+KEY|KEY|INDEX|UNIQUE|CONSTRAINT|FULLTEXT)/i', $line)) {
                continue;
            }

            if (preg_match('/^[`"]?([a-zA-Z0-9_]+)[`"]?\s+([a-zA-Z0-9_\(\)\',]+)/i', $line, $colMatch)) {
                $colName = $colMatch[1];
                $colType = $colMatch[2];
                $columns[$colName] = [
                    'type' => $colType,
                    'raw' => $line
                ];
            }
        }

        $tables[$tableName] = [
            'columns' => $columns
        ];
    }

    return $tables;
}
