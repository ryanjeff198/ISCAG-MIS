<?php 
// ═══════════════════════════════════════════
//  LOAD SESSION & DATA
// ═══════════════════════════════════════════
require_once BASE_PATH . '/app/models/ApartmentApp.php';
$model = new ApartmentApp();
$userId = $_SESSION['user_id'];

// Fetch data for the dashboard
$parkingApps = $model->getParkingApplicationsByTenant($userId);
$hasPending = $model->hasPendingParkingApplication($userId);

// Fetch user info for pre-filling (if needed)
require_once BASE_PATH . '/app/models/User.php';
$userModel = new User();
$account = $userModel->findById($userId);
$info = $userModel->getAdditionalInfo($userId);
$fullName = ($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? '');
$dob = $info['birthdate'] ?? '';

// Fetch room assignment info
require_once BASE_PATH . '/app/models/ApartmentApp.php';
$apaModel = new ApartmentApp();
$appInfo = $apaModel->getApplication($userId);
$assignedRoom = $appInfo['room_number'] ?? 'N/A';
$assignedBldg = $appInfo['building'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Parking Dashboard</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        /* ── DASHBOARD LAYOUT ── */
        .info-container { max-width: 1100px; margin: 0 auto; padding: 24px; }

        /* ── Vehicle Table Container ── */
        .dashboard-card {
            background: white; border-radius: 16px; border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04); overflow: hidden;
            animation: slideUp 0.4s ease;
        }
        .card-header {
            padding: 24px 32px; background: #fcfdfc; border-bottom: 1.5px solid #f0f0f0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title-box { display: flex; align-items: center; gap: 16px; }
        .card-icon {
            width: 44px; height: 44px; border-radius: 12px; background: var(--primary-dark);
            display: flex; align-items: center; justify-content: center; color: white;
        }
        .card-title { font-family: 'Lora', serif; font-size: 1.25rem; font-weight: 800; color: var(--primary-dark); margin: 0; }
        
        /* ── Table Styling ── */
        .parking-table { width: 100%; border-collapse: collapse; }
        .parking-table th {
            padding: 16px 24px; background: #f8faf9; text-align: left;
            font-size: 0.72rem; font-weight: 800; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 1.5px solid #f0f0f0;
        }
        .parking-table td { padding: 20px 24px; border-bottom: 1px solid #f4f6f5; vertical-align: middle; }
        .vehicle-info-cell { display: flex; align-items: center; gap: 14px; }
        .vehicle-avatar {
            width: 40px; height: 40px; border-radius: 10px; background: #f0f4f3;
            display: flex; align-items: center; justify-content: center; color: var(--primary);
        }
        .vehicle-name-text { display: block; font-weight: 700; color: var(--text-main); margin-bottom: 2px; }
        .vehicle-type-tag { font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
        
        .plate-badge {
            font-family: 'Source Code Pro', monospace; font-weight: 800; font-size: 0.9rem;
            background: #f4f6f5; color: #333; padding: 4px 10px; border-radius: 6px;
            border: 1px solid #e0e6e4; letter-spacing: 0.05em;
        }

        .status-pill {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
            border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
        }
        .status-pill.approved { background: rgba(47, 138, 96, 0.1); color: #2f8a60; }
        .status-pill.pending { background: rgba(199, 154, 43, 0.1); color: #c79a2b; }
        .status-pill.rejected { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }

        .btn-action-sm {
            padding: 8px 14px; border-radius: 8px; border: 1.5px solid #eef2f1;
            background: white; color: #666; font-size: 0.85rem; font-weight: 700;
            cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-action-sm:hover { border-color: var(--primary); color: var(--primary); background: #f8faf9; }

        /* ── Registration Form (Document Style) ── */
        .form-doc {
            background: white; border-radius: 16px; border: 1.5px solid var(--border);
            padding: 40px; margin-top: 32px; box-shadow: 0 8px 30px rgba(0,0,0,0.03);
            position: relative; overflow: hidden;
        }
        .form-doc::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--primary-dark); }
        .section-title {
            font-size: 0.75rem; font-weight: 900; color: var(--primary-dark);
            text-transform: uppercase; letter-spacing: 0.15em; margin: 24px 0 16px;
            display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #f4f6f5; padding-bottom: 10px;
        }
        
        .vehicle-block {
            background: #fcfdfc; border: 1.5px solid #f0f4f3; border-radius: 12px;
            padding: 24px; margin-bottom: 20px; position: relative;
        }
        .btn-remove-vehicle {
            position: absolute; top: 12px; right: 12px; background: #fff1f0;
            color: #f5222d; border: 1px solid #ffa39e; border-radius: 6px;
            width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-remove-vehicle:hover { background: #f5222d; color: white; }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 16px; }
        .form-group label { display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
        .form-control {
            width: 100%; padding: 12px 16px; border: 1.5px solid #e0e6e4; border-radius: 8px;
            font-size: 0.95rem; font-weight: 600; color: var(--text-main); transition: all 0.2s;
        }
        .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(47, 138, 96, 0.1); }
        .form-control[readonly] { background: #f8faf9; cursor: not-allowed; }

        /* ── Modals ── */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 34, 31, 0.7); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center;
            z-index: 2000; animation: fadeIn 0.3s ease;
        }
        .modal-overlay.active { display: flex; }
        .modal-container {
            background: white; border-radius: 20px; width: 100%; max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-header {
            padding: 24px 32px; border-bottom: 1.5px solid #f0f0f0;
            display: flex; align-items: center; justify-content: space-between;
            background: #fcfdfc;
        }
        .modal-header h3 { font-family: 'Lora', serif; font-size: 1.3rem; font-weight: 800; color: var(--primary-dark); margin: 0; }
        .btn-close-modal { background: none; border: none; font-size: 1.8rem; color: #ccc; cursor: pointer; transition: color 0.2s; }
        .btn-close-modal:hover { color: #666; }

        /* ── Permit Card ── */
        .permit-card {
            background: #f8faf9; border: 2px dashed #d1dbd8; border-radius: 12px;
            padding: 32px; margin: 32px; position: relative;
        }
        .permit-watermark {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 4rem; font-weight: 900; color: rgba(47, 138, 96, 0.03); pointer-events: none;
        }

        /* ── Toast ── */
        .toast {
            position: fixed; top: 24px; right: 24px; padding: 16px 24px; border-radius: 12px;
            background: #333; color: white; z-index: 3000; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            font-weight: 600; display: none; animation: slideUp 0.3s ease;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

<body>
    <!-- ═══ PERMIT DETAILS MODAL ═══ -->
    <div class="modal-overlay" id="permit-modal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Vehicle Permit Details</h3>
                <button class="btn-close-modal" onclick="closeModal('permit-modal')">&times;</button>
            </div>
            <div id="print-area">
                <div class="permit-card">
                    <div class="permit-watermark">ISCAG OFFICIAL</div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; border-bottom:1.5px solid #d1dbd8; padding-bottom:16px;">
                        <img src="<?= asset('assets/logo.jpg') ?>" style="height:50px; border-radius:6px;" />
                        <div style="text-align:right;">
                            <div id="p-status" class="status-pill approved">Verified</div>
                            <div style="font-size:0.65rem; color:#999; margin-top:4px; font-weight:800;">PERMIT ID: <span id="p-id">#PKG-0000</span></div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                        <div><label style="display:block; font-size:0.7rem; color:#999; font-weight:800; text-transform:uppercase;">Vehicle</label><span id="p-vehicle" style="font-weight:700; color:#333;">Toyota Vios</span></div>
                        <div><label style="display:block; font-size:0.7rem; color:#999; font-weight:800; text-transform:uppercase;">Plate No.</label><span id="p-plate" class="plate-badge">ABC 1234</span></div>
                        <div><label style="display:block; font-size:0.7rem; color:#999; font-weight:800; text-transform:uppercase;">Owner</label><span id="p-owner" style="font-weight:700; color:#333;">Muhammad Usman</span></div>
                        <div><label style="display:block; font-size:0.7rem; color:#999; font-weight:800; text-transform:uppercase;">Type</label><span id="p-type" style="font-weight:700; color:#333;">Sedan</span></div>
                    </div>
                </div>
            </div>
            <div style="padding: 0 32px 32px; display:flex; gap:12px; justify-content:center;">
                <button class="btn-action-sm" onclick="window.print()"><svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Print</button>
                <button class="btn-action-sm" onclick="downloadPermit()"><svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg> Download</button>
            </div>
        </div>
    </div>

    <!-- ═══ APP WRAPPER ═══ -->
    <div class="app-wrapper">
        <?php 
          $active_page = 'apartment_parking'; 
          include BASE_PATH . '/app/views/user/sidebar.php'; 
        ?>

        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Parking Management</div>
                    <div class="top-bar-subtitle">Manage your vehicle access and permits</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
                </div>
            </div>

            <div class="info-container">
                
                <?php if ($hasPending): ?>
                    <div style="background: #fffbe6; border: 1px solid #ffe58f; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; color: #856404; font-weight: 600;">
                        <svg style="width:24px;height:24px;fill:currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <span>You have a pending parking application. Please wait for approval before submitting more vehicles.</span>
                    </div>
                <?php endif; ?>

                <!-- ═══ DASHBOARD TABLE ═══ -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <div class="card-title-box">
                            <div class="card-icon"><svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg></div>
                            <h3 class="card-title">Registered Vehicles & Permits</h3>
                        </div>
                    </div>
                    <?php if (empty($parkingApps)): ?>
                        <div style="padding: 60px; text-align: center;">
                            <img src="<?= asset('assets/icons/car-empty.svg') ?>" style="width: 80px; opacity: 0.2; margin-bottom: 20px;" />
                            <p style="color: var(--text-muted); font-weight: 600;">No vehicles registered yet. Use the form below to apply.</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="parking-table">
                                <thead>
                                    <tr>
                                        <th>Vehicle Details</th>
                                        <th>Plate Number</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($parkingApps as $app): ?>
                                        <?php 
                                            $status = strtoupper($app['status'] ?? 'PENDING');
                                            $pillClass = ($status === 'APPROVED') ? 'approved' : (($status === 'REJECTED') ? 'rejected' : 'pending');
                                            $appJson = htmlspecialchars(json_encode($app), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="vehicle-info-cell">
                                                    <div class="vehicle-avatar"><svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg></div>
                                                    <div>
                                                        <span class="vehicle-name-text"><?= htmlspecialchars($app['vehiclename']) ?></span>
                                                        <span class="vehicle-type-tag"><?= htmlspecialchars($app['typeofvehicle']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="plate-badge"><?= htmlspecialchars($app['plateno']) ?></span></td>
                                            <td><span class="status-pill <?= $pillClass ?>"><?= $status ?></span></td>
                                            <td style="text-align: right;">
                                                <button class="btn-action-sm" onclick='openPermit(<?= $appJson ?>)'>View Permit</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ═══ REGISTRATION FORM ═══ -->
                <?php if (!$hasPending): ?>
                <form id="parking-form" class="form-doc">
                    <div class="section-title">Personal Information</div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($fullName) ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($dob) ?>" readonly />
                        </div>
                    </div>

                    <div class="section-title">Address Details</div>
                    <div class="form-grid" style="grid-template-columns: repeat(4, 1fr);">
                        <div class="form-group">
                            <label>Room No.</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($assignedRoom) ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label>Bldg No.</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($assignedBldg) ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label>Barangay</label>
                            <input type="text" class="form-control" value="Salitran I" readonly />
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" class="form-control" value="Dasmariñas City" readonly />
                        </div>
                    </div>

                    <div class="section-title">Parking Details</div>
                    <div class="form-grid">
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Date Started</label>
                            <input type="date" id="date-started" class="form-control" value="<?= date('Y-m-d') ?>" />
                        </div>
                    </div>

                    <div class="section-title" style="margin-top: 40px; display:flex; justify-content:space-between; align-items:center;">
                        <span>Vehicle Details</span>
                        <button type="button" onclick="addVehicleBlock()" class="btn-action-sm" style="background: var(--primary); color: white; border: none;">+ Add Vehicle</button>
                    </div>

                    <div id="vehicles-container">
                        <!-- Vehicle blocks injected here -->
                    </div>

                    <div style="margin-top: 40px; text-align: right; border-top: 1.5px solid #f0f0f0; padding-top: 32px;">
                        <button type="submit" id="btn-submit" class="btn-action-sm" style="padding: 14px 40px; background: var(--primary-dark); color: white; border: none; font-size: 1rem;">Submit Application</button>
                    </div>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        // ── VEHICLE MANAGEMENT ──
        let vehicleCount = 0;

        function addVehicleBlock() {
            vehicleCount++;
            const container = document.getElementById('vehicles-container');
            const block = document.createElement('div');
            block.className = 'vehicle-block';
            block.id = `v-block-${vehicleCount}`;
            block.innerHTML = `
                ${vehicleCount > 1 ? `<button type="button" class="btn-remove-vehicle" onclick="removeVehicleBlock(${vehicleCount})">&times;</button>` : ''}
                <div class="vehicle-number" style="font-weight: 800; color: var(--primary-dark); margin-bottom: 20px; font-size: 0.9rem;">Vehicle #</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Vehicle Name</label>
                        <input type="text" class="form-control v-name" placeholder="e.g. Toyota Vios" required />
                    </div>
                    <div class="form-group">
                        <label>Owner Name</label>
                        <input type="text" class="form-control v-owner" placeholder="Enter owner's name" required />
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Vehicle Type</label>
                        <select class="form-control v-type" required>
                            <option value="Sedan">Sedan</option>
                            <option value="SUV">SUV / AUV</option>
                            <option value="Pickup">Pickup Truck</option>
                            <option value="Van">Van / MPV</option>
                            <option value="Hatchback">Hatchback</option>
                            <option value="Wagon">Station Wagon</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Plate Number</label>
                        <input type="text" class="form-control v-plate" placeholder="ABC 1234" style="text-transform: uppercase;" required />
                    </div>
                </div>
            `;
            container.appendChild(block);
            resequenceVehicles();
            if (vehicleCount > 1) {
                showToast(`Vehicle fields added to form`, 'var(--primary-dark)');
            }
        }

        function removeVehicleBlock(id) {
            const block = document.getElementById(`v-block-${id}`);
            if (block) block.remove();
            resequenceVehicles();
        }

        function resequenceVehicles() {
            const blocks = document.querySelectorAll('.vehicle-block');
            blocks.forEach((block, index) => {
                const num = index + 1;
                block.querySelector('.vehicle-number').textContent = `Vehicle #${num}`;
            });
        }

        // Initialize with one vehicle
        if (document.getElementById('vehicles-container')) {
            addVehicleBlock();
        }

        // ── FORM SUBMISSION ──
        const form = document.getElementById('parking-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btn-submit');
                btn.disabled = true;
                btn.textContent = 'Submitting...';

                const vehicles = [];
                document.querySelectorAll('.vehicle-block').forEach(block => {
                    vehicles.push({
                        vehicleName: block.querySelector('.v-name').value,
                        vehicleOwner: block.querySelector('.v-owner').value,
                        vehicleType: block.querySelector('.v-type').value,
                        plateNo: block.querySelector('.v-plate').value
                    });
                });

                const payload = {
                    dateStarted: document.getElementById('date-started').value,
                    vehicles: vehicles
                };

                fetch('<?= url("/user/apartment/parking/submit") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Application submitted successfully!', 'var(--success)');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Error submitting application.', '#f5222d');
                        btn.disabled = false;
                        btn.textContent = 'Submit Application';
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Network error. Please try again.', '#f5222d');
                    btn.disabled = false;
                    btn.textContent = 'Submit Application';
                });
            });
        }

        // ── PERMIT MODAL ──
        function openPermit(app) {
            document.getElementById('p-id').textContent = '#PKG-' + String(app.parking_id).padStart(4, '0');
            document.getElementById('p-vehicle').textContent = app.vehiclename;
            document.getElementById('p-plate').textContent = app.plateno;
            document.getElementById('p-owner').textContent = app.ownername || 'Registered Tenant';
            document.getElementById('p-type').textContent = app.typeofvehicle;
            
            const status = (app.status || 'PENDING').toUpperCase();
            const pill = document.getElementById('p-status');
            pill.textContent = status;
            pill.className = 'status-pill ' + status.toLowerCase();
            
            document.getElementById('permit-modal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function downloadPermit() {
            const area = document.getElementById('print-area');
            html2canvas(area, { scale: 2, useCORS: true }).then(canvas => {
                const link = document.createElement('a');
                link.download = `Permit-${document.getElementById('p-plate').textContent}.png`;
                link.href = canvas.toDataURL();
                link.click();
            });
        }

        function showToast(msg, bg) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.style.background = bg;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        // Close modal on outside click
        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>