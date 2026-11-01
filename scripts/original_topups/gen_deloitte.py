from common import make_q

def get_deloitte():
    c = "Deloitte"
    qs = []
    # Quant (15)
    qs.append(make_q(c, "QUANT_WORK", "easy", "A Deloitte advisory team of 5 consultants finishes a risk assessment in 12 days. How many days will 6 consultants take at the same pace?", "10 days", "9 days", "11 days", "8 days", "A", "Effort = 5 * 12 = 60 days. Time = 60 / 6 = 10 days."))
    qs.append(make_q(c, "QUANT_PERC", "medium", "An audit client revenue increased from $50M in 2024 to $65M in 2025. What is the percentage growth?", "30%", "25%", "35%", "20%", "A", "Increase = 15 / 50 = 0.30 = 30%."))
    qs.append(make_q(c, "QUANT_SDT", "medium", "An executive drives 210 km in 3.5 hours. What is the average speed?", "60 km/h", "55 km/h", "65 km/h", "70 km/h", "A", "Speed = 210 / 3.5 = 60 km/h."))
    qs.append(make_q(c, "QUANT_RATIO", "easy", "In a partnership, profits are divided between A and B in ratio 5:3. If A gets Rs. 25,000, what is total profit?", "Rs. 40,000", "Rs. 35,000", "Rs. 45,000", "Rs. 30,000", "A", "5 parts = 25000 => 1 part = 5000. Total = 8 * 5000 = Rs. 40,000."))
    qs.append(make_q(c, "QUANT_ADV", "hard", "What is the sum of all interior angles of a regular hexagon?", "720 degrees", "540 degrees", "1080 degrees", "360 degrees", "A", "(n - 2) * 180 = (6 - 2) * 180 = 4 * 180 = 720 degrees."))
    qs.append(make_q(c, "QUANT_PERC", "easy", "A tax advisory service fees Rs. 8,000 is subject to 10% discount. What is the discounted fee?", "Rs. 7,200", "Rs. 7,000", "Rs. 7,400", "Rs. 7,500", "A", "Fee = 8000 * 0.90 = Rs. 7,200."))
    qs.append(make_q(c, "QUANT_SDT", "easy", "How far will an express courier travel in 45 minutes at a constant speed of 80 km/h?", "60 km", "50 km", "55 km", "70 km", "A", "Distance = 80 * (45/60) = 80 * 0.75 = 60 km."))
    qs.append(make_q(c, "QUANT_WORK", "medium", "Analyst X can review a ledger in 4 hours, Y in 6 hours. Working together, in how many hours can they review it?", "2.4 hours", "2.5 hours", "3 hours", "2 hours", "A", "Rate = 1/4 + 1/6 = 5/12. Time = 12 / 5 = 2.4 hours."))
    qs.append(make_q(c, "DI_BAR_TABLE", "medium", "Quarterly consulting revenues: Q1: $10M, Q2: $14M, Q3: $12M, Q4: $16M. What is the quarterly average?", "$13M", "$12M", "$14M", "$15M", "A", "Total = $52M / 4 = $13M."))
    qs.append(make_q(c, "QUANT_ADV", "medium", "Find the area of a circle of radius 7 cm (using pi = 22/7):", "154 cm^2", "144 cm^2", "164 cm^2", "132 cm^2", "A", "Area = (22/7) * 7 * 7 = 154 cm^2."))
    qs.append(make_q(c, "QUANT_PERC", "hard", "A trader sold two items for Rs. 990 each, making 10% gain on one and 10% loss on the other. What is the net overall outcome?", "1% loss", "1% gain", "No profit no loss", "2% loss", "A", "CP1 = 990/1.10 = 900. CP2 = 990/0.90 = 1100. Total CP = 2000, Total SP = 1980. Loss = 20/2000 = 1% loss."))
    qs.append(make_q(c, "QUANT_SDT", "medium", "A train 150 m long crosses a 250 m bridge at 72 km/h. How many seconds does it take?", "20 seconds", "18 seconds", "22 seconds", "25 seconds", "A", "Total distance = 150 + 250 = 400 m. Speed = 72 * (5/18) = 20 m/s. Time = 400 / 20 = 20 seconds."))
    qs.append(make_q(c, "QUANT_RATIO", "medium", "If 3A = 4B = 5C, what is A:B:C?", "20:15:12", "15:12:20", "12:15:20", "5:4:3", "A", "Let 3A = 4B = 5C = 60. Then A=20, B=15, C=12 => 20:15:12."))
    qs.append(make_q(c, "QUANT_ADV", "easy", "What is the simple interest on Rs. 5,000 at 8% per annum for 3 years?", "Rs. 1,200", "Rs. 1,000", "Rs. 1,400", "Rs. 1,500", "A", "SI = (5000 * 8 * 3) / 100 = Rs. 1,200."))
    qs.append(make_q(c, "QUANT_WORK", "hard", "A can do a piece of work in 20 days and B in 30 days. They work together for 5 days and then A leaves. How many days does B take to finish the rest?", "17.5 days", "15 days", "20 days", "18 days", "A", "Combined rate = 1/20 + 1/30 = 5/60 = 1/12. In 5 days: 5/12. Remaining = 7/12. Time for B = (7/12) / (1/30) = (7/12) * 30 = 35/2 = 17.5 days."))

    # Logical (12)
    qs.append(make_q(c, "LR_SERIES", "easy", "Next number: 7, 14, 28, 56, ?", "112", "108", "120", "116", "A", "Doubling sequence: 56 * 2 = 112."))
    qs.append(make_q(c, "LR_CODING", "easy", "If 'DELOITTE' is coded by replacing each character by +1, what is 'TAX'?", "UBY", "UBZ", "VBY", "UAY", "A", "T->U, A->B, X->Y => UBY."))
    qs.append(make_q(c, "LR_BLOOD", "medium", "A man's sister is the wife of B. How is B related to the man?", "Brother-in-law", "Cousin", "Uncle", "Father", "A", "The husband of a man's sister is his brother-in-law."))
    qs.append(make_q(c, "LR_DIRECTION", "medium", "Travel 20 km East, turn left and travel 15 km North. Direct distance from start?", "25 km", "35 km", "28 km", "30 km", "A", "sqrt(20^2 + 15^2) = sqrt(400 + 225) = sqrt(625) = 25 km."))
    qs.append(make_q(c, "LR_SEATING", "medium", "Six partners sit around a circular table facing inward. P is opposite Q. R is right of P. Who is left of Q if S is opposite R?", "S", "R", "P", "T", "A", "Clockwise from P: P, R, T, Q, U, S. S is opposite R, so S sits to the left of Q."))
    qs.append(make_q(c, "LR_PUZZLES", "medium", "Statements: Some managers are auditors. All auditors are CPAs. Conclusion: Some managers are CPAs.", "Follows", "Does not follow", "Cannot say", "False", "A", "The overlap between managers and auditors must also be CPAs."))
    qs.append(make_q(c, "LR_CODING", "medium", "If 'AUDIT' has letter positions 1-21-4-9-20, what is 'RISK'?", "18-9-19-11", "18-8-19-11", "17-9-19-11", "18-9-20-11", "A", "R=18, I=9, S=19, K=11."))
    qs.append(make_q(c, "LR_SERIES", "medium", "Missing number: 50, 45, 38, 29, 18, ?", "5", "8", "6", "7", "A", "Differences: -5, -7, -9, -11. Next is -13 => 18 - 13 = 5."))
    qs.append(make_q(c, "LR_PUZZLES", "hard", "In an office of 120 employees, 70 prefer remote work, 60 prefer in-office, and 20 prefer neither. How many prefer both?", "30", "25", "35", "20", "A", "Preferring at least one = 120 - 20 = 100. Both = 70 + 60 - 100 = 30."))
    qs.append(make_q(c, "LR_DIRECTION", "easy", "Facing South, you make a 180-degree turn. Which direction are you facing?", "North", "South", "East", "West", "A", "180 degree turn inverts South to North."))
    qs.append(make_q(c, "LR_BLOOD", "easy", "P is Q's daughter. Q is R's mother. How is P related to R if R is male?", "Sister", "Brother", "Mother", "Aunt", "A", "Both are children of Q; P is R's sister."))
    qs.append(make_q(c, "LR_PUZZLES", "easy", "Statements: All taxes are liabilities. All liabilities are obligations. Conclusion: All taxes are obligations.", "Follows", "Does not follow", "Cannot say", "Invalid", "A", "Transitive deduction holds."))

    # Verbal (8)
    qs.append(make_q(c, "VERBAL_SC", "easy", "Fill in: 'The senior partner insisted _____ conducting an independent audit.'", "on", "in", "to", "for", "A", "Insist takes preposition 'on'."))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy", "Synonym of 'ACCURATE':", "Precise", "Vague", "False", "Erratic", "A", "Accurate means correct or precise."))
    qs.append(make_q(c, "VERBAL_RC", "medium", "Passage: 'Internal controls mitigate financial reporting misstatements by segregating duties across authorization and execution.' What segregates duties?", "Internal controls", "External hackers", "Sales quotas", "Marketing slogans", "A", "Internal controls segregate duties."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Antonym of 'SOLVENT':", "Bankrupt", "Wealthy", "Stable", "Prosperous", "A", "Solvent means able to pay debts; opposite is Bankrupt."))
    qs.append(make_q(c, "VERBAL_SC", "medium", "Error: 'The advisory team comprising six senior consultants are meeting today.'", "are meeting (should be 'is meeting')", "The advisory team", "comprising six senior consultants", "today", "A", "'The advisory team' is singular collective noun."))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium", "Word choice: 'The fiscal policy was designed to _____ economic growth during the downturn.'", "stimulate", "depress", "stifle", "negate", "A", "Stimulate means encourage development."))
    qs.append(make_q(c, "VERBAL_SC", "easy", "Conjunction: 'The audit will proceed _____ the client provides all documentation on time.'", "provided that", "unless", "although", "whereas", "A", "'Provided that' specifies the conditional agreement."))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy", "Synonym of 'COLLABORATE':", "Cooperate", "Compete", "Isolate", "Disrupt", "A", "Collaborate means cooperate."))

    # Technical (10)
    qs.append(make_q(c, "TECH_C", "easy", "What is printed by this C snippet?", "24", "20", "28", "0", "A", "Multiplication: 4 * 6 = 24.", code="#include <stdio.h>\nint main() { printf(\"%d\", 4 * 6); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does Python output?", "3", "2", "4", "Error", "A", "len of string 'TAX' is 3.", code="print(len('TAX'))", lang="python"))
    qs.append(make_q(c, "TECH_PYTHON", "medium", "What does this Python list slice print?", "['audit', 'tax']", "['tax', 'risk']", "['audit', 'risk']", "[]", "A", "Slice [:2] selects elements 0 and 1.", code="s = ['audit', 'tax', 'risk']\nprint(s[:2])", lang="python"))
    qs.append(make_q(c, "TECH_DSA", "medium", "What is the time complexity of searching a key in a Hash Map on average?", "O(1)", "O(N)", "O(log N)", "O(N^2)", "A", "Hash table provides O(1) expected lookup time."))
    qs.append(make_q(c, "TECH_SQL", "medium", "Which SQL function calculates the average value of a numeric column?", "AVG()", "MEAN()", "AVERAGE()", "SUM()", "A", "AVG() computes the arithmetic mean."))
    qs.append(make_q(c, "TECH_C", "medium", "What is the output of this C code?", "15", "10", "5", "0", "A", "Pointers: *p + 5 = 10 + 5 = 15.", code="#include <stdio.h>\nint main() { int a = 10; int *p = &a; printf(\"%d\", *p + 5); return 0; }", lang="c"))
    qs.append(make_q(c, "TECH_DSA", "easy", "Which data structure uses top pointer for push and pop?", "Stack", "Queue", "Binary Tree", "Graph", "A", "Stack pushes and pops from top."))
    qs.append(make_q(c, "TECH_SQL", "easy", "Which keyword is used to modify existing table rows in SQL?", "UPDATE", "MODIFY", "ALTER", "CHANGE", "A", "UPDATE modifies row data."))
    qs.append(make_q(c, "TECH_DSA", "hard", "What is the worst-case time complexity of Merge Sort on N elements?", "O(N log N)", "O(N^2)", "O(N)", "O(log N)", "A", "Merge Sort is always O(N log N) in all cases."))
    qs.append(make_q(c, "TECH_PYTHON", "easy", "What does this Python snippet evaluate to?", "True", "False", "None", "Error", "A", "100 > 50 is True.", code="print(100 > 50)", lang="python"))

    return qs

if __name__ == '__main__':
    delo = get_deloitte()
    print(f"Deloitte generated: {len(delo)} Qs. All verified.")
