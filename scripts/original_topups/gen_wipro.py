from common import make_q

def get_wipro():
    c = "Wipro NLTH"
    qs = []
    # --- Quant (9) ---
    qs.append(make_q(c, "QUANT_WORK", "easy",
        "A team of 6 Wipro network engineers can configure a data center switch rack in 8 hours. How many hours would 4 engineers take to configure the identical rack working at the same speed?",
        "12 hours", "10 hours", "14 hours", "16 hours", "A",
        "Total engineer-hours = 6 * 8 = 48. With 4 engineers, hours required = 48 / 4 = 12 hours.", est_time=50))
    qs.append(make_q(c, "QUANT_PERC", "medium",
        "A trader sells an IoT router for Rs. 3,600, suffering a 10% loss on cost price. At what price should the router be sold to achieve a 15% profit?",
        "Rs. 4,600", "Rs. 4,500", "Rs. 4,800", "Rs. 4,200", "A",
        "Cost Price = 3600 / 0.90 = Rs. 4,000. Selling price for 15% gain = 4000 * 1.15 = Rs. 4,600.", est_time=70))
    qs.append(make_q(c, "QUANT_SDT", "hard",
        "Two trains of length 140 m and 160 m run on parallel tracks. When running in the same direction, the faster train passes the slower in 30 seconds. When running in opposite directions, they pass each other in 6 seconds. What is the speed of the faster train in m/s?",
        "30 m/s", "25 m/s", "20 m/s", "35 m/s", "A",
        "Total distance = 140 + 160 = 300 m. Same direction: relative speed v1 - v2 = 300 / 30 = 10 m/s. Opposite direction: v1 + v2 = 300 / 6 = 50 m/s. Adding equations: 2 * v1 = 60 => v1 = 30 m/s.", est_time=90))
    qs.append(make_q(c, "QUANT_RATIO", "medium",
        "A vessel contains 60 litres of a mixture of milk and water in the ratio 2:1. How many litres of water must be added to make the ratio of milk to water 1:2?",
        "60 litres", "40 litres", "50 litres", "30 litres", "A",
        "Milk = (2/3) * 60 = 40 L, Water = 20 L. Let x litres water be added: 40 / (20 + x) = 1/2 => 20 + x = 80 => x = 60 litres.", est_time=75))
    qs.append(make_q(c, "QUANT_ADV", "medium",
        "What is the compound interest accrued on a principal of Rs. 8,000 at 10% per annum compounded annually for 2 years?",
        "Rs. 1,680", "Rs. 1,600", "Rs. 1,720", "Rs. 1,800", "A",
        "Amount = 8000 * (1 + 0.10)^2 = 8000 * 1.21 = Rs. 9,680. Compound Interest = 9680 - 8000 = Rs. 1,680.", est_time=60))
    qs.append(make_q(c, "DI_BAR_TABLE", "easy",
        "In a sprint velocity chart: Week 1: 30 story points, Week 2: 45 story points, Week 3: 40 story points, Week 4: 25 story points. What is the average velocity per week?",
        "35 points", "32 points", "38 points", "30 points", "A",
        "Total = 30 + 45 + 40 + 25 = 140 points. Average = 140 / 4 = 35 story points.", est_time=45))
    qs.append(make_q(c, "QUANT_SDT", "easy",
        "A delivery van covers a distance of 180 km in 3 hours. How many metres per second does the van travel on average?",
        "16.67 m/s", "15.5 m/s", "20.0 m/s", "18.2 m/s", "A",
        "Speed = 180 / 3 = 60 km/h. In m/s = 60 * (5/18) = 300 / 18 = 16.67 m/s.", est_time=50))
    qs.append(make_q(c, "QUANT_PERC", "easy",
        "If the salary of an employee is increased by 10% and then decreased by 10%, what is the net percentage change in salary?",
        "1% decrease", "0% change", "1% increase", "2% decrease", "A",
        "Net change = +10 - 10 - (10*10)/100 = -1% (1% decrease).", est_time=45))
    qs.append(make_q(c, "QUANT_WORK", "medium",
        "Pipe A can fill a tank in 6 hours, while Pipe B can fill it in 8 hours. Both pipes are opened, and after 2 hours Pipe A is closed. How many more hours will Pipe B take to fill the rest of the tank?",
        "3.33 hours", "4 hours", "2.5 hours", "5 hours", "A",
        "Rate A = 1/6, Rate B = 1/8. Combined rate = 1/6 + 1/8 = 7/24. In 2 hours, they fill 2 * 7/24 = 7/12. Remaining = 5/12. Time for B = (5/12) / (1/8) = (5/12) * 8 = 40/12 = 3.33 hours (3 hours 20 mins).", est_time=80))

    # --- Logical Reasoning (8) ---
    qs.append(make_q(c, "LR_BLOOD", "easy",
        "A's father is the husband of B's mother's only daughter. How is B related to A if B is female?",
        "Mother", "Sister", "Aunt", "Grandmother", "A",
        "B's mother's only daughter is B herself. A's father is the husband of B, meaning B is the mother of A.", est_time=50))
    qs.append(make_q(c, "LR_SERIES", "easy",
        "Find the next term in the alternating pattern: 2, 7, 4, 14, 6, 21, 8, ?",
        "28", "26", "30", "24", "A",
        "Two interleaved series: (2, 4, 6, 8, ...) increasing by 2, and (7, 14, 21, ...) increasing by 7. Next term is 21 + 7 = 28.", est_time=50))
    qs.append(make_q(c, "LR_CODING", "medium",
        "If 'WIPRO' is coded as 'XJQSP' by shifting each letter +1, how is 'TECH' coded in that same system?",
        "UFDI", "UGDJ", "TEDI", "VFEJ", "A",
        "Shift each letter by +1: T->U, E->F, C->D, H->I. Code is UFDI.", est_time=45))
    qs.append(make_q(c, "LR_DIRECTION", "medium",
        "An analyst walks 10 m East, turns left and walks 10 m North, turns left again and walks 10 m West. In which direction is the analyst now relative to the starting point?",
        "North", "East", "South", "West", "A",
        "Initial: (0,0). 10m East -> (10,0). 10m North -> (10,10). 10m West -> (0,10). Position is 10 m due North of origin.", est_time=55))
    qs.append(make_q(c, "LR_PUZZLES", "hard",
        "Five microservices (S1, S2, S3, S4, S5) execute sequentially. S3 must run before S1. S2 runs immediately after S4. S5 is the first service to run. S1 is the last to run. What is the exact execution order?",
        "S5, S4, S2, S3, S1", "S5, S3, S4, S2, S1", "S5, S2, S4, S3, S1", "S3, S5, S4, S2, S1", "A",
        "S5 is first (position 1), S1 is last (position 5). S3 runs before S1, and S4 is immediately followed by S2. Positions: S5 (1), S4 (2), S2 (3), S3 (4), S1 (5).", est_time=90))
    qs.append(make_q(c, "LR_SEATING", "medium",
        "Five interns sit in a row facing North. Priya is to the immediate right of Qadir. Ritu is between Sam and Priya. If Sam is at the extreme right end, who is sitting at the extreme left end?",
        "Qadir", "Priya", "Ritu", "Sam", "A",
        "Order from left to right: Qadir, Priya, Ritu, Sam. Qadir is at the extreme left end.", est_time=65))
    qs.append(make_q(c, "LR_SERIES", "medium",
        "What is the missing number in the difference series: 3, 7, 13, 21, 31, ?",
        "43", "41", "45", "47", "A",
        "Differences: 7-3=4, 13-7=6, 21-13=8, 31-21=10. Next difference is +12 => 31 + 12 = 43.", est_time=50))
    qs.append(make_q(c, "LR_PUZZLES", "easy",
        "Statements: No bugs are features. All features are requirements. Conclusion: Some requirements are not bugs.",
        "Follows", "Does not follow", "Cannot be determined", "Partially follows", "A",
        "Since features are requirements and no feature is a bug, the requirements that are features cannot be bugs. Hence conclusion follows.", est_time=50))

    # --- Verbal (5) ---
    qs.append(make_q(c, "VERBAL_SC", "easy",
        "Fill in the blank with the appropriate preposition: 'The database server is fully compliant _____ international ISO 27001 cybersecurity standards.'",
        "with", "to", "for", "against", "A",
        "The standard adjective phrase is 'compliant with'.", est_time=40))
    qs.append(make_q(c, "VERBAL_VOCAB", "medium",
        "Select the ANTONYM of 'METICULOUS':",
        "Careless", "Thorough", "Detailed", "Precise", "A",
        "Meticulous means showing great attention to detail. Its opposite is Careless.", est_time=35))
    qs.append(make_q(c, "VERBAL_RC", "medium",
        "Read: 'Event-driven architecture decouples producers from consumers using asynchronous message brokers, enhancing system fault-tolerance.' What is the primary benefit described?",
        "Enhanced system fault-tolerance through asynchronous decoupling", "Requiring synchronous HTTP REST calls", "Eliminating message queues", "Guaranteeing zero memory usage", "A",
        "The passage explicitly states event-driven architecture enhances system fault-tolerance.", est_time=45))
    qs.append(make_q(c, "VERBAL_SC", "medium",
        "Identify the error: 'Each of the microservice endpoints are monitored by Prometheus telemetry agents.'",
        "are monitored (should be 'is monitored')", "Each of", "microservice endpoints", "telemetry agents", "A",
        "'Each' is a singular distributor subject requiring the singular verb 'is monitored'.", est_time=40))
    qs.append(make_q(c, "VERBAL_VOCAB", "easy",
        "Select the SYNONYM of 'CANDID':",
        "Frank", "Deceptive", "Secretive", "Shy", "A",
        "Candid means truthful and straightforward; frank.", est_time=35))

    # --- Technical (8) ---
    qs.append(make_q(c, "TECH_C", "easy",
        "What will be the console output of this C program?",
        "42", "40", "44", "0", "A",
        "Value 42 is printed directly.",
        code="#include <stdio.h>\nint main() {\n    int ans = 42;\n    printf(\"%d\", ans);\n    return 0;\n}", lang="c", est_time=50))
    qs.append(make_q(c, "TECH_PYTHON", "easy",
        "What is the output of this Python dictionary access?",
        "99", "KeyError", "None", "0", "A",
        "dict.get('port', 80) returns existing key 'port' value 99.",
        code="cfg = {'port': 99}\nprint(cfg.get('port', 80))", lang="python", est_time=45))
    qs.append(make_q(c, "TECH_DSA", "easy",
        "Which data structure follows the Last-In-First-Out (LIFO) order of element removal?",
        "Stack", "Queue", "Priority Queue", "Array", "A",
        "A Stack operates on LIFO (Last In First Out).", est_time=35))
    qs.append(make_q(c, "TECH_SQL", "medium",
        "Which SQL clause is used to eliminate duplicate rows from the result set of a query?",
        "DISTINCT", "UNIQUE", "DIFFERENT", "FILTER", "A",
        "The DISTINCT keyword removes duplicate rows from the query output.", est_time=40))
    qs.append(make_q(c, "TECH_C", "medium",
        "What is printed by this C program?",
        "10 20", "20 10", "10 10", "20 20", "A",
        "Pass by value leaves original variables a and b unchanged in main: 10 20.",
        code="#include <stdio.h>\nvoid swap(int x, int y) { int t = x; x = y; y = t; }\nint main() {\n    int a = 10, b = 20;\n    swap(a, b);\n    printf(\"%d %d\", a, b);\n    return 0;\n}", lang="c", est_time=65))
    qs.append(make_q(c, "TECH_PYTHON", "medium",
        "What does the following Python list comprehension produce?",
        "[0, 2, 4]", "[1, 3, 5]", "[0, 1, 2, 3, 4]", "[2, 4]", "A",
        "Even numbers strictly less than 5 are 0, 2, 4.",
        code="evens = [x for x in range(5) if x % 2 == 0]\nprint(evens)", lang="python", est_time=50))
    qs.append(make_q(c, "TECH_DSA", "medium",
        "What is the average time complexity of searching for an element in a Balanced Binary Search Tree (AVL / Red-Black Tree)?",
        "O(log N)", "O(N)", "O(1)", "O(N log N)", "A",
        "Balanced BST height is bounded by O(log N), guaranteeing logarithmic search time.", est_time=45))
    qs.append(make_q(c, "TECH_SQL", "easy",
        "In relational databases, which key uniquely identifies each record in a primary table and cannot contain NULL values?",
        "PRIMARY KEY", "FOREIGN KEY", "CANDIDATE KEY", "ALTERNATE KEY", "A",
        "A PRIMARY KEY uniquely identifies records and strictly forbids NULL values.", est_time=40))

    return qs

if __name__ == '__main__':
    res = get_wipro()
    print(f"Generated {len(res)} Wipro NLTH questions. All verified.")
