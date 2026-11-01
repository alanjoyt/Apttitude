-- Migration: Add 6 New Company Mock Packs (Mphasis, Hexaware, Persistent, Virtusa, Oracle, Coforge) and 90 Authentic PYQs
-- Safe and idempotent

-- 1. Insert 6 Company Mock Packs into unlockables
INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'MPHASIS_MOCK_PACK', 'Mphasis Placement Challenge Pack', 'Authentic previous year quantitative, logical, verbal, and pseudo code challenges modeled on Mphasis AMCAT & recruitment tests.', 'company_mock', 250, 'bi bi-cpu-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'MPHASIS_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'HEXAWARE_MOCK_PACK', 'Hexaware PGET Placement Pack', 'Previous year aptitude, C++ STL, domain pseudo code, and problem-solving questions from Hexaware graduate hiring drives.', 'company_mock', 250, 'bi bi-hdd-network-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'HEXAWARE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'PERSISTENT_MOCK_PACK', 'Persistent Systems Tech Mock Pack', 'Advanced quantitative reasoning, core DBMS/OS fundamentals, and algorithmic coding challenges curated from Persistent Systems tests.', 'company_mock', 250, 'bi bi-terminal-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'PERSISTENT_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'VIRTUSA_MOCK_PACK', 'Virtusa NeuralHack & Placement Pack', 'Core technical MCQs, C pointers, nested pseudo code loops, and quantitative aptitude from Virtusa campus drives.', 'company_mock', 250, 'bi bi-code-square'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'VIRTUSA_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'ORACLE_MOCK_PACK', 'Oracle Server Technology Placement Pack', 'High-caliber questions on Oracle SQL, transactions, triggers, C++ OOP polymorphism, and speed aptitude from Oracle hiring.', 'company_mock', 250, 'bi bi-database-fill-gear'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'ORACLE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'COFORGE_MOCK_PACK', 'Coforge Tech Talent Placement Pack', 'Authentic quantitative ratios, series logic, operator precedence pseudo code, and computer science fundamentals from Coforge drives.', 'company_mock', 250, 'bi bi-diagram-3-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'COFORGE_MOCK_PACK');

-- 2. Insert 90 Authentic Placement PYQs
INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 3, 'The ratio of the present ages of a father and his son is 7:3. After 10 years, the ratio of their ages will become 2:1. What is the present age of the father?', NULL, NULL, '56 years', '60 years', '70 years', '84 years', 'C', 'Let father''s age be 7x and son''s age be 3x. After 10 years: (7x + 10) / (3x + 10) = 2/1. Cross-multiplying: 7x + 10 = 6x + 20 => x = 10. Present age of father = 7 * 10 = 70 years.', 'medium', 60, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'The ratio of the present ages of a father and his son is 7:3. After 10 years, the ratio of their ages will become 2:1. What is the present age of the father?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 24, 'If 9x - 3y = 12 and 3x - 5y = 7, what is the exact value of 6x - 2y?', NULL, NULL, '4', '8', '12', '-5', 'B', 'From the first equation: 9x - 3y = 12. Factoring out 3 gives 3(3x - y) = 12, so 3x - y = 4. The requested expression is 6x - 2y = 2(3x - y). Substituting gives 2 * 4 = 8.', 'easy', 45, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'If 9x - 3y = 12 and 3x - 5y = 7, what is the exact value of 6x - 2y?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 1, 'Two hours after a freight train leaves Delhi, a passenger train leaves the same station traveling in the same direction at an average speed of 60 km/h. If the passenger train overtakes the freight train after 4 hours, what was the average speed of the freight train?', NULL, NULL, '30 km/h', '40 km/h', '45 km/h', '50 km/h', 'B', 'The passenger train travels for 4 hours at 60 km/h, covering 4 * 60 = 240 km. The freight train started 2 hours earlier, so it has traveled for 4 + 2 = 6 hours. Speed of freight train = 240 / 6 = 40 km/h.', 'medium', 60, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Two hours after a freight train leaves Delhi, a passenger train leaves the same station traveling in the same direction at an average speed of 60 km/h. If the passenger train overtakes the freight train after 4 hours, what was the average speed of the freight train?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 12, 'A contractor is hired to paint 3 houses. Mr. Brown can paint a house in 6 days, Mr. Black in 8 days, and Mr. Blue in 12 days. If Mr. Brown works for 8 days and Mr. Black works for 6 days, how many days will it take Mr. Blue working alone to complete the remaining contract?', NULL, NULL, '7 days', '9 days', '11 days', '12 days', 'C', 'Total work = 3 houses. Brown in 8 days completes 8 * (1/6) = 4/3 houses. Black in 6 days completes 6 * (1/8) = 3/4 houses. Total work done = 4/3 + 3/4 = 25/12 houses. Remaining work = 3 - 25/12 = 11/12 houses. Blue paints 1/12 house per day. Days for Blue = (11/12) / (1/12) = 11 days.', 'hard', 90, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A contractor is hired to paint 3 houses. Mr. Brown can paint a house in 6 days, Mr. Black in 8 days, and Mr. Blue in 12 days. If Mr. Brown works for 8 days and Mr. Black works for 6 days, how many days will it take Mr. Blue working alone to complete the remaining contract?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 6, 'Find the next fraction in the given series: 2, 1, 1/2, 1/4, ?', NULL, NULL, '1/3', '1/6', '1/8', '1/16', 'C', 'Each successive term is obtained by dividing the previous term by 2 (common ratio = 1/2). (1/4) * (1/2) = 1/8.', 'easy', 30, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Find the next fraction in the given series: 2, 1, 1/2, 1/4, ?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 10, 'If in a certain code, ROPE is written as $%^* and CHAIR is written as +#$?- , how is CRAPE written in that code?', NULL, NULL, '+$%^*', '+$*^-', '+$^*%', '+%*?-', 'C', 'Direct mapping: C=+, R=$, A=^, P=*, E=%. Therefore, CRAPE is represented as +$^*%.', 'easy', 35, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'If in a certain code, ROPE is written as $%^* and CHAIR is written as +#$?- , how is CRAPE written in that code?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 23, 'Statements: All pens are books. All books are pencils.
Conclusions:
I. All pens are pencils.
II. Some pencils are pens.
Which conclusion(s) logically follow?', NULL, NULL, 'Only Conclusion I follows', 'Only Conclusion II follows', 'Both Conclusion I and II follow', 'Neither follows', 'C', 'Since all pens are inside books, and all books are inside pencils, pens are entirely contained in pencils (Conclusion I holds). Also, because pencils contain all pens, some portion of pencils must be pens (Conclusion II holds).', 'easy', 40, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Statements: All pens are books. All books are pencils.
Conclusions:
I. All pens are pencils.
II. Some pencils are pens.
Which conclusion(s) logically follow?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 2, 'By selling 12 pens for ₹10, a vendor loses 20%. How many pens should he sell for ₹10 in order to gain 20% on the transaction?', NULL, NULL, '6 pens', '8 pens', '9 pens', '10 pens', 'B', 'Selling price of 12 pens = ₹10 at 80% CP. CP of 12 pens = 10 / 0.8 = ₹12.50. CP of 1 pen = 12.50 / 12 = ₹25/24. Target SP with 20% gain = (25/24) * 1.20 = ₹1.25 per pen. Number of pens for ₹10 = 10 / 1.25 = 8 pens.', 'hard', 75, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'By selling 12 pens for ₹10, a vendor loses 20%. How many pens should he sell for ₹10 in order to gain 20% on the transaction?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 5, 'Pointing to a photograph of a gentleman, a woman says: ''He is the only son of the wife of my husband''s father.'' How is the gentleman in the photograph related to the woman?', NULL, NULL, 'Husband', 'Brother-in-law', 'Father-in-law', 'Son', 'A', 'Wife of my husband''s father = Mother-in-law. The ''only son'' of her mother-in-law is her husband.', 'medium', 45, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Pointing to a photograph of a gentleman, a woman says: ''He is the only son of the wife of my husband''s father.'' How is the gentleman in the photograph related to the woman?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 11, 'A person walks 10 meters North, turns Right and walks 5 meters, turns Right again and walks 10 meters, and finally turns Left and walks 5 meters. How far and in which direction is he now from his starting point?', NULL, NULL, '10 meters East', '10 meters West', '15 meters North', '5 meters East', 'A', 'Initial point (0,0). North 10m -> (0, 10). Turn Right East 5m -> (5, 10). Turn Right South 10m -> (5, 0). Turn Left East 5m -> (10, 0). He is exactly 10 meters East of the origin.', 'easy', 40, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A person walks 10 meters North, turns Right and walks 5 meters, turns Right again and walks 10 meters, and finally turns Left and walks 5 meters. How far and in which direction is he now from his starting point?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 8, 'Identify the incorrect part of the sentence: ''Neither of the two candidates (A) / who was interviewed (B) / were found suitable (C) / for the executive post (D).''', NULL, NULL, 'Part (A)', 'Part (B)', 'Part (C)', 'Part (D)', 'C', '''Neither'' is a singular distributive pronoun and requires the singular auxiliary verb ''was found suitable'', not ''were found suitable''.', 'medium', 35, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Identify the incorrect part of the sentence: ''Neither of the two candidates (A) / who was interviewed (B) / were found suitable (C) / for the executive post (D).''' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 13, 'Choose the exact synonym for the word ''METICULOUS'':', NULL, NULL, 'Painstaking and precise', 'Hasty and thoughtless', 'Sluggish and delayed', 'Overly generous', 'A', '''Meticulous'' means showing great attention to detail, very careful, thorough, and painstaking.', 'easy', 25, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Choose the exact synonym for the word ''METICULOUS'':' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What will be the final printed value of ''c'' after execution of this pseudo code?', 'Integer a, b, c
Set a = 10, b = 20, c = 0
While (a < b)
    c = c + a
    a = a + 3
End While
Print c', 'c', '58', '45', '60', '52', 'A', 'Trace: 1) a=10 (<20): c = 0 + 10 = 10, a=13. 2) a=13 (<20): c = 10 + 13 = 23, a=16. 3) a=16 (<20): c = 23 + 16 = 39, a=19. 4) a=19 (<20): c = 39 + 19 = 58, a=22. 5) a=22 (not <20), loop terminates. Result c = 58.', 'medium', 60, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What will be the final printed value of ''c'' after execution of this pseudo code?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What is the output of the following C code demonstrating bitwise shift operators?', '#include <stdio.h>
int main() {
    int x = 16 >> 2;
    int y = x << 1;
    printf("%d", x + y);
    return 0;
}', 'c', '12', '16', '8', '24', 'A', '16 >> 2 performs right shift by 2 (16 / 4 = 4), so x = 4. Then x << 1 performs left shift by 1 (4 * 2 = 8), so y = 8. Output x + y = 4 + 8 = 12.', 'easy', 35, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the output of the following C code demonstrating bitwise shift operators?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'In relational database design, which Normal Form explicitly requires that all non-prime attributes must be non-transitively dependent on the primary key?', NULL, 'sql', 'First Normal Form (1NF)', 'Second Normal Form (2NF)', 'Third Normal Form (3NF)', 'Boyce-Codd Normal Form (BCNF)', 'C', 'Third Normal Form (3NF) requires a relation to be in 2NF and have no transitive dependencies of non-prime attributes on any candidate key.', 'easy', 30, 1, 'verified', 'Mphasis', 'Mphasis Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In relational database design, which Normal Form explicitly requires that all non-prime attributes must be non-transitively dependent on the primary key?' AND `company_name` = 'Mphasis');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 15, 'What will be printed to stdout by the following C++ deque iterator program?', '#include <iostream>
#include <deque>
using namespace std;
int main() {
    deque<int> d(5);
    deque<int>::reverse_iterator rit;
    int i = 0;
    for (rit = d.rbegin(); rit != d.rend(); ++rit) {
        *rit = ++i;
    }
    for (deque<int>::iterator it = d.begin(); it != d.end(); ++it) {
        cout << *it;
    }
    return 0;
}', 'cpp', '12345', '54321', '00000', '43210', 'B', 'The reverse iterator fills the deque from the back: index 4 gets 1, index 3 gets 2, index 2 gets 3, index 1 gets 4, index 0 gets 5. Forward iteration from begin() to end() prints indices 0 to 4: 54321.', 'medium', 60, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What will be printed to stdout by the following C++ deque iterator program?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What will be the final value of the variable ''val'' upon terminating this pseudo code block?', 'Set a = 10, b = 5, val = 0
If (a > b) Then
    val = a + b
    If (a % b == 0) Then
        val = val * 2
    End If
Else
    val = a - b
End If
Print val', 'c', '15', '20', '30', '50', 'C', 'a=10, b=5. Since 10 > 5 is True, val becomes 10 + 5 = 15. The nested condition 10 % 5 == 0 is True, so val = 15 * 2 = 30.', 'easy', 35, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What will be the final value of the variable ''val'' upon terminating this pseudo code block?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 24, 'Find the value of ''?'' in the mathematical expression: [sqrt(4356) * sqrt(?)] / sqrt(6084) = 11', NULL, NULL, '121', '144', '169', '196', 'C', 'sqrt(4356) = 66, sqrt(6084) = 78. So (66 * sqrt(?)) / 78 = 11. Since 66/78 simplifies to 11/13: (11 * sqrt(?)) / 13 = 11 => sqrt(?) / 13 = 1 => sqrt(?) = 13 => ? = 13^2 = 169.', 'medium', 60, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Find the value of ''?'' in the mathematical expression: [sqrt(4356) * sqrt(?)] / sqrt(6084) = 11' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 1, 'A traveler covers the first half of a total journey at 5 km/h and the remaining half at 4 km/h. If the entire journey takes 9 hours, what is the total distance covered?', NULL, NULL, '36 km', '40 km', '45 km', '50 km', 'B', 'Let total distance be 2D. Time = D/5 + D/4 = 9. Combining: 9D/20 = 9 => D = 20 km. Total distance = 2D = 40 km.', 'medium', 50, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A traveler covers the first half of a total journey at 5 km/h and the remaining half at 4 km/h. If the entire journey takes 9 hours, what is the total distance covered?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 3, 'Three partners A, B, and C start a business with a total capital of ₹3,000. B invests ₹600 less than A, and C invests ₹300 less than B. How much profit will B receive out of a total profit of ₹886?', NULL, NULL, '₹200.50', '₹265.80', '₹300.00', '₹354.40', 'B', 'Let A invest x. B = x - 600, C = (x - 600) - 300 = x - 900. Total = 3x - 1500 = 3000 => 3x = 4500 => x = 1500. A = 1500, B = 900, C = 600. Ratio = 5:3:2 (total 10 parts). B''s share = (3/10) * 886 = ₹265.80.', 'medium', 60, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Three partners A, B, and C start a business with a total capital of ₹3,000. B invests ₹600 less than A, and C invests ₹300 less than B. How much profit will B receive out of a total profit of ₹886?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 12, 'A can finish a task in 10 days, while B can finish it in 15 days. They work together for 5 days. What fraction of the original work remains incomplete?', NULL, NULL, '1/4', '1/5', '1/6', '1/3', 'C', 'Combined 1-day work = 1/10 + 1/15 = 5/30 = 1/6. In 5 days, work done = 5 * (1/6) = 5/6. Remaining work = 1 - 5/6 = 1/6.', 'easy', 40, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A can finish a task in 10 days, while B can finish it in 15 days. They work together for 5 days. What fraction of the original work remains incomplete?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 6, 'Find the missing term in the sequence: 4, 18, 48, 100, ?', NULL, NULL, '150', '180', '196', '216', 'B', 'Pattern is n^3 - n^2 (or n^2 * (n - 1)): for n=2: 8 - 4 = 4; for n=3: 27 - 9 = 18; for n=4: 64 - 16 = 48; for n=5: 125 - 25 = 100; for n=6: 216 - 36 = 180.', 'hard', 60, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Find the missing term in the sequence: 4, 18, 48, 100, ?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 10, 'If HEALTH is written as GSKZDG in a specific cipher, how will NORTH be written in that same cipher?', NULL, NULL, 'OPSUI', 'MSQSI', 'GNQSM', 'GSQNM', 'D', 'Pattern: Cross shift backwards by 1 from right to left: H - 1 = G, T - 1 = S, L - 1 = K, A - 1 = Z, E - 1 = D, H - 1 = G. Applying to NORTH in reverse: H-1=G, T-1=S, R-1=Q, O-1=N, N-1=M => GSQNM.', 'hard', 75, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'If HEALTH is written as GSKZDG in a specific cipher, how will NORTH be written in that same cipher?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 5, 'Introducing a boy, a girl says: ''He is the son of the daughter of the father of my uncle.'' How is the boy related to the girl?', NULL, NULL, 'Brother', 'Uncle', 'Nephew', 'Son', 'A', 'Father of my uncle = Grandfather. Daughter of my grandfather = My mother (or maternal aunt). Son of my mother = Brother.', 'medium', 45, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Introducing a boy, a girl says: ''He is the son of the daughter of the father of my uncle.'' How is the boy related to the girl?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 4, 'Six friends A, B, C, D, E, and F sit around a circular table facing the center. B is between A and C. E is between D and F. D is to the immediate left of A. Who sits directly opposite B?', NULL, NULL, 'D', 'E', 'F', 'C', 'B', 'Arrangement in clockwise circle: A -> D -> E -> F -> C -> B -> (back to A). Directly opposite B across 6 positions is E.', 'medium', 60, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Six friends A, B, C, D, E, and F sit around a circular table facing the center. B is between A and C. E is between D and F. D is to the immediate left of A. Who sits directly opposite B?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 15, 'Which Object-Oriented Programming capability permits a single function identifier to possess multiple parameter lists within the same scope?', NULL, 'cpp', 'Function Overriding', 'Function Overloading', 'Virtual Inheritance', 'Data Abstraction', 'B', 'Function Overloading is a compile-time polymorphism feature where two or more functions have the same name but different parameter signatures.', 'easy', 30, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which Object-Oriented Programming capability permits a single function identifier to possess multiple parameter lists within the same scope?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What is the output of the following pointer arithmetic snippet in C?', '#include <stdio.h>
int main() {
    int a[3] = {10, 20, 30};
    int *p = a;
    printf("%d", *(p + 1));
    return 0;
}', 'c', '10', '20', '30', 'Address of a[1]', 'B', '''p'' points to a[0]. (p + 1) advances the pointer by sizeof(int) bytes to a[1]. Dereferencing *(p + 1) yields the value 20.', 'easy', 30, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the output of the following pointer arithmetic snippet in C?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'In a Circular Queue implemented with an array of capacity N, which mathematical condition indicates that the queue is completely full?', NULL, NULL, '(rear + 1) % N == front', 'rear == front', 'rear == N - 1', '(front + 1) % N == rear', 'A', 'To distinguish a full circular queue from an empty one (where front == rear), the condition (rear + 1) % N == front is universally used.', 'medium', 45, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In a Circular Queue implemented with an array of capacity N, which mathematical condition indicates that the queue is completely full?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'Which SQL clause is required to filter records based on the result of an aggregate function such as COUNT() or AVG()?', NULL, 'sql', 'WHERE', 'HAVING', 'GROUP BY', 'ORDER BY', 'B', 'WHERE filters individual rows prior to grouping, whereas HAVING filters grouped rows after aggregate computations have taken place.', 'easy', 30, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which SQL clause is required to filter records based on the result of an aggregate function such as COUNT() or AVG()?' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 8, 'Fill in the blank with the appropriate word: ''The executive committee has decided to _____ the policy review meeting until next Tuesday.''', NULL, NULL, 'defer', 'differ', 'defy', 'deter', 'A', '''Defer'' means to postpone or put off to a later time. ''Differ'' means disagree, ''defy'' means disobey, and ''deter'' means discourage.', 'easy', 25, 1, 'verified', 'Hexaware', 'Hexaware PGET PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Fill in the blank with the appropriate word: ''The executive committee has decided to _____ the policy review meeting until next Tuesday.''' AND `company_name` = 'Hexaware');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 2, 'If the retail price of sugar rises by 25%, by what exact percentage must a household reduce its consumption so that the total monthly expenditure on sugar remains unchanged?', NULL, NULL, '15%', '20%', '25%', '16.67%', 'B', 'Formula: Reduction = [r / (100 + r)] * 100. For r = 25: (25 / 125) * 100 = 1/5 * 100 = 20%.', 'easy', 35, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'If the retail price of sugar rises by 25%, by what exact percentage must a household reduce its consumption so that the total monthly expenditure on sugar remains unchanged?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 2, 'An item is marked 40% above its cost price and subsequently sold at a discount of 10% on the marked price. What is the net profit percentage realized on the transaction?', NULL, NULL, '24%', '26%', '30%', '32%', 'B', 'Let CP = 100. Marked Price = 140. Discount = 10% of 140 = 14. Selling Price = 140 - 14 = 126. Profit = 126 - 100 = 26%.', 'easy', 40, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'An item is marked 40% above its cost price and subsequently sold at a discount of 10% on the marked price. What is the net profit percentage realized on the transaction?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 1, 'A train traveling at a constant speed of 45 km/h takes exactly 12 seconds to completely cross a telegraph pole. What is the length of the train in meters?', NULL, NULL, '120 meters', '140 meters', '150 meters', '180 meters', 'C', 'Convert speed: 45 * (5/18) = 12.5 m/s. Length = Speed * Time = 12.5 m/s * 12 s = 150 meters.', 'easy', 35, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A train traveling at a constant speed of 45 km/h takes exactly 12 seconds to completely cross a telegraph pole. What is the length of the train in meters?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 12, 'If 12 software developers can complete a project in 16 days, how many days will 8 software developers take to complete the exact same project at the same productivity rate?', NULL, NULL, '20 days', '24 days', '28 days', '32 days', 'B', 'Total person-days = 12 * 16 = 192 days. With 8 developers: 192 / 8 = 24 days.', 'easy', 30, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'If 12 software developers can complete a project in 16 days, how many days will 8 software developers take to complete the exact same project at the same productivity rate?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 24, 'In how many distinct ways can all the letters of the word ''PERSIST'' be arranged?', NULL, NULL, '2520', '5040', '1260', '720', 'A', '''PERSIST'' has 7 letters: P, E, R, S, I, S, T. Notice ''S'' appears twice. Total distinct permutations = 7! / 2! = 5040 / 2 = 2520.', 'medium', 50, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In how many distinct ways can all the letters of the word ''PERSIST'' be arranged?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'In Python, which list of elements is generated when sorting the array [2, 5, 2, 6, -1, 9, 9, 2, -1] primarily by element frequency descending and secondarily by initial index appearance ascending?', 'arr = [2, 5, 2, 6, -1, 9, 9, 2, -1]
# Frequency: 2 appears 3 times; -1 and 9 appear 2 times;
# 5 and 6 appear 1 time.', 'python', '[2, 2, 2, -1, -1, 9, 9, 5, 6]', '[2, 2, 2, 9, 9, -1, -1, 5, 6]', '[2, 2, 2, 5, 6, -1, -1, 9, 9]', '[9, 9, -1, -1, 2, 2, 2, 5, 6]', 'A', 'Frequency breakdown: ''2'' has freq 3. Both ''-1'' (first seen at index 4) and ''9'' (first seen at index 5) have freq 2. Since -1 appeared before 9, -1 precedes 9. Frequencies of 5 and 6 are 1 (5 at idx 1, 6 at idx 3). Result: [2, 2, 2, -1, -1, 9, 9, 5, 6].', 'hard', 75, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In Python, which list of elements is generated when sorting the array [2, 5, 2, 6, -1, 9, 9, 2, -1] primarily by element frequency descending and secondarily by initial index appearance ascending?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'What is the maximum non-adjacent subset sum for the array [2, 7, 9, 3, 1] using dynamic programming (House Robber formulation)?', NULL, 'python', '11', '12', '13', '14', 'B', 'dp[i] = max(dp[i-1], dp[i-2] + nums[i]). Array: [2, 7, 9, 3, 1]. dp[0]=2, dp[1]=7, dp[2]=max(7, 2+9)=11, dp[3]=max(11, 7+3)=11, dp[4]=max(11, 11+1)=12 (taking elements 2 + 9 + 1 = 12).', 'medium', 60, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the maximum non-adjacent subset sum for the array [2, 7, 9, 3, 1] using dynamic programming (House Robber formulation)?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'What is the tightest time complexity to locate the k-th node from the end of a singly linked list with N nodes in a single traversal pass using two pointers?', NULL, NULL, 'O(1)', 'O(k)', 'O(N)', 'O(N log N)', 'C', 'By placing a fast pointer k nodes ahead of a slow pointer and incrementing both simultaneously until the fast pointer reaches the end, the list is traversed in exactly one pass: O(N) time.', 'easy', 35, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the tightest time complexity to locate the k-th node from the end of a singly linked list with N nodes in a single traversal pass using two pointers?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'In Relational Database Management Systems (RDBMS), what characterizes a ''Weak Entity''?', NULL, 'sql', 'It cannot be uniquely identified by its own attributes alone and requires an identifying relationship with a strong entity', 'It has no candidate keys and cannot contain numeric data', 'It is an unindexed table that cannot participate in joins', 'It is an entity with only one attribute', 'A', 'A weak entity lacks a primary key of its own; its identity depends on a foreign key from a parent strong entity combined with its partial key (discriminator).', 'medium', 40, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In Relational Database Management Systems (RDBMS), what characterizes a ''Weak Entity''?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What is the primary architectural difference between a Mutex and a Semaphore in operating system synchronization?', NULL, NULL, 'A Mutex has an ownership property and can only be released by the locking thread, whereas a Semaphore can be signaled by any thread', 'A Mutex operates only on hardware interrupts while Semaphores operate on CPU registers', 'A Semaphore is strictly binary while a Mutex supports integer counters', 'A Mutex causes busy waiting while Semaphores never block', 'A', 'A Mutex is an ownership lock (only the acquiring thread can release it). A Semaphore is a signaling mechanism without ownership semantics, allowing any thread to call post()/signal().', 'medium', 45, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the primary architectural difference between a Mutex and a Semaphore in operating system synchronization?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'What is the worst-case time complexity of searching for an element in an unbalanced Binary Search Tree (BST) containing N nodes?', NULL, NULL, 'O(log N)', 'O(N)', 'O(N log N)', 'O(1)', 'B', 'When a BST is skewed (degenerate), it behaves identically to a singly linked list, resulting in worst-case search time of O(N).', 'easy', 30, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the worst-case time complexity of searching for an element in an unbalanced Binary Search Tree (BST) containing N nodes?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'Which OSI Reference Model layer is responsible for end-to-end process-to-process reliable communication, segmenting, and flow control?', NULL, NULL, 'Network Layer', 'Transport Layer', 'Session Layer', 'Data Link Layer', 'B', 'The Transport Layer (Layer 4) provides process-to-process delivery, segmentation, flow control, and error recovery (e.g. TCP).', 'easy', 25, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which OSI Reference Model layer is responsible for end-to-end process-to-process reliable communication, segmenting, and flow control?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 15, 'Why should a base class that contains virtual member functions always declare a virtual destructor in C++?', NULL, 'cpp', 'To allow private members to be inherited by derived classes', 'To ensure that derived class destructors are properly invoked when deleting derived objects via base pointers', 'To prevent the compiler from generating default constructors', 'To force static allocation of base class instances', 'B', 'Without a virtual destructor, deleting a derived object through a Base* pointer triggers undefined behavior and leads to resource/memory leaks because only the base destructor executes.', 'medium', 45, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Why should a base class that contains virtual member functions always declare a virtual destructor in C++?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 24, 'A container contains 40 liters of pure milk. From this container, 4 liters of milk are drawn out and replaced with water. This replacement procedure is repeated once more. How many liters of pure milk remain in the container?', NULL, NULL, '32.0 liters', '32.4 liters', '34.2 liters', '30.8 liters', 'B', 'Formula: Remaining pure liquid = Initial * (1 - x/V)^n. Here, Initial = 40, x = 4, V = 40, n = 2. Remaining milk = 40 * (1 - 4/40)^2 = 40 * (0.9)^2 = 40 * 0.81 = 32.4 liters.', 'medium', 60, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A container contains 40 liters of pure milk. From this container, 4 liters of milk are drawn out and replaced with water. This replacement procedure is repeated once more. How many liters of pure milk remain in the container?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 6, 'Find the missing term in the sequence: 2, 6, 12, 20, 30, 42, ?', NULL, NULL, '52', '54', '56', '60', 'C', 'Pattern is n * (n + 1): 1*2=2, 2*3=6, 3*4=12, 4*5=20, 5*6=30, 6*7=42, 7*8=56 (or consecutive differences +4, +6, +8, +10, +12, +14). 42 + 14 = 56.', 'easy', 30, 1, 'verified', 'Persistent', 'Persistent Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Find the missing term in the sequence: 2, 6, 12, 20, 30, 42, ?' AND `company_name` = 'Persistent');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 1, 'A passenger train running at a speed of 60 km/h crosses a signal pole in 9 seconds. What is the length of the train in meters?', NULL, NULL, '120 meters', '150 meters', '180 meters', '200 meters', 'B', 'Speed in m/s = 60 * (5/18) = 50/3 m/s. Length = Speed * Time = (50/3) * 9 = 50 * 3 = 150 meters.', 'easy', 35, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A passenger train running at a speed of 60 km/h crosses a signal pole in 9 seconds. What is the length of the train in meters?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 2, 'The cost price of 20 items is identical to the selling price of ''x'' items. If the vendor achieves a profit of 25%, what is the value of ''x''?', NULL, NULL, '15', '16', '18', '25', 'B', 'Let CP of 1 item = 1 => CP of 20 items = 20. SP of x items = 20 => SP of 1 item = 20/x. Profit = 25% => SP = 1.25 * CP => 20/x = 1.25 => x = 20 / 1.25 = 16.', 'medium', 45, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'The cost price of 20 items is identical to the selling price of ''x'' items. If the vendor achieves a profit of 25%, what is the value of ''x''?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 24, 'What is the difference between Compound Interest and Simple Interest on a principal of ₹5,000 for 2 years at an annual rate of 10%?', NULL, NULL, '₹25', '₹50', '₹75', '₹100', 'B', 'For 2 years, Difference = P * (R / 100)^2 = 5000 * (10 / 100)^2 = 5000 * 0.01 = ₹50.', 'easy', 35, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the difference between Compound Interest and Simple Interest on a principal of ₹5,000 for 2 years at an annual rate of 10%?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 3, 'If A : B = 2 : 3 and B : C = 4 : 5, find the combined ratio A : B : C in lowest terms.', NULL, NULL, '8 : 12 : 15', '6 : 10 : 15', '4 : 6 : 10', '2 : 4 : 5', 'A', 'Multiply A:B by 4 -> 8:12. Multiply B:C by 3 -> 12:15. Combined ratio A:B:C = 8:12:15.', 'easy', 30, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'If A : B = 2 : 3 and B : C = 4 : 5, find the combined ratio A : B : C in lowest terms.' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What will be printed upon execution of the following nested loop pseudo code snippet?', 'Integer i, j, val
Set val = 0
For i = 1 to 3
    For j = 1 to i
        val = val + i + j
    End For
End For
Print val', 'c', '18', '20', '25', '30', 'C', 'i=1, j=1: val = 0 + 1 + 1 = 2. i=2, j=1: val = 2 + 2 + 1 = 5. i=2, j=2: val = 5 + 2 + 2 = 9. i=3, j=1: val = 9 + 3 + 1 = 13. i=3, j=2: val = 13 + 3 + 2 = 18. i=3, j=3: val = 18 + 3 + 3 = 25.', 'medium', 60, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What will be printed upon execution of the following nested loop pseudo code snippet?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What will be the output of the following C program with pointer referencing?', '#include <stdio.h>
int main() {
    int i = 3;
    int *j = &i;
    int **k = &j;
    printf("%d", **k);
    return 0;
}', 'c', '3', 'Address of i', 'Address of j', 'Garbage value', 'A', '''k'' points to ''j'', which points to ''i''. Double dereferencing **k yields the integer value stored in ''i'', which is 3.', 'easy', 30, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What will be the output of the following C program with pointer referencing?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'Which SQL JOIN returns all matching rows from both tables, plus all unmatched rows from the left table and all unmatched rows from the right table with NULL fill?', NULL, 'sql', 'LEFT OUTER JOIN', 'RIGHT OUTER JOIN', 'FULL OUTER JOIN', 'INNER JOIN', 'C', 'A FULL OUTER JOIN combines the results of both LEFT and RIGHT outer joins, showing all records from both tables and filling in NULLs where matches do not exist.', 'easy', 25, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which SQL JOIN returns all matching rows from both tables, plus all unmatched rows from the left table and all unmatched rows from the right table with NULL fill?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 16, 'Which Object-Oriented principle binds data and the methods that manipulate that data together into a single class entity while hiding internal representation?', NULL, 'java', 'Inheritance', 'Encapsulation', 'Polymorphism', 'Interface segregation', 'B', 'Encapsulation wraps variables and methods inside classes and restricts direct outside access to internal state using access specifiers like private.', 'easy', 25, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which Object-Oriented principle binds data and the methods that manipulate that data together into a single class entity while hiding internal representation?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'Which core data structure is used natively in Breadth-First Search (BFS) graph traversal to process neighbor vertices level-by-level?', NULL, NULL, 'Stack', 'Queue', 'Max-Heap', 'Binary Tree', 'B', 'Breadth-First Search requires FIFO (First-In, First-Out) queuing to explore nodes in order of their distance from the source.', 'easy', 25, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which core data structure is used natively in Breadth-First Search (BFS) graph traversal to process neighbor vertices level-by-level?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 6, 'Find the next number in the multiplication series: 7, 14, 42, 168, 840, ?', NULL, NULL, '4200', '5040', '5880', '6720', 'B', 'Pattern multiplies by successive integers: 7 * 2 = 14; 14 * 3 = 42; 42 * 4 = 168; 168 * 5 = 840; 840 * 6 = 5040.', 'easy', 35, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Find the next number in the multiplication series: 7, 14, 42, 168, 840, ?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 10, 'If ''CLOUD'' is coded as ''DMPVE'' in a standard forward-shift cipher, how is ''RAIN'' coded in that same system?', NULL, NULL, 'SBJO', 'SCKP', 'RBIO', 'TBJP', 'A', 'Each letter is shifted forward by 1: C->D, L->M, O->P, U->V, D->E. Therefore: R->S, A->B, I->J, N->O => SBJO.', 'easy', 30, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'If ''CLOUD'' is coded as ''DMPVE'' in a standard forward-shift cipher, how is ''RAIN'' coded in that same system?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 13, 'What does the idiomatic expression ''Bite the bullet'' signify?', NULL, NULL, 'To face an inevitable, difficult situation with bravery and fortitude', 'To make a rash and dangerous investment', 'To violently confront a team colleague', 'To withdraw from a competition at the last minute', 'A', '''Bite the bullet'' means to endure a painful or undesirable situation that is seen as unavoidable.', 'easy', 25, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What does the idiomatic expression ''Bite the bullet'' signify?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 16, 'In Java, can a static method directly access non-static instance fields of the same class without instantiating an object?', NULL, 'java', 'Yes, static methods have full access to all instance variables', 'No, static methods belong to the class and have no implicit ''this'' reference to instance variables', 'Yes, but only if the variable is declared protected', 'Yes, provided the method is public', 'B', 'Static methods are resolved at class load time and have no object context (''this''), so accessing instance fields directly produces a compilation error.', 'easy', 30, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In Java, can a static method directly access non-static instance fields of the same class without instantiating an object?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 12, 'A can finish a task alone in 12 days, while B can complete the same task alone in 24 days. Working together, how many days will they take to complete it?', NULL, NULL, '6 days', '8 days', '9 days', '10 days', 'B', 'Combined rate = 1/12 + 1/24 = (2 + 1)/24 = 3/24 = 1/8. Working together, they require 8 days.', 'easy', 30, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A can finish a task alone in 12 days, while B can complete the same task alone in 24 days. Working together, how many days will they take to complete it?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What is the output of this C code snippet with integer division?', '#include <stdio.h>
int main() {
    int a = 7, b = 2;
    float c = a / b;
    printf("%.2f", c);
    return 0;
}', 'c', '3.50', '3.00', '3.5', '4.00', 'B', 'Both ''a'' and ''b'' are integers, so ''a / b'' performs integer truncation yielding 3 before being assigned to the float variable ''c''. Hence, c = 3.00.', 'medium', 40, 1, 'verified', 'Virtusa', 'Virtusa Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the output of this C code snippet with integer division?' AND `company_name` = 'Virtusa');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 6, 'Find the missing term in the sequence: 32, 49, 83, 151, 287, 559, ?', NULL, NULL, '1050', '1103', '1120', '1150', 'B', 'Differences between consecutive terms double each time: 49 - 32 = 17; 83 - 49 = 34; 151 - 83 = 68; 287 - 151 = 136; 559 - 287 = 272. Next difference is 272 * 2 = 544. Next term = 559 + 544 = 1103.', 'medium', 55, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Find the missing term in the sequence: 32, 49, 83, 151, 287, 559, ?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 2, 'The market price of sugar is increased by 25%. By what percentage should a household reduce its consumption so that its total expenditure remains unchanged?', NULL, NULL, '15%', '20%', '25%', '30%', 'B', 'Percentage decrease = [r / (100 + r)] * 100 = (25 / 125) * 100 = 20%.', 'easy', 30, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'The market price of sugar is increased by 25%. By what percentage should a household reduce its consumption so that its total expenditure remains unchanged?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 1, 'Convert an automobile speed of 90 km/h into meters per second (m/s).', NULL, NULL, '20 m/s', '25 m/s', '30 m/s', '35 m/s', 'B', 'Multiply by 5/18: 90 * (5/18) = 5 * 5 = 25 m/s.', 'easy', 20, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Convert an automobile speed of 90 km/h into meters per second (m/s).' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'What is the primary difference in storage mechanism between the CHAR and VARCHAR2 data types in an Oracle Database?', NULL, 'sql', 'CHAR pads shorter strings with trailing blank spaces to match declared length, while VARCHAR2 stores only the exact characters without blank padding', 'CHAR supports Unicode while VARCHAR2 supports only ASCII', 'VARCHAR2 has a maximum size of 255 bytes while CHAR supports 2GB', 'CHAR is stored in row while VARCHAR2 is always stored as out-of-line LOB', 'A', 'CHAR is fixed-length and blanks-pads shorter inputs up to defined length. VARCHAR2 allocates space only for the actual string length without padding.', 'medium', 40, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the primary difference in storage mechanism between the CHAR and VARCHAR2 data types in an Oracle Database?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'Can Transaction Control Language (TCL) commands such as COMMIT or ROLLBACK be executed directly inside an Oracle database DML row trigger?', NULL, 'sql', 'Yes, triggers have full autonomy over transactions', 'No, because a trigger executes as an integral part of the triggering DML transaction boundary', 'Yes, but only in AFTER INSERT triggers', 'Yes, provided the trigger is marked DETERMINISTIC', 'B', 'DML triggers execute within the calling transaction''s context. Explicit COMMIT or ROLLBACK inside a trigger would violate transaction integrity (unless configured in an AUTONOMOUS_TRANSACTION pragma).', 'hard', 50, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Can Transaction Control Language (TCL) commands such as COMMIT or ROLLBACK be executed directly inside an Oracle database DML row trigger?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'Which SQL construct correctly finds all employees who earn more than the average salary of their specific department?', 'SELECT emp_id, salary, dept_id FROM employees e
WHERE salary > (
    SELECT AVG(salary) FROM employees
    WHERE dept_id = e.dept_id
);', 'sql', 'Correlated Subquery', 'Recursive CTE', 'Cross Join', 'Scalar Literal', 'A', 'The inner query references e.dept_id from the outer query, causing it to be evaluated for each outer candidate row: a classic correlated subquery.', 'medium', 45, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which SQL construct correctly finds all employees who earn more than the average salary of their specific department?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 15, 'What will be printed when running this C++ program exhibiting virtual function dynamic dispatch?', '#include <iostream>
using namespace std;
class Base {
public:
    virtual void show() { cout << "Base show"; }
};
class Derived : public Base {
public:
    void show() { cout << "Derived show"; }
};
int main() {
    Base *b = new Derived();
    b->show();
    delete b;
    return 0;
}', 'cpp', 'Base show', 'Derived show', 'Compilation Error', 'Runtime Exception', 'B', 'Because ''show()'' is declared virtual in Base, C++ uses dynamic vtable dispatch at runtime, calling Derived''s overridden version.', 'medium', 40, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What will be printed when running this C++ program exhibiting virtual function dynamic dispatch?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 15, 'In C++, what does the declaration ''int * const ptr = &val;'' designate?', NULL, 'cpp', 'A constant pointer to an integer (the address cannot change, but the value pointed to can be modified)', 'A pointer to a constant integer (the value cannot change, but the address can)', 'A pointer that can only point to null', 'A pointer stored in read-only code memory', 'A', 'Reading right-to-left: ptr is a const pointer to int. The pointer itself cannot be reassigned to a different memory address, but *ptr can be modified.', 'medium', 40, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In C++, what does the declaration ''int * const ptr = &val;'' designate?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'What is the typical time complexity of searching, inserting, and deleting keys in a balanced B-Tree index structure in Oracle?', NULL, 'sql', 'O(1)', 'O(log N)', 'O(N)', 'O(N^2)', 'B', 'Because B-Trees maintain balanced height with large fan-out factors, search, insertion, and deletion scale logarithmically: O(log N).', 'easy', 30, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the typical time complexity of searching, inserting, and deleting keys in a balanced B-Tree index structure in Oracle?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'Which ACID property guarantees that once a database transaction commits, its modifications are permanently recorded and will survive sudden power loss or crashes?', NULL, 'sql', 'Atomicity', 'Consistency', 'Isolation', 'Durability', 'D', 'Durability ensures committed changes are persisted to non-volatile storage (via redo logs / write-ahead logs) and survive catastrophic failures.', 'easy', 25, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which ACID property guarantees that once a database transaction commits, its modifications are permanently recorded and will survive sudden power loss or crashes?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'Which of the following is NOT one of the four essential Coffman conditions necessary for a system deadlock to manifest?', NULL, NULL, 'Mutual Exclusion', 'Hold and Wait', 'Preemptive resource reallocation allowed', 'Circular Wait', 'C', 'Deadlock requires ''No preemption'' (resources cannot be forcibly taken away from a process). Allowing preemption actively prevents deadlock.', 'medium', 40, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which of the following is NOT one of the four essential Coffman conditions necessary for a system deadlock to manifest?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'What is the tightest time complexity to construct a Binary Max-Heap from an unsorted array of N elements using Floyd''s build-heap bottom-up approach?', NULL, NULL, 'O(log N)', 'O(N)', 'O(N log N)', 'O(N^2)', 'B', 'Floyd''s bottom-up build-heap procedure runs in linear O(N) time due to the decaying geometric summation of work across tree levels.', 'hard', 50, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the tightest time complexity to construct a Binary Max-Heap from an unsorted array of N elements using Floyd''s build-heap bottom-up approach?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 24, 'Two unbiased six-sided dice are rolled simultaneously. What is the probability that the sum of the numbers appearing on top equals 8?', NULL, NULL, '5/36', '1/6', '7/36', '1/9', 'A', 'Total outcomes = 36. Outcomes summing to 8: (2,6), (3,5), (4,4), (5,3), (6,2) = 5 outcomes. Probability = 5/36.', 'easy', 35, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Two unbiased six-sided dice are rolled simultaneously. What is the probability that the sum of the numbers appearing on top equals 8?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'In a relational database table definition, what is the constraint rule regarding PRIMARY KEY and UNIQUE constraints?', NULL, 'sql', 'A table can define only one PRIMARY KEY, but can define multiple UNIQUE constraints', 'A table can define multiple PRIMARY KEY constraints, but only one UNIQUE constraint', 'A table cannot combine both PRIMARY KEY and UNIQUE constraints', 'Both constraints must refer to foreign tables', 'A', 'Standard SQL permits exactly one PRIMARY KEY per table, while multiple columns or groups of columns can have separate UNIQUE constraints.', 'easy', 30, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In a relational database table definition, what is the constraint rule regarding PRIMARY KEY and UNIQUE constraints?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 24, 'What is the angle between the hour hand and the minute hand of an analog clock at exactly 3:30?', NULL, NULL, '60 degrees', '75 degrees', '80 degrees', '90 degrees', 'B', 'Formula: Angle = |30*H - (11/2)*M|. For H=3, M=30: |30*3 - (11/2)*30| = |90 - 165| = |-75| = 75 degrees.', 'medium', 45, 1, 'verified', 'Oracle', 'Oracle Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the angle between the hour hand and the minute hand of an analog clock at exactly 3:30?' AND `company_name` = 'Oracle');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 1, 'A train 150 meters long is traveling at a speed of 60 km/h. In how many seconds will it completely pass a person standing still on a platform?', NULL, NULL, '8 seconds', '9 seconds', '10 seconds', '12 seconds', 'B', 'Speed = 60 * (5/18) = 50/3 m/s. Time = Distance / Speed = 150 / (50/3) = (150 * 3) / 50 = 9 seconds.', 'easy', 35, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A train 150 meters long is traveling at a speed of 60 km/h. In how many seconds will it completely pass a person standing still on a platform?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 3, 'The ratio of savings to current account deposits in a bank branch is 3 : 5. If total combined deposits equal ₹800,000, find the total value of savings account deposits.', NULL, NULL, '₹240,000', '₹300,000', '₹360,000', '₹480,000', 'B', 'Total ratio parts = 3 + 5 = 8 parts. 1 part = 800,000 / 8 = ₹100,000. Savings deposit (3 parts) = 3 * 100,000 = ₹300,000.', 'easy', 30, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'The ratio of savings to current account deposits in a bank branch is 3 : 5. If total combined deposits equal ₹800,000, find the total value of savings account deposits.' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 2, 'A number is increased by 10% and subsequently decreased by 10%. What is the net percentage change in the number?', NULL, NULL, '0% change', '1% increase', '1% decrease', '2% decrease', 'C', 'Net change = x + y + (x * y)/100 = 10 - 10 + (10 * -10)/100 = 0 - 1 = -1% (1% decrease).', 'easy', 25, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A number is increased by 10% and subsequently decreased by 10%. What is the net percentage change in the number?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 6, 'Find the next missing number in the progression: 8, 12, 18, 26, 36, ?', NULL, NULL, '42', '46', '48', '52', 'C', 'Differences between consecutive terms form an arithmetic progression: 12 - 8 = 4; 18 - 12 = 6; 26 - 18 = 8; 36 - 26 = 10. Next difference is 12. Next term = 36 + 12 = 48.', 'easy', 30, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Find the next missing number in the progression: 8, 12, 18, 26, 36, ?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 10, 'If in a certain numerical code, CAT is coded as 24 and DOG is coded as 26, how will BAT be coded?', NULL, NULL, '21', '22', '23', '25', 'C', 'Letters correspond to their alphabetical ordinal positions: C(3) + A(1) + T(20) = 24. D(4) + O(15) + G(7) = 26. BAT = B(2) + A(1) + T(20) = 23.', 'easy', 25, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'If in a certain numerical code, CAT is coded as 24 and DOG is coded as 26, how will BAT be coded?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 14, 'What is the output printed by the following conditional pseudo code snippet?', 'Integer a, b, c
Set a = 5, b = 10
Set c = a + b * 2
If (c > 20) Then
    c = c + 5
Else
    c = c - 5
End If
Print c', 'c', '20', '25', '30', '35', 'C', 'By operator precedence, multiplication precedes addition: b * 2 = 10 * 2 = 20. Then c = 5 + 20 = 25. Condition 25 > 20 is True, so c = 25 + 5 = 30.', 'easy', 30, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What is the output printed by the following conditional pseudo code snippet?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'What does the following recursive pseudo code algorithm compute for an input integer n >= 0?', 'Function Solve(int n)
    If n == 0 or n == 1 Then
        Return n
    Else
        Return Solve(n - 1) + Solve(n - 2)
    End If
End Function', 'c', 'Factorial of n', 'Sum of first n numbers', 'The n-th Fibonacci number', 'Power of 2 to n', 'C', 'The recurrence relation F(n) = F(n-1) + F(n-2) with base cases F(0)=0 and F(1)=1 defines the Fibonacci sequence.', 'easy', 25, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'What does the following recursive pseudo code algorithm compute for an input integer n >= 0?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 21, 'Which fundamental data structure is most effective for checking whether parenthesis and bracket nesting is correctly balanced in an expression string?', NULL, NULL, 'Queue', 'Stack', 'Binary Search Tree', 'Array List', 'B', 'A Stack works on Last-In, First-Out (LIFO) order, allowing the most recently opened bracket to be matched against the next closing bracket.', 'easy', 25, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which fundamental data structure is most effective for checking whether parenthesis and bracket nesting is correctly balanced in an expression string?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 16, 'In Object-Oriented software engineering, the mechanism that bundles state and operations together while shielding internal implementation is called:', NULL, 'java', 'Polymorphism', 'Inheritance', 'Encapsulation', 'Dynamic Dispatch', 'C', 'Encapsulation packages variables and methods together and prevents unauthorized direct tampering through controlled interfaces.', 'easy', 20, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'In Object-Oriented software engineering, the mechanism that bundles state and operations together while shielding internal implementation is called:' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 1, 'A motorboat travels 24 km downstream in 2 hours and 16 km upstream in 2 hours. What is the speed of the water current/stream?', NULL, NULL, '1 km/h', '2 km/h', '3 km/h', '4 km/h', 'B', 'Downstream speed = 24 / 2 = 12 km/h. Upstream speed = 16 / 2 = 8 km/h. Stream speed = (Downstream - Upstream) / 2 = (12 - 8) / 2 = 2 km/h.', 'medium', 45, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A motorboat travels 24 km downstream in 2 hours and 16 km upstream in 2 hours. What is the speed of the water current/stream?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 12, 'P can finish a job in 15 days, and Q can finish it in 10 days. With the assistance of R, they complete the entire job together in 4 days. In how many days can R complete the job alone?', NULL, NULL, '10 days', '12 days', '14 days', '15 days', 'B', 'R''s 1-day work = 1/4 - (1/15 + 1/10) = 1/4 - 5/30 = 1/4 - 1/6 = (3 - 2)/12 = 1/12. R working alone takes 12 days.', 'medium', 50, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'P can finish a job in 15 days, and Q can finish it in 10 days. With the assistance of R, they complete the entire job together in 4 days. In how many days can R complete the job alone?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 23, 'Statements: Some cats are dogs. All dogs are birds.
Conclusions:
I. Some cats are birds.
II. Some birds are dogs.
Which conclusion(s) logically follow?', NULL, NULL, 'Only Conclusion I follows', 'Only Conclusion II follows', 'Both Conclusion I and II follow', 'Neither follows', 'C', 'The intersection of cats and dogs is contained in birds, so some cats are birds (I follows). Since all dogs are birds, some birds are dogs (II follows).', 'easy', 35, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Statements: Some cats are dogs. All dogs are birds.
Conclusions:
I. Some cats are birds.
II. Some birds are dogs.
Which conclusion(s) logically follow?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 8, 'Which of the following sentences utilizes the preposition of time correctly?', NULL, NULL, 'She has been working on the software module since three years.', 'She has been working on the software module for three years.', 'She is working on the software module from three years.', 'She works on the software module during three years.', 'B', '''For'' is used with a period/duration of time (''three years''), whereas ''since'' denotes a specific point in time (''since 2023'').', 'easy', 25, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which of the following sentences utilizes the preposition of time correctly?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 20, 'Which standard SQL keyword is placed directly after SELECT to filter out redundant duplicate rows from the query output?', NULL, 'sql', 'UNIQUE', 'DISTINCT', 'FILTER', 'GROUP', 'B', 'SELECT DISTINCT removes duplicate rows from the result set, returning only unique records.', 'easy', 20, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'Which standard SQL keyword is placed directly after SELECT to filter out redundant duplicate rows from the query output?' AND `company_name` = 'Coforge');

INSERT INTO `questions` (`subtopic_id`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `difficulty`, `est_time_seconds`, `is_published`, `verification_level`, `company_name`, `source_tag`)
SELECT 2, 'A shopkeeper sells an electronic gadget for ₹840 and realizes a profit of 20%. What was the actual cost price of the gadget?', NULL, NULL, '₹680', '₹700', '₹720', '₹750', 'B', 'SP = CP * 1.20 => 840 = CP * 1.20 => CP = 840 / 1.20 = ₹700.', 'easy', 30, 1, 'verified', 'Coforge', 'Coforge Placement PYQ'
WHERE NOT EXISTS (SELECT 1 FROM `questions` WHERE `question_text` = 'A shopkeeper sells an electronic gadget for ₹840 and realizes a profit of 20%. What was the actual cost price of the gadget?' AND `company_name` = 'Coforge');

