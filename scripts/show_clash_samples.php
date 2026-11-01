<?php
$data = json_decode(file_get_contents(__DIR__ . '/cross_company_duplicates.json'), true);
$groups = [];
foreach ($data['clashingIds'] as $id => $info) {
    $groups[$info['norm']][] = $info['company'] . " (ID $id): " . $info['text'];
}

$i = 0;
foreach ($groups as $norm => $items) {
    echo "==================================================\n";
    echo "GROUP " . (++$i) . " (Norm: " . substr($norm, 0, 60) . "...)\n";
    foreach ($items as $it) {
        echo "  - $it\n";
    }
    if ($i >= 8) break;
}
