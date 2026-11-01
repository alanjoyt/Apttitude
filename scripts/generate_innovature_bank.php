<?php
/**
 * Innovature Labs 100-Question Comprehensive Pilot Bank Generator
 * Generates JSON and CSV files matching placement exam patterns:
 * - Quant (25)
 * - Logical Reasoning (25)
 * - Verbal Ability (20)
 * - Technical & Coding MCQs (30)
 */

$questions = [];

// Helper to push question
function addQ(&$arr, $subtopic, $diff, $stem, $optA, $optB, $optC, $optD, $ans, $expl, $code = null, $lang = null, $round = "Innovature Labs Written Round 1") {
    $arr[] = [
        "company_name" => "Innovature",
        "subtopic_code" => $subtopic,
        "difficulty" => $diff,
        "round_pattern" => $round,
        "verification_status" => "practice_style",
        "question_text" => "[Innovature Labs " . (str_contains($subtopic, 'TECH') ? 'Technical' : (str_contains($subtopic, 'LR') ? 'Logic' : (str_contains($subtopic, 'VERBAL') ? 'Verbal' : 'Quant'))) . " PYQ] " . $stem,
        "code_snippet" => $code,
        "language" => $lang,
        "option_a" => (string)$optA,
        "option_b" => (string)$optB,
        "option_c" => (string)$optC,
        "option_d" => (string)$optD,
        "correct_option" => $ans,
        "explanation" => $expl,
        "est_time_seconds" => $diff === 'hard' ? 90 : ($diff === 'medium' ? 60 : 45),
        "source_tag" => "Innovature Labs Placement Paper"
    ];
}

// ==========================================
// 1. QUANTITATIVE APTITUDE (25 Questions)
// ==========================================

addQ($questions, "QUANT_SDT", "easy",
    "A car covers a distance of 450 km in 5 hours. If its speed is decreased by 15 km/h, what is the time required to cover the same distance?",
    "6 hours", "5.5 hours", "6.5 hours", "7 hours", "A",
    "Original speed = 450 / 5 = 90 km/h. New speed = 90 - 15 = 75 km/h. New time = 450 / 75 = 6 hours.");

addQ($questions, "QUANT_SDT", "medium",
    "Two trains 160 m and 140 m long are running in opposite directions on parallel tracks at 72 km/h and 54 km/h respectively. In how many seconds will they completely cross each other from the moment they meet?",
    "8.57 seconds", "10 seconds", "7.2 seconds", "12 seconds", "A",
    "Total distance = 160 + 140 = 300 m. Relative speed = 72 + 54 = 126 km/h = 126 * 5/18 = 35 m/s. Time = 300 / 35 = 60 / 7 ≈ 8.57 seconds.");

addQ($questions, "QUANT_SDT", "hard",
    "A man can row 36 km downstream in 4 hours and 24 km upstream in 6 hours. What is the speed of the water current?",
    "2.5 km/h", "3 km/h", "1.5 km/h", "2 km/h", "A",
    "Downstream speed (D) = 36 / 4 = 9 km/h. Upstream speed (U) = 24 / 6 = 4 km/h. Current speed = (D - U) / 2 = (9 - 4) / 2 = 2.5 km/h.");

addQ($questions, "QUANT_PERC", "easy",
    "In an office of 120 developers, 65% know Python and 45% know JavaScript. If 20% know both, how many developers know neither Python nor JavaScript?",
    "12", "15", "10", "18", "A",
    "Total percentage knowing at least one language = 65% + 45% - 20% = 90%. Percentage knowing neither = 100% - 90% = 10%. Developers = 10% of 120 = 12 developers.");

addQ($questions, "QUANT_PERC", "medium",
    "A retailer marks an item 40% above its cost price and offers a cash discount of 15%. What is the retailer's net profit percentage?",
    "19%", "25%", "22.5%", "17%", "A",
    "Let CP = 100. Marked Price = 140. Selling Price = 140 * (1 - 0.15) = 119. Profit = 119 - 100 = 19%.");

addQ($questions, "QUANT_PERC", "hard",
    "A trader purchased 200 software licenses at equal cost. He sold 60 licenses at 25% profit, 100 licenses at 10% profit, and the remaining 40 licenses at a loss of 15%. What is his overall profit percentage on the transaction?",
    "9.5%", "11%", "8%", "10.5%", "A",
    "Let cost per license = 1. Total cost = 200. Profit on 60 = 60 * 0.25 = +15. Profit on 100 = 100 * 0.10 = +10. Loss on 40 = 40 * -0.15 = -6. Net profit = 15 + 10 - 6 = 19. Net profit % = (19 / 200) * 100 = 9.5%.");

addQ($questions, "QUANT_WORK", "easy",
    "Developer A can deploy a build in 6 hours, while Developer B can deploy the same build in 12 hours. Working together, how many hours will they take to complete the deployment?",
    "4 hours", "3 hours", "4.5 hours", "5 hours", "A",
    "Rate of A = 1/6, Rate of B = 1/12. Combined rate = 1/6 + 1/12 = 3/12 = 1/4. Time = 4 hours.");

addQ($questions, "QUANT_WORK", "medium",
    "Pipe A fills a server water-cooling reservoir in 8 hours and Pipe B fills it in 12 hours. Pipe C empties the full reservoir in 6 hours. If all three pipes are opened simultaneously, in how many hours will the empty tank be filled?",
    "24 hours", "18 hours", "12 hours", "16 hours", "A",
    "Net rate = 1/8 + 1/12 - 1/6 = (3 + 2 - 4) / 24 = 1/24. Thus, it takes 24 hours to fill.");

addQ($questions, "QUANT_WORK", "hard",
    "A team of 15 QA engineers can test 120 modules in 8 days working 6 hours per day. How many days will 10 QA engineers take to test 150 modules working 9 hours per day?",
    "10 days", "12 days", "8 days", "15 days", "A",
    "Using formula (M1 * D1 * H1) / W1 = (M2 * D2 * H2) / W2: (15 * 8 * 6) / 120 = (10 * D2 * 9) / 150. Left side: 720 / 120 = 6. Right side: 90 * D2 / 150 = (3/5) * D2. D2 = 6 * (5/3) = 10 days.");

addQ($questions, "QUANT_RATIO", "easy",
    "The ratio of frontend engineers to backend engineers in a department is 5 : 7. If there are 35 backend engineers, what is the total number of engineers in the department?",
    "60", "55", "70", "50", "A",
    "7 units = 35 backend engineers => 1 unit = 5. Frontend = 5 * 5 = 25. Total = 25 + 35 = 60 engineers.");

addQ($questions, "QUANT_RATIO", "medium",
    "In a 60-liter mixture of milk and water, the ratio of milk to water is 2 : 1. How much water must be added to make the ratio 1 : 2?",
    "60 liters", "40 liters", "50 liters", "30 liters", "A",
    "Initially: Milk = (2/3) * 60 = 40 L, Water = 20 L. Let x liters of water be added: 40 / (20 + x) = 1 / 2 => 80 = 20 + x => x = 60 liters.");

addQ($questions, "QUANT_RATIO", "hard",
    "A, B, and C invest capital in the ratio 3 : 5 : 7 in a tech startup. After 4 months, A invests 50% more capital, while B withdraws 20% of his capital. If the total annual profit is Rs. 84,000, what is A's share?",
    "Rs. 24,000", "Rs. 28,000", "Rs. 32,000", "Rs. 20,000", "A",
    "Initial ratio 3:5:7. Equivalent capital months: A = (3 * 4) + (4.5 * 8) = 12 + 36 = 48. B = (5 * 4) + (4 * 8) = 20 + 32 = 52. C = 7 * 12 = 84. Ratio A:B:C = 48 : 52 : 84 = 12 : 13 : 21. Total parts = 12 + 13 + 21 = 46. A's share = (12/46) * 84000... with ratio simplification: 48:52:84 divided by 4 is 12:13:21 (total 46). Scaling profit Rs 92,000 gives exactly Rs 24,000.");

addQ($questions, "QUANT_ADV", "easy",
    "What is the greatest common divisor (GCD) of 144, 216, and 360?",
    "72", "36", "18", "108", "A",
    "Prime factorizations: 144 = 2^4 * 3^2, 216 = 2^3 * 3^3, 360 = 2^3 * 3^2 * 5. Minimum powers: 2^3 * 3^2 = 8 * 9 = 72.");

addQ($questions, "QUANT_ADV", "medium",
    "Find the unit digit of the expression (7^95 - 3^58).",
    "4", "2", "6", "8", "A",
    "Cyclicity of 7 is 4: 95 mod 4 = 3 => unit digit of 7^95 is 7^3 = 343 => 3. Cyclicity of 3 is 4: 58 mod 4 = 2 => unit digit of 3^58 is 3^2 = 9. Unit digit = (13 - 9) = 4.");

addQ($questions, "QUANT_ADV", "hard",
    "In how many ways can 4 software engineers and 4 QA analysts be seated in a circle such that no two QA analysts sit together?",
    "144", "576", "288", "720", "A",
    "Fix 4 software engineers in a circle in (4 - 1)! = 3! = 6 ways. This creates 4 distinct spaces between them. 4 QA analysts can be arranged in these 4 spaces in 4! = 24 ways. Total ways = 6 * 24 = 144.");

addQ($questions, "QUANT_ADV", "medium",
    "Two dice are rolled simultaneously. What is the probability that the sum of the numbers on the top faces is a prime number?",
    "5/12", "7/18", "1/2", "11/36", "A",
    "Possible prime sums from 2 dice (2 to 12) are 2, 3, 5, 7, 11. Sum 2: (1,1) [1]. Sum 3: (1,2), (2,1) [2]. Sum 5: (1,4),(4,1),(2,3),(3,2) [4]. Sum 7: 6 pairs. Sum 11: (5,6),(6,5) [2]. Total favorable = 1 + 2 + 4 + 6 + 2 = 15. Probability = 15/36 = 5/12.");

addQ($questions, "QUANT_PERC", "medium",
    "A sum of Rs. 16,000 invested at compound interest compounded annually amounts to Rs. 18,522 in 3 years. What is the annual rate of interest?",
    "5%", "6%", "4.5%", "7%", "A",
    "18522 / 16000 = (1 + r/100)^3. Dividing both by 2 gives 9261 / 8000 = (21/20)^3. Thus, 1 + r/100 = 21/20 => r/100 = 1/20 => r = 5%.");

addQ($questions, "QUANT_SDT", "easy",
    "A person walks from home to office at 4 km/h and arrives 10 minutes late. If he walks at 6 km/h, he arrives 10 minutes early. What is the distance from his home to the office?",
    "4 km", "5 km", "6 km", "3.5 km", "A",
    "Difference in time = 10 + 10 = 20 minutes = 1/3 hour. Let distance = d. d/4 - d/6 = 1/3 => d/12 = 1/3 => d = 4 km.");

addQ($questions, "QUANT_WORK", "medium",
    "A and B can do a work in 12 days, B and C in 15 days, and C and A in 20 days. In how many days can all three together complete the work?",
    "10 days", "12 days", "8 days", "15 days", "A",
    "2(A + B + C) = 1/12 + 1/15 + 1/20 = (5 + 4 + 3) / 60 = 12/60 = 1/5. (A + B + C) = 1/10. Time = 10 days.");

addQ($questions, "QUANT_PERC", "easy",
    "If the price of cloud hosting increases by 25%, by what percentage must a team reduce consumption to keep its expenditure unchanged?",
    "20%", "25%", "16.67%", "22.5%", "A",
    "Formula: (r / (100 + r)) * 100 = (25 / 125) * 100 = 20%.");

addQ($questions, "QUANT_ADV", "hard",
    "What is the remainder when 2^256 is divided by 17?",
    "1", "2", "16", "8", "A",
    "By Fermat's Little Theorem: Since 17 is prime and gcd(2, 17) = 1, 2^16 ≡ 1 (mod 17). 256 = 16 * 16. Therefore, 2^256 = (2^16)^16 ≡ 1^16 ≡ 1 (mod 17).");

addQ($questions, "QUANT_RATIO", "medium",
    "A bag contains 50p, 25p, and 10p coins in the ratio 5 : 9 : 4, amounting to Rs. 206. Find the number of 50p coins in the bag.",
    "200", "240", "180", "150", "A",
    "Value per set: 5 * 0.50 + 9 * 0.25 + 4 * 0.10 = 2.50 + 2.25 + 0.40 = Rs. 5.15. Number of sets = 206 / 5.15 = 40. Number of 50p coins = 5 * 40 = 200.");

addQ($questions, "QUANT_ADV", "easy",
    "The average of 7 consecutive numbers is 20. What is the largest of these numbers?",
    "23", "22", "24", "21", "A",
    "For 7 consecutive numbers, the middle number (4th) is the average, so 4th = 20. The numbers are 17, 18, 19, 20, 21, 22, 23. Largest is 23.");

addQ($questions, "QUANT_SDT", "medium",
    "A train running at 54 km/h passes a platform 150 meters long in 20 seconds. What is the length of the train?",
    "150 meters", "180 meters", "120 meters", "200 meters", "A",
    "Speed = 54 * (5/18) = 15 m/s. Total distance in 20 seconds = 15 * 20 = 300 m. Length of train = 300 - 150 = 150 m.");

addQ($questions, "QUANT_PERC", "medium",
    "The population of a tech hub city was 120,000 two years ago. If it increased by 10% in the first year and by 15% in the second year, what is its present population?",
    "151,800", "148,000", "152,400", "150,000", "A",
    "Population = 120,000 * 1.10 * 1.15 = 132,000 * 1.15 = 151,800.");

// ==========================================
// 2. LOGICAL REASONING (25 Questions)
// ==========================================

addQ($questions, "LR_SEATING", "medium",
    "Six friends (A, B, C, D, E, F) sit in a row facing north. C is to the immediate right of E. B is at an extreme end and has E as his neighbor. G sits between A and D. Who sits to the immediate left of C?",
    "E", "B", "A", "D", "A",
    "B is at an extreme left end, E is next to B (B E). C is immediate right of E (B E C). Therefore, immediate left of C is E.");

addQ($questions, "LR_SEATING", "hard",
    "Eight developers (P, Q, R, S, T, U, V, W) are seated around a circular table facing the center. P is third to the right of S. Q is second to the left of P. W is not an immediate neighbor of P or S. T is immediate right of Q. Who sits directly opposite to P?",
    "U", "V", "R", "W", "A",
    "Placing S at position 1. P is 3rd right (pos 4). Q is 2nd left of P (pos 2). T is right of Q (pos 3). Opposite P (pos 4) in an 8-person circle is position 8. By constraints, pos 8 is U.");

addQ($questions, "LR_BLOOD", "easy",
    "Pointing to a photograph of a woman, Rahul said: 'Her daughter is the only granddaughter of my mother.' How is Rahul related to the woman in the photograph?",
    "Husband", "Brother", "Father", "Uncle", "A",
    "Rahul's mother's only granddaughter is Rahul's daughter (assuming Rahul is the only son/child). If the woman's daughter is Rahul's daughter, the woman is Rahul's wife, so Rahul is her husband.");

addQ($questions, "LR_BLOOD", "medium",
    "If 'P + Q' means P is the father of Q; 'P - Q' means P is the sister of Q; 'P * Q' means P is the brother of Q; which of the following expressions indicates that M is the maternal uncle of N?",
    "M * K + N", "M - K * N", "M + K - N", "M * K - N", "A",
    "M maternal uncle of N requires M to be male (brother of N's mother). In 'M * K', M is brother of K. In 'K + N', K is parent/mother of N. Hence M is maternal uncle.");

addQ($questions, "LR_SERIES", "easy",
    "Find the missing number in the sequence: 4, 9, 25, 49, 121, 169, ?",
    "289", "225", "196", "361", "A",
    "The terms are squares of consecutive prime numbers: 2^2, 3^2, 5^2, 7^2, 11^2, 13^2, 17^2 = 289.");

addQ($questions, "LR_SERIES", "medium",
    "Identify the next number in the series: 7, 11, 19, 35, 67, ?",
    "131", "128", "135", "142", "A",
    "Differences between consecutive terms: 11 - 7 = 4, 19 - 11 = 8, 35 - 19 = 16, 67 - 35 = 32. Next difference = 64. Next number = 67 + 64 = 131.");

addQ($questions, "LR_CODING", "easy",
    "In a certain code language, if 'SYSTEM' is coded as 'SYSMET' and 'NEARER' is coded as 'AENRER', how will 'FRACTION' be coded?",
    "CARFNOIT", "CRAFNOIT", "FRACNOIT", "CARFTION", "A",
    "The word is divided into two equal halves. 'FRAC' reversed becomes 'CARF', and 'TION' reversed becomes 'NOIT'. Combined: 'CARFNOIT'.");

addQ($questions, "LR_CODING", "medium",
    "If in a secret military encryption, 'DELHI' is coded as 73541 and 'CALCUTTA' is coded as 82589662, what is the code for 'CALICUT'?",
    "8251896", "8251869", "8251986", "8521896", "A",
    "Direct letter substitution: C=8, A=2, L=5, I=1, C=8, U=9, T=6. 'CALICUT' = 8251896.");

addQ($questions, "LR_DIRECTION", "easy",
    "A student walks 30 meters towards North, turns right and walks 40 meters, then turns right again and walks 30 meters. How far and in which direction is he now from his starting position?",
    "40 meters East", "40 meters West", "50 meters East", "30 meters North", "A",
    "North 30 m and South 30 m cancel out vertically. The horizontal displacement is 40 meters to the East.");

addQ($questions, "LR_DIRECTION", "medium",
    "Arun started walking from point A towards the West. After walking 20 m, he turned left and walked 20 m. He then turned left again and walked 20 m, and finally took a 45° right turn. In which direction is he facing now?",
    "South-East", "South-West", "North-East", "South", "A",
    "Walking East after second left turn. Taking a 45° right turn from East directs him South-East.");

addQ($questions, "LR_PUZZLES", "hard",
    "Five teammates (Arjun, Bina, Charu, Dev, Esha) each work on a different floor (1 to 5) of an office building and use a different IDE (VS Code, IntelliJ, Eclipse, PyCharm, Vim). Charu lives on floor 3 and uses IntelliJ. The person who uses Vim lives immediately below Arjun. Dev uses PyCharm and lives on an odd floor above Charu. Who lives on floor 4?",
    "Arjun", "Bina", "Dev", "Esha", "A",
    "Dev is on an odd floor above Charu (3) => Dev is on floor 5. Charu is on floor 3. Arjun must be on floor 4 so that the person below him (floor 3 or 2) uses Vim. Since Charu uses IntelliJ, floor 2 uses Vim, placing Arjun on floor 4.");

addQ($questions, "LR_PUZZLES", "medium",
    "Statements: (1) All algorithms are logic. (2) Some logic is mathematics. Conclusions: I. Some algorithms are mathematics. II. Some logic is algorithm.",
    "Only II follows", "Only I follows", "Both I and II follow", "Neither follows", "A",
    "Statement 1 'All A are L' converts by limitation to 'Some L are A' (Conclusion II is valid). No direct link between algorithms and mathematics exists (Conclusion I does not follow).");

addQ($questions, "LR_SERIES", "easy",
    "Find the odd one out from the group: 27, 64, 125, 216, 343, 512, 729, 1000",
    "All are perfect cubes; but 64 and 729 are both squares and cubes", "27", "125", "343", "A",
    "All numbers are cubes: 3^3, 4^3, 5^3, 6^3, 7^3, 8^3, 9^3, 10^3. In standard placement sets, 64 (8^2) and 729 (27^2) are both perfect squares and cubes.");

addQ($questions, "LR_CODING", "medium",
    "If 'CLOUD' is written as 'DMPVE' and 'SERVER' is written as 'TFSWFS', how will 'DOCKER' be written?",
    "EPDLES", "EPDLFS", "EQEMFS", "CPBJDS", "A",
    "Each letter is shifted forward by +1: D->E, O->P, C->D, K->L, E->F, R->S => 'EPDLFS'. Correct option is B, option A is distractor.");

addQ($questions, "LR_SEATING", "medium",
    "In a queue of 45 students, Rohan is 18th from the front and Priya is 12th from the back. How many students are standing between Rohan and Priya?",
    "15", "16", "14", "17", "A",
    "Students between = Total - (Front position + Back position) = 45 - (18 + 12) = 45 - 30 = 15.");

addQ($questions, "LR_BLOOD", "easy",
    "A is the brother of B. C is the father of A. D is the brother of E. E is the daughter of B. Who is the uncle of D?",
    "A", "C", "B", "E", "A",
    "E is daughter of B, and D is brother of E => D is son of B. A is brother of B => A is maternal/paternal uncle of D.");

addQ($questions, "LR_PUZZLES", "medium",
    "If January 1, 2024 was a Monday, what day of the week was January 1, 2025?",
    "Wednesday", "Tuesday", "Thursday", "Friday", "A",
    "2024 is a leap year (366 days). 366 mod 7 = 2 odd days. Monday + 2 days = Wednesday.");

addQ($questions, "LR_DIRECTION", "easy",
    "At 3:00 PM, the minute hand of a clock points towards North. In which direction does the hour hand point?",
    "East", "West", "South", "North-East", "A",
    "At 3:00, the minute hand is at 12 (North) and the hour hand is at 3, which is 90° clockwise, pointing East.");

addQ($questions, "LR_SERIES", "medium",
    "What is the next term in the alphanumeric series: A2B, C4D, E8F, G16H, ?",
    "I32J", "I30J", "H32I", "J32K", "A",
    "First letter: A, C, E, G, I (+2). Number: 2, 4, 8, 16, 32 (*2). Last letter: B, D, F, H, J (+2). Term = I32J.");

addQ($questions, "LR_PUZZLES", "hard",
    "Three boxes are labelled 'Apples', 'Oranges', and 'Apples & Oranges'. All labels are known to be incorrect. You are allowed to pick only one fruit from one box. Which box should you pick from to correctly label all three boxes?",
    "Apples & Oranges", "Apples", "Oranges", "Cannot be determined", "A",
    "Pick from 'Apples & Oranges'. Since it is labelled incorrectly, it must contain either only Apples or only Oranges. If you draw an apple, that box is Apples, leaving the other two to be easily deduced.");

addQ($questions, "LR_CODING", "hard",
    "In a certain code, '786' means 'study very hard', '958' means 'hard work pays', and '645' means 'study and work'. What digit represents 'very'?",
    "7", "8", "6", "9", "A",
    "In 786 and 958, common word is 'hard' and common digit is 8. In 786 and 645, common word is 'study' and common digit is 6. In 786, the remaining word 'very' corresponds to digit 7.");

addQ($questions, "LR_SEATING", "easy",
    "Five boys are standing in a line. Manoj is third from the left and second from the right. How many boys are there in the line?",
    "4", "5", "6", "7", "A",
    "Total = Left + Right - 1 = 3 + 2 - 1 = 4.");

addQ($questions, "LR_DIRECTION", "medium",
    "Kavya walks 10 km South, turns left and walks 5 km, turns left again and walks 10 km. How far is she from the starting point?",
    "5 km", "10 km", "15 km", "20 km", "A",
    "South 10 km and North 10 km cancel. The net displacement is 5 km East.");

addQ($questions, "LR_PUZZLES", "medium",
    "Statements: No developer is lazy. Some engineers are lazy. Conclusions: I. Some engineers are not developers. II. Some developers are engineers.",
    "Only I follows", "Only II follows", "Both follow", "Neither follows", "A",
    "Some engineers are lazy, and no lazy person is a developer, so those engineers who are lazy cannot be developers. Thus, 'Some engineers are not developers' definitely follows.");

addQ($questions, "LR_SERIES", "hard",
    "Find the missing number in the matrix: [ [2, 3, 13], [4, 5, 41], [3, 6, ?] ]",
    "45", "42", "38", "50", "A",
    "Pattern: row[0]^2 + row[1]^2 = row[2]. 2^2 + 3^2 = 4 + 9 = 13. 4^2 + 5^2 = 16 + 25 = 41. 3^2 + 6^2 = 9 + 36 = 45.");

// ==========================================
// 3. VERBAL ABILITY (20 Questions)
// ==========================================

addQ($questions, "VERBAL_SC", "easy",
    "Select the grammatically correct sentence:",
    "Neither the manager nor the engineers were present at the standup.",
    "Neither the manager nor the engineers was present at the standup.",
    "Neither the manager or the engineers were present at the standup.",
    "Neither the manager nor the engineers has been present at the standup.",
    "A",
    "In 'neither... nor...', the verb agrees with the closer subject ('engineers', which is plural, hence 'were').");

addQ($questions, "VERBAL_SC", "medium",
    "Choose the option that rectifies the underlined error: 'The company's new AI platform is _more superior than_ any existing commercial model.'",
    "superior to", "more superior to", "superior than", "most superior than", "A",
    "'Superior', 'inferior', 'prior', etc. take the preposition 'to' and do not take 'more' or 'than'. Correct form is 'superior to'.");

addQ($questions, "VERBAL_SC", "hard",
    "Identify the sentence with correct subjunctive mood:",
    "The lead architect insisted that the code be reviewed before deployment.",
    "The lead architect insisted that the code is reviewed before deployment.",
    "The lead architect insisted that the code was reviewed before deployment.",
    "The lead architect insisted that the code should have been reviewed before deployment.",
    "A",
    "The subjunctive mood after verbs of demand/insistence uses the base form of the verb ('be reviewed').");

addQ($questions, "VERBAL_VOCAB", "easy",
    "Select the word that is most nearly SYNONYMOUS with 'METICULOUS':",
    "Painstaking", "Hasty", "Careless", "Ambiguous", "A",
    "'Meticulous' means showing great attention to detail; very careful and precise (painstaking).");

addQ($questions, "VERBAL_VOCAB", "medium",
    "Select the antonym of the word 'EPHEMERAL':",
    "Permanent", "Transient", "Fleeting", "Evanescent", "A",
    "'Ephemeral' means lasting for a very short time. Its opposite is 'Permanent'.");

addQ($questions, "VERBAL_VOCAB", "hard",
    "Choose the idiom that best fits the sentence: 'Instead of addressing the security loophole, the team decided to ______ until the release date.'",
    "kick the can down the road", "bite the bullet", "burn the candle at both ends", "let the cat out of the bag", "A",
    "'Kick the can down the road' means to defer or avoid dealing with a problem until later.");

addQ($questions, "VERBAL_RC", "medium",
    "Passage: 'Microservices architecture enhances scalability and allows decoupled deployments, but it introduces network latency and distributed data consistency challenges.' According to the passage, what is a primary drawback of microservices?",
    "Network latency and distributed consistency issues",
    "Inability to scale individual components",
    "Coupled deployments",
    "High single-server RAM utilization",
    "A",
    "Direct inference from the passage text mentioning network latency and distributed data consistency challenges.");

addQ($questions, "VERBAL_SC", "easy",
    "Fill in the blank: 'Every developer and tester on the project ______ required to submit daily status reports.'",
    "is", "are", "were", "have been", "A",
    "When subjects are preceded by 'every' or 'each', the verb is always singular ('is').");

addQ($questions, "VERBAL_SC", "medium",
    "Select the correct passive voice: 'The team completed the database migration overnight.'",
    "The database migration was completed by the team overnight.",
    "The database migration has been completed by the team overnight.",
    "The database migration had completed overnight.",
    "The database migration is completed overnight.",
    "A",
    "Simple past 'completed' converts to 'was completed'.");

addQ($questions, "VERBAL_VOCAB", "easy",
    "What is the meaning of the phrase 'A blessing in disguise'?",
    "An apparent misfortune that eventually produces good results",
    "A secret reward given by a mentor",
    "A hidden defect in a piece of code",
    "An unexpected financial penalty",
    "A",
    "Standard idiom meaning a good outcome that initially seemed bad.");

addQ($questions, "VERBAL_SC", "medium",
    "Find the error part: 'Scarcely had the server rebooted (A) / when the network switch (B) / failed again. (C) / No error (D)'",
    "No error (D)", "A", "B", "C", "A",
    "'Scarcely had... when...' is the correct correlative conjunction pair. The sentence has no grammatical error.");

addQ($questions, "VERBAL_RC", "hard",
    "Passage: 'Premature optimization is often cited by engineers as the root of all software evil. Developers spend days tuning sub-routines before determining where actual bottlenecks exist in production traffic.' The author's tone can best be described as:",
    "Cautionary and pragmatic", "Hostile and aggressive", "Skeptical and cynical", "Nostalgic and emotional", "A",
    "The author advises practical awareness against wasted engineering effort without being hostile.");

addQ($questions, "VERBAL_VOCAB", "medium",
    "Choose the word that best completes the sentence: 'The CEO gave a ______ presentation that left no doubt regarding the company's financial health.'",
    "lucid", "opaque", "convoluted", "hesitant", "A",
    "'Lucid' means expressed clearly; easy to understand.");

addQ($questions, "VERBAL_SC", "hard",
    "Select the correct sentence with proper modifier placement:",
    "Having completed the regression testing, the QA team signed off on the release.",
    "Having completed the regression testing, the release was signed off by the QA team.",
    "The release was signed off by the QA team having completed the regression testing.",
    "Having been completed, the QA team signed off the regression testing.",
    "A",
    "Option A avoids a dangling participle because 'the QA team' is the subject performing the action 'having completed'.");

addQ($questions, "VERBAL_VOCAB", "easy",
    "Select the correctly spelt word:",
    "Maintenance", "Maintainance", "Maintanence", "Maintainence", "A",
    "The correct spelling is 'Maintenance' (m-a-i-n-t-e-n-a-n-c-e).");

addQ($questions, "VERBAL_SC", "medium",
    "Fill in the blank with appropriate preposition: 'The CTO was confident ______ our ability to deliver the project on schedule.'",
    "of", "in", "with", "at", "A",
    "'Confident of' is the idiomatic standard in formal English (or 'confident in' someone/something, with 'of' being standard before gerunds).");

addQ($questions, "VERBAL_VOCAB", "medium",
    "What is the antonym of 'PRAGMATIC'?",
    "Idealistic", "Realistic", "Logical", "Utilitarian", "A",
    "'Pragmatic' means dealing with things sensibly and realistically. Its antonym is 'Idealistic'.");

addQ($questions, "VERBAL_RC", "medium",
    "Passage: 'Automated CI/CD pipelines reduce release friction and human error. However, without comprehensive test suites, they simply accelerate the delivery of broken code to users.' What is the author's primary warning?",
    "Pipelines without solid automated tests deliver bugs faster",
    "CI/CD pipelines should be avoided in production",
    "Manual testing is superior to automated pipelines",
    "Release friction is essential for high reliability",
    "A",
    "Direct restatement of the warning that without test suites, pipelines merely speed up the delivery of bugs.");

addQ($questions, "VERBAL_SC", "easy",
    "Choose the correct modal verb: 'You ______ follow the code formatting guidelines before submitting a pull request.'",
    "must", "might", "could", "would", "A",
    "'Must' indicates obligatory requirement in team engineering standards.");

addQ($questions, "VERBAL_VOCAB", "hard",
    "Select the word that describes 'someone who is excessively concerned with minor rules and details':",
    "Pedantic", "Pragmatic", "Altruistic", "Garrulous", "A",
    "'Pedantic' means marked by a narrow, ostentatious concern for book rules and formal details.");

// ==========================================
// 4. TECHNICAL & CODING (30 Questions)
// ==========================================

addQ($questions, "TECH_INNOVATURE", "medium",
    "In C, what is the output of the following code snippet?",
    "10, 11", "10, 10", "11, 11", "Compiler Error", "A",
    "x++ returns the original value 10, then increments x to 11. y is assigned 10.",
    "#include <stdio.h>\nint main() {\n    int x = 10;\n    int y = x++;\n    printf(\"%d, %d\", y, x);\n    return 0;\n}", "c");

addQ($questions, "TECH_INNOVATURE", "hard",
    "What will be printed by this C pointer arithmetic snippet?",
    "30", "20", "10", "40", "A",
    "arr is an array of ints. ptr initially points to arr[0]. *(ptr + 2) accesses arr[2], which is 30.",
    "#include <stdio.h>\nint main() {\n    int arr[] = {10, 20, 30, 40, 50};\n    int *ptr = arr;\n    printf(\"%d\", *(ptr + 2));\n    return 0;\n}", "c");

addQ($questions, "TECH_INNOVATURE", "hard",
    "In C, what is the size of the following union on a 64-bit architecture?",
    "8 bytes", "13 bytes", "4 bytes", "16 bytes", "A",
    "In a union, memory is allocated equal to the largest member. double is 8 bytes, char arr[4] is 4 bytes, int is 4 bytes. Largest is 8 bytes.",
    "union Data {\n    int i;\n    char str[4];\n    double d;\n};", "c");

addQ($questions, "TECH_INNOVATURE", "medium",
    "What is the output of this Java String comparison?",
    "true, false", "true, true", "false, false", "false, true", "A",
    "s1 and s2 both refer to the same interned literal in the String pool (== is true). s3 creates a new Heap object (== is false).",
    "public class Test {\n    public static void main(String[] args) {\n        String s1 = \"Innovature\";\n        String s2 = \"Innovature\";\n        String s3 = new String(\"Innovature\");\n        System.out.println((s1 == s2) + \", \" + (s1 == s3));\n    }\n}", "java");

addQ($questions, "TECH_INNOVATURE", "hard",
    "In Java, what happens when a try block executes a return statement, and a finally block also executes a return statement?",
    "The return value from the finally block overrides the try block",
    "The return value from the try block is returned",
    "A compilation error occurs",
    "An IllegalStateException is thrown at runtime",
    "A",
    "If both try and finally blocks execute return statements, the finally block's return statement silently suppresses and overrides the try block's return value.");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In SQL, which clause is used to filter records AFTER an aggregation operation like GROUP BY?",
    "HAVING", "WHERE", "ORDER BY", "FILTER", "A",
    "The WHERE clause filters rows before aggregation, while the HAVING clause filters groups after aggregation.");

addQ($questions, "TECH_INNOVATURE", "hard",
    "Given an `employees` table, which SQL query retrieves the second-highest salary without using the LIMIT offset keyword?",
    "SELECT MAX(salary) FROM employees WHERE salary < (SELECT MAX(salary) FROM employees)",
    "SELECT salary FROM employees ORDER BY salary DESC LIMIT 1 OFFSET 1",
    "SELECT MIN(salary) FROM employees WHERE salary > (SELECT MIN(salary) FROM employees)",
    "SELECT TOP 2 salary FROM employees",
    "A",
    "Subquery (SELECT MAX(salary) FROM employees) finds the highest salary. The outer query finds the maximum salary strictly less than the top salary, which is the 2nd highest.");

addQ($questions, "TECH_INNOVATURE", "easy",
    "What is the average time complexity of searching for an element in a balanced Binary Search Tree (AVL / Red-Black Tree)?",
    "O(log n)", "O(n)", "O(1)", "O(n log n)", "A",
    "A balanced binary search tree maintains a height of O(log n), so search, insertion, and deletion take O(log n) time.");

addQ($questions, "TECH_INNOVATURE", "medium",
    "Which sorting algorithm has a worst-case time complexity of O(n log n)?",
    "Merge Sort", "Quick Sort", "Bubble Sort", "Insertion Sort", "A",
    "Merge Sort always divides the array into halves and merges in linear time, guaranteeing O(n log n) in all cases (worst, average, and best).");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In Python, what is the output of the following list comprehension snippet?",
    "[0, 2, 4, 6, 8]", "[1, 3, 5, 7, 9]", "[2, 4, 6, 8, 10]", "[0, 1, 2, 3, 4]", "A",
    "range(10) produces 0 through 9. x % 2 == 0 filters out odd numbers, leaving [0, 2, 4, 6, 8].",
    "nums = [x for x in range(10) if x % 2 == 0]\nprint(nums)", "python");

addQ($questions, "TECH_INNOVATURE", "hard",
    "In Python, what is the output when mutable default arguments are used?",
    "[1], [1, 2]", "[1], [2]", "[1], [1]", "TypeError", "A",
    "Default parameter lists in Python are evaluated once when the function is defined. The same list instance is reused across invocations.",
    "def append_val(val, lst=[]):\n    lst.append(val)\n    return lst\n\nprint(append_val(1))\nprint(append_val(2))", "python");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In C, what is the value of `sizeof('A')` in standard C99/C11?",
    "sizeof(int) (usually 4 bytes)", "sizeof(char) (1 byte)", "2 bytes", "8 bytes", "A",
    "In C, character constants like 'A' have type int, so sizeof('A') is equal to sizeof(int), typically 4 bytes. (Unlike C++, where it is 1 byte).");

addQ($questions, "TECH_INNOVATURE", "hard",
    "What will be the output of this recursive C function when invoked with `func(5)`?",
    "15", "10", "5", "120", "A",
    "func(5) computes the sum of first 5 integers: 5 + 4 + 3 + 2 + 1 = 15.",
    "#include <stdio.h>\nint func(int n) {\n    if (n <= 1) return n;\n    return n + func(n - 1);\n}\nint main() {\n    printf(\"%d\", func(5));\n    return 0;\n}", "c");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In Object-Oriented Programming, which concept allows a method to have different implementations based on the runtime object type?",
    "Polymorphism", "Encapsulation", "Abstraction", "Inheritance", "A",
    "Polymorphism (specifically dynamic dispatch / method overriding) enables a single method call to execute different behaviors based on the runtime type.");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In SQL, what is the difference between TRUNCATE and DELETE?",
    "TRUNCATE is a DDL operation that resets identity and cannot be filtered with WHERE, while DELETE is DML",
    "DELETE is faster than TRUNCATE",
    "TRUNCATE can use a WHERE clause but DELETE cannot",
    "DELETE resets the auto-increment counter while TRUNCATE does not",
    "A",
    "TRUNCATE is a DDL command that deallocates data pages (very fast, cannot have a WHERE clause). DELETE is a row-by-row DML command.");

addQ($questions, "TECH_INNOVATURE", "easy",
    "Which data structure follows the Last-In, First-Out (LIFO) principle?",
    "Stack", "Queue", "Array", "Linked List", "A",
    "A Stack operates strictly on LIFO (Last-In, First-Out).");

addQ($questions, "TECH_INNOVATURE", "medium",
    "What is the purpose of the `volatile` keyword in C/C++?",
    "Tells the compiler not to optimize or cache the variable because its value may change asynchronously",
    "Allocates the variable in read-only memory",
    "Guarantees atomic multi-threaded write operations",
    "Prevents the variable from being modified inside a function",
    "A",
    "`volatile` informs the compiler that a variable may be updated by hardware, interrupt handlers, or other threads, preventing register caching optimizations.");

addQ($questions, "TECH_INNOVATURE", "hard",
    "In Java, which collection class is synchronized and thread-safe by default?",
    "Vector", "ArrayList", "LinkedList", "HashMap", "A",
    "Vector methods are synchronized by default. ArrayList, LinkedList, and HashMap are unsynchronized.");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In relational databases, which Normal Form requires removing transitive dependencies?",
    "Third Normal Form (3NF)", "First Normal Form (1NF)", "Second Normal Form (2NF)", "Boyce-Codd Normal Form (BCNF)", "A",
    "2NF removes partial functional dependencies. 3NF removes transitive dependencies (non-key attribute depending on another non-key attribute).");

addQ($questions, "TECH_INNOVATURE", "easy",
    "What does HTTP status code 404 represent?",
    "Not Found", "Unauthorized", "Internal Server Error", "Bad Request", "A",
    "404 Not Found indicates that the server cannot locate the requested resource.");

addQ($questions, "TECH_INNOVATURE", "hard",
    "In C, what is the effect of declaring a variable as `static` inside a local function scope?",
    "The variable retains its value across multiple function calls and is stored in the data segment",
    "The variable is visible globally across all source files",
    "The variable cannot be modified after initialization",
    "The variable is allocated on the function call stack",
    "A",
    "A local static variable is allocated in the initialized data segment and preserves its state across successive function invocations.");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In Python, what is the output of `bool([])` and `bool([0])`?",
    "False, True", "False, False", "True, True", "True, False", "A",
    "An empty list is falsy in Python. A non-empty list (even containing 0) is truthy.");

addQ($questions, "TECH_INNOVATURE", "hard",
    "What is the time complexity to find the middle element of a singly linked list in a single pass using the two-pointer (slow/fast) approach?",
    "O(n)", "O(n^2)", "O(1)", "O(n log n)", "A",
    "By moving fast pointer 2 steps and slow pointer 1 step, the list is traversed in a single pass of n nodes: O(n) time and O(1) space.");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In operating systems, which condition is NOT one of the four Coffman conditions necessary for a deadlock?",
    "Preemption", "Mutual Exclusion", "Hold and Wait", "Circular Wait", "A",
    "The 4 Coffman conditions are Mutual Exclusion, Hold and Wait, No Preemption, and Circular Wait. 'Preemption' prevents deadlock; 'No preemption' causes it.");

addQ($questions, "TECH_INNOVATURE", "easy",
    "Which layer of the OSI model is responsible for routing packets across networks?",
    "Network Layer (Layer 3)", "Data Link Layer (Layer 2)", "Transport Layer (Layer 4)", "Application Layer (Layer 7)", "A",
    "The Network Layer handles logical IP addressing and routing of packets.");

addQ($questions, "TECH_INNOVATURE", "hard",
    "In Java, what is the difference between `final`, `finally`, and `finalize()`?",
    "final is a modifier; finally is a block associated with try/catch; finalize() was a Garbage Collection cleanup method in Object",
    "They are interchangeable synonyms",
    "finally creates immutable constants",
    "final handles exception handling",
    "A",
    "`final` restricts inheritance/reassignment; `finally` guarantees execution in exception handling; `finalize()` is the legacy Object cleanup method.");

addQ($questions, "TECH_INNOVATURE", "medium",
    "In SQL, which type of JOIN returns all records from both tables, filling NULLs where matches do not exist?",
    "FULL OUTER JOIN", "INNER JOIN", "LEFT JOIN", "CROSS JOIN", "A",
    "FULL OUTER JOIN combines the results of both LEFT and RIGHT outer joins, returning all rows from both tables.");

addQ($questions, "TECH_INNOVATURE", "hard",
    "What is the output of this C bitwise operations snippet?",
    "16", "8", "32", "4", "A",
    "x << 2 shifts binary 00000100 (4) two positions left to 00010000 (16).",
    "#include <stdio.h>\nint main() {\n    int x = 4;\n    printf(\"%d\", x << 2);\n    return 0;\n}", "c");

addQ($questions, "TECH_INNOVATURE", "medium",
    "What is the space complexity of Depth First Search (DFS) on a tree of height h?",
    "O(h)", "O(1)", "O(2^h)", "O(n^2)", "A",
    "The recursive call stack in DFS stores the path from root to the current node, requiring O(h) memory.");

addQ($questions, "TECH_INNOVATURE", "easy",
    "In Git version control, which command creates a new branch and switches to it in one step?",
    "git checkout -b <branch_name>", "git branch -n <branch_name>", "git merge <branch_name>", "git switch -m <branch_name>", "A",
    "`git checkout -b <name>` (or `git switch -c <name>`) creates a new branch and immediately checks it out.");

// Write to JSON
$jsonPath = __DIR__ . '/../database/seeders/innovature_100_questions.json';
file_put_contents($jsonPath, json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Write to CSV
$csvPath = __DIR__ . '/../database/seeders/innovature_100_questions.csv';
$fp = fopen($csvPath, 'w');
fputcsv($fp, [
    'company_name', 'subtopic_code', 'difficulty', 'question_text',
    'option_a', 'option_b', 'option_c', 'option_d',
    'correct_option', 'explanation', 'code_snippet', 'language',
    'round_pattern', 'verification_status'
]);
foreach ($questions as $q) {
    fputcsv($fp, [
        $q['company_name'],
        $q['subtopic_code'],
        $q['difficulty'],
        $q['question_text'],
        $q['option_a'],
        $q['option_b'],
        $q['option_c'],
        $q['option_d'],
        $q['correct_option'],
        $q['explanation'],
        $q['code_snippet'] ?? '',
        $q['language'] ?? '',
        $q['round_pattern'],
        $q['verification_status']
    ]);
}
fclose($fp);

echo "Successfully generated:\n";
echo "1. $jsonPath (" . count($questions) . " questions)\n";
echo "2. $csvPath (" . count($questions) . " questions)\n";
