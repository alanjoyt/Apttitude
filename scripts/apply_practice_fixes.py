import re

# 1. Update app/Views/technical.php
with open('app/Views/technical.php', 'r', encoding='utf-8') as f:
    tech_content = f.read()

# Fix hint coins update in technical.php
old_tech_coins = """        if (typeof data.new_coin_balance !== 'undefined') {
          const userCoinsEl = document.getElementById('userCoinsDisplay');
          if (userCoinsEl) {
            userCoinsEl.innerText = Number(data.new_coin_balance).toLocaleString();
          }
        }"""

new_tech_coins = """        if (typeof data.new_coin_balance !== 'undefined') {
          const formatted = Number(data.new_coin_balance).toLocaleString();
          ['userCoinsDisplay', 'userCoinsDisplayMobile', 'dropdownUserCoins'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerText = formatted;
          });
        }"""

if old_tech_coins in tech_content:
    tech_content = tech_content.replace(old_tech_coins, new_tech_coins)
    print("[+] Successfully replaced hint coins in app/Views/technical.php")
else:
    print("[!] Warning: old_tech_coins not found in technical.php")

# Fix question parser rendering in technical.php
old_tech_parser = """        // Question parsing: source tag stamp, setup sentences, prompt
        const parsed = data.parsed_question || QuizShell.parseQuestion(data.question_text);
        const tagWrap = document.getElementById('quizSourceTagWrap');
        const tagStamp = document.getElementById('quizSourceTag');
        if (parsed.sourceTag && tagWrap && tagStamp) {
          tagWrap.style.display = 'block';
          tagStamp.innerText = parsed.sourceTag;
        } else if (tagWrap) {
          tagWrap.style.display = 'none';
        }

        const setupWrap = document.getElementById('quizQuestionSetup');
        const qTextEl = document.getElementById('questionText');
        if (parsed.isSplit && parsed.setupSentences && parsed.setupSentences.length > 0) {
          if (setupWrap) {
            setupWrap.style.display = 'flex';
            setupWrap.innerHTML = parsed.setupSentences.map(s => `<p class="setup-sentence mb-0">${QuizShell.formatInlineCode(s)}</p>`).join('');
          }
          if (qTextEl) {
            qTextEl.classList.add('is-prompt');
            qTextEl.innerHTML = QuizShell.formatInlineCode(parsed.prompt);
          }
        } else {
          if (setupWrap) setupWrap.style.display = 'none';
          if (qTextEl) {
            qTextEl.classList.remove('is-prompt');
            qTextEl.innerHTML = QuizShell.formatInlineCode(parsed.prompt || parsed.fullText || data.question_text);
          }
        }"""

new_tech_parser = """        // Question parsing: source tag stamp, setup sentences, prompt
        const parsed = data.parsed_question || QuizShell.parseQuestion(data.question_text);
        const sourceTagText = parsed.sourceTag || parsed.source_tag || (data.company_name && data.company_name !== 'General' ? data.company_name : null);
        const tagWrap = document.getElementById('quizSourceTagWrap');
        const tagStamp = document.getElementById('quizSourceTag');
        if (sourceTagText && tagWrap && tagStamp) {
          tagWrap.style.display = 'block';
          tagStamp.innerText = sourceTagText;
        } else if (tagWrap) {
          tagWrap.style.display = 'none';
        }

        const isSplit = parsed.isSplit !== undefined ? parsed.isSplit : parsed.is_split;
        const setupSentences = parsed.setupSentences || parsed.setup_sentences || [];
        const fullQuestionText = parsed.fullText || parsed.full_text || data.question_text || '';
        const promptText = parsed.prompt || fullQuestionText;

        const setupWrap = document.getElementById('quizQuestionSetup');
        const qTextEl = document.getElementById('questionText');
        if (isSplit && setupSentences && setupSentences.length > 0) {
          if (setupWrap) {
            setupWrap.style.display = 'flex';
            setupWrap.innerHTML = setupSentences.map(s => `<p class="setup-sentence mb-0">${QuizShell.formatInlineCode(s)}</p>`).join('');
          }
          if (qTextEl) {
            qTextEl.classList.add('is-prompt');
            qTextEl.innerHTML = QuizShell.formatInlineCode(promptText);
          }
        } else {
          if (setupWrap) setupWrap.style.display = 'none';
          if (qTextEl) {
            qTextEl.classList.remove('is-prompt');
            qTextEl.innerHTML = QuizShell.formatInlineCode(fullQuestionText);
          }
        }"""

if old_tech_parser in tech_content:
    tech_content = tech_content.replace(old_tech_parser, new_tech_parser)
    print("[+] Successfully replaced question parser rendering in app/Views/technical.php")
else:
    print("[!] Warning: old_tech_parser not found in technical.php")

# Speed up auto next timer in technical.php
old_tech_timer = """  startAutoNextCountdown() {
    clearInterval(this.countdownInterval);
    this.countdownSeconds = 10;
    const badge = document.getElementById('autoNextCountdownBadge') || document.getElementById('techAutoCountdown');
    const text = document.getElementById('autoNextSecondsText') || document.getElementById('techCountdownSecs');
    if (badge) badge.style.display = 'inline-flex';
    if (text) text.innerText = '10';"""

new_tech_timer = """  startAutoNextCountdown() {
    clearInterval(this.countdownInterval);
    this.countdownSeconds = 4;
    const badge = document.getElementById('autoNextCountdownBadge') || document.getElementById('techAutoCountdown');
    const text = document.getElementById('autoNextSecondsText') || document.getElementById('techCountdownSecs');
    if (badge) badge.style.display = 'inline-flex';
    if (text) text.innerText = '4';"""

if old_tech_timer in tech_content:
    tech_content = tech_content.replace(old_tech_timer, new_tech_timer)
    print("[+] Successfully updated auto-next countdown in app/Views/technical.php")
else:
    print("[!] Warning: old_tech_timer not found in technical.php")

with open('app/Views/technical.php', 'w', encoding='utf-8') as f:
    f.write(tech_content)


# 2. Update public/js/app.js
with open('public/js/app.js', 'r', encoding='utf-8') as f:
    app_content = f.read()

# Fix hint coins update in public/js/app.js
old_app_coins = """            // Update user coins on navbar if logged in
            if (typeof data.new_coin_balance !== 'undefined') {
                const userCoinsEl = document.getElementById('userCoinsDisplay');
                if (userCoinsEl) {
                    userCoinsEl.innerText = Number(data.new_coin_balance).toLocaleString();
                }
            }"""

new_app_coins = """            // Update user coins on navbar if logged in
            if (typeof data.new_coin_balance !== 'undefined') {
                const formatted = Number(data.new_coin_balance).toLocaleString();
                ['userCoinsDisplay', 'userCoinsDisplayMobile', 'dropdownUserCoins'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.innerText = formatted;
                });
            }"""

if old_app_coins in app_content:
    app_content = app_content.replace(old_app_coins, new_app_coins)
    print("[+] Successfully replaced hint coins in public/js/app.js")
else:
    print("[!] Warning: old_app_coins not found in public/js/app.js")

# Fix question parser rendering in public/js/app.js
old_app_parser = """                const setupWrap = document.getElementById('quizQuestionSetup');
                const qTextEl = document.getElementById('questionText');
                if (parsed.isSplit && parsed.setupSentences && parsed.setupSentences.length > 0) {
                    if (setupWrap) {
                        setupWrap.style.display = 'flex';
                        setupWrap.innerHTML = parsed.setupSentences.map(s => `<p class="setup-sentence mb-0">${window.QuizShell ? QuizShell.formatInlineCode(s) : s}</p>`).join('');
                    }
                    if (qTextEl) {
                        qTextEl.classList.add('is-prompt');
                        qTextEl.innerHTML = window.QuizShell ? QuizShell.formatInlineCode(parsed.prompt) : parsed.prompt;
                    }
                } else {
                    if (setupWrap) setupWrap.style.display = 'none';
                    if (qTextEl) {
                        qTextEl.classList.remove('is-prompt');
                        qTextEl.innerHTML = window.QuizShell ? QuizShell.formatInlineCode(parsed.prompt || parsed.fullText || data.question_text) : data.question_text;
                    }
                }"""

new_app_parser = """                const isSplit = parsed.isSplit !== undefined ? parsed.isSplit : parsed.is_split;
                const setupSentences = parsed.setupSentences || parsed.setup_sentences || [];
                const fullQuestionText = parsed.fullText || parsed.full_text || data.question_text || '';
                const promptText = parsed.prompt || fullQuestionText;

                const setupWrap = document.getElementById('quizQuestionSetup');
                const qTextEl = document.getElementById('questionText');
                if (isSplit && setupSentences && setupSentences.length > 0) {
                    if (setupWrap) {
                        setupWrap.style.display = 'flex';
                        setupWrap.innerHTML = setupSentences.map(s => `<p class="setup-sentence mb-0">${window.QuizShell ? QuizShell.formatInlineCode(s) : s}</p>`).join('');
                    }
                    if (qTextEl) {
                        qTextEl.classList.add('is-prompt');
                        qTextEl.innerHTML = window.QuizShell ? QuizShell.formatInlineCode(promptText) : promptText;
                    }
                } else {
                    if (setupWrap) setupWrap.style.display = 'none';
                    if (qTextEl) {
                        qTextEl.classList.remove('is-prompt');
                        qTextEl.innerHTML = window.QuizShell ? QuizShell.formatInlineCode(fullQuestionText) : fullQuestionText;
                    }
                }"""

if old_app_parser in app_content:
    app_content = app_content.replace(old_app_parser, new_app_parser)
    print("[+] Successfully replaced question parser rendering in public/js/app.js")
else:
    print("[!] Warning: old_app_parser not found in public/js/app.js")

# Speed up auto next timer in public/js/app.js
old_app_timer = """    startAutoNextCountdown() {
        clearInterval(this.autoNextTimerInterval);
        this.autoNextSeconds = 10;

        const countdownBadge = document.getElementById('autoNextCountdownBadge');
        const countdownText = document.getElementById('autoNextSecondsText');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (submitBtn) submitBtn.style.display = 'none';
        if (countdownBadge) countdownBadge.style.display = 'inline-flex';
        if (countdownText) countdownText.innerText = '10';
        if (nextBtn) {
            nextBtn.style.display = 'inline-flex';
            nextBtn.innerText = 'Next (10s)';
        }"""

new_app_timer = """    startAutoNextCountdown() {
        clearInterval(this.autoNextTimerInterval);
        this.autoNextSeconds = 4;

        const countdownBadge = document.getElementById('autoNextCountdownBadge');
        const countdownText = document.getElementById('autoNextSecondsText');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (submitBtn) submitBtn.style.display = 'none';
        if (countdownBadge) countdownBadge.style.display = 'inline-flex';
        if (countdownText) countdownText.innerText = '4';
        if (nextBtn) {
            nextBtn.style.display = 'inline-flex';
            nextBtn.innerText = 'Next (4s)';
        }"""

if old_app_timer in app_content:
    app_content = app_content.replace(old_app_timer, new_app_timer)
    print("[+] Successfully updated auto-next timer in public/js/app.js")
else:
    print("[!] Warning: old_app_timer not found in public/js/app.js")

# Fix sourceTagText check in public/js/app.js:
old_source_tag = "const sourceTagText = parsed.sourceTag || (data.company_name && data.company_name !== 'General' ? data.company_name : null);"
new_source_tag = "const sourceTagText = parsed.sourceTag || parsed.source_tag || (data.company_name && data.company_name !== 'General' ? data.company_name : null);"
if old_source_tag in app_content:
    app_content = app_content.replace(old_source_tag, new_source_tag)
    print("[+] Successfully updated sourceTagText in public/js/app.js")

with open('public/js/app.js', 'w', encoding='utf-8') as f:
    f.write(app_content)

print("[*] All updates processed.")
