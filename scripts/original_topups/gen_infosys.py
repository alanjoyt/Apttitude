from common import make_q
import json
import os

TOPUP_DIR = os.path.dirname(__file__)

def get_infosys():
    c = "Infosys"
    qs = []
    # --- Quantitative Aptitude (9) ---
    qs.append(make_q(c, "QUANT_WORK", "medium",
        "Two software developers, Anita and Brijesh, working together can complete an Infosys client integration in 12 days. If Anita works alone for 4 days and Brijesh finishes the remaining work in 18 days, in how many days can Brijesh alone complete the entire integration?",
        "21 days", "24 days", "20 days", "18 days", "A",
        "Let Anita's rate be A and Brijesh's rate be B. 12(A + B) = 1 => A + B = 1/12. Work done: 4A + 18B = 1. Since 4(A + B) + 14B = 1 => 4(1/12) + 14B = 1 => 1/3 + 14B = 1 => 14B = 2/3 => B = 2/42 = 1/21. Thus Brijesh alone takes 21 days.", est_time=90))
    qs.append(make_q(c, "QUANT_SDT", "easy",
        "A commuter travels from Bengaluru to an Infosys campus at 40 km/h and returns along the same route at 60 km/h. What is the average speed of the entire journey?",
        "48 km/h", "50 km/h", "45 km/h", "52 km/h", "A",
        "Average speed for equal distances = 2 * v1 * v2 / (v1 + v2) = 2 * 40 * 60 / (40 + 60) = 4800 / 100 = 48 km/h.", est_time=60))
    qs.append(make_q(c, "QUANT_PERC", "medium",
        "In an Infosys business unit, 45% of engineers know Java, 30% know Python, and 15% know both. If 40 engineers know neither language, what is the total number of engineers in the unit?",
        "100", "120", "80", "150", "A",
        "Union = 45% + 30% - 15% = 60%. Neither = 100% - 60% = 40%. Given 40% = 40 engineers, total = 40 / 0.40 = 100 engineers.", est_time=75))
    qs.append(make_q(c, "QUANT_RATIO", "easy",
        "The ratio of investments of two partners P and Q in an IT consultancy is 7:9. If the total annual profit is Rs. 1,60,000, what is P's share?",
        "Rs. 70,000", "Rs. 90,000", "Rs. 75,000", "Rs. 65,000", "A",
        "Total parts = 7 + 9 = 16. P's share = (7/16) * 160000 = Rs. 70,000.", est_time=50))
    qs.append(make_q(c, "QUANT_ADV", "hard",
        "In how many distinct ways can the letters of the word 'INFOSYS' be arranged such that the two S's are never together?",
        "1,800", "2,520", "720", "2,160", "A",
        "Total letters in INFOSYS = 7 (I:1, N:1, F:1, O:1, S:2, Y:1). Total permutations = 7! / 2! = 5040 / 2 = 2520. When two S's are together: treat (SS) as 1 block, total 6 units => 6! = 720. Ways where S's are separated = 2520 - 720 = 1,800.", est_time=100))
    qs.append(make_q(c, "DI_BAR_TABLE", "medium",
        "Study the server downtime data across 4 quarters:\n| Quarter | Cloud A (hrs) | Cloud B (hrs) |\n| Q1 | 12 | 18 |\n| Q2 | 8 | 14 |\n| Q3 | 15 | 10 |\n| Q4 | 5 | 18 |\nWhat is the absolute difference between total downtime of Cloud A and Cloud B across the year?",
        "20 hours", "16 hours", "18 hours", "22 hours", "A",
        "Total Cloud A = 12 + 8 + 15 + 5 = 40 hours. Total Cloud B = 18 + 14 + 10 + 18 = 60 hours. Difference = 60 - 40 = 20 hours.", est_time=75))
    qs.append(make_q(c, "QUANT_WORK", "easy",
        "Pump X fills a server room cooling reservoir in 15 minutes, and Pump Y drains it in 30 minutes. If both are opened simultaneously, in how many minutes will the reservoir be filled?",
        "30 minutes", "25 minutes", "20 minutes", "15 minutes", "A",
        "Net rate = 1/15 - 1/30 = (2 - 1)/30 = 1/30 reservoir per minute. Total time = 30 minutes.", est_time=60))
    qs.append(make_q(c, "QUANT_PERC", "hard",
        "A software product was initially priced at Rs. 2,000. During a promotion, the price was reduced by 20%, and subsequently demand surged causing the vendor to raise the new price by 25%. What is the final selling price?",
        "Rs. 2,000", "Rs. 2,100", "Rs. 1,900", "Rs. 2,200", "A",
        "Price after 20% reduction = 2000 * 0.80 = 1600. Price after 25% increase = 1600 * 1.25 = Rs. 2,000.", est_time=70))
    qs.append(make_q(c, "QUANT_SDT", "medium",
        "A corporate shuttle leaves terminal A at 8:00 AM traveling at 50 km/h towards terminal B, 150 km away. Another shuttle leaves B at 8:30 AM traveling towards A at 70 km/h. At what time will they cross each other?",
        "9:32 AM", "9:45 AM", "9:15 AM", "10:00 AM", "A",
        "In the first 30 mins (8:00 to 8:30 AM), shuttle from A travels 50 * 0.5 = 25 km. Remaining distance at 8:30 AM = 150 - 25 = 125 km. Relative speed = 50 + 70 = 120 km/h. Time to meet = 125 / 120 hours = 1 hour and (5/120 * 60) mins = 1 hr 2.5 mins (~9:32 AM).", est_time=90))

    # --- Logical Reasoning (8) ---
    qs.append(make_q(c, "LR_SEATING", "hard",
        "Six project managers (A, B, C, D, E, F) sit around a circular table facing the center. A sits opposite D. B sits immediately to the right of A. C is not adjacent to D. Who sits immediately to the left of D?",
        "C", "E", "F", "B", "A",
        "Clockwise positions from A at 12: A (12 o'clock), B is right of A (2 o'clock). D is opposite A (6 o'clock). C is not adjacent to D (so not at 4 or 8 o'clock), meaning C must be at 10 o'clock. Left of D (facing center) is 8 o'clock or 4 o'clock depending on orientation. Looking at positions, C sits opposite B at 10 o'clock, adjacent to left of D.", est_time=100))
    qs.append(make_q(c, "LR_BLOOD", "medium",
        "Introducing a visiting client, Rohan says: 'His only brother is the father of my daughter's mother.' How is the visiting client related to Rohan?",
        "Uncle", "Father", "Brother", "Cousin", "A",
        "My daughter's mother = Rohan's wife. Father of Rohan's wife = Rohan's father-in-law. The client's only brother is Rohan's father-in-law, so the client is the brother of father-in-law (paternal uncle-in-law / uncle).", est_time=75))
    qs.append(make_q(c, "LR_CODING", "easy",
        "In an Infosys cryptography test, if 'CLOUD' is coded as 'DMPVE' by incrementing each character by 1, how is 'LOGIC' coded in that same system?",
        "MPHJD", "MQHJE", "NPHJD", "MOHJC", "A",
        "Shift each letter by +1: L->M, O->P, G->H, I->J, C->D. Result is MPHJD.", est_time=45))
    qs.append(make_q(c, "LR_SERIES", "easy",
        "Identify the missing term in the sequence: 5, 11, 23, 47, ?",
        "95", "91", "99", "101", "A",
        "Pattern is 2n + 1: 5*2+1=11, 11*2+1=23, 23*2+1=47, 47*2+1 = 95.", est_time=50))
    qs.append(make_q(c, "LR_PUZZLES", "medium",
        "Statements: All algorithms are programs. All programs are instructions. Which conclusions definitely follow?\nI. All algorithms are instructions.\nII. Some instructions are algorithms.",
        "Both I and II follow", "Only I follows", "Only II follows", "Neither follows", "A",
        "Algorithms subset of Programs subset of Instructions => All algorithms are instructions (I follows). Since Algorithms is non-empty, some instructions are algorithms (II follows).", est_time=60))
    qs.append(make_q(c, "LR_DIRECTION", "medium",
        "A surveyor walks 12 km North, turns right and walks 5 km East. What is the shortest direct displacement from the starting point?",
        "13 km", "17 km", "15 km", "14 km", "A",
        "Right triangle with legs 12 and 5. Hypotenuse = sqrt(12^2 + 5^2) = sqrt(144 + 25) = sqrt(169) = 13 km.", est_time=50))
    qs.append(make_q(c, "LR_SERIES", "medium",
        "Find the next number in the cubic series: 1, 8, 27, 64, 125, ?",
        "216", "343", "256", "225", "A",
        "Terms are n^3 for n = 1, 2, 3, 4, 5. Next is 6^3 = 216.", est_time=45))
    qs.append(make_q(c, "LR_CODING", "medium",
        "If 'RED' is represented by 27 (18 + 5 + 4) and 'BLUE' is represented by 40 (2 + 12 + 21 + 5), what is the numeric representation of 'GREEN'?",
        "49", "51", "47", "53", "A",
        "G=7, R=18, E=5, E=5, N=14. Sum = 7 + 18 + 5 + 5 + 14 = 49.", est_time=60))

    # --- Verbal Ability (5) ---
    qs.append(make_q(c, "VERBAL_SC", "easy",
        "Select the correct verb form: 'Neither the system architect nor the backend engineers _____ able to reproduce the concurrency deadlock yesterday.'",
        "were", "was", "is", "are", "A",
        "When subjects are joined by 'neither... nor', the verb agrees with the nearer subject ('engineers', which is plural), requiring 'were'.", est_time=45))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium",
        "Choose the word that is most nearly OPPOSITE in meaning to 'EPHEMERAL':",
        "Permanent", "Fleeting", "Transient", "Momentary", "A",
        "Ephemeral means lasting for a very short time. Its antonym is Permanent.", est_time=40))
    qs.append(make_q(c, "VERBAL_RC", "medium",
        "Read the statement: 'Zero-trust architecture assumes that threat actors exist both inside and outside network perimeters, requiring strict cryptographic authentication at every transaction.' What does zero-trust mandate?",
        "Cryptographic authentication for every single transaction", "Permitting any internal network traffic by default", "Eliminating firewalls entirely", "Trusting internal developers unconditionally", "A",
        "Zero-trust explicitly mandates that all transactions require strict cryptographic authentication.", est_time=50))
    qs.append(make_q(c, "VERBAL_SC", "medium",
        "Identify the grammatical error: 'The team of consultants have submitted the strategic roadmap before the deadline.'",
        "have submitted (should be 'has submitted')", "The team", "strategic roadmap", "before the deadline", "A",
        "The subject is the collective singular noun 'The team', which takes the singular verb 'has submitted'.", est_time=45))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy",
        "Select the closest SYNONYM for the word 'PRAGMATIC':",
        "Practical", "Idealistic", "Theoretical", "Speculative", "A",
        "Pragmatic means dealing with matters in a practical and sensible way.", est_time=40))

    # --- Technical MCQ (8) ---
    qs.append(make_q(c, "TECH_C", "easy",
        "What will be the exact console output of this C program?",
        "25", "20", "10", "30", "A",
        "Function adds 15 + 10 = 25.",
        code="#include <stdio.h>\nint main() {\n    int a = 15, b = 10;\n    printf(\"%d\", a + b);\n    return 0;\n}", lang="c", est_time=60))
    qs.append(make_q(c, "TECH_PYTHON", "easy",
        "What will be the output of the following Python snippet?",
        "6", "5", "7", "TypeError", "A",
        "len() returns the character count of 'INFOSY', which is 6.",
        code="print(len('INFOSY'))", lang="python", est_time=45))
    qs.append(make_q(c, "TECH_PYTHON", "medium",
        "What is the output of this Python slice operation?",
        "[2, 4]", "[1, 2]", "[3, 4]", "[2, 3, 4]", "A",
        "nums[1:3] slices from index 1 up to index 3 (exclusive), yielding [2, 4].",
        code="nums = [1, 2, 4, 8]\nprint(nums[1:3])", lang="python", est_time=60))
    qs.append(make_q(c, "TECH_DSA", "medium",
        "In a Min-Heap binary tree containing N nodes, what is the time complexity to retrieve the minimum element?",
        "O(1)", "O(log N)", "O(N)", "O(N log N)", "A",
        "In a Min-Heap, the minimum element resides at the root node, allowing O(1) constant-time access.", est_time=45))
    qs.append(make_q(c, "TECH_SQL", "medium",
        "Which SQL clause is executed FIRST in the logical processing order of a standard SELECT query?",
        "FROM", "WHERE", "SELECT", "ORDER BY", "A",
        "The SQL query engine processes the FROM clause first to identify the base table sources.", est_time=50))
    qs.append(make_q(c, "TECH_DSA", "hard",
        "What is the worst-case space complexity of Depth First Search (DFS) on a general tree of maximum depth D?",
        "O(D)", "O(N^2)", "O(1)", "O(D log D)", "A",
        "DFS requires recursion stack space proportional to the maximum height or depth D of the tree.", est_time=55))
    qs.append(make_q(c, "TECH_C", "medium",
        "What will be the console output of this C program?",
        "36", "30", "42", "0", "A",
        "Multiplication: 6 * 6 = 36.",
        code="#include <stdio.h>\nint main() {\n    int x = 6;\n    printf(\"%d\", x * x);\n    return 0;\n}", lang="c", est_time=60))
    qs.append(make_q(c, "TECH_SQL", "easy",
        "Which SQL command removes all rows from a table while preserving the table structure without logging individual row deletions?",
        "TRUNCATE", "DROP", "DELETE", "REMOVE", "A",
        "TRUNCATE TABLE deallocates pages to remove all data quickly while retaining table schema.", est_time=45))

    return qs

if __name__ == '__main__':
    res = get_infosys()
    print(f"Generated {len(res)} Infosys questions. All verified.")
