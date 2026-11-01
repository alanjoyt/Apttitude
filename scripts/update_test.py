path = "tests/economy_test.php"
with open(path, "r", encoding="utf-8") as f:
    text = f.read()

old = 'assertTest("Formula Hint costs 2 coins (was 1)", (int)$cfg[\'store\'][\'hint_cost\'] === 2);'
new = 'assertTest("Option Elimination Hint costs 5 coins (was 2)", (int)$cfg[\'store\'][\'hint_cost\'] === 5);'
if old in text:
    text = text.replace(old, new)
    with open(path, "w", encoding="utf-8") as f:
        f.write(text)
    print("Updated tests/economy_test.php")
else:
    print("tests/economy_test.php already updated.")
