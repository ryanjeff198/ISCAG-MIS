<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCAG MIS — Initial Payments</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>">
    <style>
        .payment-container { max-width: 800px; margin: 0 auto; }
        
        .payment-banner { background: linear-gradient(135deg, var(--primary-dark) 0%, #155736 100%); color: white; border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(15, 92, 58, 0.15); animation: slideUp 0.4s ease; }
        .payment-banner::after { content: ''; position: absolute; right: -30px; bottom: -30px; width: 160px; height: 160px; border-radius: 50%; background: linear-gradient(135deg, rgba(201, 168, 76, 0.1), rgba(201, 168, 76, 0.02)); border: 1px solid rgba(201, 168, 76, 0.2); }
        .payment-banner-title { font-family: 'Lora', serif; font-size: 1.6rem; font-weight: 700; margin: 0 0 8px; position: relative; z-index: 1; }
        .payment-banner-subtitle { font-size: 0.9rem; color: rgba(255, 255, 255, 0.8); margin: 0; line-height: 1.5; position: relative; z-index: 1; max-width: 85%; }
        
        .payment-card { background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 14px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; animation: slideUp 0.4s ease 0.1s backwards; }
        .payment-header { padding: 20px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, #fbfdfc, white); display: flex; align-items: center; justify-content: space-between; }
        .payment-header-left { display: flex; align-items: center; gap: 12px; }
        .payment-icon { width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, rgba(201, 168, 76, 0.15), rgba(201, 168, 76, 0.05)); color: var(--accent); display: flex; align-items: center; justify-content: center; }
        .payment-icon svg { width: 22px; height: 22px; fill: currentColor; }
        .payment-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--primary-dark); margin: 0; }
        
        .payment-body { padding: 32px; }
        
        /* breakdown table */
        .breakdown-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .breakdown-table th { text-align: left; padding: 12px 16px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        .breakdown-table th.text-right { text-align: right; }
        .breakdown-table td { padding: 20px 16px; border-bottom: 1px dashed var(--border); vertical-align: top; }
        .breakdown-table tr:last-child td { border-bottom: none; }
        .breakdown-item-name { font-weight: 700; color: var(--text-main); font-size: 0.95rem; margin-bottom: 4px; }
        .breakdown-item-desc { font-size: 0.8rem; color: var(--text-muted); }
        .breakdown-item-price { font-family: 'Lora', serif; font-weight: 700; font-size: 1.1rem; color: var(--primary-dark); text-align: right; }
        
        .payment-status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        .payment-status-badge.pending { background: #fffbeb; color: #ca8a04; border: 1px solid #fef08a; }
        .payment-status-badge.paid { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .payment-status-badge.failed { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .payment-status-badge svg { width: 14px; height: 14px; fill: currentColor; }

        .btn-pay { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(23, 107, 69, 0.2); }
        .btn-pay:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(23, 107, 69, 0.3); }
        .btn-pay svg { width: 16px; height: 16px; fill: currentColor; }

        .total-row { background: #f8fcfb; border-radius: 12px; padding: 24px 32px; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(23, 107, 69, 0.1); margin-top: 20px; }
        .total-label { font-size: 1rem; font-weight: 700; color: var(--text-main); }
        .total-amount { font-family: 'Lora', serif; font-size: 1.8rem; font-weight: 800; color: var(--primary-dark); }
        
        .lease-active-notice { padding: 16px 24px; border-radius: 12px; background: rgba(47, 138, 96, 0.1); border: 1px solid rgba(47, 138, 96, 0.3); color: #166534; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 12px; margin-top: 24px; animation: slideUp 0.4s ease backwards; }
        .lease-active-notice svg { width: 24px; height: 24px; fill: #166534; flex-shrink: 0; }

        /* Modal styling */
        .modal-overlay { position: fixed; inset: 0; z-index: 99999; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; padding: 20px; opacity: 0; transition: opacity 0.2s; }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content { background: white; border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); overflow: hidden; }
        .modal-overlay.show .modal-content { transform: translateY(0); }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .modal-header h3 { margin: 0; font-family: 'Lora', serif; font-size: 1.2rem; color: var(--primary-dark); }
        .modal-close { background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer; }
        .modal-body { padding: 24px; }
        .payment-options { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .payment-option { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px; border: 1.5px solid var(--border); border-radius: 12px; cursor: pointer; transition: all 0.2s; }
        .payment-option:hover { border-color: var(--primary); background: #fbfdfc; }
        .payment-option.selected { border-color: var(--primary); background: rgba(23, 107, 69, 0.05); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(23, 107, 69, 0.1); }
        .payment-option svg { width: 32px; height: 32px; fill: var(--primary); }
        .payment-option span { font-size: 0.85rem; font-weight: 600; color: var(--text-main); }
        
        .mock-input { width: 100%; padding: 12px 14px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 0.9rem; margin-bottom: 24px; color: var(--text-main); }
        .mock-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1); }

        .btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, var(--primary-dark), var(--primary-light)); color: white; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 16px rgba(23, 107, 69, 0.25); }
        .btn-submit:hover { box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35); transform: translateY(-2px); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .toast-msg { position: fixed; bottom: 32px; left: 50%; transform: translateX(-50%) translateY(100px); background: #1f2e2a; color: white; padding: 14px 28px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 999999; box-shadow: 0 8px 32px rgba(0,0,0,0.3); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .toast-msg.show { transform: translateX(-50%) translateY(0); }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php $active_page = 'apartment_payment'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Initial Payments</div>
                    <div class="top-bar-subtitle">Settle your deposit and advance rent to activate your lease</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/apartment/lease') ?>" class="btn-topbar">← Back to Lease</a>
                </div>
            </div>

            <div class="page-body">
                <div class="payment-container">
                    
<?php if (!$lease || !in_array($lease['lease_status'], ['Accepted', 'Active'])): ?>
                    <div class="payment-card" style="text-align:center; padding: 60px 24px;">
                        <svg viewBox="0 0 24 24" style="width:64px;height:64px;fill:var(--border);margin-bottom:16px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <h3 style="font-family:'Lora',serif; color:var(--text-main); margin-bottom:8px;">Payments Not Available</h3>
                        <p style="color:var(--text-muted); font-size:0.9rem;">You must have an <strong>Accepted</strong> lease to view and settle your initial payments.</p>
                        <a href="<?= url('/user/apartment/lease') ?>" class="btn-pay" style="margin-top:16px;">View Lease Contract</a>
                    </div>
<?php else: ?>
                    
                    <div class="payment-banner">
                        <h2 class="payment-banner-title">Welcome to ISCAG!</h2>
                        <p class="payment-banner-subtitle">To finalize your application and activate your lease for <strong><?= htmlspecialchars($lease['roomtype']) ?></strong>, please complete your initial payment breakdown below.</p>
                    </div>

                    <div class="payment-card">
                        <div class="payment-header">
                            <div class="payment-header-left">
                                <div class="payment-icon"><svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg></div>
                                <h3 class="payment-title">Payment Breakdown</h3>
                            </div>
                        </div>

                        <div class="payment-body">
                            <table class="breakdown-table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th class="text-right">Action</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $totalDue = 0;
                                        $allPaid = true;
                                        foreach ($payments as $pay): 
                                            $isPaid = $pay['payment_status'] === 'Paid';
                                            $isFailed = $pay['payment_status'] === 'Failed';
                                            if (!$isPaid) {
                                                $totalDue += (float)$pay['amount'];
                                                $allPaid = false;
                                            }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="breakdown-item-name"><?= $pay['payment_type'] === 'Deposit' ? 'Security Deposit' : 'Advance Rent' ?></div>
                                            <div class="breakdown-item-desc">
                                                <?php if($pay['payment_type'] === 'Deposit') echo "Refundable at end of lease"; else echo "Equivalent to 1 month rent"; ?>
                                                <?php if($isPaid && $pay['reference_number']) echo "<br/><span style='color:#166534;font-size:0.75rem;font-weight:600;'>Ref: {$pay['reference_number']}</span>"; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($isPaid): ?>
                                                <div class="payment-status-badge paid"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> PAID</div>
                                            <?php elseif ($isFailed): ?>
                                                <div class="payment-status-badge failed"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> FAILED</div>
                                            <?php else: ?>
                                                <div class="payment-status-badge pending"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg> PENDING</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <?php if (!$isPaid): ?>
                                                <button class="btn-pay" onclick="openPaymentModal(<?= $pay['payment_id'] ?>, '<?= $pay['payment_type'] ?>', <?= $pay['amount'] ?>)">
                                                    Pay Now
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <div class="breakdown-item-price">₱<?= number_format($pay['amount'], 2) ?></div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="total-row">
                                <div class="total-label"><?= $allPaid ? 'Balance' : 'Total Amount Due' ?></div>
                                <div class="total-amount">₱<?= number_format($totalDue, 2) ?></div>
                            </div>
                            
                            <?php if ($allPaid || $lease['lease_status'] === 'Active'): ?>
                            <div class="lease-active-notice">
                                <svg viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                Thank you! Your initial payments are fully settled. Your lease contract is now ACTIVATED. You can proceed with Room Assignment info.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

<?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Submit Payment</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin: 0 0 4px; color: var(--text-muted); font-size: 0.85rem;">Payment For:</p>
                <h2 style="margin: 0 0 20px; color: var(--primary-dark); font-family: 'Lora', serif;" id="modalPayType">Deposit</h2>
                
                <p style="margin: 0 0 10px; font-weight: 600; font-size: 0.9rem;">Select Payment Method</p>
                <div class="payment-options">
                    <div class="payment-option selected" id="opt-gcash" onclick="selectMethod('gcash')">
                        <svg viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                        <span>GCash</span>
                    </div>
                    <div class="payment-option" id="opt-bank" onclick="selectMethod('bank')">
                        <svg viewBox="0 0 24 24"><path d="M12 3L1 9h4v12h14V9h4L12 3zm-1 16H8v-7h3v7zm6 0h-3v-7h3v7z"/></svg>
                        <span>Bank Transfer</span>
                    </div>
                </div>

                <p style="margin: 0 0 6px; font-weight: 600; font-size: 0.9rem;">Reference Number / Receipt ID</p>
                <input type="text" id="refNumber" class="mock-input" placeholder="e.g. 100234567890" autocomplete="off">

                <button class="btn-submit" id="btnConfirmPay" onclick="submitPayment()">Confirm Payment of ₱<span id="modalAmount">0</span></button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-msg" id="toastMsg">Payment Successful!</div>

    <script>
        let currentPaymentId = 0;
        let selectedMethod = 'gcash';

        function showToast(msg, isErr = false) {
            const t = document.getElementById('toastMsg');
            t.textContent = msg;
            t.style.background = isErr ? '#991b1b' : '#166534';
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        function openPaymentModal(pid, typeName, amount) {
            currentPaymentId = pid;
            document.getElementById('modalPayType').textContent = (typeName === 'Deposit' ? 'Security Deposit' : 'Advance Rent');
            document.getElementById('modalAmount').textContent = parseFloat(amount).toFixed(2);
            document.getElementById('refNumber').value = 'PAY-' + Math.floor(Math.random() * 10000000); // Mock Ref
            document.getElementById('paymentModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.remove('show');
            currentPaymentId = 0;
        }

        function selectMethod(method) {
            selectedMethod = method;
            document.getElementById('opt-gcash').classList.remove('selected');
            document.getElementById('opt-bank').classList.remove('selected');
            document.getElementById('opt-' + method).classList.add('selected');
        }

        function submitPayment() {
            const refNo = document.getElementById('refNumber').value.trim();
            if (!refNo) {
                showToast('Please enter a reference number.', true);
                return;
            }

            const btn = document.getElementById('btnConfirmPay');
            btn.disabled = true;
            btn.innerHTML = 'Processing...';

            fetch('<?= url("/user/apartment/payment/submit") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payment_id: currentPaymentId, reference: refNo })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('Payment confirmed successfully!');
                    closeModal();
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showToast(res.message || 'Payment failed.', true);
                    btn.disabled = false;
                    btn.innerHTML = 'Confirm Payment of ₱<span id="modalAmount">' + document.getElementById('modalAmount').textContent + '</span>';
                }
            })
            .catch(err => {
                showToast('Network error.', true);
                btn.disabled = false;
                btn.innerHTML = 'Confirm Payment';
            });
        }
    </script>
</body>
</html>
