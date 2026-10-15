<!-- ══════════════════════════════════════════════════════════════════
     DAMAYAN BURIAL CASE MANAGEMENT MODAL
     Complete Islamic Funeral Coordination & Case Lifecycle Management
══════════════════════════════════════════════════════════════════ -->
<div id="damayanCaseModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.55); backdrop-filter:blur(4px); z-index:99999; align-items:center; justify-content:center; padding:20px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:18px; width:100%; max-width:880px; max-height:92vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden; animation:modalSlideUp 0.3s ease;">
    
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
            <span id="modal-sex-badge" class="badge-status badge-info" style="font-size:0.7rem;">Male</span>
          </div>
          <div id="modal-deceased-title" style="font-size:0.85rem; color:var(--text-muted); margin-top:2px; font-weight:600;">
            Deceased: <span id="modal-deceased-name" style="color:var(--text-main); font-weight:700;">Loading...</span>
          </div>
        </div>
      </div>
      <button type="button" class="close-btn" onclick="closeCaseModal()" style="background:transparent; border:none; font-size:1.6rem; line-height:1; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>

    <!-- Modal Sub-Navigation Tabs -->
    <div style="display:flex; gap:4px; padding:8px 20px 0; background:#f4f7f5; border-bottom:1px solid var(--border); overflow-x:auto;" id="modalTabsBar">
      <button type="button" class="case-tab-btn active" onclick="switchCaseTab('overview')">1. Overview</button>
      <button type="button" class="case-tab-btn" onclick="switchCaseTab('deceased')">2. Deceased</button>
      <button type="button" class="case-tab-btn" onclick="switchCaseTab('ghusl')">3. Ghusl</button>
      <button type="button" class="case-tab-btn" onclick="switchCaseTab('kafan')">4. Kafan</button>
      <button type="button" class="case-tab-btn" onclick="switchCaseTab('janazah')">5. Janazah</button>
      <button type="button" class="case-tab-btn" onclick="switchCaseTab('transport')">6. Transport</button>
      <button type="button" class="case-tab-btn" onclick="switchCaseTab('cemetery')">7. Cemetery</button>
      <button type="button" class="case-tab-btn" onclick="switchCaseTab('complete')">8. Finalize</button>
      <button type="button" class="case-tab-btn" onclick="switchCaseTab('logs')">9. Activity Logs</button>
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
             TAB 1: OVERVIEW & REVIEW ACTIONS
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-overview" class="case-tab-pane">
          
          <!-- Alert Banner for Additional Info Request / Response if applicable -->
          <div id="info-request-alert" style="display:none; padding:14px 16px; border-radius:10px; margin-bottom:18px; background:#fffbeb; border:1px solid #fde68a;">
            <div style="display:flex; align-items:flex-start; gap:10px;">
              <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:#d97706; flex-shrink:0; margin-top:2px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              <div>
                <strong style="color:#92400e; font-size:0.85rem;">Additional Information Status:</strong>
                <div id="info-request-notes-text" style="font-size:0.82rem; color:#78350f; margin-top:3px;"></div>
                <div id="info-response-container" style="margin-top:6px; padding-top:6px; border-top:1px dashed #fcd34d; font-size:0.82rem;">
                  <span style="font-weight:700; color:#047857;">User Response:</span>
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
                Deceased Details & Timing
              </div>
              <div style="display:flex; flex-direction:column; gap:8px; font-size:0.84rem;">
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Deceased Full Name</span><strong id="v-deceased-fullname">—</strong></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Gender & Nationality</span><span id="v-deceased-gender">—</span> · <span id="v-nationality">Filipino</span></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Date & Place of Death</span><span id="v-death-date">—</span> · <span id="v-death-place">—</span></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Services Requested</span><span id="v-requested-services" style="font-weight:600; color:var(--damayan-accent);">Complete Islamic Funeral Rites</span></div>
                <div><span style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; display:block;">Cemetery Preference</span><span id="v-cemetery-pref">—</span></div>
              </div>
            </div>

          </div>

          <!-- Section: Supporting Document & Additional Notes -->
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
                <div style="font-size:0.75rem; font-weight:800; color:var(--damayan-dark); text-transform:uppercase; letter-spacing:0.04em;">Assigned Staff Coordinator</div>
                <div id="v-coordinator-name" style="font-size:0.95rem; font-weight:700; color:var(--damayan-accent); margin-top:2px;">Not Assigned</div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <select id="assign-staff-select" style="padding:7px 12px; font-size:0.82rem; border-radius:8px; border:1px solid var(--border); outline:none; background:#fff;">
                  <option value="">-- Select Staff --</option>
                  <!-- Populated dynamically -->
                </select>
                <button type="button" class="btn-topbar primary" style="padding:7px 14px; font-size:0.82rem;" onclick="submitAssignStaff()">Reassign</button>
              </div>
            </div>
          </div>

          <!-- Section: Staff Review Actions -->
          <div style="background:#fff; border:1px solid var(--border); border-radius:12px; padding:18px; margin-bottom:10px;">
            <div style="font-size:0.85rem; font-weight:800; color:var(--damayan-dark); margin-bottom:12px;">Review Actions & Status Control</div>
            <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
              <button type="button" class="btn-topbar primary" style="padding:8px 16px; font-size:0.82rem; background:var(--damayan-accent); color:#fff;" onclick="quickAcceptRequest()">
                Accept for Coordination
              </button>
              <button type="button" class="btn-topbar" style="padding:8px 16px; font-size:0.82rem; background:#fffbeb; color:#b45309; border-color:#fde68a;" onclick="toggleReqInfoBox()">
                Request Additional Info
              </button>
              <button type="button" class="btn-topbar" style="padding:8px 16px; font-size:0.82rem; background:#fef2f2; color:#b91c1c; border-color:#fecaca;" onclick="toggleRejectBox()">
                Reject Request
              </button>
              
              <div style="margin-left:auto; display:flex; align-items:center; gap:8px;">
                <span style="font-size:0.78rem; font-weight:600; color:var(--text-muted);">Workflow Status:</span>
                <select id="quick-workflow-status" style="padding:7px 10px; font-size:0.8rem; border-radius:8px; border:1px solid var(--border); background:#fff;">
                  <option value="New Request">New Request</option>
                  <option value="Under Review">Under Review</option>
                  <option value="Additional Information Required">Additional Information Required</option>
                  <option value="Accepted for Coordination">Accepted for Coordination</option>
                  <option value="Pending Volunteer Assignment">Pending Volunteer Assignment</option>
                  <option value="Volunteers Confirmed">Volunteers Confirmed</option>
                  <option value="Ghusl Scheduled">Ghusl Scheduled</option>
                  <option value="Ghusl Completed">Ghusl Completed</option>
                  <option value="Kafan Prepared">Kafan Prepared</option>
                  <option value="Janazah Scheduled">Janazah Scheduled</option>
                  <option value="Janazah Completed">Janazah Completed</option>
                  <option value="Transportation Arranged">Transportation Arranged</option>
                  <option value="Burial Scheduled">Burial Scheduled</option>
                  <option value="Burial Completed">Burial Completed</option>
                  <option value="Rejected">Rejected</option>
                </select>
                <button type="button" class="btn-topbar" style="padding:7px 12px; font-size:0.8rem;" onclick="submitDirectStatusUpdate()">Set</button>
              </div>
            </div>

            <!-- Sub-Box: Request Additional Info Form -->
            <div id="req-info-box" style="display:none; margin-top:16px; padding:16px; background:#fffbeb; border:1px solid #fcd34d; border-radius:10px;">
              <label style="display:block; font-size:0.82rem; font-weight:700; color:#92400e; margin-bottom:6px;">Specify Required Information / Documents from Applicant:</label>
              <textarea id="req-info-explanation" rows="3" style="width:100%; padding:10px; border-radius:8px; border:1px solid #fde68a; font-family:inherit; font-size:0.85rem; outline:none;" placeholder="Example: Please provide a clear copy of the Death Certificate or Certificate of Identification from the hospital..."></textarea>
              <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px;">
                <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.8rem;" onclick="toggleReqInfoBox()">Cancel</button>
                <button type="button" class="btn-topbar primary" style="padding:6px 16px; font-size:0.8rem; background:#b45309; border-color:#b45309;" onclick="submitRequestInfo()">Send Request to Family</button>
              </div>
            </div>

            <!-- Sub-Box: Reject Form -->
            <div id="reject-box" style="display:none; margin-top:16px; padding:16px; background:#fef2f2; border:1px solid #fecaca; border-radius:10px;">
              <label style="display:block; font-size:0.82rem; font-weight:700; color:#991b1b; margin-bottom:6px;">Recorded Reason for Rejection (Required):</label>
              <textarea id="reject-reason" rows="3" style="width:100%; padding:10px; border-radius:8px; border:1px solid #fecaca; font-family:inherit; font-size:0.85rem; outline:none;" placeholder="State the clear, justified reason for declining this request..."></textarea>
              <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px;">
                <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.8rem;" onclick="toggleRejectBox()">Cancel</button>
                <button type="button" class="btn-topbar" style="padding:6px 16px; font-size:0.8rem; background:#dc2626; color:#fff; border-color:#dc2626;" onclick="submitRejectRequest()">Confirm Rejection</button>
              </div>
            </div>

          </div>

        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 2: DECEASED RECORD (Manage & Synchronize)
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-deceased" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Official Deceased Person Record</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              Data is automatically populated from the user's initial application and synchronized across all Islamic funeral service modules.
            </div>
          </div>

          <form id="deceased-form" onsubmit="event.preventDefault(); submitDeceasedForm();">
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
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Gender *</label>
                <select id="df-sex" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;" onchange="onDeceasedGenderChange(this.value)">
                  <option value="Male">Male (Brother)</option>
                  <option value="Female">Female (Sister)</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Date of Birth</label>
                <input type="date" id="df-dob" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Civil Status</label>
                <select id="df-civil" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Widowed">Widowed</option>
                  <option value="Divorced">Divorced</option>
                </select>
              </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:14px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Date of Death *</label>
                <input type="date" id="df-dod" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Time of Death</label>
                <input type="time" id="df-tod" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Nationality</label>
                <input type="text" id="df-nationality" value="Filipino" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Place of Death</label>
                <input type="text" id="df-pod" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Hospital / City / Residence" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Residential Address</label>
                <input type="text" id="df-residence" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Complete street address" />
              </div>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Save Deceased Record</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 3: GHUSL COORDINATION (Ritual Washing)
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-ghusl" class="case-tab-pane" style="display:none;">
          
          <!-- Sunnah Gender Compliance Notice -->
          <div id="ghusl-sunnah-banner" style="padding:12px 16px; border-radius:10px; margin-bottom:18px; background:var(--damayan-light); border:1px solid #c7e6d4; display:flex; align-items:center; gap:10px;">
            <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:var(--damayan-accent); flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            <div style="font-size:0.84rem; color:var(--damayan-dark);">
              <strong>Islamic Sunnah Requirement:</strong> The deceased is <span id="ghusl-gender-label" style="font-weight:700;">Male</span>. Ritual washing must be performed strictly by assigned <strong><span id="ghusl-volunteer-type">Male</span></strong> volunteers.
            </div>
          </div>

          <form id="ghusl-form" onsubmit="event.preventDefault(); submitGhuslForm();">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Ghusl Status</label>
                <select id="gf-status" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="Pending">Pending</option>
                  <option value="Scheduled">Scheduled</option>
                  <option value="Volunteers Confirmed">Volunteers Confirmed</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Completed">Completed</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Facility / Venue</label>
                <input type="text" id="gf-venue" value="ISCAG Ghusl Facility" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Scheduled Date</label>
                <input type="date" id="gf-date" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Scheduled Time</label>
                <input type="time" id="gf-time" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <!-- Volunteer Assignment Section -->
            <div style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:16px;">
              <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <div style="font-size:0.8rem; font-weight:800; color:var(--damayan-dark); text-transform:uppercase;">
                  Assigned Volunteers (<span id="vols-gender-tag">Male</span> Pool)
                </div>
                <div style="font-size:0.75rem; color:var(--text-muted);">
                  Select volunteers and record confirmation
                </div>
              </div>

              <!-- Volunteers Checklist (Filtered by Gender) -->
              <div id="volunteers-checkbox-list" style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">
                <!-- Populated via renderVolunteersList() -->
              </div>

              <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:center; margin-top:10px; border-top:1px dashed var(--border); padding-top:10px;">
                <div>
                  <label style="font-size:0.75rem; font-weight:700; color:var(--text-muted); display:block; margin-bottom:4px;">VOLUNTEER AVAILABILITY CONFIRMATION</label>
                  <select id="gf-vol-status" style="width:100%; padding:7px 10px; border-radius:8px; border:1px solid var(--border); font-size:0.82rem; background:#fff;">
                    <option value="Pending">Pending Confirmation</option>
                    <option value="Confirmed">Confirmed by Volunteers</option>
                    <option value="Unavailable">Unavailable (Reassign Others)</option>
                  </select>
                </div>
                <div>
                  <label style="font-size:0.75rem; font-weight:700; color:var(--text-muted); display:block; margin-bottom:4px;">CUSTOM / ADDITIONAL VOLUNTEERS (NAMES)</label>
                  <input type="text" id="gf-custom-vols" placeholder="e.g. Brother Farooq, Brother Rashid" style="width:100%; padding:7px 10px; border-radius:8px; border:1px solid var(--border); font-size:0.82rem;" />
                </div>
              </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
              <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Ghusl Remarks / Instructions</label>
              <textarea id="gf-remarks" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Specific instructions, family attendance during preparation, etc."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Save Ghusl Coordination</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 4: KAFAN PREPARATION (Burial Shroud)
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-kafan" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Kafan (Burial Shroud) Preparation & Inventory</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              Track shroud material preparation in accordance with Sunnah standards (3 pieces for men, 5 pieces for women).
            </div>
          </div>

          <form id="kafan-form" onsubmit="event.preventDefault(); submitKafanForm();">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Shroud Specification</label>
                <select id="kf-type" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="Standard 3-piece shroud (Brother)">Standard 3-piece shroud (Male)</option>
                  <option value="5-piece shroud (Sister)">5-piece shroud (Female)</option>
                  <option value="Child / Infant shroud">Child / Infant shroud</option>
                  <option value="Custom Islamic Shroud">Custom Islamic Shroud</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Kafan Status</label>
                <select id="kf-status" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="Pending">Pending</option>
                  <option value="In Preparation">In Preparation</option>
                  <option value="Ready">Ready / Available</option>
                  <option value="Completed">Completed</option>
                </select>
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Prepared By (Staff / Volunteer)</label>
                <input type="text" id="kf-prep-by" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Staff name or volunteer" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Kafan Remarks / Camphor & Perfumes</label>
                <input type="text" id="kf-remarks" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Camphor (kafoor), sidr leaves, etc." />
              </div>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Save Kafan Preparation</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 5: SALAT AL-JANAZAH (Funeral Prayer)
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-janazah" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Salat al-Janazah (Funeral Prayer) Coordination</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              Coordinate schedule with the officiating Imam and notify the congregation and bereaved family.
            </div>
          </div>

          <form id="janazah-form" onsubmit="event.preventDefault(); submitJanazahForm();">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Janazah Status</label>
                <select id="jf-status" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="Pending">Pending</option>
                  <option value="Scheduled">Scheduled</option>
                  <option value="Completed">Completed</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Masjid / Location</label>
                <input type="text" id="jf-masjid" value="ISCAG Main Masjid" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Prayer Timing</label>
                <select id="jf-prayer-time" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="After Dhuhr">After Dhuhr (Sunnah)</option>
                  <option value="After Asr">After Asr</option>
                  <option value="After Maghrib">After Maghrib</option>
                  <option value="After Fajr">After Fajr</option>
                  <option value="Custom Time">Custom Scheduled Time</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Scheduled Date</label>
                <input type="date" id="jf-date" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Scheduled Time</label>
                <input type="time" id="jf-time" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Officiating Imam Name</label>
                <input type="text" id="jf-imam-name" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. Imam Omar Farooq" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Imam Contact Number</label>
                <input type="text" id="jf-imam-contact" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. +63 917 123 4567" />
              </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
              <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Janazah Remarks</label>
              <textarea id="jf-remarks" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Arrangements for congregation, announcements, etc."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Save Janazah Coordination</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 6: TRANSPORTATION (Ambulance / Hearse)
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-transport" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Deceased Transportation Management</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              Coordinate ISCAG ambulance or designated vehicle for pickup, transit to Ghusl facility, Masjid, and Cemetery.
            </div>
          </div>

          <form id="transport-form" onsubmit="event.preventDefault(); submitTransportForm();">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Vehicle Type</label>
                <select id="tf-vehicle" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="ISCAG Ambulance">ISCAG Ambulance</option>
                  <option value="Islamic Funeral Hearse">Islamic Funeral Hearse</option>
                  <option value="Arranged Private Vehicle">Arranged Private Vehicle</option>
                </select>
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Transportation Status</label>
                <select id="tf-status" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="Pending">Pending</option>
                  <option value="Scheduled">Scheduled</option>
                  <option value="En Route">En Route (In Transit)</option>
                  <option value="Arrived">Arrived at Destination</option>
                  <option value="Completed">Completed</option>
                </select>
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Assigned Driver / Personnel</label>
                <input type="text" id="tf-driver" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. Brother Tariq" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Driver Contact Number</label>
                <input type="text" id="tf-driver-contact" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. +63 917 555 1234" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Pickup Location</label>
                <input type="text" id="tf-pickup" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Hospital / Residence address" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Destination</label>
                <input type="text" id="tf-dest" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="ISCAG Facility / Cemetery" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Scheduled Departure</label>
                <input type="datetime-local" id="tf-sched-dt" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Actual Departure</label>
                <input type="datetime-local" id="tf-actual-dep" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Actual Arrival</label>
                <input type="datetime-local" id="tf-actual-arr" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
              <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Transportation Remarks</label>
              <textarea id="tf-remarks" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Vehicle condition, route details, escort notes"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Save Transportation Details</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 7: CEMETERY & BURIAL ARRANGEMENTS
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-cemetery" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Cemetery & Grave Coordination</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              Coordinate cemetery plot reservation, grave diggers, and burial time in accordance with Islamic Sunnah.
            </div>
          </div>

          <form id="cemetery-form" onsubmit="event.preventDefault(); submitCemeteryForm();">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Islamic Cemetery *</label>
                <input type="text" id="cf-name" value="ISCAG Designated Muslim Cemetery" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Cemetery Status</label>
                <select id="cf-status" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem; background:#fff;">
                  <option value="Pending">Pending</option>
                  <option value="Plot Reserved">Plot Reserved</option>
                  <option value="Scheduled">Scheduled</option>
                  <option value="Completed">Completed</option>
                </select>
              </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Plot / Grave Number</label>
                <input type="text" id="cf-plot" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. Block 4, Plot 18" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Scheduled Burial Date</label>
                <input type="date" id="cf-date" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Scheduled Burial Time</label>
                <input type="time" id="cf-time" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
              <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Cemetery Arrangements & Notes</label>
              <textarea id="cf-remarks" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Caretaker contact, grave preparation status, lahd (crevice) preparation..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:10px 22px; font-size:0.85rem;">Save Cemetery Coordination</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 8: COMPLETE BURIAL CASE (Explicit Confirmation)
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-complete" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Official Burial Case Completion</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              Per requirement 3.I, complete case recording occurs ONLY after the burial has taken place and all services are verified.
            </div>
          </div>

          <form id="complete-case-form" onsubmit="event.preventDefault(); submitCompleteCaseForm();">
            
            <div class="panel-section" style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:18px;">
              <div style="font-size:0.8rem; font-weight:800; color:var(--damayan-dark); text-transform:uppercase; margin-bottom:10px;">
                Islamic Funeral Services Delivered Checklist
              </div>
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.84rem;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                  <input type="checkbox" id="chk-ghusl" checked style="accent-color:var(--damayan-accent);" />
                  Ghusl (Ritual Washing according to Sunnah)
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                  <input type="checkbox" id="chk-kafan" checked style="accent-color:var(--damayan-accent);" />
                  Kafan (Burial Shroud Shrouding)
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                  <input type="checkbox" id="chk-janazah" checked style="accent-color:var(--damayan-accent);" />
                  Salat al-Janazah (Funeral Prayer in Congregation)
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                  <input type="checkbox" id="chk-transport" checked style="accent-color:var(--damayan-accent);" />
                  Transportation (Hearse / Ambulance Transit)
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                  <input type="checkbox" id="chk-burial" checked style="accent-color:var(--damayan-accent);" />
                  Interment in Designated Muslim Cemetery
                </label>
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Actual Burial Date & Time *</label>
                <input type="datetime-local" id="comp-actual-date" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
              <div class="form-group">
                <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Final Cemetery *</label>
                <input type="text" id="comp-final-cemetery" required style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
              </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
              <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Final Remarks / Notes</label>
              <textarea id="comp-final-remarks" rows="3" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="Final notes, family acknowledgment, completion documentation..."></textarea>
            </div>

            <!-- Confirmation Action Requirement 3.I -->
            <div style="padding:14px 16px; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; margin-bottom:18px;">
              <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:0.84rem; color:#065f46; font-weight:600;">
                <input type="checkbox" id="comp-confirmation-checkbox" required style="margin-top:2px; accent-color:var(--damayan-accent);" />
                <span>I confirm that the burial has taken place and all Islamic funeral rites have been completed with dignity and in accordance with Islamic Sunnah.</span>
              </label>
            </div>

            <div style="display:flex; justify-content:flex-end;">
              <button type="submit" class="btn-topbar primary" style="padding:12px 26px; font-size:0.9rem; background:#047857;">Confirm &amp; Complete Burial Case</button>
            </div>
          </form>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             TAB 9: ACTIVITY LOGS & AUDIT TRAIL
        ───────────────────────────────────────────────────────────── -->
        <div id="tab-logs" class="case-tab-pane" style="display:none;">
          <div style="margin-bottom:16px;">
            <h6 style="color:var(--damayan-dark); margin:0 0 4px; font-size:0.95rem;">Chronological Case Audit Trail</h6>
            <div style="font-size:0.78rem; color:var(--text-muted);">
              Every status change, staff action, scheduling update, and user submission is recorded with actor and timestamp.
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
        ISCAG Damayan Burial Service Management System
      </div>
      <button type="button" class="btn-topbar" style="padding:6px 18px; font-size:0.82rem;" onclick="closeCaseModal()">Close</button>
    </div>

  </div>
</div>

<style>
  .case-tab-btn {
    padding: 10px 18px;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--text-muted);
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
  }
  .case-tab-btn:hover {
    color: var(--damayan-dark);
  }
  .case-tab-btn.active {
    color: var(--damayan-accent) !important;
    border-bottom-color: var(--damayan-accent) !important;
    background: #fff;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
  }
  @keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<script>
  let activeCase = null;
  let activeGenderVolunteers = [];
  let activeAllVolunteers = [];
  let activeStaffList = [];

  function switchCaseTab(tabKey) {
    document.querySelectorAll('.case-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.case-tab-pane').forEach(p => p.style.display = 'none');
    
    // Activate target
    event.target.classList.add('active');
    const pane = document.getElementById('tab-' + tabKey);
    if(pane) pane.style.display = 'block';
  }

  async function openCaseModal(caseIdOrRef) {
    const modal = document.getElementById('damayanCaseModal');
    const loading = document.getElementById('modal-loading');
    const content = document.getElementById('modal-content-container');
    
    modal.style.display = 'flex';
    loading.style.display = 'block';
    content.style.display = 'none';

    // Reset tabs to Overview
    document.querySelectorAll('.case-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.case-tab-btn')[0].classList.add('active');
    document.querySelectorAll('.case-tab-pane').forEach(p => p.style.display = 'none');
    document.getElementById('tab-overview').style.display = 'block';

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/details') ?>?id=' + encodeURIComponent(caseIdOrRef));
      const data = await res.json();
      if(!data.success || !data.burial) {
        showAlert('Error', data.error || 'Failed to load burial case details.', 'error');
        closeCaseModal();
        return;
      }

      activeCase = data.burial;
      activeGenderVolunteers = data.gender_volunteers || [];
      activeAllVolunteers = data.all_volunteers || [];
      activeStaffList = data.staff_list || [];

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
    
    // Badges
    const statusBadge = document.getElementById('modal-status-badge');
    statusBadge.textContent = c.status;
    statusBadge.className = 'badge-status ' + getBadgeClass(c.status);

    const sexBadge = document.getElementById('modal-sex-badge');
    sexBadge.textContent = c.deceased_sex || 'Male';
    sexBadge.style.background = (c.deceased_sex === 'Female') ? '#fce7f3' : '#e0e7ff';
    sexBadge.style.color = (c.deceased_sex === 'Female') ? '#9d174d' : '#3730a3';

    // ──────────────── TAB 1: OVERVIEW ────────────────
    const applicantName = c.informant_name || ((c.user_first_name || '') + ' ' + (c.user_last_name || '')) || 'Applicant';
    document.getElementById('v-applicant-name').textContent = applicantName;
    document.getElementById('v-relationship').textContent = c.relationship || 'Family Member';
    document.getElementById('v-applicant-phone').textContent = c.informant_contact || c.user_contact || 'N/A';
    document.getElementById('v-applicant-email').textContent = c.informant_email || c.user_email || 'N/A';
    document.getElementById('v-applicant-address').textContent = c.informant_address || c.residence || 'N/A';

    document.getElementById('v-deceased-fullname').textContent = c.deceased_name;
    document.getElementById('v-deceased-gender').textContent = (c.deceased_sex === 'Female' ? 'Sister (Female)' : 'Brother (Male)');
    document.getElementById('v-nationality').textContent = c.nationality || 'Filipino';
    document.getElementById('v-death-date').textContent = c.date_of_death || 'N/A';
    document.getElementById('v-death-place').textContent = c.place_of_death || 'N/A';
    document.getElementById('v-requested-services').textContent = c.requested_services || 'Complete Islamic Funeral Rites';
    document.getElementById('v-cemetery-pref').textContent = c.cemetery_preference || c.cemetery_name || 'ISCAG Muslim Cemetery';
    document.getElementById('v-family-notes').textContent = c.additional_notes || 'None provided.';

    // Supporting document
    const docContainer = document.getElementById('v-doc-container');
    if(c.supporting_document) {
      docContainer.innerHTML = `
        <a href="<?= url('/') ?>/${c.supporting_document}" target="_blank" class="btn-topbar" style="display:inline-flex; align-items:center; gap:6px; color:var(--damayan-accent); font-weight:700; text-decoration:none;">
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
          document.getElementById('info-submitted-doc').innerHTML = `<a href="<?= url('/') ?>/${c.additional_doc_path}" target="_blank" style="color:var(--damayan-accent);font-weight:700;">View Submitted File</a>`;
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
    staffSelect.innerHTML = '<option value="">-- Select Staff --</option>' + (data.staff_list || []).map(s => `
      <option value="${s.id}" data-name="${s.name}" ${s.id == c.assigned_staff_id ? 'selected' : ''}>${s.name} (${s.email})</option>
    `).join('');

    // Quick workflow status dropdown
    document.getElementById('quick-workflow-status').value = c.status;

    // ──────────────── TAB 2: DECEASED FORM ────────────────
    document.getElementById('df-fname').value = c.dec_fname || c.deceased_first_name || '';
    document.getElementById('df-mname').value = c.dec_mname || c.deceased_middle_name || '';
    document.getElementById('df-lname').value = c.dec_lname || c.deceased_last_name || '';
    document.getElementById('df-sex').value = c.deceased_sex || 'Male';
    document.getElementById('df-dob').value = c.date_of_birth || '';
    document.getElementById('df-dod').value = c.date_of_death || '';
    document.getElementById('df-tod').value = c.time_of_death || '';
    document.getElementById('df-civil').value = c.civil_status || 'Married';
    document.getElementById('df-nationality').value = c.nationality || 'Filipino';
    document.getElementById('df-pod').value = c.place_of_death || '';
    document.getElementById('df-residence').value = c.residence || '';

    // ──────────────── TAB 3: GHUSL ────────────────
    const sex = c.deceased_sex || 'Male';
    document.getElementById('ghusl-gender-label').textContent = sex;
    document.getElementById('ghusl-volunteer-type').textContent = sex;
    document.getElementById('vols-gender-tag').textContent = sex;
    document.getElementById('gf-status').value = c.ghusl_status || 'Pending';
    document.getElementById('gf-venue').value = c.ghusl_venue || 'ISCAG Ghusl Facility';
    document.getElementById('gf-date').value = c.ghusl_date || '';
    document.getElementById('gf-time').value = c.ghusl_time || '';
    document.getElementById('gf-vol-status').value = c.ghusl_volunteer_status || 'Pending';
    document.getElementById('gf-remarks').value = c.ghusl_remarks || '';
    
    renderVolunteersList(data.gender_volunteers || [], c.ghusl_assigned_volunteers);

    // ──────────────── TAB 4: KAFAN ────────────────
    document.getElementById('kf-status').value = c.kafan_status || 'Pending';
    document.getElementById('kf-type').value = c.kafan_type || (sex === 'Female' ? '5-piece shroud (Sister)' : 'Standard 3-piece shroud (Brother)');
    document.getElementById('kf-prep-by').value = c.kafan_prepared_by || c.assigned_staff_name || '';
    document.getElementById('kf-remarks').value = c.kafan_remarks || '';

    // ──────────────── TAB 5: JANAZAH ────────────────
    document.getElementById('jf-status').value = c.janazah_status || 'Pending';
    document.getElementById('jf-masjid').value = c.janazah_masjid || 'ISCAG Main Masjid';
    document.getElementById('jf-prayer-time').value = c.janazah_prayer_time || 'After Dhuhr';
    document.getElementById('jf-date').value = c.janazah_date || '';
    document.getElementById('jf-time').value = c.janazah_time || '';
    document.getElementById('jf-imam-name').value = c.janazah_imam_name || '';
    document.getElementById('jf-imam-contact').value = c.janazah_imam_contact || '';
    document.getElementById('jf-remarks').value = c.janazah_remarks || '';

    // ──────────────── TAB 6: TRANSPORTATION ────────────────
    document.getElementById('tf-vehicle').value = c.transport_vehicle_type || 'ISCAG Ambulance';
    document.getElementById('tf-status').value = c.transport_status || 'Pending';
    document.getElementById('tf-driver').value = c.transport_driver_name || '';
    document.getElementById('tf-driver-contact').value = c.transport_driver_contact || '';
    document.getElementById('tf-pickup').value = c.transport_pickup_location || c.place_of_death || '';
    document.getElementById('tf-dest').value = c.transport_destination || c.cemetery_name || 'ISCAG Cemetery';
    document.getElementById('tf-sched-dt').value = formatDateTimeInput(c.transport_scheduled_datetime);
    document.getElementById('tf-actual-dep').value = formatDateTimeInput(c.transport_actual_departure);
    document.getElementById('tf-actual-arr').value = formatDateTimeInput(c.transport_actual_arrival);
    document.getElementById('tf-remarks').value = c.transport_remarks || '';

    // ──────────────── TAB 7: CEMETERY ────────────────
    document.getElementById('cf-name').value = c.cemetery_name || c.cemetery_preference || 'ISCAG Designated Muslim Cemetery';
    document.getElementById('cf-status').value = c.cemetery_status || 'Pending';
    document.getElementById('cf-plot').value = c.cemetery_plot_number || '';
    document.getElementById('cf-date').value = c.cemetery_scheduled_date || '';
    document.getElementById('cf-time').value = c.cemetery_scheduled_time || '';
    document.getElementById('cf-remarks').value = c.cemetery_remarks || '';

    // ──────────────── TAB 8: COMPLETE CASE ────────────────
    document.getElementById('comp-final-cemetery').value = c.final_cemetery || c.cemetery_name || 'ISCAG Designated Muslim Cemetery';
    document.getElementById('comp-actual-date').value = formatDateTimeInput(c.actual_burial_date) || formatDateTimeInput(new Date().toISOString());
    document.getElementById('comp-final-remarks').value = c.final_remarks || 'Burial rites completed in accordance with Islamic Sunnah.';
    document.getElementById('comp-confirmation-checkbox').checked = false;

    // ──────────────── TAB 9: LOGS ────────────────
    renderActivityLogs(c.activity_logs || []);
  }

  function renderVolunteersList(vols, assignedStr) {
    const list = document.getElementById('volunteers-checkbox-list');
    const assigned = (assignedStr || '').split(',').map(s => s.trim().toLowerCase());
    
    if(!vols || vols.length === 0) {
      list.innerHTML = '<div style="color:var(--text-muted); font-size:0.82rem; grid-column:span 2;">No registered volunteers found for this gender pool.</div>';
      return;
    }

    list.innerHTML = vols.map(v => {
      const isChecked = assigned.includes(v.name.toLowerCase());
      const availClass = (v.availability_status === 'Available') ? '#ecfdf5;color:#047857' : (v.availability_status === 'Busy' ? '#fffbeb;color:#b45309' : '#fef2f2;color:#b91c1c');
      return `
        <label style="display:flex; align-items:center; justify-content:space-between; background:#fff; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.82rem; cursor:pointer;">
          <div style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" class="ghusl-vol-cb" value="${v.name}" ${isChecked ? 'checked' : ''} style="accent-color:var(--damayan-accent);" />
            <div>
              <strong>${v.name}</strong>
              <div style="font-size:0.72rem; color:var(--text-muted);">${v.phone}</div>
            </div>
          </div>
          <span style="font-size:0.68rem; font-weight:700; padding:2px 8px; border-radius:10px; background:${availClass};">${v.availability_status}</span>
        </label>
      `;
    }).join('');
  }

  function renderActivityLogs(logs) {
    const container = document.getElementById('activity-logs-container');
    if(!logs || logs.length === 0) {
      container.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);">No activity logs recorded yet.</div>';
      return;
    }

    container.innerHTML = logs.map(l => `
      <div style="display:flex; gap:14px; background:#fafbfc; border:1px solid var(--border); border-radius:10px; padding:12px 16px;">
        <div style="width:10px; height:10px; border-radius:50%; background:var(--damayan-accent); margin-top:5px; flex-shrink:0;"></div>
        <div style="flex:1;">
          <div style="display:flex; align-items:center; justify-content:space-between;">
            <strong style="font-size:0.85rem; color:var(--damayan-dark);">${l.title}</strong>
            <span style="font-size:0.75rem; color:var(--text-muted);">${formatLogTime(l.created_at)}</span>
          </div>
          <div style="font-size:0.82rem; color:#4b5563; margin-top:3px;">${l.details || ''}</div>
          <div style="font-size:0.72rem; color:var(--text-muted); margin-top:4px;">
            Actor: <strong>${l.actor_name || 'Staff'}</strong> (${l.actor_role || 'Staff_Damayan'}) · Type: ${l.activity_type}
          </div>
        </div>
      </div>
    `).join('');
  }

  function onDeceasedGenderChange(newGender) {
    document.getElementById('modal-sex-badge').textContent = newGender;
    document.getElementById('modal-sex-badge').style.background = (newGender === 'Female') ? '#fce7f3' : '#e0e7ff';
    document.getElementById('modal-sex-badge').style.color = (newGender === 'Female') ? '#9d174d' : '#3730a3';
    
    // Auto-update Kafan shroud suggestion
    document.getElementById('kf-type').value = (newGender === 'Female') ? '5-piece shroud (Sister)' : 'Standard 3-piece shroud (Brother)';
    
    // Re-fetch gender-specific volunteers
    fetch('<?= url('/admin/damayan/burial/volunteers') ?>?gender=' + encodeURIComponent(newGender))
      .then(r => r.json())
      .then(d => {
        if(d.success) {
          activeGenderVolunteers = d.volunteers;
          document.getElementById('ghusl-gender-label').textContent = newGender;
          document.getElementById('ghusl-volunteer-type').textContent = newGender;
          document.getElementById('vols-gender-tag').textContent = newGender;
          renderVolunteersList(d.volunteers, activeCase ? activeCase.ghusl_assigned_volunteers : '');
        }
      });
  }

  // ──────────────── SUBMISSION HANDLERS ────────────────
  async function submitAssignStaff() {
    if(!activeCase) return;
    const select = document.getElementById('assign-staff-select');
    const staffId = select.value;
    const staffName = select.options[select.selectedIndex]?.getAttribute('data-name') || '';

    if(!staffId) {
      showAlert('Selection Required', 'Please select a staff member to assign.', 'warning');
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
        showAlert('Assigned', `Case successfully assigned to ${staffName}.`, 'success');
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to assign staff.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network or server error.', 'error');
    }
  }

  async function quickAcceptRequest() {
    if(!activeCase) return;
    showConfirm('Accept Request', `Accept burial request #${activeCase.ref_id} for Islamic funeral coordination?`, async () => {
      try {
        const res = await fetch('<?= url('/admin/damayan/burial/accept') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: activeCase.id })
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Accepted', 'Request accepted for coordination. Family has been notified.', 'success');
          openCaseModal(activeCase.id);
        } else {
          showAlert('Error', data.error || 'Failed to accept request.', 'error');
        }
      } catch(e) {
        showAlert('Error', 'Network error.', 'error');
      }
    });
  }

  function toggleReqInfoBox() {
    const box = document.getElementById('req-info-box');
    box.style.display = (box.style.display === 'none' || !box.style.display) ? 'block' : 'none';
    document.getElementById('reject-box').style.display = 'none';
  }

  async function submitRequestInfo() {
    if(!activeCase) return;
    const text = document.getElementById('req-info-explanation').value.trim();
    if(!text) {
      showAlert('Required', 'Please explain what additional information or documents are needed.', 'warning');
      return;
    }

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/request-info') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: activeCase.id, explanation: text })
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Request Sent', 'The applicant has been notified of the requested requirements.', 'success');
        toggleReqInfoBox();
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to send request.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  function toggleRejectBox() {
    const box = document.getElementById('reject-box');
    box.style.display = (box.style.display === 'none' || !box.style.display) ? 'block' : 'none';
    document.getElementById('req-info-box').style.display = 'none';
  }

  async function submitRejectRequest() {
    if(!activeCase) return;
    const reason = document.getElementById('reject-reason').value.trim();
    if(!reason) {
      showAlert('Reason Required', 'Please provide a clear justification for rejecting the request.', 'warning');
      return;
    }

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/reject') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: activeCase.id, reason: reason })
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Request Declined', 'Request status set to Rejected and recorded in the audit trail.', 'info');
        toggleRejectBox();
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to decline request.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitDirectStatusUpdate() {
    if(!activeCase) return;
    const st = document.getElementById('quick-workflow-status').value;
    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: activeCase.id, status: st })
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Updated', `Workflow status updated to "${st}".`, 'success');
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', 'Failed to update workflow status.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitDeceasedForm() {
    if(!activeCase) return;
    const payload = {
      id: activeCase.id,
      first_name: document.getElementById('df-fname').value.trim(),
      middle_name: document.getElementById('df-mname').value.trim(),
      last_name: document.getElementById('df-lname').value.trim(),
      sex: document.getElementById('df-sex').value,
      date_of_birth: document.getElementById('df-dob').value,
      date_of_death: document.getElementById('df-dod').value,
      time_of_death: document.getElementById('df-tod').value,
      civil_status: document.getElementById('df-civil').value,
      nationality: document.getElementById('df-nationality').value,
      place_of_death: document.getElementById('df-pod').value.trim(),
      residence_address: document.getElementById('df-residence').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update-deceased') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Saved', 'Deceased record synchronized across all modules.', 'success');
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to update deceased record.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitGhuslForm() {
    if(!activeCase) return;
    
    // Collect checked volunteers + custom string
    const checkedVols = Array.from(document.querySelectorAll('.ghusl-vol-cb:checked')).map(cb => cb.value);
    const customVols = document.getElementById('gf-custom-vols').value.trim();
    if(customVols) checkedVols.push(customVols);

    const payload = {
      id: activeCase.id,
      ghusl_status: document.getElementById('gf-status').value,
      ghusl_venue: document.getElementById('gf-venue').value.trim(),
      ghusl_date: document.getElementById('gf-date').value,
      ghusl_time: document.getElementById('gf-time').value,
      ghusl_assigned_volunteers: checkedVols.join(', '),
      ghusl_volunteer_status: document.getElementById('gf-vol-status').value,
      ghusl_remarks: document.getElementById('gf-remarks').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update-ghusl') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Ghusl Updated', 'Ghusl ritual coordination saved and status updated.', 'success');
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to update Ghusl.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitKafanForm() {
    if(!activeCase) return;
    const payload = {
      id: activeCase.id,
      kafan_status: document.getElementById('kf-status').value,
      kafan_type: document.getElementById('kf-type').value,
      kafan_prepared_by: document.getElementById('kf-prep-by').value.trim(),
      kafan_remarks: document.getElementById('kf-remarks').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update-kafan') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Kafan Updated', 'Kafan shroud preparation recorded.', 'success');
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to update Kafan.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitJanazahForm() {
    if(!activeCase) return;
    const payload = {
      id: activeCase.id,
      janazah_status: document.getElementById('jf-status').value,
      janazah_masjid: document.getElementById('jf-masjid').value.trim(),
      janazah_prayer_time: document.getElementById('jf-prayer-time').value,
      janazah_date: document.getElementById('jf-date').value,
      janazah_time: document.getElementById('jf-time').value,
      janazah_imam_name: document.getElementById('jf-imam-name').value.trim(),
      janazah_imam_contact: document.getElementById('jf-imam-contact').value.trim(),
      janazah_remarks: document.getElementById('jf-remarks').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update-janazah') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Janazah Updated', 'Salat al-Janazah schedule saved and applicant notified.', 'success');
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to update Janazah.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitTransportForm() {
    if(!activeCase) return;
    const payload = {
      id: activeCase.id,
      transport_vehicle_type: document.getElementById('tf-vehicle').value,
      transport_status: document.getElementById('tf-status').value,
      transport_driver_name: document.getElementById('tf-driver').value.trim(),
      transport_driver_contact: document.getElementById('tf-driver-contact').value.trim(),
      transport_pickup_location: document.getElementById('tf-pickup').value.trim(),
      transport_destination: document.getElementById('tf-dest').value.trim(),
      transport_scheduled_datetime: document.getElementById('tf-sched-dt').value,
      transport_actual_departure: document.getElementById('tf-actual-dep').value,
      transport_actual_arrival: document.getElementById('tf-actual-arr').value,
      transport_remarks: document.getElementById('tf-remarks').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update-transport') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Transport Updated', 'Transportation arrangements saved and tracking updated.', 'success');
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to update transport.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitCemeteryForm() {
    if(!activeCase) return;
    const payload = {
      id: activeCase.id,
      cemetery_name: document.getElementById('cf-name').value.trim(),
      cemetery_status: document.getElementById('cf-status').value,
      cemetery_plot_number: document.getElementById('cf-plot').value.trim(),
      cemetery_scheduled_date: document.getElementById('cf-date').value,
      cemetery_scheduled_time: document.getElementById('cf-time').value,
      cemetery_remarks: document.getElementById('cf-remarks').value.trim()
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update-cemetery') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(data.success) {
        showAlert('Cemetery Updated', 'Cemetery and burial arrangements saved.', 'success');
        openCaseModal(activeCase.id);
      } else {
        showAlert('Error', data.error || 'Failed to update cemetery.', 'error');
      }
    } catch(e) {
      showAlert('Error', 'Network error.', 'error');
    }
  }

  async function submitCompleteCaseForm() {
    if(!activeCase) return;
    const chk = document.getElementById('comp-confirmation-checkbox');
    if(!chk.checked) {
      showAlert('Confirmation Required', 'Please confirm that all Islamic funeral rites and burial have been completed.', 'warning');
      return;
    }

    const payload = {
      id: activeCase.id,
      actual_burial_date: document.getElementById('comp-actual-date').value,
      final_cemetery: document.getElementById('comp-final-cemetery').value.trim(),
      final_remarks: document.getElementById('comp-final-remarks').value.trim()
    };

    showConfirm('Complete Case', `Are you certain you wish to mark burial case #${activeCase.ref_id} as officially COMPLETED? All services will be recorded as completed and archived.`, async () => {
      try {
        const res = await fetch('<?= url('/admin/damayan/burial/complete') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Case Completed', 'Burial case officially completed. Notification sent to family.', 'success');
          openCaseModal(activeCase.id);
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert('Error', data.error || 'Failed to complete burial case.', 'error');
        }
      } catch(e) {
        showAlert('Error', 'Network error.', 'error');
      }
    });
  }

  // Helper formatting functions
  function getBadgeClass(status) {
    const s = (status || '').toLowerCase();
    if(s.includes('new') || s.includes('pending') || s.includes('review')) return 'badge-pending';
    if(s.includes('additional') || s.includes('warning') || s.includes('info required')) return 'badge-warning';
    if(s.includes('accepted') || s.includes('scheduled') || s.includes('arranged') || s.includes('confirmed') || s.includes('prepared')) return 'badge-info';
    if(s.includes('completed')) return 'badge-active';
    if(s.includes('rejected')) return 'badge-danger';
    return 'badge-approved';
  }

  function formatDateTimeInput(val) {
    if(!val) return '';
    const d = new Date(val);
    if(isNaN(d)) return '';
    const pad = n => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
  }

  function formatLogTime(val) {
    if(!val) return '';
    const d = new Date(val);
    return isNaN(d) ? val : d.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
  }
</script>
