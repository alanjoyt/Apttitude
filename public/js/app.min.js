/* Apttitudee Gamified Frontend Engine - Phase 2 (mr.ignite ~ BOLDBYTE) */

document.addEventListener('DOMContentLoaded', () => {
    console.log('Apttitudee Gamified Engine initialized.');
    SoundFX.init();
    if (document.getElementById('dailyQuestsContainer')) {
        loadDailyQuests();
    }
});

// Canvas Confetti helper function with defer safety
function launchConfetti(opts) {
    if (typeof confetti === 'function') {
        confetti(opts || {
            particleCount: 80,
            spread: 70,
            origin: { y: 0.6 }
        });
    } else {
        window.addEventListener('load', function() {
            if (typeof confetti === 'function') {
                confetti(opts || { particleCount: 80, spread: 70, origin: { y: 0.6 } });
            }
        }, { once: true });
    }
}

// Floating XP animation helper
function showFloatingXP(amount, element) {
    if (!element) return;
    const rect = element.getBoundingClientRect();
    const xpEl = document.createElement('div');
    xpEl.className = 'floating-xp' + (amount < 0 ? ' text-danger' : '');
    xpEl.innerHTML = amount < 0 ? `${amount} XP` : `+${amount} XP!`;
    if (amount < 0) {
        xpEl.style.color = '#ef4444';
    }
    xpEl.style.left = `${rect.left + rect.width / 2 - 30}px`;
    xpEl.style.top = `${rect.top - 20}px`;
    document.body.appendChild(xpEl);

    setTimeout(() => {
        xpEl.remove();
    }, 1200);
}

// Web Audio API Synthesizer (Zero External Assets) + Haptic Feedback
const SoundFX = {
    audioCtx: null,
    muted: localStorage.getItem('apttitudee_sound_muted') === 'true',

    init() {
        const toggleBtns = document.querySelectorAll('#soundToggleBtn, .btn-sound-toggle');
        if (toggleBtns.length > 0) {
            toggleBtns.forEach(btn => {
                this.updateButton(btn);
                btn.addEventListener('click', () => {
                    this.muted = !this.muted;
                    localStorage.setItem('apttitudee_sound_muted', this.muted);
                    toggleBtns.forEach(b => this.updateButton(b));
                });
            });
        }
    },

    updateButton(toggleBtn) {
        if (!toggleBtn) return;
        const iconClass = this.muted ? 'bi bi-volume-mute-fill text-danger' : 'bi bi-volume-up-fill text-success';
        const label = this.muted ? 'Sound Muted' : 'Sound Enabled';
        toggleBtn.innerHTML = `<i class="${iconClass}"></i>`;
        toggleBtn.title = label;
        toggleBtn.setAttribute('aria-label', label);
    },

    vibrate(pattern = 15) {
        if (this.muted) return;
        if (typeof navigator !== 'undefined' && 'vibrate' in navigator) {
            try {
                navigator.vibrate(pattern);
            } catch (e) {}
        }
    },

    getAudioContext() {
        if (!this.audioCtx) {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) this.audioCtx = new AudioCtx();
        }
        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }
        return this.audioCtx;
    },

    playTone(freq, duration = 0.15, type = 'sine', startTimeOffset = 0) {
        if (this.muted) return;
        try {
            const ctx = this.getAudioContext();
            if (!ctx) return;

            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = type;
            osc.frequency.setValueAtTime(freq, ctx.currentTime + startTimeOffset);

            gain.gain.setValueAtTime(0.12, ctx.currentTime + startTimeOffset);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + startTimeOffset + duration);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(ctx.currentTime + startTimeOffset);
            osc.stop(ctx.currentTime + startTimeOffset + duration);
        } catch (e) {
            console.warn('AudioContext error:', e);
        }
    },

    playCorrect() {
        this.playTone(523.25, 0.12, 'sine', 0);
        this.playTone(659.25, 0.12, 'sine', 0.1);
        this.playTone(783.99, 0.25, 'triangle', 0.2);
    },

    playIncorrect() {
        this.playTone(220, 0.15, 'sawtooth', 0);
        this.playTone(185, 0.25, 'sawtooth', 0.12);
    },

    playCombo(level) {
        const baseFreq = 400 + (level * 80);
        this.playTone(baseFreq, 0.1, 'triangle', 0);
        this.playTone(baseFreq * 1.25, 0.15, 'triangle', 0.08);
        this.playTone(baseFreq * 1.5, 0.25, 'sine', 0.16);
    },

    playCoin() {
        this.playTone(987.77, 0.08, 'sine', 0);
        this.playTone(1318.51, 0.2, 'sine', 0.07);
    },

    playFanfare() {
        this.playTone(523.25, 0.12, 'triangle', 0);
        this.playTone(659.25, 0.12, 'triangle', 0.12);
        this.playTone(783.99, 0.12, 'triangle', 0.24);
        this.playTone(1046.50, 0.35, 'sine', 0.36);
    }
};

// Session Combo Manager
const ComboManager = {
    currentCombo: 0,

    reset() {
        this.currentCombo = 0;
        this.updateUI();
    },

    increment() {
        this.currentCombo++;
        this.updateUI();
        if (this.currentCombo >= 2) {
            SoundFX.playCombo(this.currentCombo);
        } else {
            SoundFX.playCorrect();
        }
        return this.currentCombo;
    },

    updateUI() {
        const comboDisplay = document.getElementById('sessionComboDisplay');
        if (!comboDisplay) return;

        if (this.currentCombo <= 1) {
            comboDisplay.style.display = 'none';
        } else {
            comboDisplay.style.display = 'inline-block';
            comboDisplay.className = 'badge bg-danger text-white px-3 py-2 badge-glow pulse-animation';
            comboDisplay.innerHTML = `<i class="bi bi-fire me-1"></i> ${this.currentCombo}x COMBO STREAK!`;
        }
    }
};

// Practice Engine State
const PracticeEngine = {
    currentQuestion: null,
    selectedOption: null,
    confidence: 'sure',
    difficultyMode: 'adaptive',
    subtopicId: null,
    startTime: null,
    timerInterval: null,
    elapsedSeconds: 0,
    autoNextTimerInterval: null,
    autoNextSeconds: 10,
    hintUnlockedQuestionId: null,

    init(subtopicId) {
        if (subtopicId) {
            this.subtopicId = parseInt(subtopicId);
        } else {
            const urlParams = new URLSearchParams(window.location.search);
            const sId = urlParams.get('subtopic_id');
            if (sId) this.subtopicId = parseInt(sId);
        }
        this.seenQuestionIds = [];
        this.bindEvents();
        this.checkInterruptedSession();
        this.loadNextQuestion(this.subtopicId);
    },

    checkInterruptedSession() {
        try {
            const saved = JSON.parse(localStorage.getItem('apttitudee_practice_session') || '{}');
            if (saved && saved.subtopic_id && (Date.now() - saved.timestamp < 7200000)) {
                const urlParams = new URLSearchParams(window.location.search);
                const currentId = urlParams.get('subtopic_id');
                if (!currentId || parseInt(currentId) !== saved.subtopic_id) {
                    const banner = document.getElementById('interruptedResumeBanner');
                    if (banner) {
                        const label = document.getElementById('resumeSubtopicLabel');
                        if (label) label.textContent = `${saved.topic_name || ''} · ${saved.subtopic_name || 'Previous Topic'}`;
                        banner.classList.remove('d-none');
                        banner.classList.add('d-flex');
                    }
                }
            }
        } catch (e) {}
    },

    resumeInterruptedSession() {
        try {
            const saved = JSON.parse(localStorage.getItem('apttitudee_practice_session') || '{}');
            if (saved && saved.subtopic_id) {
                const banner = document.getElementById('interruptedResumeBanner');
                if (banner) {
                    banner.classList.add('d-none');
                    banner.classList.remove('d-flex');
                }
                this.subtopicId = saved.subtopic_id;
                this.loadNextQuestion(saved.subtopic_id);
            }
        } catch (e) {}
    },

    dismissInterruptedSession() {
        localStorage.removeItem('apttitudee_practice_session');
        const banner = document.getElementById('interruptedResumeBanner');
        if (banner) {
            banner.classList.add('d-none');
            banner.classList.remove('d-flex');
        }
    },

    restartTopic(subtopicId = null) {
        const sId = subtopicId || this.subtopicId;
        if (!sId) {
            window.location.href = 'index.php?action=practice';
            return;
        }

        const csrfToken = window.CSRF_TOKEN || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '');

        fetch('index.php?action=api_restart_topic', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                subtopic_id: sId,
                csrf_token: csrfToken
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                this.seenQuestionIds = [];
                try {
                    localStorage.removeItem('apttitudee_practice_session');
                } catch (e) {}
                this.subtopicId = sId;
                this.loadNextQuestion(sId);
            } else {
                alert(data.message || 'Could not restart topic');
            }
        })
        .catch(err => {
            console.error('Restart failed', err);
            window.location.href = `index.php?action=practice&subtopic_id=${sId}&session=1&reset=1`;
        });
    },

    bindEvents() {
        // Option selection
        document.querySelectorAll('.option-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (document.getElementById('explanationCard').style.display === 'block') return;
                this.selectOption(btn.dataset.option);
            });
        });

        // Confidence selection
        document.querySelectorAll('.confidence-pill, .confidence-pill-col').forEach(pill => {
            pill.addEventListener('click', () => {
                const expCard = document.getElementById('explanationCard');
                if (expCard && expCard.style.display === 'block') return;
                document.querySelectorAll('.confidence-pill, .confidence-pill-col').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                this.confidence = pill.dataset.confidence || 'sure';
            });
        });

        // Submit answer button
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => this.submitAnswer());
        }

        // Next question button
        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.loadNextQuestion());
        }

        // Keyboard navigation shortcuts (A/1, B/2, C/3, D/4 to select, Enter to check/next)
        document.addEventListener('keydown', (e) => {
            // Ignore if typing in text inputs or textareas or modals
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
            
            const key = e.key.toUpperCase();
            if (['A', '1'].includes(key)) this.selectOption('A');
            else if (['B', '2'].includes(key)) this.selectOption('B');
            else if (['C', '3'].includes(key)) this.selectOption('C');
            else if (['D', '4'].includes(key)) this.selectOption('D');
            else if (key === 'ENTER') {
                const sBtn = document.getElementById('submitBtn');
                const nBtn = document.getElementById('nextBtn');
                if (nBtn && nBtn.style.display !== 'none') {
                    this.loadNextQuestion();
                } else if (sBtn && !sBtn.disabled && sBtn.style.display !== 'none') {
                    this.submitAnswer();
                }
            }
        });
    },

    selectOption(optionKey) {
        if (document.getElementById('explanationCard').style.display === 'block') return;
        const btn = document.querySelector(`.option-btn[data-option="${optionKey}"]`);
        if (!btn || btn.disabled) return;
        document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        this.selectedOption = optionKey;
        SoundFX.vibrate(15);
        const sBtn = document.getElementById('submitBtn');
        if (sBtn) {
            sBtn.disabled = false;
            sBtn.removeAttribute('aria-disabled');
            sBtn.innerHTML = '<span class="btn-quiz-primary-text">Check</span>';
            sBtn.style.display = 'inline-flex';
        }
        const hint = document.getElementById('quizCheckHint');
        if (hint) hint.style.display = 'none';
        const nBtn = document.getElementById('nextBtn');
        if (nBtn) nBtn.style.display = 'none';
    },

    startPracticeTimer() {
        clearInterval(this.timerInterval);
        this.elapsedSeconds = 0;
        const displays = document.querySelectorAll('.practice-time-display, #practiceTimeDisplay, #mobileQuizTimeDisplay, #sharedQuizTopTimeText');
        displays.forEach(el => el.innerText = '0s');

        this.timerInterval = setInterval(() => {
            this.elapsedSeconds++;
            const mins = Math.floor(this.elapsedSeconds / 60);
            const secs = this.elapsedSeconds % 60;
            const timeStr = mins > 0 ? `${mins}m ${secs}s` : `${secs}s`;
            displays.forEach(el => el.innerText = timeStr);
        }, 1000);
    },

    stopPracticeTimer() {
        clearInterval(this.timerInterval);
    },

    startAutoNextCountdown() {
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
        }

        this.autoNextTimerInterval = setInterval(() => {
            this.autoNextSeconds--;
            if (countdownText) countdownText.innerText = this.autoNextSeconds;
            if (nextBtn) {
                nextBtn.innerText = `Next (${this.autoNextSeconds}s)`;
            }

            if (this.autoNextSeconds <= 0) {
                clearInterval(this.autoNextTimerInterval);
                this.autoNextTimerInterval = null;
                this.loadNextQuestion();
            }
        }, 1000);
    },

    loadNextQuestion(subtopicId = null) {
        clearInterval(this.autoNextTimerInterval);
        this.autoNextTimerInterval = null;
        const countdownBadge = document.getElementById('autoNextCountdownBadge');
        if (countdownBadge) countdownBadge.style.display = 'none';

        this.selectedOption = null;
        this.hintUnlockedQuestionId = null;
        this.eliminatedOption = null;
        
        // Reset and show Submit button, hide Next button completely
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.setAttribute('aria-disabled', 'true');
            submitBtn.innerHTML = '<span class="btn-quiz-primary-text">Check</span>';
            submitBtn.classList.remove('d-none');
            submitBtn.style.display = 'inline-flex';
        }
        const hint = document.getElementById('quizCheckHint');
        if (hint) hint.style.display = '';

        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) {
            nextBtn.classList.add('d-none');
            nextBtn.style.display = 'none';
            nextBtn.innerHTML = '<span class="btn-quiz-primary-text">Next</span>';
        }

        document.getElementById('explanationCard').style.display = 'none';
        
        const mistakeCard = document.getElementById('mistakeTagCard');
        if (mistakeCard) mistakeCard.style.display = 'none';
        const mistakeBadge = document.getElementById('mistakeTaggedBadge');
        if (mistakeBadge) mistakeBadge.style.display = 'none';

        document.getElementById('optionsContainer').style.display = 'flex';
        const confContainer = document.querySelector('.confidence-container');
        if (confContainer) confContainer.style.display = 'block';

        // Reset confidence to sure by default and update UI active state
        this.confidence = 'sure';
        document.querySelectorAll('.confidence-pill, .confidence-pill-col').forEach(p => {
            if (p.dataset.confidence === 'sure') {
                p.classList.add('active');
            } else {
                p.classList.remove('active');
            }
        });

        document.querySelectorAll('.option-btn, .tech-opt-btn').forEach(b => {
            b.classList.remove('selected', 'correct', 'wrong', 'eliminated');
            b.disabled = false;
            b.removeAttribute('aria-disabled');
            const bBadge = b.querySelector('.eliminated-badge');
            if (bBadge) bBadge.remove();
        });

        if (subtopicId !== undefined && subtopicId !== null) {
            this.subtopicId = parseInt(subtopicId);
        } else if (!this.subtopicId) {
            const urlParams = new URLSearchParams(window.location.search);
            const sId = urlParams.get('subtopic_id');
            if (sId) this.subtopicId = parseInt(sId);
        }
        const activeSubtopicId = this.subtopicId;

        let url = activeSubtopicId ? `index.php?action=api_next_question&subtopic_id=${activeSubtopicId}` : 'index.php?action=api_next_question';
        if (this.difficultyMode && this.difficultyMode !== 'adaptive') {
            url += `&difficulty=${encodeURIComponent(this.difficultyMode)}`;
        }
        this.seenQuestionIds = this.seenQuestionIds || [];
        if (this.seenQuestionIds.length > 0) {
            url += `&exclude=${encodeURIComponent(this.seenQuestionIds.join(','))}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && res.data && res.data.id) {
                    if (!this.seenQuestionIds.includes(res.data.id)) {
                        this.seenQuestionIds.push(res.data.id);
                    }
                }
                if (res.status === 'guest_limit_reached') {
                    const guestModalEl = document.getElementById('guestLimitModal');
                    if (guestModalEl) {
                        const modal = new bootstrap.Modal(guestModalEl);
                        modal.show();
                    } else {
                        alert(res.message);
                        window.location.href = 'index.php?action=register';
                    }
                    return;
                }
                if (res.status === 'empty' || res.status === 'completed') {
                    const subtopicName = res.subtopic_name || (this.currentQuestion && this.currentQuestion.subtopic_name) || 'Topic';
                    const sId = res.subtopic_id || this.subtopicId;

                    const topicBadge = document.getElementById('topicBadge');
                    if (topicBadge && subtopicName) {
                        topicBadge.innerText = subtopicName;
                        topicBadge.title = subtopicName;
                    }

                    if (res.empty_topic) {
                        document.getElementById('questionText').innerHTML = `
                            <div class="text-center py-4">
                                <div class="mb-3 text-warning">
                                    <svg class="exam-icon" style="width:36px;height:36px;color:var(--marker-orange);"><use href="#icon-clock"></use></svg>
                                </div>
                                <h3 class="text-ink fw-bold mb-2 font-serif">Questions Arriving Soon</h3>
                                <p class="text-ink-muted mb-4 fs-7" style="max-width: 480px; margin: 0 auto;">
                                    ${res.message || `Practice questions for <strong>${window.QuizShell ? QuizShell.formatInlineCode(subtopicName) : subtopicName}</strong> are currently being prepared.`}
                                </p>
                                <div class="d-flex justify-content-center flex-wrap gap-2">
                                    <a href="index.php?action=practice" class="btn btn-primary-gamified px-4 py-2.5 fw-bold d-inline-flex align-items-center">
                                        <svg class="exam-icon me-1 icon-14"><use href="#icon-grid"></use></svg> Back to Syllabus
                                    </a>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('questionText').innerHTML = `
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <span class="badge-stamp-sm" style="font-size: 0.82rem; padding: 0.35rem 0.75rem; background: var(--success-bg); color: var(--success-text); border: 1.5px solid var(--success-border);">
                                        <svg class="exam-icon me-1 icon-14"><use href="#icon-shield-check"></use></svg> 100% COMPLETED
                                    </span>
                                </div>
                                <h3 class="text-ink fw-bold mb-2 font-serif">All Questions Completed!</h3>
                                <p class="text-ink-muted mb-4 fs-7" style="max-width: 480px; margin: 0 auto;">
                                    You have answered all available questions in <strong>${window.QuizShell ? QuizShell.formatInlineCode(subtopicName) : subtopicName}</strong> without repetition.
                                    You can restart this topic to practice fresh from question 1, or practice another topic.
                                </p>
                                <div class="d-flex justify-content-center flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary-gamified px-4 py-2.5 fw-bold d-inline-flex align-items-center" onclick="PracticeEngine.restartTopic(${sId})">
                                        <svg class="exam-icon me-1 icon-14"><use href="#icon-arrow-repeat"></use></svg> Restart Topic
                                    </button>
                                    <a href="index.php?action=practice" class="btn btn-outline-gamified px-4 py-2.5 fw-bold d-inline-flex align-items-center">
                                        <svg class="exam-icon me-1 icon-14"><use href="#icon-grid"></use></svg> Back to Syllabus
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                    document.getElementById('optionsContainer').style.display = 'none';
                    if (confContainer) confContainer.style.display = 'none';
                    if (submitBtn) submitBtn.style.display = 'none';
                    const nextBtn = document.getElementById('nextBtn');
                    if (nextBtn) nextBtn.style.display = 'none';
                    const codeBox = document.getElementById('practiceCodeContainer');
                    if (codeBox) codeBox.style.display = 'none';
                    return;
                }

                const data = res.data;
                this.currentQuestion = data;
                this.startTime = Date.now();
                this.startPracticeTimer();

                try {
                    localStorage.setItem('apttitudee_practice_session', JSON.stringify({
                        subtopic_id: activeSubtopicId,
                        topic_name: data.topic_name,
                        subtopic_name: data.subtopic_name,
                        question_id: data.id,
                        timestamp: Date.now()
                    }));
                } catch (e) {}

                // Shorten topic stamp: e.g. "Logical > Number Series" with single line ellipsis
                let shortTopic = data.topic_name || '';
                if (shortTopic.includes('Logical Reasoning')) shortTopic = 'Logical';
                else if (shortTopic.includes('Quantitative Aptitude')) shortTopic = 'Quant';
                else if (shortTopic.includes('Verbal Ability')) shortTopic = 'Verbal';
                else if (shortTopic.includes('Technical Assessment')) shortTopic = 'Tech';
                else if (shortTopic.includes('Company Placement PYQs')) shortTopic = 'Company';

                const subtopicTitle = data.subtopic_name || '';
                const topicBadge = document.getElementById('topicBadge');
                if (topicBadge) {
                    topicBadge.innerText = `${shortTopic} > ${subtopicTitle}`;
                    topicBadge.title = `${data.topic_name} > ${subtopicTitle}`;
                }

                // Update mobile quiz progress bar
                const pBar = document.getElementById('mobileQuizProgressBar');
                if (pBar) {
                    this.quizQuestionCount = (this.quizQuestionCount || 0) + 1;
                    const pct = Math.min(100, Math.max(10, Math.round((this.quizQuestionCount / 10) * 100)));
                    pBar.style.width = `${pct}%`;
                }

                document.getElementById('difficultyBadge').innerText = data.difficulty.toUpperCase();

                // Question parsing: source tag stamp, verification badges, round pattern, setup sentences, prompt
                const parsed = data.parsed_question || (window.QuizShell ? QuizShell.parseQuestion(data.question_text) : { prompt: data.question_text });
                const tagWrap = document.getElementById('quizSourceTagWrap');
                const tagStamp = document.getElementById('quizSourceTag');
                const vBadgeWrap = document.getElementById('verificationBadgeWrap');
                const roundBadge = document.getElementById('roundPatternBadge');

                const sourceTagText = parsed.sourceTag || parsed.source_tag || (data.company_name && data.company_name !== 'General' ? data.company_name : null);
                if (tagStamp && sourceTagText) {
                    tagStamp.innerText = sourceTagText;
                    tagStamp.style.display = 'inline-block';
                } else if (tagStamp) {
                    tagStamp.style.display = 'none';
                }

                if (vBadgeWrap) {
                    const vStatus = data.verification_status || 'practice_style';
                    if (vStatus === 'verified_pyq') {
                        vBadgeWrap.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-8 fw-semibold"><i class="bi bi-patch-check-fill me-1"></i>Verified PYQ</span>';
                    } else if (vStatus === 'student_reported') {
                        vBadgeWrap.innerHTML = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fs-8 fw-semibold"><i class="bi bi-mortarboard-fill me-1"></i>Reported by Students</span>';
                    } else {
                        vBadgeWrap.innerHTML = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fs-8 fw-semibold"><i class="bi bi-briefcase me-1"></i>Practice Question in Company Style</span>';
                    }
                    vBadgeWrap.style.display = 'inline-block';
                }

                if (roundBadge) {
                    if (data.round_pattern) {
                        roundBadge.style.display = 'inline-block';
                        roundBadge.innerHTML = '<i class="bi bi-diagram-3 me-1"></i>' + data.round_pattern;
                    } else {
                        roundBadge.style.display = 'none';
                    }
                }

                if (tagWrap) {
                    tagWrap.style.display = 'flex';
                }

                const fullQuestionText = parsed.fullText || parsed.full_text || parsed.prompt || data.question_text || '';
                const setupWrap = document.getElementById('quizQuestionSetup');
                const qTextEl = document.getElementById('questionText');
                if (setupWrap) setupWrap.style.display = 'none';
                if (qTextEl) {
                    qTextEl.classList.remove('is-prompt');
                    qTextEl.innerHTML = (window.QuizShell && QuizShell.formatInlineCode) ? QuizShell.formatInlineCode(fullQuestionText) : fullQuestionText;
                }

                // Render Code Snippet ONLY if non-trivial
                const codeBox = document.getElementById('practiceCodeContainer');
                const codeEl = document.getElementById('practiceCodeSnippet');
                const langEl = document.getElementById('practiceCodeLang');
                const isTrivial = data.is_trivial_code !== undefined ? data.is_trivial_code : (window.QuizShell ? QuizShell.isTrivialCode(data.code_snippet, data.question_text) : false);

                if (codeBox && codeEl) {
                    if (data.code_snippet && data.code_snippet.trim() !== '' && !isTrivial) {
                        codeBox.style.display = 'block';
                        codeEl.innerText = data.code_snippet;
                        if (langEl) langEl.innerText = (data.language || 'Code').toUpperCase();
                    } else {
                        codeBox.style.display = 'none';
                    }
                }

                // Check Boss Question Trigger (Hard Difficulty Event)
                const bossBanner = document.getElementById('bossQuestionBanner');
                if (bossBanner) {
                    if (data.difficulty === 'hard') {
                        bossBanner.style.display = 'block';
                        SoundFX.playTone(300, 0.3, 'sawtooth');
                    } else {
                        bossBanner.style.display = 'none';
                    }
                }

                ['A', 'B', 'C', 'D'].forEach(opt => {
                    const optEl = document.querySelector(`.option-btn[data-option="${opt}"] .option-text`);
                    if (optEl && data.options && data.options[opt]) {
                        optEl.innerHTML = window.QuizShell ? QuizShell.formatInlineCode(data.options[opt]) : data.options[opt];
                    }
                });
            })
            .catch(err => {
                console.error('Failed to load next question:', err);
                const qText = document.getElementById('questionText');
                if (qText) {
                    qText.innerHTML = `
                        <div class="text-center py-4">
                            <div class="mb-3 text-warning">
                                <svg class="exam-icon" style="width:36px;height:36px;color:var(--marker-orange);"><use href="#icon-clock"></use></svg>
                            </div>
                            <h4 class="text-ink fw-bold mb-2 font-serif">Unable to load question</h4>
                            <p class="text-ink-muted mb-4 fs-7" style="max-width: 440px; margin: 0 auto;">
                                We encountered a network connection issue or the session could not be reached. Please check your connection and try again.
                            </p>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <button type="button" class="btn btn-primary-gamified px-4 py-2 fw-bold d-inline-flex align-items-center" onclick="PracticeEngine.loadNextQuestion(${activeSubtopicId || ''})">
                                    <svg class="exam-icon me-1 icon-14"><use href="#icon-arrow-repeat"></use></svg> Retry
                                </button>
                                <a href="index.php?action=practice" class="btn btn-outline-gamified px-4 py-2 fw-bold d-inline-flex align-items-center">
                                    <svg class="exam-icon me-1 icon-14"><use href="#icon-grid"></use></svg> Back to Syllabus
                                </a>
                            </div>
                        </div>
                    `;
                }
                const optBox = document.getElementById('optionsContainer');
                if (optBox) optBox.style.display = 'none';
                const cBox = document.querySelector('.confidence-container');
                if (cBox) cBox.style.display = 'none';
                if (submitBtn) submitBtn.style.display = 'none';
                const nBtn = document.getElementById('nextBtn');
                if (nBtn) nBtn.style.display = 'none';
                const codeBox = document.getElementById('practiceCodeContainer');
                if (codeBox) codeBox.style.display = 'none';
            });
    },

    submitAnswer() {
        if (!this.selectedOption || !this.currentQuestion) return;

        const submitBtn = document.getElementById('submitBtn');
        if (!submitBtn || submitBtn.disabled) return; // Prevent double submission

        // Immediately disable submit button and show Checking spinner
        submitBtn.disabled = true;
        submitBtn.setAttribute('aria-disabled', 'true');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Checking...';
        submitBtn.style.display = 'inline-flex';
        
        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) nextBtn.style.display = 'none';

        this.stopPracticeTimer();
        const timeTakenSeconds = Math.max(1, this.elapsedSeconds);

        fetch('index.php?action=api_submit_answer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                question_id: this.currentQuestion.id,
                selected_option: this.selectedOption,
                confidence: this.confidence,
                time_taken_seconds: timeTakenSeconds,
                csrf_token: window.CSRF_TOKEN || ''
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') {
                // If rate-limited or error, re-enable button cleanly with warning
                submitBtn.disabled = false;
                submitBtn.removeAttribute('aria-disabled');
                submitBtn.innerHTML = '<span class="btn-quiz-primary-text">Check</span>';
                submitBtn.style.display = 'inline-flex';
                if (data.message) alert(data.message);
                return;
            }

            // Lock option buttons
            document.querySelectorAll('.option-btn').forEach(b => {
                b.disabled = true;
                if (b.dataset.option === data.correct_option) {
                    b.classList.add('correct');
                    b.classList.remove('eliminated');
                    const elim = b.querySelector('.eliminated-badge');
                    if (elim) elim.remove();
                } else if (b.dataset.option === this.selectedOption && !data.is_correct) {
                    b.classList.add('wrong');
                }
            });

            // Display explanation card & switch to Next button
            const expEl = document.getElementById('explanationText');
            if (expEl) {
                expEl.innerHTML = window.QuizShell ? QuizShell.formatInlineCode(data.explanation) : data.explanation;
            }
            const expCard = document.getElementById('explanationCard');
            if (expCard) {
                expCard.style.display = 'block';
                setTimeout(() => {
                    expCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            }
            
            submitBtn.classList.add('d-none');
            submitBtn.style.display = 'none';
            submitBtn.disabled = true;
            submitBtn.innerText = 'Check';
            
            if (nextBtn) {
                nextBtn.classList.remove('d-none');
                nextBtn.style.display = 'inline-flex';
                nextBtn.innerHTML = '<span class="btn-quiz-primary-text">Next</span> <svg class="exam-icon ms-1" style="width:14px;height:14px;"><use href="#icon-arrow-right"></use></svg>';
                nextBtn.focus(); // Focus next question for easy keyboard navigation
            }

            // Result icon, title, and rewards badge in explanation header
            const resultIcon = document.getElementById('techResultIcon');
            const resultTitle = document.getElementById('techResultTitle');
            const rewardsEl = document.getElementById('techRewardsEarned');

            if (resultIcon) {
                resultIcon.innerHTML = data.is_correct ? '<svg class="exam-icon text-success" style="width:24px;height:24px;"><use href="#icon-check"></use></svg>' : '<svg class="exam-icon text-danger" style="width:24px;height:24px;"><use href="#icon-x"></use></svg>';
            }
            if (resultTitle) {
                resultTitle.innerText = data.is_correct ? 'Correct! Well Done' : 'Incorrect Solution';
                resultTitle.className = data.is_correct ? 'fw-bold m-0 font-serif text-success' : 'fw-bold m-0 font-serif text-danger';
            }
            if (rewardsEl) {
                let xpBadge = '';
                if (data.xp_earned > 0) {
                    xpBadge = `<span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill">+${data.xp_earned} XP</span>`;
                } else if (data.xp_earned < 0) {
                    xpBadge = `<span class="badge bg-danger text-white px-2.5 py-1 rounded-pill">${data.xp_earned} XP (Overconfidence)</span>`;
                }
                let coinBadge = (data.coins_earned > 0) ? `<span class="badge bg-success text-white px-2.5 py-1 rounded-pill">+${data.coins_earned} Coins</span>` : '';
                rewardsEl.innerHTML = `${xpBadge} ${coinBadge}`;
            }

            // Start 10-second auto-skip countdown to read explanation
            this.startAutoNextCountdown();

            // Micro-interactions & Combo Meter
            if (data.is_correct) {
                SoundFX.vibrate([20, 40, 20]);
                ComboManager.increment();
                showFloatingXP(data.xp_earned, submitBtn);
                if (data.coins_earned > 0) SoundFX.playCoin();
                if (data.trigger_confetti) launchConfetti();
            } else {
                SoundFX.vibrate(60);
                ComboManager.reset();
                if (data.xp_earned < 0) {
                    showFloatingXP(data.xp_earned, submitBtn);
                }
                SoundFX.playIncorrect();
                const card = document.getElementById('practiceCard');
                if (card) {
                    card.classList.add('shake-animation');
                    setTimeout(() => {
                        card.classList.remove('shake-animation');
                    }, 500);
                }

                // Show mistake classification tagger if attempt saved
                if (data.attempt_id && data.attempt_id > 0) {
                    const mistakeCard = document.getElementById('mistakeTagCard');
                    if (mistakeCard) {
                        mistakeCard.style.display = 'block';
                        setTimeout(() => {
                            mistakeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 200);
                        document.querySelectorAll('.mistake-tag-btn').forEach(btn => {
                            btn.onclick = () => {
                                fetch('index.php?action=api_tag_mistake', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        attempt_id: data.attempt_id,
                                        mistake_type: btn.dataset.type,
                                        csrf_token: window.CSRF_TOKEN || ''
                                    })
                                })
                                .then(r => r.json())
                                .then(tagRes => {
                                    if (tagRes.status === 'success') {
                                        const badge = document.getElementById('mistakeTaggedBadge');
                                        if (badge) badge.style.display = 'inline-block';
                                        document.querySelectorAll('.mistake-tag-btn').forEach(b => b.classList.add('disabled'));
                                    }
                                });
                            };
                        });
                    }
                }
            }

            // Update top bar XP & Streak if logged in
            if (data.xp_data && data.xp_data.new_xp) {
                const xpBadge = document.getElementById('userXPDisplay');
                if (xpBadge) xpBadge.innerText = `${data.xp_data.new_xp} XP`;
            }
        })
        .catch(err => {
            console.error(err);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Check Answer <i class="bi bi-arrow-right-circle ms-1"></i>';
                submitBtn.style.display = 'inline-block';
            }
            const nextBtn = document.getElementById('nextBtn');
            if (nextBtn) nextBtn.style.display = 'none';
        });
    },

    saveProgress() {
        const btn = document.getElementById('saveProgressBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
        }

        fetch('index.php?action=api_save_progress')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    SoundFX.playTone(800, 0.15, 'sine');
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-floppy-fill me-1" style="color: var(--accent);"></i> Save Progress';
                }
            });
    },

    applyEliminatedOption(optKey, optText) {
        if (!optKey) return;
        const btn = document.querySelector(`.option-btn[data-option="${optKey}"], .tech-opt-btn[data-option="${optKey}"]`);
        if (btn) {
            btn.classList.add('eliminated');
            btn.classList.remove('selected');
            btn.disabled = true;
            btn.setAttribute('aria-disabled', 'true');

            // If user had previously selected this option, clear selection
            if (this.selectedOption === optKey) {
                this.selectedOption = null;
                const submitBtn = document.getElementById('submitBtn') || document.getElementById('techSubmitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.setAttribute('aria-disabled', 'true');
                }
            }

            // Add ELIMINATED badge if not present
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

        // If already unlocked for this question, reopen modal with the already eliminated option
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

        const visibleOpts = {};
        document.querySelectorAll('.option-btn').forEach(btn => {
            const opt = btn.dataset.option;
            if (opt) {
                const textSpan = btn.querySelector('.option-text') || btn;
                let txt = textSpan.innerText || '';
                txt = txt.replace(/^[A-D]\s*/, '').replace(/❌\s*ELIMINATED.*/i, '').replace(/✔\s*CORRECT.*/i, '').replace(/✖\s*INCORRECT.*/i, '').trim();
                visibleOpts[opt] = txt;
            }
        });

        fetch('index.php?action=api_use_hint', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question_id: this.currentQuestion.id, visible_options: visibleOpts, csrf_token: window.CSRF_TOKEN || '' })
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

            // Update user coins on navbar if logged in
            if (typeof data.new_coin_balance !== 'undefined') {
                const formatted = Number(data.new_coin_balance).toLocaleString();
                ['userCoinsDisplay', 'userCoinsDisplayMobile', 'dropdownUserCoins', 'drawerUserCoins', 'navCoinsDisplay', 'storeUserCoinsDisplay'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.innerText = formatted;
                });
            }

            // Save state for this question
            this.hintUnlockedQuestionId = this.currentQuestion.id;
            this.eliminatedOption = data.eliminated_option;

            // Apply visual strike-through to the eliminated button on the board
            this.applyEliminatedOption(data.eliminated_option, data.eliminated_text);

            if (typeof SoundFX !== 'undefined' && SoundFX.playCoin) {
                SoundFX.playCoin();
            }

            // Populate hint modal content
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
    }
};

// Daily Quests Loader & Claim Handler
function loadDailyQuests() {
    const container = document.getElementById('dailyQuestsContainer');
    if (!container) return;

    fetch('index.php?action=api_get_quests')
        .then(res => res.json())
        .then(res => {
            if (res.status !== 'success' || !res.quests) return;
            let html = '';
            res.quests.forEach(q => {
                const percent = Math.min(100, Math.round((q.progress_count / q.target_count) * 100));
                const isReady = q.is_completed && !q.is_claimed;

                html += `
                    <div class="card card-gamified p-3 mb-3" style="background-color: var(--surface-bg); color: var(--surface-text); border: 1.5px solid var(--border-strong);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 rounded-circle" style="background-color: var(--surface-muted-bg); color: var(--marker-orange); border: 1.5px solid var(--border-strong);">
                                    <i class="bi ${q.icon || 'bi-bullseye'} fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-ink mb-0">${q.title}</h6>
                                    <small class="text-ink-muted">${q.description}</small>
                                </div>
                            </div>
                            <div>
                                ${q.is_claimed ? `
                                    <span class="badge px-3 py-2" style="background-color: var(--surface-muted-bg); color: var(--ink-muted); border: 1px solid var(--border-strong);"><i class="bi bi-check-circle-fill me-1"></i> Claimed</span>
                                ` : (isReady ? `
                                    <button onclick="claimQuest(${q.id})" class="btn btn-primary-gamified btn-sm px-3 py-1 fw-bold">
                                        Claim +${q.xp_reward}XP +${q.coin_reward}Coins!
                                    </button>
                                ` : `
                                    <span class="badge px-2 py-1" style="background-color: var(--surface-bg); color: var(--ink); border: 1px solid var(--border-strong);">${q.progress_count}/${q.target_count}</span>
                                `)}
                            </div>
                        </div>
                        <div class="progress progress-gamified" style="height: 6px;">
                            <div class="progress-bar-fill" role="progressbar" style="width: ${percent}%;"></div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        });
}

function claimQuest(questId) {
    fetch('index.php?action=api_claim_quest', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ quest_id: questId, csrf_token: window.CSRF_TOKEN || '' })
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            SoundFX.playFanfare();
            launchConfetti();
            alert(res.message);
            loadDailyQuests();
        } else {
            alert(res.message);
        }
    });
}

function setPracticeDifficulty(mode) {
    if (typeof PracticeEngine !== 'undefined') {
        PracticeEngine.difficultyMode = mode;
        const textEl = document.getElementById('practiceDiffModeText');
        if (textEl) {
            const map = {
                'adaptive': 'Adaptive',
                'hard': 'Challenger (Hard Only)',
                'medium': 'Medium'
            };
            textEl.innerText = map[mode] || 'Adaptive';
        }
        PracticeEngine.loadNextQuestion(PracticeEngine.subtopicId);
    }
}

// Global Accessible Keyboard Listener: Escape key closes any active dropdowns or menus
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
            const toggle = menu.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]');
            if (toggle && window.bootstrap && bootstrap.Dropdown) {
                const instance = bootstrap.Dropdown.getInstance(toggle);
                if (instance) instance.hide();
            } else {
                menu.classList.remove('show');
            }
        });
    }
});

// Dropdown Viewport Clamping Defense (Ensures dropdowns never clip off screen edges)
document.addEventListener('shown.bs.dropdown', function (e) {
    const dropdownToggle = e.target;
    const menu = dropdownToggle ? dropdownToggle.closest('.dropdown')?.querySelector('.dropdown-menu') : null;
    if (!menu) return;

    const rect = menu.getBoundingClientRect();
    const viewportWidth = document.documentElement.clientWidth || window.innerWidth;
    
    if (rect.right > viewportWidth) {
        const excess = Math.ceil(rect.right - viewportWidth) + 12; // 12px margin from edge
        menu.style.setProperty('transform', `translateX(-${excess}px)`, 'important');
    }
});

document.addEventListener('hidden.bs.dropdown', function (e) {
    const dropdownToggle = e.target;
    const menu = dropdownToggle ? dropdownToggle.closest('.dropdown')?.querySelector('.dropdown-menu') : null;
    if (menu) {
        menu.style.removeProperty('transform');
    }
});
