
    // Global State Variables
    let isCertVerifiedInDept = false;
    let isRegNumberAlreadyRegistered = false;
    let registeredDeceasedName = '';
    let certLookupTimeout = null;
    let regCertLookupTimeout = null;
    let isSubmittingBurial = false;
    let isSubmittingResp = false;
    let lastSubmittedRefId = null;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ── Function Selector Switching (Register vs Copy) ──
    function selectCertFunction(func) {
        document.getElementById('selected-cert-action').value = func;
        const isCopy = (func === 'copy');
        
        const regCard = document.getElementById('opt-type-register');
        const copyCard = document.getElementById('opt-type-copy');
        const copyContainer = document.getElementById('copy-specific-container');
        const regContainer = document.getElementById('registration-deceased-fields');
        const requesterTitle = document.getElementById('requester-section-title');
        const btnSubmitText = document.getElementById('btn-submit-burial-text');

        // List of registration inputs that have required attributes
        const regRequiredIds = [
            'deceased-first-name',
            'deceased-last-name',
            'deceased-dod',
            'deceased-pod',
            'deceased-residence'
        ];
        
        if (isCopy) {
            if (copyCard) {
                copyCard.classList.add('selected');
                const r = copyCard.querySelector('input[type="radio"]');
                if (r) r.checked = true;
            }
            if (regCard) {
                regCard.classList.remove('selected');
                const r = regCard.querySelector('input[type="radio"]');
                if (r) r.checked = false;
            }

            // Show ONLY Existing Certificate Details & Purpose of Request + Requester
            if (copyContainer) copyContainer.style.display = 'block';
            if (regContainer) regContainer.style.display = 'none';

            // Remove required attribute from hidden registration inputs so HTML5 validation passes
            regRequiredIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.removeAttribute('required');
            });

            // Add required attribute to copy fields
            document.getElementById('copy-deceased-name')?.setAttribute('required', 'required');
            document.getElementById('copy-reason')?.setAttribute('required', 'required');

            const regTag = document.getElementById('details-reg-req-tag');
            if (regTag) regTag.innerHTML = '<span style="color:var(--danger,#dc2626); font-weight:bold;">*</span>';

            const curRegVal = document.getElementById('reg-cert-no')?.value?.trim();
            if (curRegVal) {
                checkRegCertNumber(curRegVal, false);
            } else {
                const regFeedback = document.getElementById('reg-cert-feedback');
                if (regFeedback) regFeedback.innerHTML = '';
            }

            if (requesterTitle) {
                requesterTitle.innerHTML = '<svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg> 2. Requester / Authorized Representative Information';
            }
            if (btnSubmitText) btnSubmitText.textContent = 'Submit Copy Request';
        } else {
            if (regCard) {
                regCard.classList.add('selected');
                const r = regCard.querySelector('input[type="radio"]');
                if (r) r.checked = true;
            }
            if (copyCard) {
                copyCard.classList.remove('selected');
                const r = copyCard.querySelector('input[type="radio"]');
                if (r) r.checked = false;
            }

            // Show Registration Deceased inputs + Requester
            if (copyContainer) copyContainer.style.display = 'none';
            if (regContainer) regContainer.style.display = 'block';

            // Restore required attribute to registration inputs
            regRequiredIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });

            // Remove required attribute from hidden copy fields
            document.getElementById('copy-deceased-name')?.removeAttribute('required');
            document.getElementById('copy-reason')?.removeAttribute('required');

            const regTag = document.getElementById('details-reg-req-tag');
            if (regTag) regTag.innerHTML = '(Optional - if already assigned by LCRO/Hospital)';

            const curRegVal = document.getElementById('reg-cert-no')?.value?.trim();
            if (curRegVal) {
                checkRegCertNumber(curRegVal, false);
            } else {
                const regFeedback = document.getElementById('reg-cert-feedback');
                if (regFeedback) regFeedback.innerHTML = '';
            }

            if (requesterTitle) {
                requesterTitle.innerHTML = '<svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg> 3. Requester / Authorized Representative Information';
            }
            if (btnSubmitText) btnSubmitText.textContent = 'Register Death Certificate';
        }
    }

    // ── Auto-fill Existing Deceased (For Registration Form) ──
    function autoFillExistingDeceased(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        if(!opt || !opt.value) {
            document.getElementById('selected-deceased-id').value = '';
            return;
        }

        document.getElementById('selected-deceased-id').value = opt.value;
        isCertVerifiedInDept = true;
        document.getElementById('deceased-first-name').value = opt.getAttribute('data-fname') || '';
        document.getElementById('deceased-middle-name').value = opt.getAttribute('data-mname') || '';
        document.getElementById('deceased-last-name').value = opt.getAttribute('data-lname') || '';
        document.getElementById('deceased-sex').value = opt.getAttribute('data-sex') || 'Male';
        document.getElementById('deceased-dob').value = (opt.getAttribute('data-dob') || '').substring(0, 10);
        document.getElementById('deceased-dod').value = (opt.getAttribute('data-dod') || '').substring(0, 10);
        document.getElementById('deceased-pod').value = opt.getAttribute('data-pod') || '';
        document.getElementById('deceased-residence').value = opt.getAttribute('data-residence') || '';
        document.getElementById('deceased-civil-status').value = opt.getAttribute('data-civil') || '';
        document.getElementById('deceased-citizenship').value = opt.getAttribute('data-nationality') || 'Filipino';
        document.getElementById('deceased-religion').value = opt.getAttribute('data-religion') || 'Islam';
    }

    // ── Auto-fill Existing Deceased (For Copy Request Form) ──
    function autoFillCopyDeceased(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        if(!opt || !opt.value) {
            document.getElementById('selected-deceased-id').value = '';
            return;
        }

        document.getElementById('selected-deceased-id').value = opt.value;
        const fullName = opt.getAttribute('data-fullname') || opt.text.split('(')[0].trim();
        if (fullName && document.getElementById('copy-deceased-name')) {
            document.getElementById('copy-deceased-name').value = fullName;
        }
        const dod = (opt.getAttribute('data-dod') || '').substring(0, 10);
        if (dod && document.getElementById('copy-deceased-dod')) {
            document.getElementById('copy-deceased-dod').value = dod;
        }
        const refNo = opt.getAttribute('data-ref') || '';
        if (refNo && document.getElementById('reg-cert-no') && !document.getElementById('reg-cert-no').value) {
            document.getElementById('reg-cert-no').value = refNo;
            checkRegCertNumber(refNo);
        }
    }

    // ── Toggle Request Form Visibility ──
    function toggleRequestForm(forceState = null) {
        const container = document.getElementById('burial-form-container');
        const btnTopText = document.getElementById('btn-request-text-top');
        const btnBottomText = document.getElementById('btn-request-text-bottom');
        
        let isOpen = container.style.display === 'block';
        if (forceState !== null) {
            isOpen = !forceState;
        }

        if (isOpen) {
            container.style.display = 'none';
            if (btnTopText) btnTopText.textContent = 'Submit New Request';
            if (btnBottomText) btnBottomText.textContent = 'Submit New Request';
        } else {
            container.style.display = 'block';
            if (btnTopText) btnTopText.textContent = 'Close Request Form';
            if (btnBottomText) btnBottomText.textContent = 'Close Request Form';
            
            setTimeout(() => {
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
        }
    }

    // ── Confirm Receipt of Certificate Copy ──
    async function confirmCopyReceipt(refId) {
        if (!confirm('Have you physically or officially received your requested Death Certificate copy?')) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('ref_id', refId);

            const res = await fetch(''dummy'', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                const toast = document.getElementById('burial-toast');
                toast.innerHTML = '<strong>Receipt Confirmed!</strong> Your receipt of the certificate copy has been recorded.<br><span style="font-size:0.8rem;opacity:0.9;">Updating request status...</span>';
                toast.style.display = 'block';
                setTimeout(() => {
                    window.location.reload();
                }, 1300);
            } else {
                alert(data.error || data.message || 'Failed to confirm receipt.');
            }
        } catch(err) {
            console.error(err);
            alert('An error occurred while confirming receipt.');
        }
    }

    // certLookupTimeout and verification flags declared at top of script

    function debouncedCheckCert(val) {
        clearTimeout(certLookupTimeout);
        certLookupTimeout = setTimeout(() => {
            checkExistingCertNumber(val);
        }, 300);
    }

    function isRegistryNumberFormatValid(val) {
        val = (val || '').trim();
        if (!val) return false;
        const clean = val.replace(/^#/, '').trim();
        if (/[^A-Za-z0-9\-\/\s]/.test(clean)) return false;
        
        const alphanumOnly = clean.replace(/[^A-Za-z0-9]/g, '');
        if (alphanumOnly.length < 4) return false;

        if (/[-\/]/.test(clean)) {
            return /[-\/][A-Za-z0-9]{1,}$/.test(clean);
        }
        return alphanumOnly.length >= 5;
    }

    function isRegistryNumberComplete(val, isBlur = false) {
        val = (val || '').trim();
        if (!val) return false;
        const clean = val.replace(/^#/, '').trim();
        if (/[^A-Za-z0-9\-\/\s]/.test(clean)) return false;
        
        const alphanumOnly = clean.replace(/[^A-Za-z0-9]/g, '');
        if (alphanumOnly.length < 4) return false;

        if (/[-\/]/.test(clean)) {
            return /[-\/][A-Za-z0-9]{1,}$/.test(clean);
        }
        return isBlur ? (alphanumOnly.length >= 5) : false;
    }

    function debouncedCheckRegCert(val) {
        clearTimeout(regCertLookupTimeout);
        regCertLookupTimeout = setTimeout(() => {
            checkRegCertNumber(val, false);
        }, 250);
    }

    async function checkRegCertNumber(val, isBlur = false) {
        val = (val || '').trim();
        const fb = document.getElementById('reg-cert-feedback');
        const hiddenRegId = document.getElementById('linked-reg-cert-id');
        const certAction = document.getElementById('selected-cert-action')?.value || 'register';
        const isCopy = (certAction === 'copy');

        if (!val) {
            isCertVerifiedInDept = false;
            isRegNumberAlreadyRegistered = false;
            registeredDeceasedName = '';
            if (hiddenRegId) hiddenRegId.value = '';
            if (fb) fb.innerHTML = '';
            return false;
        }

        // If on blur and format is invalid, show Invalid Registry No.
        if (isBlur && !isRegistryNumberFormatValid(val)) {
            isCertVerifiedInDept = false;
            isRegNumberAlreadyRegistered = false;
            if (fb) {
                fb.innerHTML = `
                    <div style="background:#fef2f2; border:1.5px solid #ef4444; border-radius:10px; padding:12px 16px; margin-top:8px;">
                        <div style="color:#dc2626; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.9rem;">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#dc2626;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            Invalid Registry No.
                        </div>
                    </div>
                `;
            }
            return false;
        }

        // Do not display a premature validation result if incomplete format or length while typing
        if (!isRegistryNumberComplete(val, isBlur)) {
            isCertVerifiedInDept = false;
            isRegNumberAlreadyRegistered = false;
            if (fb && !isBlur) fb.innerHTML = '';
            return false;
        }

        try {
            const res = await fetch(''dummy'?reg=' + encodeURIComponent(val));
            const text = await res.text();
            let data = null;
            try { 
                data = JSON.parse(text); 
            } catch(e) {
                const m = text.match(/\{[\s\S]*\}/);
                if (m) { try { data = JSON.parse(m[0]); } catch(e2){} }
            }

            if (data && data.found && data.record) {
                isCertVerifiedInDept = true;
                isRegNumberAlreadyRegistered = true;
                registeredDeceasedName = data.record.deceased_name || '';

                const recId = data.record.encoded_cert_id || data.record.burial_request_id || data.record.id || '';
                if (hiddenRegId) hiddenRegId.value = recId;
                const hiddenEncId = document.getElementById('linked-encoded-cert-id');
                if (hiddenEncId) hiddenEncId.value = recId;
                const hiddenDecId = document.getElementById('selected-deceased-id');
                if (hiddenDecId && data.record.deceased_id) hiddenDecId.value = data.record.deceased_id;

                const copyNameInput = document.getElementById('copy-deceased-name');
                if (copyNameInput && !copyNameInput.value.trim() && data.record.deceased_name) {
                    copyNameInput.value = data.record.deceased_name;
                }

                if (fb) {
                    if (isCopy) {
                        // In the right place (Additional Copy): Indicator disappears
                        fb.innerHTML = '';
                    } else {
                        // In Register: Suggest switching to Additional Certificate Copy Request
                        fb.innerHTML = `
                            <div style="background:#fff7ed; border:1.5px solid #f97316; border-radius:10px; padding:12px 16px; margin-top:8px;">
                                <div style="color:#c2410c; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#ea580c;flex-shrink:0;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                    Already Registered
                                </div>
                                <div style="color:#9a3412; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                                    This death certificate is already registered in ISCAG Damayan records. Please choose <strong>Additional Certificate Copy Request</strong> to request a copy.
                                </div>
                                <button type="button" onclick="selectCertFunction('copy')" style="margin-top:8px; display:inline-flex; align-items:center; gap:6px; background:#ea580c; color:#ffffff; font-weight:700; font-size:0.8rem; padding:6px 14px; border-radius:8px; border:none; cursor:pointer;">
                                    <svg viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;flex-shrink:0;"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    Select Additional Certificate Copy Request
                                </button>
                            </div>
                        `;
                    }
                }
                return true;
            } else {
                isCertVerifiedInDept = false;
                isRegNumberAlreadyRegistered = false;
                registeredDeceasedName = '';
                if (hiddenRegId) hiddenRegId.value = '';

                if (fb) {
                    if (isCopy) {
                        // In Additional Copy but not found: Rephrase to "No Record Found"
                        fb.innerHTML = `
                            <div style="background:#fff7ed; border:1.5px solid #f97316; border-radius:10px; padding:12px 16px; margin-top:8px;">
                                <div style="color:#c2410c; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#ea580c;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                    No Record Found
                                </div>
                                <div style="color:#9a3412; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                                    No death certificate record was found for this registry number in ISCAG Damayan records. Please choose <strong>Register Certificate</strong> to register this record first.
                                </div>
                                <button type="button" onclick="selectCertFunction('register')" style="margin-top:8px; display:inline-flex; align-items:center; gap:6px; background:#ea580c; color:#ffffff; font-weight:700; font-size:0.8rem; padding:6px 14px; border-radius:8px; border:none; cursor:pointer;">
                                    <svg viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;flex-shrink:0;"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    Select Register Certificate
                                </button>
                            </div>
                        `;
                    } else {
                        // In Register and not found (available for registration): Indicator disappears
                        fb.innerHTML = '';
                    }
                }
                return false;
            }
        } catch(e) {
            console.error('Error checking registry number:', e);
            return false;
        }


    }

    function switchToCopyRequest(regNo) {
        selectCertFunction('copy');
        const regInput = document.getElementById('reg-cert-no');
        if (regInput) {
            regInput.value = regNo;
            checkRegCertNumber(regNo);
        }
        document.getElementById('opt-type-copy')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function autoFillExistingCopyDeceased(selectEl) {
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || !opt.value) return;
        isCertVerifiedInDept = true;
        const fullname = opt.getAttribute('data-fullname') || '';
        const dod = opt.getAttribute('data-dod') || '';
        const ref = opt.getAttribute('data-ref') || '';

        const nameInput = document.getElementById('copy-deceased-name');
        if (nameInput && fullname) nameInput.value = fullname;

        const dodInput = document.getElementById('copy-deceased-dod');
        if (dodInput && dod) dodInput.value = dod;

        const regInput = document.getElementById('reg-cert-no');
        if (regInput && ref) {
            regInput.value = ref;
            checkRegCertNumber(ref);
        }
    }

    async function checkExistingCertNumber(val) {
        val = (val || '').trim();
        const fb = document.getElementById('existing-cert-feedback');
        const hiddenEncId = document.getElementById('linked-encoded-cert-id');
        if (!val) {
            if (fb) fb.innerHTML = 'Allows staff to locate and link the existing record immediately.';
            if (hiddenEncId) hiddenEncId.value = '';
            isCertVerifiedInDept = false;
            return;
        }

        if (!isRegistryNumberFormatValid(val)) {
            isCertVerifiedInDept = false;
            if (hiddenEncId) hiddenEncId.value = '';
            if (fb) {
                fb.innerHTML = `
                    <div style="background:#fef2f2; border:1.5px solid #ef4444; border-radius:10px; padding:12px 16px; margin-top:6px;">
                        <div style="color:#dc2626; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#dc2626;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            Invalid Registry No.
                        </div>
                        <div style="color:#991b1b; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                            Please enter a valid registry number format (e.g. 2026-00412 or #2023-2-0001113).
                        </div>
                    </div>
                `;
            }
            return;
        }

        try {
            const res = await fetch(''dummy'?reg=' + encodeURIComponent(val));
            const data = await res.json();
            if (data && data.found && data.record) {
                isCertVerifiedInDept = true;
                if (hiddenEncId) hiddenEncId.value = data.record.id;
                if (fb) {
                    fb.innerHTML = `
                        <div style="background:#fff7ed; border:1.5px solid #f97316; border-radius:10px; padding:12px 16px; margin-top:6px;">
                            <div style="color:#c2410c; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#ea580c;flex-shrink:0;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                Already Registered
                            </div>
                            <div style="color:#9a3412; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                                Death certificate record verified on file.
                            </div>
                        </div>
                    `;
                }
                const decInput = document.getElementById('copy-deceased-name');
                if (decInput && !decInput.value.trim()) {
                    decInput.value = data.record.deceased_name;
                }
            } else {
                isCertVerifiedInDept = false;
                if (hiddenEncId) hiddenEncId.value = '';
                if (fb) {
                    fb.innerHTML = `
                        <div style="background:#fff7ed; border:1.5px solid #f97316; border-radius:10px; padding:12px 16px; margin-top:6px;">
                            <div style="color:#c2410c; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#ea580c;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                Not Registered
                            </div>
                            <div style="color:#9a3412; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                                This registry number is not registered on file. Please select <strong>Register Certificate</strong> to register first.
                            </div>
                            <button type="button" onclick="selectCertFunction('register')" style="margin-top:8px; display:inline-flex; align-items:center; gap:6px; background:#ea580c; color:#ffffff; font-weight:700; font-size:0.8rem; padding:6px 14px; border-radius:8px; border:none; cursor:pointer;">
                                <svg viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;flex-shrink:0;"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                Select Register Certificate
                            </button>
                        </div>
                    `;
                }
            }
        } catch(e) {}
    }

    // ── Birthday and Date of Death Validation ──
    function validateDamayanDates(showAlert = false) {
        const dobInput = document.getElementById('deceased-dob');
        const dodInput = document.getElementById('deceased-dod');
        const errEl = document.getElementById('deceased-dob-error');
        if (!dobInput || !dodInput) return true;
        const dobVal = dobInput.value;
        const dodVal = dodInput.value;
        if (dobVal && dodVal && dobVal >= dodVal) {
            const msg = 'Invalid date of birth. The date of birth must be earlier than the date of death.';
            if (errEl) {
                errEl.textContent = msg;
                errEl.style.display = 'block';
            }
            dobInput.style.borderColor = '#dc2626';
            if (showAlert) {
                alert(msg);
                dobInput.focus();
            }
            return false;
        } else {
            if (errEl) {
                errEl.textContent = '';
                errEl.style.display = 'none';
            }
            dobInput.style.borderColor = '';
            return true;
        }
    }

    // ── Submit Request via AJAX ──
    async function submitBurialRequest(event) {
        event.preventDefault();
        
        if (isSubmittingBurial) {
            return;
        }

        const certifyCheck = document.getElementById('burial-certify');
        if (!certifyCheck || !certifyCheck.checked) {
            alert('Please check the certification box to confirm the request.');
            return;
        }

        const certAction = document.getElementById('selected-cert-action').value;

        // Date of birth and date of death validation for registration
        if (certAction === 'register') {
            const dobVal = document.getElementById('deceased-dob')?.value;
            const dodVal = document.getElementById('deceased-dod')?.value;
            if (dobVal && dodVal && dobVal >= dodVal) {
                alert('Invalid date of birth. The date of birth must be earlier than the date of death.');
                const errEl = document.getElementById('deceased-dob-error');
                if (errEl) {
                    errEl.textContent = 'Invalid date of birth. The date of birth must be earlier than the date of death.';
                    errEl.style.display = 'block';
                }
                document.getElementById('deceased-dob')?.focus();
                return;
            }

            const regCertNoVal = document.getElementById('reg-cert-no')?.value?.trim();
            if (regCertNoVal) {
                if (!isRegistryNumberFormatValid(regCertNoVal)) {
                    const fb = document.getElementById('reg-cert-feedback');
                    if (fb) {
                        fb.innerHTML = `
                            <div style="background:#fef2f2; border:1.5px solid #ef4444; border-radius:10px; padding:12px 16px; margin-top:8px;">
                                <div style="color:#dc2626; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#dc2626;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                    Invalid Registry No.
                                </div>
                                <div style="color:#991b1b; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                                    Please enter a valid registry number format (e.g. 2026-00412 or #2023-2-0001113).
                                </div>
                            </div>
                        `;
                    }
                    document.getElementById('reg-cert-no')?.focus();
                    return;
                }
                if (isRegNumberAlreadyRegistered) {
                    const fb = document.getElementById('reg-cert-feedback');
                    if (fb) {
                        fb.innerHTML = `
                            <div style="background:#fff7ed; border:1.5px solid #f97316; border-radius:10px; padding:12px 16px; margin-top:8px;">
                                <div style="color:#c2410c; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#ea580c;flex-shrink:0;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                    Already Registered
                                </div>
                                <div style="color:#9a3412; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                                    This death certificate is already registered in ISCAG Damayan records. Please choose <strong>Additional Certificate Copy Request</strong> to request a copy.
                                </div>
                                <button type="button" onclick="selectCertFunction('copy')" style="margin-top:8px; display:inline-flex; align-items:center; gap:6px; background:#ea580c; color:#ffffff; font-weight:700; font-size:0.8rem; padding:6px 14px; border-radius:8px; border:none; cursor:pointer;">
                                    <svg viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;flex-shrink:0;"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    Select Additional Certificate Copy Request
                                </button>
                            </div>
                        `;
                    }
                    document.getElementById('reg-cert-no')?.focus();
                    return;
                }
            }
        }

        const selectedDecId = document.getElementById('selected-deceased-id')?.value;
        const copySelectDecId = document.getElementById('copy-select-deceased')?.value;
        const linkedEncId = document.getElementById('linked-encoded-cert-id')?.value;
        const linkedRegId = document.getElementById('linked-reg-cert-id')?.value;
        const regCertNo = document.getElementById('reg-cert-no')?.value.trim() || '';

        // Verification Gate: Existing certificate record must be confirmed in department archive only for copy requests
        if (certAction === 'copy') {
            if (!regCertNo || !isRegistryNumberFormatValid(regCertNo)) {
                const fb = document.getElementById('reg-cert-feedback');
                if (fb) {
                    fb.innerHTML = `
                        <div style="background:#fef2f2; border:1.5px solid #ef4444; border-radius:10px; padding:12px 16px; margin-top:8px;">
                            <div style="color:#dc2626; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#dc2626;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                Invalid Registry No.
                            </div>
                            <div style="color:#991b1b; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                                Please enter a valid registry number to proceed with a copy request.
                            </div>
                        </div>
                    `;
                }
                document.getElementById('reg-cert-no')?.focus();
                return;
            }
            if (!isCertVerifiedInDept) {
                const fb = document.getElementById('reg-cert-feedback');
                if (fb) {
                    fb.innerHTML = `
                        <div style="background:#fff7ed; border:1.5px solid #f97316; border-radius:10px; padding:12px 16px; margin-top:8px;">
                            <div style="color:#c2410c; font-weight:700; display:flex; align-items:center; gap:8px; font-size:0.92rem;">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#ea580c;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                No Record Found
                            </div>
                            <div style="color:#9a3412; font-size:0.84rem; margin-top:4px; line-height:1.4;">
                                No death certificate record was found for this registry number in ISCAG Damayan records. Please choose <strong>Register Certificate</strong> to register this record first.
                            </div>
                            <button type="button" onclick="selectCertFunction('register')" style="margin-top:8px; display:inline-flex; align-items:center; gap:6px; background:#ea580c; color:#ffffff; font-weight:700; font-size:0.8rem; padding:6px 14px; border-radius:8px; border:none; cursor:pointer;">
                                <svg viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;flex-shrink:0;"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                Select Register Certificate
                            </button>
                        </div>
                    `;
                }
                document.getElementById('reg-cert-no')?.focus();
                return;
            }
        }


        const btnSubmit = document.getElementById('btn-submit-burial');
        const originalText = btnSubmit.innerHTML;

        // Package as FormData to support file upload
        const formData = new FormData();
        formData.append('cert_action_type', certAction);

        if (certAction === 'copy') {
            const decName = document.getElementById('copy-deceased-name')?.value.trim();
            const reason = document.getElementById('copy-reason')?.value.trim();
            if (!decName) {
                alert('Please enter the deceased person full name as appearing on the certificate.');
                document.getElementById('copy-deceased-name')?.focus();
                return;
            }
            if (!reason) {
                alert('Please specify the reason for requesting another copy of the death certificate.');
                document.getElementById('copy-reason')?.focus();
                return;
            }

            formData.append('request_type', 'death_certificate');
            formData.append('death_cert_requested', '1');
            formData.append('cemetery_plot_requested', '0');
            formData.append('deceased_name', decName);
            formData.append('copy_reason', reason);
            formData.append('existing_cert_no', regCertNo);
            formData.append('encoded_cert_id', linkedEncId);
            formData.append('num_copies', document.getElementById('num-copies')?.value || '1');
            formData.append('dod', document.getElementById('copy-deceased-dod')?.value || '');

            const copyDoc = document.getElementById('copy-supporting-document');
            if (copyDoc && copyDoc.files.length > 0) {
                formData.append('supporting_document', copyDoc.files[0]);
            }
        } else {
            formData.append('request_type', 'death_certificate');
            formData.append('death_cert_requested', '1');
            formData.append('cemetery_plot_requested', '0');
            formData.append('existing_cert_no', regCertNo);
            formData.append('encoded_cert_id', linkedRegId);
            formData.append('firstName', document.getElementById('deceased-first-name')?.value.trim() || '');
            formData.append('middleName', document.getElementById('deceased-middle-name')?.value.trim() || '');
            formData.append('lastName', document.getElementById('deceased-last-name')?.value.trim() || '');
            formData.append('sex', document.getElementById('deceased-sex')?.value || 'Male');
            formData.append('dob', document.getElementById('deceased-dob')?.value || '');
            formData.append('dod', document.getElementById('deceased-dod')?.value || '');
            formData.append('pod', document.getElementById('deceased-pod')?.value.trim() || '');
            formData.append('residence', document.getElementById('deceased-residence')?.value.trim() || '');
            formData.append('religion', document.getElementById('deceased-religion')?.value || 'Islam');
            formData.append('civilStatus', document.getElementById('deceased-civil-status')?.value || '');
            formData.append('citizenship', document.getElementById('deceased-citizenship')?.value || 'Filipino');

            const fileInput = document.getElementById('supporting-document-file');
            if (fileInput && fileInput.files.length > 0) {
                formData.append('supporting_document', fileInput.files[0]);
            }
        }

        const deceasedId = document.getElementById('selected-deceased-id')?.value;
        if(deceasedId) {
            formData.append('deceased_id', deceasedId);
        }

        const informantPhone = document.getElementById('informant-phone').value.trim();
        if (informantPhone !== '' && typeof isValidPhone === 'function' && !isValidPhone(informantPhone)) {
            alert('Please enter a valid phone number with the correct country code and number format.');
            document.getElementById('informant-phone').focus();
            return;
        }

        formData.append('relationship', document.getElementById('informant-rel').value);
        formData.append('informantName', document.getElementById('informant-name').value.trim());
        formData.append('informantContact', informantPhone);
        formData.append('informantEmail', document.getElementById('informant-email').value.trim());
        formData.append('informantAddress', document.getElementById('informant-address').value.trim());
        formData.append('additionalNotes', document.getElementById('additional-notes').value.trim());

        isSubmittingBurial = true;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display:inline-block;width:1rem;height:1rem;border:2px solid currentColor;border-right-color:transparent;border-radius:50%;animation:spinner-border .75s linear infinite;vertical-align:-0.125em;"></span>Submitting Request...';

        try {
            const response = await fetch(''dummy'', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            let data = null;
            try {
                data = JSON.parse(text);
            } catch (jsonErr) {
                const match = text.match(/\{[\s\S]*\}/);
                if (match) {
                    try {
                        data = JSON.parse(match[0]);
                    } catch (e2) {}
                }
            }

            if (!data) {
                console.error('Non-JSON response received from server:', text);
                throw new Error('Server returned an unexpected response format. Please try again or contact support.');
            }

            if (data.success) {
                const ref = data.ref_id || 'SUBMITTED';
                lastSubmittedRefId = ref;

                // Re-enable submit button
                isSubmittingBurial = false;
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;

                // Extract submitted values to populate official claiming slip
                const decName = (certAction === 'copy')
                    ? (document.getElementById('copy-deceased-name')?.value.trim() || 'Record on File')
                    : ((document.getElementById('deceased-first-name')?.value.trim() || '') + ' ' + (document.getElementById('deceased-last-name')?.value.trim() || '')).trim();
                const dod = (certAction === 'copy')
                    ? (document.getElementById('copy-deceased-dod')?.value || 'On File')
                    : (document.getElementById('deceased-dod')?.value || '—');
                const pod = (certAction === 'copy')
                    ? 'On File'
                    : (document.getElementById('deceased-pod')?.value.trim() || 'ISCAG Area');
                const reqName = document.getElementById('informant-name')?.value.trim() || 'Applicant';
                const reqRel = document.getElementById('informant-rel')?.value || 'Relative';
                const reqPhone = informantPhone || 'On File';

                // Populate Slip Fields
                const slipRef = document.getElementById('slip-ref-num-val');
                if (slipRef) slipRef.textContent = '#' + ref;
                const slipDec = document.getElementById('slip-deceased-name-val');
                if (slipDec) slipDec.innerHTML = '<strong>' + escapeHtml(decName) + '</strong>';
                const slipDod = document.getElementById('slip-dod-val');
                if (slipDod) slipDod.textContent = dod;
                const slipPod = document.getElementById('slip-pod-val');
                if (slipPod) slipPod.textContent = pod;
                const slipReg = document.getElementById('slip-reg-no-val');
                if (slipReg) slipReg.innerHTML = '<strong>' + escapeHtml(regCertNo || 'Under Department Processing') + '</strong>';
                const slipRegDate = document.getElementById('slip-reg-date-val');
                if (slipRegDate) slipRegDate.textContent = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                const slipStatus = document.getElementById('slip-status-val');
                if (slipStatus) slipStatus.innerHTML = '<strong>Request Received</strong>';
                const slipClaimant = document.getElementById('slip-claimant-name-val');
                if (slipClaimant) slipClaimant.innerHTML = '<strong>' + escapeHtml(reqName) + '</strong>';
                const slipRel = document.getElementById('slip-relationship-val');
                if (slipRel) slipRel.textContent = reqRel;
                const slipContact = document.getElementById('slip-contact-val');
                if (slipContact) slipContact.textContent = reqPhone;
                const slipSig = document.getElementById('slip-sig-claimant-val');
                if (slipSig) slipSig.textContent = reqName;

                // Show Success Alert Banner inside Slip Modal
                const banner = document.getElementById('slip-success-banner');
                if (banner) banner.style.display = 'block';
                const successTitle = (certAction === 'copy') ? 'Copy Request Submitted Successfully!' : 'Death Certificate Registration Submitted Successfully!';
                const bannerTitle = document.getElementById('slip-success-title');
                if (bannerTitle) bannerTitle.textContent = successTitle;

                // Display Claiming Slip Modal immediately
                openClaimingSlipModal();

                // Optional Toast Feedback
                const toast = document.getElementById('burial-toast');
                if (toast) {
                    toast.innerHTML = '<strong>' + successTitle + '</strong><br>Reference ID: <code>#' + ref + '</code><br><span style="font-size:0.8rem;opacity:0.9;">Official request slip generated.</span>';
                    toast.style.display = 'block';
                    setTimeout(() => { toast.style.display = 'none'; }, 4000);
                }
            } else {
                throw new Error(data.message || 'Failed to submit request.');
            }
        } catch (error) {
            console.error('Submission error:', error);
            alert(error.message || 'Failed to submit request. Please check your inputs and try again.');
            isSubmittingBurial = false;
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    }

    // ── Submit Additional Requirements Response via AJAX ──
    async function submitRequirementsResponse(event) {
        event.preventDefault();
        if (isSubmittingResp) {
            return;
        }

        const refId = document.getElementById('resp-ref-id').value;
        const infoText = document.getElementById('resp-info-text').value.trim();
        const btn = document.getElementById('btn-submit-resp');
        const originalText = btn.innerHTML;

        if (!infoText) {
            alert('Please provide the requested details or explanation.');
            return;
        }

        isSubmittingResp = true;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display:inline-block;width:1rem;height:1rem;border:2px solid currentColor;border-right-color:transparent;border-radius:50%;animation:spinner-border .75s linear infinite;vertical-align:-0.125em;"></span>Submitting...';

        const formData = new FormData();
        formData.append('ref_id', refId);
        formData.append('additional_info', infoText);

        const fileInput = document.getElementById('resp-doc-file');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('additional_doc', fileInput.files[0]);
        }

        try {
            const res = await fetch(''dummy'', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                const toast = document.getElementById('burial-toast');
                toast.innerHTML = '<strong>Success!</strong> Additional information submitted to Damayan staff.<br><span style="font-size:0.8rem;opacity:0.9;">Updating record...</span>';
                toast.style.display = 'block';
                setTimeout(() => {
                    window.location.reload();
                }, 1400);
            } else {
                throw new Error(data.message || 'Failed to submit information.');
            }
        } catch (err) {
            console.error(err);
            alert(err.message || 'Error submitting response.');
            isSubmittingResp = false;
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    function openClaimingSlipModal() {
        const modal = document.getElementById('claiming-slip-modal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.classList.add('slip-modal-active');
        }
    }

    function closeClaimingSlipModal() {
        const modal = document.getElementById('claiming-slip-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('slip-modal-active', 'printing-slip');
        }
        if (lastSubmittedRefId) {
            window.location.href = ''dummy'?id=' + encodeURIComponent(lastSubmittedRefId);
        }
    }

    function closeClaimingSlipAndGoTracking() {
        closeClaimingSlipModal();
    }
    }

function triggerClaimingSlipPrint() {
        let iframe = document.getElementById('claiming-slip-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'claiming-slip-iframe';
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            iframe.style.opacity = '0';
            iframe.style.pointerEvents = 'none';
            document.body.appendChild(iframe);
        }

        try {
            const sheet = document.getElementById('claiming-slip-sheet');
            const sheetHtml = sheet ? sheet.outerHTML : '';
            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write('<!DOCTYPE html><html><head><title>ISCAG Claiming Slip</title>');
            document.querySelectorAll('link[rel="stylesheet"], style').forEach(function(el) {
                doc.write(el.outerHTML);
            });
            doc.write('<style>body{background:#fff;margin:0;padding:20px;-webkit-print-color-adjust:exact;print-color-adjust:exact;} .slip-wrapper{border:1.5px solid #059669;box-shadow:none;margin:0 auto;max-width:720px;}</style>');
            doc.write('<' + '/head><body>');
            doc.write(sheetHtml);
            doc.write('<' + '/body><' + '/html>');
            doc.close();

            setTimeout(function() {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (e) {
                    console.warn('Iframe print error, falling back to modal print:', e);
                    document.body.classList.add('printing-slip');
                    window.print();
                    setTimeout(() => { document.body.classList.remove('printing-slip'); }, 1000);
                }
            }, 300);
        } catch (err) {
            console.warn('Iframe write error, falling back to modal print:', err);
            document.body.classList.add('printing-slip');
            window.print();
            setTimeout(() => { document.body.classList.remove('printing-slip'); }, 1000);
        }
    }

    function printClaimingSlip() {
        openClaimingSlipModal();
        setTimeout(function() {
            triggerClaimingSlipPrint();
        }, 150);
    }

    function printServiceDetails() {
        closeClaimingSlipModal();
        document.body.classList.remove('printing-slip', 'slip-modal-active');
        window.print();
    }

        document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const purposeParam = urlParams.get('purpose') || urlParams.get('action');
        if (purposeParam === 'copy') {
            selectCertFunction('copy');
        } else if (purposeParam === 'register' || purposeParam === 'request') {
            selectCertFunction('register');
        }
        const regParam = (urlParams.get('reg_no') || urlParams.get('reg') || '').trim();
        if (regParam) {
            const regInput = document.getElementById('reg-cert-no');
            if (regInput) {
                regInput.value = regParam;
                checkRegCertNumber(regParam, true);
            }
            const copyCertInput = document.getElementById('existing-cert-no');
            if (copyCertInput && !copyCertInput.value) {
                copyCertInput.value = regParam;
                checkExistingCertNumber(regParam);
            }
        }
    });
