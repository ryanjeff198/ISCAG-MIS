<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — My Billing & Proof of Payment</title>
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <style>
        .billing-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .billing-grid {
                grid-template-columns: 1fr;
            }
        }

        .billing-card {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            padding: 24px;
        }

        .billing-header {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid var(--border);
            padding-bottom: 12px;
        }

        .soa-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed var(--border);
        }

        .soa-stat-row:last-child {
            border-bottom: none;
        }

        .soa-stat-row span {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .soa-stat-row strong {
            font-size: 1.05rem;
            color: var(--text-main);
        }

        .due-amount-box {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            color: white;
            margin-top: 16px;
            box-shadow: 0 4px 12px rgba(23, 107, 69, 0.2);
        }

        .due-amount-box p {
            margin: 0 0 6px 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.9;
        }

        .due-amount-box h2 {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 800;
        }

        /* Form Styles */
        .form-row {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.2s;
            background: #fff;
        }

        .form-control:read-only {
            background: #f4f6f5;
            color: var(--text-muted);
            cursor: not-allowed;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
        }

        .file-upload-wrapper {
            position: relative;
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #fafafa;
            transition: all 0.2s;
            cursor: pointer;
        }

        .file-upload-wrapper:hover {
            border-color: var(--primary);
            background: rgba(23, 107, 69, 0.02);
        }

        .file-upload-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(23, 107, 69, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(23, 107, 69, 0.3);
        }

        /* History Table */
        .history-card {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            margin-top: 24px;
            overflow: hidden;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th {
            background: #f9f9f9;
            padding: 12px 20px;
            text-align: left;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border);
        }

        .history-table td {
            padding: 14px 20px;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
            font-weight: 500;
        }

        .badge-pending {
            background: rgba(199, 154, 43, 0.1);
            color: #c79a2b;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-verified {
            background: rgba(47, 138, 96, 0.1);
            color: #2f8a60;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-rejected {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">
        <!-- ═══ SIDEBAR ═══ -->
        <?php 
          $active_page = 'apartment_info'; 
          include BASE_PATH . '/app/views/user/sidebar.php'; 
        ?>

        <!-- ═══ MAIN CONTENT ═══ -->
        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Billing & Payment</div>
                    <div class="top-bar-subtitle">View your SOA and submit proof of payment documents.</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
                </div>
            </div>

            <div class="page-body">
                <div class="breadcrumb-bar">
                    <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
                    <span class="sep">›</span>
                    <span>Apartment</span>
                    <span class="sep">›</span>
                    <span class="current">Billing & Payment</span>
                </div>

                <div class="billing-grid">
                    <!-- SOA SUMMARY CARD -->
                    <div class="billing-card">
                        <div class="billing-header">
                            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;">
                                <path
                                    d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z" />
                            </svg>
                            Current Statement of Account
                        </div>
                        <div style="margin-bottom: 20px;">
                            <div class="soa-stat-row">
                                <span>Tenant ID</span>
                                <strong id="soa-tenant-id">TNT-001</strong>
                            </div>
                            <div class="soa-stat-row">
                                <span>Apartment / Room</span>
                                <strong id="soa-room">101-A</strong>
                            </div>
                            <div class="soa-stat-row">
                                <span>Billing Month</span>
                                <strong id="soa-month">April 2026</strong>
                            </div>
                            <div class="soa-stat-row" style="margin-top:10px;">
                                <span>Water Bill Balance</span>
                                <strong>₱350.00</strong>
                            </div>
                            <div class="soa-stat-row">
                                <span>Rent Balance</span>
                                <strong>₱5,500.00</strong>
                            </div>
                            <div class="soa-stat-row">
                                <span>Contribution / Parking</span>
                                <strong>₱100.00</strong>
                            </div>
                        </div>

                        <div class="due-amount-box">
                            <p>Total Amount Due</p>
                            <h2 id="soa-total">₱5,950.00</h2>
                        </div>
                    </div>

                    <!-- UPLOAD FORM CARD -->
                    <div class="billing-card">
                        <div class="billing-header">
                            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;">
                                <path
                                    d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z" />
                            </svg>
                            Submit Proof of Payment
                        </div>
                        <form id="paymentForm" onsubmit="event.preventDefault(); submitPayment();">
                            <div class="form-row" style="display:flex;gap:16px;">
                                <div style="flex:1;">
                                    <label class="form-label">Amount Paid (₱) *</label>
                                    <input type="number" id="f-amount" class="form-control" placeholder="e.g. 5950.00"
                                        required step="0.01">
                                </div>
                                <div style="flex:1;">
                                    <label class="form-label">Date Paid *</label>
                                    <input type="date" id="f-date" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Payment Method *</label>
                                <select id="f-method" class="form-control" required>
                                    <option value="" disabled selected>Select method...</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Bank Transfer (BDO)">Bank Transfer (BDO)</option>
                                    <option value="Maya">Maya</option>
                                    <option value="Cash / Over-the-counter">Cash / Over-the-counter</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Reference Number *</label>
                                <input type="text" id="f-ref" class="form-control" placeholder="Transaction or Track ID"
                                    required>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Upload Proof (Image or PDF, Max 5MB) *</label>
                                <div class="file-upload-wrapper">
                                    <svg viewBox="0 0 24 24"
                                        style="width:32px;height:32px;fill:var(--text-muted);margin-bottom:8px;">
                                        <path
                                            d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.36 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z" />
                                    </svg>
                                    <p id="file-name"
                                        style="margin:0;font-size:0.85rem;color:var(--text-main);font-weight:600;">Drag
                                        & Drop or Click to Select File</p>
                                    <input type="file" id="f-file" accept=".jpg,.jpeg,.png,.pdf" required
                                        onchange="handleFile(this)">
                                </div>
                            </div>

                            <button type="submit" class="btn-submit">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;">
                                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                                </svg>
                                Submit Payment Proof
                            </button>
                        </form>
                    </div>
                </div>

                <!-- PAYMENT HISTORY -->
                <div class="history-card">
                    <div class="billing-header" style="border:none; padding:20px; margin:0;">
                        <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;">
                            <path
                                d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                        </svg>
                        Submission History
                    </div>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference No.</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- NOTIFICATION SNACKBAR -->
    <div id="toast"
        style="visibility:hidden; min-width:250px; background-color:#333; color:#fff; text-align:center; border-radius:8px; padding:16px; position:fixed; z-index:9999; bottom:30px; right:30px; font-size:0.9rem; font-weight:600; box-shadow:0 10px 30px rgba(0,0,0,0.2); transition:visibility 0.4s, opacity 0.4s; opacity:0;">
    </div>

    <script>
        // Sidebar Toggle logic
        const toggleBtn = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        toggleBtn.addEventListener('click', () => { sidebar.classList.toggle('collapsed'); });

        // Dropdown toggles
        document.querySelectorAll('.nav-dropdown-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                trigger.classList.toggle('open');
                const dd = trigger.nextElementSibling;
                if(dd) dd.classList.toggle('open');
            });
        });

        // User Setup Simulation
        const CURRENT_TENANT = {
            id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>',
            name: '<?= addslashes($_SESSION['name'] ?? "User") ?>',
            room: '101-A'
        };

        // DOM Setup
        const d = new Date();
        document.getElementById('soa-month').textContent = d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        document.getElementById('f-date').valueAsDate = d;

        function handleFile(input) {
            const fileName = input.files[0]?.name;
            const size = input.files[0]?.size;

            if (size > 5 * 1024 * 1024) {
                showToast("❌ File size must be under 5MB.", "#dc3545");
                input.value = "";
                document.getElementById('file-name').textContent = "Drag & Drop or Click to Select File";
                return;
            }
            if (fileName) {
                document.getElementById('file-name').textContent = fileName;
                document.getElementById('file-name').style.color = "var(--primary-dark)";
            }
        }

        function showToast(msg, bg) {
            const t = document.getElementById("toast");
            t.textContent = msg;
            t.style.backgroundColor = bg;
            t.style.visibility = "visible";
            t.style.opacity = "1";
            setTimeout(() => { t.style.opacity = "0"; t.style.visibility = "hidden"; }, 3500);
        }

        // Shared Logs Interop
        function logAudit(actionString) {
            const logs = JSON.parse(localStorage.getItem('mis_audit_logs') || '[]');
            logs.push({
                admin_id: CURRENT_TENANT.id,
                action: 'SUBMIT_PAYMENT',
                module: 'BILLING',
                description: actionString,
                ip_address: '10.0.0.' + Math.floor(Math.random() * 255),
                timestamp: new Date().toISOString()
            });
            localStorage.setItem('mis_audit_logs', JSON.stringify(logs));
        }

        function notifyAdmin(msg) {
            // Simulated notification trigger to the Admin module
            console.log("NOTIFY ADMIN:", msg);
        }

        function renderHistory() {
            const history = JSON.parse(localStorage.getItem('mis_proof_of_payments') || '[]');
            const myHistory = history.filter(h => h.app_id === CURRENT_TENANT.app_id || h.tenant_name === CURRENT_TENANT.name);

            const tbody = document.getElementById('history-tbody');
            if (myHistory.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">No payment submissions yet.</td></tr>';
                return;
            }

            tbody.innerHTML = myHistory.sort((a, b) => new Date(b.upload_date) - new Date(a.upload_date)).map(h => {
                let badge = 'badge-pending';
                if (h.status === 'Verified') badge = 'badge-verified';
                if (h.status === 'Rejected') badge = 'badge-rejected';

                return `
                    <tr>
                        <td>${new Date(h.upload_date).toLocaleDateString()}</td>
                        <td style="font-weight:700; color:var(--primary-dark);">₱${parseFloat(h.amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                        <td>${h.method}</td>
                        <td>${h.reference_no}</td>
                        <td><span class="${badge}">${h.status}</span></td>
                    </tr>
                `;
            }).join('');
        }

        function submitPayment() {
            const amt = document.getElementById('f-amount').value;
            const date = document.getElementById('f-date').value;
            const method = document.getElementById('f-method').value;
            const ref = document.getElementById('f-ref').value;
            const file = document.getElementById('f-file').files[0];

            if (!amt || !date || !method || !ref || !file) {
                showToast("⚠️ All fields are required to submit.", "orange");
                return;
            }

            // Secure local-storage integration bridging tenant & admin views
            const allProofs = JSON.parse(localStorage.getItem('mis_proof_of_payments') || '[]');

            const newProof = {
                proof_id: "PR-2026-" + String(allProofs.length + 1).padStart(3, '0'),
                app_id: CURRENT_TENANT.app_id,
                tenant_name: CURRENT_TENANT.name,
                amount: parseFloat(amt),
                method: method,
                reference_no: ref,
                upload_date: new Date().toISOString(),
                status: "Pending" // Flow initializes as Pending
            };

            allProofs.push(newProof);
            localStorage.setItem('mis_proof_of_payments', JSON.stringify(allProofs));

            logAudit(`Uploaded proof of payment for ₱${amt} (${method})`);
            notifyAdmin(`New Payment Proof submitted by ${CURRENT_TENANT.name} (${ref})`);

            showToast("✅ Payment Proof submitted successfully! Pending verification.", "var(--success)");

            // Notification ping for tenant
            setTimeout(() => {
                showToast(`🔔 Notice: You will be notified when the Billing Admin verifies this.`, "var(--primary)");
            }, 3500);

            // Clean form
            document.getElementById('paymentForm').reset();
            document.getElementById('file-name').textContent = "Drag & Drop or Click to Select File";
            document.getElementById('file-name').style.color = "var(--text-main)";

            renderHistory();
        }

        // Init
        renderHistory();
    </script>
</body>

</html>