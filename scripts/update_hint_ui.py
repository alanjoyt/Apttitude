import os

path = "app/Views/components/quiz_shell.php"
with open(path, "r", encoding="utf-8") as f:
    text = f.read()

is_crlf = "\r\n" in text
nl = "\r\n" if is_crlf else "\n"

# 1. Update Hint button
old_btn = (
    '        <!-- 1. Hint Button -->' + nl +
    '        <button type="button" class="btn-quiz-util" id="formulaHintBtn" onclick="openQuizHint()" title="Strategy & Formula Hint" aria-label="Strategy & Formula Hint">' + nl +
    '          <svg class="exam-icon text-warning icon-16"><use href="#icon-lightbulb"></use></svg>' + nl +
    '          <span class="btn-quiz-util-label">Hint</span>' + nl +
    '        </button>'
)

new_btn = (
    '        <!-- 1. Hint Button: Lifeline (Eliminate 1 Wrong Option) -->' + nl +
    '        <button type="button" class="btn-quiz-util" id="formulaHintBtn" onclick="openQuizHint()" title="Eliminate 1 Wrong Option (5 Speed Coins)" aria-label="Eliminate 1 Wrong Option">' + nl +
    '          <svg class="exam-icon text-warning icon-16"><use href="#icon-lightbulb"></use></svg>' + nl +
    '          <span class="btn-quiz-util-label">Hint (5¢)</span>' + nl +
    '        </button>'
)

if old_btn in text:
    text = text.replace(old_btn, new_btn)
    print("Replaced Hint button successfully.")

# 2. Update Hint Modal
old_modal = (
    '<!-- Formula & Strategy Hint Modal -->' + nl +
    '<div class="modal fade" id="hintModal" tabindex="-1" aria-labelledby="hintModalTitle" aria-hidden="true">' + nl +
    '  <div class="modal-dialog modal-dialog-centered">' + nl +
    '    <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border: 2px solid var(--border-strong) !important;">' + nl +
    '      <div class="modal-header border-bottom" style="border-color: var(--border) !important;">' + nl +
    '        <h5 class="modal-title font-serif fw-bold" id="hintModalTitle">' + nl +
    '          <svg class="exam-icon text-warning me-2 icon-20"><use href="#icon-lightbulb"></use></svg> Topic Strategy & Formula Hint' + nl +
    '        </h5>' + nl +
    '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' + nl +
    '      </div>' + nl +
    '      <div class="modal-body p-4">' + nl +
    '        <div class="border rounded-3 p-2 mb-3 d-flex align-items-center justify-content-between small" style="background-color: var(--highlight-tint); border: 1.5px solid var(--border-strong); color: var(--ink);">' + nl +
    '          <span><svg class="exam-icon text-warning me-1 icon-14"><use href="#icon-coin"></use></svg> Cost: <strong>2 Speed Coins</strong></span>' + nl +
    '          <span class="badge bg-success text-white"><svg class="exam-icon me-0.5 icon-11"><use href="#icon-check"></use></svg> Unlocked for this question</span>' + nl +
    '        </div>' + nl +
    '        <div class="p-3 rounded-3 mb-3 border" style="background-color: var(--surface); border: 1.5px solid var(--border) !important;">' + nl +
    '          <h6 class="font-serif fw-bold text-ink mb-1"><svg class="exam-icon text-accent me-1 icon-15"><use href="#icon-brain"></use></svg> Core Formula / Rules:</h6>' + nl +
    '          <p class="text-ink-muted small mb-0 lh-base" id="hintModalFormula">' + nl +
    '            Reviewing fundamental relationships and quick conversion shortcuts.' + nl +
    '          </p>' + nl +
    '        </div>' + nl +
    '        <div class="p-3 rounded-3 border" style="background-color: var(--surface); border: 1.5px solid var(--border) !important;">' + nl +
    '          <h6 class="font-serif fw-bold text-ink mb-1"><svg class="exam-icon text-warning me-1 icon-15"><use href="#icon-target"></use></svg> Solving Tip & Strategy:</h6>' + nl +
    '          <p class="text-ink-muted small mb-0 lh-base" id="hintModalStrategy">' + nl +
    '            Identify given constraints, cancel out dimensions, and eliminate extreme answers first.' + nl +
    '          </p>' + nl +
    '        </div>' + nl +
    '      </div>' + nl +
    '      <div class="modal-footer border-top" style="border-color: var(--border) !important;">' + nl +
    '        <button type="button" class="btn btn-primary-gamified btn-sm px-4 fw-bold w-100" data-bs-dismiss="modal">Got it!</button>' + nl +
    '      </div>' + nl +
    '    </div>' + nl +
    '  </div>' + nl +
    '</div>'
)

new_modal = (
    '<!-- Eliminate 1 Wrong Option Lifeline Modal -->' + nl +
    '<div class="modal fade" id="hintModal" tabindex="-1" aria-labelledby="hintModalTitle" aria-hidden="true">' + nl +
    '  <div class="modal-dialog modal-dialog-centered">' + nl +
    '    <div class="modal-content text-ink border rounded-4" style="background-color: var(--surface); border: 2px solid var(--border-strong) !important;">' + nl +
    '      <div class="modal-header border-bottom" style="border-color: var(--border) !important;">' + nl +
    '        <h5 class="modal-title font-serif fw-bold d-flex align-items-center gap-2" id="hintModalTitle">' + nl +
    '          <svg class="exam-icon text-warning icon-20"><use href="#icon-sparkles"></use></svg>' + nl +
    '          <span>Eliminate 1 Wrong Option</span>' + nl +
    '        </h5>' + nl +
    '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' + nl +
    '      </div>' + nl +
    '      <div class="modal-body p-4">' + nl +
    '        <div class="border rounded-3 p-2 mb-3 d-flex align-items-center justify-content-between small" style="background-color: var(--highlight-tint); border: 1.5px solid var(--border-strong); color: var(--ink);">' + nl +
    '          <span><svg class="exam-icon text-warning me-1 icon-14"><use href="#icon-coin"></use></svg> Cost: <strong>5 Speed Coins</strong></span>' + nl +
    '          <span class="badge bg-success text-white" id="hintModalStatusBadge"><svg class="exam-icon me-0.5 icon-11"><use href="#icon-check"></use></svg> 1 Option Eliminated</span>' + nl +
    '        </div>' + nl +
    '        <div class="p-3 rounded-3 mb-3 border text-center" style="background-color: rgba(197, 48, 48, 0.08); border: 1.5px dashed var(--danger, #c53030) !important;">' + nl +
    '          <div class="text-danger fw-bold fs-5 mb-1" id="hintModalEliminatedHeading">' + nl +
    '            ❌ Option Eliminated' + nl +
    '          </div>' + nl +
    '          <p class="text-ink-muted small mb-0 font-monospace" id="hintModalEliminatedText" style="word-break: break-word;">' + nl +
    '            An incorrect option has been removed from this question.' + nl +
    '          </p>' + nl +
    '        </div>' + nl +
    '        <div class="p-3 rounded-3 border" style="background-color: var(--surface); border: 1.5px solid var(--border) !important;">' + nl +
    '          <h6 class="font-serif fw-bold text-ink mb-1 d-flex align-items-center gap-1.5">' + nl +
    '            <svg class="exam-icon text-success icon-15"><use href="#icon-target"></use></svg>' + nl +
    '            <span>3 Options Remaining</span>' + nl +
    '          </h6>' + nl +
    '          <p class="text-ink-muted small mb-0 lh-base" id="hintModalTip">' + nl +
    '            The eliminated option is crossed out and disabled on your question card. You now have a 1-in-3 chance to pick the correct answer!' + nl +
    '          </p>' + nl +
    '        </div>' + nl +
    '      </div>' + nl +
    '      <div class="modal-footer border-top" style="border-color: var(--border) !important;">' + nl +
    '        <button type="button" class="btn btn-primary-gamified btn-sm px-4 fw-bold w-100" data-bs-dismiss="modal">Continue with 3 Options &rarr;</button>' + nl +
    '      </div>' + nl +
    '    </div>' + nl +
    '  </div>' + nl +
    '</div>'
)

if old_modal in text:
    text = text.replace(old_modal, new_modal)
    print("Replaced Hint modal successfully.")

with open(path, "w", encoding="utf-8") as f:
    f.write(text)
print("Saved updated quiz_shell.php.")
