<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__, 4));
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();
require_once BASE_PATH . '/app/models/User.php';
$u = new User();
$d = $u->findById($_SESSION['user_id']) ?: [];
$e = $u->getAdditionalInfo($_SESSION['user_id']) ?: [];
$d = array_merge($d, $e);
$name = htmlspecialchars(($d['first_name']??'').' '.($d['last_name']??''));
$init = strtoupper(substr($d['first_name']??'S',0,1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ISCAG — Counseling Dashboard</title>
<link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg-color: #f9fbf9;
    --primary: #114B2C;      /* Dark Green */
    --primary-light: #e6eee9;
    --gold: #D4AF37;
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --border: #f3f4f6;
    --white: #ffffff;
    --radius: 16px;
    --shadow: 0 4px 20px rgba(0,0,0,0.03);
  }
  
  body {
    font-family: 'Inter', sans-serif;
    background: var(--bg-color);
    color: var(--text-main);
    margin: 0;
    padding: 40px;
    box-sizing: border-box;
  }
  
  /* 1. Greeting Section */
  .greeting {
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .greeting h1 {
    font-family: 'Lora', serif;
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: var(--text-main);
  }
  .greeting p {
    color: var(--text-muted);
    margin: 6px 0 0;
    font-size: 0.95rem;
  }
  .top-right {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .notif-bell {
    position: relative;
    cursor: pointer;
    color: var(--text-main);
  }
  .notif-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: var(--primary);
    color: var(--white);
    font-size: 0.6rem;
    font-weight: 700;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--bg-color);
  }
  .user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gold);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.2rem;
    overflow: hidden;
  }
  .user-info .name { font-size: 0.85rem; font-weight: 700; color: var(--text-main); }
  .user-info .role { font-size: 0.75rem; color: var(--text-muted); }

  /* Grid Layout */
  .grid-container {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
  }
  .greeting {
    max-width: 1200px;
    margin: 0 auto 32px auto;
  }
  
  /* Base Card */
  .card {
    background: var(--white);
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: var(--shadow);
    border: 1px solid rgba(0,0,0,0.02);
  }

  /* 2. Summary Cards */
  .summary-row {
    grid-column: span 12;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
  }
  .summary-card {
    padding: 20px 24px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    gap: 16px;
    background: var(--white);
    box-shadow: var(--shadow);
    border: 1px solid rgba(0,0,0,0.02);
  }
  .summary-card.active {
    background: var(--primary);
    color: var(--white);
  }
  .summary-card.active p, .summary-card.active h4 {
    color: rgba(255,255,255,0.8);
  }
  .summary-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: var(--primary-light);
    color: var(--primary);
  }
  .summary-card.active .summary-icon {
    background: rgba(255,255,255,0.15);
    color: var(--white);
  }
  .summary-info h4 {
    margin: 0 0 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
  }
  .summary-info .val {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
  }
  .summary-info p {
    margin: 4px 0 0;
    font-size: 0.8rem;
    color: var(--text-muted);
  }

  /* Row 2: Progress & Request */
  .progress-card { grid-column: span 7; }
  .request-card {
    grid-column: span 5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #ffffff, #fdfcf9);
  }

  /* 3. Counseling Progress Tracker */
  .progress-card h3 { margin: 0 0 16px; font-size: 1.1rem; font-weight: 700; }
  .progress-tracker {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-top: 24px;
    position: relative;
    padding: 0 10px;
  }
  .progress-line {
    position: absolute;
    top: 14px;
    left: 20px;
    right: 20px;
    height: 2px;
    background: #e5e7eb;
    z-index: 1;
  }
  .progress-line-fill {
    position: absolute;
    top: 14px;
    left: 20px;
    width: 60%; /* Matches 'Scheduled' step */
    height: 2px;
    background: var(--primary);
    z-index: 2;
  }
  .step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 3;
    gap: 10px;
    width: 80px;
  }
  .step-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--white);
    border: 2px solid #e5e7eb;
    color: #9ca3af;
  }
  .step.completed .step-icon {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--white);
  }
  .step.current .step-icon {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 4px var(--primary-light);
  }
  .step-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    color: var(--text-main);
    line-height: 1.2;
  }
  .step-date {
    font-size: 0.65rem;
    color: var(--text-muted);
  }

  /* 5. Request Counseling Card */
  .req-text h3 { margin: 0 0 8px; font-size: 1.1rem; font-weight: 700; }
  .req-text p {
    margin: 0 0 20px;
    font-size: 0.85rem;
    color: var(--text-muted);
    line-height: 1.5;
    max-width: 250px;
  }
  .btn-primary {
    background: var(--primary);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
  }
  .req-icon-circle {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: var(--primary-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
  }

  /* 4. Two-Column Main Section */
  .sessions-card { grid-column: span 7; }
  .details-card { grid-column: span 5; }

  /* Tabs (Left) */
  .sessions-card h3 { margin: 0 0 20px; font-size: 1.1rem; font-weight: 700; }
  .tabs {
    display: flex;
    gap: 24px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 24px;
  }
  .tab {
    padding-bottom: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    position: relative;
  }
  .tab.active { color: var(--primary); }
  .tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--primary);
  }

  /* Session List */
  .session-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    border: 1px solid transparent;
    border-radius: 12px;
    margin-bottom: 12px;
    background: #fafafa;
    transition: 0.2s;
  }
  .session-item.active {
    background: var(--primary-light);
  }
  .session-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #e5e7eb;
    color: #4b5563;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .session-item.active .session-icon {
    background: var(--white);
    color: var(--primary);
  }
  .session-info { flex: 1; }
  .session-info h4 { margin: 0 0 4px; font-size: 0.9rem; font-weight: 700; }
  .session-info p { margin: 0; font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; }
  .badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
  }
  .badge.scheduled { background: #dcfce7; color: #166534; }
  .badge.completed { background: #f3f4f6; color: #4b5563; }
  .view-all {
    text-align: center;
    margin-top: 20px;
    display: block;
    color: var(--primary);
    font-size: 0.85rem;
    font-weight: 700;
    text-decoration: none;
  }

  /* Session Details (Right) */
  .details-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
  }
  .details-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
  .details-header-img {
    width: 80px;
    height: 80px;
    background: #fcfcfc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .details-list { display: flex; flex-direction: column; gap: 20px; }
  .detail-item { display: flex; gap: 16px; }
  .detail-icon { color: var(--primary); margin-top: 2px; }
  .detail-text h5 { margin: 0 0 4px; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; }
  .detail-text p { margin: 0; font-size: 0.9rem; font-weight: 600; color: var(--text-main); }
  .detail-text p.note { font-weight: 500; font-size: 0.85rem; line-height: 1.5; }

  /* Message Field */
  .message-box { margin-top: 32px; }
  .message-box h5 { margin: 0 0 12px; font-size: 0.85rem; font-weight: 700; }
  .msg-input-wrap { position: relative; }
  .msg-input {
    width: 100%;
    box-sizing: border-box;
    padding: 14px 48px 14px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.9rem;
    background: #f9fafb;
    outline: none;
  }
  .msg-input:focus { border-color: var(--primary); }
  .btn-send {
    position: absolute;
    right: 6px;
    top: 6px;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: var(--primary);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  .msg-notice { font-size: 0.75rem; color: var(--text-muted); margin-top: 10px; }

  /* 6. Reflection & 7. Privacy */
  .reflection-card {
    grid-column: span 7;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .reflection-text h3 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; }
  .reflection-text p { margin: 0; font-size: 0.85rem; color: var(--text-muted); }
  .btn-outline {
    background: var(--white);
    color: var(--text-main);
    border: 1px solid #e5e7eb;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .privacy-card {
    grid-column: span 5;
    display: flex;
    align-items: center;
    gap: 16px;
    background: #fcfdfc;
  }
  .privacy-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .privacy-text h4 { margin: 0 0 4px; font-size: 0.95rem; font-weight: 700; color: var(--primary); }
  .privacy-text p { margin: 0; font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; }
  
  @media(max-width:900px){
      .grid-container { grid-template-columns: 1fr; }
      .summary-row { grid-template-columns: 1fr; }
      .progress-card, .request-card, .sessions-card, .details-card, .reflection-card, .privacy-card { grid-column: span 12; }
      .reflection-card { flex-direction: column; align-items: flex-start; gap: 15px; }
  }
</style>
</head>
<body>

<!-- 1. Greeting Section -->
<div class="greeting">
  <div>
    <h1>Assalamu Alaikum, <?= $name ?> <span style="color:var(--primary);">🌿</span></h1>
    <p>We are here to support and guide you.</p>
  </div>
  <div class="top-right">
    <div class="notif-bell">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
      <div class="notif-badge">2</div>
    </div>
    <div class="user-profile">
      <div class="user-avatar"><?= $init ?></div>
      <div class="user-info">
        <div class="name"><?= $name ?></div>
        <div class="role">Student</div>
      </div>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </div>
  </div>
</div>

<div class="grid-container">

  <!-- 2. Summary Cards -->
  <div class="summary-row">
    <div class="summary-card active">
      <div class="summary-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
      </div>
      <div class="summary-info">
        <h4>Next Appointment</h4>
        <div class="val">May 24, 2026</div>
        <p>Saturday • 10:00 AM</p>
      </div>
    </div>
    
    <div class="summary-card">
      <div class="summary-icon" style="border-radius: 50%; overflow: hidden; padding: 0;">
         <img src="https://i.pravatar.cc/100?img=9" style="width:100%; height:100%; object-fit: cover;">
      </div>
      <div class="summary-info">
        <h4>Counselor</h4>
        <div class="val">Ustazah Fatimah</div>
        <p>Dawah Female Staff</p>
      </div>
    </div>
    
    <div class="summary-card">
      <div class="summary-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
      </div>
      <div class="summary-info">
        <h4>Session Type</h4>
        <div class="val">Spiritual Guidance</div>
        <p>On-site Session</p>
      </div>
    </div>
    
    <div class="summary-card">
      <div class="summary-icon" style="background:#fffbeb; color:var(--gold);">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
      </div>
      <div class="summary-info">
        <h4>Status</h4>
        <div class="val" style="color:var(--primary);">Scheduled</div>
        <p>See details below</p>
      </div>
    </div>
  </div>
  
  <!-- 3. Counseling Progress Tracker -->
  <div class="card progress-card">
    <h3>Your Counseling Progress</h3>
    <div class="progress-tracker">
      <div class="progress-line"></div>
      <div class="progress-line-fill"></div>
      
      <div class="step completed">
        <div class="step-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
        <div class="step-label">Request<br>Submitted</div>
        <div class="step-date">May 18, 2026</div>
      </div>
      <div class="step completed">
        <div class="step-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
        <div class="step-label">Under<br>Review</div>
        <div class="step-date">May 19, 2026</div>
      </div>
      <div class="step completed">
        <div class="step-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
        <div class="step-label">Approved</div>
        <div class="step-date">May 20, 2026</div>
      </div>
      <div class="step current">
        <div class="step-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="step-label">Scheduled</div>
        <div class="step-date">May 21, 2026</div>
      </div>
      <div class="step">
        <div class="step-icon"></div>
        <div class="step-label">Completed</div>
        <div class="step-date">&nbsp;</div>
      </div>
    </div>
  </div>
  
  <!-- 5. Request Counseling Card -->
  <div class="card request-card">
    <div class="req-text">
      <h3>Need a Counseling Session?</h3>
      <p>You can request a new counseling session whenever you need support.</p>
      <button class="btn-primary" onclick="window.location.href='<?= url('/user/services/education/female/counseling') ?>'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
        Request Counseling
      </button>
    </div>
    <div class="req-icon-circle">
      <svg width="45" height="45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path><path d="M15 15l-3 3-3-3" stroke-width="1"></path></svg>
    </div>
  </div>
  
  <!-- 4. Main Two-Column Section (Left) -->
  <div class="card sessions-card">
    <h3>My Counseling Sessions</h3>
    <div class="tabs">
      <div class="tab active">Upcoming</div>
      <div class="tab">Past Sessions</div>
    </div>
    
    <!-- Active Session Item -->
    <div class="session-item active">
      <div class="session-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
      </div>
      <div class="session-info">
        <h4>Spiritual Guidance</h4>
        <p>May 24, 2026 • 10:00 AM<br>With Ustazah Fatimah</p>
      </div>
      <div class="badge scheduled">Scheduled</div>
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </div>
    
    <!-- Past Session Item -->
    <div class="session-item">
      <div class="session-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
      </div>
      <div class="session-info">
        <h4>Personal Concerns</h4>
        <p>May 10, 2026 • 02:00 PM<br>With Ustazah Amina</p>
      </div>
      <div class="badge completed">Completed</div>
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </div>
    
    <!-- Past Session Item -->
    <div class="session-item">
      <div class="session-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
      </div>
      <div class="session-info">
        <h4>Family Concerns</h4>
        <p>Apr 26, 2026 • 11:00 AM<br>With Ustazah Fatimah</p>
      </div>
      <div class="badge completed">Completed</div>
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </div>
    
    <a href="#" class="view-all">View all sessions</a>
  </div>
  
  <!-- 4. Session Details (Right) -->
  <div class="card details-card">
    <div class="details-header">
      <h3>Session Details</h3>
      <div class="details-header-img">
        <svg viewBox="0 0 100 100" style="width:50px; height:50px; fill:none; stroke:#e5e7eb; stroke-width:3;">
          <rect x="20" y="10" width="60" height="80" rx="4" />
          <path d="M40 5 l20 0" stroke="var(--primary)" stroke-width="4" stroke-linecap="round" />
          <path d="M30 30 h40" /> <path d="M30 45 h40" /> <path d="M30 60 h25" />
          <circle cx="35" cy="30" r="2" fill="var(--primary)" stroke="none" />
          <circle cx="35" cy="45" r="2" fill="var(--primary)" stroke="none" />
          <circle cx="35" cy="60" r="2" fill="var(--primary)" stroke="none" />
          <!-- Small plants decor -->
          <path d="M10 90 Q15 70 25 80" stroke="var(--primary)" stroke-linecap="round" />
          <path d="M90 90 Q85 75 75 80" stroke="var(--primary)" stroke-linecap="round" />
        </svg>
      </div>
    </div>
    
    <div class="details-list">
      <div class="detail-item">
        <div class="detail-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>
        <div class="detail-text"><h5>Type</h5><p>Spiritual Guidance</p></div>
      </div>
      <div class="detail-item">
        <div class="detail-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></div>
        <div class="detail-text"><h5>Date & Time</h5><p>May 24, 2026 • 10:00 AM</p></div>
      </div>
      <div class="detail-item">
        <div class="detail-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></div>
        <div class="detail-text"><h5>Location</h5><p>IDFS Center - Counseling Room</p></div>
      </div>
      <div class="detail-item">
        <div class="detail-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></div>
        <div class="detail-text"><h5>Notes</h5><p class="note">Please prepare your questions or concerns for the session.</p></div>
      </div>
    </div>
    
    <div class="message-box">
      <h5>Message Counselor</h5>
      <div class="msg-input-wrap">
        <input type="text" class="msg-input" placeholder="Type your message...">
        <button class="btn-send">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
        </button>
      </div>
      <div class="msg-notice">Messages are private and confidential.</div>
    </div>
  </div>
  
  <!-- 6. Reflection Section -->
  <div class="card reflection-card">
    <div class="reflection-text">
      <h3>Reflection Journal</h3>
      <p>Write your reflections after each session. This helps you track your growth.</p>
    </div>
    <button class="btn-outline">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
      Go to Journal
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>
  </div>
  
  <!-- 7. Privacy Notice Card -->
  <div class="card privacy-card">
    <div class="privacy-icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
    </div>
    <div class="privacy-text">
      <h4>Confidential & Private</h4>
      <p>Your information is private and handled with care.</p>
    </div>
  </div>

</div>

</body>
</html>
