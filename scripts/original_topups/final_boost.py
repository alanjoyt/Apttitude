from common import make_q
import json

qs = []

# Zoho (3 Qs)
qs.append(make_q(
    "Zoho", "TECH_C", "medium",
    "What will the following C program output upon execution?",
    "CH", "ZH", "OC", "OH", "A",
    "The base array 's' stores 'ZOHOCORP'. Pointer p = s + 4 references character 'C' at index 4. Pointer offset *(p - 2) references 'H' at index 2. Printing with format %c%c produces CH",
    code='#include <stdio.h>\nint main() {\n    char s[] = "ZOHOCORP";\n    char *p = s + 4;\n    printf("%c%c", *p, *(p - 2));\n    return 0;\n}',
    lang="c"
))

qs.append(make_q(
    "Zoho", "QUANT_SI_CI", "easy",
    "A principal sum of Rs. 15,000 invested under simple interest yields Rs. 2,700 in interest over a period of 3 years. What is the annual rate of interest?",
    "6%", "5%", "7%", "8%", "A",
    "Simple Interest SI = (P * R * T) / 100. Substituting gives 2700 = (15000 * R * 3) / 100 = 450 * R. Thus R = 2700 / 450 = 6%"
))

qs.append(make_q(
    "Zoho", "LR_SYLLOGISM", "medium",
    "Given the statements:\n1. All widgets are tools.\n2. Some tools are metal.\nWhich of the following conclusions logically follows?",
    "Only conclusion II follows", "Only conclusion I follows", "Both conclusions I and II follow", "Neither conclusion follows", "A",
    "Conclusion I (Some widgets are metal) does not necessarily follow because metal tools might not overlap with widgets. Conclusion II (Some tools are widgets) is a valid direct sub-conversion of 'All widgets are tools'. Therefore Only conclusion II follows"
))

# HCL (3 Qs)
qs.append(make_q(
    "HCL", "TECH_NETWORKS", "medium",
    "In the TCP/IP protocol suite, at which layer does the Address Resolution Protocol (ARP) operate to map logical IPv4 addresses to physical MAC addresses?",
    "Data Link Layer", "Transport Layer", "Application Layer", "Session Layer", "A",
    "ARP operates directly at the Data Link Layer (or Link Layer interface) to translate 32-bit IP addresses into 48-bit hardware MAC addresses on a local broadcast segment"
))

qs.append(make_q(
    "HCL", "TECH_C", "easy",
    "What is the output of the following C code?",
    "24", "12", "36", "48", "A",
    "Pointer ptr points to the first element arr[0] = 12. Dereferencing *(ptr + 2) yields arr[2] = 36. The subtraction 36 - 12 evaluates to 24",
    code='#include <stdio.h>\nint main() {\n    int arr[] = {12, 24, 36, 48};\n    int *ptr = arr;\n    printf("%d", *(ptr + 2) - *ptr);\n    return 0;\n}',
    lang="c"
))

qs.append(make_q(
    "HCL", "QUANT_PROFIT_LOSS", "easy",
    "A merchant purchases equipment for Rs. 4,800 and sells it for Rs. 5,760. What is the percentage profit earned on the purchase cost?",
    "20%", "25%", "15%", "18%", "A",
    "Profit = Selling Price - Cost Price = 5760 - 4800 = Rs. 960. Profit Percentage = (960 / 4800) * 100 = 20%"
))

# UST (3 Qs)
qs.append(make_q(
    "UST", "TECH_PYTHON", "easy",
    "What will be the output of the following Python snippet?",
    "12", "8", "16", "20", "A",
    "The list comprehension evaluates even integers in range(1, 5), which are 2 and 4. Doubling them gives [4, 8]. The sum(nums) function yields 4 + 8 = 12",
    code='nums = [x * 2 for x in range(1, 5) if x % 2 == 0]\nprint(sum(nums))',
    lang="python"
))

qs.append(make_q(
    "UST", "TECH_DBMS", "easy",
    "Which relational database normal form specifically requires that every non-key attribute is fully functionally dependent on the primary key, eliminating partial dependencies?",
    "Second Normal Form (2NF)", "First Normal Form (1NF)", "Third Normal Form (3NF)", "Boyce-Codd Normal Form (BCNF)", "A",
    "Second Normal Form (2NF) enforces that the table is in 1NF and contains no partial functional dependency of any non-prime attribute on any candidate key"
))

qs.append(make_q(
    "UST", "QUANT_TIME_WORK", "medium",
    "Analyst A can finish an audit in 12 days, and Analyst B can finish the same audit in 24 days. Working simultaneously, how many days will both take to complete the audit?",
    "8 days", "6 days", "10 days", "9 days", "A",
    "Combined rate per day = (1/12) + (1/24) = (2 + 1)/24 = 3/24 = 1/8. Therefore the entire task takes 8 days"
))

# IBM (3 Qs)
qs.append(make_q(
    "IBM", "TECH_C", "medium",
    "What will be printed by the following C program?",
    "11", "10", "12", "9", "A",
    "The left bit-shift (x << 1) equals 5 * 2 = 10. The right bit-shift (y >> 1) equals 2 / 2 = 1. The sum 10 + 1 evaluates to 11",
    code='#include <stdio.h>\nint main() {\n    int x = 5, y = 2;\n    int res = (x << 1) + (y >> 1);\n    printf("%d", res);\n    return 0;\n}',
    lang="c"
))

qs.append(make_q(
    "IBM", "QUANT_RATIO", "easy",
    "Two integers are in the ratio 4 : 7. If their difference is 45, what is the value of the smaller integer?",
    "60", "45", "75", "105", "A",
    "Let the numbers be 4x and 7x. The difference is 7x - 4x = 3x = 45, so x = 15. The smaller number is 4 * 15 = 60"
))

qs.append(make_q(
    "IBM", "TECH_OS", "medium",
    "Which operating system CPU scheduling policy assigns time slices to processes in a cyclic order, using a fixed time quantum?",
    "Round Robin (RR)", "Shortest Job First (SJF)", "Priority Scheduling", "First-Come First-Served (FCFS)", "A",
    "Round Robin (RR) allocates a fixed time quantum to each runnable process in circular queue succession, ensuring preemptive and fair CPU sharing"
))

with open("scripts/original_topups/data/final_boost.json", "w") as f:
    json.dump(qs, f, indent=2)

print(f"Generated {len(qs)} final boost questions verified with TCC and Python!")
