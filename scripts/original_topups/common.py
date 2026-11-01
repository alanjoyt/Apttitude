import subprocess
import tempfile
import os

TCC_PATH = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..", "tools", "tcc", "tcc", "tcc.exe"))

def verify_c_snippet(code, expected_output):
    """Executes C code via TCC and asserts output matches expected."""
    with tempfile.NamedTemporaryFile('w', suffix='.c', delete=False) as f:
        # Wrap if no main
        if 'main(' not in code and 'main (' not in code:
            code = "#include <stdio.h>\nint main() {\n" + code + "\nreturn 0;\n}"
        elif '<stdio.h>' not in code:
            code = "#include <stdio.h>\n" + code
        f.write(code)
        temp_path = f.name

    try:
        res = subprocess.run([TCC_PATH, "-run", temp_path], capture_output=True, text=True, timeout=2)
        out = res.stdout.strip()
        if out != expected_output.strip() and expected_output.strip() not in out:
            raise ValueError(f"C execution mismatch: got '{out}', expected '{expected_output}'")
        return True
    finally:
        if os.path.exists(temp_path):
            os.remove(temp_path)

def verify_py_snippet(code, expected_output):
    """Executes Python code and asserts output matches expected."""
    res = subprocess.run(["python", "-c", code], capture_output=True, text=True, timeout=2)
    out = res.stdout.strip()
    if out != expected_output.strip() and expected_output.strip() not in out:
        raise ValueError(f"Python execution mismatch: got '{out}', expected '{expected_output}'")
    return True

def make_q(company, subtopic, diff, stem, a, b, c, d, correct, expl, code=None, lang=None, est_time=60):
    diff = diff.lower()
    if diff not in ['easy', 'medium', 'hard']:
        diff = 'medium'
    correct = correct.upper()
    opts = {'A': a, 'B': b, 'C': c, 'D': d}
    ans = opts[correct]

    # Verify options are unique
    opt_set = {str(a).strip(), str(b).strip(), str(c).strip(), str(d).strip()}
    if len(opt_set) != 4:
        raise ValueError(f"Duplicate options in question: {stem} -> {opt_set}")

    # Verify code snippets
    if code and lang == 'c':
        verify_c_snippet(code, ans)
    elif code and lang == 'python':
        verify_py_snippet(code, ans)

    if f"(Option {correct})" not in expl:
        expl = expl.rstrip('.') + f". Therefore, the correct answer is {ans} (Option {correct})."

    return {
        "company_name": company,
        "subtopic_code": subtopic,
        "difficulty": diff,
        "question_text": stem,
        "option_a": str(a),
        "option_b": str(b),
        "option_c": str(c),
        "option_d": str(d),
        "correct_option": correct,
        "explanation": expl,
        "code_snippet": code,
        "language": lang,
        "est_time_seconds": est_time,
        "verification_status": "practice_style",
        "round_pattern": f"{company} Placement Assessment (reported pattern, please verify)"
    }
