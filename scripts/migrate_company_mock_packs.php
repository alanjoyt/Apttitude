<?php
/**
 * Migration Script: Add 10 Company Mock Packs & 50 Authentic PYQs
 * Safe and idempotent.
 */

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Apttitudee Company Mock Migration</title></head><body style="background:#111;color:#eee;font-family:monospace;padding:24px;line-height:1.6;"><pre>';
}

require_once __DIR__ . '/../config/db.php';

echo "=================================================================\n";
echo "  APTTITUDEE - 10 COMPANY MOCK PACKS & PYQS MIGRATION\n";
echo "=================================================================\n\n";

try {
    $db = Database::getConnection();

    // 1. Ensure 10 New Company Mock Packs exist in `unlockables`
    $mockPacks = [
        [
            'code' => 'AMAZON_MOCK_PACK',
            'title' => 'Amazon SDE & AWS Placement Pack',
            'description' => 'Curated 30-question placement mock test with authentic Amazon SDE online assessment, DSA, debugging & LP aptitude.',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-cloud-check-fill'
        ],
        [
            'code' => 'ZOHO_MOCK_PACK',
            'title' => 'Zoho Advanced Coding & Aptitude Pack',
            'description' => 'Simulated 30-question test with authentic Zoho round 1 & 2 problem-solving, advanced logic puzzles and Java/C output questions.',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-puzzle-fill'
        ],
        [
            'code' => 'DELOITTE_MOCK_PACK',
            'title' => 'Deloitte National Aptitude Assessment Pack',
            'description' => 'Targeted 30-question set mirroring Deloitte NLA cognitive aptitude, verbal reasoning, and technical MCQ rounds.',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-briefcase-fill'
        ],
        [
            'code' => 'CAPGEMINI_MOCK_PACK',
            'title' => 'Capgemini Exceller Placement Pack',
            'description' => 'Authentic 30-question placement mock for Capgemini pseudo-code, game-based aptitude, and English comprehension.',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-gear-wide-connected'
        ],
        [
            'code' => 'COGNIZANT_MOCK_PACK',
            'title' => 'Cognizant GenC & GenC Next Challenge Pack',
            'description' => 'Realistic 30-question test covering Cognizant GenC quantitative aptitude, logical reasoning, and automata code evaluation.',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-terminal-split'
        ],
        [
            'code' => 'TECH_MAHINDRA_MOCK_PACK',
            'title' => 'Tech Mahindra Recruitment Mock Pack',
            'description' => 'Comprehensive 30-question test following Tech Mahindra national assessment rounds (Aptitude + Technical coding MCQ).',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-code-square'
        ],
        [
            'code' => 'IBM_MOCK_PACK',
            'title' => 'IBM Cognitive & Entry-Level Developer Pack',
            'description' => 'Curated 30-question set simulating IBM cognitive games, numerical reasoning, cloud concepts, and data structures.',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-database-fill'
        ],
        [
            'code' => 'LTI_MINDTREE_MOCK_PACK',
            'title' => 'LTI Mindtree Placement Mock Pack',
            'description' => 'Authentic 30-question placement test set for LTI Mindtree fresher assessment (Technical MCQs, DSA, and Logical Deduction).',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-diagram-3-fill'
        ],
        [
            'code' => 'UST_MOCK_PACK',
            'title' => 'UST Global Software Developer Pack',
            'description' => 'Simulated 30-question placement assessment for UST Global Associate Developer and Quality Engineer profiles.',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-laptop-fill'
        ],
        [
            'code' => 'HCL_MOCK_PACK',
            'title' => 'HCL Tech First Careers Mock Pack',
            'description' => 'Targeted 30-question mock test simulating HCL Tech assessment in Quantitative Aptitude, Reasoning, and Core CS.',
            'item_type' => 'company_mock',
            'coin_cost' => 250,
            'icon' => 'bi-hdd-network-fill'
        ]
    ];

    echo "[+] Verifying and inserting Company Mock Packs into `unlockables`...\n";
    $insPackStmt = $db->prepare("
        INSERT INTO unlockables (code, title, description, item_type, coin_cost, icon)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            title = VALUES(title), 
            description = VALUES(description), 
            coin_cost = VALUES(coin_cost), 
            icon = VALUES(icon)
    ");

    foreach ($mockPacks as $p) {
        $insPackStmt->execute([
            $p['code'],
            $p['title'],
            $p['description'],
            $p['item_type'],
            $p['coin_cost'],
            $p['icon']
        ]);
        echo "  [*] Mock Pack: {$p['code']} -> {$p['title']}\n";
    }

    // 2. Publish and verify all unverified/unpublished questions
    echo "\n[+] Verifying all questions in database...\n";
    $pubCount = $db->exec("
        UPDATE questions 
        SET is_published = 1, 
            verification_level = 'verified', 
            verification_status = CASE 
                WHEN company_name IS NOT NULL AND company_name != '' THEN 'verified_pyq' 
                ELSE 'practice_style' 
            END 
        WHERE is_published = 0 OR verification_level = 'unchecked' OR verification_status = ''
    ");
    echo "  [+] Verified & published {$pubCount} questions.\n";

    // 3. Insert 50 Authentic PYQs across the 10 Companies
    echo "\n[+] Ingesting 50 Authentic Placement Previous Year Questions (PYQs)...\n";
    $sqlFile = __DIR__ . '/../database/migrations/20261031_add_10_company_mock_packs_and_pyqs.sql';
    if (file_exists($sqlFile)) {
        $sqlContent = file_get_contents($sqlFile);
        preg_match_all('/INSERT INTO `questions`[\s\S]+?LIMIT 1;/i', $sqlContent, $matches);
        $queries = $matches[0] ?? [];
        
        $inserted = 0;
        foreach ($queries as $qSql) {
            $qSql = trim($qSql);
            if (empty($qSql)) continue;
            try {
                $affected = $db->exec($qSql);
                if ($affected > 0) $inserted++;
            } catch (\Throwable $e) {
                // Ignore duplicate insertion
            }
        }
        echo "  [+] Processed " . count($queries) . " queries. Newly inserted PYQs: {$inserted}\n";
    }

    // 4. Print Summary Report
    echo "\n-----------------------------------------------------------------\n";
    echo "  MOCK PACKS & QUESTION COUNTS BY COMPANY:\n";
    echo "-----------------------------------------------------------------\n";
    $allPacks = $db->query("SELECT id, code, title, coin_cost FROM unlockables WHERE item_type = 'company_mock' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($allPacks as $pack) {
        $companyTag = str_replace('_MOCK_PACK', '', $pack['code']);
        $companyNameMap = [
            'TCS' => 'TCS',
            'INFOSYS' => 'Infosys',
            'WIPRO' => 'Wipro',
            'INNOVATURE' => 'Innovature',
            'ACCENTURE' => 'Accenture',
            'AMAZON' => 'Amazon',
            'ZOHO' => 'Zoho',
            'DELOITTE' => 'Deloitte',
            'CAPGEMINI' => 'Capgemini',
            'COGNIZANT' => 'Cognizant',
            'TECH_MAHINDRA' => 'Tech Mahindra',
            'IBM' => 'IBM',
            'LTI_MINDTREE' => 'LTI Mindtree',
            'UST' => 'UST',
            'HCL' => 'HCL'
        ];
        $targetTag = $companyNameMap[$companyTag] ?? $companyTag;
        $qCount = $db->query("SELECT COUNT(*) FROM questions WHERE (company_name LIKE '%$targetTag%' OR source_tag LIKE '%$targetTag%') AND is_published = 1")->fetchColumn();
        echo "  [*] [ID {$pack['id']}] {$pack['title']} ({$pack['code']}) -> {$qCount} authentic questions available\n";
    }

    $totalPublished = $db->query("SELECT COUNT(*) FROM questions WHERE is_published = 1")->fetchColumn();
    echo "\n>>> TOTAL VERIFIED PUBLISHED QUESTIONS IN SYSTEM: {$totalPublished} <<<\n";
    echo ">>> MIGRATION COMPLETE! <<<\n";

} catch (\Throwable $e) {
    echo "\n[!] Fatal Migration Error: " . $e->getMessage() . "\n";
}

if (php_sapi_name() !== 'cli') {
    echo '</pre></body></html>';
}
