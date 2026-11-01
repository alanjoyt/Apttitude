path = "public/js/app.js"
with open(path, "r", encoding="utf-8") as f:
    text = f.read()

is_crlf = "\r\n" in text
nl = "\r\n" if is_crlf else "\n"

# 1. Update loadNextQuestion reset
old_reset = (
    "        this.selectedOption = null;" + nl +
    "        this.hintUnlockedQuestionId = null;"
)
new_reset = (
    "        this.selectedOption = null;" + nl +
    "        this.hintUnlockedQuestionId = null;" + nl +
    "        this.eliminatedOption = null;"
)
if old_reset in text and "this.eliminatedOption = null;" not in text:
    text = text.replace(old_reset, new_reset)
    print("Updated loadNextQuestion reset.")

# 2. Update options reset in loadNextQuestion
old_opts_reset = (
    "        document.querySelectorAll('.option-btn').forEach(b => {" + nl +
    "            b.classList.remove('selected', 'correct', 'wrong');" + nl +
    "            b.disabled = false;" + nl +
    "        });"
)
new_opts_reset = (
    "        document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(b => {" + nl +
    "            b.classList.remove('selected', 'correct', 'wrong', 'eliminated');" + nl +
    "            b.disabled = false;" + nl +
    "            b.removeAttribute('aria-disabled');" + nl +
    "            const bBadge = b.querySelector('.eliminated-badge');" + nl +
    "            if (bBadge) bBadge.remove();" + nl +
    "        });"
)
if old_opts_reset in text:
    text = text.replace(old_opts_reset, new_opts_reset)
    print("Updated loadNextQuestion options reset.")

# 3. Add applyEliminatedOption and update openHint
old_open_hint = (
    "    openHint() {" + nl +
    "        if (!this.currentQuestion) {" + nl +
    "            alert('Question is loading, please wait a moment.');" + nl +
    "            return;" + nl +
    "        }" + nl +
    nl +
    "        // If already unlocked for this question, open modal without charging again" + nl +
    "        if (this.hintUnlockedQuestionId === this.currentQuestion.id) {" + nl +
    "            const hintModalEl = document.getElementById('hintModal');" + nl +
    "            if (hintModalEl) {" + nl +
    "                const modal = bootstrap.Modal.getOrCreateInstance(hintModalEl);" + nl +
    "                modal.show();" + nl +
    "            }" + nl +
    "            return;" + nl +
    "        }" + nl +
    nl +
    "        const hintBtn = document.getElementById('formulaHintBtn');" + nl +
    "        const origText = hintBtn ? hintBtn.innerHTML : '';" + nl +
    "        if (hintBtn) {" + nl +
    "            hintBtn.disabled = true;" + nl +
    "            hintBtn.innerHTML = '<span class=\"spinner-border spinner-border-sm me-1\"></span> Unlocking...';" + nl +
    "        }" + nl +
    nl +
    "        fetch('index.php?action=api_use_hint', {" + nl +
    "            method: 'POST'," + nl +
    "            headers: { 'Content-Type': 'application/json' }," + nl +
    "            body: JSON.stringify({ question_id: this.currentQuestion.id, csrf_token: window.CSRF_TOKEN || '' })" + nl +
    "        })" + nl +
    "        .then(res => res.json())" + nl +
    "        .then(data => {" + nl +
    "            if (data.status === 'insufficient_coins') {" + nl +
    "                alert(data.message || 'You need at least 2 Speed Coins to unlock this Formula Hint!');" + nl +
    "                return;" + nl +
    "            }" + nl +
    "            if (data.status !== 'success') {" + nl +
    "                alert(data.message || 'Unable to unlock hint.');" + nl +
    "                return;" + nl +
    "            }" + nl +
    nl +
    "            // Update user coins on navbar if logged in" + nl +
    "            if (typeof data.new_coin_balance !== 'undefined') {" + nl +
    "                const userCoinsEl = document.getElementById('userCoinsDisplay');" + nl +
    "                if (userCoinsEl) {" + nl +
    "                    userCoinsEl.innerText = Number(data.new_coin_balance).toLocaleString();" + nl +
    "                }" + nl +
    "            }" + nl +
    nl +
    "            // Populate hint modal content" + nl +
    "            if (data.hint) {" + nl +
    "                const titleEl = document.getElementById('hintModalTitle');" + nl +
    "                const formulaEl = document.getElementById('hintModalFormula');" + nl +
    "                const strategyEl = document.getElementById('hintModalStrategy');" + nl +
    nl +
    "                if (titleEl && data.hint.title) {" + nl +
    "                    titleEl.innerHTML = `<i class=\"bi bi-lightbulb-fill me-2\" style=\"color: var(--highlight);\"></i> ${data.hint.title}`;" + nl +
    "                }" + nl +
    "                if (formulaEl && data.hint.formula) {" + nl +
    "                    formulaEl.innerText = data.hint.formula;" + nl +
    "                }" + nl +
    "                if (strategyEl && data.hint.strategy) {" + nl +
    "                    strategyEl.innerText = data.hint.strategy;" + nl +
    "                }" + nl +
    "            }" + nl +
    nl +
    "            // Mark unlocked for current question" + nl +
    "            this.hintUnlockedQuestionId = this.currentQuestion.id;" + nl +
    "            if (typeof SoundFX !== 'undefined' && SoundFX.playCoin) {" + nl +
    "                SoundFX.playCoin();" + nl +
    "            }" + nl +
    nl +
    "            const hintModalEl = document.getElementById('hintModal');" + nl +
    "            if (hintModalEl) {" + nl +
    "                const modal = bootstrap.Modal.getOrCreateInstance(hintModalEl);" + nl +
    "                modal.show();" + nl +
    "            }" + nl +
    "        })" + nl +
    "        .catch(err => {" + nl +
    "            console.error(err);" + nl +
    "            alert('Failed to connect to server. Please try again.');" + nl +
    "        })" + nl +
    "        .finally(() => {" + nl +
    "            if (hintBtn) {" + nl +
    "                hintBtn.disabled = false;" + nl +
    "                hintBtn.innerHTML = origText;" + nl +
    "            }" + nl +
    "        });" + nl +
    "    }"
)

new_open_hint = (
    "    applyEliminatedOption(optKey, optText) {" + nl +
    "        if (!optKey) return;" + nl +
    "        const btn = document.querySelector(`.option-btn[data-option=\"${optKey}\"], .tech-opt-btn[data-option=\"${optKey}\"]`);" + nl +
    "        if (btn) {" + nl +
    "            btn.classList.add('eliminated');" + nl +
    "            btn.classList.remove('selected');" + nl +
    "            btn.disabled = true;" + nl +
    "            btn.setAttribute('aria-disabled', 'true');" + nl +
    nl +
    "            // If user had previously selected this option, clear selection" + nl +
    "            if (this.selectedOption === optKey) {" + nl +
    "                this.selectedOption = null;" + nl +
    "                const submitBtn = document.getElementById('submitBtn') || document.getElementById('techSubmitBtn');" + nl +
    "                if (submitBtn) {" + nl +
    "                    submitBtn.disabled = true;" + nl +
    "                    submitBtn.setAttribute('aria-disabled', 'true');" + nl +
    "                }" + nl +
    "            }" + nl +
    nl +
    "            // Add ELIMINATED badge if not present" + nl +
    "            if (!btn.querySelector('.eliminated-badge')) {" + nl +
    "                const badge = document.createElement('span');" + nl +
    "                badge.className = 'badge bg-danger-subtle text-danger extra-small ms-auto font-monospace eliminated-badge';" + nl +
    "                badge.style.fontSize = '0.72rem';" + nl +
    "                badge.style.letterSpacing = '0.04em';" + nl +
    "                badge.innerHTML = '❌ ELIMINATED';" + nl +
    "                btn.appendChild(badge);" + nl +
    "            }" + nl +
    "        }" + nl +
    "    }," + nl +
    nl +
    "    openHint() {" + nl +
    "        if (!this.currentQuestion) {" + nl +
    "            alert('Question is loading, please wait a moment.');" + nl +
    "            return;" + nl +
    "        }" + nl +
    nl +
    "        // If already unlocked for this question, reopen modal with the already eliminated option" + nl +
    "        if (this.hintUnlockedQuestionId === this.currentQuestion.id && this.eliminatedOption) {" + nl +
    "            this.applyEliminatedOption(this.eliminatedOption);" + nl +
    "            const hintModalEl = document.getElementById('hintModal');" + nl +
    "            if (hintModalEl) {" + nl +
    "                const modal = bootstrap.Modal.getOrCreateInstance(hintModalEl);" + nl +
    "                modal.show();" + nl +
    "            }" + nl +
    "            return;" + nl +
    "        }" + nl +
    nl +
    "        const hintBtn = document.getElementById('formulaHintBtn');" + nl +
    "        const origText = hintBtn ? hintBtn.innerHTML : '';" + nl +
    "        if (hintBtn) {" + nl +
    "            hintBtn.disabled = true;" + nl +
    "            hintBtn.innerHTML = '<span class=\"spinner-border spinner-border-sm me-1\"></span> Eliminating...';" + nl +
    "        }" + nl +
    nl +
    "        fetch('index.php?action=api_use_hint', {" + nl +
    "            method: 'POST'," + nl +
    "            headers: { 'Content-Type': 'application/json' }," + nl +
    "            body: JSON.stringify({ question_id: this.currentQuestion.id, csrf_token: window.CSRF_TOKEN || '' })" + nl +
    "        })" + nl +
    "        .then(res => res.json())" + nl +
    "        .then(data => {" + nl +
    "            if (data.status === 'insufficient_coins') {" + nl +
    "                alert(data.message || 'You need at least 5 Speed Coins to eliminate a wrong option!');" + nl +
    "                return;" + nl +
    "            }" + nl +
    "            if (data.status !== 'success') {" + nl +
    "                alert(data.message || 'Unable to eliminate option.');" + nl +
    "                return;" + nl +
    "            }" + nl +
    nl +
    "            // Update user coins on navbar if logged in" + nl +
    "            if (typeof data.new_coin_balance !== 'undefined') {" + nl +
    "                const userCoinsEl = document.getElementById('userCoinsDisplay');" + nl +
    "                if (userCoinsEl) {" + nl +
    "                    userCoinsEl.innerText = Number(data.new_coin_balance).toLocaleString();" + nl +
    "                }" + nl +
    "            }" + nl +
    nl +
    "            // Save state for this question" + nl +
    "            this.hintUnlockedQuestionId = this.currentQuestion.id;" + nl +
    "            this.eliminatedOption = data.eliminated_option;" + nl +
    nl +
    "            // Apply visual strike-through to the eliminated button on the board" + nl +
    "            this.applyEliminatedOption(data.eliminated_option, data.eliminated_text);" + nl +
    nl +
    "            if (typeof SoundFX !== 'undefined' && SoundFX.playCoin) {" + nl +
    "                SoundFX.playCoin();" + nl +
    "            }" + nl +
    nl +
    "            // Populate hint modal content" + nl +
    "            const headingEl = document.getElementById('hintModalEliminatedHeading');" + nl +
    "            const textEl = document.getElementById('hintModalEliminatedText');" + nl +
    "            const tipEl = document.getElementById('hintModalTip');" + nl +
    "            const badgeEl = document.getElementById('hintModalStatusBadge');" + nl +
    nl +
    "            if (headingEl) {" + nl +
    "                headingEl.innerHTML = `❌ Option ${data.eliminated_option} Eliminated!`;" + nl +
    "            }" + nl +
    "            if (textEl && data.eliminated_text) {" + nl +
    "                textEl.innerText = `\"${data.eliminated_text}\" is incorrect.`;" + nl +
    "            }" + nl +
    "            if (tipEl && data.hint && data.hint.tip) {" + nl +
    "                tipEl.innerText = data.hint.tip;" + nl +
    "            }" + nl +
    "            if (badgeEl) {" + nl +
    "                badgeEl.innerHTML = `<svg class=\"exam-icon me-0.5 icon-11\"><use href=\"#icon-check\"></use></svg> Option ${data.eliminated_option} Eliminated`;" + nl +
    "            }" + nl +
    nl +
    "            const hintModalEl = document.getElementById('hintModal');" + nl +
    "            if (hintModalEl) {" + nl +
    "                const modal = bootstrap.Modal.getOrCreateInstance(hintModalEl);" + nl +
    "                modal.show();" + nl +
    "            }" + nl +
    "        })" + nl +
    "        .catch(err => {" + nl +
    "            console.error(err);" + nl +
    "            alert('Failed to connect to server. Please try again.');" + nl +
    "        })" + nl +
    "        .finally(() => {" + nl +
    "            if (hintBtn) {" + nl +
    "                hintBtn.disabled = false;" + nl +
    "                hintBtn.innerHTML = origText;" + nl +
    "            }" + nl +
    "        });" + nl +
    "    }"
)

if old_open_hint in text:
    text = text.replace(old_open_hint, new_open_hint)
    print("Updated openHint and added applyEliminatedOption in app.js.")

with open(path, "w", encoding="utf-8") as f:
    f.write(text)
print("Saved public/js/app.js.")
