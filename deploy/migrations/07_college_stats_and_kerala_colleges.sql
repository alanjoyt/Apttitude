-- Migration 07: College Stats Aggregate Caching, Foreign Keys, and Kerala Colleges Seed
-- MariaDB-compatible, idempotent migration

-- 1. Create college_stats aggregate caching table
CREATE TABLE IF NOT EXISTS college_stats (
    college_id INT(11) NOT NULL PRIMARY KEY,
    total_students INT(11) NOT NULL DEFAULT 0,
    active_members INT(11) NOT NULL DEFAULT 0,
    weekly_xp INT(11) NOT NULL DEFAULT 0,
    avg_xp_per_active DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    avg_mock_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    last_calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_stats_ranking (active_members, avg_xp_per_active, weekly_xp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Foreign Key constraints for referential integrity
-- users.college_id -> colleges.id ON DELETE SET NULL
SET @exist_fk_users = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'users' 
      AND CONSTRAINT_NAME = 'fk_users_college'
);
SET @sql_fk_users = IF(@exist_fk_users = 0, 
    'ALTER TABLE users ADD CONSTRAINT fk_users_college FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE SET NULL', 
    'SELECT "fk_users_college already exists"'
);
PREPARE stmt_fk_users FROM @sql_fk_users;
EXECUTE stmt_fk_users;
DEALLOCATE PREPARE stmt_fk_users;

-- colleges.requested_by -> users.id ON DELETE SET NULL
SET @exist_fk_req = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'colleges' 
      AND CONSTRAINT_NAME = 'fk_colleges_requester'
);
SET @sql_fk_req = IF(@exist_fk_req = 0, 
    'ALTER TABLE colleges ADD CONSTRAINT fk_colleges_requester FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE SET NULL', 
    'SELECT "fk_colleges_requester already exists"'
);
PREPARE stmt_fk_req FROM @sql_fk_req;
EXECUTE stmt_fk_req;
DEALLOCATE PREPARE stmt_fk_req;

-- 3. Seed Top 20 Kerala Engineering Colleges
INSERT INTO colleges (code, name, city, state, status, created_at)
VALUES
    ('CET_TVM', 'College of Engineering, Trivandrum (CET)', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('MEC_KOCHI', 'Govt Model Engineering College (MEC)', 'Ernakulam', 'Kerala', 'active', NOW()),
    ('GEC_THRISSUR', 'Government Engineering College, Thrissur', 'Thrissur', 'Kerala', 'active', NOW()),
    ('TKMCE_KOLLAM', 'TKM College of Engineering', 'Kollam', 'Kerala', 'active', NOW()),
    ('RIT_KOTTAYAM', 'Rajiv Gandhi Institute of Technology (RIT)', 'Kottayam', 'Kerala', 'active', NOW()),
    ('GECB_TVM', 'Government Engineering College, Barton Hill', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('GCEK_KANNUR', 'Government College of Engineering, Kannur', 'Kannur', 'Kerala', 'active', NOW()),
    ('MACE_KOTHAMANGALAM', 'Mar Athanasius College of Engineering (MACE)', 'Kothamangalam', 'Kerala', 'active', NOW()),
    ('RSET_KOCHI', 'Rajagiri School of Engineering & Technology (RSET)', 'Kochi', 'Kerala', 'active', NOW()),
    ('FISAT_ANGAMALY', 'Federal Institute of Science and Technology (FISAT)', 'Angamaly', 'Kerala', 'active', NOW()),
    ('SSET_ERNAKULAM', 'SCMS School of Engineering and Technology', 'Ernakulam', 'Kerala', 'active', NOW()),
    ('MITS_VARIKOLI', 'Muthoot Institute of Technology and Science (MITS)', 'Ernakulam', 'Kerala', 'active', NOW()),
    ('SAINTGITS_KTM', 'Saintgits College of Engineering', 'Kottayam', 'Kerala', 'active', NOW()),
    ('AJCE_KANJIRAPPALLY', 'Amal Jyothi College of Engineering', 'Kanjirappally', 'Kerala', 'active', NOW()),
    ('NSSCE_PALAKKAD', 'NSS College of Engineering', 'Palakkad', 'Kerala', 'active', NOW()),
    ('LBSITW_TVM', 'LBS Institute of Technology for Women', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('VAST_THRISSUR', 'Vidya Academy of Science and Technology', 'Thrissur', 'Kerala', 'active', NOW()),
    ('MEC_TVM', 'Marian Engineering College', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('SCTCE_TVM', 'Sree Chitra Thirunal College of Engineering (SCTCE)', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('CUSAT_SOE', 'Cochin University School of Engineering (CUSAT SOE)', 'Kochi', 'Kerala', 'active', NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name), city = VALUES(city), state = VALUES(state);
