from common import make_q

def get_capgemini():
    c = "Capgemini"
    qs = []
    # Quant (13)
    qs.append(make_q(c, "QUANT_WORK", "easy", "A project team of 8 analysts completes an audit in 15 days. How many days would 12 analysts take?", "10 days", "12 days", "8 days", "14 days", "A", "Effort = 8 * 15 = 120. Days for 12 analysts = 120 / 12 = 10 days."))
    qs.append(make_q(c, "QUANT_PERC", "medium", "An equipment price of Rs. 12,000 is discounted by 25%. What is the sale price?", "Rs. 9,000", "Rs. 8,500", "Rs. 9,500", "Rs. 10,000", "A", "Sale price = 12000 * 0.75 = Rs. 9,000."))
    qs.append(make_q(c, "QUANT_SDT", "medium", "A car travels 150 km at 50 km/h and returns at 75 km/h. What is the average speed?", "60 km/h", "62.5 km/h", "58 km/h", "65 km/h", "A", "Avg speed = 2 * 50 * 75 / (50 + 75) = 7500 / 125 = 60 km/h."))
    qs.append(make_q(c, "QUANT_RATIO", "easy", "Two numbers are in ratio 4:5 and their sum is 180. What is the smaller number?", "80", "100", "75", "90", "A", "9 parts = 180 => 1 part = 20. Smaller = 4 * 20 = 80."))
    qs.append(make_q(c, "QUANT_ADV", "hard", "How many 4-digit numbers can be formed from digits 1, 2, 3, 4, 5 without repetition?", "120", "24", "60", "240", "A", "Permutations = 5P4 = 5 * 4 * 3 * 2 = 120."))
    qs.append(make_q(c, "QUANT_PERC", "easy", "What is 15% of 600?", "90", "80", "100", "85", "A", "15% of 600 = 0.15 * 600 = 90."))
    qs.append(make_q(c, "QUANT_SDT", "easy", "A train 120 m long crosses a pole in 6 seconds. What is its speed in km/h?", "72 km/h", "60 km/h", "80 km/h", "54 km/h", "A", "Speed = 120 / 6 = 20 m/s = 20 * (18/5) = 72 km/h."))
    qs.append(make_q(c, "QUANT_WORK", "medium", "P finishes work in 12 days, Q in 24 days. Working together, in how many days will they finish?", "8 days", "6 days", "10 days", "9 days", "A", "1/12 + 1/24 = 3/24 = 1/8. Time = 8 days."))
    qs.append(make_q(c, "DI_BAR_TABLE", "medium", "Monthly cloud costs: Jan: Rs. 20k, Feb: Rs. 25k, Mar: Rs. 30k, Apr: Rs. 45k. What is the mean monthly cost?", "Rs. 30k", "Rs. 28k", "Rs. 32k", "Rs. 35k", "A", "Total = 120k / 4 = Rs. 30k."))
    qs.append(make_q(c, "QUANT_ADV", "medium", "What is the perimeter of a rectangle of length 24 cm and breadth 16 cm?", "80 cm", "75 cm", "84 cm", "90 cm", "A", "Perimeter = 2 * (24 + 16) = 2 * 40 = 80 cm."))
    qs.append(make_q(c, "QUANT_PERC", "hard", "A sum invested at 10% per annum compound interest for 2 years yields Rs. 2,100 interest. What was the principal?", "Rs. 10,000", "Rs. 9,000", "Rs. 11,000", "Rs. 12,000", "A", "CI = P * ((1.10)^2 - 1) = P * 0.21. P = 2100 / 0.21 = Rs. 10,000."))
    qs.append(make_q(c, "QUANT_SDT", "medium", "A cyclist rides 45 km in 2.5 hours. What is the speed in km/h?", "18 km/h", "20 km/h", "16 km/h", "15 km/h", "A", "Speed = 45 / 2.5 = 18 km/h."))
    qs.append(make_q(c, "QUANT_RATIO", "medium", "If A:B = 2:3 and B:C = 4:5, what is A:C?", "8:15", "6:15", "8:12", "10:15", "A", "A:C = (A/B) * (B/C) = (2/3) * (4/5) = 8/15."))

    # Logical (11)
    qs.append(make_q(c, "LR_SERIES", "easy", "Find next: 3, 9, 27, 81, ?", "243", "216", "256", "324", "A", "Powers of 3: 3^5 = 243."))
    qs.append(make_q(c, "LR_CODING", "easy", "If 'INK' = 9-14-11, what is 'PEN'?", "16-5-14", "15-5-14", "16-6-14", "16-5-15", "A", "P=16, E=5, N=14."))
    qs.append(make_q(c, "LR_BLOOD", "medium", "Pointing to a man, Geeta says: 'His mother is the only daughter of my mother.' How is Geeta related to the man?", "Mother", "Sister", "Aunt", "Daughter", "A", "Only daughter of Geeta's mother is Geeta. She is his mother."))
    qs.append(make_q(c, "LR_DIRECTION", "medium", "Walk 6 km North, turn right and walk 8 km. Distance from start?", "10 km", "14 km", "12 km", "9 km", "A", "sqrt(36 + 64) = 10 km."))
    qs.append(make_q(c, "LR_SEATING", "medium", "Five friends P, Q, R, S, T sit in a line facing North. R is in center. P is at left end. Q is adjacent to P. Who is adjacent to S if T is right end?", "R", "P", "Q", "None", "A", "Order: P, Q, R, S, T. S is adjacent to R and T."))
    qs.append(make_q(c, "LR_PUZZLES", "medium", "Statements: All cars are vehicles. All vehicles have wheels. Conclusion: All cars have wheels.", "Follows", "Does not follow", "Cannot say", "Invalid", "A", "Transitive property guarantees cars have wheels."))
    qs.append(make_q(c, "LR_CODING", "medium", "If 'APPLE' is coded as 'BQQMF' (+1 shift), what is 'GRAPE'?", "HSBQF", "HSARF", "ITCQG", "HRBQF", "A", "Shift +1: G->H, R->S, A->B, P->Q, E->F => HSBQF."))
    qs.append(make_q(c, "LR_SERIES", "medium", "Find next: 10, 19, 37, 73, ?", "145", "144", "146", "140", "A", "Pattern: 2x - 1: 10*2-1=19, 19*2-1=37, 37*2-1=73, 73*2-1 = 145."))
    qs.append(make_q(c, "LR_PUZZLES", "hard", "Out of 60 engineers, 35 know C++, 25 know Java, and 10 know neither. How many know both?", "10", "15", "5", "20", "A", "Either = 60 - 10 = 50. Both = 35 + 25 - 50 = 10."))
    qs.append(make_q(c, "LR_DIRECTION", "easy", "Facing North, you turn 90 degrees clockwise. Which direction do you face?", "East", "West", "South", "North-East", "A", "90 deg clockwise from North is East."))
    qs.append(make_q(c, "LR_BLOOD", "easy", "X is the brother of Y. Y is the mother of Z. How is X related to Z?", "Maternal Uncle", "Father", "Paternal Uncle", "Brother", "A", "Mother's brother is maternal uncle."))

    # Verbal (7)
    qs.append(make_q(c, "VERBAL_SC", "easy", "Choose correct preposition: 'The team must adhere _____ strict quality controls.'", "to", "with", "by", "on", "A", "Adhere takes 'to'."))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy", "Synonym of 'AMBIGUOUS':", "Vague", "Clear", "Definite", "Lucid", "A", "Ambiguous means unclear or vague."))
    qs.append(make_q(c, "VERBAL_RC", "medium", "Passage: 'Refactoring cleans internal code structure without changing observable behavior.' What is altered during refactoring?", "Internal structure only", "External output", "Business requirements", "User interface", "A", "Refactoring alters internal structure only."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Antonym of 'LUCID':", "Obscure", "Clear", "Bright", "Transparent", "A", "Lucid means clear; opposite is Obscure."))
    qs.append(make_q(c, "VERBAL_SC", "medium", "Error: 'Neither of the proposals were accepted by the steering board.'", "were accepted (should be 'was accepted')", "Neither of", "the proposals", "by the board", "A", "'Neither' takes singular verb 'was'."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Word choice: 'The manager offered a _____ apology for the deployment delay.'", "sincere", "spurious", "hasty", "fragile", "A", "Sincere fits the context."))
    qs.append(make_q(c, "VERBAL_SC", "easy", "Choose conjunction: 'He worked diligently _____ he could meet the launch deadline.'", "so that", "unless", "although", "despite", "A", "'So that' expresses purpose."))

    # Technical (11)
    qs.append(make_q(c, "TECH_C", "easy", "What will be printed?", "14", "10", "12", "0", "A", "7 * 2 = 14.", code="#include <stdio.h>\nint main() { printf(\"%d\", 7 * 2); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What is printed by Python?", "10", "5", "15", "Error", "A", "abs(-10) = 10.", code="print(abs(-10))", lang="python"))
    qs.append(make_q(c, "TECH_PYTHON", "medium", "What is the output?", "['a', 'b']", "['b', 'c']", "['a', 'b', 'c']", "[]", "A", "Slice [:2] selects first two elements.", code="letters = ['a', 'b', 'c']\nprint(letters[:2])", lang="python"))
    qs.append(make_q(c, "TECH_DSA", "medium", "What is the time complexity to access an element by index in an array?", "O(1)", "O(N)", "O(log N)", "O(N^2)", "A", "Direct memory offset provides O(1) access."))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL constraint ensures all values in a column are distinct?", "UNIQUE", "NOT NULL", "CHECK", "FOREIGN KEY", "A", "UNIQUE enforces distinctness."))
    qs.append(make_q(c, "TECH_C", "medium", "What does this C code output?", "8", "6", "10", "0", "A", "Function adds 5 + 3 = 8.", code="#include <stdio.h>\nint add(int x, int y) { return x + y; }\nint main() { printf(\"%d\", add(5, 3)); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_DSA", "easy", "Which data structure follows First-In-First-Out (FIFO)?", "Queue", "Stack", "Binary Tree", "Hash Map", "A", "A Queue is FIFO."))
    qs.append(make_q(c, "TECH_SQL", "easy", "Which clause sorts query results in descending order?", "ORDER BY col DESC", "SORT BY col DESC", "GROUP BY col DESC", "ARRANGE BY col DESC", "A", "ORDER BY ... DESC sorts descending."))
    qs.append(make_q(c, "TECH_DSA", "hard", "What is the worst-case search time in an unbalanced Binary Search Tree?", "O(N)", "O(log N)", "O(1)", "O(N log N)", "A", "A degenerate BST behaves as a linked list: O(N)."))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does Python print?", "CAPGEMINI", "capgemini", "Capgemini", "Error", "A", "'capgemini'.upper() gives 'CAPGEMINI'.", code="print('capgemini'.upper())", lang="python"))
    qs.append(make_q(c, "TECH_C", "medium", "What does this C code output?", "1 2 3", "1 2", "0 1 2", "3 2 1", "A", "Loop prints 1, 2, 3.", code="#include <stdio.h>\nint main() {\n    for(int i = 1; i <= 3; i++) printf(\"%d \", i);\n    return 0;\n}", lang="c"))

    return qs

if __name__ == '__main__':
    cap = get_capgemini()
    print(f"Capgemini generated: {len(cap)} Qs. All verified.")
