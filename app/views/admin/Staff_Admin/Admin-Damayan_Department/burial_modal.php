<!-- ══════════════════════════════════════════════════════════════════
     DAMAYAN SIMPLE DEATH CERTIFICATE VERIFICATION MODAL
     Focused verification against the physical death certificate
══════════════════════════════════════════════════════════════════ -->

<!-- 1. COMPACT VERIFICATION MODAL -->
<div id="damayanCaseModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:16px; width:98%; max-width:760px; max-height:92vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideUp 0.25s ease;">
    
    <!-- Modal Header -->
    <div class="mis-modal-header" style="padding:16px 22px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:38px; height:38px; border-radius:10px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <svg viewBox="0 0 24 24" style="width:22px; height:22px; fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        </div>
        <div>
          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <h5 id="modal-ref-id" style="margin:0; font-size:1.15rem; font-weight:800; color:#064e3b;">#BR-0000</h5>
            <span id="modal-status-badge" class="badge-status badge-pending">Pending Review</span>
            <span id="modal-reqtype-badge" class="badge-status" style="font-size:0.72rem; background:#d1fae5; color:#065f46;">Register Death Certificate</span>
            <span id="modal-verification-status-badge" class="badge-status" style="font-size:0.72rem; background:#f3f4f6; color:#4b5563;">Unverified</span>
          </div>
          <div style="font-size:0.8rem; color:#6b7280; margin-top:2px;">
            Death Certificate Verification
          </div>
        </div>
      </div>
      <button type="button" class="close-btn" onclick="closeCaseModal()" style="background:transparent; border:none; font-size:1.6rem; line-height:1; cursor:pointer; color:#9ca3af; padding:4px 8px;" title="Close Modal">&times;</button>
    </div>

    <!-- Modal Body (Scrollable) -->
    <div class="mis-modal-body" style="padding:20px; overflow-y:auto; flex:1; background:#f8fafc;">
      
      <!-- Loading State -->
      <div id="modal-loading" style="text-align:center; padding:60px 20px; color:#6b7280;">
        <div style="font-size:1rem; font-weight:700; color:#065f46;">Loading request information...</div>
        <div style="font-size:0.82rem; margin-top:4px;">Retrieving request details and civil registry information.</div>
      </div>

      <!-- Main Verification Content (Side-by-Side Grid) -->
      <div id="modal-content-container" style="display:none;">
        
        <!-- Potential Duplicate Warning Card (Shown if duplicate records found) -->
        <div id="duplicate-warning-container" style="display:none; background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px; margin-bottom:18px;">
          <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
            <div style="display:flex; align-items:flex-start; gap:10px;">
              <svg viewBox="0 0 24 24" style="width:22px; height:22px; fill:#d97706; flex-shrink:0; margin-top:2px;"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
              <div>
                <strong style="color:#92400e; font-size:0.92rem; display:block;">Potential Duplicate Match Detected</strong>
                <div style="font-size:0.82rem; color:#78350f; margin-top:2px;">
                  The deceased name or certificate registration number matches existing records in the database. Please review the identifying details below before taking action.
                </div>
              </div>
            </div>
            <div id="duplicate-review-status" style="font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:6px; background:#fef3c7; color:#92400e;">
              Pending Review
            </div>
          </div>

          <!-- Matched Duplicate Records List -->
          <div id="duplicate-records-list" style="margin-top:12px; display:flex; flex-direction:column; gap:8px;"></div>

          <!-- Staff Duplicate Review Action -->
          <div style="margin-top:14px; padding-top:12px; border-top:1px dashed #fcd34d; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:14px; font-size:0.84rem;">
              <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-weight:700; color:#065f46;">
                <input type="radio" name="dup_decision" value="Confirmed Different Individual" id="dup-radio-diff" onchange="onDuplicateRadioChange()" />
                Different Person (Same Name)
              </label>
              <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-weight:700; color:#b91c1c;">
                <input type="radio" name="dup_decision" value="Duplicate Request" id="dup-radio-dup" onchange="onDuplicateRadioChange()" />
                Genuinely Duplicate Request
              </label>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <input type="text" id="dup-review-notes" placeholder="Optional staff review notes..." style="padding:6px 12px; border:1px solid #d1d5db; border-radius:6px; font-size:0.8rem; width:220px;" />
              <button type="button" class="btn-topbar primary" style="padding:6px 14px; font-size:0.78rem; background:#059669; border-color:#059669;" onclick="saveDuplicateReviewDecision()">
                Save Decision
              </button>
            </div>
          </div>
        </div>

        <!-- VERIFICATION CONTENT -->
        <div style="display:flex; flex-direction:column; gap:16px;">
            
            <!-- SECTION A: REQUEST INFORMATION -->
            <div class="panel-section" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
              <div style="font-size:0.75rem; font-weight:800; color:#065f46; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px; display:flex; align-items:center; gap:6px;">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:#059669;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                A. Request Information
              </div>
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:0.84rem;">
                <div>
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Reference Number</span>
                  <strong id="v-ref-id" style="color:#111827; font-size:0.9rem;">—</strong>
                </div>
                <div>
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Request Type</span>
                  <span id="v-request-type" style="font-weight:700; color:#065f46;">—</span>
                </div>
                <div>
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Date Submitted</span>
                  <span id="v-date-submitted" style="color:#374151;">—</span>
                </div>
                <div>
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Current Request Status</span>
                  <span id="v-current-status" style="font-weight:700;">—</span>
                </div>
              </div>
            </div>

            <!-- SECTION B: REQUESTER INFORMATION -->
            <div class="panel-section" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
              <div style="font-size:0.75rem; font-weight:800; color:#065f46; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px; display:flex; align-items:center; gap:6px;">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:#059669;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                B. Requester Information
              </div>
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:0.84rem;">
                <div style="grid-column: span 2;">
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Requester / Representative Name</span>
                  <strong id="v-requester-name" style="color:#111827; font-size:0.92rem;">—</strong>
                </div>
                <div>
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Relationship to Deceased</span>
                  <span id="v-relationship" style="font-weight:600; color:#374151;">—</span>
                </div>
                <div>
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Contact Number</span>
                  <span id="v-contact-number" style="font-weight:600; color:#374151;">—</span>
                </div>
              </div>
            </div>

            <!-- SECTION C: DEATH CERTIFICATE INFORMATION -->
            <div class="panel-section" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
              <div style="font-size:0.75rem; font-weight:800; color:#065f46; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px; display:flex; align-items:center; gap:6px;">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:#059669;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                C. Recorded Death Certificate Information
              </div>
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:0.84rem;">
                <div style="grid-column: span 2; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px;">
                  <span style="color:#166534; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:800;">Certificate Registration Number</span>
                  <strong id="v-cert-reg-no" style="font-size:1.05rem; color:#065f46; letter-spacing:0.03em;">—</strong>
                </div>
                <div style="grid-column: span 2;">
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Full Name of Deceased</span>
                  <strong id="v-deceased-name" style="color:#111827; font-size:1rem;">—</strong>
                </div>
                <div>
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Sex / Gender</span>
                  <span id="v-deceased-sex" style="font-weight:600; color:#374151;">—</span>
                </div>
                <div>
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Date of Death</span>
                  <span id="v-death-date" style="font-weight:600; color:#374151;">—</span>
                </div>
                <div style="grid-column: span 2;">
                  <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; display:block; font-weight:700;">Place of Death</span>
                  <span id="v-death-place" style="color:#374151;">—</span>
                </div>
              </div>
            </div>

            <!-- SECTION 3: VERIFICATION PROCESS (RESULT & REMARKS) -->
            <div class="panel-section" style="background:#fff; border:2px solid #a7f3d0; border-radius:12px; padding:16px;">
              <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <div style="font-size:0.8rem; font-weight:800; color:#065f46; text-transform:uppercase; letter-spacing:0.04em;">
                  Verification Decision
                </div>
                <div id="v-result-summary-badge" class="badge-status badge-pending" style="font-size:0.72rem;">
                  Pending Verification
                </div>
              </div>
              <div style="font-size:0.8rem; color:#4b5563; margin-bottom:12px;">
                Select the verification result based on recorded civil registry data:
              </div>

              <!-- 3 Result Radio Buttons -->
              <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:8px; margin-bottom:12px;">
                
                <label id="btn-result-matched" class="verify-choice-btn" style="border:2px solid #d1d5db; border-radius:8px; padding:10px 8px; text-align:center; cursor:pointer; background:#fff; transition:all 0.15s ease;" onclick="selectVerificationResult('Matched')">
                  <input type="radio" name="verify_result_choice" value="Matched" id="radio-matched" style="display:none;" />
                  <div style="color:#059669; line-height:1; margin-bottom:4px; display:flex; justify-content:center;"><svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg></div>
                  <strong style="display:block; font-size:0.82rem; color:#065f46;">Matched</strong>
                  <span style="font-size:0.68rem; color:#6b7280; display:block; margin-top:2px;">Details match certificate</span>
                </label>

                <label id="btn-result-mismatch" class="verify-choice-btn" style="border:2px solid #d1d5db; border-radius:8px; padding:10px 8px; text-align:center; cursor:pointer; background:#fff; transition:all 0.15s ease;" onclick="selectVerificationResult('Mismatch')">
                  <input type="radio" name="verify_result_choice" value="Mismatch" id="radio-mismatch" style="display:none;" />
                  <div style="color:#d97706; line-height:1; margin-bottom:4px; display:flex; justify-content:center;"><svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></div>
                  <strong style="display:block; font-size:0.82rem; color:#b45309;">Mismatch</strong>
                  <span style="font-size:0.68rem; color:#6b7280; display:block; margin-top:2px;">Details do not match</span>
                </label>

                <label id="btn-result-unable" class="verify-choice-btn" style="border:2px solid #d1d5db; border-radius:8px; padding:10px 8px; text-align:center; cursor:pointer; background:#fff; transition:all 0.15s ease;" onclick="selectVerificationResult('Unable to Verify')">
                  <input type="radio" name="verify_result_choice" value="Unable to Verify" id="radio-unable" style="display:none;" />
                  <div style="color:#6b7280; line-height:1; margin-bottom:4px; display:flex; justify-content:center;"><svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 16h-2v-2h2v2zm1.07-7.75l-.9.92C12.45 11.9 12 12.5 12 14h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg></div>
                  <strong style="display:block; font-size:0.82rem; color:#4b5563;">Unable to Verify</strong>
                  <span style="font-size:0.68rem; color:#6b7280; display:block; margin-top:2px;">Unreadable / Missing</span>
                </label>

              </div>

              <!-- Remarks Input Field -->
              <div style="margin-bottom:12px;">
                <label for="v-remarks-input" style="display:block; font-size:0.75rem; font-weight:700; color:#374151; margin-bottom:4px;">
                  Verification Remarks <span id="v-remarks-req-star" style="color:#dc2626; display:none;">*</span>
                </label>
                <textarea id="v-remarks-input" rows="2" placeholder="Enter findings, notes, or discrepancies..." style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:0.82rem; font-family:inherit; resize:vertical;"></textarea>
              </div>

              <!-- Save Verification Result Button -->
              <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                <div id="v-verified-meta" style="font-size:0.72rem; color:#6b7280;"></div>
                <button type="button" id="btn-save-verification" class="btn-topbar primary" style="padding:6px 16px; font-size:0.8rem; background:#059669; border-color:#059669;" onclick="saveVerificationResult()">
                  Save Verification Result
                </button>
              </div>

            </div>

            <!-- SECTION D: WORKFLOW STAGE & RELEASE SCHEDULING (DAMAYAN ADMIN) -->
            <div class="panel-section" id="section-release-scheduling" style="background:#fff; border:2px solid #6ee7b7; border-radius:12px; padding:18px;">
              <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:8px;">
                  <div style="width:30px; height:30px; border-radius:8px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center;">
                    <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                  </div>
                  <div>
                    <div style="font-size:0.84rem; font-weight:800; color:#064e3b; text-transform:uppercase; letter-spacing:0.04em;">
                      D. Processing Stage &amp; Release Scheduling
                    </div>
                    <div style="font-size:0.75rem; color:#6b7280; margin-top:1px;">
                      Advance stage and select the exact date and time when user can claim death certificate
                    </div>
                  </div>
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                  <span style="font-size:0.75rem; color:#6b7280; font-weight:600;">Current Stage:</span>
                  <span id="v-current-stage-badge" class="badge-status badge-active" style="font-size:0.75rem; padding:3px 10px;">Submitted</span>
                </div>
              </div>

              <!-- Pipeline Progression Pills -->
              <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; margin-bottom:16px;">
                <div style="font-size:0.72rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:10px;">
                  Workflow Pipeline:
                </div>
                <div style="display:grid; grid-template-columns:repeat(6, 1fr); gap:6px; text-align:center;" id="admin-pipeline-steps">
                  <div class="pipe-step" id="pstep-1" style="padding:6px 4px; border-radius:6px; font-size:0.7rem; font-weight:700; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0;">1. Submitted</div>
                  <div class="pipe-step" id="pstep-2" style="padding:6px 4px; border-radius:6px; font-size:0.7rem; font-weight:700; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0;">2. Under Review</div>
                  <div class="pipe-step" id="pstep-3" style="padding:6px 4px; border-radius:6px; font-size:0.7rem; font-weight:700; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0;">3. Approved</div>
                  <div class="pipe-step" id="pstep-4" style="padding:6px 4px; border-radius:6px; font-size:0.7rem; font-weight:700; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0;">4. Certificate Prepared</div>
                  <div class="pipe-step" id="pstep-5" style="padding:6px 4px; border-radius:6px; font-size:0.7rem; font-weight:700; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0;">5. Ready for Release</div>
                  <div class="pipe-step" id="pstep-6" style="padding:6px 4px; border-radius:6px; font-size:0.7rem; font-weight:700; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0;">6. Completed</div>
                </div>

                <div style="margin-top:12px; padding-top:10px; border-top:1px dashed #cbd5e1; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                  <div style="font-size:0.78rem; color:#475569;">
                    Update request processing stage:
                  </div>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <select id="stage-select-dropdown" style="padding:6px 10px; font-size:0.8rem; border:1px solid #cbd5e1; border-radius:6px; background:#fff; font-family:inherit;">
                      <option value="Submitted">1. Submitted</option>
                      <option value="Under Review">2. Under Review</option>
                      <option value="Approved">3. Approved</option>
                      <option value="Certificate Prepared">4. Certificate Prepared</option>
                      <option value="Ready for Release">5. Ready for Release</option>
                      <option value="Completed">6. Completed</option>
                    </select>
                    <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.78rem; background:#059669; color:#fff; border-color:#059669; font-weight:700;" onclick="submitStageUpdate()">
                      Update Stage
                    </button>
                  </div>
                </div>
              </div>

              <!-- VIEW A: CONFIRMED CLAIMING APPOINTMENT (When schedule is active) -->
              <div id="confirmed-schedule-view" style="display:none; background:#ecfdf5; border:1.5px solid #a7f3d0; border-radius:10px; padding:16px; margin-bottom:14px;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                  <div style="display:flex; align-items:flex-start; gap:10px;">
                    <div style="width:34px; height:34px; border-radius:8px; background:#d1fae5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                      <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                    </div>
                    <div>
                      <strong style="color:#065f46; font-size:0.94rem; display:block;">Confirmed Release Appointment</strong>
                      <div style="font-size:0.78rem; color:#047857; margin-top:2px;">
                        The applicant can visit the Damayan Department on the confirmed schedule below.
                      </div>
                    </div>
                  </div>
                  <span class="badge-status badge-active" style="font-size:0.72rem; padding:3px 10px; background:#059669; color:#fff;">
                    Ready for Release
                  </span>
                </div>

                <!-- Schedule Details Grid -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:14px; font-size:0.84rem; background:#fff; border:1px solid #bbf7d0; border-radius:8px; padding:12px 14px;">
                  <div>
                    <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; font-weight:700; display:block;">Scheduled Date:</span>
                    <strong id="display-sched-date" style="color:#0f172a; font-size:0.95rem;">—</strong>
                  </div>
                  <div>
                    <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; font-weight:700; display:block;">Scheduled Time:</span>
                    <strong id="display-sched-time" style="color:#0f172a; font-size:0.95rem;">—</strong>
                  </div>
                  <div>
                    <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; font-weight:700; display:block;">Claiming Location:</span>
                    <strong id="display-sched-loc" style="color:#065f46; font-size:0.9rem;">Damayan Department</strong>
                  </div>
                  <div>
                    <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; font-weight:700; display:block;">Scheduled By:</span>
                    <span id="display-sched-by" style="color:#374151; font-weight:600;">—</span>
                  </div>
                  <div style="grid-column: span 2;" id="display-sched-instructions-row">
                    <span style="color:#6b7280; font-size:0.72rem; text-transform:uppercase; font-weight:700; display:block;">Release Instructions:</span>
                    <div id="display-sched-instructions" style="color:#374151; margin-top:3px; font-size:0.84rem; background:#f8fafc; padding:8px 12px; border-radius:6px; border:1px solid #e2e8f0;">None provided</div>
                  </div>
                </div>

                <!-- Schedule Controls -->
                <div style="margin-top:14px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                  <div style="display:flex; align-items:center; gap:8px;">
                    <button type="button" class="btn-topbar" style="padding:6px 12px; font-size:0.78rem; background:#fff; color:#b45309; border:1px solid #fcd34d; font-weight:700;" onclick="toggleRescheduleForm()">
                      Reschedule Appointment
                    </button>
                    <button type="button" class="btn-topbar" style="padding:6px 12px; font-size:0.78rem; background:#fff; color:#dc2626; border:1px solid #fca5a5; font-weight:700;" onclick="openCancelScheduleModal()">
                      Cancel Schedule
                    </button>
                  </div>
                  <button type="button" class="btn-topbar primary" style="padding:6px 16px; font-size:0.8rem; background:#059669; border-color:#059669; color:#fff; font-weight:700; display:inline-flex; align-items:center; gap:5px;" onclick="openMarkReleasedModal()">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Record Certificate Released
                  </button>
                </div>
              </div>

              <!-- VIEW B: SCHEDULE / RESCHEDULE ENTRY FORM -->
              <div id="schedule-entry-form" style="background:#fff; border:1.5px solid #cbd5e1; border-radius:10px; padding:16px; margin-bottom:14px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                  <div>
                    <strong id="sched-form-title" style="color:#0f5c3a; font-size:0.9rem; display:block;">Schedule Certificate Release</strong>
                    <span style="font-size:0.72rem; color:#6b7280;">Specify exact claiming date and time. Must be in the future.</span>
                  </div>
                  <span id="sched-mode-badge" style="display:none; font-size:0.72rem; font-weight:700; padding:2px 8px; border-radius:4px; background:#fef3c7; color:#92400e;">Rescheduling</span>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                  <div>
                    <label for="sched-date-input" style="display:block; font-size:0.76rem; font-weight:700; color:#374151; margin-bottom:4px;">
                      Release Date <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="date" id="sched-date-input" style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:7px 10px; font-size:0.84rem; font-family:inherit; background:#fff;" min="<?= date('Y-m-d') ?>" />
                  </div>
                  <div>
                    <label for="sched-time-input" style="display:block; font-size:0.76rem; font-weight:700; color:#374151; margin-bottom:4px;">
                      Release Time <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="time" id="sched-time-input" style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:7px 10px; font-size:0.84rem; font-family:inherit; background:#fff;" />
                  </div>
                  <div style="grid-column: span 2;">
                    <label for="sched-location-input" style="display:block; font-size:0.76rem; font-weight:700; color:#374151; margin-bottom:4px;">
                      Release Location
                    </label>
                    <input type="text" id="sched-location-input" value="Damayan Department" readonly style="width:100%; box-sizing:border-box; border:1px solid #e5e7eb; border-radius:8px; padding:7px 10px; font-size:0.84rem; font-family:inherit; background:#f9fafb; color:#374151; font-weight:600;" />
                  </div>
                  <div style="grid-column: span 2;">
                    <label for="sched-instructions-input" style="display:block; font-size:0.76rem; font-weight:700; color:#374151; margin-bottom:4px;">
                      Release Instructions <span style="color:#6b7280; font-weight:normal;">(Optional instructions for the user)</span>
                    </label>
                    <textarea id="sched-instructions-input" rows="2" placeholder="e.g. Please bring a valid government ID and official receipt upon claiming at the Damayan counter..." style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:7px 10px; font-size:0.82rem; font-family:inherit; resize:vertical;"></textarea>
                  </div>
                  <div style="grid-column: span 2; display:none;" id="sched-reason-container">
                    <label for="sched-reason-input" style="display:block; font-size:0.76rem; font-weight:700; color:#b45309; margin-bottom:4px;">
                      Reason for Rescheduling <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" id="sched-reason-input" placeholder="Explain reason for revised claiming appointment..." style="width:100%; box-sizing:border-box; border:1px solid #fcd34d; border-radius:8px; padding:7px 10px; font-size:0.82rem; font-family:inherit; background:#fffbeb;" />
                  </div>
                </div>

                <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                  <button type="button" id="btn-cancel-sched-edit" style="display:none; padding:6px 14px; font-size:0.8rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; color:#374151; cursor:pointer;" onclick="cancelRescheduleEdit()">
                    Cancel
                  </button>
                  <button type="button" id="btn-confirm-schedule" class="btn-topbar primary" style="padding:6px 18px; font-size:0.82rem; background:#059669; border-color:#059669; color:#fff; font-weight:700;" onclick="submitReleaseSchedule()">
                    Confirm Schedule
                  </button>
                </div>
              </div>

              <!-- VIEW C: CERTIFICATE OFFICIALLY RELEASED & COMPLETED -->
              <div id="released-completed-view" style="display:none; background:#ecfdf5; border:1.5px solid #6ee7b7; border-radius:10px; padding:14px 16px; margin-bottom:14px;">
                <div style="display:flex; align-items:center; gap:10px;">
                  <div style="width:32px; height:32px; border-radius:8px; background:#d1fae5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                  </div>
                  <div>
                    <strong style="color:#065f46; font-size:0.92rem; display:block;">Certificate Released &amp; Completed</strong>
                    <div style="font-size:0.78rem; color:#047857; margin-top:2px;" id="display-released-meta">
                      The death certificate has been physically released to the recipient.
                    </div>
                  </div>
                </div>
              </div>

              <!-- SCHEDULE HISTORY AUDIT LOGS -->
              <div id="schedule-history-container" style="display:none; margin-top:14px; padding-top:12px; border-top:1px dashed #cbd5e1;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                  <div style="font-size:0.75rem; font-weight:800; color:#334155; text-transform:uppercase; letter-spacing:0.04em;">
                    Appointment History &amp; Revisions
                  </div>
                  <span id="sched-history-count" style="font-size:0.72rem; color:#6b7280; font-weight:600;">0 records</span>
                </div>
                <div id="sched-history-list" style="display:flex; flex-direction:column; gap:6px; max-height:160px; overflow-y:auto;"></div>
              </div>

            </div>

          </div>

      </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="mis-modal-footer" style="padding:14px 22px; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <div id="modal-footer-status-text" style="font-size:0.8rem; color:#4b5563;">
        <!-- Dynamic verification advice -->
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <span id="modal-final-status-badge" style="display:none; font-size:0.8rem; font-weight:700; padding:6px 14px; border-radius:6px;"></span>
        <button type="button" id="modal-btn-schedule-quick" class="btn-topbar primary" style="padding:6px 14px; font-size:0.82rem; background:#047857; border-color:#047857; color:#fff; font-weight:700; display:inline-flex; align-items:center; gap:5px;" onclick="scrollToSchedulingSection()">
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg> Schedule Release
        </button>
        <button type="button" id="modal-btn-send-dawah" class="btn-topbar" style="padding:6px 14px; font-size:0.82rem; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-weight:700; display:inline-flex; align-items:center; gap:6px;" onclick="onModalSendDawahClick()">
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
          Send to Da'wah
        </button>
        <button type="button" id="modal-btn-reject" class="btn-topbar" style="padding:6px 14px; font-size:0.82rem; background:#fff; color:#dc2626; border:1px solid #fca5a5; font-weight:700;" onclick="onModalRejectClick()">
          Reject Request
        </button>
        <button type="button" id="modal-btn-accept" class="btn-topbar primary" style="padding:6px 18px; font-size:0.82rem; background:#059669; border-color:#059669; color:#fff; font-weight:700;" onclick="onModalAcceptClick()">
          Accept Request
        </button>
        <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.82rem; border:1px solid #d1d5db; background:#fff; color:#374151;" onclick="closeCaseModal()">
          Close
        </button>
      </div>
    </div>

  </div>
</div>


<!-- 2. REJECTION MODAL (WITH DROPDOWN AND CONDITIONAL OTHERS INPUT) -->
<div id="damayanRejectModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:100000; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:14px; width:95%; max-width:520px; box-shadow:0 20px 50px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideUp 0.2s ease;">
    
    <div class="mis-modal-header" style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; background:#fef2f2;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:32px; height:32px; border-radius:8px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.05rem; font-weight:800; color:#991b1b;">Reject Request</h5>
          <div id="reject-case-label" style="font-size:0.75rem; color:#7f1d1d; margin-top:2px;">Case #BR-0000</div>
        </div>
      </div>
      <button type="button" onclick="closeRejectModal()" style="background:transparent; border:none; font-size:1.4rem; line-height:1; cursor:pointer; color:#9ca3af;">&times;</button>
    </div>

    <div class="mis-modal-body" style="padding:18px 20px;">
      <p style="font-size:0.84rem; color:#4b5563; margin:0 0 14px 0;">
        Please select the official reason for rejecting this death certificate request. The applicant will be notified with this reason.
      </p>

      <!-- Rejection Reason Dropdown -->
      <div style="margin-bottom:14px;">
        <label for="reject-reason-select" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:6px;">
          Rejection Reason <span style="color:#dc2626;">*</span>
        </label>
        <select id="reject-reason-select" style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:0.84rem; background:#fff; font-family:inherit;" onchange="onRejectReasonChange(this.value)">
          <option value="">-- Select Rejection Reason --</option>
          <option value="Information does not match civil registry records">Information does not match civil registry records</option>
          <option value="Invalid or incorrect registration number">Invalid or incorrect registration number</option>
          <option value="Duplicate request">Duplicate request</option>
          <option value="Incomplete or incorrect information">Incomplete or incorrect information</option>
          <option value="Others">Others</option>
        </select>
      </div>

      <!-- Conditional Input Field for 'Others' -->
      <div id="reject-others-container" style="display:none; margin-bottom:14px;">
        <label for="reject-specific-reason" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:6px;">
          Specify Rejection Reason <span style="color:#dc2626;">*</span>
        </label>
        <textarea id="reject-specific-reason" rows="3" placeholder="Provide a detailed explanation for this rejection..." style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:0.82rem; font-family:inherit; resize:vertical;"></textarea>
      </div>

    </div>

    <div class="mis-modal-footer" style="padding:14px 20px; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:flex-end; gap:10px; background:#fafbfc;">
      <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.82rem; border:1px solid #d1d5db; background:#fff; color:#374151;" onclick="closeRejectModal()">
        Cancel
      </button>
      <button type="button" id="btn-confirm-rejection" class="btn-topbar" style="padding:6px 16px; font-size:0.82rem; background:#dc2626; color:#fff; border:1px solid #dc2626; font-weight:700;" onclick="submitRejectRequest()">
        Confirm Rejection
      </button>
    </div>

  </div>
</div>


<!-- 3. ACCEPT CONFIRMATION MODAL -->
<div id="damayanAcceptConfirmModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:100000; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:14px; width:95%; max-width:480px; box-shadow:0 20px 50px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideUp 0.2s ease;">
    
    <div class="mis-modal-header" style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; background:#ecfdf5;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:32px; height:32px; border-radius:8px; background:#d1fae5; color:#059669; display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.05rem; font-weight:800; color:#064e3b;">Confirm Acceptance</h5>
          <div id="accept-case-label" style="font-size:0.75rem; color:#065f46; margin-top:2px;">Case #BR-0000</div>
        </div>
      </div>
      <button type="button" onclick="closeAcceptModal()" style="background:transparent; border:none; font-size:1.4rem; line-height:1; cursor:pointer; color:#9ca3af;">&times;</button>
    </div>

    <div class="mis-modal-body" style="padding:18px 20px;">
      <p style="font-size:0.86rem; color:#374151; margin:0 0 12px 0;">
        Are you sure you want to accept <strong id="accept-modal-target-label">this request</strong>?
      </p>
      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 12px; font-size:0.8rem; color:#166534; margin-bottom:14px; display:flex; align-items:flex-start; gap:8px;">
        <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;flex-shrink:0;margin-top:2px;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
        <span>Certificate details verified as <strong>Matched</strong>. Once accepted, the request status will change to <strong>Accepted</strong> and the requester will be notified.</span>
      </div>
      <div>
        <label for="accept-optional-notes" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:4px;">
          Optional Notes / Remarks
        </label>
        <textarea id="accept-optional-notes" rows="2" placeholder="Optional notes for the record..." style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:0.82rem; font-family:inherit; resize:vertical;"></textarea>
      </div>
    </div>

    <div class="mis-modal-footer" style="padding:14px 20px; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:flex-end; gap:10px; background:#fafbfc;">
      <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.82rem; border:1px solid #d1d5db; background:#fff; color:#374151;" onclick="closeAcceptModal()">
        Cancel
      </button>
      <button type="button" id="btn-confirm-accept" class="btn-topbar primary" style="padding:6px 18px; font-size:0.82rem; background:#059669; border-color:#059669; color:#fff; font-weight:700;" onclick="submitAcceptRequest()">
        Confirm Acceptance
      </button>
    </div>

  </div>
</div>


<!-- 4. CANCEL RELEASE SCHEDULE MODAL -->
<div id="damayanCancelSchedModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:100000; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:14px; width:95%; max-width:500px; box-shadow:0 20px 50px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideUp 0.2s ease;">
    <div class="mis-modal-header" style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; background:#fef2f2;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:32px; height:32px; border-radius:8px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.05rem; font-weight:800; color:#991b1b;">Cancel Claiming Schedule</h5>
          <div id="cancel-sched-case-label" style="font-size:0.75rem; color:#7f1d1d; margin-top:2px;">Case #BR-0000</div>
        </div>
      </div>
      <button type="button" onclick="closeCancelScheduleModal()" style="background:transparent; border:none; font-size:1.4rem; line-height:1; cursor:pointer; color:#9ca3af;">&times;</button>
    </div>
    <div class="mis-modal-body" style="padding:18px 20px;">
      <p style="font-size:0.84rem; color:#4b5563; margin:0 0 12px 0;">
        Cancelling the claiming schedule will remove the confirmed claiming appointment, return the request stage to <strong>Certificate Prepared</strong>, and notify the applicant.
      </p>
      <div style="margin-bottom:12px;">
        <label for="cancel-sched-reason" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:4px;">
          Cancellation Reason <span style="color:#dc2626;">*</span>
        </label>
        <textarea id="cancel-sched-reason" rows="3" placeholder="Provide a reason for cancelling this appointment..." style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:0.82rem; font-family:inherit; resize:vertical;"></textarea>
      </div>
    </div>
    <div class="mis-modal-footer" style="padding:14px 20px; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:flex-end; gap:10px; background:#fafbfc;">
      <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.82rem; border:1px solid #d1d5db; background:#fff; color:#374151;" onclick="closeCancelScheduleModal()">
        Back
      </button>
      <button type="button" id="btn-confirm-cancel-sched" class="btn-topbar" style="padding:6px 16px; font-size:0.82rem; background:#dc2626; color:#fff; border:1px solid #dc2626; font-weight:700;" onclick="submitCancelSchedule()">
        Confirm Cancellation
      </button>
    </div>
  </div>
</div>


<!-- 5. MARK CERTIFICATE RELEASED / COMPLETED MODAL -->
<div id="damayanMarkReleasedModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:100000; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:14px; width:95%; max-width:500px; box-shadow:0 20px 50px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideUp 0.2s ease;">
    <div class="mis-modal-header" style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; background:#ecfdf5;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:32px; height:32px; border-radius:8px; background:#d1fae5; color:#059669; display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.05rem; font-weight:800; color:#064e3b;">Record Certificate Released</h5>
          <div id="released-case-label" style="font-size:0.75rem; color:#065f46; margin-top:2px;">Case #BR-0000</div>
        </div>
      </div>
      <button type="button" onclick="closeMarkReleasedModal()" style="background:transparent; border:none; font-size:1.4rem; line-height:1; cursor:pointer; color:#9ca3af;">&times;</button>
    </div>
    <div class="mis-modal-body" style="padding:18px 20px;">
      <p style="font-size:0.84rem; color:#374151; margin:0 0 12px 0;">
        Confirm that the official death certificate has been physically claimed by and released to the requesting party. This will mark the case as <strong>Completed</strong>.
      </p>
      <div style="margin-bottom:12px;">
        <label for="release-recipient-input" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:4px;">
          Recipient Full Name <span style="color:#dc2626;">*</span>
        </label>
        <input type="text" id="release-recipient-input" placeholder="Name of person who received the certificate..." style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:7px 10px; font-size:0.84rem; font-family:inherit; background:#fff;" />
      </div>
      <div>
        <label for="release-notes-input" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:4px;">
          Release Remarks / ID Reference (Optional)
        </label>
        <textarea id="release-notes-input" rows="2" placeholder="e.g. Verified with PhilID Card #1234, original receipt presented..." style="width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:8px; padding:7px 10px; font-size:0.82rem; font-family:inherit; resize:vertical;"></textarea>
      </div>
    </div>
    <div class="mis-modal-footer" style="padding:14px 20px; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:flex-end; gap:10px; background:#fafbfc;">
      <button type="button" class="btn-topbar" style="padding:6px 14px; font-size:0.82rem; border:1px solid #d1d5db; background:#fff; color:#374151;" onclick="closeMarkReleasedModal()">
        Cancel
      </button>
      <button type="button" id="btn-confirm-release-done" class="btn-topbar primary" style="padding:6px 18px; font-size:0.82rem; background:#059669; border-color:#059669; color:#fff; font-weight:700;" onclick="submitMarkReleased()">
        Confirm Physical Release
      </button>
    </div>
  </div>
</div>


<!-- JAVASCRIPT LOGIC FOR VERIFICATION, ACCEPT, AND REJECT -->
<script>
  let activeCase = null;
  let targetRejectCaseId = null;
  let targetAcceptCaseId = null;
  let selectedVerificationResult = null;

  // ─────────────────────────────────────────────────────────────
  // 1. OPEN VERIFICATION MODAL
  // ─────────────────────────────────────────────────────────────
  async function openCaseModal(caseIdOrRef) {
    const modal = document.getElementById('damayanCaseModal');
    const loading = document.getElementById('modal-loading');
    const content = document.getElementById('modal-content-container');
    
    if(!modal) return;
    modal.style.display = 'flex';
    loading.style.display = 'block';
    content.style.display = 'none';

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/details') ?>?id=' + encodeURIComponent(caseIdOrRef));
      const data = await res.json();
      
      if(!data.success || !data.burial) {
        if(typeof showAlert === 'function') showAlert('Error', data.error || 'Failed to load case details.', 'error');
        else alert(data.error || 'Failed to load case details.');
        closeCaseModal();
        return;
      }

      activeCase = data.burial;
      populateVerificationModal(data);
      loading.style.display = 'none';
      content.style.display = 'block';
    } catch(err) {
      console.error(err);
      if(typeof showAlert === 'function') showAlert('Error', 'An error occurred while fetching request details.', 'error');
      else alert('An error occurred while fetching request details.');
      closeCaseModal();
    }
  }

  function closeCaseModal() {
    const modal = document.getElementById('damayanCaseModal');
    if(modal) modal.style.display = 'none';
    activeCase = null;
  }

  function onModalSendDawahClick() {
    if (!activeCase) {
      if (typeof showAlert === 'function') showAlert('Information', 'No active case selected.', 'info');
      else alert('No active case selected.');
      return;
    }
    const cert = {
      registry_number: activeCase.death_cert_registry_no || activeCase.encoded_reg_no || activeCase.ref_id || ('BR-' + activeCase.id),
      deceased_name: activeCase.deceased_name || activeCase.deceased || '',
      sex: activeCase.deceased_sex || 'Male',
      date_of_death: activeCase.date_of_death || activeCase.date_died || '',
      time_of_death: activeCase.time_of_death || '',
      date_of_birth: activeCase.date_of_birth || '',
      age: activeCase.age || '',
      place_of_death: activeCase.place_of_death || '',
      residence: activeCase.residence || activeCase.address || '',
      civil_status: activeCase.civil_status || '',
      religion: activeCase.religion || 'Islam',
      citizenship: activeCase.citizenship || 'Filipino',
      occupation: activeCase.occupation || '',
      informant_name: activeCase.applicant_name || activeCase.informant_name || activeCase.name || '',
      informant_contact: activeCase.contact_number || activeCase.phone || '',
      informant_relationship: activeCase.relationship || '',
      death_cert_date: activeCase.death_cert_date || new Date().toISOString().split('T')[0],
      purpose: 'Da\'wah Male Janazah & Burial Coordination',
      transmission_notes: 'Transmitted from Damayan Burial Case #' + (activeCase.ref_id || activeCase.id)
    };
    if (typeof openSendToDawahModal === 'function') {
      openSendToDawahModal(cert);
    } else {
      if (typeof showAlert === 'function') showAlert('Error', 'Transmittal modal is not loaded.', 'error');
      else alert('Transmittal modal is not loaded.');
    }
  }

  // ─────────────────────────────────────────────────────────────
  // 2. POPULATE MODAL CONTENT
  // ─────────────────────────────────────────────────────────────
  function populateVerificationModal(data) {
    const c = data.burial;
    const duplicates = data.duplicates || [];

    // Header
    document.getElementById('modal-ref-id').textContent = '#' + (c.ref_id || 'BR-0000');
    
    // Status Badge (Must be Pending unless Accepted or Rejected by Admin)
    const rawSt = (c.status || 'Pending').toLowerCase().trim();
    const dcSt = (c.death_cert_status || '').toLowerCase().trim();
    const displayStatus = (rawSt === 'accepted' || rawSt.startsWith('accept') || dcSt === 'awaiting authorized certificate preparation')
      ? 'Accepted'
      : ((rawSt === 'rejected' || dcSt === 'rejected')
        ? 'Rejected'
        : ((rawSt === 'completed' || dcSt === 'registered' || dcSt === 'certified copy requested or received') ? 'Completed' : 'Pending'));

    const statusBadge = document.getElementById('modal-status-badge');
    statusBadge.textContent = displayStatus;
    statusBadge.className = 'badge-status ' + getBadgeClass(displayStatus);

    // Request Type Badge
    const reqTypeBadge = document.getElementById('modal-reqtype-badge');
    const isCopy = (c.cert_action_type === 'copy');
    if (isCopy) {
      reqTypeBadge.textContent = 'Request Another Death Certificate';
      reqTypeBadge.style.background = '#dbeafe';
      reqTypeBadge.style.color = '#1e40af';
    } else {
      reqTypeBadge.textContent = 'Register Death Certificate';
      reqTypeBadge.style.background = '#d1fae5';
      reqTypeBadge.style.color = '#065f46';
    }

    // Verification Result Badge in Header
    updateHeaderVerificationBadge(c.verification_result);

    // SECTION A: Request Information
    document.getElementById('v-ref-id').textContent = '#' + (c.ref_id || '—');
    document.getElementById('v-request-type').textContent = isCopy ? 'Request Another Death Certificate' : 'Register Death Certificate';
    document.getElementById('v-date-submitted').textContent = c.submitted_at ? new Date(c.submitted_at).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }) : '—';
    document.getElementById('v-current-status').textContent = displayStatus;

    // SECTION B: Requester Information
    const applicantName = (c.first_name || '') + ' ' + (c.last_name || '');
    document.getElementById('v-requester-name').textContent = applicantName.trim() || (c.informant_name || 'N/A');
    document.getElementById('v-relationship').textContent = c.relationship || (c.informant_relationship || 'Representative');
    document.getElementById('v-contact-number').textContent = c.contact_number || (c.informant_contact || 'N/A');

    // SECTION C: Death Certificate Information
    const regNo = c.existing_cert_no || c.death_cert_registry_no || 'N/A';
    document.getElementById('v-cert-reg-no').textContent = regNo;
    document.getElementById('v-deceased-name').textContent = c.deceased_name || 'N/A';
    document.getElementById('v-deceased-sex').textContent = c.deceased_sex || 'Male';
    document.getElementById('v-death-date').textContent = c.date_of_death ? new Date(c.date_of_death).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }) : 'N/A';
    document.getElementById('v-death-place').textContent = c.place_of_death || 'N/A';



    // DUPLICATE DETECTION WARNING
    const dupContainer = document.getElementById('duplicate-warning-container');
    const dupList = document.getElementById('duplicate-records-list');
    const dupDecisionBadge = document.getElementById('duplicate-review-status');
    const radioDiff = document.getElementById('dup-radio-diff');
    const radioDup = document.getElementById('dup-radio-dup');
    const dupNotes = document.getElementById('dup-review-notes');

    if (duplicates && duplicates.length > 0) {
      dupContainer.style.display = 'block';
      dupList.innerHTML = duplicates.map(d => `
        <div style="background:#fff; border:1px solid #fde68a; border-radius:8px; padding:8px 12px; font-size:0.8rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
          <div>
            <span style="font-weight:700; color:#92400e;">${escapeHtml(d.match_type)}</span>: 
            <strong>${escapeHtml(d.deceased_name || 'N/A')}</strong>
            ${d.reg_no ? ' (Reg #: <code>' + escapeHtml(d.reg_no) + '</code>)' : ''}
          </div>
          <div style="font-size:0.75rem; color:#6b7280;">
            Source: <strong>${escapeHtml(d.source)}</strong> · Status: <strong>${escapeHtml(d.status || 'Active')}</strong>
          </div>
        </div>
      `).join('');

      // Existing decision if previously recorded
      if (c.duplicate_review_decision) {
        dupDecisionBadge.textContent = 'Decision: ' + c.duplicate_review_decision;
        dupDecisionBadge.style.background = (c.duplicate_review_decision === 'Duplicate Request') ? '#fee2e2' : '#d1fae5';
        dupDecisionBadge.style.color = (c.duplicate_review_decision === 'Duplicate Request') ? '#991b1b' : '#065f46';
        if (c.duplicate_review_decision === 'Duplicate Request') radioDup.checked = true;
        else radioDiff.checked = true;
      } else {
        dupDecisionBadge.textContent = 'Pending Staff Review (' + duplicates.length + ' found)';
        dupDecisionBadge.style.background = '#fef3c7';
        dupDecisionBadge.style.color = '#92400e';
        radioDiff.checked = false;
        radioDup.checked = false;
      }
      dupNotes.value = c.duplicate_review_notes || '';
    } else {
      dupContainer.style.display = 'none';
      dupList.innerHTML = '';
    }

    // SECTION 3: VERIFICATION RESULT & REMARKS
    selectedVerificationResult = c.verification_result || null;
    highlightVerificationChoice(selectedVerificationResult);
    document.getElementById('v-remarks-input').value = c.verification_remarks || '';
    
    const verifiedMeta = document.getElementById('v-verified-meta');
    if (c.verified_by && c.verified_at) {
      verifiedMeta.textContent = 'Last recorded by ' + c.verified_by + ' on ' + new Date(c.verified_at).toLocaleDateString();
    } else {
      verifiedMeta.textContent = 'Not yet verified.';
    }

    // Update Modal Action Buttons based on final decision status
    const btnAccept = document.getElementById('modal-btn-accept');
    const btnReject = document.getElementById('modal-btn-reject');
    const badgeFinal = document.getElementById('modal-final-status-badge');
    const footAdvice = document.getElementById('modal-footer-status-text');

    if (displayStatus === 'Accepted') {
      if (btnAccept) btnAccept.style.display = 'none';
      if (btnReject) btnReject.style.display = 'none';
      if (badgeFinal) {
        badgeFinal.style.display = 'inline-flex';
        badgeFinal.style.background = '#d1fae5';
        badgeFinal.style.color = '#065f46';
        badgeFinal.style.border = '1px solid #a7f3d0';
        badgeFinal.innerHTML = '<svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;margin-right:4px;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Request Accepted';
      }
      if (footAdvice) {
        footAdvice.innerHTML = '<span style="color:#059669; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> This request has already been accepted and approved for processing.</span>';
      }
    } else if (displayStatus === 'Rejected') {
      if (btnAccept) btnAccept.style.display = 'none';
      if (btnReject) btnReject.style.display = 'none';
      if (badgeFinal) {
        badgeFinal.style.display = 'inline-flex';
        badgeFinal.style.background = '#fee2e2';
        badgeFinal.style.color = '#991b1b';
        badgeFinal.style.border = '1px solid #fecaca';
        badgeFinal.innerHTML = '<svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;margin-right:4px;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> Request Rejected';
      }
      if (footAdvice) {
        footAdvice.innerHTML = '<span style="color:#dc2626; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> This request has been rejected.</span>';
      }
    } else {
      if (btnAccept) btnAccept.style.display = 'inline-flex';
      if (btnReject) btnReject.style.display = 'inline-flex';
      if (badgeFinal) badgeFinal.style.display = 'none';
      updateFooterAdvice();
    }

    // SECTION D: Workflow Stage & Release Scheduling
    populateSchedulingSection(data);
  }

  function updateHeaderVerificationBadge(res) {
    const badge = document.getElementById('modal-verification-status-badge');
    const summaryBadge = document.getElementById('v-result-summary-badge');
    if(!badge) return;

    if (res === 'Matched') {
      badge.innerHTML = '<svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;margin-right:3px;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Verified: Matched';
      badge.style.background = '#d1fae5';
      badge.style.color = '#065f46';
      if(summaryBadge) {
        summaryBadge.textContent = 'Verified: Matched';
        summaryBadge.className = 'badge-status badge-active';
      }
    } else if (res === 'Mismatch') {
      badge.innerHTML = '<svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;margin-right:3px;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> Mismatch Found';
      badge.style.background = '#fee2e2';
      badge.style.color = '#991b1b';
      if(summaryBadge) {
        summaryBadge.textContent = 'Mismatch Recorded';
        summaryBadge.className = 'badge-status badge-danger';
      }
    } else if (res === 'Unable to Verify') {
      badge.innerHTML = '<svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;margin-right:3px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 16h-2v-2h2v2zm1.07-7.75l-.9.92C12.45 11.9 12 12.5 12 14h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg> Unable to Verify';
      badge.style.background = '#fef3c7';
      badge.style.color = '#92400e';
      if(summaryBadge) {
        summaryBadge.textContent = 'Unable to Verify';
        summaryBadge.className = 'badge-status badge-warning';
      }
    } else {
      badge.textContent = 'Unverified';
      badge.style.background = '#f3f4f6';
      badge.style.color = '#4b5563';
      if(summaryBadge) {
        summaryBadge.textContent = 'Pending Verification';
        summaryBadge.className = 'badge-status badge-pending';
      }
    }
  }

  function updateFooterAdvice() {
    const footAdvice = document.getElementById('modal-footer-status-text');
    if(!footAdvice) return;

    const rawSt = activeCase ? (activeCase.status || '').toLowerCase().trim() : '';
    const dcSt = activeCase ? (activeCase.death_cert_status || '').toLowerCase().trim() : '';
    if (rawSt === 'accepted' || rawSt.startsWith('accept') || dcSt === 'awaiting authorized certificate preparation') {
      footAdvice.innerHTML = '<span style="color:#059669; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> This request has already been accepted and approved for processing.</span>';
      return;
    }
    if (rawSt === 'rejected' || dcSt === 'rejected') {
      footAdvice.innerHTML = '<span style="color:#dc2626; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> This request has been rejected.</span>';
      return;
    }

    const res = selectedVerificationResult || (activeCase ? activeCase.verification_result : null);
    if (res === 'Matched') {
      footAdvice.innerHTML = '<span style="color:#059669; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Information verified and matches records. Request can now be accepted.</span>';
    } else if (res === 'Mismatch') {
      footAdvice.innerHTML = '<span style="color:#dc2626; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> Discrepancies found. Resolve issues or proceed with rejection.</span>';
    } else if (res === 'Unable to Verify') {
      footAdvice.innerHTML = '<span style="color:#d97706; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg> Certificate unreadable or unavailable. Request cannot be accepted.</span>';
    } else {
      footAdvice.innerHTML = '<span style="color:#6b7280;">Verification required: Check registration number and deceased name before accepting.</span>';
    }
  }

  // ─────────────────────────────────────────────────────────────
  // 3. SELECT & SAVE VERIFICATION RESULT
  // ─────────────────────────────────────────────────────────────
  function selectVerificationResult(res) {
    selectedVerificationResult = res;
    highlightVerificationChoice(res);

    const star = document.getElementById('v-remarks-req-star');
    if (star) {
      star.style.display = (res === 'Mismatch' || res === 'Unable to Verify') ? 'inline' : 'none';
    }
    updateFooterAdvice();
  }

  function highlightVerificationChoice(res) {
    const btnMatched = document.getElementById('btn-result-matched');
    const btnMismatch = document.getElementById('btn-result-mismatch');
    const btnUnable = document.getElementById('btn-result-unable');

    if(!btnMatched || !btnMismatch || !btnUnable) return;

    // Reset styles
    btnMatched.style.borderColor = '#d1d5db';
    btnMatched.style.background = '#fff';
    btnMismatch.style.borderColor = '#d1d5db';
    btnMismatch.style.background = '#fff';
    btnUnable.style.borderColor = '#d1d5db';
    btnUnable.style.background = '#fff';

    if (res === 'Matched') {
      btnMatched.style.borderColor = '#059669';
      btnMatched.style.background = '#ecfdf5';
      document.getElementById('radio-matched').checked = true;
    } else if (res === 'Mismatch') {
      btnMismatch.style.borderColor = '#dc2626';
      btnMismatch.style.background = '#fef2f2';
      document.getElementById('radio-mismatch').checked = true;
    } else if (res === 'Unable to Verify') {
      btnUnable.style.borderColor = '#d97706';
      btnUnable.style.background = '#fffbeb';
      document.getElementById('radio-unable').checked = true;
    }
  }

  async function saveVerificationResult() {
    if(!activeCase) return;
    if(!selectedVerificationResult) {
      if(typeof showAlert === 'function') showAlert('Verification Selection Required', 'Please select a verification result (Matched, Mismatch, or Unable to Verify).', 'warning');
      else alert('Please select a verification result.');
      return;
    }

    const remarks = document.getElementById('v-remarks-input').value.trim();

    // Remarks required if Mismatch or Unable to Verify
    if((selectedVerificationResult === 'Mismatch' || selectedVerificationResult === 'Unable to Verify') && !remarks) {
      if(typeof showAlert === 'function') showAlert('Remarks Required', 'Please enter verification remarks explaining the discrepancy or issue found.', 'warning');
      else alert('Please enter verification remarks explaining the discrepancy or issue found.');
      document.getElementById('v-remarks-input').focus();
      return;
    }

    const btn = document.getElementById('btn-save-verification');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/record-verification') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: activeCase.id,
          result: selectedVerificationResult,
          remarks: remarks
        })
      });
      const data = await res.json();

      if(data.success) {
        activeCase.verification_result = selectedVerificationResult;
        activeCase.verification_remarks = remarks;
        updateHeaderVerificationBadge(selectedVerificationResult);
        updateFooterAdvice();

        document.getElementById('v-verified-meta').textContent = 'Last recorded by ' + (data.verified_by || 'Staff') + ' just now.';
        if(typeof showAlert === 'function') showAlert('Verification Saved', 'Verification result marked as ' + selectedVerificationResult + ' and recorded in database.', 'success');
        else alert('Verification result saved.');
      } else {
        if(typeof showAlert === 'function') showAlert('Error', data.error || 'Failed to save verification result.', 'error');
        else alert(data.error || 'Failed to save verification result.');
      }
    } catch(err) {
      console.error(err);
      if(typeof showAlert === 'function') showAlert('Error', 'Network error occurred while saving verification result.', 'error');
      else alert('Network error occurred.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Save Verification Result';
    }
  }

  // ─────────────────────────────────────────────────────────────
  // 4. DUPLICATE REVIEW DECISION
  // ─────────────────────────────────────────────────────────────
  function onDuplicateRadioChange() {
    // optional helper
  }

  async function saveDuplicateReviewDecision() {
    if(!activeCase) return;
    const selectedRadio = document.querySelector('input[name="dup_decision"]:checked');
    if(!selectedRadio) {
      if(typeof showAlert === 'function') showAlert('Decision Required', 'Please select whether this is a Different Person or a Duplicate Request.', 'warning');
      else alert('Please select a duplicate review decision.');
      return;
    }

    const decision = selectedRadio.value;
    const notes = document.getElementById('dup-review-notes').value.trim();

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/review-duplicate') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: activeCase.id,
          decision: decision,
          notes: notes
        })
      });
      const data = await res.json();

      if(data.success) {
        const badge = document.getElementById('duplicate-review-status');
        badge.textContent = 'Decision: ' + decision;
        badge.style.background = (decision === 'Duplicate Request') ? '#fee2e2' : '#d1fae5';
        badge.style.color = (decision === 'Duplicate Request') ? '#991b1b' : '#065f46';
        if(typeof showAlert === 'function') showAlert('Review Saved', 'Duplicate review decision saved: ' + decision, 'success');
        else alert('Duplicate review saved.');
      } else {
        if(typeof showAlert === 'function') showAlert('Error', data.error || 'Failed to record duplicate decision.', 'error');
        else alert(data.error || 'Failed to record duplicate decision.');
      }
    } catch(err) {
      console.error(err);
      if(typeof showAlert === 'function') showAlert('Error', 'Network error occurred.', 'error');
      else alert('Network error occurred.');
    }
  }

  // ─────────────────────────────────────────────────────────────
  // 5. ACCEPT PROCESS (GUARDED BY VERIFICATION)
  // ─────────────────────────────────────────────────────────────
  function tableAcceptRequest(refId, verificationResult, deceasedName) {
    openAcceptModal(refId, deceasedName);
  }

  function onModalAcceptClick() {
    if(!activeCase) return;
    openAcceptModal(activeCase.id, activeCase.deceased_name || ('#' + activeCase.ref_id));
  }

  function openAcceptModal(caseId, deceasedName) {
    targetAcceptCaseId = caseId;
    document.getElementById('accept-case-label').textContent = 'Case #' + caseId;
    document.getElementById('accept-modal-target-label').textContent = deceasedName ? ('request for ' + deceasedName) : 'this request';
    document.getElementById('accept-optional-notes').value = '';
    document.getElementById('damayanAcceptConfirmModal').style.display = 'flex';
  }

  function closeAcceptModal() {
    document.getElementById('damayanAcceptConfirmModal').style.display = 'none';
    targetAcceptCaseId = null;
  }

  async function submitAcceptRequest() {
    if(!targetAcceptCaseId) return;

    const notes = document.getElementById('accept-optional-notes').value.trim();
    const btn = document.getElementById('btn-confirm-accept');
    btn.disabled = true;
    btn.textContent = 'Accepting...';

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/accept') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: targetAcceptCaseId,
          notes: notes
        })
      });
      const data = await res.json();

      if(data.success) {
        closeAcceptModal();
        closeCaseModal();
        if(typeof showAlert === 'function') {
          showAlert('Request Accepted', 'The death certificate request has been accepted for processing, and the requester has been notified.', 'success');
        } else {
          alert('Request accepted successfully.');
        }
        // Redirect to accepted list so the accepted case is immediately visible in the Accepted view
        setTimeout(() => { 
          window.location.href = '<?= url('/admin/damayan/burial-requests?filter=accepted') ?>'; 
        }, 1000);
      } else {
        if(typeof showAlert === 'function') showAlert('Acceptance Failed', data.error || 'Failed to accept request.', 'error');
        else alert(data.error || 'Failed to accept request.');
      }
    } catch(err) {
      console.error(err);
      if(typeof showAlert === 'function') showAlert('Error', 'Network error occurred.', 'error');
      else alert('Network error occurred.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Confirm Acceptance';
    }
  }

  // ─────────────────────────────────────────────────────────────
  // 6. REJECT PROCESS (DROPDOWN WITH CONDITIONAL OTHERS)
  // ─────────────────────────────────────────────────────────────
  function onModalRejectClick() {
    if(!activeCase) return;
    openRejectModal(activeCase.id);
  }

  function openRejectModal(caseId) {
    targetRejectCaseId = caseId;
    document.getElementById('reject-case-label').textContent = 'Case #' + caseId;
    
    // Reset dropdown and conditional field
    const select = document.getElementById('reject-reason-select');
    select.value = '';
    document.getElementById('reject-others-container').style.display = 'none';
    document.getElementById('reject-specific-reason').value = '';

    document.getElementById('damayanRejectModal').style.display = 'flex';
  }

  function closeRejectModal() {
    document.getElementById('damayanRejectModal').style.display = 'none';
    targetRejectCaseId = null;
  }

  function onRejectReasonChange(val) {
    const othersContainer = document.getElementById('reject-others-container');
    const specificInput = document.getElementById('reject-specific-reason');

    if (val === 'Others') {
      othersContainer.style.display = 'block';
      specificInput.focus();
    } else {
      othersContainer.style.display = 'none';
      specificInput.value = ''; // Clear entered explanation when different reason chosen
    }
  }

  async function submitRejectRequest() {
    if(!targetRejectCaseId) return;

    const select = document.getElementById('reject-reason-select');
    const reasonCategory = select.value.trim();
    const specificReason = document.getElementById('reject-specific-reason').value.trim();

    if(!reasonCategory) {
      if(typeof showAlert === 'function') showAlert('Reason Required', 'Please select a rejection reason from the dropdown.', 'warning');
      else alert('Please select a rejection reason from the dropdown.');
      select.focus();
      return;
    }

    if(reasonCategory === 'Others' && !specificReason) {
      if(typeof showAlert === 'function') showAlert('Specific Reason Required', 'Please specify the rejection reason in the provided field.', 'warning');
      else alert('Please specify the rejection reason.');
      document.getElementById('reject-specific-reason').focus();
      return;
    }

    const btn = document.getElementById('btn-confirm-rejection');
    btn.disabled = true;
    btn.textContent = 'Rejecting...';

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/reject') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: targetRejectCaseId,
          reason_category: reasonCategory,
          specific_reason: specificReason
        })
      });
      const data = await res.json();

      if(data.success) {
        closeRejectModal();
        closeCaseModal();
        if(typeof showAlert === 'function') {
          showAlert('Request Rejected', 'The request has been rejected and the requester has been notified with the reason.', 'success');
        } else {
          alert('Request rejected successfully.');
        }
        // Redirect to rejected list so the rejected case is immediately visible in the Rejected view
        setTimeout(() => { 
          window.location.href = '<?= url('/admin/damayan/burial-requests?filter=rejected') ?>'; 
        }, 1000);
      } else {
        if(typeof showAlert === 'function') showAlert('Rejection Failed', data.error || 'Failed to reject request.', 'error');
        else alert(data.error || 'Failed to reject request.');
      }
    } catch(err) {
      console.error(err);
      if(typeof showAlert === 'function') showAlert('Error', 'Network error occurred.', 'error');
      else alert('Network error occurred.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Confirm Rejection';
    }
  }

  // ─────────────────────────────────────────────────────────────
  // 7. UTILITIES & KEYBOARD / BACKDROP DISMISSAL
  // ─────────────────────────────────────────────────────────────

  function getBadgeClass(status) {
    const st = String(status || '').toLowerCase().trim();
    if(st === 'accepted' || st === 'burial completed' || st === 'completed') return 'badge-active';
    if(st === 'rejected') return 'badge-danger';
    if(st === 'additional information required' || st === 'mismatch') return 'badge-warning';
    return 'badge-pending';
  }

  function escapeHtml(str) {
    if(!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
    });
  }

  // ─────────────────────────────────────────────────────────────
  // 8. SECTION D: RELEASE SCHEDULING & STAGE ADVANCEMENT LOGIC
  // ─────────────────────────────────────────────────────────────
  let isReschedulingMode = false;

  function scrollToSchedulingSection() {
    const el = document.getElementById('section-release-scheduling');
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.style.transition = 'box-shadow 0.3s ease';
      el.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.4)';
      setTimeout(() => { el.style.boxShadow = ''; }, 1500);
    }
  }

  function populateSchedulingSection(data) {
    const c = data.burial || {};
    const history = data.schedule_history || c.schedule_history || [];

    isReschedulingMode = false;
    const stage = c.death_cert_status || (c.status === 'Accepted' ? 'Approved' : (c.status === 'Completed' ? 'Completed' : 'Submitted'));

    // Update current stage badge
    const stageBadge = document.getElementById('v-current-stage-badge');
    if (stageBadge) {
      stageBadge.textContent = stage;
      stageBadge.className = 'badge-status ' + (stage === 'Completed' || stage === 'Ready for Release' ? 'badge-active' : 'badge-pending');
    }

    // Update Stage Dropdown
    const stageDropdown = document.getElementById('stage-select-dropdown');
    if (stageDropdown) {
      stageDropdown.value = stage;
    }

    // Update Pipeline visual pills
    updatePipelineUI(stage);

    // Section Views
    const schedView = document.getElementById('confirmed-schedule-view');
    const formView = document.getElementById('schedule-entry-form');
    const releasedView = document.getElementById('released-completed-view');
    const cancelEditBtn = document.getElementById('btn-cancel-sched-edit');
    const reasonContainer = document.getElementById('sched-reason-container');
    const formTitle = document.getElementById('sched-form-title');
    const modeBadge = document.getElementById('sched-mode-badge');
    const quickSchedBtn = document.getElementById('modal-btn-schedule-quick');

    if (cancelEditBtn) cancelEditBtn.style.display = 'none';
    if (reasonContainer) reasonContainer.style.display = 'none';
    if (modeBadge) modeBadge.style.display = 'none';
    if (formTitle) formTitle.textContent = 'Schedule Certificate Release';

    if (c.certificate_released_at) {
      // Completed & Physically Released
      if (schedView) schedView.style.display = 'none';
      if (formView) formView.style.display = 'none';
      if (quickSchedBtn) quickSchedBtn.style.display = 'none';
      if (releasedView) {
        releasedView.style.display = 'block';
        const releasedDate = new Date(c.certificate_released_at).toLocaleString('en-US', { year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'2-digit' });
        document.getElementById('display-released-meta').innerHTML = `
          Certificate officially released to <strong>${escapeHtml(c.certificate_recipient_name || 'Claimant')}</strong> on <strong>${releasedDate}</strong> by ${escapeHtml(c.certificate_released_by || 'Damayan Staff')}.
          ${c.release_notes ? `<div style="margin-top:5px; font-size:0.75rem; color:#475569; background:#fff; padding:6px 10px; border-radius:6px; border:1px solid #bbf7d0;">ID / Reference: <em>${escapeHtml(c.release_notes)}</em></div>` : ''}
        `;
      }
    } else if (c.scheduled_release_date) {
      // Confirmed Claiming Schedule exists
      if (releasedView) releasedView.style.display = 'none';
      if (formView) formView.style.display = 'none';
      if (quickSchedBtn) quickSchedBtn.style.display = 'none';
      if (schedView) {
        schedView.style.display = 'block';

        let formattedDate = c.scheduled_release_date;
        try {
          const parts = c.scheduled_release_date.split('-');
          if (parts.length === 3) {
            const d = new Date(parts[0], parts[1]-1, parts[2]);
            formattedDate = d.toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
          }
        } catch(e){}

        let formattedTime = c.scheduled_release_time || '—';
        if (c.scheduled_release_time) {
          try {
            const tParts = c.scheduled_release_time.split(':');
            const h = parseInt(tParts[0], 10);
            const m = tParts[1] || '00';
            const ampm = h >= 12 ? 'PM' : 'AM';
            const h12 = h % 12 || 12;
            formattedTime = h12 + ':' + m + ' ' + ampm;
          } catch(e){}
        }

        document.getElementById('display-sched-date').textContent = formattedDate;
        document.getElementById('display-sched-time').textContent = formattedTime;
        document.getElementById('display-sched-loc').textContent = c.release_location || 'Damayan Department';
        document.getElementById('display-sched-by').textContent = c.release_scheduled_by_name || 'Damayan Admin';
        document.getElementById('display-sched-instructions').textContent = c.release_instructions || 'Please bring a valid ID when claiming your certificate.';
      }
    } else {
      // No schedule set yet
      if (releasedView) releasedView.style.display = 'none';
      if (schedView) schedView.style.display = 'none';
      if (quickSchedBtn) quickSchedBtn.style.display = 'inline-flex';
      if (formView) {
        formView.style.display = 'block';
        document.getElementById('sched-date-input').value = '';
        document.getElementById('sched-time-input').value = '10:00';
        document.getElementById('sched-instructions-input').value = '';
        document.getElementById('sched-reason-input').value = '';
      }
    }

    // Render schedule audit history
    renderScheduleHistory(history);
  }

  function updatePipelineUI(currentStage) {
    const stageRankMap = {
      'Submitted': 1,
      'Request Received': 1,
      'Under Review': 2,
      'Pending': 2,
      'Approved': 3,
      'Accepted': 3,
      'Certificate Prepared': 4,
      'Certificate Prepared and Certified': 4,
      'Ready for Release': 5,
      'Completed': 6,
      'Registered': 6,
      'Released': 6
    };
    const currentRank = stageRankMap[currentStage] || 1;

    for (let i = 1; i <= 6; i++) {
      const stepEl = document.getElementById('pstep-' + i);
      if (!stepEl) continue;
      if (i < currentRank) {
        stepEl.style.background = '#d1fae5';
        stepEl.style.color = '#065f46';
        stepEl.style.borderColor = '#10b981';
      } else if (i === currentRank) {
        stepEl.style.background = '#059669';
        stepEl.style.color = '#ffffff';
        stepEl.style.borderColor = '#047857';
      } else {
        stepEl.style.background = '#f1f5f9';
        stepEl.style.color = '#64748b';
        stepEl.style.borderColor = '#e2e8f0';
      }
    }
  }

  function renderScheduleHistory(history) {
    const container = document.getElementById('schedule-history-container');
    const list = document.getElementById('sched-history-list');
    const countBadge = document.getElementById('sched-history-count');
    if (!container || !list) return;

    if (!history || history.length === 0) {
      container.style.display = 'none';
      list.innerHTML = '';
      return;
    }

    container.style.display = 'block';
    countBadge.textContent = history.length + ' update' + (history.length > 1 ? 's' : '');

    list.innerHTML = history.map(h => {
      let badgeBg = '#f1f5f9';
      let badgeCol = '#475569';
      if (h.action_type === 'SCHEDULED') { badgeBg = '#ecfdf5'; badgeCol = '#065f46'; }
      else if (h.action_type === 'RESCHEDULED') { badgeBg = '#fffbeb'; badgeCol = '#92400e'; }
      else if (h.action_type === 'CANCELLED') { badgeBg = '#fef2f2'; badgeCol = '#991b1b'; }
      else if (h.action_type === 'RELEASED') { badgeBg = '#ecfdf5'; badgeCol = '#047857'; }

      const createdDate = new Date(h.created_at).toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'2-digit' });
      return `
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:6px; padding:8px 10px; font-size:0.75rem;">
          <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:3px;">
            <span style="font-weight:700; padding:2px 6px; border-radius:4px; font-size:0.68rem; background:${badgeBg}; color:${badgeCol};">
              ${escapeHtml(h.action_type)}
            </span>
            <span style="color:#94a3b8; font-size:0.7rem;">${createdDate}</span>
          </div>
          <div style="color:#334155; margin-top:2px;">
            ${h.scheduled_date ? `<strong>Scheduled:</strong> ${escapeHtml(h.scheduled_date)} ${h.scheduled_time ? escapeHtml(h.scheduled_time) : ''} &bull; ` : ''}
            <span>By: <strong>${escapeHtml(h.action_by_name || 'Damayan Staff')}</strong></span>
          </div>
          ${h.reason ? `<div style="color:#64748b; font-style:italic; margin-top:2px;">Reason / Note: ${escapeHtml(h.reason)}</div>` : ''}
        </div>
      `;
    }).join('');
  }

  function toggleRescheduleForm() {
    if (!activeCase) return;
    isReschedulingMode = true;

    document.getElementById('confirmed-schedule-view').style.display = 'none';
    const formView = document.getElementById('schedule-entry-form');
    formView.style.display = 'block';

    document.getElementById('sched-form-title').textContent = 'Reschedule Claiming Appointment';
    document.getElementById('sched-mode-badge').style.display = 'inline-block';
    document.getElementById('btn-cancel-sched-edit').style.display = 'inline-block';
    document.getElementById('sched-reason-container').style.display = 'block';

    document.getElementById('sched-date-input').value = activeCase.scheduled_release_date || '';
    document.getElementById('sched-time-input').value = activeCase.scheduled_release_time || '10:00';
    document.getElementById('sched-instructions-input').value = activeCase.release_instructions || '';
    document.getElementById('sched-reason-input').value = '';
    document.getElementById('sched-reason-input').focus();
  }

  function cancelRescheduleEdit() {
    isReschedulingMode = false;
    document.getElementById('schedule-entry-form').style.display = 'none';
    document.getElementById('confirmed-schedule-view').style.display = 'block';
  }

  async function submitReleaseSchedule() {
    if (!activeCase) return;

    const dateInput = document.getElementById('sched-date-input');
    const timeInput = document.getElementById('sched-time-input');
    const instInput = document.getElementById('sched-instructions-input');
    const reasonInput = document.getElementById('sched-reason-input');

    const releaseDate = dateInput.value.trim();
    const releaseTime = timeInput.value.trim();
    const instructions = instInput.value.trim();
    const reason = reasonInput.value.trim();

    if (!releaseDate) {
      if (typeof showAlert === 'function') showAlert('Release Date Required', 'Please select a scheduled claiming date.', 'warning');
      else alert('Please select a scheduled release date.');
      dateInput.focus();
      return;
    }

    if (!releaseTime) {
      if (typeof showAlert === 'function') showAlert('Release Time Required', 'Please select a scheduled claiming time.', 'warning');
      else alert('Please select a scheduled release time.');
      timeInput.focus();
      return;
    }

    // Client-side future validation
    const schedDateTime = new Date(releaseDate + 'T' + releaseTime);
    const now = new Date();
    if (isNaN(schedDateTime.getTime()) || schedDateTime <= now) {
      if (typeof showAlert === 'function') showAlert('Invalid Appointment Time', 'The scheduled claiming date and time must be in the future.', 'warning');
      else alert('The scheduled claiming date and time must be in the future.');
      dateInput.focus();
      return;
    }

    if (isReschedulingMode && !reason) {
      if (typeof showAlert === 'function') showAlert('Reschedule Reason Required', 'Please provide a brief reason for changing the claiming appointment.', 'warning');
      else alert('Please provide a reason for rescheduling.');
      reasonInput.focus();
      return;
    }

    // Admin confirmation
    const readableDate = schedDateTime.toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
    const readableTime = schedDateTime.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' });
    const confirmPrompt = isReschedulingMode
      ? `Confirm rescheduling the claiming appointment to ${readableDate} at ${readableTime}? The applicant will be notified of the revised schedule.`
      : `Confirm scheduling the certificate claiming appointment on ${readableDate} at ${readableTime}? The applicant will be notified to visit Damayan Department.`;

    if (!confirm(confirmPrompt)) {
      return;
    }

    const btn = document.getElementById('btn-confirm-schedule');
    btn.disabled = true;
    btn.textContent = 'Saving Schedule...';

    const endpoint = isReschedulingMode
      ? '<?= url('/admin/damayan/burial/reschedule-release') ?>'
      : '<?= url('/admin/damayan/burial/schedule-release') ?>';

    const payload = {
      id: activeCase.id,
      release_date: releaseDate,
      release_time: releaseTime,
      instructions: instructions
    };
    if (isReschedulingMode) {
      payload.reason = reason;
    }

    try {
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (data.success) {
        // Update activeCase record
        activeCase.death_cert_status = 'Ready for Release';
        activeCase.scheduled_release_date = data.scheduled_release_date || releaseDate;
        activeCase.scheduled_release_time = data.scheduled_release_time || releaseTime;
        activeCase.release_instructions = data.release_instructions || instructions;
        activeCase.release_location = data.release_location || 'Damayan Department';
        activeCase.release_scheduled_by_name = data.release_scheduled_by_name || 'Damayan Admin';

        // Refresh UI
        populateSchedulingSection({
          burial: activeCase,
          schedule_history: data.schedule_history || []
        });

        let emailMsg = data.email_sent ? ' An email notification was also sent to the applicant.' : '';
        if (typeof showAlert === 'function') {
          showAlert('Schedule Confirmed', 'The death certificate release schedule was confirmed and saved.' + emailMsg, 'success');
        } else {
          alert('Release schedule confirmed and saved.' + emailMsg);
        }
      } else {
        if (typeof showAlert === 'function') showAlert('Scheduling Failed', data.error || 'Failed to save schedule.', 'error');
        else alert(data.error || 'Failed to save schedule.');
      }
    } catch (err) {
      console.error(err);
      if (typeof showAlert === 'function') showAlert('Error', 'Network error occurred while confirming schedule.', 'error');
      else alert('Network error occurred.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Confirm Schedule';
    }
  }

  async function submitStageUpdate() {
    if (!activeCase) return;

    const select = document.getElementById('stage-select-dropdown');
    const newStage = select.value.trim();

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/update-stage') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: activeCase.id,
          stage: newStage
        })
      });
      const data = await res.json();

      if (data.success) {
        activeCase.death_cert_status = data.new_stage || newStage;
        if (data.new_status) activeCase.status = data.new_status;

        const stageBadge = document.getElementById('v-current-stage-badge');
        if (stageBadge) {
          stageBadge.textContent = activeCase.death_cert_status;
          stageBadge.className = 'badge-status ' + (activeCase.death_cert_status === 'Completed' ? 'badge-active' : 'badge-pending');
        }
        updatePipelineUI(activeCase.death_cert_status);

        if (typeof showAlert === 'function') showAlert('Stage Updated', 'Processing stage updated to: ' + activeCase.death_cert_status, 'success');
        else alert('Stage updated.');
      } else {
        if (typeof showAlert === 'function') showAlert('Update Failed', data.error || 'Failed to update processing stage.', 'error');
        else alert(data.error || 'Failed to update stage.');
      }
    } catch (err) {
      console.error(err);
      if (typeof showAlert === 'function') showAlert('Error', 'Network error while updating stage.', 'error');
      else alert('Network error.');
    }
  }

  function openCancelScheduleModal() {
    if (!activeCase) return;
    document.getElementById('cancel-sched-case-label').textContent = 'Case #' + (activeCase.ref_id || activeCase.id);
    document.getElementById('cancel-sched-reason').value = '';
    document.getElementById('damayanCancelSchedModal').style.display = 'flex';
    document.getElementById('cancel-sched-reason').focus();
  }

  function closeCancelScheduleModal() {
    document.getElementById('damayanCancelSchedModal').style.display = 'none';
  }

  async function submitCancelSchedule() {
    if (!activeCase) return;

    const reason = document.getElementById('cancel-sched-reason').value.trim();
    if (!reason) {
      if (typeof showAlert === 'function') showAlert('Reason Required', 'Please provide a reason for cancelling this claiming schedule.', 'warning');
      else alert('Please enter a cancellation reason.');
      document.getElementById('cancel-sched-reason').focus();
      return;
    }

    const btn = document.getElementById('btn-confirm-cancel-sched');
    btn.disabled = true;
    btn.textContent = 'Cancelling...';

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/cancel-release') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: activeCase.id,
          reason: reason
        })
      });
      const data = await res.json();

      if (data.success) {
        closeCancelScheduleModal();
        activeCase.death_cert_status = 'Certificate Prepared';
        activeCase.scheduled_release_date = null;
        activeCase.scheduled_release_time = null;

        populateSchedulingSection({
          burial: activeCase,
          schedule_history: data.schedule_history || []
        });

        let emailMsg = data.email_sent ? ' An email notification was also sent to the applicant.' : '';
        if (typeof showAlert === 'function') {
          showAlert('Schedule Cancelled', 'The claiming appointment was cancelled and applicant notified.' + emailMsg, 'info');
        } else {
          alert('Claiming appointment cancelled.' + emailMsg);
        }
      } else {
        if (typeof showAlert === 'function') showAlert('Cancellation Failed', data.error || 'Failed to cancel schedule.', 'error');
        else alert(data.error || 'Failed to cancel schedule.');
      }
    } catch (err) {
      console.error(err);
      if (typeof showAlert === 'function') showAlert('Error', 'Network error occurred.', 'error');
      else alert('Network error occurred.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Confirm Cancellation';
    }
  }

  function openMarkReleasedModal() {
    if (!activeCase) return;
    document.getElementById('released-case-label').textContent = 'Case #' + (activeCase.ref_id || activeCase.id);
    const applicantName = (activeCase.first_name || '') + ' ' + (activeCase.last_name || '');
    document.getElementById('release-recipient-input').value = applicantName.trim() || (activeCase.informant_name || '');
    document.getElementById('release-notes-input').value = '';
    document.getElementById('damayanMarkReleasedModal').style.display = 'flex';
  }

  function closeMarkReleasedModal() {
    document.getElementById('damayanMarkReleasedModal').style.display = 'none';
  }

  async function submitMarkReleased() {
    if (!activeCase) return;

    const recipient = document.getElementById('release-recipient-input').value.trim();
    const notes = document.getElementById('release-notes-input').value.trim();

    if (!recipient) {
      if (typeof showAlert === 'function') showAlert('Recipient Required', 'Please enter the name of the recipient who claimed the certificate.', 'warning');
      else alert('Please enter recipient full name.');
      document.getElementById('release-recipient-input').focus();
      return;
    }

    const btn = document.getElementById('btn-confirm-release-done');
    btn.disabled = true;
    btn.textContent = 'Recording Release...';

    try {
      const res = await fetch('<?= url('/admin/damayan/burial/mark-released') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: activeCase.id,
          recipient_name: recipient,
          release_notes: notes
        })
      });
      const data = await res.json();

      if (data.success) {
        closeMarkReleasedModal();
        activeCase.status = 'Completed';
        activeCase.death_cert_status = 'Completed';
        activeCase.certificate_released_at = data.certificate_released_at || new Date().toISOString();
        activeCase.certificate_released_by = data.certificate_released_by || 'Damayan Staff';
        activeCase.certificate_recipient_name = recipient;
        activeCase.release_notes = notes;

        populateSchedulingSection({
          burial: activeCase,
          schedule_history: data.schedule_history || []
        });

        // Also update modal top status badge
        const statusBadge = document.getElementById('modal-status-badge');
        if (statusBadge) {
          statusBadge.textContent = 'Completed';
          statusBadge.className = 'badge-status badge-active';
        }

        let emailMsg = data.email_sent ? ' An email confirmation was sent to the applicant.' : '';
        if (typeof showAlert === 'function') {
          showAlert('Certificate Released', 'Certificate recorded as physically claimed. Request marked as Completed.' + emailMsg, 'success');
        } else {
          alert('Certificate released and request marked as Completed.' + emailMsg);
        }
      } else {
        if (typeof showAlert === 'function') showAlert('Recording Failed', data.error || 'Failed to record release.', 'error');
        else alert(data.error || 'Failed to record release.');
      }
    } catch (err) {
      console.error(err);
      if (typeof showAlert === 'function') showAlert('Error', 'Network error occurred.', 'error');
      else alert('Network error occurred.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Confirm Physical Release';
    }
  }

  // Keyboard Escape listener
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const caseModal = document.getElementById('damayanCaseModal');
      const rejectModal = document.getElementById('damayanRejectModal');
      const acceptModal = document.getElementById('damayanAcceptConfirmModal');

      if (rejectModal && rejectModal.style.display === 'flex') {
        closeRejectModal();
        return;
      }
      if (acceptModal && acceptModal.style.display === 'flex') {
        closeAcceptModal();
        return;
      }
      if (caseModal && caseModal.style.display === 'flex') {
        closeCaseModal();
      }
    }
  });

  // Backdrop click listener
  document.getElementById('damayanCaseModal')?.addEventListener('click', function(e) {
    if(e.target === this) closeCaseModal();
  });
  document.getElementById('damayanRejectModal')?.addEventListener('click', function(e) {
    if(e.target === this) closeRejectModal();
  });
  document.getElementById('damayanAcceptConfirmModal')?.addEventListener('click', function(e) {
    if(e.target === this) closeAcceptModal();
  });
</script>
