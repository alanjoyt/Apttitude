<?php
require_once __DIR__ . '/config/db.php';

try {
    $db = Database::getConnection();

    $items = [
        [
            1, 'TCS_MOCK_PACK', 'TCS NQT Pattern Mock Pack',
            'Curated 30-question mock test matching TCS NQT placement pattern.',
            'company_mock', 150, 'bi-building-fill-check'
        ],
        [
            2, 'INFOSYS_MOCK_PACK', 'Infosys Placement Test Pack',
            'Simulated 25-question test with Infosys reasoning & verbal difficulty.',
            'company_mock', 150, 'bi-cpu-fill'
        ],
        [
            3, 'WIPRO_MOCK_PACK', 'Wipro NLTH Challenge Pack',
            'Targeted question set simulating Wipro National Level Talent Hunt.',
            'company_mock', 150, 'bi-diagram-3-fill'
        ],
        [
            4, 'ACCENTURE_COGNIZANT_PACK', 'Accenture & Cognizant Mock Pack',
            'Simulated 30-question cognitive and technical placement test for Accenture & Cognizant.',
            'company_mock', 150, 'bi-lightning-charge-fill'
        ],
        [
            5, 'STREAK_FREEZE_REFILL', 'Streak Repair Shield Token',
            'Automatically protects your practice streak if you miss 1 day of practice.',
            'streak_freeze', 50, 'bi-shield-fill-plus'
        ],
        [
            6, 'FORMULA_CHEAT_SHEET', 'Quant & Reasoning Formula Cheat-Sheet',
            'Instant high-speed formula cheat-sheet for Speed, Distance, Profit/Loss & Series shortcuts.',
            'cheat_sheet', 75, 'bi-file-earmark-code-fill'
        ],
        [
            7, 'COIN_BOOSTER_24H', '2X Speed Coins Booster (24h)',
            'Earn double Speed Coins on every question answered correctly for 24 hours!',
            'booster', 100, 'bi-rocket-takeoff-fill'
        ],
        [
            8, 'COSMETIC_CYBER_FRAME', 'Neon Cyberpunk Profile Badge',
            'Exclusive glowing cyan cosmetic badge for your leaderboard card.',
            'cosmetic_badge', 200, 'bi-palette-fill'
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO `unlockables` (`id`, `code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            `title` = VALUES(`title`),
            `description` = VALUES(`description`),
            `coin_cost` = VALUES(`coin_cost`),
            `icon` = VALUES(`icon`)
    ");

    foreach ($items as $item) {
        $stmt->execute($item);
    }

    echo "SUCCESS: Seeded " . count($items) . " store items into unlockables table!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
