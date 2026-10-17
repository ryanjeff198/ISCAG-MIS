<!-- ══════════════════════════════════════════════════════════════════
     DAMAYAN CASE MANAGEMENT MODAL
     Death Certificate Processing & Cemetery Plot Management
══════════════════════════════════════════════════════════════════ -->
<div id="damayanCaseModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.55); backdrop-filter:blur(4px); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:18px; width:96%; max-width:1060px; max-height:92vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden; animation:modalSlideUp 0.3s ease;">
    
    <!-- Modal Header -->
    <div class="mis-modal-header" style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:var(--damayan-light); color:var(--damayan-accent); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:22px; height:22px; fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        </div>
        <div>
          <div style="display:flex; align-items:center; gap:8px;">
            <h5 id="modal-ref-id" style="margin:0; font-size:1.15rem; font-weight:800; color:var(--damayan-dark);">Case #BR-0000</h5>
            <span id="modal-status-badge" class="badge-status badge-pending">New Request</span>
            <span id="modal-reqtype-badge" class="badge-status badge-info" style="font-size:0.7rem;">Both Services</span>
            <span id="modal-actiontype-badge" class="badge-status" style="font-size:0.7rem; background:#dbeafe; color:#1e40af;">New Registration</span>
            <span id="modal-sex-badge" class="badge-status" style="font-size:0.7rem; background:#e0e7ff; color:#3730a3;">Male</span>
          </div>
          <div id="modal-deceased-title" style="font-size:0.85rem; color:var(--text-muted); margin-top:2px; font-weight:600;">
            Deceased: <span id="modal-deceased-name" style="color:var(--text-main); font-weight:700;">Loading...</span>
          </div>
        </div>
      </div>
      <button type="button" class="close-btn" onclick="closeCaseModal()" style="background:transparent; border:none; font-size:1.6rem; line-height:1; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>

    <!-- Modal Sub-Navigation Tabs (Visible whole, never scrollable) -->
    <div id="modalTabsBar" style="display:flex; gap:6px; padding:8px 16px 0; background:#f1f5f3; border-bottom:2px solid #e2e8f0; overflow:hidden; width:100%; box-sizing:border-box;">
      <button type="button" class="case-tab-btn active" data-tab="overview" onclick="switchCaseTab('overview', this)">
        <span class="tab-badge">1</span>
        <span class="tab-label">Overview &amp; Verification</span>
      </button>
      <button type="button" class="case-tab-btn" data-tab="deceased" onclick="switchCaseTab('deceased', this)">
        <span class="tab-badge">2</span>
        <span class="tab-label">Deceased Record</span>
      </button>
      <button type="button" class="case-tab-btn" data-tab="death_cert" onclick="switchCaseTab('death_cert', this)">
        <span class="tab-badge">3</span>
        <span class="tab-label">Death Certificate</span>
      </button>
      <button type="button" class="case-tab-btn" data-tab="cemetery_plot" onclick="switchCaseTab('cemetery_plot', this)">
        <span class="tab-badge">4</span>
        <span class="tab-label">Cemetery Plot</span>
      </button>
      <button type="button" class="case-tab-btn" data-tab="logs" onclick="switchCaseTab('logs', this)">
        <span class="tab-badge">5</span>
        <span class="tab-label">Activity Logs</span>
      </button>
    </div>

    <!-- Modal Body (Scrollable) -->
    <div class="mis-modal-body" style="padding:22px 24px; overflow-y:auto; flex:1;">
      
      <!-- Loading State -->
      <div id="modal-loading" style="text-align:center; padding:50px 20px; color:var(--text-muted);">
        <div style="font-size:1rem; font-weight:600;">Loading case details from database...</div>
      </div>

      <!-- Content Container -->
      <div id="modal-content-container" style="display:none;">

        <!-- ─────────────────────────────────────────────────────────────
             TAB 1: OVERVIEW & VERIFICATION
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-overview" class="case-tab-pane">
          
          <!-- Alert Banner for Additional Info Request / Response if applicable -->
          <div id="info-request-alert" style="display:none; padding:14px 16px; border-radius:10px; margin-bottom:18px; background:#fffbeb; border:1px solid #fde68a;">
            <div style="display:flex; align-items:flex-start; gap:10px;">
              <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:#d97706; flex-shrink:0; margin-top:2px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              <div>
                <strong style="color:#92400e; font-size:0.85rem;">Additional Information Required:</strong>
                <div id="info-request-notes-text" style="font-size:0.82rem; color:#78350f; margin-top:3px;"></div>
                <div id="info-response-container" style="margin-top:6px; padding-top:6px; border-top:1px dashed #fcd34d; font-size:0.82rem;">
                  <span style="font-weight:700; color:#047857;">User Submitted Response:</span>
                  <span id="info-submitted-text" style="color:#065f46;"></span>
                  <div id="info-submitted-doc" style="margin-top:4px;"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Rejection Banner if Rejected -->
          <div id="rejection-alert" style="display:none; padding:12px 16px; border-radius:10px; margin-bottom:18px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; font-size:0.85rem;">
            <strong>Rejection Reason:</strong> <span id="rejection-reason-text"></span>
          </div>

          <!-- Copy Request Verification & Record Link Panel -->
          <div id="copy-request-panel" style="display:none; background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:18px; margin-bottom:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="background:#2563eb; color:#fff; font-size:0.75rem; font-weight:800; padding:3px 8px; border-radius:6px; text-transform:uppercase;">Copy Request</span>
                <strong style="color:#1e40af; font-size:0.95rem;">Request for Another Death Certificate Copy</strong>
              </div>
              <span id="v-copy-verified-badge" class="badge-status badge-info" style="font-size:0.75rem;">Awaiting Record Verification</span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; font-size:0.84rem; background:#fff; padding:14px; border-radius:10px; border:1px solid #dbeafe; margin-bottom:14px;">
              <div>
                <span style="color:var(--text-muted); font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Existing Cert / Reg #</span>
                <strong id="v-copy-existing-cert" style="color:var(--damayan-dark);">—</strong>
              </div>
              <div>
                <span style="color:var(--text-muted); font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Copies Requested</span>
                <strong id="v-copy-num-copies">1 copy</strong>
              </div>
              <div>
                <span style="color:var(--text-muted); font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Reason for Request</span>
                <span id="v-copy-reason" style="color:#1f2937; font-weight:600;">—</span>
              </div>
            </div>

            <!-- Verification Status & Actions -->
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; background:#f8fafc; padding:12px 14px; border-radius:8px; border:1px solid #e2e8f0;">
              <div>
                <span style="font-size:0.75rem; font-weight:700; color:#475569; text-transform:uppercase; display:block;">Registered Deceased Record Link</span>
                <div id="v-copy-record-link-desc" style="font-size:0.86rem; font-weight:700; color:#0f172a; margin-top:2px;">Checking database linkage...</div>
              </div>
              <div style="display:flex; gap:8px;">
                <button type="button" id="btn-search-link-record" class="btn-topbar primary" style="padding:6px 14px; font-size:0.8rem; background:#2563eb; border-color:#2563eb;" onclick="showSearchDeceasedModal()">
                  Search &amp; Link Existing Record
                </button>
                <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.8rem; color:#b45309; border-color:#fde68a; background:#fffbeb;" onclick="showRequestInfoModal()">
                  Request More Info
                </button>
              </div>
            </div>
          </div>

          <!-- Section: Encoded Death Certificate Recognized Banner -->
          <div id="v-encoded-cert-overview-banner" style="display:none; margin-bottom:16px;"></div>

          <!-- Section: Applicant & Deceased Information Summary -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
            
            <div class="panel-section" style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px;">
              <div class="panel-section-title" style="font-size:0.75rem; font-weight:800; color:var(--damayan-dark); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:var(--damayan-accent);"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Family Representative / Applicant
              </div>
              <div style="display:flex; flex-direction:column; gap:8px; font-size:0.84rem;">
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Full Name</span><strong id="v-applicant-name">—</strong></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Relationship to Deceased</span><span id="v-relationship">—</span></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Contact Number</span><span id="v-applicant-phone">—</span></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Email Address</span><span id="v-applicant-email">—</span></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Residential Address</span><span id="v-applicant-address">—</span></div>
              </div>
            </div>

            <div class="panel-section" style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px;">
              <div class="panel-section-title" style="font-size:0.75rem; font-weight:800; color:var(--damayan-dark); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:var(--damayan-accent);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                Deceased Summary & Services
              </div>
              <div style="display:flex; flex-direction:column; gap:8px; font-size:0.84rem;">
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Deceased Full Name</span><strong id="v-deceased-fullname">—</strong></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Gender & Nationality</span><span id="v-deceased-gender">—</span> · <span id="v-nationality">Filipino</span></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Date & Place of Death</span><span id="v-death-date">—</span> · <span id="v-death-place">—</span></div>
                <div>
                  <span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Requested Services</span>
                  <div style="margin-top:4px; display:flex; gap:6px; flex-wrap:wrap;">
                    <span id="v-dc-badge" class="badge-status badge-pending" style="font-size:0.75rem;">Death Certificate</span>
                    <span id="v-plot-badge" class="badge-status badge-pending" style="font-size:0.75rem;">Cemetery Plot</span>
                  </div>
                </div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Cemetery Preference</span><span id="v-cemetery-pref">—</span></div>
              </div>
            </div>

          </div>

          <!-- Section: Supporting Document & Notes -->
          <div class="panel-section" style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:20px;">
            <div class="panel-section-title" style="font-size:0.75rem; font-weight:800; color:var(--damayan-dark); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:10px;">
              Verification & Supporting Documents
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; font-size:0.84rem;">
              <div>
                <span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block; margin-bottom:4px;">Supporting Document</span>
                <div id="v-doc-container">
                  <span style="color:var(--text-muted);">No document uploaded</span>
                </div>
              </div>
              <div>
                <span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block; margin-bottom:4px;">Family Notes / Instructions</span>
                <div id="v-family-notes" style="font-style:italic; color:#4b5563;">None provided.</div>
              </div>
            </div>
          </div>

          <!-- Section: Coordinator Assignment -->
          <div class="panel-section" style="background:var(--damayan-light); border:1px solid #c7e6d4; border-radius:12px; padding:16px; margin-bottom:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
              <div>
                <div style="font-size:0.75rem; font-weight:800; color:var(--damayan-dark); text-transform:uppercase; letter-spacing:0.04em;">Assigned Damayan Staff Coordinator</div>
                <div id="v-coordinator-name" style="font-size:0.95rem; font-weight:700; color:var(--damayan-accent); margin-top:2px;">Not Assigned</div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <select id="assign-staff-select" style="padding:7px 12px; font-size:0.82rem; border-radius:8px; border:1px solid var(--border); outline:none; background:#fff;">
                  <option value="">-- Select Staff --</option>
                </select>
                <button type="button" class="btn-topbar primary" style="padding:7px 14px; font-size:0.82rem;" onclick="submitAssignStaff()">Reassign</button>
              </div>
            </div>
          </div>

          <!-- Section: Staff Review Actions -->
          <div style="background:#fff; border:1px solid var(--border); border-radius:12px; padding:18px; margin-bottom:10px;">
            <div style="font-size:0.85rem; font-weight:800; color:var(--damayan-dark); margin-bottom:12px;">Case Review Actions & Workflow Control</div>
            <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
              <button type="button" id="btn-action-accept" class="btn-topbar primary" style="padding:9px 18px; font-size:0.84rem;" onclick="triggerAcceptRequest()">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                Accept Case for Processing
              </button>
              <button type="button" class="btn-topbar" style="padding:9px 18px; font-size:0.84rem; color:#b45309; border-color:#fde68a; background:#fffbeb;" onclick="showRequestInfoModal()">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Request Additional Information
              </button>
              <button type="button" class="btn-topbar" style="padding:9px 18px; font-size:0.84rem; color:#dc2626; border-color:#fecaca; background:#fef2f2;" onclick="showRejectModal()">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                Reject Request
              </button>
            </div>
          </div>

        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 2: DECEASED RECORD (Linked Deceased Case)
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-deceased" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Deceased Person Record</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              View and verify information about the deceased. Updates here keep both Death Certificate and Cemetery Plot records synchronized.
            </div>
          </div>

          <form id="deceased-record-form" onsubmit="event.preventDefault(); submitDeceasedForm();">
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:14px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">First Name *</label>
                <input type="text" id="df-fname" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Middle Name</label>
                <input type="text" id="df-mname" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Last Name *</label>
                <input type="text" id="df-lname" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:14px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Gender / Sex *</label>
                <select id="df-sex" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;">
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Date of Birth</label>
                <input type="date" id="df-dob" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Date of Death *</label>
                <input type="date" id="df-dod" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Place of Death *</label>
                <input type="text" id="df-pod" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Last Residence</label>
                <input type="text" id="df-residence" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:20px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Civil Status</label>
                <select id="df-civil-status" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;">
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Widowed">Widowed</option>
                  <option value="Divorced">Divorced</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Nationality</label>
                <input type="text" id="df-nationality" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Religion</label>
                <input type="text" id="df-religion" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Save Deceased Record</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 3: DEATH CERTIFICATE PROCESSING
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-death_cert" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
              <h6 style="color:var(--damayan-dark); margin:0; font-size:0.95rem;">Death Certificate Tracking &amp; Coordination</h6>
              <span id="dc-current-stage-badge" class="badge-status badge-pending">Request Received</span>
            </div>
            <div style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;">
              Track the official preparation, hospital coordination, transmittal to Local Civil Registrar (LCRO), and civil registration progress.
            </div>
          </div>

          <div id="dc-not-requested-alert" style="display:none; padding:14px 16px; background:#f3f4f6; border-radius:10px; margin-bottom:18px; color:#4b5563; font-size:0.85rem;">
            Notice: Death certificate was not initially flagged for this request (Applicant selected Cemetery Plot only). You may still initiate processing if requested by the family below.
          </div>

          <!-- Specialized Copy Issuance & Tracking Section -->
          <div id="copy-issuance-section" style="display:none; background:#fafbfc; border:1px solid #93c5fd; border-radius:12px; padding:18px; margin-bottom:18px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
              <h6 style="color:#1e40af; margin:0; font-size:0.95rem; font-weight:800;">Issue Additional Death Certificate Copy</h6>
              <span id="copy-issuance-status-badge" class="badge-status badge-pending">Pending Issuance</span>
            </div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:14px;">
              Staff must verify the existing registered record before issuing. Official copies are marked received only when confirmed by the requester.
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Copy Tracking / Reference Number *</label>
                <input type="text" id="copy-issue-ref" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. CPY-2026-0084 or PSA-CC-5521" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Staff Issuance Remarks</label>
                <input type="text" id="copy-issue-remarks" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. 1 official copy prepared for family reference..." />
              </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding-top:12px; border-top:1px dashed #cbd5e1; flex-wrap:wrap; gap:10px;">
              <div id="copy-receipt-status-info" style="font-size:0.82rem; color:#475569;">
                <strong>Receipt Status:</strong> <span id="copy-receipt-status-text">Not issued</span>
              </div>
              <button type="button" class="btn-topbar primary" style="background:#2563eb; border-color:#2563eb; padding:8px 18px; font-size:0.84rem;" onclick="submitIssueCopy()">
                Confirm &amp; Issue Certificate Copy
              </button>
            </div>
          </div>

          <form id="death-cert-form" onsubmit="event.preventDefault(); submitDeathCertForm();">
            
            <div class="panel-section" style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:16px;">
              <div class="form-group" style="margin-bottom:14px;">
                <label style="font-size:0.82rem; font-weight:800; color:var(--damayan-dark); display:block; margin-bottom:6px;">Current Processing Stage *</label>
                <select id="dc-status" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.88rem; font-weight:600; color:var(--damayan-dark); background:#fff;">
                  <option value="Request Received">1. Request Received</option>
                  <option value="Under Review">2. Under Review</option>
                  <option value="Additional Information Required">3. Additional Information Required</option>
                  <option value="Awaiting Authorized Certificate Preparation">4. Awaiting Authorized Certificate Preparation</option>
                  <option value="Certificate Prepared and Certified">5. Certificate Prepared and Certified</option>
                  <option value="Submitted for Civil Registration">6. Submitted for Civil Registration</option>
                  <option value="Registered">7. Registered</option>
                  <option value="Certified Copy Requested or Received">8. Certified Copy Requested or Received</option>
                  <option value="Rejected">Rejected</option>
                </select>
              </div>

              <!-- Stage Dates -->
              <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:14px; margin-bottom:14px;">
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Preparation Date</label>
                  <input type="date" id="dc-prep-date" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
                </div>
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Certified Date</label>
                  <input type="date" id="dc-certified-date" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
                </div>
              </div>

              <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:14px; margin-bottom:14px;">
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Submitted to LCRO Date</label>
                  <input type="date" id="dc-submitted-lcro-date" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
                </div>
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Official Registered Date</label>
                  <input type="date" id="dc-registered-date" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
                </div>
              </div>
            </div>

            <!-- Civil Registry and Document References -->
            <div class="panel-section" style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:16px;">
              <div style="font-size:0.75rem; font-weight:800; color:var(--damayan-dark); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px;">
                Official Document References &amp; Registry Identifiers
              </div>
              <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:14px;">
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">LCRO Registry Number</label>
                  <input type="text" id="dc-registry-no" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. 2026-00412" />
                </div>
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">LCRO Transmittal Ref</label>
                  <input type="text" id="dc-lcro-ref" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. TR-2026-088" />
                </div>
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Certified Copy / PSA Ref</label>
                  <input type="text" id="dc-certified-copy-ref" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. PSA-CC-9912" />
                </div>
              </div>

              <div class="form-group" style="margin-bottom:14px;">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Responsible Personnel / Hospital / Office Contact</label>
                <input type="text" id="dc-responsible-personnel" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. Dr. A. Usman (Hospital Lying-in) / Officer B. Ramos (LCRO)" />
              </div>

              <div class="form-group" style="margin-bottom:10px;">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Staff Remarks &amp; Progress Notes</label>
                <textarea id="dc-remarks" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Hospital coordination notes, verification updates, transmittal receipt details..."></textarea>
              </div>

              <div id="dc-rejection-row" class="form-group" style="display:none; margin-top:10px;">
                <label style="font-size:0.8rem; font-weight:700; color:#dc2626; display:block; margin-bottom:4px;">Death Certificate Rejection Reason *</label>
                <textarea id="dc-rejection-reason" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid #fca5a5; font-size:0.85rem; background:#fff5f5;" placeholder="Specify why the death certificate request cannot be processed..."></textarea>
              </div>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Update Death Certificate Status</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 4: CEMETERY PLOT MANAGEMENT
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-cemetery_plot" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
              <h6 style="color:var(--damayan-dark); margin:0; font-size:0.95rem;">Cemetery Plot Management &amp; Assignment</h6>
              <span id="cp-current-status-badge" class="badge-status badge-pending">Pending Review</span>
            </div>
            <div style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;">
              Manage plot availability, prevent duplicate allocations, and assign burial plots for the deceased.
            </div>
          </div>

          <div id="cp-not-requested-alert" style="display:none; padding:14px 16px; background:#f3f4f6; border-radius:10px; margin-bottom:18px; color:#4b5563; font-size:0.85rem;">
            Notice: Cemetery plot was not initially selected by the family (Applicant requested Death Certificate only). You may allocate a plot if requested below.
          </div>

          <!-- Currently Assigned Plot Highlight -->
          <div id="cp-assigned-highlight" style="display:none; padding:14px 18px; border-radius:12px; margin-bottom:16px; background:#ecfdf5; border:1px solid #a7f3d0;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
              <div style="display:flex; align-items:center; gap:10px;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:var(--damayan-accent);"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                <div>
                  <div style="font-size:0.75rem; font-weight:800; color:#065f46; text-transform:uppercase;">Currently Assigned Burial Plot</div>
                  <div id="cp-assigned-display-text" style="font-size:0.92rem; font-weight:700; color:var(--damayan-dark); margin-top:1px;">
                    ISCAG Cemetery - Section A, Plot #101
                  </div>
                </div>
              </div>
              <span class="badge-status badge-active" style="font-size:0.75rem;">Assigned</span>
            </div>
          </div>

          <form id="cemetery-plot-form" onsubmit="event.preventDefault(); submitCemeteryPlotForm();">
            
            <div class="panel-section" style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:16px;">
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div class="form-group">
                  <label style="font-size:0.82rem; font-weight:800; color:var(--damayan-dark); display:block; margin-bottom:6px;">Plot Request Status *</label>
                  <select id="cp-status" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.88rem; font-weight:600; color:var(--damayan-dark); background:#fff;" onchange="handlePlotStatusChange()">
                    <option value="Pending Review">Pending Review</option>
                    <option value="Under Review">Under Review</option>
                    <option value="Available for Assignment">Available for Assignment</option>
                    <option value="Approved">Approved</option>
                    <option value="Plot Assigned">Plot Assigned</option>
                    <option value="Awaiting Plot Availability">Awaiting Plot Availability</option>
                    <option value="Unavailable">Unavailable</option>
                    <option value="Rejected">Rejected</option>
                  </select>
                </div>
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:6px;">Family Cemetery Preference</label>
                  <input type="text" id="cp-family-pref" readonly style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#f3f4f6; color:#4b5563;" />
                </div>
              </div>

              <!-- Available Inventory Dropdown Selection -->
              <div class="form-group" style="margin-bottom:14px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
                  <label style="font-size:0.8rem; font-weight:700; color:var(--damayan-dark);">Select from Available Cemetery Inventory (Unique Assignment)</label>
                  <span style="font-size:0.75rem; color:var(--damayan-accent); font-weight:600;">Prevents Duplicate Plots</span>
                </div>
                <select id="cp-inventory-select" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;" onchange="onSelectInventoryPlot(this)">
                  <option value="">-- Choose an Available Plot from Database --</option>
                </select>
              </div>

              <!-- Assignment Fields -->
              <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:14px;">
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Cemetery Name *</label>
                  <input type="text" id="cp-cemetery-name" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. ISCAG Muslim Cemetery" />
                </div>
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Section / Block</label>
                  <input type="text" id="cp-section" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. Section A" />
                </div>
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Plot / Grave Number *</label>
                  <input type="text" id="cp-plot-number" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. A-101" />
                </div>
              </div>

              <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Plot Assignment Date</label>
                  <input type="date" id="cp-assigned-date" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
                </div>
                <div class="form-group">
                  <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Cemetery Plot ID (Internal)</label>
                  <input type="text" id="cp-plot-id" readonly style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#f3f4f6; color:#6b7280;" placeholder="Auto-linked" />
                </div>
              </div>

              <div class="form-group" style="margin-bottom:10px;">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Plot Remarks &amp; Caretaker Notes</label>
                <textarea id="cp-remarks" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Grave preparation, caretaker instructions, orientation (Qiblah)..."></textarea>
              </div>

              <div id="cp-rejection-row" class="form-group" style="display:none; margin-top:10px;">
                <label style="font-size:0.8rem; font-weight:700; color:#dc2626; display:block; margin-bottom:4px;">Plot Rejection / Unavailability Reason *</label>
                <textarea id="cp-rejection-reason" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid #fca5a5; font-size:0.85rem; background:#fff5f5;" placeholder="Specify why the burial plot cannot be allocated or why the request was rejected..."></textarea>
              </div>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Save Cemetery Plot Assignment</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 5: ACTIVITY LOGS & AUDIT TRAIL
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-logs" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Chronological Case Audit Trail</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              Every status change, staff action, Death Certificate update, and Cemetery Plot assignment is recorded with responsible actor and timestamp.
            </div>
          </div>

          <div id="activity-logs-container" style="display:flex; flex-direction:column; gap:12px;">
            <!-- Populated dynamically via renderActivityLogs() -->
          </div>
        </div>

      </div>

    </div>

    <!-- Modal Footer -->
    <div class="mis-modal-footer" style="padding:14px 24px; border-top:1px solid var(--border); background:#fafbfc; display:flex; align-items:center; justify-content:space-between;">
      <div style="font-size:0.75rem; color:var(--text-muted);">
        ISCAG Damayan Death Certificate &amp; Cemetery Plot Management System
      </div>
      <button type="button" class="btn-topbar" style="padding:6px 18px; font-size:0.82rem;" onclick="closeCaseModal()">Close</button>
    </div>

  </div>
</div>

<!-- Modal: Request Additional Info Dialog -->
<div id="damayanReqInfoModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100000; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#fff; border-radius:14px; max-width:500px; width:100%; padding:22px; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
    <h5 style="margin:0 0 6px; font-size:1.05rem; color:var(--damayan-dark); font-weight:800;">Request Additional Information</h5>
    <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:14px;">
      Notify the family representative of missing documents or clarification needed for this case.
    </div>
    <form onsubmit="event.preventDefault(); submitRequestInfo();">
      <div class="form-group" style="margin-bottom:16px;">
        <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Requirements / Details Needed *</label>
        <textarea id="reqinfo-explanation" rows="4" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. Please provide a clearer copy of the hospital death summary, medical certificate, or valid ID of the representative..."></textarea>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn-topbar" onclick="closeReqInfoModal()">Cancel</button>
        <button type="submit" class="btn-topbar primary" style="background:#d97706; border-color:#d97706;">Send Requirement Notice</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Reject Case Dialog -->
<div id="damayanRejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100000; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#fff; border-radius:14px; max-width:500px; width:100%; padding:22px; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
    <h5 style="margin:0 0 6px; font-size:1.05rem; color:#dc2626; font-weight:800;">Reject Burial Request</h5>
    <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:14px;">
      State the clear reason for rejecting this application. The family will be notified.
    </div>
    <form onsubmit="event.preventDefault(); submitRejectRequest();">
      <div class="form-group" style="margin-bottom:16px;">
        <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Rejection Reason *</label>
        <textarea id="reject-reason" rows="3" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #fca5a5; font-size:0.85rem;" placeholder="e.g. Duplicate submission, invalid deceased credentials, out of service jurisdiction..."></textarea>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn-topbar" onclick="closeRejectModal()">Cancel</button>
        <button type="submit" class="btn-topbar" style="background:#dc2626; color:#fff; border-color:#dc2626;">Confirm Rejection</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Search & Link Existing Deceased Record -->
<div id="searchDeceasedModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); backdrop-filter:blur(3px); z-index:100001; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#fff; border-radius:16px; max-width:640px; width:100%; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 25px 50px rgba(0,0,0,0.25); overflow:hidden;">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <h5 style="margin:0; font-size:1.05rem; color:var(--damayan-dark); font-weight:800;">Link Existing Registered Deceased Record</h5>
      <button type="button" onclick="closeSearchDeceasedModal()" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>
    <div style="padding:18px 20px; flex:1; overflow-y:auto;">
      <div style="font-size:0.82rem; color:var(--text-muted); margin-bottom:14px;">
        Important: Do not create duplicate records. Search and select the existing registered deceased person from previous ISCAG records or death registrations.
      </div>
      <div style="display:flex; gap:8px; margin-bottom:16px;">
        <input type="text" id="deceased-search-query" style="flex:1; padding:8px 14px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Search deceased name, date of death, or ref #..." onkeyup="if(event.key==='Enter') executeSearchDeceased()" />
        <button type="button" class="btn-topbar primary" style="padding:8px 16px; font-size:0.82rem;" onclick="executeSearchDeceased()">Search</button>
      </div>

      <div id="deceased-search-results" style="max-height:260px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
        <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:0.85rem;">Click 'Search' to search existing deceased records.</div>
      </div>
    </div>
    <div style="padding:12px 20px; border-top:1px solid var(--border); background:#fafbfc; display:flex; justify-content:flex-end; gap:8px;">
      <button type="button" class="btn-topbar" onclick="closeSearchDeceasedModal()">Cancel</button>
    </div>
  </div>
</div>

<style>
  :root {
    --damayan-accent: #176b45;
    --damayan-dark: #0f5c3a;
    --damayan-light: #e8f5ed;
  }
  #modalTabsBar {
    display: flex;
    gap: 6px;
    padding: 8px 16px 0;
    background: #f1f5f3;
    border-bottom: 2px solid #e2e8f0;
    overflow: hidden;
    width: 100%;
    box-sizing: border-box;
  }
  .case-tab-btn {
    flex: 1 1 0;
    min-width: 0;
    padding: 11px 6px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #4b5563;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-align: center;
    white-space: nowrap;
    outline: none;
  }
  .case-tab-btn:hover {
    color: var(--damayan-dark);
    background: rgba(255, 255, 255, 0.75);
  }
  .case-tab-btn.active {
    color: var(--damayan-accent) !important;
    background: #ffffff !important;
    border-bottom: 3px solid var(--damayan-accent) !important;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
    font-weight: 800;
  }
  .case-tab-btn .tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #475569;
    font-size: 0.72rem;
    font-weight: 800;
    flex-shrink: 0;
    transition: all 0.2s ease;
  }
  .case-tab-btn.active .tab-badge {
    background: var(--damayan-accent);
    color: #ffffff;
  }
  .case-tab-btn .tab-label {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  @media (max-width: 900px) {
    .case-tab-btn {
      padding: 9px 4px;
      font-size: 0.75rem;
      gap: 4px;
    }
    .case-tab-btn .tab-badge {
      width: 18px;
      height: 18px;
      font-size: 0.68rem;
    }
  }
  @keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<script>
  let activeCase = null;
  let activeStaffList = [];
  let activeAvailablePlots = [];
  let activeAllPlots = [];
  let activeAuditLogs = [];
  let currentCaseTab = 'overview';

  function switchCaseTab(tabKey, btnElement) {
    currentCaseTab = tabKey;
    document.querySelectorAll('.case-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.case-tab-pane').forEach(p => p.style.display = 'none');
    
    // Activate target button
    const targetBtn = btnElement || document.querySelector(`.case-tab-btn[data-tab="${tabKey}"]`);
    if (targetBtn) {
      targetBtn.classList.add('active');
    }
    const pane = document.getElementById('tab-' + tabKey);
    if(pane) pane.style.display = 'block';
  }

  async function openCaseModal(caseIdOrRef, preserveTab = false) {
    const modal = document.getElementById('damayanCaseModal');
    const loading = document.getElementById('modal-loading');
    const content = document.getElementById('modal-content-container');
    
    modal.style.display = 'flex';
    loading.style.display = 'block';
    content.style.display = 'none';

    // Set active tab (preserving user position if specified)
    const activeTabToSelect = (preserveTab && currentCaseTab) ? currentCaseTab : 'overview';
    switchCaseTab(activeTabToSelect);

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/details') ?>?id=' + encodeURIComponent(caseIdOrRef));
      const data = await res.json();
      if(!data.success || !data.burial) {
        showAlert('Error', data.error || 'Failed to load burial case details.', 'error');
        closeCaseModal();
        return;
      }

      activeCase = data.burial;
      activeStaffList = data.staff_list || [];
      activeAvailablePlots = data.available_plots || [];
      activeAllPlots = data.all_plots || [];
      activeAuditLogs = data.audit_logs || [];

      populateCaseModal(data);
      loading.style.display = 'none';
      content.style.display = 'block';
    } catch(err) {
      console.error(err);
      showAlert('Error', 'An error occurred while fetching case details.', 'error');
      closeCaseModal();
    }
  }

  function closeCaseModal() {
    document.getElementById('damayanCaseModal').style.display = 'none';
    activeCase = null;
  }

  function populateCaseModal(data) {
    const c = data.burial;

    // Header
    document.getElementById('modal-ref-id').textContent = '#' + c.ref_id;
    document.getElementById('modal-deceased-name').textContent = c.deceased_name || (c.first_name + ' ' + c.last_name);
    
    // Status Badge
    const statusBadge = document.getElementById('modal-status-badge');
    statusBadge.textContent = c.status;
    statusBadge.className = 'badge-status ' + getBadgeClass(c.status);

    // Request Type Badge
    const reqTypeBadge = document.getElementById('modal-reqtype-badge');
    const rType = (c.request_type || 'both').toLowerCase();
    if (rType === 'death_certificate') {
      reqTypeBadge.textContent = 'Death Certificate Only';
      reqTypeBadge.className = 'badge-status badge-info';
    } else if (rType === 'cemetery_plot') {
      reqTypeBadge.textContent = 'Cemetery Plot Only';
      reqTypeBadge.className = 'badge-status badge-info';
    } else {
      reqTypeBadge.textContent = 'Both Services';
      reqTypeBadge.className = 'badge-status badge-active';
    }

    // Action Type Badge (Register vs Copy)
    const actionBadge = document.getElementById('modal-actiontype-badge');
    const isCopy = (c.cert_action_type === 'copy');
    if (actionBadge) {
      if (isCopy) {
        actionBadge.textContent = 'Copy Request';
        actionBadge.style.background = '#dbeafe';
        actionBadge.style.color = '#1e40af';
      } else {
        actionBadge.textContent = 'New Registration';
        actionBadge.style.background = '#d1fae5';
        actionBadge.style.color = '#065f46';
      }
    }

    const sexBadge = document.getElementById('modal-sex-badge');
    sexBadge.textContent = c.deceased_sex || 'Male';
    sexBadge.style.background = (c.deceased_sex === 'Female') ? '#fce7f3' : '#e0e7ff';
    sexBadge.style.color = (c.deceased_sex === 'Female') ? '#9d174d' : '#3730a3';

    // ──────────────── TAB 1: OVERVIEW ────────────────
    const isEncodedLinked = Boolean(c.is_encoded_linked || c.encoded_cert_id || c.encoded_reg_no);
    const encBanner = document.getElementById('v-encoded-cert-overview-banner');
    if (encBanner) {
      if (isEncodedLinked && !isCopy) {
        encBanner.style.display = 'block';
        encBanner.innerHTML = `
          <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:12px; padding:14px 18px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div style="width:36px; height:36px; border-radius:50%; background:#d1fae5; display:flex; align-items:center; justify-content:center; color:#059669; flex-shrink:0;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
              </div>
              <div>
                <div style="font-size:0.75rem; font-weight:800; color:#065f46; text-transform:uppercase; letter-spacing:0.04em;">Recognized &amp; Linked to Encoded Death Certificate</div>
                <div style="font-size:0.95rem; font-weight:700; color:#064e3b; margin-top:1px;">
                  Registration #: <strong>${escapeHtml(c.encoded_reg_no || c.death_cert_registry_no || c.existing_cert_no || '')}</strong>
                  <span style="font-weight:500; color:#047857; margin-left:8px;">Deceased: ${escapeHtml(c.encoded_deceased_name || c.deceased_name || '')} · Rep: ${escapeHtml(c.encoded_representative_name || c.informant_name || 'N/A')}</span>
                </div>
                <div style="font-size:0.72rem; color:#6b7280; margin-top:2px;">
                  Encoded by: <strong>${escapeHtml(c.encoded_by_name || 'Damayan Staff')}</strong> on ${escapeHtml(c.encoded_at || 'N/A')}
                </div>
              </div>
            </div>
            <span class="badge-status badge-active" style="font-size:0.75rem;">Verified Link</span>
          </div>
        `;
      } else {
        encBanner.style.display = 'none';
      }
    }

    const copyPanel = document.getElementById('copy-request-panel');
    if (copyPanel) {
      if (isCopy) {
        copyPanel.style.display = 'block';
        document.getElementById('v-copy-existing-cert').textContent = c.encoded_reg_no || c.existing_cert_no || 'Not Provided';
        document.getElementById('v-copy-num-copies').textContent = (c.num_copies || 1) + (parseInt(c.num_copies) > 1 ? ' copies' : ' copy');
        document.getElementById('v-copy-reason').textContent = c.copy_reason || 'Personal / Official Use';

        const copyVerifiedBadge = document.getElementById('v-copy-verified-badge');
        const recordLinkDesc = document.getElementById('v-copy-record-link-desc');
        if (isEncodedLinked) {
          copyVerifiedBadge.textContent = 'Record Verified & Linked (Encoded Death Cert)';
          copyVerifiedBadge.className = 'badge-status badge-active';
          recordLinkDesc.innerHTML = `
            <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:12px; margin-top:4px;">
              <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
                <span style="color:#065f46; font-weight:800; font-size:0.75rem; text-transform:uppercase; display:flex; align-items:center; gap:4px;">
                  <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:#059669;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                  Linked to Encoded Death Certificate (Same Registration # Matched)
                </span>
                <span class="badge-status badge-active" style="font-size:0.68rem;">Verified in Archive</span>
              </div>
              <div style="font-size:0.95rem; font-weight:800; color:#064e3b;">
                Registration #: ${escapeHtml(c.encoded_reg_no || c.existing_cert_no || '')}
              </div>
              <div style="font-size:0.82rem; color:#047857; margin-top:2px;">
                Deceased: <strong>${escapeHtml(c.encoded_deceased_name || c.deceased_name || '')}</strong> · Representative: <strong>${escapeHtml(c.encoded_representative_name || c.informant_name || 'N/A')}</strong>
              </div>
              <div style="font-size:0.72rem; color:#6b7280; margin-top:4px;">
                Encoded by: <strong>${escapeHtml(c.encoded_by_name || 'Damayan Staff')}</strong> on ${escapeHtml(c.encoded_at || 'N/A')}
              </div>
            </div>
          `;
        } else if (parseInt(c.existing_record_verified) === 1) {
          copyVerifiedBadge.textContent = 'Record Verified & Linked';
          copyVerifiedBadge.className = 'badge-status badge-active';
          recordLinkDesc.innerHTML = `<span style="color:#059669;">Linked to Registered Deceased Record #${escapeHtml(c.deceased_id || '')}: ${escapeHtml(c.deceased_name || '')} (DOD: ${escapeHtml(c.date_of_death || 'N/A')})</span>`;
        } else {
          copyVerifiedBadge.textContent = 'Awaiting Record Search';
          copyVerifiedBadge.className = 'badge-status badge-warning';
          recordLinkDesc.innerHTML = `<span style="color:#dc2626;">No existing registered record linked yet. Search and link to an existing record, or request additional information from the applicant.</span>`;
        }
      } else {
        copyPanel.style.display = 'none';
      }
    }
    const applicantName = c.informant_name || ((c.user_first_name || '') + ' ' + (c.user_last_name || '')) || 'Applicant';
    document.getElementById('v-applicant-name').textContent = applicantName;
    document.getElementById('v-relationship').textContent = c.relationship || 'Family Member';
    document.getElementById('v-applicant-phone').textContent = c.informant_contact || c.user_contact || 'N/A';
    document.getElementById('v-applicant-email').textContent = c.informant_email || c.user_email || 'N/A';
    document.getElementById('v-applicant-address').textContent = c.informant_address || c.residence || 'N/A';

    document.getElementById('v-deceased-fullname').textContent = c.deceased_name;
    document.getElementById('v-deceased-gender').textContent = (c.deceased_sex === 'Female' ? 'Female' : 'Male');
    document.getElementById('v-nationality').textContent = c.nationality || 'Filipino';
    document.getElementById('v-death-date').textContent = c.date_of_death || 'N/A';
    document.getElementById('v-death-place').textContent = c.place_of_death || 'N/A';
    document.getElementById('v-cemetery-pref').textContent = c.cemetery_preference || c.assigned_cemetery_name || 'ISCAG Muslim Cemetery';
    document.getElementById('v-family-notes').textContent = c.additional_notes || 'None provided.';

    // Requested Services Badges
    const dcRequested = (parseInt(c.death_cert_requested) === 1) || (rType === 'both') || (rType === 'death_certificate');
    const plotRequested = (parseInt(c.cemetery_plot_requested) === 1) || (rType === 'both') || (rType === 'cemetery_plot');
    
    const dcBadge = document.getElementById('v-dc-badge');
    dcBadge.textContent = 'Death Certificate: ' + (c.death_cert_status || (dcRequested ? 'Requested' : 'Not Requested'));
    dcBadge.className = 'badge-status ' + (dcRequested ? getBadgeClass(c.death_cert_status) : 'badge-disabled');

    const plotBadge = document.getElementById('v-plot-badge');
    plotBadge.textContent = 'Cemetery Plot: ' + (c.cemetery_plot_status || (plotRequested ? 'Requested' : 'Not Requested'));
    plotBadge.className = 'badge-status ' + (plotRequested ? getBadgeClass(c.cemetery_plot_status) : 'badge-disabled');

    // Supporting document
    const docContainer = document.getElementById('v-doc-container');
    if(c.supporting_document) {
      const docUrl = c.supporting_document.startsWith('http') ? c.supporting_document : ('<?= url('') ?>/' + c.supporting_document.replace(/^\/+/, ''));
      docContainer.innerHTML = `
        <a href="${docUrl}" target="_blank" class="btn-topbar" style="display:inline-flex; align-items:center; gap:6px; color:var(--damayan-accent); font-weight:700; text-decoration:none;">
          <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
          View Attached Document
        </a>
      `;
    } else {
      docContainer.innerHTML = '<span style="color:var(--text-muted); font-style:italic;">No supporting document uploaded</span>';
    }

    // Additional info banner
    const infoAlert = document.getElementById('info-request-alert');
    if(c.info_request_notes) {
      infoAlert.style.display = 'block';
      document.getElementById('info-request-notes-text').textContent = c.info_request_notes;
      if(c.additional_info_submitted) {
        document.getElementById('info-response-container').style.display = 'block';
        document.getElementById('info-submitted-text').textContent = ' ' + c.additional_info_submitted;
        if(c.additional_doc_path) {
          const addDocUrl = c.additional_doc_path.startsWith('http') ? c.additional_doc_path : ('<?= url('') ?>/' + c.additional_doc_path.replace(/^\/+/, ''));
          document.getElementById('info-submitted-doc').innerHTML = `<a href="${addDocUrl}" target="_blank" style="color:var(--damayan-accent);font-weight:700;">View Submitted File</a>`;
        } else {
          document.getElementById('info-submitted-doc').innerHTML = '';
        }
      } else {
        document.getElementById('info-response-container').style.display = 'none';
      }
    } else {
      infoAlert.style.display = 'none';
    }

    // Rejection banner
    const rejAlert = document.getElementById('rejection-alert');
    if(c.status === 'Rejected' && c.rejection_reason) {
      rejAlert.style.display = 'block';
      document.getElementById('rejection-reason-text').textContent = c.rejection_reason;
    } else {
      rejAlert.style.display = 'none';
    }

    // Coordinator name & staff dropdown
    document.getElementById('v-coordinator-name').textContent = c.assigned_staff_name || 'Unassigned';
    const staffSelect = document.getElementById('assign-staff-select');
    staffSelect.innerHTML = '<option value="">-- Select Staff --</option>' + (data.staff_list || []).map(s => {
      const sId = s.tenant_id || s.id;
      const sName = (s.first_name && s.last_name) ? (s.first_name + ' ' + s.last_name).trim() : (s.name || 'Staff');
      return `
        <option value="${sId}" ${sId == c.assigned_staff_id ? 'selected' : ''}>
          ${sName} (${s.role || 'Staff'})
        </option>
      `;
    }).join('');

    // ──────────────── TAB 2: DECEASED RECORD ────────────────
    const names = (c.deceased_name || '').split(' ');
    document.getElementById('df-fname').value = c.deceased_first_name || names[0] || '';
    document.getElementById('df-mname').value = c.deceased_middle_name || (names.length > 2 ? names.slice(1, -1).join(' ') : '');
    document.getElementById('df-lname').value = c.deceased_last_name || (names.length > 1 ? names[names.length - 1] : '');
    document.getElementById('df-sex').value = c.deceased_sex || 'Male';
    document.getElementById('df-dob').value = c.date_of_birth ? c.date_of_birth.substring(0, 10) : '';
    document.getElementById('df-dod').value = c.date_of_death ? c.date_of_death.substring(0, 10) : '';
    document.getElementById('df-pod').value = c.place_of_death || '';
    document.getElementById('df-residence').value = c.residence || '';
    document.getElementById('df-civil-status').value = c.civil_status || 'Married';
    document.getElementById('df-nationality').value = c.nationality || 'Filipino';
    document.getElementById('df-religion').value = c.religion || 'Islam';

    // ──────────────── TAB 3: DEATH CERTIFICATE ────────────────
    const dcStatus = c.death_cert_status || 'Request Received';
    document.getElementById('dc-status').value = dcStatus;
    const dcBadgeHeader = document.getElementById('dc-current-stage-badge');
    dcBadgeHeader.textContent = dcStatus;
    dcBadgeHeader.className = 'badge-status ' + getBadgeClass(dcStatus);

    const copyIssueSec = document.getElementById('copy-issuance-section');
    if (copyIssueSec) {
      if (isCopy) {
        copyIssueSec.style.display = 'block';
        document.getElementById('copy-issue-ref').value = c.death_cert_certified_copy_ref || '';
        document.getElementById('copy-issue-remarks').value = c.death_cert_remarks || '';

        const issueBadge = document.getElementById('copy-issuance-status-badge');
        const receiptText = document.getElementById('copy-receipt-status-text');
        if (c.copy_issued_at) {
          issueBadge.textContent = 'Copy Issued';
          issueBadge.className = 'badge-status badge-active';
        } else {
          issueBadge.textContent = 'Pending Issuance';
          issueBadge.className = 'badge-status badge-pending';
        }

        if (parseInt(c.copy_received_confirmed) === 1) {
          receiptText.innerHTML = `<strong style="color:var(--damayan-accent);">Confirmed Received on ${formatLogTime(c.copy_received_at)}</strong>`;
        } else if (c.copy_issued_at) {
          receiptText.innerHTML = `<span style="color:#d97706; font-weight:700;">Issued on ${formatLogTime(c.copy_issued_at)} — Awaiting Physical Receipt Confirmation by Requester</span>`;
        } else {
          receiptText.textContent = 'Awaiting Record Verification & Staff Issuance';
        }
      } else {
        copyIssueSec.style.display = 'none';
      }
    }

    document.getElementById('dc-not-requested-alert').style.display = dcRequested ? 'none' : 'block';
    document.getElementById('dc-prep-date').value = c.death_cert_prep_date ? c.death_cert_prep_date.substring(0, 10) : '';
    document.getElementById('dc-certified-date').value = c.death_cert_certified_date ? c.death_cert_certified_date.substring(0, 10) : '';
    document.getElementById('dc-submitted-lcro-date').value = c.death_cert_submitted_lcro_date ? c.death_cert_submitted_lcro_date.substring(0, 10) : '';
    document.getElementById('dc-registered-date').value = c.death_cert_registered_date ? c.death_cert_registered_date.substring(0, 10) : '';
    document.getElementById('dc-registry-no').value = c.death_cert_registry_no || '';
    document.getElementById('dc-lcro-ref').value = c.death_cert_lcro_ref || '';
    document.getElementById('dc-certified-copy-ref').value = c.death_cert_certified_copy_ref || '';
    document.getElementById('dc-responsible-personnel').value = c.death_cert_responsible_personnel || '';
    document.getElementById('dc-remarks').value = c.death_cert_remarks || '';
    document.getElementById('dc-rejection-reason').value = c.death_cert_rejection_reason || '';
    document.getElementById('dc-rejection-row').style.display = (dcStatus === 'Rejected') ? 'block' : 'none';

    document.getElementById('dc-status').onchange = function() {
      document.getElementById('dc-rejection-row').style.display = (this.value === 'Rejected') ? 'block' : 'none';
    };

    // ──────────────── TAB 4: CEMETERY PLOT ────────────────
    const cpStatus = c.cemetery_plot_status || 'Pending Review';
    document.getElementById('cp-status').value = cpStatus;
    const cpBadgeHeader = document.getElementById('cp-current-status-badge');
    cpBadgeHeader.textContent = cpStatus;
    cpBadgeHeader.className = 'badge-status ' + getBadgeClass(cpStatus);

    document.getElementById('cp-not-requested-alert').style.display = plotRequested ? 'none' : 'block';
    document.getElementById('cp-family-pref').value = c.cemetery_preference || 'ISCAG Muslim Cemetery';

    // Populate Available Plots Dropdown
    const invSelect = document.getElementById('cp-inventory-select');
    invSelect.innerHTML = '<option value="">-- Choose an Available Plot from Database --</option>';
    (data.available_plots || []).forEach(p => {
      invSelect.innerHTML += `
        <option value="${p.id}" data-cemetery="${p.cemetery_name}" data-section="${p.section || ''}" data-plot="${p.plot_number}">
          ${p.cemetery_name} | Sec ${p.section || 'General'} - Plot #${p.plot_number} (${p.plot_type || 'Standard'})
        </option>
      `;
    });

    // Populate plot fields
    document.getElementById('cp-cemetery-name').value = c.assigned_cemetery_name || c.cemetery_name || c.cemetery_preference || 'ISCAG Muslim Cemetery';
    document.getElementById('cp-section').value = c.assigned_section || c.section || '';
    document.getElementById('cp-plot-number').value = c.assigned_plot_number || c.plot_number || '';
    document.getElementById('cp-assigned-date').value = c.plot_assigned_date ? c.plot_assigned_date.substring(0, 10) : '';
    document.getElementById('cp-plot-id').value = c.cemetery_plot_id || '';
    document.getElementById('cp-remarks').value = c.cemetery_plot_remarks || '';
    document.getElementById('cp-rejection-reason').value = c.cemetery_plot_rejection_reason || '';
    document.getElementById('cp-rejection-row').style.display = (cpStatus === 'Rejected') ? 'block' : 'none';

    // Assigned Highlight
    const assignHighlight = document.getElementById('cp-assigned-highlight');
    if (c.assigned_plot_number || cpStatus === 'Plot Assigned') {
      assignHighlight.style.display = 'block';
      document.getElementById('cp-assigned-display-text').textContent = 
        `${c.assigned_cemetery_name || 'ISCAG Cemetery'} - Section ${c.assigned_section || 'A'}, Plot #${c.assigned_plot_number || 'N/A'}`;
    } else {
      assignHighlight.style.display = 'none';
    }

    // ──────────────── TAB 5: ACTIVITY LOGS ────────────────
    renderActivityLogs(data.audit_logs || []);
  }

  function handlePlotStatusChange() {
    const st = document.getElementById('cp-status').value;
    document.getElementById('cp-rejection-row').style.display = (st === 'Rejected') ? 'block' : 'none';
    if (st === 'Plot Assigned' && !document.getElementById('cp-assigned-date').value) {
      document.getElementById('cp-assigned-date').value = new Date().toISOString().substring(0, 10);
    }
  }

  function onSelectInventoryPlot(selectElem) {
    const opt = selectElem.options[selectElem.selectedIndex];
    if (!opt || !opt.value) return;

    document.getElementById('cp-cemetery-name').value = opt.getAttribute('data-cemetery') || '';
    document.getElementById('cp-section').value = opt.getAttribute('data-section') || '';
    document.getElementById('cp-plot-number').value = opt.getAttribute('data-plot') || '';
    document.getElementById('cp-plot-id').value = opt.value;
    document.getElementById('cp-status').value = 'Plot Assigned';
    if (!document.getElementById('cp-assigned-date').value) {
      document.getElementById('cp-assigned-date').value = new Date().toISOString().substring(0, 10);
    }
    handlePlotStatusChange();
  }

  function renderActivityLogs(logs) {
    const container = document.getElementById('activity-logs-container');
    if(!logs || logs.length === 0) {
      container.innerHTML = '<div style="padding:24px; text-align:center; color:var(--text-muted); font-size:0.85rem;">No activity recorded for this case yet.</div>';
      return;
    }

    container.innerHTML = logs.map(l => `
      <div style="display:flex; gap:12px; font-size:0.83rem; padding-bottom:12px; border-bottom:1px solid #f0f0f0;">
        <div style="width:30px; height:30px; border-radius:50%; background:var(--damayan-light); color:var(--damayan-accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        </div>
        <div style="flex:1;">
          <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2px;">
            <strong style="color:var(--damayan-dark);">${escapeHtml(l.title || l.activity_type || l.action || 'Case Activity')}</strong>
            <span style="font-size:0.75rem; color:var(--text-muted);">${formatLogTime(l.created_at)}</span>
          </div>
          <div style="color:#4b5563;">${escapeHtml(l.details || '')}</div>
          <div style="font-size:0.72rem; color:var(--text-muted); margin-top:2px;">By: <strong>${escapeHtml(l.actor_name || 'System')}</strong> (${escapeHtml(l.actor_role || 'Staff')})</div>
        </div>
      </div>
    `).join('');
  }

  // ──────────────── SUBMISSION HANDLERS ────────────────

  async function submitDeathCertForm() {
    if(!activeCase) return;
    const payload = {
      id: activeCase.id,
      death_cert_status: document.getElementById('dc-status').value,
      death_cert_prep_date: document.getElementById('dc-prep-date').value,
      death_cert_certified_date: document.getElementById('dc-certified-date').value,
      death_cert_submitted_lcro_date: document.getElementById('dc-submitted-lcro-date').value,
      death_cert_registered_date: document.getElementById('dc-registered-date').value,
      death_cert_registry_no: document.getElementById('dc-registry-no').value.trim(),
      death_cert_lcro_ref: document.getElementById('dc-lcro-ref').value.trim(),
      death_cert_certified_copy_ref: document.getElementById('dc-certified-copy-ref').value.trim(),
      death_cert_responsible_personnel: document.getElementById('dc-responsible-personnel').value.trim(),
      death_cert_remarks: document.getElementById('dc-remarks').value.trim(),
      death_cert_rejection_reason: document.getElementById('dc-rejection-reason').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/death-cert/update') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Death Certificate Updated', 'Death certificate processing stage and details saved.', 'success');
        openCaseModal(activeCase.id, true);
      } else {
        showAlert('Error', data.error || 'Failed to update death certificate status.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error occurred.', 'error');
    }
  }

  async function submitCemeteryPlotForm() {
    if(!activeCase) return;
    const payload = {
      id: activeCase.id,
      cemetery_plot_status: document.getElementById('cp-status').value,
      cemetery_plot_id: document.getElementById('cp-plot-id').value,
      cemetery_name: document.getElementById('cp-cemetery-name').value.trim(),
      section: document.getElementById('cp-section').value.trim(),
      plot_number: document.getElementById('cp-plot-number').value.trim(),
      plot_assigned_date: document.getElementById('cp-assigned-date').value,
      cemetery_plot_remarks: document.getElementById('cp-remarks').value.trim(),
      cemetery_plot_rejection_reason: document.getElementById('cp-rejection-reason').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/cemetery-plot/update') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Plot Assigned', 'Cemetery plot assignment and availability records updated.', 'success');
        openCaseModal(activeCase.id, true);
      } else {
        showAlert('Assignment Error', data.error || 'Failed to assign cemetery plot.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error occurred.', 'error');
    }
  }

  async function submitDeceasedForm() {
    if(!activeCase) return;
    const payload = {
      id: activeCase.id,
      deceased_first_name: document.getElementById('df-fname').value.trim(),
      deceased_middle_name: document.getElementById('df-mname').value.trim(),
      deceased_last_name: document.getElementById('df-lname').value.trim(),
      deceased_sex: document.getElementById('df-sex').value,
      date_of_birth: document.getElementById('df-dob').value,
      date_of_death: document.getElementById('df-dod').value,
      place_of_death: document.getElementById('df-pod').value.trim(),
      residence: document.getElementById('df-residence').value.trim(),
      civil_status: document.getElementById('df-civil-status').value,
      nationality: document.getElementById('df-nationality').value.trim(),
      religion: document.getElementById('df-religion').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update-deceased') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Updated', 'Deceased record details successfully updated.', 'success');
        openCaseModal(activeCase.id, true);
      } else {
        showAlert('Error', data.error || 'Failed to update deceased details.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitAssignStaff() {
    if(!activeCase) return;
    const staffSelect = document.getElementById('assign-staff-select');
    const staffId = staffSelect.value;
    const staffName = staffSelect.options[staffSelect.selectedIndex]?.text.split('(')[0].trim();
    if(!staffId) {
      showAlert('Notice', 'Please choose a staff coordinator from the list.', 'warning');
      return;
    }

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/assign-staff') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: activeCase.id, staff_id: staffId, staff_name: staffName })
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Staff Assigned', 'Coordinator assigned to case.', 'success');
        openCaseModal(activeCase.id, true);
      } else {
        showAlert('Error', data.error || 'Failed to assign staff.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function triggerAcceptRequest() {
    if(!activeCase) return;
    showConfirm('Accept Request', `Accept burial case #${activeCase.ref_id} for processing? This moves the request into active coordination.`, async () => {
      try {
        const res = await fetch('<?= url('/admin/damayan/burial/accept') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: activeCase.id })
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Accepted', 'Burial request accepted for processing.', 'success');
          openCaseModal(activeCase.id, true);
        } else {
          showAlert('Error', data.error || 'Failed to accept request.', 'error');
        }
      } catch(e) {
        showAlert('Error', 'Network error.', 'error');
      }
    });
  }

  function showRequestInfoModal() {
    document.getElementById('damayanReqInfoModal').style.display = 'flex';
  }
  function closeReqInfoModal() {
    document.getElementById('damayanReqInfoModal').style.display = 'none';
  }

  async function submitRequestInfo() {
    if(!activeCase) return;
    const text = document.getElementById('reqinfo-explanation').value.trim();
    if(!text) return;
    try {
      const res = await fetch('<?= url('/admin/damayan/burial/request-info') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: activeCase.id, explanation: text })
      });
      const data = await res.json();
      if(data.success) {
        closeReqInfoModal();
        showAlert('Notice Sent', 'The family has been requested to provide additional information.', 'success');
        openCaseModal(activeCase.id, true);
      } else {
        showAlert('Error', data.error || 'Failed to send request.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  function showRejectModal() {
    document.getElementById('damayanRejectModal').style.display = 'flex';
  }
  function closeRejectModal() {
    document.getElementById('damayanRejectModal').style.display = 'none';
  }

  async function submitRejectRequest() {
    if(!activeCase) return;
    const reason = document.getElementById('reject-reason').value.trim();
    if(!reason) return;
    try {
      const res = await fetch('<?= url('/admin/damayan/burial/reject') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: activeCase.id, reason: reason })
      });
      const data = await res.json();
      if(data.success) {
        closeRejectModal();
        showAlert('Case Rejected', 'Request rejected and family notified.', 'success');
        openCaseModal(activeCase.id, true);
      } else {
        showAlert('Error', data.error || 'Failed to reject request.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  // Formatting helpers
  function getBadgeClass(status) {
    const s = (status || '').toLowerCase();
    if(s.includes('new') || s.includes('pending') || s.includes('review') || s.includes('awaiting')) return 'badge-pending';
    if(s.includes('additional') || s.includes('warning') || s.includes('info required')) return 'badge-warning';
    if(s.includes('accepted') || s.includes('available') || s.includes('certified') || s.includes('submitted') || s.includes('prepared')) return 'badge-info';
    if(s.includes('completed') || s.includes('assigned') || s.includes('registered') || s.includes('approved')) return 'badge-active';
    if(s.includes('rejected') || s.includes('unavailable')) return 'badge-danger';
    return 'badge-approved';
  }

  function formatLogTime(val) {
    if(!val) return '';
    const d = new Date(val);
    return isNaN(d) ? val : d.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
  }

  function escapeHtml(text) {
    if(!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
  }

  function showSearchDeceasedModal() {
    document.getElementById('searchDeceasedModal').style.display = 'flex';
    if(activeCase) {
      document.getElementById('deceased-search-query').value = activeCase.deceased_name || '';
      executeSearchDeceased();
    }
  }

  function closeSearchDeceasedModal() {
    document.getElementById('searchDeceasedModal').style.display = 'none';
  }

  async function executeSearchDeceased() {
    const q = document.getElementById('deceased-search-query').value.trim();
    const resultsContainer = document.getElementById('deceased-search-results');
    resultsContainer.innerHTML = '<div style="text-align:center; padding:15px; color:var(--text-muted);">Searching records...</div>';

    try {
      const res = await fetch('<?= url('/admin/damayan/deceased-records/search') ?>?q=' + encodeURIComponent(q));
      const data = await res.json();
      if(!data.success || !data.records || data.records.length === 0) {
        resultsContainer.innerHTML = `
          <div style="padding:20px; text-align:center; color:var(--text-muted); background:#f9fafb; border-radius:10px;">
            <div>No matching deceased record found.</div>
            <div style="font-size:0.78rem; margin-top:4px;">Do not create a duplicate. You may use "Request More Info" to request more information from the family.</div>
          </div>
        `;
        return;
      }

      resultsContainer.innerHTML = data.records.map(r => {
        const isEncoded = Boolean(r.is_encoded);
        return `
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 14px; background:${isEncoded ? '#f0fdf4' : '#f8fafc'}; border:1px solid ${isEncoded ? '#bbf7d0' : '#e2e8f0'}; border-radius:10px; font-size:0.85rem; margin-bottom:8px;">
          <div>
            <div style="display:flex; align-items:center; gap:8px;">
              <strong style="color:var(--damayan-dark);">${escapeHtml(r.deceased_name)}</strong>
              ${isEncoded ? '<span class="badge-status badge-active" style="font-size:0.68rem; padding:2px 8px;">Encoded Certificate Archive</span>' : '<span class="badge-status" style="font-size:0.68rem; padding:2px 8px; background:#e0e7ff; color:#3730a3;">Registered Civil Case</span>'}
            </div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
              ${r.death_cert_registry_no ? '<strong>Registration #: ' + escapeHtml(r.death_cert_registry_no) + '</strong> · ' : ''}
              ${isEncoded ? 'Representative: <strong>' + escapeHtml(r.representative_name || 'N/A') + '</strong> · Encoded by: ' + escapeHtml(r.encoded_by_name || 'Staff') : 'DOD: ' + escapeHtml(r.date_of_death || 'N/A') + ' · POD: ' + escapeHtml(r.place_of_death || 'N/A')}
              ${r.ref_id ? ' (Case #' + escapeHtml(r.ref_id) + ')' : ''}
            </div>
          </div>
          <button type="button" class="btn-topbar primary" style="padding:6px 14px; font-size:0.78rem;" onclick="linkDeceasedRecord(${r.id}, '${isEncoded ? 'encoded' : 'deceased_records'}')">
            Link to Record
          </button>
        </div>
      `;}).join('');
    } catch(e) {
      resultsContainer.innerHTML = '<div style="color:#dc2626; padding:10px; text-align:center;">Error searching records.</div>';
    }
  }

  async function linkDeceasedRecord(recordId, source = 'deceased_records') {
    if(!activeCase || !recordId) return;
    try {
      const payload = { id: activeCase.id, source: source };
      if (source === 'encoded' || source === 'encoded_death_certificates') {
        payload.encoded_cert_id = recordId;
      } else {
        payload.deceased_id = recordId;
      }
      const res = await fetch('<?= url('/admin/damayan/burial/verify-record') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        closeSearchDeceasedModal();
        showAlert('Record Linked', 'Existing certificate record successfully linked and verified. No duplicate record was created.', 'success');
        openCaseModal(activeCase.id, true);
      } else {
        showAlert('Error', data.error || 'Failed to link record.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error occurred.', 'error');
    }
  }

  async function submitIssueCopy() {
    if(!activeCase) return;
    const ref = document.getElementById('copy-issue-ref').value.trim();
    const remarks = document.getElementById('copy-issue-remarks').value.trim();

    if(!ref) {
      showAlert('Reference Required', 'Please enter a Certificate Copy Tracking / Reference number.', 'warning');
      return;
    }

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/issue-copy') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: activeCase.id, copy_ref: ref, remarks: remarks })
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Copy Issued', 'Death certificate copy has been officially issued with reference #' + ref + '. The user can now confirm receipt.', 'success');
        openCaseModal(activeCase.id, true);
      } else {
        showAlert('Error', data.error || 'Failed to issue copy.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error occurred.', 'error');
    }
  }

  // Backdrop and Escape key listeners for smooth dismissal
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const m = document.getElementById('damayanCaseModal');
      if (m && m.style.display === 'flex') closeCaseModal();
      const r = document.getElementById('damayanReqInfoModal');
      if (r && r.style.display === 'flex') closeReqInfoModal();
      const j = document.getElementById('damayanRejectModal');
      if (j && j.style.display === 'flex') closeRejectModal();
      const s = document.getElementById('searchDeceasedModal');
      if (s && s.style.display === 'flex') closeSearchDeceasedModal();
    }
  });

  document.getElementById('damayanCaseModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCaseModal();
  });
</script>
