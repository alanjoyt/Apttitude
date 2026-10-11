<!-- Contact & Developer Details Modal -->
<div class="modal fade" id="contactDetailsModal" tabindex="-1" aria-labelledby="contactModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
    <div class="modal-content border-2 rounded-3 shadow-hard-lg" style="background-color: var(--surface); border-color: var(--border-strong); color: var(--ink);">
      <div class="modal-header py-3 px-3.5 border-bottom" style="border-color: var(--border-strong) !important; background-color: var(--surface-muted-bg);">
        <h5 class="modal-title font-serif fw-bold d-flex align-items-center gap-2 m-0 fs-5 text-ink" id="contactModalTitle">
          <svg class="exam-icon text-accent" style="width:20px;height:20px;"><use href="#icon-bookmark"></use></svg>
          <span>Contact &amp; Developer</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-3.5 d-flex flex-column gap-3">
        <!-- Support Emails Card -->
        <div class="p-3 rounded-2 border" style="background-color: var(--surface-bg); border-color: var(--border-strong) !important;">
          <div class="small fw-bold text-ink-muted text-uppercase mb-2 d-flex align-items-center gap-1.5" style="letter-spacing: 0.05em; font-size: 0.72rem;">
            <svg class="exam-icon text-warning" style="width:13px;height:13px;"><use href="#icon-shield-check"></use></svg> Official Support &amp; Inquiries
          </div>
          <div class="d-flex flex-column gap-2">
            <div class="d-flex align-items-center justify-content-between p-2 rounded border" style="background-color: var(--surface); border-color: var(--border) !important;">
              <div class="d-flex align-items-center gap-2 min-w-0">
                <i class="bi bi-envelope-fill text-accent flex-shrink-0"></i>
                <a href="mailto:alanjoy103@gmail.com" class="text-ink fw-bold text-truncate text-decoration-none small">alanjoy103@gmail.com</a>
              </div>
              <a href="mailto:alanjoy103@gmail.com" class="btn btn-outline-gamified btn-sm py-1 px-2.5 fs-8 text-nowrap flex-shrink-0">
                Email
              </a>
            </div>

            <div class="d-flex align-items-center justify-content-between p-2 rounded border" style="background-color: var(--surface); border-color: var(--border) !important;">
              <div class="d-flex align-items-center gap-2 min-w-0">
                <i class="bi bi-envelope-fill text-accent flex-shrink-0"></i>
                <a href="mailto:asknexyra@gmail.com" class="text-ink fw-bold text-truncate text-decoration-none small">asknexyra@gmail.com</a>
              </div>
              <a href="mailto:asknexyra@gmail.com" class="btn btn-outline-gamified btn-sm py-1 px-2.5 fs-8 text-nowrap flex-shrink-0">
                Email
              </a>
            </div>
          </div>
        </div>

        <!-- Developer Profile Card -->
        <div class="p-3 rounded-2 border" style="background-color: var(--surface-bg); border-color: var(--border-strong) !important;">
          <div class="small fw-bold text-ink-muted text-uppercase mb-2 d-flex align-items-center gap-1.5" style="letter-spacing: 0.05em; font-size: 0.72rem;">
            <svg class="exam-icon text-marker" style="width:13px;height:13px;"><use href="#icon-terminal"></use></svg> Lead Engineer &amp; Designer
          </div>
          <div class="d-flex align-items-center justify-content-between gap-2 p-2.5 rounded border" style="background-color: var(--surface); border-color: var(--border) !important;">
            <div class="min-w-0">
              <div class="fw-bold text-ink fs-6 text-truncate">Alan Joy ~ mr.ignite</div>
              <div class="small text-ink-muted" style="font-size: 0.75rem;">Nexyra Solutions</div>
            </div>
            <a href="https://www.instagram.com/mr.ignite/?utm_source=ig_web_button_share_sheet" target="_blank" rel="noopener noreferrer" class="btn btn-primary-gamified btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5 text-nowrap flex-shrink-0">
              <i class="bi bi-instagram"></i>
              <span>@mr.ignite</span>
              <svg class="exam-icon" style="width:11px;height:11px;"><use href="#icon-arrow-right"></use></svg>
            </a>
          </div>
        </div>
      </div>

      <div class="modal-footer py-2 px-3 border-top d-flex justify-content-between" style="border-color: var(--border-strong) !important; background-color: var(--surface-muted-bg);">
        <span class="small text-ink-muted" style="font-size: 0.75rem;">&copy; 2026 Apttitudee</span>
        <button type="button" class="btn btn-outline-gamified btn-sm py-1 px-3 fw-bold" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function openContactModal(e) {
  if (e && e.preventDefault) e.preventDefault();
  const modalEl = document.getElementById('contactDetailsModal');
  if (modalEl && typeof bootstrap !== 'undefined') {
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  }
}
</script>
