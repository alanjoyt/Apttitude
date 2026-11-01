# Sync app.min.js, style.min.css, theme.min.css

# 1. Update app.min.js
with open("public/js/app.min.js", "r", encoding="utf-8") as f:
    text_min = f.read()

# Replace openHint in app.min.js
if "openHint() {" in text_min and "loadDailyQuests() {" in text_min:
    old_open_min = text_min[text_min.find("openHint() {"):text_min.find("loadDailyQuests() {")]

    new_open_min = """applyEliminatedOption(optKey, optText) {
if (!optKey) return;
const btn = document.querySelector(`.option-btn[data-option="${optKey}"], .tech-opt-btn[data-option="${optKey}"]`);
if (btn) {
btn.classList.add('eliminated');
btn.classList.remove('selected');
btn.disabled = true;
btn.setAttribute('aria-disabled', 'true');
if (this.selectedOption === optKey) {
this.selectedOption = null;
const submitBtn = document.getElementById('submitBtn');
if (submitBtn) {
submitBtn.disabled = true;
submitBtn.setAttribute('aria-disabled', 'true');
}
}
if (!btn.querySelector('.eliminated-badge')) {
const badge = document.createElement('span');
badge.className = 'badge bg-danger-subtle text-danger extra-small ms-auto font-monospace eliminated-badge';
badge.style.fontSize = '0.72rem';
badge.style.letterSpacing = '0.04em';
badge.innerHTML = '❌ ELIMINATED';
btn.appendChild(badge);
}
}
},
openHint() {
if (!this.currentQuestion) {
alert('Question is loading, please wait a moment.');
return;
}
if (this.hintUnlockedQuestionId === this.currentQuestion.id && this.eliminatedOption) {
this.applyEliminatedOption(this.eliminatedOption);
const hintModalEl = document.getElementById('hintModal');
if (hintModalEl) {
const modal = bootstrap.Modal.getOrCreateInstance(hintModalEl);
modal.show();
}
return;
}
const hintBtn = document.getElementById('formulaHintBtn');
const origText = hintBtn ? hintBtn.innerHTML : '';
if (hintBtn) {
hintBtn.disabled = true;
hintBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Eliminating...';
}
fetch('index.php?action=api_use_hint', {
method: 'POST',
headers: { 'Content-Type': 'application/json' },
body: JSON.stringify({ question_id: this.currentQuestion.id, csrf_token: window.CSRF_TOKEN || '' })
})
.then(res => res.json())
.then(data => {
if (data.status === 'insufficient_coins') {
alert(data.message || 'You need at least 5 Speed Coins to eliminate a wrong option!');
return;
}
if (data.status !== 'success') {
alert(data.message || 'Unable to eliminate option.');
return;
}
if (typeof data.new_coin_balance !== 'undefined') {
const userCoinsEl = document.getElementById('userCoinsDisplay');
if (userCoinsEl) {
userCoinsEl.innerText = Number(data.new_coin_balance).toLocaleString();
}
}
this.hintUnlockedQuestionId = this.currentQuestion.id;
this.eliminatedOption = data.eliminated_option;
this.applyEliminatedOption(data.eliminated_option, data.eliminated_text);
if (typeof SoundFX !== 'undefined' && SoundFX.playCoin) {
SoundFX.playCoin();
}
const headingEl = document.getElementById('hintModalEliminatedHeading');
const textEl = document.getElementById('hintModalEliminatedText');
const tipEl = document.getElementById('hintModalTip');
const badgeEl = document.getElementById('hintModalStatusBadge');
if (headingEl) {
headingEl.innerHTML = `❌ Option ${data.eliminated_option} Eliminated!`;
}
if (textEl && data.eliminated_text) {
textEl.innerText = `"${data.eliminated_text}" is incorrect.`;
}
if (tipEl && data.hint && data.hint.tip) {
tipEl.innerText = data.hint.tip;
}
if (badgeEl) {
badgeEl.innerHTML = `<svg class="exam-icon me-0.5 icon-11"><use href="#icon-check"></use></svg> Option ${data.eliminated_option} Eliminated`;
}
const hintModalEl = document.getElementById('hintModal');
if (hintModalEl) {
const modal = bootstrap.Modal.getOrCreateInstance(hintModalEl);
modal.show();
}
})
.catch(err => {
console.error(err);
alert('Failed to connect to server. Please try again.');
})
.finally(() => {
if (hintBtn) {
hintBtn.disabled = false;
hintBtn.innerHTML = origText;
}
});
},
"""
    if "applyEliminatedOption" not in text_min:
        text_min = text_min.replace(old_open_min, new_open_min)

old_reset_min = "this.hintUnlockedQuestionId = null;"
if old_reset_min in text_min and "this.eliminatedOption = null;" not in text_min:
    text_min = text_min.replace(old_reset_min, "this.hintUnlockedQuestionId = null; this.eliminatedOption = null;", 1)

old_btn_reset_min = "b.classList.remove('selected', 'correct', 'wrong');"
if old_btn_reset_min in text_min and "'eliminated'" not in text_min:
    text_min = text_min.replace(old_btn_reset_min, "b.classList.remove('selected', 'correct', 'wrong', 'eliminated'); b.removeAttribute('aria-disabled'); const bB = b.querySelector('.eliminated-badge'); if (bB) bB.remove();", 1)

with open("public/js/app.min.js", "w", encoding="utf-8") as f:
    f.write(text_min)
print("Updated public/js/app.min.js successfully!")

# 2. Update style.min.css & theme.min.css with .eliminated
elim_css = """.option-btn.eliminated,.tech-opt-btn.eliminated{opacity:.38!important;pointer-events:none!important;cursor:not-allowed!important;background-color:var(--surface)!important;border:1.5px dashed var(--border)!important;box-shadow:none!important;transform:none!important;filter:grayscale(85%)!important;position:relative!important}.option-btn.eliminated .option-text,.tech-opt-btn.eliminated .option-text{text-decoration:line-through!important;color:var(--ink-muted)!important}.option-btn.eliminated .option-key,.tech-opt-btn.eliminated .option-key{opacity:.45!important;text-decoration:line-through!important;border-style:dashed!important}"""

with open("public/css/style.min.css", "r+", encoding="utf-8") as f:
    c = f.read()
    if ".option-btn.eliminated" not in c:
        f.write("\n" + elim_css)
print("Checked public/css/style.min.css.")

with open("public/css/theme.min.css", "r+", encoding="utf-8") as f:
    c = f.read()
    if ".option-btn.eliminated" not in c:
        f.write("\n" + elim_css)
print("Checked public/css/theme.min.css.")
