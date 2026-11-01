<?php
$backupFile = __DIR__ . '/../backups/backup_apttitudee_20261018_155242.sql';
$fp = fopen($backupFile, 'r');
while (($line = fgets($fp)) !== false) {
    if (strpos($line, '1763') !== false && strpos($line, 'Capgemini') !== false) {
        echo "Found in backup:\n$line\n";
        break;
    }
}
fclose($fp);
