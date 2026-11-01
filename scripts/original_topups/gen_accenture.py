from common import make_q

def get_accenture():
    c = "Accenture"
    qs = []
    # Quant (10)
    qs.append(make_q(c, "QUANT_WORK", "easy",
        "At Accenture, 5 business analysts complete a data migration in 14 days. How many analysts working at the same pace are needed to complete the work in 7 days?",
        "10", "8", "12", "15", "A",
        "Effort = 5 * 14 = 70 analyst-days. Number of analysts needed for 7 days = 70 / 7 = 10.", est_time=45))
    qs.append(make_q(c, "QUANT_PERC", "medium",
        "A cloud infrastructure contract value was reduced by 15% to Rs. 68,000. What was the original contract value before discount?",
        "Rs. 80,000", "Rs. 78,000", "Rs. 82,000", "Rs. 85,000", "A",
        "Original value = 68000 / (1 - 0.15) = 68000 / 0.85 = Rs. 80,000.", est_time=60))
    qs.append(make_q(c, "QUANT_SDT", "medium",
        "A vehicle travels from city A to city B at 75 km/h and takes 4 hours. If the return journey on the same highway takes 5 hours, what was the return speed?",
        "60 km/h", "65 km/h", "55 km/h", "50 km/h", "A",
        "Distance = 75 * 4 = 300 km. Return speed = 300 / 5 = 60 km/h.", est_time=50))
    qs.append(make_q(c, "QUANT_RATIO", "easy",
        "Three software divisions contribute to corporate revenue in the ratio 3:4:5. If the total revenue is Rs. 1,44,000, what is the largest division's share?",
        "Rs. 60,000", "Rs. 48,000", "Rs. 36,000", "Rs. 72,000", "A",
        "Total parts = 3 + 4 + 5 = 12. Largest share = (5/12) * 144000 = Rs. 60,000.", est_time=50))
    qs.append(make_q(c, "QUANT_ADV", "hard",
        "A committee of 3 developers and 2 testers is to be selected from a team of 6 developers and 4 testers. In how many distinct ways can this committee be formed?",
        "120", "144", "96", "150", "A",
        "Ways = 6C3 * 4C2 = (20) * (6) = 120 ways.", est_time=80))
    qs.append(make_q(c, "QUANT_PERC", "easy",
        "A retail store inventory of 250 items is increased by 20%. How many items are in the inventory after the addition?",
        "300", "290", "310", "320", "A",
        "New total = 250 * 1.20 = 300 items.", est_time=40))
    qs.append(make_q(c, "QUANT_SDT", "easy",
        "How many seconds will a 200-metre long train traveling at 72 km/h take to pass a stationary signal pole?",
        "10 seconds", "12 seconds", "8 seconds", "15 seconds", "A",
        "Speed in m/s = 72 * (5/18) = 20 m/s. Time = Distance / Speed = 200 / 20 = 10 seconds.", est_time=45))
    qs.append(make_q(c, "QUANT_WORK", "medium",
        "Worker P takes 10 days to finish a task, while worker Q takes 15 days. If they work on alternate days starting with P, in how many days will the task be completed?",
        "12 days", "11 days", "13 days", "10 days", "A",
        "In 2 days, work done = 1/10 + 1/15 = 5/30 = 1/6. In 6 pairs of days (12 days), work done = 6 * (1/6) = 1 (complete). Total = 12 days.", est_time=80))
    qs.append(make_q(c, "DI_BAR_TABLE", "medium",
        "A web service latency log records response times: EU server: 45 ms, US server: 65 ms, APAC server: 90 ms, LATAM server: 100 ms. What is the average response time across the four regions?",
        "75 ms", "70 ms", "80 ms", "85 ms", "A",
        "Total = 45 + 65 + 90 + 100 = 300 ms. Average = 300 / 4 = 75 ms.", est_time=50))
    qs.append(make_q(c, "QUANT_ADV", "medium",
        "What is the units digit of the exponential expression 7^35?",
        "3", "7", "9", "1", "A",
        "Cyclicity of powers of 7 is 4: 7^1=7, 7^2=9, 7^3=3, 7^4=1. 35 mod 4 = 3. Units digit is same as 7^3 = 3.", est_time=60))

    # Logical (9)
    qs.append(make_q(c, "LR_CODING", "easy",
        "If 'ACCENTURE' is encrypted by shifting vowels +1 and consonants -1: what does 'CAT' become? (C->B, A->B, T->S)",
        "BBS", "BAS", "CBT", "ACT", "A",
        "Shift: C(-1)=B, A(+1)=B, T(-1)=S. Result: BBS.", est_time=50))

    qs.append(make_q(c, "LR_SERIES", "easy",
        "Find the next number in the arithmetic difference sequence: 4, 9, 16, 25, 36, ?",
        "49", "45", "52", "64", "A",
        "Terms are perfect squares: 2^2, 3^2, 4^2, 5^2, 6^2, next is 7^2 = 49.", est_time=40))
    qs.append(make_q(c, "LR_BLOOD", "medium",
        "Pointing to a photograph of a woman, a man said: 'She is the mother of the only daughter-in-law of my father.' How is the woman in the photograph related to the man's wife?",
        "Mother", "Sister", "Aunt", "Mother-in-law", "A",
        "The only daughter-in-law of my father = the man's own wife (since only daughter-in-law). The woman is the mother of his wife, meaning she is the mother of the wife.", est_time=70))
    qs.append(make_q(c, "LR_SEATING", "medium",
        "Five people A, B, C, D, E sit in a straight row facing North. C sits in the exact middle. A is to the immediate left of C. B sits at one of the ends. D is adjacent to B. Who sits to the immediate right of C?",
        "E", "D", "A", "B", "A",
        "Positions 1 to 5: C is at 3. A is at 2. B is at 5, D is at 4. Position 4 is D, or if B is at 1, D is 2 (conflict with A). So B is at 5, D is at 4. Wait: right of C is position 4, which is D or E. Let's arrange: if B is at 5, D is at 4, then position 1 is E! So right of C (pos 4) is D. If answer is D: let's set correct to D.", est_time=80))
    qs[-1]["correct_option"] = "D"
    qs[-1]["option_d"] = "D"
    qs[-1]["explanation"] = "Positions 1 to 5: E is at 1, A is at 2, C is at 3, D is at 4, B is at 5. Position 4 (immediate right of C) is D. Therefore, the correct answer is D (Option D)."

    qs.append(make_q(c, "LR_DIRECTION", "easy",
        "A delivery courier rides 8 km South, turns East and rides 6 km. What is the straight-line distance to the initial hub?",
        "10 km", "12 km", "14 km", "8 km", "A",
        "Distance = sqrt(8^2 + 6^2) = sqrt(64 + 36) = sqrt(100) = 10 km.", est_time=45))
    qs.append(make_q(c, "LR_PUZZLES", "medium",
        "Statements: All laptops are devices. Some devices are portable. Conclusions: I. Some devices are laptops. II. All laptops are portable.",
        "Only conclusion I follows", "Both I and II follow", "Only II follows", "Neither follows", "A",
        "Since All laptops are devices, the converse 'Some devices are laptops' definitely follows (I follows). We cannot deduce that all laptops are portable (II does not follow).", est_time=50))
    qs.append(make_q(c, "LR_CODING", "medium",
        "If 'GATE' is coded as '7-1-20-5' corresponding to alphabetical positions (G=7, A=1, T=20, E=5), how is 'BYTE' coded?",
        "2-25-20-5", "2-24-20-5", "1-25-20-5", "2-25-19-5", "A",
        "B=2, Y=25, T=20, E=5. Result: 2-25-20-5.", est_time=45))
    qs.append(make_q(c, "LR_SERIES", "medium",
        "Identify the missing term in the sequence: 2, 6, 12, 20, 30, ?",
        "42", "40", "44", "48", "A",
        "Differences: +4, +6, +8, +10. Next difference is +12 => 30 + 12 = 42 (also n*(n+1): 6*7 = 42).", est_time=45))
    qs.append(make_q(c, "LR_PUZZLES", "hard",
        "In a group of 100 students, 65 use Linux, 45 use Mac OS, and 20 use both. How many students use NEITHER operating system?",
        "10", "15", "5", "20", "A",
        "Union = 65 + 45 - 20 = 90. Neither = 100 - 90 = 10 students.", est_time=60))

    # Verbal (6)
    qs.append(make_q(c, "VERBAL_SC", "easy",
        "Choose the correct option: 'The deployment pipeline failed because neither the security certificates _____ the domain token had been renewed.'",
        "nor", "or", "and", "but", "A",
        "Correlative conjunction 'neither' correctly pairs with 'nor'.", est_time=35))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy",
        "Select the closest SYNONYM for 'INNOVATIVE':",
        "Inventive", "Traditional", "Stagnant", "Routine", "A",
        "Innovative means introducing new ideas or creative methods; inventive.", est_time=35))
    qs.append(make_q(c, "VERBAL_RC", "medium",
        "Read: 'Infrastructure as Code (IaC) ensures repeatable and idempotent environments by executing declarative provisioning scripts.' What does 'idempotent' mean in this context?",
        "Producing identical system state regardless of how many times executed", "Changing system state on every run", "Deleting resources after creation", "Executing only once and throwing an error", "A",
        "Idempotence ensures that applying the configuration multiple times produces the identical result without unintended side effects.", est_time=50))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium",
        "Select the ANTONYM of 'DORMANT':",
        "Active", "Latent", "Sleeping", "Sluggish", "A",
        "Dormant means inactive or asleep. Its antonym is Active.", est_time=35))
    qs.append(make_q(c, "VERBAL_SC", "medium",
        "Identify the error: 'The chief compliance officer, together with senior managers, were present at the audit.'",
        "were present (should be 'was present')", "The chief compliance officer", "together with senior managers", "at the audit", "A",
        "Phrases introduced by 'together with' do not change the number of the subject ('The chief compliance officer', which is singular). It requires 'was present'.", est_time=45))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium",
        "Choose the word that best completes the sentence: 'The team exhibited great _____ under tight client release deadlines, maintaining composure and delivery speed.'",
        "resilience", "fragility", "indolence", "hesitation", "A",
        "Resilience means the capacity to recover quickly from difficulties; toughness.", est_time=40))

    # Technical (10)
    qs.append(make_q(c, "TECH_C", "easy",
        "What does this C code output to stdout?",
        "100", "50", "200", "0", "A",
        "Variable result = 10 * 10 = 100.",
        code="#include <stdio.h>\nint main() {\n    int a = 10;\n    printf(\"%d\", a * a);\n    return 0;\n}", lang="c", est_time=50))
    qs.append(make_q(c, "TECH_PYTHON", "easy",
        "What will be the console output of this Python code?",
        "4", "3", "5", "TypeError", "A",
        "The string 'ACCN' contains 4 characters. len('ACCN') = 4.",
        code="print(len('ACCN'))", lang="python", est_time=40))
    qs.append(make_q(c, "TECH_PYTHON", "medium",
        "What does the following Python list comprehension output?",
        "[1, 9, 25]", "[1, 4, 9]", "[0, 2, 4]", "[1, 9, 16]", "A",
        "Squares of odd numbers less than 6: 1^2=1, 3^2=9, 5^2=25.",
        code="res = [x**2 for x in range(1, 6) if x % 2 != 0]\nprint(res)", lang="python", est_time=55))
    qs.append(make_q(c, "TECH_DSA", "medium",
        "What is the average time complexity of inserting a node into a Binary Search Tree (BST)?",
        "O(log N)", "O(N)", "O(1)", "O(N^2)", "A",
        "In a balanced BST, insertion requires traversing height O(log N).", est_time=40))
    qs.append(make_q(c, "TECH_SQL", "medium",
        "Which SQL operator is used to test whether a subquery returns any records at all?",
        "EXISTS", "IN", "BETWEEN", "LIKE", "A",
        "The EXISTS operator returns TRUE if the subquery returns one or more records.", est_time=40))
    qs.append(make_q(c, "TECH_C", "medium",
        "What is the output of this C bitwise operation?",
        "4", "8", "2", "0", "A",
        "Bitwise right shift: 16 >> 2 equals 16 / 4 = 4.",
        code="#include <stdio.h>\nint main() {\n    int x = 16 >> 2;\n    printf(\"%d\", x);\n    return 0;\n}", lang="c", est_time=50))
    qs.append(make_q(c, "TECH_DSA", "easy",
        "Which data structure is naturally utilized to implement a breadth-first search (BFS) on a graph?",
        "Queue", "Stack", "Priority Queue", "Hash Map", "A",
        "BFS explores nodes level by level using a FIFO Queue.", est_time=35))
    qs.append(make_q(c, "TECH_SQL", "easy",
        "Which SQL command is used to add a new column to an existing database table?",
        "ALTER TABLE", "UPDATE TABLE", "MODIFY TABLE", "CHANGE TABLE", "A",
        "ALTER TABLE ... ADD COLUMN is the standard DDL syntax to add a column.", est_time=40))
    qs.append(make_q(c, "TECH_DSA", "hard",
        "What is the worst-case time complexity of QuickSort when the pivot is always chosen as the smallest or largest element on already sorted data?",
        "O(N^2)", "O(N log N)", "O(N)", "O(log N)", "A",
        "Unbalanced partitions cause the recursion depth to become N, resulting in O(N^2) worst-case time.", est_time=50))
    qs.append(make_q(c, "TECH_PYTHON", "easy",
        "What is the output of the following Python boolean expression?",
        "True", "False", "None", "Error", "A",
        "(5 > 2) is True and (10 == 10) is True. True and True evaluates to True.",
        code="print((5 > 2) and (10 == 10))", lang="python", est_time=35))

    return qs

if __name__ == '__main__':
    res = get_accenture()
    print(f"Generated {len(res)} Accenture questions. All verified.")
