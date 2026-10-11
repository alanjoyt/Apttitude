-- Migration: Fix duplicate distractors in questions (Apttitudee)
-- Date: 2026-10-10
-- Author: mr.ignite ~ Nexyra Solutions
-- Description: Corrects duplicate distractors in 11 questions where option choices were identical.

-- Question 174 (Ratio 3:7 sum 100, larger share = 70; option C and D were both 90)
UPDATE `questions` SET `option_d` = '80' WHERE `id` = 174 AND `option_d` = '90';

-- Question 194 (Ratio 3:8 sum 110, larger share = 80; option C and D were both 100)
UPDATE `questions` SET `option_d` = '90' WHERE `id` = 194 AND `option_d` = '100';

-- Question 196 (Ratio 5:5 sum 100, larger share = 50; option A and B were both 50)
UPDATE `questions` SET `option_b` = '40' WHERE `id` = 196 AND `option_b` = '50';

-- Question 203 (Ratio 5:5 sum 150, larger share = 75; option A and B were both 75)
UPDATE `questions` SET `option_b` = '65' WHERE `id` = 203 AND `option_b` = '75';

-- Question 504 (Pythagoras 8 km N + 6 km E = 10 km; option B and D were both 14 km)
UPDATE `questions` SET `option_d` = '12 km' WHERE `id` = 504 AND `option_d` = '14 km';

-- Question 508 (Pythagoras 6 km N + 8 km E = 10 km; option B and D were both 14 km)
UPDATE `questions` SET `option_d` = '12 km' WHERE `id` = 508 AND `option_d` = '14 km';

-- Question 516 (Pythagoras 6 km N + 6 km E = 8 km approx; option B and D were both 12 km)
UPDATE `questions` SET `option_d` = '10 km' WHERE `id` = 516 AND `option_d` = '12 km';

-- Question 523 (Pythagoras 8 km N + 6 km E = 10 km; option B and D were both 14 km)
UPDATE `questions` SET `option_d` = '12 km' WHERE `id` = 523 AND `option_d` = '14 km';

-- Question 525 (Pythagoras 6 km N + 8 km E = 10 km; option B and D were both 14 km)
UPDATE `questions` SET `option_d` = '12 km' WHERE `id` = 525 AND `option_d` = '14 km';

-- Question 532 (Pythagoras 6 km N + 8 km E = 10 km; option B and D were both 14 km)
UPDATE `questions` SET `option_d` = '12 km' WHERE `id` = 532 AND `option_d` = '14 km';

-- Question 533 (Pythagoras 8 km N + 6 km E = 10 km; option B and D were both 14 km)
UPDATE `questions` SET `option_d` = '12 km' WHERE `id` = 533 AND `option_d` = '14 km';
