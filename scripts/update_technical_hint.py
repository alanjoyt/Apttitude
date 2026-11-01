# Update quiz_shell.js and technical.php

# 1. Update quiz_shell.js
path_qs = "public/js/quiz_shell.js"
with open(path_qs, "r", encoding="utf-8") as f:
    text_qs = f.read()

is_crlf_qs = "\r\n" in text_qs
nl_qs = "\r\n" if is_crlf_qs else "\n"

old_qs_func = (
    "function openQuizHint() {" + nl_qs +
    "  if (window.PracticeEngine && typeof PracticeEngine.openHint === 'function') {" + nl_qs +
    "    PracticeEngine.openHint();" + nl_qs +
    "    return;" + nl_qs +
    "  }" + nl_qs +
    "  const hintModalEl = document.getElementById('hintModal') || document.getElementById('formulaModal');" + nl_qs +
    "  if (hintModalEl && window.bootstrap) {" + nl_qs +
    "    bootstrap.Modal.getOrCreateInstance(hintModalEl).show();" + nl_qs +
    "  }" + nl_qs +
    "}"
)

new_qs_func = (
    "function openQuizHint() {" + nl_qs +
    "  if (window.PracticeEngine && typeof PracticeEngine.openHint === 'function') {" + nl_qs +
    "    PracticeEngine.openHint();" + nl_qs +
    "    return;" + nl_qs +
    "  }" + nl_qs +
    "  if (window.TechnicalEngine && typeof TechnicalEngine.openHint === 'function') {" + nl_qs +
    "    TechnicalEngine.openHint();" + nl_qs +
    "    return;" + nl_qs +
    "  }" + nl_qs +
    "  const hintModalEl = document.getElementById('hintModal') || document.getElementById('formulaModal');" + nl_qs +
    "  if (hintModalEl && window.bootstrap) {" + nl_qs +
    "    bootstrap.Modal.getOrCreateInstance(hintModalEl).show();" + nl_qs +
    "  }" + nl_qs +
    "}"
)

if old_qs_func in text_qs:
    text_qs = text_qs.replace(old_qs_func, new_qs_func)
    with open(path_qs, "w", encoding="utf-8") as f:
        f.write(text_qs)
    print("Updated quiz_shell.js successfully!")
else:
    print("quiz_shell.js already up to date.")

# 2. Update technical.php
path_tech = "app/Views/technical.php"
with open(path_tech, "r", encoding="utf-8") as f:
    text_tech = f.read()

is_crlf_tech = "\r\n" in text_tech
nl_t = "\r\n" if is_crlf_tech else "\n"

# Properties
old_tech_props = (
    "  countdownInterval: null," + nl_t +
    "  countdownSeconds: 10," + nl_t +
    "  seenQuestionIds: [],"
)
new_tech_props = (
    "  countdownInterval: null," + nl_t +
    "  countdownSeconds: 10," + nl_t +
    "  seenQuestionIds: []," + nl_t +
    "  hintUnlockedQuestionId: null," + nl_t +
    "  eliminatedOption: null,"
)
if old_tech_props in text_tech and "hintUnlockedQuestionId" not in text_tech:
    text_tech = text_tech.replace(old_tech_props, new_tech_props)
    print("Added hint properties to TechnicalEngine.")

# Options reset in loadNextQuestion
old_tech_opts = (
    "    document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(b => {" + nl_t +
    "      b.classList.remove('selected', 'correct', 'wrong');" + nl_t +
    "      b.disabled = false;" + nl_t +
    "    });"
)
new_tech_opts = (
    "    this.hintUnlockedQuestionId = null;" + nl_t +
    "    this.eliminatedOption = null;" + nl_t +
    "    document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(b => {" + nl_t +
    "      b.classList.remove('selected', 'correct', 'wrong', 'eliminated');" + nl_t +
    "      b.disabled = false;" + nl_t +
    "      b.removeAttribute('aria-disabled');" + nl_t +
    "      const bBadge = b.querySelector('.eliminated-badge');" + nl_t +
    "      if (bBadge) bBadge.remove();" + nl_t +
    "    });"
)
if old_tech_opts in text_tech:
    text_tech = text_tech.replace(old_tech_opts, new_tech_opts)
    print("Updated options reset in TechnicalEngine.")

# Add applyEliminatedOption and openHint before window assignment / at the end of TechnicalEngine
target_bind = "  bindEvents() {"
tech_hint_methods = (
    "  applyEliminatedOption(optKey, optText) {" + nl_t +
    "    if (!optKey) return;" + nl_t +
    "    const btn = document.querySelector(`.option-btn[data-option=\"${optKey}\"], .tech-opt-btn[data-option=\"${optKey}\"]`);" + nl_t +
    "    if (btn) {" + nl_t +
    "      btn.classList.add('eliminated');" + nl_t +
    "      btn.classList.remove('selected');" + nl_t +
    "      btn.disabled = true;" + nl_t +
    "      btn.setAttribute('aria-disabled', 'true');" + nl_t +
    nl_t +
    "      if (this.selectedOption === optKey) {" + nl_t +
    "        this.selectedOption = null;" + nl_t +
    "        const submitBtn = document.getElementById('submitBtn') || document.getElementById('techSubmitBtn');" + nl_t +
    "        if (submitBtn) {" + nl_t +
    "          submitBtn.disabled = true;" + nl_t +
    "          submitBtn.setAttribute('aria-disabled', 'true');" + nl_t +
    "        }" + nl_t +
    "      }" + nl_t +
    nl_t +
    "      if (!btn.querySelector('.eliminated-badge')) {" + nl_t +
    "        const badge = document.createElement('span');" + nl_t +
    "        badge.className = 'badge bg-danger-subtle text-danger extra-small ms-auto font-monospace eliminated-badge';" + nl_t +
    "        badge.style.fontSize = '0.72rem';" + nl_t +
    "        badge.style.letterSpacing = '0.04em';" + nl_t +
    "        badge.innerHTML = '❌ ELIMINATED';" + nl_t +
    "        btn.appendChild(badge);" + nl_t +
    "      }" + nl_t +
    "    }" + nl_t +
    "  }," + nl_t +
    nl_t +
    "  openHint() {" + nl_t +
    "    if (!this.currentQuestion) {" + nl_t +
    "      alert('Question is loading, please wait a moment.');" + nl_t +
    "      return;" + nl_t +
    "    }" + nl_t +
    nl_t +
    "    if (this.hintUnlockedQuestionId === this.currentQuestion.id && this.eliminatedOption) {" + nl_t +
    "      this.applyEliminatedOption(this.eliminatedOption);" + nl_t +
    "      const hintModalEl = document.getElementById('hintModal');" + nl_t +
    "      if (hintModalEl) {" + nl_t +
    "        bootstrap.Modal.getOrCreateInstance(hintModalEl).show();" + nl_t +
    "      }" + nl_t +
    "      return;" + nl_t +
    "    }" + nl_t +
    nl_t +
    "    const hintBtn = document.getElementById('formulaHintBtn');" + nl_t +
    "    const origText = hintBtn ? hintBtn.innerHTML : '';" + nl_t +
    "    if (hintBtn) {" + nl_t +
    "      hintBtn.disabled = true;" + nl_t +
    "      hintBtn.innerHTML = '<span class=\"spinner-border spinner-border-sm me-1\"></span> Eliminating...';" + nl_t +
    "    }" + nl_t +
    nl_t +
    "    fetch('index.php?action=api_use_hint', {" + nl_t +
    "      method: 'POST'," + nl_t +
    "      headers: { 'Content-Type': 'application/json' }," + nl_t +
    "      body: JSON.stringify({ question_id: this.currentQuestion.id, csrf_token: window.CSRF_TOKEN || '' })" + nl_t +
    "    })" + nl_t +
    "      .then(res => res.json())" + nl_t +
    "      .then(data => {" + nl_t +
    "        if (data.status === 'insufficient_coins') {" + nl_t +
    "          alert(data.message || 'You need at least 5 Speed Coins to eliminate a wrong option!');" + nl_t +
    "          return;" + nl_t +
    "        }" + nl_t +
    "        if (data.status !== 'success') {" + nl_t +
    "          alert(data.message || 'Unable to eliminate option.');" + nl_t +
    "          return;" + nl_t +
    "        }" + nl_t +
    nl_t +
    "        if (typeof data.new_coin_balance !== 'undefined') {" + nl_t +
    "          const userCoinsEl = document.getElementById('userCoinsDisplay');" + nl_t +
    "          if (userCoinsEl) {" + nl_t +
    "            userCoinsEl.innerText = Number(data.new_coin_balance).toLocaleString();" + nl_t +
    "          }" + nl_t +
    "        }" + nl_t +
    nl_t +
    "        this.hintUnlockedQuestionId = this.currentQuestion.id;" + nl_t +
    "        this.eliminatedOption = data.eliminated_option;" + nl_t +
    "        this.applyEliminatedOption(data.eliminated_option, data.eliminated_text);" + nl_t +
    nl_t +
    "        if (typeof SoundFX !== 'undefined' && SoundFX.playCoin) {" + nl_t +
    "          SoundFX.playCoin();" + nl_t +
    "        }" + nl_t +
    nl_t +
    "        const headingEl = document.getElementById('hintModalEliminatedHeading');" + nl_t +
    "        const textEl = document.getElementById('hintModalEliminatedText');" + nl_t +
    "        const tipEl = document.getElementById('hintModalTip');" + nl_t +
    "        const badgeEl = document.getElementById('hintModalStatusBadge');" + nl_t +
    nl_t +
    "        if (headingEl) {" + nl_t +
    "          headingEl.innerHTML = `❌ Option ${data.eliminated_option} Eliminated!`;" + nl_t +
    "        }" + nl_t +
    "        if (textEl && data.eliminated_text) {" + nl_t +
    "          textEl.innerText = `\"${data.eliminated_text}\" is incorrect.`;" + nl_t +
    "        }" + nl_t +
    "        if (tipEl && data.hint && data.hint.tip) {" + nl_t +
    "          tipEl.innerText = data.hint.tip;" + nl_t +
    "        }" + nl_t +
    "        if (badgeEl) {" + nl_t +
    "          badgeEl.innerHTML = `<svg class=\"exam-icon me-0.5 icon-11\"><use href=\"#icon-check\"></use></svg> Option ${data.eliminated_option} Eliminated`;" + nl_t +
    "        }" + nl_t +
    nl_t +
    "        const hintModalEl = document.getElementById('hintModal');" + nl_t +
    "        if (hintModalEl) {" + nl_t +
    "          bootstrap.Modal.getOrCreateInstance(hintModalEl).show();" + nl_t +
    "        }" + nl_t +
    "      })" + nl_t +
    "      .catch(err => {" + nl_t +
    "        console.error(err);" + nl_t +
    "        alert('Failed to connect to server. Please try again.');" + nl_t +
    "      })" + nl_t +
    "      .finally(() => {" + nl_t +
    "        if (hintBtn) {" + nl_t +
    "          hintBtn.disabled = false;" + nl_t +
    "          hintBtn.innerHTML = origText;" + nl_t +
    "        }" + nl_t +
    "      });" + nl_t +
    "  }," + nl_t +
    nl_t +
    "  bindEvents() {"
)

if target_bind in text_tech and "openHint() {" not in text_tech:
    text_tech = text_tech.replace(target_bind, tech_hint_methods)
    print("Added hint methods to TechnicalEngine.")

if "window.TechnicalEngine = TechnicalEngine;" not in text_tech:
    text_tech = text_tech.replace("TechnicalEngine.init();", "window.TechnicalEngine = TechnicalEngine;\nTechnicalEngine.init();")
    print("Exposed window.TechnicalEngine.")

with open(path_tech, "w", encoding="utf-8") as f:
    f.write(text_tech)
print("Saved app/Views/technical.php.")
