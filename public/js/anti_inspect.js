/**
 * Anti-Inspect & Exam Integrity Guard (Feature 4)
 * Provides deterrent-level protection for exam surfaces (mock, duel, practice).
 *
 * NOTE: Client-side inspect prevention is a deterrent only.
 * The core exam security remains strictly server-side (hidden answer keys,
 * server-authoritative scoring, and deadline validation).
 */
(function() {
    'use strict';

    const AntiInspect = {
        scope: window.ANTI_INSPECT_SCOPE || 'exam',
        devtoolsTimer: null,
        isDevToolsActive: false,
        skippedDevTools: new Set(),
        onViolationCallback: null,

        /**
         * Determines if an active exam surface is currently in progress.
         */
        isExamActive: function() {
            // 1. If scorecard / review mode is visible, exam is over -> disable restrictions
            const mockScorecard = document.getElementById('mockScoreSheetContainer');
            if (mockScorecard && !mockScorecard.classList.contains('d-none')) {
                return false;
            }

            const duelResults = document.getElementById('duelResults');
            if (duelResults && !duelResults.classList.contains('d-none')) {
                return false;
            }

            const practiceScorecard = document.getElementById('practiceScorecard');
            if (practiceScorecard && !practiceScorecard.classList.contains('d-none')) {
                return false;
            }

            // 2. If scope is 'site', enforce everywhere except review mode
            if (this.scope === 'site') {
                return true;
            }

            // 3. Exam surface detection (scope === 'exam')
            // Mock active quiz zone
            const mockZone = document.getElementById('mockActiveQuizZone');
            if (mockZone && !mockZone.classList.contains('d-none')) {
                const qText = document.getElementById('mockQuestionText');
                if (qText && qText.innerText.trim() !== '') {
                    return true;
                }
            }

            // Duel active arena
            const duelModal = document.getElementById('duelArenaModal');
            if (duelModal && (duelModal.classList.contains('show') || !duelModal.classList.contains('d-none'))) {
                return true;
            }

            // Practice / Quiz shell active question container
            const practiceCard = document.getElementById('practiceCard') || document.getElementById('quizFocusWrapper');
            if (practiceCard && !practiceCard.classList.contains('d-none')) {
                const qText = document.getElementById('questionText');
                if (qText && qText.innerText.trim() !== '') {
                    return true;
                }
            }

            return false;
        },

        /**
         * Checks if an event occurred within a text entry element (<input>, <textarea>, contenteditable).
         */
        isTextEntryTarget: function(target) {
            if (!target) return false;
            const tag = target.tagName ? target.tagName.toLowerCase() : '';
            if (tag === 'input' || tag === 'textarea') return true;
            if (target.isContentEditable) return true;
            return false;
        },

        /**
         * Right-Click Context Menu Interception
         */
        handleContextMenu: function(e) {
            if (!AntiInspect.isExamActive()) return;
            if (AntiInspect.isTextEntryTarget(e.target)) return;

            e.preventDefault();
            if (window.showToast) {
                window.showToast('Right-click is disabled during the exam.', 'warning');
            }
        },

        /**
         * Keyboard Shortcut Interception (F12, DevTools Combos, View Source)
         */
        handleKeyDown: function(e) {
            if (!AntiInspect.isExamActive()) return;

            const isCtrlOrCmd = e.ctrlKey || e.metaKey;
            const key = (e.key || '').toUpperCase();
            const keyCode = e.keyCode || e.which;

            const isF12 = key === 'F12' || keyCode === 123;
            const isDevToolsCombo = isCtrlOrCmd && e.shiftKey && (key === 'I' || key === 'J' || key === 'C' || keyCode === 73 || keyCode === 74 || keyCode === 67);
            const isViewSource = isCtrlOrCmd && (key === 'U' || keyCode === 85);

            if (isF12 || isDevToolsCombo || isViewSource) {
                e.preventDefault();
                e.stopPropagation();
                if (window.showToast) {
                    window.showToast('Developer tool shortcuts are disabled during the exam.', 'warning');
                }
                return false;
            }
        },

        /**
         * Selection & Copy Interception (protects question text, preserves inputs)
         */
        handleCopyCut: function(e) {
            if (!AntiInspect.isExamActive()) return;
            if (AntiInspect.isTextEntryTarget(e.target)) return;

            e.preventDefault();
            if (window.showToast) {
                window.showToast('Copying question text is disabled during the exam.', 'warning');
            }
        },

        handleSelectStart: function(e) {
            if (!AntiInspect.isExamActive()) return;
            if (AntiInspect.isTextEntryTarget(e.target)) return;

            // Prevent text selection only within exam question and option cards
            if (e.target && (e.target.closest('#mockQuestionText') || 
                             e.target.closest('#mockOptionsContainer') || 
                             e.target.closest('.exam-protected-text') ||
                             e.target.closest('#questionText') ||
                             e.target.closest('#optionsContainer') ||
                             e.target.closest('#duelOptionsList'))) {
                e.preventDefault();
            }
        },

        /**
         * DevTools Open Detection via Window Size Delta Heuristic
         * Threshold: width delta > 160px or height delta > 200px persisting for > 1.0 second.
         * Bypasses on touch devices, zoom != 1.0, and split-screen windows.
         */
        checkDevTools: function() {
            if (!AntiInspect.isExamActive()) {
                if (AntiInspect.devtoolsTimer) {
                    clearTimeout(AntiInspect.devtoolsTimer);
                    AntiInspect.devtoolsTimer = null;
                }
                return;
            }

            // 1. Touch device guard (bypasses mobile/tablet to avoid false positives on soft keyboards)
            const isTouch = ('ontouchstart' in window) || 
                            (navigator.maxTouchPoints > 0) || 
                            (window.matchMedia && window.matchMedia('(pointer: coarse)').matches);
            if (isTouch) {
                if (AntiInspect.devtoolsTimer) {
                    clearTimeout(AntiInspect.devtoolsTimer);
                    AntiInspect.devtoolsTimer = null;
                }
                return;
            }

            // 2. Zoom guard (tolerate browser zoom e.g. 150%)
            const dpr = window.devicePixelRatio || 1;
            if (Math.abs(dpr - 1.0) > 0.15) {
                if (AntiInspect.devtoolsTimer) {
                    clearTimeout(AntiInspect.devtoolsTimer);
                    AntiInspect.devtoolsTimer = null;
                }
                return;
            }

            // 3. Window dimension deltas
            const widthDelta = window.outerWidth - window.innerWidth;
            const heightDelta = window.outerHeight - window.innerHeight;

            // Split screen tolerance: snapping window to half-width resizes both outer and inner simultaneously,
            // resulting in small border deltas (< 30px). A docked DevTools panel consumes >= 160px from inner only.
            const isOpen = (widthDelta > 160) || (heightDelta > 200);

            if (isOpen) {
                if (!AntiInspect.devtoolsTimer) {
                    AntiInspect.devtoolsTimer = setTimeout(function() {
                        const curWD = window.outerWidth - window.innerWidth;
                        const curHD = window.outerHeight - window.innerHeight;
                        if (AntiInspect.isExamActive() && ((curWD > 160) || (curHD > 200))) {
                            AntiInspect.triggerDevToolsViolation();
                        }
                        AntiInspect.devtoolsTimer = null;
                    }, 1100); // 1.1s persistence threshold
                }
            } else {
                if (AntiInspect.devtoolsTimer) {
                    clearTimeout(AntiInspect.devtoolsTimer);
                    AntiInspect.devtoolsTimer = null;
                }
            }
        },

        /**
         * Trigger DevTools violation: notifies active exam handler
         */
        triggerDevToolsViolation: function() {
            const event = new CustomEvent('anti-inspect:devtools', {
                detail: {
                    timestamp: Date.now(),
                    reason: 'devtools'
                }
            });
            window.dispatchEvent(event);

            if (typeof AntiInspect.onViolationCallback === 'function') {
                AntiInspect.onViolationCallback('devtools');
            }
        },

        injectStyles: function() {
            if (document.getElementById('anti-inspect-styles')) return;
            const style = document.createElement('style');
            style.id = 'anti-inspect-styles';
            style.textContent = `
                .exam-blurred {
                    filter: blur(8px) !important;
                    user-select: none !important;
                    -webkit-user-select: none !important;
                    pointer-events: none !important;
                    transition: filter 0.2s ease !important;
                }
                .exam-no-select {
                    user-select: none !important;
                    -webkit-user-select: none !important;
                    -moz-user-select: none !important;
                    -ms-user-select: none !important;
                }
            `;
            (document.head || document.documentElement).appendChild(style);
        },

        init: function() {
            this.injectStyles();
            document.addEventListener('contextmenu', this.handleContextMenu);
            document.addEventListener('keydown', this.handleKeyDown);
            window.addEventListener('keydown', this.handleKeyDown);
            document.addEventListener('copy', this.handleCopyCut);
            document.addEventListener('cut', this.handleCopyCut);
            document.addEventListener('selectstart', this.handleSelectStart);

            window.addEventListener('resize', this.checkDevTools);
            setInterval(this.checkDevTools, 2000);
        }
    };

    window.AntiInspect = AntiInspect;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            AntiInspect.init();
        });
    } else {
        AntiInspect.init();
    }
})();
