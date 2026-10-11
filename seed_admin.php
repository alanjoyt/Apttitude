<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Models/User.php';

try {
    $db = Database::getConnection();

    User::ensureAddonColumnsExist();

    // Check if admin user already exists
    $existing = User::findByEmail('admin@apttitudee.com');
    if (!$existing) {
        $hash = password_hash('adminpassword123', PASSWORD_BCRYPT);
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password_hash, role, plan, total_xp, coins, tier, current_streak)
            VALUES ('System Administrator', 'admin@apttitudee.com', ?, 'admin', 'premium', 25000, 1000, 'Diamond', 30)
        ");
        $stmt->execute([$hash]);
        echo "SUCCESS: Admin User Created!\n";
    } else {
        $db->prepare("UPDATE users SET role = 'admin' WHERE email = 'admin@apttitudee.com'")->execute();
        echo "Admin User updated with admin role.\n";
    }
    echo "Email: admin@apttitudee.com\n";
    echo "Password: adminpassword123\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
