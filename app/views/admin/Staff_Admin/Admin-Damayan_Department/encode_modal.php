<!-- ══════════════════════════════════════════════════════════════════
     DEATH CERTIFICATE ENCODING MODAL
     Minimal data entry for existing death certificates:
     1. Registration Number
     2. Full Name of Deceased
     3. Full Name of Representative
     Automatically captures: Staff Member & Encoding Date/Time
══════════════════════════════════════════════════════════════════ -->
<div id="encodeDeathCertModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:999999; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:18px; width:100%; max-width:540px; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden; animation:modalSlideUp 0.25s ease;">
    
    <!-- Modal Header -->
    <div style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:42px; height:42px; border-radius:12px; background:var(--damayan-light, #e8f5ed); color:var(--damayan-accent, #176b45); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:24px; height:24px; fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.1rem; font-weight:800; color:var(--damayan-dark, #0f5c3a);">Encode Death Certificate</h5>
          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Record existing civil registry certificate</div>
        </div>
      </div>
      <button type="button" class="close-btn" onclick="closeEncodeModal()" style="background:transparent; border:none; font-size:1.6rem; line-height:1; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>

    <!-- Modal Body -->
    <form id="encode-death-cert-form" onsubmit="event.preventDefault(); submitEncodeDeathCert();" style="margin:0;">
      <div style="padding:22px 24px;">
        
        <!-- Info Banner -->
        <div style="padding:10px 14px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; font-size:0.78rem; color:#1e40af; margin-bottom:18px; display:flex; align-items:flex-start; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:#2563eb; flex-shrink:0; margin-top:1px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
          <div>
            Record an existing death certificate in the database without encoding the entire certificate. Only the 3 fields below are required.
          </div>
        </div>

        <!-- Field 1: Registration Number -->
        <div class="form-group" style="margin-bottom:16px;">
          <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--text-main); margin-bottom:6px;">
            Death Certificate Registration Number <span style="color:#dc2626;">*</span>
          </label>
          <div style="position:relative;">
            <input type="text" id="enc-reg-no" required placeholder="e.g. 2026-0412 or DC-REG-2026-001" 
                   onblur="checkEncodeRegDuplicate(this.value)"
                   style="width:100%; padding:10px 14px; border-radius:8px; border:1.5px solid var(--border); font-size:0.88rem; font-weight:600; font-family:inherit; box-sizing:border-box; outline:none; transition:border 0.2s;" />
            <span id="enc-reg-status" style="position:absolute; right:12px; top:11px; display:none;"></span>
          </div>
          <div id="enc-reg-msg" style="font-size:0.75rem; margin-top:4px; display:none;"></div>
        </div>

        <!-- Field 2: Full Name of Deceased -->
        <div class="form-group" style="margin-bottom:16px;">
          <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--text-main); margin-bottom:6px;">
            Full Name of the Deceased <span style="color:#dc2626;">*</span>
          </label>
          <input type="text" id="enc-deceased-name" required placeholder="e.g. Juan Dela Cruz" 
                 style="width:100%; padding:10px 14px; border-radius:8px; border:1.5px solid var(--border); font-size:0.88rem; font-family:inherit; box-sizing:border-box; outline:none; transition:border 0.2s;" />
        </div>

        <!-- Field 3: Full Name of Representative -->
        <div class="form-group" style="margin-bottom:18px;">
          <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--text-main); margin-bottom:6px;">
            Full Name of the Representative <span style="color:#dc2626;">*</span>
          </label>
          <input type="text" id="enc-rep-name" required placeholder="e.g. Maria Dela Cruz" 
                 style="width:100%; padding:10px 14px; border-radius:8px; border:1.5px solid var(--border); font-size:0.88rem; font-family:inherit; box-sizing:border-box; outline:none; transition:border 0.2s;" />
        </div>

        <!-- Automatic Metadata Stamp -->
        <div style="background:#f8faf9; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; display:flex; justify-content:space-between; font-size:0.75rem; color:var(--text-muted);">
          <div>
            Encoded By: <strong style="color:var(--damayan-dark);"><?= htmlspecialchars(trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: ($_SESSION['name'] ?? 'Staff Admin')) ?></strong>
          </div>
          <div>
            Date: <strong style="color:var(--damayan-dark);"><?= date('M d, Y') ?></strong> (Auto-recorded)
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div style="padding:14px 24px; border-top:1px solid var(--border); background:#fafbfc; display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn-topbar" onclick="closeEncodeModal()" style="padding:8px 18px; font-size:0.84rem; cursor:pointer;">Cancel</button>
        <button type="submit" id="enc-submit-btn" class="btn-topbar primary" style="padding:8px 22px; font-size:0.84rem; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
          <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Save &amp; Record
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════
     VIEW ENCODED CERTIFICATE DETAILS MODAL
══════════════════════════════════════════════════════════════════ -->
<div id="viewEncodedDetailModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:999999; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:18px; width:100%; max-width:540px; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden; animation:modalSlideUp 0.25s ease;">
    <div style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:10px; background:var(--damayan-light); color:var(--damayan-accent); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:currentColor;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.05rem; font-weight:800; color:var(--damayan-dark);">Encoded Death Certificate</h5>
          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Civil Registry Record Details</div>
        </div>
      </div>
      <button type="button" class="close-btn" onclick="closeViewEncodedModal()" style="background:transparent; border:none; font-size:1.6rem; line-height:1; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>
    
    <div style="padding:22px 24px;">
      <div style="background:#f8faf9; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:16px;">
        <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.04em;">Registration Number</div>
        <div id="v-enc-reg" style="font-size:1.25rem; font-weight:800; color:var(--damayan-accent); margin-top:4px;">—</div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Deceased Full Name</div>
          <div id="v-enc-deceased" style="font-size:0.95rem; font-weight:700; color:var(--text-main); margin-top:4px;">—</div>
        </div>
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Representative Name</div>
          <div id="v-enc-rep" style="font-size:0.95rem; font-weight:700; color:var(--text-main); margin-top:4px;">—</div>
        </div>
      </div>

      <div style="background:#fafbfc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; font-size:0.8rem;">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:var(--text-muted);">Encoded By:</span>
          <strong id="v-enc-staff" style="color:var(--damayan-dark);">—</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:var(--text-muted);">Encoded Date &amp; Time:</span>
          <strong id="v-enc-date" style="color:#1f2937;">—</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:var(--text-muted);">Record Status:</span>
          <span class="badge-status badge-active" style="font-size:0.7rem;">Verified Recorded</span>
        </div>
      </div>
    </div>

    <div style="padding:14px 24px; border-top:1px solid var(--border); background:#fafbfc; display:flex; justify-content:flex-end;">
      <button type="button" class="btn-topbar" onclick="closeViewEncodedModal()">Close</button>
    </div>
  </div>
</div>

<script>
  function openEncodeModal() {
    document.getElementById('encode-death-cert-form').reset();
    document.getElementById('enc-reg-msg').style.display = 'none';
    document.getElementById('enc-reg-no').style.borderColor = 'var(--border)';
    document.getElementById('encodeDeathCertModal').style.display = 'flex';
    setTimeout(() => document.getElementById('enc-reg-no').focus(), 100);
  }

  function closeEncodeModal() {
    document.getElementById('encodeDeathCertModal').style.display = 'none';
  }

  function closeViewEncodedModal() {
    document.getElementById('viewEncodedDetailModal').style.display = 'none';
  }

  async function checkEncodeRegDuplicate(regNo) {
    regNo = regNo.trim();
    const msgEl = document.getElementById('enc-reg-msg');
    const inputEl = document.getElementById('enc-reg-no');
    if(!regNo) {
      msgEl.style.display = 'none';
      inputEl.style.borderColor = 'var(--border)';
      return false;
    }

    try {
      const res = await fetch('<?= url('/admin/damayan/encoded-certificates/check-reg') ?>?reg=' + encodeURIComponent(regNo));
      const data = await res.json();
      if(data.exists) {
        msgEl.style.display = 'block';
        msgEl.style.color = '#dc2626';
        msgEl.innerHTML = `<strong>Duplicate:</strong> ${escapeHtml(data.message)}`;
        inputEl.style.borderColor = '#dc2626';
        return true;
      } else {
        msgEl.style.display = 'block';
        msgEl.style.color = '#15803d';
        msgEl.innerHTML = `<span style="display:inline-flex;align-items:center;gap:4px;">&#10003; Registration number is available and valid.</span>`;
        inputEl.style.borderColor = '#15803d';
        return false;
      }
    } catch(e) {
      return false;
    }
  }

  async function submitEncodeDeathCert() {
    const regNo = document.getElementById('enc-reg-no').value.trim();
    const decName = document.getElementById('enc-deceased-name').value.trim();
    const repName = document.getElementById('enc-rep-name').value.trim();
    const submitBtn = document.getElementById('enc-submit-btn');

    if(!regNo) {
      showAlert('Required Field', 'Please enter the Death Certificate Registration Number.', 'warning');
      document.getElementById('enc-reg-no').focus();
      return;
    }
    if(!decName) {
      showAlert('Required Field', 'Please enter the Full Name of the Deceased.', 'warning');
      document.getElementById('enc-deceased-name').focus();
      return;
    }
    if(!repName) {
      showAlert('Required Field', 'Please enter the Full Name of the Representative.', 'warning');
      document.getElementById('enc-rep-name').focus();
      return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Saving...';

    try {
      const res = await fetch('<?= url('/admin/damayan/encoded-certificates/save') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          registration_number: regNo,
          deceased_name: decName,
          representative_name: repName
        })
      });

      const data = await res.json();
      if(data.success) {
        showAlert('Certificate Encoded', data.message || 'Death certificate has been successfully encoded and recorded.', 'success');
        closeEncodeModal();
        setTimeout(() => location.reload(), 1200);
      } else {
        showAlert('Encoding Error', data.error || 'Failed to save encoded certificate.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Save &amp; Record';
      }
    } catch(e) {
      console.error(e);
      showAlert('Error', 'An error occurred while saving the encoded certificate.', 'error');
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Save &amp; Record';
    }
  }

  async function openEncodedRecordDetails(id) {
    try {
      const res = await fetch('<?= url('/admin/damayan/encoded-certificates/detail') ?>?id=' + encodeURIComponent(id));
      const data = await res.json();
      if(!data.success || !data.record) {
        showAlert('Error', data.error || 'Failed to retrieve encoded certificate details.', 'error');
        return;
      }

      const r = data.record;
      document.getElementById('v-enc-reg').textContent = '#' + r.registration_number;
      document.getElementById('v-enc-deceased').textContent = r.deceased_name;
      document.getElementById('v-enc-rep').textContent = r.representative_name;
      document.getElementById('v-enc-staff').textContent = r.encoded_by_name || 'Staff Admin';
      
      const d = new Date(r.encoded_at);
      document.getElementById('v-enc-date').textContent = isNaN(d) ? r.encoded_at : d.toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true
      });

      document.getElementById('viewEncodedDetailModal').style.display = 'flex';
    } catch(e) {
      showAlert('Error', 'Failed to retrieve record.', 'error');
    }
  }

  // Escape key & backdrop dismissal
  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape') {
      const m1 = document.getElementById('encodeDeathCertModal');
      if(m1 && m1.style.display === 'flex') closeEncodeModal();
      const m2 = document.getElementById('viewEncodedDetailModal');
      if(m2 && m2.style.display === 'flex') closeViewEncodedModal();
    }
  });

  document.getElementById('encodeDeathCertModal')?.addEventListener('click', function(e) {
    if(e.target === this) closeEncodeModal();
  });
  document.getElementById('viewEncodedDetailModal')?.addEventListener('click', function(e) {
    if(e.target === this) closeViewEncodedModal();
  });
</script>
