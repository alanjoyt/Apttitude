<?php
/**
 * Database Backup / Export Script for Apttitudee
 * Creates a complete SQL dump with table structures and data.
 * Safe for rollback before schema/economy migrations.
 */

require_once __DIR__ . '/../config/db.php';

$backupDir = __DIR__ . '/../backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$timestamp = date('Ymd_His');
$backupFile = $backupDir . '/backup_apttitudee_' . $timestamp . '.sql';

echo "=== Apttitudee Database Backup Script ===\n";
echo "Connecting to database...\n";

try {
    $db = Database::getConnection();
    $dbName = $db->query("SELECT DATABASE()")->fetchColumn();
    echo "Connected to database: {$dbName}\n";

    $handle = fopen($backupFile, 'w');
    if (!$handle) {
        throw new Exception("Unable to open backup file for writing: {$backupFile}");
    }

    // Header
    fwrite($handle, "-- Apttitudee Database Backup\n");
    fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
    fwrite($handle, "-- Database: {$dbName}\n");
    fwrite($handle, "-- --------------------------------------------------------\n\n");
    fwrite($handle, "SET NAMES utf8mb4;\n");
    fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

    // Fetch all tables
    $tables = $db->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);
    $tableNames = array_column($tables, 0);

    echo "Found " . count($tableNames) . " tables to export.\n";

    $totalRows = 0;
    foreach ($tableNames as $table) {
        echo " - Exporting table: {$table}... ";

        // Structure
        fwrite($handle, "-- --------------------------------------------------------\n");
        fwrite($handle, "-- Table structure for `{$table}`\n");
        fwrite($handle, "-- --------------------------------------------------------\n");
        fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

        $createStmt = $db->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
        $createSql = $createStmt['Create Table'] ?? '';
        fwrite($handle, $createSql . ";\n\n");

        // Data
        $rowsStmt = $db->query("SELECT * FROM `{$table}`");
        $rowCount = 0;
        $batch = [];
        $columns = [];

        while ($row = $rowsStmt->fetch(PDO::FETCH_ASSOC)) {
            $rowCount++;
            $totalRows++;
            if (empty($columns)) {
                $columns = array_map(function ($col) {
                    return "`{$col}`";
                }, array_keys($row));
            }

            $escapedValues = array_map(function ($val) use ($db) {
                if ($val === null) {
                    return 'NULL';
                }
                return $db->quote($val);
            }, array_values($row));

            $batch[] = "(" . implode(", ", $escapedValues) . ")";

            // Write in batches of 100
            if (count($batch) >= 100) {
                fwrite($handle, "INSERT INTO `{$table}` (" . implode(", ", $columns) . ") VALUES\n" . implode(",\n", $batch) . ";\n");
                $batch = [];
            }
        }

        // Remaining batch
        if (!empty($batch) && !empty($columns)) {
            fwrite($handle, "INSERT INTO `{$table}` (" . implode(", ", $columns) . ") VALUES\n" . implode(",\n", $batch) . ";\n");
        }

        fwrite($handle, "\n\n");
        echo "({$rowCount} rows)\n";
    }

    fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
    fwrite($handle, "-- Backup Complete\n");
    fclose($handle);

    $fileSize = round(filesize($backupFile) / 1024, 2);
    echo "\nBackup completed successfully!\n";
    echo "Saved to: " . realpath($backupFile) . "\n";
    echo "File size: {$fileSize} KB\n";
    echo "Total rows archived: {$totalRows}\n";

} catch (Exception $e) {
    echo "ERROR during backup: " . $e->getMessage() . "\n";
    exit(1);
}
