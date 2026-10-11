<!-- Cookie & Data Consent Banner (Keyboard Accessible) -->
<div id="cookieConsentBanner" class="cookie-banner-container fixed-bottom p-3 shadow-lg" style="display: none; z-index: 1060; background-color: var(--surface); border-top: 2px solid var(--highlight); max-width: 100vw; box-sizing: border-box;" role="region" aria-label="Cookie and Privacy Consent">
    <div style="max-width: 1140px; margin: 0 auto; width: 100%; box-sizing: border-box;">
        <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-start gap-2.5 min-w-0" style="flex: 1 1 auto;">
                <div class="flex-shrink-0 pt-0.5" aria-hidden="true">
                    <svg class="exam-icon text-warning" style="width:24px;height:24px;"><use href="#icon-shield-check"></use></svg>
                </div>
                <div class="min-w-0" style="overflow-wrap: anywhere; word-break: break-word;">
                    <h6 class="font-serif fw-bold text-ink mb-1 fs-7">Privacy &amp; Cookie Data Notice</h6>
                    <p class="text-ink-muted small mb-0 lh-base" style="font-size: 0.8rem;">
                        We use essential session cookies and local storage to save your practice progress, sound settings, and login authentication. We do <strong>not</strong> track you across third-party websites or sell user data. Read our <a href="index.php?action=privacy" class="text-accent fw-bold underline">Privacy Policy</a> and <a href="index.php?action=terms" class="text-accent fw-bold underline">Terms &amp; Conditions</a>.
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-start justify-content-lg-end flex-wrap flex-shrink-0">
                <button type="button" id="acceptCookiesBtn" class="btn btn-primary-gamified btn-sm px-3 py-1.5 fw-bold text-nowrap" aria-label="Accept all essential cookies">
                    Accept All
                </button>
                <button type="button" id="essentialOnlyCookiesBtn" class="btn btn-outline-gamified btn-sm px-3 py-1.5 text-nowrap" aria-label="Accept essential cookies only">
                    Essential Only
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('cookieConsentBanner');
    const acceptBtn = document.getElementById('acceptCookiesBtn');
    const essentialBtn = document.getElementById('essentialOnlyCookiesBtn');

    if (banner && !localStorage.getItem('apptitudee_cookie_consent') && !document.body.classList.contains('is-quiz')) {
        banner.style.display = 'block';
    }

    if (acceptBtn) {
        acceptBtn.addEventListener('click', () => {
            localStorage.setItem('apptitudee_cookie_consent', 'accepted');
            banner.style.display = 'none';
        });
    }

    if (essentialBtn) {
        essentialBtn.addEventListener('click', () => {
            localStorage.setItem('apptitudee_cookie_consent', 'essential');
            banner.style.display = 'none';
        });
    }
});
</script>
