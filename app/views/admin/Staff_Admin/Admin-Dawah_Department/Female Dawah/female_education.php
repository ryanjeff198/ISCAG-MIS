<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Islamic Education</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'education';
      $dawah_type = 'female';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Islamic Education</div>
          <div class="top-bar-subtitle">Female Da'wah Department — Manage student enrollments and academic progress</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar primary" onclick="alert('Module for adding students will be available in the next phase.')">
             <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2.5;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
             Add Student
           </button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/female') ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span class="current">Education Records</span>
        </div>

        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
              Student Enrollment List (Female)
            </h6>
            <div class="filter-group" style="display:flex; gap:10px; align-items:center;">
              <span style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase;">Category:</span>
              <select id="categoryFilter" class="form-select-sm" style="padding:6px 12px; border-radius:8px; border:1.5px solid var(--border); font-size:0.85rem; font-weight:700; color:var(--primary-dark); cursor:pointer;">
                <option value="All">All Programs</option>
                <option value="Beginners Qur'an (B4)">Beginners Qur'an (B4)</option>
                <option value="Intermediate Islamic Studies">Intermediate Islamic Studies</option>
                <option value="Tajweed Mastery">Tajweed Mastery</option>
                <option value="Tahfidhul Qur'an">Tahfidhul Qur'an</option>
                <option value="Arabic Language">Arabic Language</option>
                <option value="Special Class">Special Class</option>
                <option value="Others">Others</option>
              </select>
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="tabs-bar" style="display:flex; gap:20px; padding:15px 24px; border-bottom:1px solid var(--border); background:rgba(244,246,248,0.5);">
              <button class="tab-btn active" data-status="All" style="background:none; border:none; padding:8px 16px; border-radius:8px; font-weight:800; font-size:0.8rem; cursor:pointer; color:var(--text-muted); transition:0.3s; position:relative;">ALL RECORDS</button>
              <button class="tab-btn" data-status="pending" style="background:none; border:none; padding:8px 16px; border-radius:8px; font-weight:800; font-size:0.8rem; cursor:pointer; color:var(--text-muted); transition:0.3s;">PENDING</button>
              <button class="tab-btn" data-status="active" style="background:none; border:none; padding:8px 16px; border-radius:8px; font-weight:800; font-size:0.8rem; cursor:pointer; color:var(--text-muted); transition:0.3s;">ENROLLED</button>
              <button class="tab-btn" data-status="completed" style="background:none; border:none; padding:8px 16px; border-radius:8px; font-weight:800; font-size:0.8rem; cursor:pointer; color:var(--text-muted); transition:0.3s;">COMPLETED / PAID</button>
            </div>
            <style>
              .tab-btn.active { background: white !important; color: var(--primary-dark) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
              .tab-btn:hover:not(.active) { color: var(--accent); }
            </style>
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Student Name</th>
                    <th>Age</th>
                    <th>Program</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($records)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No student records found.</td></tr>
                  <?php else: ?>
                    <?php foreach ($records as $r): 
                      $sc = 'badge-pending';
                      if($r['status'] === 'active') $sc = 'badge-active';
                      if($r['status'] === 'completed') $sc = 'badge-complete';
                      if($r['status'] === 'dropped') $sc = 'badge-rejected';
                    ?>
                      <tr class="enrollment-row" data-program="<?= htmlspecialchars($r['program_name']) ?>" data-status="<?= htmlspecialchars($r['status']) ?>">
                        <td class="td-id">#<?= $r['id'] ?></td>
                        <td style="font-weight:600;"><?= trim($r['first_name'] . ' ' . $r['last_name']) ?></td>
                        <td>
                          <?php if (!empty($r['age'])): ?>
                            <span style="font-weight:700; color:var(--text-main);"><?= $r['age'] ?></span> <span style="font-size:0.7rem; color:var(--text-muted);">y/o</span>
                          <?php else: ?>
                            <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                          <?php endif; ?>
                        </td>
                        <td class="td-program"><?= $r['program_name'] ?></td>
                        <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                        <td><span class="badge-status <?= $sc ?>"><?= ucfirst($r['status']) ?></span></td>
                        <td>
                          <div class="actions-cell">
                            <button class="btn-action btn-view" onclick="showAlert('Profile Management', 'Loading student academic records and progress tracking...', 'info')">
                              <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                              Manage
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const catFilter = document.getElementById('categoryFilter');
    const tabBtns = document.querySelectorAll('.tab-btn');
    let activeStatus = 'All';

    function applyFilters() {
      const category = catFilter.value;
      const rows = document.querySelectorAll('.enrollment-row');
      let visibleCount = 0;

      rows.forEach(row => {
        const rowProgram = row.getAttribute('data-program');
        const rowStatus = row.getAttribute('data-status');
        
        const catMatch = (category === 'All' || rowProgram === category);
        const statusMatch = (activeStatus === 'All' || rowStatus === activeStatus);

        if(catMatch && statusMatch) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      // Show empty state if no records match
      const tbody = document.querySelector('.mis-table tbody');
      let emptyMsg = document.getElementById('no-records-msg');
      
      if(visibleCount === 0) {
        if(!emptyMsg) {
          const tr = document.createElement('tr');
          tr.id = 'no-records-msg';
          tr.innerHTML = `<td colspan="6" style="text-align:center;padding:60px;color:var(--text-muted);">
            <div style="font-size:2rem; margin-bottom:10px;">📚</div>
            No students match the selected <strong>${activeStatus === 'All' ? '' : activeStatus}</strong> criteria.
          </td>`;
          tbody.appendChild(tr);
        }
      } else if(emptyMsg) {
        emptyMsg.remove();
      }
    }

    catFilter.addEventListener('change', applyFilters);

    tabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        tabBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        activeStatus = this.getAttribute('data-status');
        applyFilters();
      });
    });
  </script>
</body>
</html>
