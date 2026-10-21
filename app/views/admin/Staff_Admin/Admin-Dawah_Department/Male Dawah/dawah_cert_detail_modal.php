<!-- Details & Action Modal for Male Da'wah -->
<div id="dawahCertModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:999999; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:18px; width:100%; max-width:800px; max-height:92vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.3); overflow:hidden;">
    
    <!-- Modal Header -->
    <div style="flex:0 0 auto; padding:18px 24px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#e8f5ed; color:#14532d; display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:22px; height:22px; fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.15rem; font-weight:800; color:#14532d;">Death Certificate Record</h5>
          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Transmitted from Damayan Department &bull; Reg <strong id="m-reg-no">—</strong></div>
        </div>
      </div>
      <button type="button" class="close-btn" onclick="closeDawahCertModal()" style="background:transparent; border:none; font-size:1.6rem; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>

    <!-- Modal Body (Scrollable) -->
    <div style="padding:22px 24px; overflow-y:auto; flex:1 1 auto; max-height:calc(92vh - 140px);">
      
      <!-- Deceased Banner -->
      <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:12px; padding:14px 18px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <div style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:var(--text-muted);">Deceased Person</div>
          <div id="m-deceased-name" style="font-size:1.2rem; font-weight:800; color:#0f172a; margin-top:2px;">—</div>
          <div id="m-vital-sub" style="font-size:0.78rem; color:var(--text-muted); margin-top:3px;">—</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:var(--text-muted);">Transmittal Status</div>
          <span id="m-status-badge" class="badge-status badge-received" style="font-size:0.75rem; margin-top:4px;">Received</span>
        </div>
      </div>

      <!-- 2 Column Details -->
      <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:12px; margin-bottom:14px;">
        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px 14px;">
          <h6 style="font-size:0.74rem; font-weight:800; text-transform:uppercase; color:#14532d; margin-bottom:8px; border-bottom:1px solid #e5e7eb; padding-bottom:4px;">Civil &amp; Registry Details</h6>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Registry No.:</span><span style="font-weight:700;" id="m-reg">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Date Cert Received:</span><span style="font-weight:700;" id="m-cert-date">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Transmitted Date:</span><span style="font-weight:700;" id="m-sent-at">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Civil Status:</span><span style="font-weight:700;" id="m-civil">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Religion:</span><span style="font-weight:700;" id="m-religion">Islam</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Citizenship:</span><span style="font-weight:700;" id="m-citizenship">Filipino</span></div>
        </div>

        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px 14px;">
          <h6 style="font-size:0.74rem; font-weight:800; text-transform:uppercase; color:#14532d; margin-bottom:8px; border-bottom:1px solid #e5e7eb; padding-bottom:4px;">Death &amp; Demographics</h6>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Date of Death:</span><span style="font-weight:700; color:#b91c1c;" id="m-dod">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Time of Death:</span><span style="font-weight:700;" id="m-tod">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Date of Birth:</span><span style="font-weight:700;" id="m-dob">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Age at Death:</span><span style="font-weight:700;" id="m-age">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600;">Occupation:</span><span style="font-weight:700;" id="m-occupation">—</span></div>
        </div>

        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px 14px; grid-column: span 2;">
          <h6 style="font-size:0.74rem; font-weight:800; text-transform:uppercase; color:#14532d; margin-bottom:8px; border-bottom:1px solid #e5e7eb; padding-bottom:4px;">Place of Death &amp; Residence</h6>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600; width:30%;">Place of Death:</span><span style="font-weight:700; width:70%; text-align:right;" id="m-pod">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600; width:30%;">Residence:</span><span style="font-weight:700; width:70%; text-align:right;" id="m-residence">—</span></div>
          <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:5px;"><span style="color:var(--text-muted); font-weight:600; width:30%;">Informant / Rep:</span><span style="font-weight:700; width:70%; text-align:right;" id="m-informant">—</span></div>
        </div>
      </div>

      <!-- Transmittal Card -->
      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:4px;">
          <div><strong>Transmitted By:</strong> <span id="m-sent-by">—</span></div>
          <div><strong>Purpose:</strong> <span id="m-purpose" style="color:#14532d; font-weight:700;">—</span></div>
        </div>
        <div style="font-size:0.8rem; margin-top:6px; color:#14532d;">
          <strong>Transmittal Notes:</strong>
          <div id="m-notes" style="background:#fff; border:1px solid #dcfce7; border-radius:6px; padding:8px 10px; margin-top:4px; font-style:italic;">None provided.</div>
        </div>
      </div>

      <!-- Da'wah Action Section -->
      <div style="background:#fff; border:1.5px solid var(--border); border-radius:12px; padding:14px 18px;">
        <h6 style="font-size:0.82rem; font-weight:800; text-transform:uppercase; color:#14532d; margin-bottom:10px;">
          Da'wah Action &amp; Religious Service Tracking
        </h6>
        <form id="dawah-ack-form" onsubmit="event.preventDefault(); submitDawahStatus();">
          <input type="hidden" id="ack-id" value="" />
          <div style="display:grid; grid-template-columns: 1fr 2fr; gap:12px; margin-bottom:10px;">
            <div>
              <label style="display:block; font-size:0.75rem; font-weight:700; margin-bottom:4px; color:var(--text-main);">Update Status</label>
              <select id="ack-status" class="enc-select" style="width:100%; padding:8px 10px; border-radius:6px; border:1px solid var(--border);">
                <option value="Received">Received (Pending Action)</option>
                <option value="Reviewed">Reviewed &amp; Scheduled</option>
                <option value="Completed">Completed (Janazah Done)</option>
              </select>
            </div>
            <div>
              <label style="display:block; font-size:0.75rem; font-weight:700; margin-bottom:4px; color:var(--text-main);">Da'wah Notes / Imam Remarks</label>
              <input type="text" id="ack-notes" placeholder="e.g. Salatul Janazah scheduled at ISCAG Masjid on Friday after Dhuhr" style="width:100%; padding:8px 12px; border-radius:6px; border:1px solid var(--border); font-size:0.82rem;" />
            </div>
          </div>
          <div style="display:flex; justify-content:flex-end;">
            <button type="submit" id="ack-btn" class="btn-topbar primary" style="padding:6px 16px; font-size:0.78rem;">
              Save Da'wah Status Update
            </button>
          </div>
        </form>
      </div>

    </div>

    <!-- Modal Footer -->
    <div style="flex:0 0 auto; padding:14px 24px; border-top:1px solid #e5e7eb; background:#fafbfc; display:flex; justify-content:space-between; align-items:center;">
      <a id="btn-open-file-doc" href="#" target="_blank" class="btn-action btn-view" style="background:#14532d; color:#fff; border-color:#14532d; font-weight:700; padding:8px 16px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
        <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Print Official Death Certificate (Form 103)
      </a>
      <button type="button" class="btn-topbar" onclick="closeDawahCertModal()">Close</button>
    </div>

  </div>
</div>
