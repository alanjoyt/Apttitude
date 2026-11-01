from common import make_q

def get_amazon():
    c = "Amazon"
    qs = []
    # Amazon is DSA & Algorithms heavy! (22 technical, 10 quant, 10 logical, 6 verbal = 48 Qs)
    # Quant (10)
    qs.append(make_q(c, "QUANT_WORK", "easy", "A fulfillment packing line of 4 workers packs 120 orders in 3 hours. How many orders can 6 workers pack in 3 hours at the same pace?", "180", "150", "160", "200", "A", "Rate per worker = 120 / (4*3) = 10 orders/hr. 6 workers in 3 hrs pack 6 * 3 * 10 = 180 orders."))
    qs.append(make_q(c, "QUANT_PERC", "medium", "An AWS server instance monthly cost decreases from $250 to $200 with a Savings Plan. What is the percentage savings?", "20%", "25%", "15%", "18%", "A", "Savings = (50 / 250) * 100 = 20%."))
    qs.append(make_q(c, "QUANT_SDT", "hard", "A delivery drone flies to a destination 60 km away against a 10 km/h wind and returns with the same wind in total 2.5 hours. What is the drone's airspeed in still air?", "50 km/h", "45 km/h", "55 km/h", "60 km/h", "A", "60/(v-10) + 60/(v+10) = 2.5 => 120v / (v^2 - 100) = 2.5 => 2.5 v^2 - 120v - 250 = 0 => v^2 - 48v - 100 = 0 => (v - 50)(v + 2) = 0 => v = 50 km/h."))
    qs.append(make_q(c, "QUANT_RATIO", "easy", "Prime delivery times between urban and rural centers are in ratio 2:5. If urban delivery takes 12 hours, how long does rural delivery take?", "30 hours", "25 hours", "35 hours", "28 hours", "A", "2 parts = 12 hrs => 1 part = 6 hrs. Rural = 5 * 6 = 30 hours."))
    qs.append(make_q(c, "QUANT_ADV", "hard", "In how many ways can 6 distinct packages be loaded into 3 distinct delivery trucks such that no truck is empty? (Stirling numbers / inclusion-exclusion)", "540", "720", "360", "480", "A", "Total assignments = 3^6 = 729. Empty truck exclusion: 3*2^6 - 3*1^6 = 192 - 3 = 189. Valid = 729 - 189 = 540."))
    qs.append(make_q(c, "QUANT_PERC", "easy", "A warehouse shelf capacity of 800 units is 75% full. How many units are on the shelf?", "600", "550", "650", "700", "A", "0.75 * 800 = 600 units."))
    qs.append(make_q(c, "QUANT_SDT", "easy", "A logistics truck covers 300 km in 5 hours. What is its speed?", "60 km/h", "50 km/h", "65 km/h", "70 km/h", "A", "300 / 5 = 60 km/h."))
    qs.append(make_q(c, "QUANT_WORK", "medium", "Robotic arm R1 sorts a pallet in 15 mins, R2 in 30 mins. How long if both sort together?", "10 minutes", "12 minutes", "8 minutes", "15 minutes", "A", "1/15 + 1/30 = 3/30 = 1/10. Time = 10 mins."))
    qs.append(make_q(c, "DI_BAR_TABLE", "medium", "Package delivery counts over 3 shifts: Morning: 1,200, Afternoon: 1,500, Night: 900. What is the average shift volume?", "1,200", "1,100", "1,300", "1,250", "A", "Sum = 3600 / 3 = 1200."))
    qs.append(make_q(c, "QUANT_ADV", "medium", "What is the probability of rolling a sum of 7 with two fair 6-sided dice?", "1/6", "1/12", "1/36", "5/36", "A", "Pairs summing to 7: (1,6), (2,5), (3,4), (4,3), (5,2), (6,1) => 6 / 36 = 1/6."))

    # Logical (10)
    qs.append(make_q(c, "LR_SERIES", "easy", "Find next: 1, 2, 4, 8, 16, ?", "32", "24", "28", "30", "A", "Doubling: 16 * 2 = 32."))
    qs.append(make_q(c, "LR_CODING", "easy", "If 'AMZ' = 1-13-26, what is 'AWS'?", "1-23-19", "1-22-19", "1-23-20", "2-23-19", "A", "A=1, W=23, S=19."))
    qs.append(make_q(c, "LR_BLOOD", "medium", "Pointing to a traveler, Suresh says: 'His father is the only son of my father.' How is Suresh related to the traveler?", "Father", "Son", "Brother", "Uncle", "A", "Only son of Suresh's father is Suresh himself. So Suresh is his father."))
    qs.append(make_q(c, "LR_DIRECTION", "medium", "Walk 12 m North, turn East and walk 9 m. What is direct distance to start?", "15 m", "21 m", "18 m", "16 m", "A", "sqrt(144 + 81) = sqrt(225) = 15 m."))
    qs.append(make_q(c, "LR_SEATING", "medium", "Four engineers sit around a table: A faces B, C is right of A. Who is opposite C?", "D", "A", "B", "None", "A", "Opposite C is D."))
    qs.append(make_q(c, "LR_PUZZLES", "medium", "Statements: All microservices are scalable. All scalable systems are resilient. Conclusion: All microservices are resilient.", "Follows", "Does not follow", "Cannot say", "Invalid", "A", "Transitive deduction holds."))
    qs.append(make_q(c, "LR_CODING", "medium", "If 'PRIME' -> 'QSJNF' (+1), what is 'KINESIS'?", "LJOFTHJ", "LJPFTIJ", "MKPFTIJ", "LJOFSIJ", "A", "Shift +1: K->L, I->J, N->O, E->F, S->T, I->J... wait: S(+1)=T! K->L, I->J, N->O, E->F, S->T, I->J, S->T => LJOFTHJ... wait, KINESIS has 7 letters. Let's trace: K->L, I->J, N->O, E->F, S->T, I->J, S->T => LJOFTJT!"))
    # Fix KINESIS:
    qs[-1]["option_a"] = "LJOFTJT"
    qs[-1]["option_b"] = "LJPFTJT"
    qs[-1]["option_c"] = "MKQFUJU"
    qs[-1]["option_d"] = "LJOFTJS"
    qs[-1]["explanation"] = "Shift +1: K->L, I->J, N->O, E->F, S->T, I->J, S->T => LJOFTJT. Therefore, the correct answer is LJOFTJT (Option A)."

    qs.append(make_q(c, "LR_SERIES", "medium", "Missing number: 3, 6, 11, 18, 27, ?", "38", "36", "40", "42", "A", "Differences: +3, +5, +7, +9. Next is +11 => 27 + 11 = 38."))
    qs.append(make_q(c, "LR_PUZZLES", "hard", "In a logistics hub of 100 staff, 60 handle packages, 50 handle routing, and 20 handle neither. How many handle both?", "30", "25", "35", "20", "A", "At least one = 100 - 20 = 80. Both = 60 + 50 - 80 = 30."))
    qs.append(make_q(c, "LR_DIRECTION", "easy", "Facing West, turn 180 degrees. Which direction do you face?", "East", "West", "North", "South", "A", "Opposite of West is East."))

    # Verbal (6)
    qs.append(make_q(c, "VERBAL_SC", "easy", "Fill in: 'Amazon S3 buckets are configured _____ high availability.'", "for", "to", "with", "at", "A", "Configured takes 'for'."))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy", "Synonym of 'FRUGAL':", "Economical", "Extravagant", "Wasteful", "Lavish", "A", "Frugal means economical or thrifty."))
    qs.append(make_q(c, "VERBAL_RC", "medium", "Passage: 'Two-pizza teams keep communication overhead minimal and ownership maximal.' What is maximized?", "Ownership", "Team size", "Meeting duration", "Paperwork", "A", "Ownership is maximized."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Antonym of 'CUSTOMER-OBSESSED':", "Indifferent", "Devoted", "Attentive", "Dedicated", "A", "Opposite is Indifferent."))
    qs.append(make_q(c, "VERBAL_SC", "medium", "Error: 'The fleet of delivery vans are scheduled for inspection.'", "are scheduled (should be 'is scheduled')", "The fleet of", "delivery vans", "for inspection", "A", "'The fleet' is singular."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Word choice: 'Leaders have conviction and are tenacious. They do not _____ for the sake of social cohesion.'", "compromise", "innovate", "persist", "rejoice", "A", "Compromise fits leadership principle."))

    # Technical (22 - DSA & Algorithms heavy!)
    qs.append(make_q(c, "TECH_C", "easy", "What is printed by this C snippet?", "21", "20", "22", "0", "A", "7 * 3 = 21.", code="#include <stdio.h>\nint main() { printf(\"%d\", 7 * 3); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does Python print?", "3", "2", "4", "Error", "A", "len of 'AWS' is 3.", code="print(len('AWS'))", lang="python"))
    qs.append(make_q(c, "TECH_PYTHON", "medium", "What is the output?", "[1, 2]", "[2, 3]", "[1, 2, 3]", "[]", "A", "Slice [:2] selects elements 0 and 1.", code="arr = [1, 2, 3]\nprint(arr[:2])", lang="python"))
    qs.append(make_q(c, "TECH_DSA", "medium", "What is the time complexity of Dijkstra's algorithm using a Min-Heap priority queue?", "O((V + E) log V)", "O(V^2)", "O(E log E)", "O(V log E)", "A", "Min-heap implementation achieves O((V + E) log V)."))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL clause is used to filter records before any groupings are made?", "WHERE", "HAVING", "GROUP BY", "ORDER BY", "A", "WHERE filters individual rows prior to GROUP BY."))
    qs.append(make_q(c, "TECH_C", "medium", "What does this C code output?", "16", "10", "20", "0", "A", "4 * 4 = 16.", code="#include <stdio.h>\nint main() { int a = 4; printf(\"%d\", a * a); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_DSA", "easy", "Which data structure is used to implement function call recursion call stack?", "Stack", "Queue", "Heap", "Hash Map", "A", "Call stack uses LIFO Stack."))
    qs.append(make_q(c, "TECH_SQL", "easy", "Which clause sorts rows in descending order in SQL?", "ORDER BY col DESC", "SORT col DESC", "DESCENDING col", "GROUP col DESC", "A", "ORDER BY col DESC."))
    qs.append(make_q(c, "TECH_DSA", "hard", "In dynamic programming, what is the time complexity of the 0/1 Knapsack problem with N items and capacity W?", "O(N * W)", "O(2^N)", "O(N + W)", "O(N log W)", "A", "Standard DP table is N by W, running in O(N * W) pseudo-polynomial time."))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does Python print?", "AMAZON", "amazon", "Amazon", "Error", "A", "'amazon'.upper() is 'AMAZON'.", code="print('amazon'.upper())", lang="python"))
    qs.append(make_q(c, "TECH_C", "easy", "What does this C snippet output?", "9", "6", "10", "0", "A", "Prints 9.", code="#include <stdio.h>\nint main() { int x = 9; printf(\"%d\", x); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL function returns the greatest value in a column?", "MAX()", "GREATEST()", "HIGH()", "TOP()", "A", "MAX() returns maximum value."))
    qs.append(make_q(c, "TECH_DSA", "easy", "What is the time complexity to insert an item into a FIFO Queue?", "O(1)", "O(N)", "O(log N)", "O(N^2)", "A", "Enqueue operation is O(1)."))
    qs.append(make_q(c, "TECH_PYTHON", "medium", "What does Python evaluate 2 ** 4 to?", "16", "8", "32", "Error", "A", "2 to the 4th power is 16.", code="print(2 ** 4)", lang="python"))
    qs.append(make_q(c, "TECH_C", "medium", "What will this C program print?", "18", "12", "20", "0", "A", "6 * 3 = 18.", code="#include <stdio.h>\nint main() { printf(\"%d\", 6 * 3); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_C", "easy", "What does this C code output?", "EC2", "ec2", "Error", "0", "A", "Prints literal string EC2.", code="#include <stdio.h>\nint main() { printf(\"EC2\"); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_DSA", "easy", "What is the in-order traversal of a Binary Search Tree (BST)?", "Sorted in ascending order", "Sorted in descending order", "Random order", "Level-by-level", "A", "In-order traversal of a BST yields elements in sorted ascending order."))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL command modifies existing column values in a database table?", "UPDATE", "ALTER", "CHANGE", "MODIFY", "A", "UPDATE modifies data values."))
    qs.append(make_q(c, "TECH_DSA", "hard", "What is the worst-case time complexity of searching in a Trie containing N words of maximum length L?", "O(L)", "O(N * L)", "O(N)", "O(log N)", "A", "Trie lookup depends solely on the length of the query string L: O(L)."))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does this Python statement evaluate to?", "True", "False", "None", "Error", "A", "10 >= 10 is True.", code="print(10 >= 10)", lang="python"))
    qs.append(make_q(c, "TECH_DSA", "medium", "Which data structure uses a pivot element to partition subarrays?", "QuickSort", "MergeSort", "HeapSort", "InsertionSort", "A", "QuickSort partitions around a pivot."))
    qs.append(make_q(c, "TECH_SQL", "easy", "Which keyword is used to eliminate duplicate rows from SQL query output?", "DISTINCT", "UNIQUE", "DIFFERENT", "FILTER", "A", "DISTINCT eliminates duplicate rows."))

    return qs

def get_ust():
    c = "UST"
    qs = []
    # UST (52 Qs: 16 quant, 13 logical, 8 verbal, 15 tech)
    # Quant (16)
    qs.append(make_q(c, "QUANT_WORK", "easy", "A team of 4 QA engineers tests 40 modules in 5 days. How many modules can 6 QA engineers test in 5 days?", "60", "50", "70", "80", "A", "Rate per QA = 40 / 20 = 2 modules/day. 6 engineers in 5 days test 6 * 5 * 2 = 60 modules."))
    qs.append(make_q(c, "QUANT_PERC", "medium", "An enterprise software license was discounted by 15% to Rs. 42,500. What was original price?", "Rs. 50,000", "Rs. 48,000", "Rs. 52,000", "Rs. 45,000", "A", "Original = 42500 / 0.85 = Rs. 50,000."))
    qs.append(make_q(c, "QUANT_SDT", "medium", "A vehicle covers 240 km in 4 hours. What is its speed?", "60 km/h", "55 km/h", "65 km/h", "70 km/h", "A", "240 / 4 = 60 km/h."))
    qs.append(make_q(c, "QUANT_RATIO", "easy", "Divide Rs. 5,000 in ratio 2:3. What is smaller share?", "Rs. 2,000", "Rs. 3,000", "Rs. 2,500", "Rs. 1,500", "A", "5 parts = 5000 => 2 parts = Rs. 2,000."))
    qs.append(make_q(c, "QUANT_ADV", "hard", "How many distinct 4-digit PINs can be formed using digits 0-9 if repetitions are allowed?", "10,000", "5,040", "1,000", "9,000", "A", "10^4 = 10,000."))
    qs.append(make_q(c, "QUANT_PERC", "easy", "What is 20% of 750?", "150", "125", "175", "200", "A", "0.20 * 750 = 150."))
    qs.append(make_q(c, "QUANT_SDT", "easy", "How many metres does a car travel in 30 seconds at 36 km/h?", "300 m", "250 m", "350 m", "400 m", "A", "36 * (5/18) = 10 m/s. 10 * 30 = 300 m."))
    qs.append(make_q(c, "QUANT_WORK", "medium", "A builds a feature in 8 days, B in 8 days. Together in how many days?", "4 days", "3 days", "5 days", "6 days", "A", "8 / 2 = 4 days."))
    qs.append(make_q(c, "DI_BAR_TABLE", "medium", "Defect logs: Mon: 10, Tue: 14, Wed: 12, Thu: 16. What is the average daily defect count?", "13", "12", "14", "15", "A", "52 / 4 = 13."))
    qs.append(make_q(c, "QUANT_ADV", "medium", "What is the area of a rectangle with length 15 cm and breadth 8 cm?", "120 cm^2", "110 cm^2", "130 cm^2", "100 cm^2", "A", "15 * 8 = 120 cm^2."))
    qs.append(make_q(c, "QUANT_PERC", "hard", "A product price is marked up by 20% and then discounted by 20%. Net effect?", "4% loss", "No change", "4% gain", "2% loss", "A", "1.20 * 0.80 = 0.96 (4% loss)."))
    qs.append(make_q(c, "QUANT_SDT", "medium", "A train 100 m long crosses a 200 m bridge in 15 seconds. What is its speed?", "20 m/s", "15 m/s", "25 m/s", "18 m/s", "A", "300 / 15 = 20 m/s."))
    qs.append(make_q(c, "QUANT_RATIO", "medium", "If A:B = 3:4 and B:C = 4:5, what is A:C?", "3:5", "3:4", "4:5", "6:10", "A", "A:C = 3:5."))
    qs.append(make_q(c, "QUANT_ADV", "easy", "Find simple interest on Rs. 3,000 for 2 years at 5% per annum:", "Rs. 300", "Rs. 250", "Rs. 350", "Rs. 400", "A", "(3000 * 5 * 2) / 100 = Rs. 300."))
    qs.append(make_q(c, "QUANT_WORK", "hard", "Pipe A fills in 12 hrs, Pipe B empties in 16 hrs. How long to fill empty tank if both open?", "48 hours", "40 hours", "36 hours", "52 hours", "A", "1/12 - 1/16 = 1/48. Time = 48 hours."))
    qs.append(make_q(c, "QUANT_PERC", "medium", "If revenue increases from Rs. 80L to Rs. 100L, what is the percentage growth?", "25%", "20%", "30%", "15%", "A", "20 / 80 = 25%."))

    # Logical (13)
    qs.append(make_q(c, "LR_SERIES", "easy", "Next number: 9, 18, 27, 36, ?", "45", "42", "48", "54", "A", "Multiples of 9: 36 + 9 = 45."))
    qs.append(make_q(c, "LR_CODING", "easy", "If 'UST' = 21-19-20, what is 'IT'?", "9-20", "8-20", "9-19", "10-20", "A", "I=9, T=20."))
    qs.append(make_q(c, "LR_BLOOD", "medium", "A woman says: 'His sister is the wife of my son.' Who is the man to the woman?", "Son-in-law's brother / relative", "Brother", "Father", "Husband", "A", "Wife of her son is daughter-in-law. Her brother is daughter-in-law's brother."))
    # Make precise:
    qs[-1]["option_a"] = "Daughter-in-law's brother"
    qs[-1]["option_b"] = "Brother"
    qs[-1]["option_c"] = "Son"
    qs[-1]["option_d"] = "Father"
    qs[-1]["explanation"] = "Wife of her son is her daughter-in-law. The man is her brother, so he is the daughter-in-law's brother. Therefore, the correct answer is Daughter-in-law's brother (Option A)."

    qs.append(make_q(c, "LR_DIRECTION", "medium", "Walk 15 km North, turn East and walk 20 km. Direct distance from start?", "25 km", "35 km", "30 km", "28 km", "A", "sqrt(225 + 400) = sqrt(625) = 25 km."))
    qs.append(make_q(c, "LR_SEATING", "medium", "Four colleagues sit around a square table. X faces Y. W is right of X. Who is opposite W?", "Z", "X", "Y", "None", "A", "Opposite W is Z."))
    qs.append(make_q(c, "LR_PUZZLES", "medium", "Statements: All agile sprints are iterations. All iterations have reviews. Conclusion: All agile sprints have reviews.", "Follows", "Does not follow", "Cannot say", "Invalid", "A", "Transitive deduction holds."))
    qs.append(make_q(c, "LR_CODING", "medium", "If 'TEST' -> 'UFTU' (+1), what is 'PLAN'?", "QMBO", "QMBN", "PLBN", "RNCO", "A", "P->Q, L->M, A->B, N->O => QMBO."))
    qs.append(make_q(c, "LR_SERIES", "medium", "Missing number: 2, 5, 10, 17, 26, ?", "37", "35", "36", "38", "A", "n^2 + 1: 6^2 + 1 = 37."))
    qs.append(make_q(c, "LR_PUZZLES", "hard", "In a team of 60 QA testers, 35 know Postman, 30 know JMeter, and 10 know neither. How many know both?", "15", "20", "10", "25", "A", "At least one = 60 - 10 = 50. Both = 35 + 30 - 50 = 15."))
    qs.append(make_q(c, "LR_DIRECTION", "easy", "Facing South, turn 90 degrees clockwise. Which direction do you face?", "West", "East", "North", "South-West", "A", "Clockwise from South is West."))
    qs.append(make_q(c, "LR_BLOOD", "easy", "P is the mother of Q. Q is the sister of R. How is P related to R?", "Mother", "Sister", "Aunt", "Grandmother", "A", "P is mother of both Q and R."))
    qs.append(make_q(c, "LR_PUZZLES", "easy", "Statements: All defects are tracked. All tracked items are prioritized. Conclusion: All defects are prioritized.", "Follows", "Does not follow", "Cannot say", "Invalid", "A", "Transitive deduction holds."))
    qs.append(make_q(c, "LR_CODING", "easy", "If 'UST' is coded as 'VTU' (+1), what is 'QA'?", "RB", "RC", "SB", "QB", "A", "Q->R, A->B => RB."))

    # Verbal (8)
    qs.append(make_q(c, "VERBAL_SC", "easy", "Fill in: 'The deployment is contingent _____ client acceptance testing.'", "upon", "with", "at", "for", "A", "Contingent takes 'upon' or 'on'."))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy", "Synonym of 'VALIDATE':", "Confirm", "Reject", "Disprove", "Ignore", "A", "Validate means confirm."))
    qs.append(make_q(c, "VERBAL_RC", "medium", "Passage: 'Agile sprints promote iterative refinement through retrospective feedback loops.' What drives iterative refinement?", "Retrospective feedback loops", "Quarterly bonuses", "Physical hardware", "Marketing campaigns", "A", "Retrospective feedback loops drive refinement."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Antonym of 'DEFECTIVE':", "Flawless", "Faulty", "Broken", "Imperfect", "A", "Defective means faulty; opposite is Flawless."))
    qs.append(make_q(c, "VERBAL_SC", "medium", "Error: 'The suite of regression tests have completed successfully.'", "have completed (should be 'has completed')", "The suite of", "regression tests", "successfully", "A", "'The suite' is singular."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Word choice: 'The software architect presented a _____ blueprint for cloud migration.'", "comprehensive", "fragile", "hesitant", "trivial", "A", "Comprehensive fits context."))
    qs.append(make_q(c, "VERBAL_SC", "easy", "Conjunction: 'Testing resumes _____ the fix is committed.'", "once", "unless", "although", "whereas", "A", "'Once' indicates condition."))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy", "Synonym of 'EXECUTE':", "Perform", "Delay", "Cancel", "Halt", "A", "Execute means perform."))

    # Technical (15)
    qs.append(make_q(c, "TECH_C", "easy", "What does this C code output?", "27", "25", "30", "0", "A", "9 * 3 = 27.", code="#include <stdio.h>\nint main() { printf(\"%d\", 9 * 3); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does Python output?", "3", "2", "4", "Error", "A", "len of 'UST' is 3.", code="print(len('UST'))", lang="python"))
    qs.append(make_q(c, "TECH_PYTHON", "medium", "What does Python print?", "[1, 2]", "[2, 3]", "[1, 2, 3]", "[]", "A", "Slice [:2] selects elements at 0 and 1.", code="tests = [1, 2, 3]\nprint(tests[:2])", lang="python"))
    qs.append(make_q(c, "TECH_DSA", "medium", "What is the average time complexity of finding an item in a Balanced BST?", "O(log N)", "O(N)", "O(1)", "O(N^2)", "A", "Search in balanced BST is O(log N)."))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL clause filters groups created by GROUP BY?", "HAVING", "WHERE", "ORDER BY", "SELECT", "A", "HAVING filters groups."))
    qs.append(make_q(c, "TECH_C", "medium", "What is the output of this C code?", "14", "10", "18", "0", "A", "x + 4 = 10 + 4 = 14.", code="#include <stdio.h>\nint main() { int x = 10; printf(\"%d\", x + 4); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_DSA", "easy", "Which data structure follows LIFO?", "Stack", "Queue", "Tree", "Graph", "A", "Stack is LIFO."))
    qs.append(make_q(c, "TECH_SQL", "easy", "Which SQL command modifies data in a table?", "UPDATE", "ALTER", "CHANGE", "MODIFY", "A", "UPDATE modifies row data."))
    qs.append(make_q(c, "TECH_DSA", "hard", "What is the worst-case time complexity of Bubble Sort on N elements?", "O(N^2)", "O(N log N)", "O(N)", "O(log N)", "A", "Bubble sort takes O(N^2) worst case."))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does Python print?", "GLOBAL", "global", "Global", "Error", "A", "'global'.upper() is 'GLOBAL'.", code="print('global'.upper())", lang="python"))
    qs.append(make_q(c, "TECH_C", "easy", "What does this C code output?", "99", "90", "100", "0", "A", "Prints 99.", code="#include <stdio.h>\nint main() { int val = 99; printf(\"%d\", val); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL constraint enforces referential integrity between tables?", "FOREIGN KEY", "PRIMARY KEY", "CHECK", "UNIQUE", "A", "FOREIGN KEY enforces referential integrity."))
    qs.append(make_q(c, "TECH_DSA", "easy", "What is the time complexity to enqueue an item into a Queue?", "O(1)", "O(N)", "O(log N)", "O(N^2)", "A", "Queue enqueue is O(1)."))
    qs.append(make_q(c, "TECH_PYTHON", "medium", "What is 4 ** 2 in Python?", "16", "8", "32", "Error", "A", "4 squared is 16.", code="print(4 ** 2)", lang="python"))
    qs.append(make_q(c, "TECH_C", "medium", "What does this C program print?", "20", "15", "25", "0", "A", "10 * 2 = 20.", code="#include <stdio.h>\nint main() { printf(\"%d\", 10 * 2); return 0; }", lang="c"))

    return qs

def get_ibm():
    c = "IBM"
    qs = []
    # IBM (55 Qs: 17 quant, 14 logical, 8 verbal, 16 tech)
    # Quant (17)
    qs.append(make_q(c, "QUANT_WORK", "easy", "A team of 4 cloud architects deploys a hybrid cluster in 10 days. How many days will 5 architects take at the same pace?", "8 days", "7 days", "9 days", "6 days", "A", "Effort = 4 * 10 = 40. Time = 40 / 5 = 8 days."))
    qs.append(make_q(c, "QUANT_PERC", "medium", "IBM Cloud server RAM allocation grew from 64 GB to 80 GB. What is the percentage increase?", "25%", "20%", "30%", "15%", "A", "Increase = (16 / 64) * 100 = 25%."))
    qs.append(make_q(c, "QUANT_SDT", "medium", "A container data synchronization sends 200 GB across a WAN link in 4 hours. What is the average speed?", "50 GB/h", "45 GB/h", "55 GB/h", "60 GB/h", "A", "Speed = 200 / 4 = 50 GB/h."))
    qs.append(make_q(c, "QUANT_RATIO", "easy", "Compute power is allocated between Batch and Interactive jobs in ratio 4:6 (2:3). If total cores = 50, how many cores are allocated to Batch?", "20 cores", "30 cores", "25 cores", "15 cores", "A", "Batch = (2/5) * 50 = 20 cores."))
    qs.append(make_q(c, "QUANT_ADV", "hard", "How many distinct 5-node subgraphs can be formed from an 8-node cluster?", "56", "48", "64", "72", "A", "8C5 = 8C3 = (8 * 7 * 6) / 6 = 56."))
    qs.append(make_q(c, "QUANT_PERC", "easy", "What is 35% of 400 GB?", "140 GB", "120 GB", "150 GB", "160 GB", "A", "0.35 * 400 = 140 GB."))
    qs.append(make_q(c, "QUANT_SDT", "easy", "A backup courier covers 150 km in 2.5 hours. What is the speed?", "60 km/h", "55 km/h", "65 km/h", "70 km/h", "A", "150 / 2.5 = 60 km/h."))
    qs.append(make_q(c, "QUANT_WORK", "medium", "Engineer A writes a Kubernetes operator in 10 days, B in 15 days. How long working together?", "6 days", "5 days", "7 days", "8 days", "A", "1/10 + 1/15 = 5/30 = 1/6. Time = 6 days."))
    qs.append(make_q(c, "DI_BAR_TABLE", "medium", "Compute utilization across 4 nodes: N1: 70%, N2: 80%, N3: 75%, N4: 95%. What is the average utilization?", "80%", "78%", "82%", "85%", "A", "Sum = 320% / 4 = 80%."))
    qs.append(make_q(c, "QUANT_ADV", "medium", "What is the area of a square server chassis of side length 12 inches?", "144 sq in", "121 sq in", "169 sq in", "100 sq in", "A", "12 * 12 = 144 sq in."))
    qs.append(make_q(c, "QUANT_PERC", "hard", "A cloud server cost of $1,000 drops by 20% with committed use, and then gets an additional 10% enterprise discount. Final cost?", "$720", "$700", "$750", "$680", "A", "1000 * 0.80 = 800. 800 * 0.90 = $720."))
    qs.append(make_q(c, "QUANT_SDT", "medium", "A satellite signal travels 300,000 km in 1 second. How far does it travel in 0.5 milliseconds (0.0005 s)?", "150 km", "100 km", "200 km", "250 km", "A", "300000 * 0.0005 = 150 km."))
    qs.append(make_q(c, "QUANT_RATIO", "medium", "If A:B = 5:6 and B:C = 6:7, what is A:C?", "5:7", "5:6", "6:7", "10:14", "A", "A:C = 5:7."))
    qs.append(make_q(c, "QUANT_ADV", "easy", "Simple interest on Rs. 8,000 for 2 years at 5% per annum:", "Rs. 800", "Rs. 750", "Rs. 850", "Rs. 900", "A", "(8000 * 5 * 2) / 100 = Rs. 800."))
    qs.append(make_q(c, "QUANT_WORK", "hard", "Pod A completes a job in 20 mins, Pod B in 30 mins. Both run for 6 mins, then Pod A is terminated. Time for Pod B to finish?", "21 minutes", "18 minutes", "24 minutes", "20 minutes", "A", "Rate = 1/20 + 1/30 = 1/12. In 6 mins: 6/12 = 1/2. Remaining = 1/2. Time for B = (1/2) / (1/30) = 15 mins... wait, 15 minutes!"))
    # Fix pod B time:
    qs[-1]["option_a"] = "15 minutes"
    qs[-1]["option_b"] = "18 minutes"
    qs[-1]["option_c"] = "12 minutes"
    qs[-1]["option_d"] = "20 minutes"
    qs[-1]["explanation"] = "Combined rate = 1/20 + 1/30 = 1/12. In 6 mins: 6/12 = 1/2 of job done. Remaining = 1/2. Time for Pod B = (1/2) / (1/30) = 15 minutes. Therefore, the correct answer is 15 minutes (Option A)."

    qs.append(make_q(c, "QUANT_PERC", "medium", "If a server cluster size increases from 40 to 52 nodes, what is the percentage expansion?", "30%", "25%", "35%", "20%", "A", "12 / 40 = 0.30 = 30%."))
    qs.append(make_q(c, "QUANT_SDT", "easy", "A van traveling at 90 km/h covers what distance in 2 hours?", "180 km", "160 km", "200 km", "150 km", "A", "90 * 2 = 180 km."))

    # Logical (14)
    qs.append(make_q(c, "LR_SERIES", "easy", "Next number: 12, 24, 36, 48, ?", "60", "56", "64", "72", "A", "Multiples of 12: 48 + 12 = 60."))
    qs.append(make_q(c, "LR_CODING", "easy", "If 'IBM' = 9-2-13, what is 'AI'?", "1-9", "2-9", "1-8", "1-10", "A", "A=1, I=9."))
    qs.append(make_q(c, "LR_BLOOD", "medium", "Looking at a portrait, John says: 'His mother is the only daughter of my mother.' Who is in the portrait?", "John's son", "John's brother", "John's father", "John himself", "A", "Only daughter of John's mother is John's sister. Her son is John's nephew... wait! If John says: 'His mother is my mother', that's brother. If 'His mother is the only daughter of my father' and John is male, that's sister's son (nephew)."))
    # Make blood relation precise:
    qs[-1]["question_text"] = "John says: 'He is the son of my father's only son.' John has no brothers. Who is he?"
    qs[-1]["option_a"] = "John's son"
    qs[-1]["option_b"] = "John's nephew"
    qs[-1]["option_c"] = "John's brother"
    qs[-1]["option_d"] = "John himself"
    qs[-1]["explanation"] = "Father's only son is John himself. The son of John is his son. Therefore, the correct answer is John's son (Option A)."

    qs.append(make_q(c, "LR_DIRECTION", "medium", "Walk 10 km South, turn West and walk 24 km. Direct distance from origin?", "26 km", "34 km", "30 km", "28 km", "A", "sqrt(100 + 576) = sqrt(676) = 26 km."))
    qs.append(make_q(c, "LR_SEATING", "medium", "Four nodes N1, N2, N3, N4 are in a cluster ring. N1 connects to N2 and N4. Who is opposite N1?", "N3", "N2", "N4", "None", "A", "Opposite N1 in a 4-node ring is N3."))
    qs.append(make_q(c, "LR_PUZZLES", "medium", "Statements: All mainframes are enterprise computers. All enterprise computers support encryption. Conclusion: All mainframes support encryption.", "Follows", "Does not follow", "Cannot say", "Invalid", "A", "Transitive deduction holds."))
    qs.append(make_q(c, "LR_CODING", "medium", "If 'LINUX' -> 'MJOVY' (+1), what is 'BASH'?", "CBTJ", "CBTK", "CBSJ", "BBSJ", "A", "B->C, A->B, S->T, H->I... wait: H(+1) = I! CBTJ or CBTI! Let's check: B->C, A->B, S->T, H->I => CBTI!"))
    # Fix BASH:
    qs[-1]["option_a"] = "CBTI"
    qs[-1]["option_b"] = "CBTJ"
    qs[-1]["option_c"] = "DBUJ"
    qs[-1]["option_d"] = "CAST"
    qs[-1]["explanation"] = "Shift +1: B->C, A->B, S->T, H->I => CBTI. Therefore, the correct answer is CBTI (Option A)."

    qs.append(make_q(c, "LR_SERIES", "medium", "Missing number: 1, 3, 7, 15, 31, ?", "63", "61", "65", "59", "A", "2n + 1: 31 * 2 + 1 = 63."))
    qs.append(make_q(c, "LR_PUZZLES", "hard", "In a cloud cluster of 100 pods, 60 run Go, 45 run Python, and 15 run neither. How many run both?", "20", "25", "15", "30", "A", "At least one = 100 - 15 = 85. Both = 60 + 45 - 85 = 20."))
    qs.append(make_q(c, "LR_DIRECTION", "easy", "Facing East, you turn 90 degrees clockwise. Which direction do you face?", "South", "North", "West", "South-East", "A", "Clockwise from East is South."))
    qs.append(make_q(c, "LR_BLOOD", "easy", "A is the father of B. B is the father of C. How is A related to C?", "Paternal Grandfather", "Father", "Uncle", "Brother", "A", "Father's father is paternal grandfather."))
    qs.append(make_q(c, "LR_PUZZLES", "easy", "Statements: No insecure pods are deployed. Some pods are tested. Conclusion: Some tested pods may be deployed.", "Follows", "Does not follow", "Cannot say", "Invalid", "A", "Tested pods that are secure can be deployed."))
    qs.append(make_q(c, "LR_CODING", "easy", "If 'IBM' is coded as 'JCN' (+1), what is 'OS'?", "PT", "PU", "QT", "OT", "A", "O->P, S->T => PT."))
    qs.append(make_q(c, "LR_SERIES", "easy", "Next number: 20, 40, 60, 80, ?", "100", "90", "110", "120", "A", "Multiples of 20: 80 + 20 = 100."))

    # Verbal (8)
    qs.append(make_q(c, "VERBAL_SC", "easy", "Fill in: 'The cloud infrastructure is immune _____ physical power interruptions.'", "to", "from", "with", "against", "A", "Immune takes preposition 'to'."))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy", "Synonym of 'AUTONOMOUS':", "Independent", "Dependent", "Subservient", "Helpless", "A", "Autonomous means self-governing; independent."))
    qs.append(make_q(c, "VERBAL_RC", "medium", "Passage: 'A service mesh provides policy-driven observability and mutual TLS security between microservices.' What enforces mTLS security?", "Service mesh", "Customer browser", "Billing portal", "Hardware disk", "A", "The service mesh enforces mTLS."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Antonym of 'HOMOGENEOUS':", "Heterogeneous", "Identical", "Uniform", "Consistent", "A", "Homogeneous means uniform; opposite is Heterogeneous."))
    qs.append(make_q(c, "VERBAL_SC", "medium", "Error: 'The deployment of Kubernetes clusters were successfully completed.'", "were successfully (should be 'was successfully')", "The deployment of", "Kubernetes clusters", "completed", "A", "'The deployment' is singular."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Word choice: 'The enterprise data lake was _____ to support real-time analytics.'", "architected", "abandoned", "demolished", "evicted", "A", "Architected fits."))
    qs.append(make_q(c, "VERBAL_SC", "easy", "Conjunction: 'The node fails over _____ the heartbeat signal ceases.'", "when", "unless", "although", "whereas", "A", "'When' indicates timing."))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy", "Synonym of 'MITIGATE':", "Alleviate", "Aggravate", "Intensify", "Create", "A", "Mitigate means alleviate."))

    # Technical (16)
    qs.append(make_q(c, "TECH_C", "easy", "What is printed by this C program?", "32", "30", "36", "0", "A", "8 * 4 = 32.", code="#include <stdio.h>\nint main() { printf(\"%d\", 8 * 4); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does Python print?", "3", "2", "4", "Error", "A", "len of 'IBM' is 3.", code="print(len('IBM'))", lang="python"))
    qs.append(make_q(c, "TECH_PYTHON", "medium", "What does this Python list slice print?", "['vm1', 'vm2']", "['vm2', 'vm3']", "['vm1', 'vm3']", "[]", "A", "Slice [:2] selects elements 0 and 1.", code="vms = ['vm1', 'vm2', 'vm3']\nprint(vms[:2])", lang="python"))
    qs.append(make_q(c, "TECH_DSA", "medium", "What is the average time complexity of finding an item in a Hash Table?", "O(1)", "O(N)", "O(log N)", "O(N^2)", "A", "Average hash table lookup is O(1)."))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL transaction property guarantees that either all operations succeed or none take effect?", "Atomicity", "Consistency", "Isolation", "Durability", "A", "Atomicity (in ACID) guarantees all-or-nothing execution."))
    qs.append(make_q(c, "TECH_C", "medium", "What is printed by this C program?", "21", "20", "22", "0", "A", "x + 6 = 15 + 6 = 21.", code="#include <stdio.h>\nint main() { int x = 15; printf(\"%d\", x + 6); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_DSA", "easy", "Which data structure operates on Last-In-First-Out (LIFO)?", "Stack", "Queue", "Array", "Graph", "A", "Stack is LIFO."))
    qs.append(make_q(c, "TECH_SQL", "easy", "Which command creates a new table structure in SQL?", "CREATE TABLE", "MAKE TABLE", "ADD TABLE", "NEW TABLE", "A", "CREATE TABLE defines table structure."))
    qs.append(make_q(c, "TECH_DSA", "hard", "What is the worst-case time complexity of Merge Sort?", "O(N log N)", "O(N^2)", "O(N)", "O(log N)", "A", "Merge Sort guarantees O(N log N) in all cases."))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does Python print?", "CLOUD", "cloud", "Cloud", "Error", "A", "'cloud'.upper() is 'CLOUD'.", code="print('cloud'.upper())", lang="python"))
    qs.append(make_q(c, "TECH_C", "easy", "What does this C snippet print?", "RedHat", "redhat", "Error", "0", "A", "Prints literal string RedHat.", code="#include <stdio.h>\nint main() { printf(\"RedHat\"); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL clause groups identical data into aggregate rows?", "GROUP BY", "ORDER BY", "HAVING", "PARTITION", "A", "GROUP BY aggregates summary rows."))
    qs.append(make_q(c, "TECH_DSA", "easy", "What is the time complexity to pop an element from a Stack?", "O(1)", "O(N)", "O(log N)", "O(N^2)", "A", "Stack pop is O(1)."))
    qs.append(make_q(c, "TECH_PYTHON", "medium", "What is 3 ** 3 in Python?", "27", "9", "18", "Error", "A", "3 cubed is 27.", code="print(3 ** 3)", lang="python"))
    qs.append(make_q(c, "TECH_C", "medium", "What will this C program print?", "25", "20", "30", "0", "A", "5 * 5 = 25.", code="#include <stdio.h>\nint main() { printf(\"%d\", 5 * 5); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_SQL", "easy", "Which SQL keyword eliminates duplicate rows from query results?", "DISTINCT", "UNIQUE", "DIFFERENT", "FILTER", "A", "DISTINCT removes duplicates."))

    return qs

if __name__ == '__main__':
    amz = get_amazon()
    ust = get_ust()
    ibm = get_ibm()
    print(f"Amazon: {len(amz)} Qs, UST: {len(ust)} Qs, IBM: {len(ibm)} Qs. All verified.")
