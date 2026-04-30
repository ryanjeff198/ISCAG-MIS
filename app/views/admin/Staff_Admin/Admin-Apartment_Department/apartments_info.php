<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Apartment Management</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0f5c3a 0%, #1a8e5f 100%);
            --accent-gradient: linear-gradient(135deg, #c79a2b 0%, #e5b95d 100%);
        }

        /* Apartment Types Grid */
        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .type-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .type-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            border-color: var(--primary-light);
        }

        .type-card-image {
            height: 180px;
            background: #f0f2f1;
            position: relative;
            overflow: hidden;
        }

        .type-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .type-card:hover .type-card-image img {
            transform: scale(1.1);
        }

        .type-card-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(15, 92, 58, 0.9);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            backdrop-filter: blur(4px);
        }

        .type-card-body {
            padding: 20px;
            flex: 1;
        }

        .type-card-title {
            font-family: 'Lora', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0 0 8px;
        }

        .type-card-price {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 12px;
        }

        .type-card-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .type-card-stat {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .type-card-stat svg {
            width: 14px;
            height: 14px;
            fill: currentColor;
        }

        .type-card-footer {
            padding: 16px 20px;
            background: #f9fafb;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
        }

        /* Table & Forms */
        .section-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-available { background: rgba(46, 125, 85, 0.1); color: var(--success); }
        .badge-occupied { background: rgba(199, 154, 43, 0.1); color: var(--accent); }
        .badge-reserved { background: rgba(30, 95, 139, 0.1); color: var(--info); }
        .badge-maintenance { background: rgba(139, 46, 46, 0.1); color: var(--danger); }

        /* Image Gallery in Modal */
        .btn-delete {
            border-color: rgba(220, 53, 69, 0.3) !important;
            color: var(--danger) !important;
            background: rgba(220, 53, 69, 0.05) !important;
        }
        .btn-delete:hover {
            background: var(--danger) !important;
            color: white !important;
            border-color: var(--danger) !important;
        }
        .btn-delete:hover svg {
            fill: white !important;
        }
        
        .btn-edit {
            color: var(--primary) !important;
            border-color: rgba(47, 138, 96, 0.3) !important;
            background: rgba(47, 138, 96, 0.05) !important;
        }
        .btn-edit:hover {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
        }
        .btn-edit:hover svg {
            fill: white !important;
        }
        
        .image-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 1;
            border: 2px solid transparent;
        }

        .gallery-item.is-thumb {
            border-color: var(--accent);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-item-actions {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .gallery-item:hover .gallery-item-actions {
            opacity: 1;
        }

        .btn-gallery-action {
            background: white;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-gallery-action:hover {
            transform: scale(1.1);
        }

        .btn-gallery-action.delete { color: var(--danger); }
        .btn-gallery-action.thumb { color: var(--accent); }

        .upload-placeholder {
            border: 2px dashed var(--border);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-placeholder:hover {
            border-color: var(--primary);
            background: rgba(23, 107, 69, 0.02);
        }

        /* Form Row Helper */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group.full { grid-column: 1 / -1; }
        .form-label { display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 700; color: var(--text-main); }
        .form-control { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 0.9rem; transition: all 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1); }

        /* Tabs System */
        .management-tabs {
            display: flex;
            gap: 24px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
            padding: 0 4px;
        }

        .tab-btn {
            padding: 12px 16px;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-muted);
            border: none;
            background: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .tab-btn:hover {
            color: var(--primary);
        }

        .tab-btn.active {
            color: var(--primary);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary);
            border-radius: 3px 3px 0 0;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        /* Pagination */
        .pagination-container {
            padding: 12px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: center;
            background: #fdfdfd;
        }
        .pagination-btns {
            display: flex;
            gap: 6px;
        }
        .btn-page {
            padding: 6px 12px;
            border: 1px solid var(--border);
            background: white;
            color: var(--text-muted);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-page:hover:not(:disabled) {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(23, 107, 69, 0.05);
        }
        .btn-page.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .btn-page:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <?php 
          $active_page = 'info';
          include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
        ?>

        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Apartment Management</div>
                    <div class="top-bar-subtitle">Manage apartment types, images, and individual units.</div>
                </div>
                <div class="top-bar-actions">
                    <button class="btn-topbar primary" onclick="openTypeModal('add')">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" /></svg>
                        Add New Type
                    </button>
                    <button class="btn-topbar" onclick="openUnitModal('add')">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" /></svg>
                        Add Room/Unit
                    </button>
                </div>
            </div>

            <div class="page-body">
                <div class="breadcrumb-bar">
                    <a href="<?= url('/admin/apartment') ?>">Dashboard</a>
                    <span class="sep">›</span>
                    <span class="current">Apartment Management</span>
                </div>

                <!-- Tabs Navigation -->
                <div class="management-tabs">
                    <button class="tab-btn active" onclick="switchTab('types')">Apartment Types</button>
                    <button class="tab-btn" onclick="switchTab('units')">Registered Units</button>
                </div>

                <!-- Section: Apartment Types -->
                <div id="types-tab" class="tab-content active">
                    <div class="section-header-row">
                        <h5 style="font-family:'Lora',serif; font-weight:700; color:var(--primary-dark); margin:0;">Apartment Types & Gallery</h5>
                    </div>
                    <div class="types-grid" id="types-grid">
                        <!-- Loaded via API -->
                        <div style="grid-column:1/-1; text-align:center; padding:40px;">
                            <div class="loader" style="margin:0 auto 20px;"></div>
                            <p style="color:var(--text-muted);">Loading apartment types...</p>
                        </div>
                    </div>
                </div>

                <!-- Section: Registered Units -->
                <div id="units-tab" class="tab-content">
                    <div class="section-card">
                        <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <h6>
                                <svg viewBox="0 0 24 24"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" /></svg>
                                Registered Room Units
                            </h6>
                            <div style="display:flex; gap:8px; align-items:center;">
                                <select id="building-filter" class="form-control" style="width:auto; min-width:160px; font-size:0.82rem; padding:6px 12px;">
                                    <option value="">All Buildings</option>
                                </select>
                            </div>
                        </div>
                        <div class="section-card-body" style="padding:0;">
                            <div class="table-wrapper">
                                <table class="mis-table">
                                    <thead>
                                        <tr>
                                            <th>Unit ID</th>
                                            <th>Floor</th>
                                            <th>Type</th>
                                            <th>Price</th>
                                            <th>Tenant</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="units-tbody">
                                        <!-- Loaded via API -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="pagination-units" class="pagination-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ MODAL: MANAGE TYPE ═══ -->
    <div class="modal-backdrop" id="type-modal" style="display:none;">
        <div class="modal-content" style="max-width:800px;">
            <div class="modal-bar"></div>
            <div class="modal-header">
                <h5 id="type-modal-title">Apartment Type Details</h5>
                <button class="modal-close" onclick="closeModal('type-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="type-form">
                    <input type="hidden" id="t-id" name="type_id">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Type Label</label>
                            <input type="text" class="form-control" name="label" id="t-label" placeholder="e.g. Studio Unit" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type Key (unique identifier)</label>
                            <input type="text" class="form-control" name="type_key" id="t-key" placeholder="e.g. studio" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Monthly Price (₱)</label>
                            <input type="number" class="form-control" name="price" id="t-price" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Capacity</label>
                            <input type="text" class="form-control" name="capacity" id="t-capacity" placeholder="e.g. 1-2 persons">
                        </div>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="t-desc"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Floor Area</label><input type="text" class="form-control" name="floor_area" id="t-area"></div>
                        <div class="form-group"><label class="form-label">Bedrooms</label><input type="text" class="form-control" name="bedrooms" id="t-bedrooms"></div>
                        <div class="form-group"><label class="form-label">Bathroom</label><input type="text" class="form-control" name="bathroom" id="t-bathroom"></div>
                    </div>

                    <!-- 📜 DYNAMIC CONFIGURATION SECTIONS -->
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #f1f5f9;">
                        <h6 style="font-family:'Lora',serif; color:var(--primary-dark); font-weight:700; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                            <svg viewBox="0 0 24 24" style="width:18px; fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            Detailed Configuration
                        </h6>

                        <div class="form-row">
                            <!-- Inclusions Management -->
                            <div class="form-group">
                                <label class="form-label">Room Inclusions</label>
                                <div id="inclusions-container" style="display:flex; flex-direction:column; gap:8px; margin-bottom:10px;">
                                    <!-- Dynamic Inputs -->
                                </div>
                                <button type="button" class="btn-topbar" style="font-size:0.75rem; width:100%; border-style:dashed;" onclick="addDynamicInput('inclusions')">
                                    + Add Inclusion (e.g. Wi-Fi)
                                </button>
                            </div>

                            <!-- Rules Management -->
                            <div class="form-group">
                                <label class="form-label">Apartment Rules</label>
                                <div id="rules-container" style="display:flex; flex-direction:column; gap:8px; margin-bottom:10px;">
                                    <!-- Dynamic Inputs -->
                                </div>
                                <button type="button" class="btn-topbar" style="font-size:0.75rem; width:100%; border-style:dashed;" onclick="addDynamicInput('rules')">
                                    + Add Rule (e.g. No Pets)
                                </button>
                            </div>
                        </div>

                        <div class="form-row" style="margin-top:20px;">
                            <!-- Payment Configuration -->
                            <div class="form-group">
                                <label class="form-label">Payment & Deposit Settings</label>
                                <div style="background:#f8fafc; padding:15px; border-radius:12px; border:1px solid #e2e8f0;">
                                    <div style="margin-bottom:12px;">
                                        <label style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase;">Security Deposit</label>
                                        <input type="text" class="form-control" name="security_deposit" id="t-deposit" placeholder="e.g. 1 Month Deposit">
                                    </div>
                                    <div style="margin-bottom:12px;">
                                        <label style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase;">Advance Payment</label>
                                        <input type="text" class="form-control" name="advance_rent" id="t-advance" placeholder="e.g. 1 Month Advance">
                                    </div>
                                    <div>
                                        <label style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase;">Other Initial Fees</label>
                                        <input type="text" class="form-control" name="other_fees" id="t-fees" placeholder="e.g. Utility Deposit (P500)">
                                    </div>
                                </div>
                            </div>

                            <!-- Lease & Occupancy -->
                            <div class="form-group">
                                <label class="form-label">Lease & Availability Info</label>
                                <div style="background:#f8fafc; padding:15px; border-radius:12px; border:1px solid #e2e8f0;">
                                    <div style="margin-bottom:12px;">
                                        <label style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase;">Minimum Stay</label>
                                        <input type="text" class="form-control" name="min_lease" id="t-min-lease" placeholder="e.g. 6 Months">
                                    </div>
                                    <div style="margin-bottom:12px;">
                                        <label style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase;">Notice Period</label>
                                        <input type="text" class="form-control" name="notice_period" id="t-notice" placeholder="e.g. 30 Days before moving">
                                    </div>
                                    <div>
                                        <label style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase;">Queue / Occupancy Label</label>
                                        <input type="text" class="form-control" name="queue_label" id="t-queue" placeholder="e.g. 3 slots remaining">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail Section -->
                    <div class="form-group full" style="margin-top:20px;">
                        <label class="form-label">Primary Thumbnail</label>
                        <div style="display:flex; gap:16px; align-items:flex-start;">
                            <div id="t-thumb-preview" style="width:120px; height:80px; border-radius:8px; background:#f0f2f1; border:1px solid var(--border); overflow:hidden; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <span style="font-size:0.7rem; color:var(--text-muted);">No image</span>
                            </div>
                            <div style="flex:1;">
                                <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:8px;">This is the main image shown in the listing view.</p>
                                <button type="button" class="btn-topbar" style="font-size:0.75rem; padding:6px 12px;" onclick="document.getElementById('thumb-upload').click()">
                                    <svg viewBox="0 0 24 24" style="width:14px; margin-right:4px;"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z"/></svg>
                                    Upload Thumbnail
                                </button>
                                <input type="file" id="thumb-upload" accept="image/*" style="display:none;" onchange="handleThumbUpload(this)">
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Slides Section -->
                    <div id="type-gallery-section" style="margin-top:24px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <label class="form-label" style="margin:0;">Carousel Slides</label>
                            <button type="button" class="btn-topbar" style="font-size:0.75rem; padding:6px 12px;" onclick="document.getElementById('gallery-upload').click()">
                                <svg viewBox="0 0 24 24" style="width:14px; margin-right:4px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                Add Slides
                            </button>
                        </div>
                        <div class="image-gallery-grid" id="image-gallery-grid">
                            <!-- JS populated -->
                        </div>
                        <input type="file" id="gallery-upload" multiple accept="image/*" style="display:none;" onchange="handleGalleryUpload(this)">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-topbar" onclick="closeModal('type-modal')">Cancel</button>
                <button class="btn-topbar primary" id="btn-save-type" onclick="saveType()">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- ═══ MODAL: MANAGE UNIT ═══ -->
    <div class="modal-backdrop" id="unit-modal" style="display:none;">
        <div class="modal-content" style="max-width:500px;">
            <div class="modal-bar"></div>
            <div class="modal-header">
                <h5 id="unit-modal-title">Room Unit Details</h5>
                <button class="modal-close" onclick="closeModal('unit-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="unit-form">
                    <input type="hidden" id="u-id" name="unit_id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Apartment Type</label>
                            <select class="form-control" name="type_id" id="u-type" required>
                                <!-- JS populated -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Building</label>
                            <select class="form-control" name="building" id="u-building" onchange="updateGeneratedCode()" required>
                                <option value="Building 1">Building 1</option>
                                <option value="Building 2">Building 2</option>
                                <option value="Building 3">Building 3</option>
                                <option value="Building 4">Building 4</option>
                                <option value="Building 5">Building 5</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Floor</label>
                            <select class="form-control" id="u-floor" onchange="updateGeneratedCode()" required>
                                <option value="1">1st Floor</option>
                                <option value="2">2nd Floor</option>
                                <option value="3">3rd Floor</option>
                                <option value="4">4th Floor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Room Number (01-05)</label>
                            <input type="number" class="form-control" id="u-room-only" placeholder="01" min="1" max="5" oninput="updateGeneratedCode()" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Generated Unit Code</label>
                            <input type="text" class="form-control" id="u-preview" readonly style="background:#f1f5f9; font-weight:800; color:var(--primary); letter-spacing:1px; text-align:center; font-size:1.1rem;">
                            <input type="hidden" name="room_number" id="u-number">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" id="u-status">
                                <option value="Available">Available</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Reserved">Reserved</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Description / Internal Notes</label>
                        <textarea class="form-control" name="description" id="u-desc" style="height:60px;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-topbar" onclick="closeModal('unit-modal')">Cancel</button>
                <button class="btn-topbar primary" onclick="saveUnit()">Save Unit</button>
            </div>
        </div>
    </div>

    <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('JS/room-preview.js') ?>?v=<?= time() ?>"></script>
    <script>
        standardizePage('staff');
        syncSessionUser("<?= addslashes(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>", "<?= addslashes($dbUser['email'] ?? '') ?>", "Apartment Manager");

        let apartmentTypes = [];
        let roomUnits = [];
        let allBuildings = [];
        let currentPage = 1;
        const rowsPerPage = 10;

        function switchTab(tab) {
            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.textContent.toLowerCase().includes(tab));
            });
            // Update content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.toggle('active', content.id === tab + '-tab');
            });
            // Optional: Store in localStorage
            localStorage.setItem('active_apartment_tab', tab);
        }

        // Initialize from storage
        const savedTab = localStorage.getItem('active_apartment_tab') || 'types';
        if (savedTab !== 'types') switchTab(savedTab);

        function formatFloor(roomNum) {
            if (!roomNum) return '—';
            let rNumOnly = roomNum.toString().replace(/\D/g, '');
            let floorDigit = '1';
            if (rNumOnly.length >= 3) {
                floorDigit = rNumOnly.charAt(0);
            }
            const n = parseInt(floorDigit);
            const s = ["th", "st", "nd", "rd"],
                  v = n % 100;
            const suffix = (s[(v - 20) % 10] || s[v] || s[0]);
            return n + suffix + " Floor";
        }

        async function init() {
            await loadData();
        }

        async function loadData() {
            try {
                const [typesRes, unitsRes] = await Promise.all([
                    fetch('<?= url("/api/apartment-types") ?>').then(r => r.json()),
                    fetch('<?= url("/api/apartment-units") ?>').then(r => r.json())
                ]);

                if (typesRes.success) {
                    apartmentTypes = typesRes.data;
                    renderTypes();
                    populateTypeDropdowns();
                }
                if (unitsRes.success) {
                    roomUnits = unitsRes.data.units.map(u => {
                        // Calculate strict 4-digit Display ID: [BuildingDigit][FloorDigit][RoomDigits]
                        const bMatch = u.building ? u.building.match(/\d+/) : null;
                        const bDigit = (bMatch ? bMatch[0] : '1').charAt(0);
                        let rDigits = u.room_number.toString().replace(/\D/g, '');
                        
                        let display_id = "";
                        if (rDigits.length >= 3) {
                            display_id = bDigit + rDigits.slice(-3);
                        } else {
                            display_id = bDigit + "1" + (rDigits || "0").padStart(2, '0').slice(-2);
                        }
                        return { ...u, display_id };
                    });
                    allBuildings = unitsRes.data.buildings || [];
                    populateBuildingFilter();
                    renderUnits();
                }
            } catch (err) {
                console.error("Failed to load data:", err);
                showToast("Failed to sync with database", "var(--danger)");
            }
        }

        function renderTypes() {
            const grid = document.getElementById('types-grid');
            if (!apartmentTypes.length) {
                grid.innerHTML = '<p style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">No apartment types defined yet.</p>';
                return;
            }

            const html = apartmentTypes.map(t => `
                <div class="type-card">
                    <div class="type-card-image">
                        ${t.thumbnail_id 
                            ? `<img src="<?= url('/api/apartment-types/serve-image') ?>?id=${t.thumbnail_id}" alt="${t.label}">`
                            : `<div style="width:100%; height:100%; background:#f0f2f1; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:0.8rem;">No Image</div>`
                        }
                        <div class="type-card-actions" style="position:absolute; top:12px; right:12px; display:flex; gap:6px;">
                            <button class="btn-action" style="padding:6px 12px; border-radius:8px; display:flex; align-items:center; gap:6px; background:white; border:1px solid var(--border); cursor:pointer; font-size:0.75rem; font-weight:700; color:var(--primary);" onclick="openTypeModal('edit', ${t.type_id})">
                                <svg viewBox="0 0 24 24" style="width:14px; fill:currentColor;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/></svg>
                                Edit
                            </button>
                        </div>
                    </div>
                    <div class="type-card-content" style="padding:16px;">
                        <h6 style="margin:0 0 4px; font-family:'Lora',serif; color:var(--primary-dark); font-weight:700;">${t.label}</h6>
                        <p style="color:var(--accent); font-weight:700; font-size:0.95rem; margin-bottom:12px;">₱${Number(t.price).toLocaleString()} / month</p>
                        <div style="display:flex; gap:12px; font-size:0.78rem; color:var(--text-muted); margin-bottom:16px;">
                            <span style="display:flex; align-items:center; gap:4px;"><svg viewBox="0 0 24 24" style="width:13px; fill:currentColor;"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg> ${t.capacity}</span>
                        </div>
                        <button class="btn-topbar" style="width:100%; justify-content:center; gap:8px; background:var(--bg-light); color:var(--primary); border-color:var(--primary);" onclick="previewUnit(${t.type_id})">
                            <svg viewBox="0 0 24 24" style="width:14px; fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            Preview as User
                        </button>
                    </div>
                </div>
            `).join('');
            grid.innerHTML = html;
        }

        function previewUnit(id) {
            const t = apartmentTypes.find(x => x.type_id == id);
            if (!t) return;
            
            // Format data for room-preview.js
            const unitData = {
                ...t,
                images: t.images || []
            };

            if (window.openRoomPreview) {
                window.openRoomPreview(unitData, {
                    basePath: '<?= asset("assets/") ?>',
                    serveUrl: '<?= url("/api/apartment-types/serve-image") ?>',
                    onSelect: () => showToast("Selection preview successful", "var(--info)")
                });
            }
        }

        function renderUnits() {
            const tbody = document.getElementById('units-tbody');
            const filterVal = document.getElementById('building-filter').value;
            const filtered = filterVal ? roomUnits.filter(u => u.building === filterVal) : roomUnits;

            const totalPages = Math.ceil(filtered.length / rowsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const pageData = filtered.slice(start, end);

            if (!pageData.length) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">No units found.</td></tr>';
                renderPagination(0, 0);
                return;
            }

            tbody.innerHTML = pageData.map(u => {
                const statusClass = u.status.toLowerCase() === 'available' ? 'badge-available' 
                    : u.status.toLowerCase() === 'occupied' ? 'badge-occupied'
                    : 'badge-reserved';

                const formattedName = u.display_id || u.room_number;

                return `<tr>
                    <td style="font-weight:600;">${formattedName}</td>
                    <td>${formatFloor(u.room_number)}</td>
                    <td>${u.type_label}</td>
                    <td>₱${Number(u.price).toLocaleString()}</td>
                    <td>${u.tenant_id ? 'Assigned' : '—'}</td>
                    <td><span class="badge-status ${statusClass}">${u.status}</span></td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-action btn-edit" style="padding:5px 12px; gap:6px;" onclick="openUnitModal('edit', ${u.unit_id})">
                                <svg viewBox="0 0 24 24" style="width:14px;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/></svg>
                                Edit
                            </button>
                            <button class="btn-action btn-delete" style="padding:5px 12px; gap:6px;" onclick="deleteUnit(${u.unit_id})" title="Delete Unit">
                                <svg viewBox="0 0 24 24" style="width:14px;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>`;
            }).join('');

            renderPagination(totalPages, currentPage);
        }

        function renderPagination(totalPages, current) {
            const container = document.getElementById('pagination-units');
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = `<div class="pagination-btns">`;
            html += `<button class="btn-page" ${current === 1 ? 'disabled' : ''} onclick="handlePageChange(${current - 1})">Prev</button>`;
            
            let start = Math.max(1, current - 1);
            let end = Math.min(totalPages, start + 2);
            if (end - start < 2) start = Math.max(1, end - 2);

            for (let i = start; i <= end; i++) {
                html += `<button class="btn-page ${i === current ? 'active' : ''}" onclick="handlePageChange(${i})">${i}</button>`;
            }

            html += `<button class="btn-page" ${current === totalPages ? 'disabled' : ''} onclick="handlePageChange(${current + 1})">Next</button>`;
            html += `</div>`;
            container.innerHTML = html;
        }

        window.handlePageChange = (p) => {
            currentPage = p;
            renderUnits();
        };

        function populateBuildingFilter() {
            const select = document.getElementById('building-filter');
            const current = select.value;
            select.innerHTML = '<option value="">All Buildings</option>' +
                allBuildings.map(b => `<option value="${b}">${b}</option>`).join('');
            select.value = current;
            select.addEventListener('change', () => {
                currentPage = 1;
                renderUnits();
            });
        }

        function populateTypeDropdowns() {
            const dropdown = document.getElementById('u-type');
            dropdown.innerHTML = '<option value="">-- Select Type --</option>' + 
                apartmentTypes.map(t => `<option value="${t.type_id}">${t.label} (₱${Number(t.price).toLocaleString()})</option>`).join('');
        }

        // ══ MODALS: TYPE ══
        async function openTypeModal(mode, id = null) {
            document.getElementById('type-form').reset();
            document.getElementById('t-id').value = ''; // Explicitly clear hidden ID
            document.getElementById('image-gallery-grid').innerHTML = '';
            document.getElementById('type-gallery-section').style.display = mode === 'add' ? 'none' : 'block';
            document.getElementById('type-modal-title').textContent = mode === 'add' ? 'Add New Apartment Type' : 'Edit Apartment Type';

            if (mode === 'edit' && id) {
                try {
                    const res = await fetch(`<?= url("/api/apartment-types/detail") ?>?id=${id}`).then(r => r.json());
                    if (res.success) {
                        const type = res.data;
                        document.getElementById('t-id').value = type.type_id;
                        document.getElementById('t-label').value = type.label;
                        document.getElementById('t-key').value = type.type_key;
                        document.getElementById('t-price').value = type.price;
                        document.getElementById('t-capacity').value = type.capacity;
                        document.getElementById('t-desc').value = type.description;
                        document.getElementById('t-area').value = type.floor_area;
                        document.getElementById('t-bedrooms').value = type.bedrooms;
                        document.getElementById('t-bathroom').value = type.bathroom;

                        // Modern Detailed Fields
                        document.getElementById('t-deposit').value = type.security_deposit || '';
                        document.getElementById('t-advance').value = type.advance_rent || '';
                        document.getElementById('t-fees').value = type.other_fees || '';
                        document.getElementById('t-min-lease').value = type.min_lease || '';
                        document.getElementById('t-notice').value = type.notice_period || '';
                        document.getElementById('t-queue').value = type.queue_label || '';

                        // Inclusions & Rules (JSON strings or arrays)
                        const inclusions = type.inclusions ? (typeof type.inclusions === 'string' ? JSON.parse(type.inclusions) : type.inclusions) : [];
                        const rules = type.rules ? (typeof type.rules === 'string' ? JSON.parse(type.rules) : type.rules) : [];
                        
                        document.getElementById('inclusions-container').innerHTML = '';
                        document.getElementById('rules-container').innerHTML = '';
                        inclusions.forEach(inc => addDynamicInput('inclusions', inc));
                        rules.forEach(rule => addDynamicInput('rules', rule));

                        // Handle thumbnail preview
                        const thumb = (type.images || []).find(img => img.is_thumbnail);
                        const thumbPreview = document.getElementById('t-thumb-preview');
                        if (thumb) {
                            thumbPreview.innerHTML = `<img src="<?= url('/api/apartment-types/serve-image') ?>?id=${thumb.image_id}" style="width:100%; height:100%; object-fit:cover;">`;
                        } else {
                            thumbPreview.innerHTML = `<span style="font-size:0.7rem; color:var(--text-muted);">No image</span>`;
                        }

                        renderGallery(type.images || []);
                    } else {
                        showToast("Could not load type details", "var(--danger)");
                        return;
                    }
                } catch (err) {
                    showToast("Network error while fetching details", "var(--danger)");
                    return;
                }
            } else {
                // For 'add' mode, ensure lists are empty
                document.getElementById('inclusions-container').innerHTML = '';
                document.getElementById('rules-container').innerHTML = '';
                // Add one empty input by default
                addDynamicInput('inclusions');
                addDynamicInput('rules');
            }
            openModal('type-modal');
        }

        function addDynamicInput(containerId, value = '') {
            const container = document.getElementById(containerId + '-container');
            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.gap = '8px';
            div.innerHTML = `
                <input type="text" class="form-control dynamic-input-${containerId}" value="${value}" placeholder="Enter text...">
                <button type="button" class="btn-action btn-delete" style="padding:0 10px;" onclick="this.parentElement.remove()">×</button>
            `;
            container.appendChild(div);
        }

        async function saveType() {
            const form = document.getElementById('type-form');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Gather Dynamic Lists
            const inclusions = Array.from(document.querySelectorAll('.dynamic-input-inclusions'))
                .map(input => input.value.trim())
                .filter(val => val !== '');
            const rules = Array.from(document.querySelectorAll('.dynamic-input-rules'))
                .map(input => input.value.trim())
                .filter(val => val !== '');
            
            data.inclusions = JSON.stringify(inclusions);
            data.rules = JSON.stringify(rules);

            const id = data.type_id;
            const endpoint = id ? '<?= url("/api/apartment-types/update") ?>' : '<?= url("/api/apartment-types/create") ?>';

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                }).then(r => r.json());

                if (res.success) {
                    showToast(id ? "Type updated" : "Type created", "var(--success)");
                    closeModal('type-modal');
                    loadData();
                } else {
                    showToast(res.error || "Failed to save type", "var(--danger)");
                }
            } catch (err) {
                console.error(err);
                showToast("Network error while saving", "var(--danger)");
            }
        }

        function renderGallery(images) {
            const grid = document.getElementById('image-gallery-grid');
            grid.innerHTML = images.map(img => `
                <div class="gallery-item ${img.is_thumbnail ? 'is-thumb' : ''}">
                    <img src="<?= url('/api/apartment-types/serve-image') ?>?id=${img.image_id}" alt="Gallery">
                    <div class="gallery-item-actions">
                        <button class="btn-gallery-action thumb" onclick="setThumbnail(${img.image_id})" title="Set as Thumbnail"><svg viewBox="0 0 24 24" style="width:14px;"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg></button>
                        <button class="btn-gallery-action delete" onclick="deleteImage(${img.image_id})" title="Delete Image"><svg viewBox="0 0 24 24" style="width:14px;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></button>
                    </div>
                </div>
            `).join('');
        }

        async function handleThumbUpload(input) {
            const typeId = document.getElementById('t-id').value;
            if (!typeId) return;
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('type_id', typeId);
            formData.append('image', file);
            formData.append('is_thumbnail', '1');

            try {
                const res = await fetch('<?= url("/api/apartment-types/upload-image") ?>', {
                    method: 'POST',
                    body: formData
                }).then(r => r.json());

                if (res.success) {
                    showToast("Thumbnail updated", "var(--success)");
                    const detail = await fetch(`<?= url("/api/apartment-types/detail") ?>?id=${typeId}`).then(r => r.json());
                    if (detail.success) {
                        const thumb = (detail.data.images || []).find(img => img.is_thumbnail);
                        if (thumb) {
                            document.getElementById('t-thumb-preview').innerHTML = `<img src="<?= url('/api/apartment-types/serve-image') ?>?id=${thumb.image_id}" style="width:100%; height:100%; object-fit:cover;">`;
                        }
                        renderGallery(detail.data.images);
                    }
                    loadData();
                } else {
                    showToast(res.error, "var(--danger)");
                }
            } catch (err) {
                showToast("Upload failed", "var(--danger)");
            }
        }

        async function handleGalleryUpload(input) {
            const typeId = document.getElementById('t-id').value;
            if (!typeId) return;

            for (const file of input.files) {
                const formData = new FormData();
                formData.append('type_id', typeId);
                formData.append('image', file);

                try {
                    const res = await fetch('<?= url("/api/apartment-types/upload-image") ?>', {
                        method: 'POST',
                        body: formData
                    }).then(r => r.json());

                    if (res.success) {
                        showToast("Slide added", "var(--success)");
                    } else {
                        showToast(res.error, "var(--danger)");
                    }
                } catch (err) {
                    showToast("Upload failed", "var(--danger)");
                }
            }
            // Refresh
            const detail = await fetch(`<?= url("/api/apartment-types/detail") ?>?id=${typeId}`).then(r => r.json());
            if (detail.success) renderGallery(detail.data.images);
        }

        async function setThumbnail(imageId) {
            const res = await fetch('<?= url("/api/apartment-types/set-thumbnail") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image_id: imageId })
            }).then(r => r.json());
            if (res.success) {
                showToast("Thumbnail updated", "var(--success)");
                loadData(); // To refresh the main grid
                const typeId = document.getElementById('t-id').value;
                const detail = await fetch(`<?= url("/api/apartment-types/detail") ?>?id=${typeId}`).then(r => r.json());
                if (detail.success) renderGallery(detail.data.images);
            }
        }

        async function deleteImage(imageId) {
            if (!confirm("Delete this image?")) return;
            const res = await fetch('<?= url("/api/apartment-types/delete-image") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image_id: imageId })
            }).then(r => r.json());
            if (res.success) {
                showToast("Image deleted", "var(--success)");
                const typeId = document.getElementById('t-id').value;
                const detail = await fetch(`<?= url("/api/apartment-types/detail") ?>?id=${typeId}`).then(r => r.json());
                if (detail.success) renderGallery(detail.data.images);
            }
        }

        function updateGeneratedCode() {
            const bVal = document.getElementById('u-building').value;
            const fVal = document.getElementById('u-floor').value;
            let rVal = parseInt(document.getElementById('u-room-only').value || 0);
            
            // Limit to 5 rooms per floor
            if (rVal > 5) {
                rVal = 5;
                document.getElementById('u-room-only').value = 5;
            }
            
            const bDigit = bVal.replace(/\D/g, '') || '1';
            const roomPadded = rVal.toString().padStart(2, '0');
            
            const generated = bDigit + fVal + roomPadded;
            document.getElementById('u-preview').value = generated;
            // The actual room_number sent to DB is [Floor][Room] because the table logic adds Building
            // Wait, the user said "Building 1 + Floor 2 + Room 01 = 1201"
            // So the database room_number should store "201" and the UI logic adds the Building digit.
            document.getElementById('u-number').value = fVal + roomPadded;
        }

        // ══ MODALS: UNIT ══
        function openUnitModal(mode, id = null) {
            document.getElementById('unit-form').reset();
            document.getElementById('u-id').value = '';
            document.getElementById('unit-modal-title').textContent = mode === 'add' ? 'Add New Room Unit' : 'Edit Room Unit';
            
            if (mode === 'edit' && id) {
                const unit = roomUnits.find(u => u.unit_id == id);
                if (unit) {
                    document.getElementById('u-id').value = unit.unit_id;
                    document.getElementById('u-type').value = unit.type_id;
                    document.getElementById('u-building').value = unit.building || 'Building 1';
                    document.getElementById('u-status').value = unit.status;
                    document.getElementById('u-desc').value = unit.description;

                    const rn = unit.room_number.toString();
                    if (rn.length >= 3) {
                        document.getElementById('u-floor').value = rn.charAt(0);
                        document.getElementById('u-room-only').value = rn.substring(1);
                    } else {
                        document.getElementById('u-floor').value = "1";
                        document.getElementById('u-room-only').value = rn.padStart(2, '0');
                    }
                }
            } else {
                // Defaults for add mode
                document.getElementById('u-building').value = 'Building 1';
                document.getElementById('u-floor').value = '1';
                document.getElementById('u-room-only').value = '';
            }
            updateGeneratedCode();
            openModal('unit-modal');
        }

        async function saveUnit() {
            updateGeneratedCode(); // Final sync
            const formData = new FormData(document.getElementById('unit-form'));
            const data = Object.fromEntries(formData.entries());
            const id = data.unit_id;
            const endpoint = id ? '<?= url("/api/apartment-units/update") ?>' : '<?= url("/api/apartment-units/create") ?>';

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                }).then(r => r.json());

                if (res.success) {
                    showToast(id ? "Unit updated" : "Unit created", "var(--success)");
                    closeModal('unit-modal');
                    loadData();
                } else {
                    showToast(res.error || "Failed to save unit", "var(--danger)");
                }
            } catch (err) {
                console.error(err);
                showToast("Network error while saving unit", "var(--danger)");
            }
        }

        async function deleteUnit(id) {
            if (!confirm("Delete this room unit permanently?")) return;
            const res = await fetch('<?= url("/api/apartment-units/delete") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ unit_id: id })
            }).then(r => r.json());
            if (res.success) {
                showToast("Unit deleted", "var(--success)");
                loadData();
            } else {
                showToast(res.error || "Failed to delete unit", "var(--danger)");
            }
        }

        init();
    </script>
</body>

</html>