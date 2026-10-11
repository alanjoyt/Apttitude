/**
 * Shared Quiz Shell Helpers & Logic (< 2KB)
 * Text sizing, safe inline code formatting, question parsing,
 * trivial code detection, copy feedback, and focus mode exit handlers.
 */
const QuizShell = {
  textSizes: ['small', 'medium', 'large'],

  init() {
    this.applyTextSize(localStorage.getItem('quiz_text_size') || 'medium');
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
    const div = document.createElement('div');
    div.textContent = text;
    let escaped = div.innerHTML;
    return escaped.replace(/`([^`]+)`/g, '<code class="inline-code">$1</code>');
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

    // Sentence splitting with decimal/abbr protection
    let protectedText = text.replace(/(\d+)\.(\d+)/g, '$1__DOT__$2');
    protectedText = protectedText.replace(/\b(e\.g|i\.e|vs|mr|ms|dr|fig|approx|no|p|q|r|s|t)\./gi, '$1__ABBR__');
    const rawSentences = protectedText.split(/(?<=[.?!])\s+(?=[A-Z0-9"\'`])/);
    const sentences = [];
    if (rawSentences) {
      rawSentences.forEach(s => {
        const clean = s.replace(/__DOT__/g, '.').replace(/__ABBR__/g, '.').trim();
        if (clean) sentences.push(clean);
      });
    }

    if ((text.length > 200 || sentences.length >= 3) && sentences.length >= 2) {
      const last = sentences[sentences.length - 1];
      const isPrompt = /\?$/.test(last) || /^(Which|What|How|Find|Determine|Calculate|Identify|Select|Choose)\b/i.test(last);
      if (isPrompt) {
        const setupSentences = sentences.slice(0, sentences.length - 1);
        return {
          sourceTag,
          setupSentences,
          prompt: last,
          fullText: text,
          isSplit: true
        };
      }
    }

    return {
      sourceTag,
      setupSentences: [],
      prompt: text,
      fullText: text,
      isSplit: false
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
    window.location.href = 'index.php?action=technical&view=hub';
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
      comment: comment
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

document.addEventListener('DOMContentLoaded', () => {
  QuizShell.init();
});
