/**
 * Shared Quiz Shell Helpers & Logic (< 2KB)
 * Text sizing, safe inline code formatting, question parsing,
 * trivial code detection, copy feedback, and focus mode exit handlers.
 */
const QuizShell = {
  textSizes: ['small', 'medium', 'large'],

  init() {
    this.applyTextSize(localStorage.getItem('quiz_text_size') || 'medium');
    this.initTouchGestures();
    this.initKeyboardHelper();
  },

  initTouchGestures() {
    let startX = 0;
    let startY = 0;
    const zone = document.querySelector('.quiz-focus-wrapper') || document.getElementById('practiceCard');
    if (!zone) return;

    zone.addEventListener('touchstart', (e) => {
      if (e.touches.length === 1) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
      }
    }, { passive: true });

    zone.addEventListener('touchend', (e) => {
      if (e.changedTouches.length === 1) {
        const diffX = e.changedTouches[0].clientX - startX;
        const diffY = e.changedTouches[0].clientY - startY;
        // Require horizontal swipe of > 60px with less vertical deviation
        if (Math.abs(diffX) > 60 && Math.abs(diffX) > Math.abs(diffY) * 1.5) {
          if (diffX < -50) {
            // Swipe Left -> Next or Check
            const nextBtn = document.getElementById('nextBtn') || document.getElementById('techNextBtn');
            if (nextBtn && nextBtn.style.display !== 'none' && !nextBtn.disabled) {
              handleQuizNext();
            } else {
              const submitBtn = document.getElementById('submitBtn');
              if (submitBtn && !submitBtn.disabled && submitBtn.style.display !== 'none') {
                if (window.PracticeEngine && typeof PracticeEngine.submitAnswer === 'function') {
                  PracticeEngine.submitAnswer();
                } else if (window.TechnicalEngine && typeof TechnicalEngine.submitAnswer === 'function') {
                  TechnicalEngine.submitAnswer();
                }
              }
            }
          } else if (diffX > 50) {
            // Swipe Right -> Skip
            handleQuizSkip();
          }
        }
      }
    }, { passive: true });
  },

  initKeyboardHelper() {
    window.addEventListener('focusin', (e) => {
      if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
        setTimeout(() => {
          e.target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
      }
    });
  },

  getTextSize() {
    return localStorage.getItem('quiz_text_size') || 'medium';
  },

  applyTextSize(size) {
    const wrapper = document.querySelector('.quiz-focus-wrapper') || document.body;
    wrapper.classList.remove('q-size-small', 'q-size-medium', 'q-size-large');
    wrapper.classList.add(`q-size-${size}`);
    localStorage.setItem('quiz_text_size', size);
  },

  cycleTextSize() {
    const current = this.getTextSize();
    const next = this.textSizes[(this.textSizes.indexOf(current) + 1) % this.textSizes.length];
    this.applyTextSize(next);
  },

  formatInlineCode(text) {
    if (!text) return '';

    const codeBlocks = {};
    let counter = 0;
    // 1. Extract fenced code blocks
    let cleanText = text.replace(/```([a-zA-Z0-9_\-+]*)\r?\n([\s\S]*?)```/g, (match, lang, code) => {
      const token = `%%CODEBLOCK_${counter++}%%`;
      const cleanLang = (lang || '').trim();
      const div = document.createElement('div');
      div.textContent = code;
      const escapedCode = div.innerHTML;
      const langClass = cleanLang ? ` class="language-${cleanLang}"` : '';
      codeBlocks[token] = `<pre class="overflow-x-auto my-2 p-2.5 rounded bg-surface border text-start" style="max-height: 350px; overflow-x: auto; white-space: pre;" tabindex="0"><code${langClass}>${escapedCode}</code></pre>`;
      return token;
    });

    const div = document.createElement('div');
    div.textContent = cleanText;
    let escaped = div.innerHTML;

    // Format `inline code`
    escaped = escaped.replace(/`([^`]+)`/g, '<code class="inline-code"></code>');
    // Format **bold**
    escaped = escaped.replace(/\*\*([^*]+)\*\*/g, '<strong class="md-bold"></strong>');
    // Format *italic*
    escaped = escaped.replace(/(?<!\*)\*([^*]+)\*(?!\*)/g, '<em class="md-italic"></em>');

    // Restore fenced code blocks
    for (const token in codeBlocks) {
      escaped = escaped.replace(token, codeBlocks[token]);
    }

    return escaped;
  },

    parseQuestion(rawText) {
    let sourceTag = null;
    let text = (rawText || '').trim();

    const tagMatch = text.match(/^\[([^\]]+)\]\s*/);
    if (tagMatch) {
      const rawTag = tagMatch[1].trim();
      const labMatch = rawTag.match(/^(Innovature\s+Labs)\s+(.*)$/i);
      sourceTag = labMatch ? `${labMatch[1]} · ${labMatch[2]}` : rawTag;
      text = text.slice(tagMatch[0].length).trim();
    }

    return {
      sourceTag,
      source_tag: sourceTag,
      setupSentences: [],
      setup_sentences: [],
      prompt: text,
      fullText: text,
      full_text: text,
      isSplit: false,
      is_split: false
    };
  },

  isTrivialCode(code, questionText) {
    if (!code || !code.trim()) return true;
    const trimmed = code.trim();
    const lines = trimmed.split('\n').map(l => l.trim()).filter(l => l.length > 0);
    if (lines.length === 0) return true;
    const allComments = lines.every(l => /^(\/\/|\/\*|\*|#|--)/.test(l));
    if (allComments) return true;
    if (questionText) {
      const cleanQ = questionText.replace(/<[^>]+>/g, '').toLowerCase().trim();
      const cleanC = trimmed.toLowerCase();
      if (cleanC.length > 10 && cleanQ.includes(cleanC)) return true;
    }
    return false;
  },

  copyCode(code) {
    if (!code) return;
    navigator.clipboard.writeText(code).then(() => {
      document.querySelectorAll('.btn-copy-code').forEach(btn => {
        const copyIcon = btn.querySelector('.copy-icon');
        const checkIcon = btn.querySelector('.check-icon');
        if (copyIcon && checkIcon) {
          copyIcon.classList.add('d-none');
          checkIcon.classList.remove('d-none');
          setTimeout(() => {
            copyIcon.classList.remove('d-none');
            checkIcon.classList.add('d-none');
          }, 1500);
        }
      });
    }).catch(err => console.error('Copy failed', err));
  }
};

function cycleQuizTextSize() { QuizShell.cycleTextSize(); }
function copySnippetCode() {
  const codeEl = document.getElementById('practiceCodeSnippet') || document.getElementById('techCodeSnippet');
  if (codeEl) QuizShell.copyCode(codeEl.innerText);
}

function handleQuizExit(e) {
  if (e && e.preventDefault) e.preventDefault();
  const isAnswerInProgress = (window.PracticeEngine && PracticeEngine.selectedOption) ||
                            (window.TechnicalEngine && TechnicalEngine.selectedOption);
  const isExpShown = (document.getElementById('explanationCard') && document.getElementById('explanationCard').style.display === 'block') ||
                     (document.getElementById('techExplanationCard') && document.getElementById('techExplanationCard').style.display === 'block');

  if (isAnswerInProgress && !isExpShown) {
    if (!confirm('You have an answer selected. Are you sure you want to exit the quiz?')) {
      return;
    }
  }

  if (window.location.search.includes('action=technical')) {
    window.location.href = 'index.php?action=technical';
    return;
  }
  window.location.href = 'index.php?action=practice';
}

function handleQuizHome(e) {
  if (e && e.preventDefault) e.preventDefault();
  const isAnswerInProgress = (window.PracticeEngine && PracticeEngine.selectedOption) ||
                            (window.TechnicalEngine && TechnicalEngine.selectedOption);
  const isExpShown = (document.getElementById('explanationCard') && document.getElementById('explanationCard').style.display === 'block') ||
                     (document.getElementById('techExplanationCard') && document.getElementById('techExplanationCard').style.display === 'block');

  if (isAnswerInProgress && !isExpShown) {
    if (!confirm('You have an answer selected. Are you sure you want to leave the quiz and return to Home?')) {
      return;
    }
  }
  window.location.href = 'index.php?action=landing';
}

function handleQuizSkip() {
  if (window.TechnicalEngine && typeof TechnicalEngine.loadNextQuestion === 'function') {
    TechnicalEngine.loadNextQuestion();
  } else if (window.PracticeEngine && typeof PracticeEngine.loadNextQuestion === 'function') {
    PracticeEngine.loadNextQuestion(PracticeEngine.subtopicId);
  }
}

function handleQuizNext() {
  if (window.TechnicalEngine && typeof TechnicalEngine.loadNextQuestion === 'function') {
    TechnicalEngine.loadNextQuestion();
  } else if (window.PracticeEngine && typeof PracticeEngine.loadNextQuestion === 'function') {
    PracticeEngine.loadNextQuestion(PracticeEngine.subtopicId);
  }
}

function saveQuizProgress() {
  const saveBtn = document.getElementById('saveProgressBtn');
  const labelEl = saveBtn ? saveBtn.querySelector('.btn-quiz-util-label') : null;
  const originalHtml = saveBtn ? saveBtn.innerHTML : '';
  
  if (saveBtn) {
    saveBtn.disabled = true;
    if (labelEl) labelEl.innerText = 'Saving...';
  }

  if (window.PracticeEngine && typeof PracticeEngine.saveProgress === 'function') {
    PracticeEngine.saveProgress();
    setTimeout(() => {
      if (saveBtn) {
        saveBtn.disabled = false;
        if (labelEl) labelEl.innerText = 'Save';
      }
    }, 1000);
    return;
  }

  fetch('index.php?action=api_save_progress')
    .then(r => r.json())
    .then(d => {
      alert(d.message || 'Progress saved successfully!');
    })
    .catch(() => alert('Progress saved to session.'))
    .finally(() => {
      if (saveBtn) {
        saveBtn.disabled = false;
        if (labelEl) labelEl.innerText = 'Save';
      }
    });
}

function openQuizHint() {
  if (window.PracticeEngine && typeof PracticeEngine.openHint === 'function') {
    PracticeEngine.openHint();
    return;
  }
  if (window.TechnicalEngine && typeof TechnicalEngine.openHint === 'function') {
    TechnicalEngine.openHint();
    return;
  }
  const hintModalEl = document.getElementById('hintModal') || document.getElementById('formulaModal');
  if (hintModalEl && window.bootstrap) {
    bootstrap.Modal.getOrCreateInstance(hintModalEl).show();
  }
}

function submitReport() {
  const currentQ = (window.PracticeEngine && PracticeEngine.currentQuestion) ||
                   (window.TechnicalEngine && TechnicalEngine.currentQuestion);
  if (!currentQ || !currentQ.id) {
    alert('Please wait for the question to load before submitting a report.');
    return;
  }

  const reasonEl = document.getElementById('reportReason');
  const commentEl = document.getElementById('reportComment');
  const reason = reasonEl ? reasonEl.value : 'other';
  const comment = commentEl ? commentEl.value.trim() : '';

  const submitBtn = document.getElementById('reportSubmitBtn');
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerText = 'Submitting...';
  }

  fetch('index.php?action=api_submit_report', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      question_id: currentQ.id,
      reason: reason,
      comment: comment,
      csrf_token: window.CSRF_TOKEN || ''
    })
  })
  .then(res => res.json())
  .then(res => {
    alert(res.message || 'Thank you! Your report has been submitted to the question review team.');
    const modalEl = document.getElementById('reportModal');
    if (modalEl && window.bootstrap) {
      bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    }
    if (commentEl) commentEl.value = '';
  })
  .catch(err => {
    console.error('Report submission error:', err);
    alert('Failed to submit report. Please check your network and try again.');
  })
  .finally(() => {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<svg class="exam-icon me-1" style="width:13px;height:13px;"><use href="#icon-check"></use></svg> Submit Report';
    }
  });
}

async function submitStudentQuestion(e) {
  if (e && e.preventDefault) e.preventDefault();
  const alertBox = document.getElementById('subAlertBox');
  const submitBtn = document.getElementById('subSubmitBtn');

  const payload = {
    company_name: (document.getElementById('subCompany')?.value || '').trim(),
    drive_date: document.getElementById('subDriveDate')?.value || '',
    difficulty: document.getElementById('subDifficulty')?.value || 'medium',
    round_pattern: (document.getElementById('subRoundPattern')?.value || '').trim(),
    topic_name: (document.getElementById('subTopicName')?.value || '').trim(),
    question_text: (document.getElementById('subQuestionText')?.value || '').trim(),
    code_snippet: (document.getElementById('subCodeSnippet')?.value || '').trim(),
    option_a: (document.getElementById('subOptA')?.value || '').trim(),
    option_b: (document.getElementById('subOptB')?.value || '').trim(),
    option_c: (document.getElementById('subOptC')?.value || '').trim(),
    option_d: (document.getElementById('subOptD')?.value || '').trim(),
    correct_option: document.getElementById('subCorrectOpt')?.value || 'A',
    explanation: (document.getElementById('subExplanation')?.value || '').trim(),
    csrf_token: window.CSRF_TOKEN || ''
  };

  if (!payload.company_name || !payload.question_text || !payload.option_a || !payload.option_b) {
    if (alertBox) {
      alertBox.className = 'alert alert-warning py-2 small mb-3';
      alertBox.textContent = 'Please fill all required fields (Company, Question stem, Option A & B).';
      alertBox.classList.remove('d-none');
    }
    return;
  }

  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';
  }

  try {
    const res = await fetch('index.php?action=api_submit_user_question', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (data.status === 'success') {
      if (alertBox) {
        alertBox.className = 'alert alert-success py-2 small mb-3';
        alertBox.textContent = data.message;
        alertBox.classList.remove('d-none');
      }
      const form = document.getElementById('studentQuestionForm');
      if (form) form.reset();
      setTimeout(() => {
        const modalEl = document.getElementById('submitQuestionModal');
        if (modalEl && window.bootstrap) {
          bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }
        if (alertBox) alertBox.classList.add('d-none');
      }, 2000);
    } else {
      if (alertBox) {
        alertBox.className = 'alert alert-danger py-2 small mb-3';
        alertBox.textContent = data.message || 'Error submitting question.';
        alertBox.classList.remove('d-none');
      }
    }
  } catch (err) {
    if (alertBox) {
      alertBox.className = 'alert alert-danger py-2 small mb-3';
      alertBox.textContent = 'Server network error submitting question.';
      alertBox.classList.remove('d-none');
    }
  } finally {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<svg class="exam-icon me-1" style="width:14px;height:14px;"><use href="#icon-rocket"></use></svg> Submit to Moderation (+50 XP)';
    }
  }
}
window.submitStudentQuestion = submitStudentQuestion;

document.addEventListener('DOMContentLoaded', () => {
  QuizShell.init();
});
