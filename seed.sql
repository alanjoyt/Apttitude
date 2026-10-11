-- Apttitudee Seed Data
USE `apttitudee`;

-- Seed Topics
INSERT INTO `topics` (`id`, `name`, `code`, `icon`) VALUES
(1, 'Quantitative Aptitude', 'QUANT', 'bi-calculator-fill'),
(2, 'Logical Reasoning', 'LR', 'bi-puzzle-fill'),
(3, 'Verbal Ability', 'VERBAL', 'bi-chat-left-quote-fill'),
(4, 'Data Interpretation', 'DI', 'bi-bar-chart-line-fill')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Seed Subtopics
INSERT INTO `subtopics` (`id`, `topic_id`, `name`, `code`) VALUES
(1, 1, 'Speed, Distance & Time', 'QUANT_SDT'),
(2, 1, 'Percentages & Profit/Loss', 'QUANT_PERC'),
(3, 1, 'Ratio & Proportion', 'QUANT_RATIO'),
(4, 2, 'Seating Arrangements', 'LR_SEATING'),
(5, 2, 'Blood Relations', 'LR_BLOOD'),
(6, 2, 'Number & Letter Series', 'LR_SERIES'),
(7, 3, 'Reading Comprehension', 'VERBAL_RC'),
(8, 3, 'Sentence Correction', 'VERBAL_SC'),
(9, 4, 'Bar Charts & Tables', 'DI_BAR_TABLE')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Seed Badges
INSERT INTO `badges` (`id`, `code`, `title`, `description`, `icon`, `xp_reward`) VALUES
(1, 'FIRST_STEP', 'First Step', 'Solved your first question correctly!', 'bi-flag-fill', 50),
(2, 'STREAK_7', 'Week Warrior', 'Maintained a 7-day practice streak!', 'bi-fire', 150),
(3, 'SPEED_DEMON', 'Speed Demon', 'Answered a Hard question in under 30 seconds!', 'bi-lightning-charge-fill', 100),
(4, 'PERFECT_MOCK', 'Mock Master', 'Scored 90%+ in a Timed Mock Test!', 'bi-award-fill', 200),
(5, 'TOPIC_PERCENT', 'Percent Prodigy', 'Reached Level 5 Mastery in Percentages!', 'bi-graph-up-arrow', 100)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- Seed Unlockables Catalog
INSERT INTO `unlockables` (`id`, `code`, `title`, `description`, `item_type`, `coin_cost`, `icon`) VALUES
(1, 'TCS_MOCK_PACK', 'TCS NQT Pattern Mock Pack', 'Curated 30-question mock test matching TCS NQT placement pattern.', 'company_mock', 150, 'bi-building-fill-check'),
(2, 'INFOSYS_MOCK_PACK', 'Infosys Placement Test Pack', 'Simulated 25-question test with Infosys reasoning & verbal difficulty.', 'company_mock', 150, 'bi-cpu-fill'),
(3, 'WIPRO_MOCK_PACK', 'Wipro NLTH Challenge Pack', 'Targeted question set simulating Wipro National Level Talent Hunt.', 'company_mock', 150, 'bi-diagram-3-fill'),
(4, 'STREAK_FREEZE_REFILL', 'Streak Freeze Protection Token', 'Automatically protects your practice streak if you miss 1 day of practice.', 'streak_freeze', 50, 'bi-shield-fill-plus'),
(5, 'COSMETIC_CYBER_FRAME', 'Neon Cyberpunk Profile Badge', 'Exclusive glowing cyan cosmetic badge for your leaderboard card.', 'cosmetic_badge', 200, 'bi-palette-fill')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- Seed Demo Users (Password: "password123")
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `plan`, `total_xp`, `coins`, `tier`, `current_streak`, `last_active_date`, `streak_freezes`) VALUES
(1, 'Alan Player', 'alan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'free', 1450, 145, 'Gold', 12, CURDATE(), 2),
(2, 'Priya Sharma', 'priya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'free', 4200, 320, 'Gold', 18, CURDATE(), 1),
(3, 'Alex Vance', 'alex@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'premium', 4850, 500, 'Platinum', 25, CURDATE(), 3),
(4, 'Rahul Kumar', 'rahul@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'free', 3950, 210, 'Gold', 9, CURDATE(), 0),
(5, 'Sneha Patel', 'sneha@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'free', 980, 80, 'Bronze', 4, CURDATE(), 1),
(99, 'System Administrator', 'admin@apttitudee.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'premium', 25000, 1000, 'Diamond', 30, CURDATE(), 5)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Seed Initial Coin Transactions
INSERT INTO `coin_transactions` (`user_id`, `amount`, `transaction_type`, `reference_description`) VALUES
(1, 100, 'admin_grant', 'Welcome Signup Speed Coins Bonus'),
(1, 45, 'speed_bonus', 'Speed Bonus on Hard Question #3')
ON DUPLICATE KEY UPDATE `amount` = VALUES(`amount`);

-- Seed Leaderboard Initial Scores
INSERT INTO `leaderboard_scores` (`user_id`, `period`, `tier`, `period_xp`, `rank_position`) VALUES
(3, 'weekly', 'Platinum', 1250, 1),
(2, 'weekly', 'Gold', 980, 2),
(4, 'weekly', 'Gold', 850, 3),
(1, 'weekly', 'Gold', 640, 4),
(5, 'weekly', 'Bronze', 320, 5)
ON DUPLICATE KEY UPDATE `period_xp` = VALUES(`period_xp`);

-- Seed Questions
INSERT INTO `questions` (`id`, `subtopic_id`, `difficulty`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `est_time_seconds`, `source_tag`) VALUES
(1, 1, 'medium', 'A train 150 meters long passes a telegraph post in 12 seconds. What is the speed of the train in km/hr?', '36 km/hr', '45 km/hr', '54 km/hr', '60 km/hr', 'B', 'Speed = Distance / Time = 150m / 12s = 12.5 m/s. Convert m/s to km/hr by multiplying by 18/5: 12.5 * (18/5) = 45 km/hr.', 45, 'TCS NQT'),

(2, 1, 'easy', 'A car travels at a speed of 60 km/hr. How much distance does it cover in 45 minutes?', '40 km', '45 km', '50 km', '55 km', 'B', 'Time = 45 minutes = 45/60 = 0.75 hours. Distance = Speed * Time = 60 * 0.75 = 45 km.', 30, 'Campus Placement'),

(3, 1, 'hard', 'Two trains running in opposite directions at 40 km/hr and 50 km/hr cross each other in 12 seconds. If the length of one train is 120m, find the length of the other train.', '150 m', '180 m', '200 m', '220 m', 'B', 'Relative speed in opposite direction = 40 + 50 = 90 km/hr = 90 * (5/18) = 25 m/s. Total distance covered = Relative Speed * Time = 25 * 12 = 300m. Length of 2nd train = 300m - 120m = 180m.', 60, 'Infosys Pattern'),

(4, 2, 'easy', 'If the price of an article is increased by 20% and then decreased by 20%, what is the net percentage change in price?', 'No change', '4% increase', '4% decrease', '2% decrease', 'C', 'Net change formula: a + b + (ab/100). Here a = +20, b = -20. Net change = 20 - 20 + (20 * -20 / 100) = -400/100 = -4% (i.e. 4% decrease).', 30, 'Wipro NLTH'),

(5, 2, 'medium', 'A shopkeeper sells a book for $240, making a profit of 20%. What was the cost price of the book?', '$180', '$190', '$200', '$210', 'C', 'Selling Price = Cost Price * (1 + Profit%). 240 = Cost Price * 1.20. Cost Price = 240 / 1.20 = $200.', 45, 'General Aptitude'),

(6, 4, 'medium', 'Six friends A, B, C, D, E, F are sitting in a circle facing the center. B is between F and C. A is between E and D. F is to the immediate left of D. Who is sitting opposite to B?', 'A', 'D', 'E', 'F', 'A', 'Arranging clockwise facing center: F - B - C - E - A - D. B is sitting directly opposite to A.', 50, 'Cognizant GenC'),

(7, 5, 'easy', 'Pointing to a photograph, Rohit said, "She is the daughter of my grandfather\'s only son." How is the girl in the photograph related to Rohit?', 'Mother', 'Sister', 'Cousin', 'Aunt', 'B', 'Rohit\'s grandfather\'s only son is Rohit\'s father. The daughter of Rohit\'s father is Rohit\'s sister.', 30, 'Blood Relations Standard'),

(8, 6, 'easy', 'Find the missing number in the sequence: 4, 9, 19, 39, 79, ?', '119', '139', '159', '179', 'C', 'Pattern: (Previous number * 2) + 1. (4*2)+1=9; (9*2)+1=19; (19*2)+1=39; (39*2)+1=79; (79*2)+1 = 159.', 35, 'Series Test'),

(9, 8, 'medium', 'Choose the grammatically correct sentence:', 'Neither of the two candidates have submitted their resume.', 'Neither of the two candidates has submitted his resume.', 'Neither of the two candidates were submitting resumes.', 'Neither of two candidate submitted their resumes.', 'B', '\'Neither\' takes a singular verb (\'has\') and singular pronoun (\'his\' or \'his/her\'). Therefore, option B is correct.', 40, 'Verbal Standard'),

(10, 9, 'medium', 'In a college of 500 students, 60% play Cricket, 40% play Football, and 20% play both. How many students play neither game?', '80', '100', '120', '150', 'B', 'Total playing at least one game = %Cricket + %Football - %Both = 60% + 40% - 20% = 80%. Students playing neither = 100% - 80% = 20%. 20% of 500 = 100 students.', 45, 'DI Venn Diagram')
ON DUPLICATE KEY UPDATE `question_text` = VALUES(`question_text`);
