<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Apartment Management</title>
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
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

        .unit-status-available { background: #e6f7ef; color: #176b45; }
        .unit-status-occupied { background: #fff4e6; color: #b45d00; }
        .unit-status-reserved { background: #eef2ff; color: #3730a3; }
        .unit-status-maintenance { background: #fef2f2; color: #991b1b; }

        /* Image Gallery in Modal */
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

                <!-- Section: Apartment Types -->
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

                <!-- Section: Registered Units -->
                <div class="section-card">
                    <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                        <h6>
                            <svg viewBox="0 0 24 24"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" /></svg>
                            Registered Room Units
                        </h6>
                    </div>
                    <div class="section-card-body" style="padding:0;">
                        <div class="table-wrapper">
                            <table class="mis-table">
                                <thead>
                                    <tr>
                                        <th>Unit / Room #</th>
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

                    <!-- Gallery Section -->
                    <div id="type-gallery-section" style="margin-top:24px;">
                        <label class="form-label">Photo Gallery</label>
                        <div class="image-gallery-grid" id="image-gallery-grid">
                            <!-- JS populated -->
                        </div>
                        <div class="upload-placeholder" onclick="document.getElementById('gallery-upload').click()" style="margin-top:16px;">
                            <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:var(--text-muted);margin-bottom:8px;"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z" /></svg>
                            <span style="font-size:0.85rem; font-weight:600; color:var(--text-muted);">Click to upload photos</span>
                            <input type="file" id="gallery-upload" multiple accept="image/*" style="display:none;" onchange="handleGalleryUpload(this)">
                        </div>
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
                    <div class="form-group full">
                        <label class="form-label">Apartment Type</label>
                        <select class="form-control" name="type_id" id="u-type" required>
                            <!-- JS populated -->
                        </select>
                    </div>
                    <div class="form-group full" style="margin-top:16px;">
                        <label class="form-label">Room Number / ID</label>
                        <input type="text" class="form-control" name="room_number" id="u-number" placeholder="e.g. 101-A" required>
                    </div>
                    <div class="form-group full" style="margin-top:16px;">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" id="u-status">
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Reserved">Reserved</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group full" style="margin-top:16px;">
                        <label class="form-label">Description / Internal Notes</label>
                        <textarea class="form-control" name="description" id="u-desc"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-topbar" onclick="closeModal('unit-modal')">Cancel</button>
                <button class="btn-topbar primary" onclick="saveUnit()">Save Unit</button>
            </div>
        </div>
    </div>

    <script src="<?= asset('JS/admin-shared.js') ?>"></script>
    <script>
        standardizePage('staff');
        syncSessionUser("<?= addslashes(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>", "<?= addslashes($dbUser['email'] ?? '') ?>", "Staff Admin");

        let apartmentTypes = [];
        let roomUnits = [];

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
                    roomUnits = unitsRes.data.units;
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

            grid.innerHTML = apartmentTypes.map(t => `
                <div class="type-card">
                    <div class="type-card-image">
                        <img src="<?= asset('') ?>${t.thumbnail || 'assets/placeholder.png'}" alt="${t.label}">
                        <span class="type-card-badge">${t.available_count} Available</span>
                    </div>
                    <div class="type-card-body">
                        <h4 class="type-card-title">${t.label}</h4>
                        <div class="type-card-price">₱${Number(t.price).toLocaleString()} <small>/ mo</small></div>
                        <div class="type-card-stats">
                            <div class="type-card-stat"><svg viewBox="0 0 24 24"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>${t.floor_area || '--'}</div>
                            <div class="type-card-stat"><svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>${t.capacity || '--'}</div>
                        </div>
                    </div>
                    <div class="type-card-footer">
                        <button class="btn-action primary" style="flex:1;" onclick="openTypeModal('edit', ${t.type_id})">Manage Type</button>
                    </div>
                </div>
            `).join('');
        }

        function renderUnits() {
            const tbody = document.getElementById('units-tbody');
            if (!roomUnits.length) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:24px;">No units registered.</td></tr>';
                return;
            }

            tbody.innerHTML = roomUnits.map(u => `
                <tr>
                    <td style="font-weight:700;">${u.room_number}</td>
                    <td><span class="badge-status badge-reserved">${u.type_label}</span></td>
                    <td style="font-weight:600;">₱${Number(u.price).toLocaleString()}</td>
                    <td>${u.tenant_id || '<span style="color:var(--text-muted);">Unassigned</span>'}</td>
                    <td><span class="badge-status unit-status-${u.status.toLowerCase()}">${u.status}</span></td>
                    <td class="actions-cell">
                        <button class="btn-action btn-view" onclick="openUnitModal('edit', ${u.unit_id})" title="Edit Unit"><svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg></button>
                        <button class="btn-action btn-reject" onclick="deleteUnit(${u.unit_id})" title="Delete Unit"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></button>
                    </td>
                </tr>
            `).join('');
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
                        renderGallery(type.images || []);
                    } else {
                        showToast("Could not load type details", "var(--danger)");
                        return;
                    }
                } catch (err) {
                    showToast("Network error while fetching details", "var(--danger)");
                    return;
                }
            }
            openModal('type-modal');
        }

        async function saveType() {
            const formData = new FormData(document.getElementById('type-form'));
            const data = Object.fromEntries(formData.entries());
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
                    <img src="<?= asset('') ?>${img.file_path}" alt="Gallery">
                    <div class="gallery-item-actions">
                        <button class="btn-gallery-action thumb" onclick="setThumbnail(${img.image_id})" title="Set as Thumbnail"><svg viewBox="0 0 24 24" style="width:14px;"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg></button>
                        <button class="btn-gallery-action delete" onclick="deleteImage(${img.image_id})" title="Delete Image"><svg viewBox="0 0 24 24" style="width:14px;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></button>
                    </div>
                </div>
            `).join('');
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
                        showToast("Image uploaded", "var(--success)");
                    } else {
                        showToast(res.error, "var(--danger)");
                    }
                } catch (err) {
                    showToast("Upload failed", "var(--danger)");
                }
            }
            // Refresh detail to show new images
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

        // ══ MODALS: UNIT ══
        function openUnitModal(mode, id = null) {
            document.getElementById('unit-form').reset();
            document.getElementById('u-id').value = ''; // Explicitly clear hidden ID
            document.getElementById('unit-modal-title').textContent = mode === 'add' ? 'Add New Room Unit' : 'Edit Room Unit';
            
            if (mode === 'edit' && id) {
                const unit = roomUnits.find(u => u.unit_id == id);
                if (unit) {
                    document.getElementById('u-id').value = unit.unit_id;
                    document.getElementById('u-type').value = unit.type_id;
                    document.getElementById('u-number').value = unit.room_number;
                    document.getElementById('u-status').value = unit.status;
                    document.getElementById('u-desc').value = unit.description;
                }
            }
            openModal('unit-modal');
        }

        async function saveUnit() {
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