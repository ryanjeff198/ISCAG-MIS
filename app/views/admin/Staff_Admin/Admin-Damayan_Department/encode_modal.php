<!-- ══════════════════════════════════════════════════════════════════
     DEATH CERTIFICATE REGISTRATION / ENCODING MODAL
     Complete Civil Registry Form 103 entry:
     1. Registration Information: Province, City/Municipality, Registry Number
     2. Information About the Deceased:
        - Names: First Name, Middle Name, Last Name
        - Sex (Male / Female)
        - Date of Death (Day, Month, Year)
        - Date of Birth (Day, Month, Year)
        - Age at Time of Death (Years, Months, Days, Hours)
        - Place of Death (Hospital/Clinic/Institution/Residence, House No, Street, Brgy, City, Province)
        - Civil Status
        - Religion / Religious Sect
        - Citizenship
        - Residence (House No, Street, Brgy, City, Province, Country)
        - Occupation
     3. Automatically captures Staff & Timestamp
══════════════════════════════════════════════════════════════════ -->

<style>
  .enc-section {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px 18px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
  .enc-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1.5px solid #f0fdf4;
  }
  .enc-section-title {
    font-size: 0.88rem;
    font-weight: 800;
    color: var(--damayan-dark, #0f5c3a);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin: 0;
  }
  .enc-section-title svg {
    width: 18px;
    height: 18px;
    fill: var(--damayan-accent, #176b45);
  }
  .enc-label {
    display: block;
    font-size: 0.76rem;
    font-weight: 700;
    color: var(--text-main, #374151);
    margin-bottom: 5px;
    letter-spacing: 0.01em;
  }
  .enc-input, .enc-select {
    width: 100%;
    padding: 9px 12px;
    border-radius: 8px;
    border: 1.5px solid var(--border, #d1d5db);
    font-size: 0.85rem;
    font-family: inherit;
    box-sizing: border-box;
    outline: none;
    background: #ffffff;
    color: #111827;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .enc-input:focus, .enc-select:focus {
    border-color: var(--damayan-accent, #176b45);
    box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.12);
  }
  .enc-sub-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 12px 14px;
    margin-top: 10px;
  }
  .enc-radio-group {
    display: flex;
    gap: 10px;
  }
  .enc-radio-pill {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    border: 1.5px solid var(--border, #d1d5db);
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.84rem;
    font-weight: 700;
    color: var(--text-muted, #6b7280);
    background: #fff;
    transition: all 0.2s;
    user-select: none;
  }
  .enc-radio-pill input[type="radio"] {
    accent-color: var(--damayan-accent, #176b45);
    margin: 0;
  }
  .enc-radio-pill:has(input:checked) {
    border-color: var(--damayan-accent, #176b45);
    background: var(--damayan-light, #e8f5ed);
    color: var(--damayan-dark, #0f5c3a);
  }
</style>

<div id="encodeDeathCertModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:999999; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:18px; width:100%; max-width:840px; max-height:92vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideUp 0.25s ease;">
    
    <!-- Modal Header -->
    <div style="flex:0 0 auto; padding:18px 24px; border-bottom:1px solid var(--border, #e5e7eb); display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:42px; height:42px; border-radius:12px; background:var(--damayan-light, #e8f5ed); color:var(--damayan-accent, #176b45); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:24px; height:24px; fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--damayan-dark, #0f5c3a);">Register Death Certificate</h5>
          <div style="font-size:0.75rem; color:var(--text-muted, #6b7280); margin-top:2px;">Civil Registry (Form 103) Record Entry</div>
        </div>
      </div>
      <button type="button" class="close-btn" onclick="closeEncodeModal()" style="background:transparent; border:none; font-size:1.6rem; line-height:1; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>

    <!-- Modal Form Body (Scrollable) -->
    <form id="encode-death-cert-form" onsubmit="event.preventDefault(); submitEncodeDeathCert();" style="margin:0; display:flex; flex-direction:column; flex:1 1 auto; overflow:hidden;">
      <div style="padding:22px 26px; overflow-y:auto; flex:1 1 auto; max-height:calc(92vh - 135px);">
        
        <!-- Info Banner -->
        <div style="padding:10px 14px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; font-size:0.78rem; color:#1e40af; margin-bottom:16px; display:flex; align-items:flex-start; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:#2563eb; flex-shrink:0; margin-top:1px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
          <div>
            Record an official Certificate of Death into the registry. Complete the civil registration information and details of the deceased below.
          </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════
             SECTION 1: REGISTRATION INFORMATION
             ══════════════════════════════════════════════════════════ -->
        <div class="enc-section">
          <div class="enc-section-header">
            <h6 class="enc-section-title">
              <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
              Registration Information
            </h6>
            <span style="font-size:0.72rem; color:var(--text-muted); font-weight:600;">Office of the Civil Registrar</span>
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
            <!-- Province -->
            <div>
              <label class="enc-label">Province <span style="color:#dc2626;">*</span></label>
              <input type="text" id="enc-province" class="enc-input" required placeholder="e.g. Cavite" value="Cavite" />
            </div>

            <!-- City / Municipality -->
            <div>
              <label class="enc-label">City / Municipality <span style="color:#dc2626;">*</span></label>
              <input type="text" id="enc-city-municipality" class="enc-input" required placeholder="e.g. City of Dasmariñas" value="City of Dasmariñas" />
            </div>

            <!-- Registry Number -->
            <div>
              <label class="enc-label">Registry Number <span style="color:#dc2626;">*</span></label>
              <div style="position:relative;">
                <input type="text" id="enc-reg-no" class="enc-input" required placeholder="e.g. 2026-0412 or DC-2026-001" 
                       onblur="checkEncodeRegDuplicate(this.value)" style="font-weight:700; color:var(--damayan-dark);" />
              </div>
              <div id="enc-reg-msg" style="font-size:0.74rem; margin-top:4px; display:none;"></div>
            </div>
          </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════
             SECTION 2: INFORMATION ABOUT THE DECEASED
             ══════════════════════════════════════════════════════════ -->
        <div class="enc-section">
          <div class="enc-section-header">
            <h6 class="enc-section-title">
              <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
              Information About the Deceased
            </h6>
            <span style="font-size:0.72rem; color:var(--text-muted); font-weight:600;">Personal &amp; Vital Statistics</span>
          </div>

          <!-- Name: First, Middle, Last -->
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap:12px; margin-bottom:14px;">
            <div>
              <label class="enc-label">First Name <span style="color:#dc2626;">*</span></label>
              <input type="text" id="enc-first-name" class="enc-input" required placeholder="e.g. Juan" />
            </div>
            <div>
              <label class="enc-label">Middle Name</label>
              <input type="text" id="enc-middle-name" class="enc-input" placeholder="e.g. Santos (leave blank if none)" />
            </div>
            <div>
              <label class="enc-label">Last Name <span style="color:#dc2626;">*</span></label>
              <input type="text" id="enc-last-name" class="enc-input" required placeholder="e.g. Dela Cruz" />
            </div>
          </div>

          <!-- Sex, Civil Status, Religion, Citizenship -->
          <div style="display:grid; grid-template-columns: 1.2fr 1fr 1fr 1fr; gap:12px; margin-bottom:14px;">
            <!-- Sex -->
            <div>
              <label class="enc-label">Sex <span style="color:#dc2626;">*</span></label>
              <div class="enc-radio-group">
                <label class="enc-radio-pill">
                  <input type="radio" name="enc_sex" value="Male" checked />
                  <span>Male</span>
                </label>
                <label class="enc-radio-pill">
                  <input type="radio" name="enc_sex" value="Female" />
                  <span>Female</span>
                </label>
              </div>
            </div>

            <!-- Civil Status -->
            <div>
              <label class="enc-label">Civil Status</label>
              <select id="enc-civil-status" class="enc-select">
                <option value="Single">Single</option>
                <option value="Married" selected>Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Divorced">Divorced</option>
                <option value="Annulled">Annulled</option>
              </select>
            </div>

            <!-- Religion / Religious Sect -->
            <div>
              <label class="enc-label">Religion / Religious Sect</label>
              <input type="text" id="enc-religion" class="enc-input" value="Islam" readonly tabindex="-1" style="background:#f3f4f6; color:#374151; cursor:not-allowed; pointer-events:none; font-weight:600;" title="Default to Islam" />
            </div>

            <!-- Citizenship -->
            <div>
              <label class="enc-label">Citizenship</label>
              <input type="text" id="enc-citizenship" class="enc-input" placeholder="e.g. Filipino" value="Filipino" />
            </div>
          </div>

          <!-- Dates: Date of Death & Date of Birth -->
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
            
            <!-- Date of Death -->
            <div class="enc-sub-card">
              <label class="enc-label" style="color:var(--damayan-dark); margin-bottom:8px;">
                Date of Death <span style="color:#dc2626;">*</span>
              </label>
              <div style="display:grid; grid-template-columns: 1fr 1.5fr 1.1fr; gap:8px;">
                <div>
                  <select id="enc-dod-day" class="enc-select" required onchange="calculateAutoAge()">
                    <option value="">Day</option>
                    <?php for($d=1; $d<=31; $d++): ?>
                      <option value="<?= $d ?>" <?= ($d == (int)date('j')) ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
                <div>
                  <select id="enc-dod-month" class="enc-select" required onchange="calculateAutoAge()">
                    <option value="">Month</option>
                    <?php 
                      $months = [1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December'];
                      $curMonth = (int)date('n');
                      foreach($months as $num => $mName): 
                    ?>
                      <option value="<?= $num ?>" <?= ($num == $curMonth) ? 'selected' : '' ?>><?= $mName ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                  <select id="enc-dod-year" class="enc-select" required onchange="calculateAutoAge()">
                    <option value="">Year</option>
                    <?php 
                      $curYear = (int)date('Y');
                      for($y = $curYear + 1; $y >= 1920; $y--): 
                    ?>
                      <option value="<?= $y ?>" <?= ($y == $curYear) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- Date of Birth -->
            <div class="enc-sub-card">
              <label class="enc-label" style="color:var(--damayan-dark); margin-bottom:8px;">
                Date of Birth
              </label>
              <div style="display:grid; grid-template-columns: 1fr 1.5fr 1.1fr; gap:8px;">
                <div>
                  <select id="enc-dob-day" class="enc-select" onchange="calculateAutoAge()">
                    <option value="">Day</option>
                    <?php for($d=1; $d<=31; $d++): ?>
                      <option value="<?= $d ?>"><?= $d ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
                <div>
                  <select id="enc-dob-month" class="enc-select" onchange="calculateAutoAge()">
                    <option value="">Month</option>
                    <?php foreach($months as $num => $mName): ?>
                      <option value="<?= $num ?>"><?= $mName ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                  <select id="enc-dob-year" class="enc-select" onchange="calculateAutoAge()">
                    <option value="">Year</option>
                    <?php 
                      $curYear = (int)date('Y');
                      for($y = $curYear; $y >= 1900; $y--): 
                    ?>
                      <option value="<?= $y ?>"><?= $y ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
              </div>
            </div>

          </div>

          <!-- Age at the Time of Death (Supports Years, Months, Days, or Hours) -->
          <div class="enc-sub-card" style="margin-bottom:14px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
              <label class="enc-label" style="margin:0; color:var(--damayan-dark);">
                Age at the Time of Death
              </label>
              <div style="font-size:0.72rem; color:var(--text-muted);">
                Applicable completed years, months, days, or hours (Form 103 Item 4)
              </div>
            </div>
            <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:10px;">
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Years (if &ge; 1 yr)</label>
                <input type="number" id="enc-age-years" class="enc-input" min="0" placeholder="e.g. 68" oninput="updateAgeSummary()" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Months (if &lt; 1 yr)</label>
                <input type="number" id="enc-age-months" class="enc-input" min="0" max="11" placeholder="e.g. 6" oninput="updateAgeSummary()" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Days (if &lt; 1 mo)</label>
                <input type="number" id="enc-age-days" class="enc-input" min="0" max="30" placeholder="e.g. 15" oninput="updateAgeSummary()" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Hours (if &lt; 1 day)</label>
                <input type="number" id="enc-age-hours" class="enc-input" min="0" max="23" placeholder="e.g. 12" oninput="updateAgeSummary()" />
              </div>
            </div>
            <div id="enc-age-summary" style="font-size:0.74rem; color:var(--damayan-dark); margin-top:6px; font-weight:700;"></div>
          </div>

          <!-- Occupation -->
          <div style="margin-bottom:14px;">
            <label class="enc-label">Occupation</label>
            <input type="text" id="enc-occupation" class="enc-input" placeholder="e.g. Government Employee, Businessman, Teacher, Retired, None" />
          </div>

          <!-- Place of Death -->
          <div class="enc-sub-card" style="margin-bottom:14px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
              <label class="enc-label" style="margin:0; color:var(--damayan-dark);">
                Place of Death
              </label>
              <span style="font-size:0.72rem; color:var(--text-muted);">Hospital, Clinic, Institution, or Residence</span>
            </div>

            <div style="margin-bottom:10px;">
              <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Name of Hospital, Clinic, Institution, or Residence</label>
              <input type="text" id="enc-pod-hospital" class="enc-input" placeholder="e.g. De La Salle University Medical Center (or 'Residence')" />
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1.5fr 1fr; gap:10px; margin-bottom:10px;">
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">House No.</label>
                <input type="text" id="enc-pod-house-no" class="enc-input" placeholder="e.g. Blk 10 Lot 5" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Street</label>
                <input type="text" id="enc-pod-street" class="enc-input" placeholder="e.g. Governor's Drive" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Barangay</label>
                <input type="text" id="enc-pod-barangay" class="enc-input" placeholder="e.g. Brgy. Burol I" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">City / Municipality</label>
                <input type="text" id="enc-pod-city" class="enc-input" placeholder="City of Dasmariñas" value="City of Dasmariñas" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Province</label>
                <input type="text" id="enc-pod-province" class="enc-input" placeholder="Cavite" value="Cavite" />
              </div>
            </div>
          </div>

          <!-- Residence -->
          <div class="enc-sub-card" style="margin-bottom:14px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
              <label class="enc-label" style="margin:0; color:var(--damayan-dark);">
                Residence
              </label>
              <button type="button" onclick="copyPodToResidence()" style="background:#f0fdf4; border:1px solid #bbf7d0; color:var(--damayan-dark); padding:3px 10px; border-radius:6px; font-size:0.72rem; font-weight:700; cursor:pointer;">
                &#8618; Same as Place of Death Address
              </button>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1.5fr 1fr; gap:10px; margin-bottom:10px;">
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">House No.</label>
                <input type="text" id="enc-res-house-no" class="enc-input" placeholder="e.g. House #142" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Street</label>
                <input type="text" id="enc-res-street" class="enc-input" placeholder="e.g. Sampaguita St." />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Barangay</label>
                <input type="text" id="enc-res-barangay" class="enc-input" placeholder="e.g. Brgy. Zone 4" />
              </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px;">
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">City / Municipality</label>
                <input type="text" id="enc-res-city" class="enc-input" placeholder="City of Dasmariñas" value="City of Dasmariñas" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Province</label>
                <input type="text" id="enc-res-province" class="enc-input" placeholder="Cavite" value="Cavite" />
              </div>
              <div>
                <label style="font-size:0.72rem; color:var(--text-muted); display:block; margin-bottom:4px; font-weight:600;">Country</label>
                <input type="text" id="enc-res-country" class="enc-input" placeholder="Philippines" value="Philippines" />
              </div>
            </div>
          </div>

          <!-- Representative / Informant (Optional) -->
          <div>
            <label class="enc-label">Representative / Informant Name (Optional)</label>
            <input type="text" id="enc-rep-name" class="enc-input" placeholder="e.g. Maria Dela Cruz (Spouse / Nearest Kin)" />
          </div>

        </div>

        <!-- Automatic Metadata Stamp -->
        <div style="background:#f8faf9; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; display:flex; justify-content:space-between; font-size:0.75rem; color:var(--text-muted);">
          <div>
            Registered By: <strong style="color:var(--damayan-dark);"><?= htmlspecialchars(trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: ($_SESSION['name'] ?? 'Staff Admin')) ?></strong>
          </div>
          <div>
            Date &amp; Time: <strong style="color:var(--damayan-dark);"><?= date('M d, Y') ?></strong> (Auto-recorded)
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div style="flex:0 0 auto; padding:14px 24px; border-top:1px solid var(--border, #e5e7eb); background:#fafbfc; display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn-topbar" onclick="closeEncodeModal()" style="padding:8px 18px; font-size:0.84rem; cursor:pointer;">Cancel</button>
        <button type="submit" id="enc-submit-btn" class="btn-topbar primary" style="padding:8px 24px; font-size:0.84rem; display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-weight:700;">
          <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Save &amp; Record Certificate
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════
     VIEW ENCODED CERTIFICATE DETAILS MODAL
     Displays complete registered civil registry record
══════════════════════════════════════════════════════════════════ -->
<div id="viewEncodedDetailModal" class="mis-modal" style="display:none; position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:999999; align-items:center; justify-content:center; padding:16px;">
  <div class="mis-modal-content" style="background:#fff; border-radius:18px; width:100%; max-width:760px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideUp 0.25s ease;">
    
    <div style="flex:0 0 auto; padding:18px 24px; border-bottom:1px solid var(--border, #e5e7eb); display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:38px; height:38px; border-radius:10px; background:var(--damayan-light); color:var(--damayan-accent); display:flex; align-items:center; justify-content:center;">
          <svg viewBox="0 0 24 24" style="width:22px; height:22px; fill:currentColor;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        </div>
        <div>
          <h5 style="margin:0; font-size:1.1rem; font-weight:800; color:var(--damayan-dark);">Registered Death Certificate</h5>
          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Civil Registry (Form 103) Official Record</div>
        </div>
      </div>
      <button type="button" class="close-btn" onclick="closeViewEncodedModal()" style="background:transparent; border:none; font-size:1.6rem; line-height:1; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>
    
    <div style="padding:22px 24px; overflow-y:auto; flex:1 1 auto;">
      
      <!-- Registry Banner -->
      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
          <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.04em;">Registry Number</div>
          <div id="v-enc-reg" style="font-size:1.4rem; font-weight:800; color:var(--damayan-accent); margin-top:2px;">—</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Civil Registrar Location</div>
          <div id="v-enc-location" style="font-size:0.92rem; font-weight:700; color:var(--text-main); margin-top:2px;">—</div>
        </div>
      </div>

      <!-- Deceased Info Grid -->
      <div style="display:grid; grid-template-columns: 2fr 1fr; gap:12px; margin-bottom:14px;">
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Deceased Full Name</div>
          <div id="v-enc-deceased" style="font-size:1.05rem; font-weight:800; color:var(--text-main); margin-top:4px;">—</div>
        </div>
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Sex / Civil Status</div>
          <div id="v-enc-sex-status" style="font-size:0.92rem; font-weight:700; color:var(--text-main); margin-top:4px;">—</div>
        </div>
      </div>

      <!-- Vital Statistics -->
      <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; margin-bottom:14px;">
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Date of Death</div>
          <div id="v-enc-dod" style="font-size:0.92rem; font-weight:700; color:var(--text-main); margin-top:4px;">—</div>
        </div>
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Date of Birth</div>
          <div id="v-enc-dob" style="font-size:0.92rem; font-weight:700; color:var(--text-main); margin-top:4px;">—</div>
        </div>
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Age at Death</div>
          <div id="v-enc-age" style="font-size:0.92rem; font-weight:700; color:var(--damayan-dark); margin-top:4px;">—</div>
        </div>
      </div>

      <!-- Religion, Citizenship, Occupation -->
      <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; margin-bottom:14px;">
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Religion / Sect</div>
          <div id="v-enc-religion" style="font-size:0.9rem; font-weight:600; color:var(--text-main); margin-top:4px;">—</div>
        </div>
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Citizenship</div>
          <div id="v-enc-citizenship" style="font-size:0.9rem; font-weight:600; color:var(--text-main); margin-top:4px;">—</div>
        </div>
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Occupation</div>
          <div id="v-enc-occupation" style="font-size:0.9rem; font-weight:600; color:var(--text-main); margin-top:4px;">—</div>
        </div>
      </div>

      <!-- Addresses: Place of Death & Residence -->
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:14px;">
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Place of Death</div>
          <div id="v-enc-pod" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-top:4px; line-height:1.4;">—</div>
        </div>
        <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px 14px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Residence</div>
          <div id="v-enc-residence" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-top:4px; line-height:1.4;">—</div>
        </div>
      </div>

      <!-- Informant / Metadata Card -->
      <div style="background:#fafbfc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; font-size:0.8rem;">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:var(--text-muted);">Representative / Informant:</span>
          <strong id="v-enc-rep" style="color:#1f2937;">—</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:var(--text-muted);">Registered By Staff:</span>
          <strong id="v-enc-staff" style="color:var(--damayan-dark);">—</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:var(--text-muted);">Registration Date &amp; Time:</span>
          <strong id="v-enc-date" style="color:#1f2937;">—</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:var(--text-muted);">Record Status:</span>
          <span class="badge-status badge-active" style="font-size:0.7rem;">Verified Recorded</span>
        </div>
      </div>

    </div>

    <div style="flex:0 0 auto; padding:14px 24px; border-top:1px solid var(--border); background:#fafbfc; display:flex; justify-content:flex-end;">
      <button type="button" class="btn-topbar" onclick="closeViewEncodedModal()">Close</button>
    </div>
  </div>
</div>

<script>
  function openEncodeModal() {
    const form = document.getElementById('encode-death-cert-form');
    if (form) form.reset();

    // Default province and city
    document.getElementById('enc-province').value = 'Cavite';
    document.getElementById('enc-city-municipality').value = 'City of Dasmariñas';
    document.getElementById('enc-pod-city').value = 'City of Dasmariñas';
    document.getElementById('enc-pod-province').value = 'Cavite';
    document.getElementById('enc-res-city').value = 'City of Dasmariñas';
    document.getElementById('enc-res-province').value = 'Cavite';
    document.getElementById('enc-res-country').value = 'Philippines';
    document.getElementById('enc-citizenship').value = 'Filipino';
    document.getElementById('enc-religion').value = 'Islam';

    // Default current date and time for DOD
    const now = new Date();
    document.getElementById('enc-dod-day').value = now.getDate();
    document.getElementById('enc-dod-month').value = now.getMonth() + 1;
    document.getElementById('enc-dod-year').value = now.getFullYear();

    document.getElementById('enc-reg-msg').style.display = 'none';
    document.getElementById('enc-reg-no').style.borderColor = 'var(--border)';
    document.getElementById('enc-age-summary').textContent = '';

    document.getElementById('encodeDeathCertModal').style.display = 'flex';
    setTimeout(() => document.getElementById('enc-reg-no').focus(), 100);
  }

  function closeEncodeModal() {
    document.getElementById('encodeDeathCertModal').style.display = 'none';
  }

  function closeViewEncodedModal() {
    document.getElementById('viewEncodedDetailModal').style.display = 'none';
  }

  // 100% Accurate Calendar Age Calculation from DOB & DOD
  function calculateAutoAge() {
    const dYear = parseInt(document.getElementById('enc-dod-year').value, 10);
    const dMonth = parseInt(document.getElementById('enc-dod-month').value, 10);
    const dDay = parseInt(document.getElementById('enc-dod-day').value, 10);

    const bYear = parseInt(document.getElementById('enc-dob-year').value, 10);
    const bMonth = parseInt(document.getElementById('enc-dob-month').value, 10);
    const bDay = parseInt(document.getElementById('enc-dob-day').value, 10);

    const summaryEl = document.getElementById('enc-age-summary');

    if (!dYear || !dMonth || !dDay || !bYear || !bMonth || !bDay) {
      updateAgeSummary();
      return;
    }

    const birthDate = new Date(bYear, bMonth - 1, bDay);
    const deathDate = new Date(dYear, dMonth - 1, dDay);

    if (deathDate < birthDate) {
      summaryEl.innerHTML = '<span style="color:#dc2626;">⚠ Date of Death cannot be earlier than Date of Birth.</span>';
      document.getElementById('enc-age-years').value = '';
      document.getElementById('enc-age-months').value = '';
      document.getElementById('enc-age-days').value = '';
      document.getElementById('enc-age-hours').value = '';
      return;
    }

    // 1. Calculate Completed Years
    let years = dYear - bYear;
    let hadBirthday = (dMonth > bMonth) || (dMonth === bMonth && dDay >= bDay);
    if (!hadBirthday) {
      years--;
    }

    // 2. Calculate Remaining Months
    let months = dMonth - bMonth;
    if (dDay < bDay) {
      months--;
    }
    if (months < 0) {
      months += 12;
    }

    // 3. Calculate Remaining Days
    let days = 0;
    if (dDay >= bDay) {
      days = dDay - bDay;
    } else {
      // Days in previous month before death date
      const daysInPrevMonth = new Date(dYear, dMonth - 1, 0).getDate();
      days = (daysInPrevMonth - bDay) + dDay;
    }

    // Populate according to PSA Civil Registry Form 103 (Item 4):
    // a. 1 year or above -> completed years
    // b. Under 1 year -> months, days
    if (years >= 1) {
      document.getElementById('enc-age-years').value = years;
      document.getElementById('enc-age-months').value = months;
      document.getElementById('enc-age-days').value = days;
      document.getElementById('enc-age-hours').value = '';
      summaryEl.innerHTML = `<span style="color:var(--damayan-dark);">&#10003; <strong>Accurate Age:</strong> ${years} completed year${years === 1 ? '' : 's'} (${years} yrs, ${months} mos, ${days} days)</span>`;
    } else if (months >= 1) {
      document.getElementById('enc-age-years').value = 0;
      document.getElementById('enc-age-months').value = months;
      document.getElementById('enc-age-days').value = days;
      document.getElementById('enc-age-hours').value = '';
      summaryEl.innerHTML = `<span style="color:var(--damayan-dark);">&#10003; <strong>Accurate Infant Age:</strong> ${months} month${months === 1 ? '' : 's'} and ${days} day${days === 1 ? '' : 's'}</span>`;
    } else {
      document.getElementById('enc-age-years').value = 0;
      document.getElementById('enc-age-months').value = 0;
      document.getElementById('enc-age-days').value = days;
      document.getElementById('enc-age-hours').value = '';
      summaryEl.innerHTML = `<span style="color:var(--damayan-dark);">&#10003; <strong>Accurate Infant Age:</strong> ${days} day${days === 1 ? '' : 's'} old</span>`;
    }
  }

  function updateAgeSummary() {
    const y = document.getElementById('enc-age-years').value.trim();
    const m = document.getElementById('enc-age-months').value.trim();
    const d = document.getElementById('enc-age-days').value.trim();
    const h = document.getElementById('enc-age-hours').value.trim();
    const summaryEl = document.getElementById('enc-age-summary');

    const parts = [];
    if (y !== '' && parseInt(y, 10) > 0) parts.push(`${y} completed year(s)`);
    if (m !== '' && parseInt(m, 10) > 0) parts.push(`${m} month(s)`);
    if (d !== '' && parseInt(d, 10) > 0) parts.push(`${d} day(s)`);
    if (h !== '' && parseInt(h, 10) > 0) parts.push(`${h} hour(s)`);

    if (parts.length > 0) {
      summaryEl.innerHTML = `<span style="color:var(--damayan-dark);">&#10003; <strong>Recorded Age:</strong> ${parts.join(', ')}</span>`;
    } else {
      summaryEl.textContent = '';
    }
  }

  // Copy Place of Death Address into Residence
  function copyPodToResidence() {
    document.getElementById('enc-res-house-no').value = document.getElementById('enc-pod-house-no').value;
    document.getElementById('enc-res-street').value = document.getElementById('enc-pod-street').value;
    document.getElementById('enc-res-barangay').value = document.getElementById('enc-pod-barangay').value;
    document.getElementById('enc-res-city').value = document.getElementById('enc-pod-city').value || 'City of Dasmariñas';
    document.getElementById('enc-res-province').value = document.getElementById('enc-pod-province').value || 'Cavite';
    document.getElementById('enc-res-country').value = 'Philippines';
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
        msgEl.innerHTML = `<span style="display:inline-flex;align-items:center;gap:4px;">&#10003; Registry number is available and valid.</span>`;
        inputEl.style.borderColor = '#15803d';
        return false;
      }
    } catch(e) {
      return false;
    }
  }

  async function submitEncodeDeathCert() {
    const province = document.getElementById('enc-province').value.trim();
    const cityMunicipality = document.getElementById('enc-city-municipality').value.trim();
    const regNo = document.getElementById('enc-reg-no').value.trim();

    const firstName = document.getElementById('enc-first-name').value.trim();
    const middleName = document.getElementById('enc-middle-name').value.trim();
    const lastName = document.getElementById('enc-last-name').value.trim();
    const sex = document.querySelector('input[name="enc_sex"]:checked')?.value || 'Male';

    const dodDay = document.getElementById('enc-dod-day').value;
    const dodMonth = document.getElementById('enc-dod-month').value;
    const dodYear = document.getElementById('enc-dod-year').value.trim();

    const dobDay = document.getElementById('enc-dob-day').value;
    const dobMonth = document.getElementById('enc-dob-month').value;
    const dobYear = document.getElementById('enc-dob-year').value.trim();

    const ageYears = document.getElementById('enc-age-years').value.trim();
    const ageMonths = document.getElementById('enc-age-months').value.trim();
    const ageDays = document.getElementById('enc-age-days').value.trim();
    const ageHours = document.getElementById('enc-age-hours').value.trim();

    const podHospital = document.getElementById('enc-pod-hospital').value.trim();
    const podHouseNo = document.getElementById('enc-pod-house-no').value.trim();
    const podStreet = document.getElementById('enc-pod-street').value.trim();
    const podBarangay = document.getElementById('enc-pod-barangay').value.trim();
    const podCity = document.getElementById('enc-pod-city').value.trim();
    const podProvince = document.getElementById('enc-pod-province').value.trim();

    const civilStatus = document.getElementById('enc-civil-status').value;
    const religion = document.getElementById('enc-religion').value.trim();
    const citizenship = document.getElementById('enc-citizenship').value.trim();

    const resHouseNo = document.getElementById('enc-res-house-no').value.trim();
    const resStreet = document.getElementById('enc-res-street').value.trim();
    const resBarangay = document.getElementById('enc-res-barangay').value.trim();
    const resCity = document.getElementById('enc-res-city').value.trim();
    const resProvince = document.getElementById('enc-res-province').value.trim();
    const resCountry = document.getElementById('enc-res-country').value.trim();

    const occupation = document.getElementById('enc-occupation').value.trim();
    const repName = document.getElementById('enc-rep-name').value.trim();

    const submitBtn = document.getElementById('enc-submit-btn');

    if(!regNo) {
      showAlert('Required Field', 'Please enter the Registry Number.', 'warning');
      document.getElementById('enc-reg-no').focus();
      return;
    }
    if(!firstName) {
      showAlert('Required Field', 'Please enter the First Name of the deceased.', 'warning');
      document.getElementById('enc-first-name').focus();
      return;
    }
    if(!lastName) {
      showAlert('Required Field', 'Please enter the Last Name of the deceased.', 'warning');
      document.getElementById('enc-last-name').focus();
      return;
    }
    if(!dodDay || !dodMonth || !dodYear) {
      showAlert('Required Field', 'Please complete the Date of Death (Day, Month, Year).', 'warning');
      document.getElementById('enc-dod-day').focus();
      return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Saving...';

    const payload = {
      province: province,
      city_municipality: cityMunicipality,
      registration_number: regNo,
      first_name: firstName,
      middle_name: middleName,
      last_name: lastName,
      deceased_name: `${firstName} ${middleName ? middleName + ' ' : ''}${lastName}`.trim(),
      sex: sex,
      date_of_death_day: dodDay,
      date_of_death_month: dodMonth,
      date_of_death_year: dodYear,
      date_of_birth_day: dobDay || null,
      date_of_birth_month: dobMonth || null,
      date_of_birth_year: dobYear || null,
      age_years: ageYears !== '' ? ageYears : null,
      age_months: ageMonths !== '' ? ageMonths : null,
      age_days: ageDays !== '' ? ageDays : null,
      age_hours: ageHours !== '' ? ageHours : null,
      place_of_death_hospital: podHospital,
      place_of_death_house_no: podHouseNo,
      place_of_death_street: podStreet,
      place_of_death_barangay: podBarangay,
      place_of_death_city: podCity,
      place_of_death_province: podProvince,
      civil_status: civilStatus,
      religion: religion,
      citizenship: citizenship,
      residence_house_no: resHouseNo,
      residence_street: resStreet,
      residence_barangay: resBarangay,
      residence_city: resCity,
      residence_province: resProvince,
      residence_country: resCountry,
      occupation: occupation,
      representative_name: repName
    };

    try {
      const res = await fetch('<?= url('/admin/damayan/encoded-certificates/save') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if(data.success) {
        showAlert('Certificate Registered', data.message || 'Death certificate has been successfully registered and recorded in the civil registry database.', 'success');
        closeEncodeModal();
        setTimeout(() => location.reload(), 1200);
      } else {
        showAlert('Registration Error', data.error || data.message || 'Failed to save registered certificate.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Save &amp; Record Certificate';
      }
    } catch(e) {
      console.error(e);
      showAlert('Error', 'An error occurred while saving the registered certificate.', 'error');
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Save &amp; Record Certificate';
    }
  }

  async function openEncodedRecordDetails(id) {
    try {
      const res = await fetch('<?= url('/admin/damayan/encoded-certificates/detail') ?>?id=' + encodeURIComponent(id));
      const data = await res.json();
      if(!data.success || !data.record) {
        showAlert('Error', data.error || 'Failed to retrieve registered certificate details.', 'error');
        return;
      }

      const r = data.record;
      document.getElementById('v-enc-reg').textContent = '#' + (r.registration_number || '—');
      
      const locParts = [r.city_municipality, r.province].filter(Boolean);
      document.getElementById('v-enc-location').textContent = locParts.length ? locParts.join(', ') : 'City of Dasmariñas, Cavite';

      const fName = r.first_name || '';
      const mName = r.middle_name || '';
      const lName = r.last_name || '';
      let fullName = r.deceased_name || '';
      if(!fullName && (fName || lName)) {
        fullName = `${fName} ${mName ? mName + ' ' : ''}${lName}`.trim();
      }
      document.getElementById('v-enc-deceased').textContent = fullName || '—';

      const sexStatusParts = [r.sex, r.civil_status].filter(Boolean);
      document.getElementById('v-enc-sex-status').textContent = sexStatusParts.length ? sexStatusParts.join(' • ') : '—';

      // Date of Death
      let dodFormatted = '—';
      if(r.date_of_death) {
        const d = new Date(r.date_of_death);
        dodFormatted = !isNaN(d) ? d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : r.date_of_death;
      } else if (r.date_of_death_year) {
        dodFormatted = `${r.date_of_death_month || ''}/${r.date_of_death_day || ''}/${r.date_of_death_year}`;
      }
      document.getElementById('v-enc-dod').textContent = dodFormatted;

      // Date of Birth
      let dobFormatted = '—';
      if(r.date_of_birth) {
        const b = new Date(r.date_of_birth);
        dobFormatted = !isNaN(b) ? b.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : r.date_of_birth;
      } else if (r.date_of_birth_year) {
        dobFormatted = `${r.date_of_birth_month || ''}/${r.date_of_birth_day || ''}/${r.date_of_birth_year}`;
      }
      document.getElementById('v-enc-dob').textContent = dobFormatted;

      // Age
      let ageText = r.age_display || '';
      if(!ageText) {
        const ageParts = [];
        if(r.age_years) ageParts.push(`${r.age_years} yrs`);
        if(r.age_months) ageParts.push(`${r.age_months} mos`);
        if(r.age_days) ageParts.push(`${r.age_days} days`);
        if(r.age_hours) ageParts.push(`${r.age_hours} hrs`);
        ageText = ageParts.join(', ') || '—';
      }
      document.getElementById('v-enc-age').textContent = ageText;

      document.getElementById('v-enc-religion').textContent = r.religion || 'Islam';
      document.getElementById('v-enc-citizenship').textContent = r.citizenship || 'Filipino';
      document.getElementById('v-enc-occupation').textContent = r.occupation || '—';

      // Place of Death
      let podText = r.place_of_death || '';
      if(!podText) {
        const parts = [r.place_of_death_hospital, r.place_of_death_house_no, r.place_of_death_street, r.place_of_death_barangay, r.place_of_death_city, r.place_of_death_province].filter(Boolean);
        podText = parts.join(', ') || '—';
      }
      document.getElementById('v-enc-pod').textContent = podText;

      // Residence
      let resText = r.residence || '';
      if(!resText) {
        const parts = [r.residence_house_no, r.residence_street, r.residence_barangay, r.residence_city, r.residence_province, r.residence_country].filter(Boolean);
        resText = parts.join(', ') || '—';
      }
      document.getElementById('v-enc-residence').textContent = resText;

      document.getElementById('v-enc-rep').textContent = r.representative_name || 'None Specified';
      document.getElementById('v-enc-staff').textContent = r.encoded_by_name || 'Staff Admin';
      
      const encD = new Date(r.encoded_at);
      document.getElementById('v-enc-date').textContent = isNaN(encD) ? (r.encoded_at || '—') : encD.toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true
      });

      document.getElementById('viewEncodedDetailModal').style.display = 'flex';
    } catch(e) {
      console.error(e);
      showAlert('Error', 'Failed to retrieve registered record.', 'error');
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
