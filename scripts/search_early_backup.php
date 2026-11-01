<?php
$backupFile = __DIR__ . '/../backups/backup_apttitudee_20261018_125144.sql';
$fp = fopen($backupFile, 'r');
while (($line = fgets($fp)) !== false) {
    if (strpos($line, 'MTHIW') !== false || strpos($line, 'DARK') !== false) {
        echo "Found in early backup:\n$line\n";
    }
}
fclose($fp);
