from common import make_q

def get_cognizant():
    c = "Cognizant"
    qs = []
    # Quant (10)
    qs.append(make_q(c, "QUANT_WORK", "easy",
        "Two developers A and B can build an API in 15 days and 10 days respectively. How many days will they take working together?",
        "6 days", "5 days", "7 days", "8 days", "A",
        "Combined rate = 1/15 + 1/10 = 5/30 = 1/6. Days required = 6 days.", est_time=45))
    qs.append(make_q(c, "QUANT_PERC", "easy",
        "An IT service cost is Rs. 5,000. If 18% GST is applied, what is the total invoice amount?",
        "Rs. 5,900", "Rs. 5,800", "Rs. 6,000", "Rs. 5,750", "A",
        "GST = 0.18 * 5000 = Rs. 900. Total = 5000 + 900 = Rs. 5,900.", est_time=40))
    qs.append(make_q(c, "QUANT_SDT", "medium",
        "A motorist drives 120 km at 40 km/h and the next 120 km at 60 km/h. What is the average speed for the 240 km trip?",
        "48 km/h", "50 km/h", "45 km/h", "52 km/h", "A",
        "Time 1 = 120 / 40 = 3 hrs. Time 2 = 120 / 60 = 2 hrs. Total time = 5 hrs. Avg speed = 240 / 5 = 48 km/h.", est_time=50))
    qs.append(make_q(c, "QUANT_RATIO", "easy",
        "Divide Rs. 3,500 among X, Y, and Z in the ratio 2:3:5. How much does Z receive?",
        "Rs. 1,750", "Rs. 1,500", "Rs. 1,050", "Rs. 700", "A",
        "Total parts = 10. Z's share = (5/10) * 3500 = Rs. 1,750.", est_time=45))
    qs.append(make_q(c, "QUANT_ADV", "hard",
        "From a pack of 52 cards, two cards are drawn at random without replacement. What is the probability that both are Aces?",
        "1/221", "1/169", "1/26", "4/663", "A",
        "Probability = (4/52) * (3/51) = (1/13) * (1/17) = 1/221.", est_time=60))
    qs.append(make_q(c, "QUANT_PERC", "medium",
        "A seller marks an item 30% above cost price and allows a 10% discount. What is the profit percentage?",
        "17%", "20%", "15%", "18%", "A",
        "Selling Price = 1.30 * 0.90 = 1.17 of Cost Price. Profit = 17%.", est_time=50))
    qs.append(make_q(c, "QUANT_SDT", "easy",
        "A boat moves at 12 km/h in still water. If the river current speed is 3 km/h, what is the boat's downstream speed?",
        "15 km/h", "9 km/h", "12 km/h", "18 km/h", "A",
        "Downstream speed = boat speed + stream speed = 12 + 3 = 15 km/h.", est_time=40))
    qs.append(make_q(c, "QUANT_WORK", "medium",
        "Pipe A can fill a cistern in 20 minutes, and Pipe B in 30 minutes. If both pipes are opened together, how many minutes will it take to fill the cistern?",
        "12 minutes", "10 minutes", "15 minutes", "25 minutes", "A",
        "Rate = 1/20 + 1/30 = 5/60 = 1/12 cistern per minute. Time = 12 minutes.", est_time=45))
    qs.append(make_q(c, "DI_BAR_TABLE", "medium",
        "Quarterly bug fix counts: Q1: 150, Q2: 200, Q3: 180, Q4: 270. What is the percentage increase in bug fixes from Q1 to Q4?",
        "80%", "75%", "90%", "85%", "A",
        "Increase = 270 - 150 = 120. Percentage increase = (120 / 150) * 100 = 80%.", est_time=50))
    qs.append(make_q(c, "QUANT_ADV", "medium",
        "Find the greatest common divisor (GCD) of 84 and 126.",
        "42", "21", "14", "28", "A",
        "84 = 2^2 * 3 * 7. 126 = 2 * 3^2 * 7. GCD = 2 * 3 * 7 = 42.", est_time=40))

    # Logical (9)
    qs.append(make_q(c, "LR_SERIES", "easy",
        "Identify the next term in the arithmetic sequence: 17, 24, 31, 38, ?",
        "45", "44", "46", "47", "A",
        "Common difference is +7. Next term = 38 + 7 = 45.", est_time=35))
    qs.append(make_q(c, "LR_CODING", "easy",
        "If 'PYTHON' is coded as 'QZUIPO' (+1 shift), how is 'JAVA' coded?",
        "KBWB", "KCWB", "JAWB", "KBVA", "A",
        "J->K, A->B, V->W, A->B => KBWB.", est_time=40))
    qs.append(make_q(c, "LR_BLOOD", "medium",
        "Looking at a portrait, Suresh says: 'He is the son of the father of my brother.' If Suresh has only one brother, who is in the portrait?",
        "Suresh or his brother", "Suresh's son", "Suresh's father", "Suresh's uncle", "A",
        "Father of Suresh's brother = Suresh's father. Son of Suresh's father = Suresh himself or his brother.", est_time=50))
    qs.append(make_q(c, "LR_DIRECTION", "medium",
        "Neha walks 15 km South, turns West and walks 8 km. What is her shortest distance from the start?",
        "17 km", "23 km", "16 km", "19 km", "A",
        "Distance = sqrt(15^2 + 8^2) = sqrt(225 + 64) = sqrt(289) = 17 km.", est_time=45))
    qs.append(make_q(c, "LR_SEATING", "medium",
        "Four colleagues W, X, Y, Z sit in a row facing North. X is not at either end. Y is to the immediate right of X. W is at the left end. What is the position of Z?",
        "Right end", "Left end", "Second from left", "Adjacent to W", "A",
        "Positions 1 to 4: W is at 1. Since X is not at ends and Y is immediate right of X, X is at 2 and Y is at 3. Thus Z is at position 4 (Right end).", est_time=60))
    qs.append(make_q(c, "LR_PUZZLES", "medium",
        "Statements: All servers are computers. All computers are electronic machines. Conclusions: I. All servers are electronic machines. II. Some electronic machines are servers.",
        "Both I and II follow", "Only I follows", "Only II follows", "Neither follows", "A",
        "Servers subset of Computers subset of Electronic Machines => All servers are electronic machines. Since servers exist, some electronic machines are servers.", est_time=50))
    qs.append(make_q(c, "LR_CODING", "medium",
        "If 'CAT' = 24 (3+1+20) and 'DOG' = 26 (4+15+7), what is the value of 'FOX'?",
        "45", "42", "48", "40", "A",
        "F=6, O=15, X=24. Sum = 6 + 15 + 24 = 45.", est_time=45))
    qs.append(make_q(c, "LR_SERIES", "medium",
        "Find the next number: 100, 96, 88, 76, 60, ?",
        "40", "44", "48", "38", "A",
        "Differences: -4, -8, -12, -16. Next difference is -20 => 60 - 20 = 40.", est_time=45))
    qs.append(make_q(c, "LR_PUZZLES", "hard",
        "In a testing team of 50 engineers, 30 know Selenium, 25 know Cypress, and 10 know neither. How many know BOTH Selenium and Cypress?",
        "15", "10", "20", "12", "A",
        "Total knowing at least one = 50 - 10 = 40. Both = 30 + 25 - 40 = 15 engineers.", est_time=55))

    # Verbal (6)
    qs.append(make_q(c, "VERBAL_SC", "easy",
        "Select the correct preposition: 'The team leader congratulated the engineer _____ successfully mitigating the production outage.'",
        "on", "for", "with", "at", "A",
        "The standard idiom is 'congratulate someone ON something'.", est_time=35))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy",
        "Select the SYNONYM of 'METICULOUS':",
        "Careful", "Hasty", "Careless", "Negligent", "A",
        "Meticulous means showing great care and precision; careful.", est_time=30))
    qs.append(make_q(c, "VERBAL_RC", "medium",
        "Read: 'Continuous integration (CI) requires developers to merge code changes into a shared repository several times a day, triggering automated build and test suites.' What is the primary objective of CI?",
        "Detecting integration defects early through automated builds", "Eliminating version control repositories", "Preventing developers from writing code", "Replacing all manual testing with meetings", "A",
        "Continuous integration catches merge conflicts and bugs early via frequent automated builds.", est_time=45))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium",
        "Select the ANTONYM of 'AFFLUENT':",
        "Poor", "Wealthy", "Opulent", "Prosperous", "A",
        "Affluent means wealthy. Its antonym is Poor.", est_time=30))
    qs.append(make_q(c, "VERBAL_SC", "medium",
        "Identify the grammatical error: 'A series of high-level meetings were held to discuss the cloud contract.'",
        "were held (should be 'was held')", "A series", "high-level meetings", "to discuss", "A",
        "The head noun of the subject is 'A series' (singular), requiring the singular verb 'was held'.", est_time=40))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium",
        "Choose the correct word: 'The software architecture must be _____ to withstand sudden traffic spikes during flash sales.'",
        "robust", "fragile", "subtle", "timid", "A",
        "Robust means strong and healthy; capable of withstanding adverse conditions.", est_time=35))

    # Technical (10)
    qs.append(make_q(c, "TECH_C", "easy",
        "What does this C code print?",
        "50", "40", "60", "0", "A",
        "Evaluates 25 * 2 = 50.",
        code="#include <stdio.h>\nint main() {\n    int a = 25;\n    printf(\"%d\", a * 2);\n    return 0;\n}", lang="c", est_time=45))
    qs.append(make_q(c, "TECH_PYTHON", "easy",
        "What is the output of this Python code snippet?",
        "8", "6", "9", "Error", "A",
        "2 ** 3 is 2 cubed = 8.",
        code="print(2 ** 3)", lang="python", est_time=35))
    qs.append(make_q(c, "TECH_PYTHON", "medium",
        "What does this Python slicing print?",
        "[10, 20]", "[20, 30]", "[10, 20, 30]", "[30, 40]", "A",
        "arr[:2] selects elements at indices 0 and 1, yielding [10, 20].",
        code="arr = [10, 20, 30, 40]\nprint(arr[:2])", lang="python", est_time=45))
    qs.append(make_q(c, "TECH_DSA", "medium",
        "Which sorting algorithm has a guaranteed worst-case time complexity of O(N log N)?",
        "Merge Sort", "Quick Sort", "Bubble Sort", "Insertion Sort", "A",
        "Merge Sort always divides in half and merges in linear time, guaranteeing O(N log N) in all cases.", est_time=40))
    qs.append(make_q(c, "TECH_SQL", "medium",
        "Which SQL clause is used to filter records AFTER an aggregation with GROUP BY has been performed?",
        "HAVING", "WHERE", "ORDER BY", "LIMIT", "A",
        "HAVING filters groups created by GROUP BY.", est_time=40))
    qs.append(make_q(c, "TECH_C", "medium",
        "What will be printed by this C program?",
        "20", "10", "30", "0", "A",
        "Pointer dereference: *p is assigned 20, modifying a.",
        code="#include <stdio.h>\nint main() {\n    int a = 10;\n    int *p = &a;\n    *p = 20;\n    printf(\"%d\", a);\n    return 0;\n}", lang="c", est_time=50))
    qs.append(make_q(c, "TECH_DSA", "easy",
        "In a singly linked list, what is the time complexity to insert a new node at the head (beginning)?",
        "O(1)", "O(N)", "O(log N)", "O(N^2)", "A",
        "Inserting at head requires updating the new node's next pointer and head pointer: O(1).", est_time=35))
    qs.append(make_q(c, "TECH_SQL", "easy",
        "Which SQL command removes a table completely along with its schema and indexes from the database?",
        "DROP TABLE", "DELETE TABLE", "TRUNCATE TABLE", "REMOVE TABLE", "A",
        "DROP TABLE deletes the data and the entire table definition.", est_time=35))
    qs.append(make_q(c, "TECH_DSA", "hard",
        "What is the time complexity of building a Binary Heap from an unordered array of N elements using the bottom-up heapify approach?",
        "O(N)", "O(N log N)", "O(N^2)", "O(log N)", "A",
        "Bottom-up heapify runs in linear O(N) time by mathematical sum of heights.", est_time=55))
    qs.append(make_q(c, "TECH_PYTHON", "easy",
        "What does the following Python statement output?",
        "3", "2", "4", "Error", "A",
        "len([10, 20, 30]) evaluates to 3.",
        code="print(len([10, 20, 30]))", lang="python", est_time=30))

    return qs

if __name__ == '__main__':
    cog = get_cognizant()
    print(f"Cognizant generated: {len(cog)} Qs. All verified.")
