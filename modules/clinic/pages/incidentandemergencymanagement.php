<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BCP · School Clinic Medical Gate Pass</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
 
</head>
<body>

<!-- HEADER -->
<header class="site-header">
  <div class="header-logo">BCP</div>
  <div class="header-title">
    School Clinic Medical Gate Pass
    <span>Bestlink College of the Philippines · BSGRA</span>
  </div>
  <div class="header-right">
    <div class="live-clock" id="liveClock"></div>
    <div class="badge-module">Incident & Emergency</div>
  </div>
</header>

<!-- MAIN CONTAINER -->
<div class="container">

  <!-- STAT STRIP -->
  <div class="stat-strip">
    <div class="stat-card">
      <div class="stat-icon blue"></div>
      <div>
        <div class="stat-label">Today's Date</div>
        <div class="stat-value" id="todayDate">—</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"></div>
      <div>
        <div class="stat-label">Guardian Notified</div>
        <div class="stat-value">Auto-Verified System</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon gold"></div>
      <div>
        <div class="stat-label">Security Verification</div>
        <div class="stat-value">M. SERENAS · R. SALVADOR</div>
      </div>
    </div>
  </div>

  <!-- DB CONNECTION BANNER -->
  <div style="background:linear-gradient(135deg,#1e3a5f,#0d2340);border-radius:14px;padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
    <div style="font-size:1.5rem;"></div>
    
  </div>
  <div class="card">
    <div class="card-header">
      <div class="card-header-title">
        <span class="icon"></span>
        Medical Gate Pass — Incident Form
      </div>
      <button class="btn btn-pdf" id="exportNewPdfBtn" style="display:none;" onclick="showPdfModal(null)">
         Export as PDF
      </button>
    </div>
    <div class="card-body">

      <div class="edit-banner" id="editBanner">
        ✏️ <span id="editBannerText">Editing record</span> &nbsp;·&nbsp;
        <button class="btn btn-outline btn-sm" style="padding:3px 12px;font-size:0.75rem;" onclick="cancelEdit()">Cancel</button>
      </div>

      <input type="hidden" id="incident_id">

      <!-- ROW 1 — Student Lookup -->
      <div class="fetch-hint" id="fetchHint" style="display:none;"></div>

      <div class="form-grid grid-4">
        <div class="field-group">
          <label>Student ID <span class="fetch-label" id="fetchLabel"></span></label>
          <div class="id-input-wrap">
            <input type="text" id="student_id" placeholder="Type ID to auto-fetch…" autocomplete="off">
            <span class="fetch-spinner" id="fetchSpinner"></span>
          </div>
        </div>
        <div class="field-group">
          <label>Student Name <span style="color:var(--muted);font-weight:400;font-size:0.65rem;">(auto-filled)</span></label>
          <input type="text" id="student_name" placeholder="Auto-filled from ID" readonly id="student_name">
        </div>
        <div class="field-group">
          <label>Course / Year <span style="color:var(--muted);font-weight:400;font-size:0.65rem;">(auto-filled)</span></label>
          <input type="text" id="course" placeholder="Auto-filled from ID" readonly>
        </div>
        <div class="field-group">
          <label>Date of Visit</label>
          <input type="date" id="visit_date">
        </div>
      </div>

      <!-- Student Info Card (shown after fetch) -->
      <div class="student-card" id="studentCard" style="display:none;">
        <div class="student-avatar" id="studentAvatar">?</div>
        <div class="student-info">
          <div class="student-fullname" id="studentFullname">—</div>
          <div class="student-meta" id="studentMeta">—</div>
        </div>
        <div class="student-status-tag" id="studentStatusTag">active</div>
      </div>

      <div class="section-divider"><span>Medical Details</span></div>

      <!-- ROW 2 -->
      <div class="form-grid grid-4">
        <div class="field-group">
          <label>Incident / Reason</label>
          <input type="text" id="incident_type" placeholder="e.g. Fever, Sprain">
        </div>
        <div class="field-group">
          <label>Incident Reference #</label>
          <input type="text" id="incident_ref" placeholder="e.g. A-1024">
        </div>
        <div class="field-group">
          <label>Time Allowed to Leave</label>
          <input type="text" id="time_allowed" placeholder="e.g. 2:30 PM">
        </div>
        <div class="field-group">
          <label>Reported By</label>
          <input type="text" id="reported_by" placeholder="Staff / Faculty name">
        </div>
      </div>

      <!-- ROW 3 -->
      <div class="form-grid grid-3" style="margin-top:14px;">
        <div class="field-group">
          <label>Status</label>
          <select id="status">
            <option value="Open">Open · Under Assessment</option>
            <option value="In Progress">In Progress · Attending</option>
            <option value="Resolved">Resolved · Gate Pass Issued</option>
          </select>
        </div>
        <div class="field-group">
          <label>Guardian Notified</label>
          <select id="guardian_notified">
            <option value="Yes">Yes</option>
            <option value="No">No</option>
          </select>
        </div>
        <div class="field-group">
          <label>Resolved Date</label>
          <input type="datetime-local" id="resolved_date">
        </div>
      </div>

      <!-- ROW 4 -->
      <div class="form-grid" style="margin-top:14px;">
        <div class="field-group">
          <label>Description / Notes</label>
          <textarea id="description" placeholder="Medical concern details, observations, instructions given..."></textarea>
        </div>
      </div>

      <div class="form-footer">
        <div class="sig-block">
          <strong>MARIA CARLA P. SERENAS, LPT, RN</strong> · Clinic Nurse<br>
          <strong>ROSEMARIE SALVADOR</strong> · College Coordinator
        </div>
        <div style="display:flex;gap:10px;">
          <button class="btn btn-outline" type="button" onclick="clearForm()"> Clear</button>
          <button class="btn btn-primary" type="button" onclick="saveIncident()"> Save Gate Pass</button>
        </div>
      </div>
    </div>
  </div>

  <!-- RECORDS TABLE CARD -->
  <div class="card">
    <div class="card-header">
      <div class="card-header-title">
        <span class="icon"></span>
        Gate Pass Records
      </div>
      <button class="refresh-btn" onclick="loadIncidents()">↻ Refresh</button>
    </div>
    <div class="card-body" style="padding:0;">
      <div class="table-wrap">
        <table id="incidentTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Student ID</th>
              <th>Name</th>
              <th>Course</th>
              <th>Reason</th>
              <th>Ref #</th>
              <th>Time Leave</th>
              <th>Reported By</th>
              <th>Date Reported</th>
              <th>Status</th>
              <th>Guardian</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="incidentTbody">
            <tr><td colspan="12" class="empty-state">No records yet. Add a gate pass above.</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- FOOTER -->
<div class="clinic-footer">
   Bestlink College of the Philippines · School Clinic Medical Gate Pass System · Security Verification Enabled
</div>

<!-- TOAST -->
<div class="toast" id="toast">Record saved!</div>

<!-- PDF PREVIEW MODAL -->
<div class="modal-overlay" id="pdfModal">
  <div class="modal-box">
    <h3> Gate Pass PDF Preview</h3>
    <p>Review the gate pass details before downloading.</p>
    <div class="modal-preview" id="pdfPreview"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closePdfModal()">Cancel</button>
      <button class="btn btn-pdf" onclick="downloadPdf()">⬇ Download PDF</button>
    </div>
  </div>
</div>

<script>
(function() {
  const STORAGE_KEY = 'bcp_gatepass_v3';
  let currentPdfData = null;

  // --- CLOCK ---
  function updateClock() {
    const now = new Date();
    document.getElementById('liveClock').textContent = now.toLocaleTimeString('en-PH');
  }
  setInterval(updateClock, 1000);
  updateClock();

  // --- Today date ---
  const today = new Date();
  document.getElementById('todayDate').textContent = today.toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric'});
  document.getElementById('visit_date').value = today.toISOString().slice(0,10);

  // --- Storage helpers ---
  function getData() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
    catch(e) { return []; }
  }

  function saveData(arr) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(arr));
  }

  function nextId(arr) {
    return arr.length > 0 ? Math.max(...arr.map(i=>i.id)) + 1 : 1001;
  }

  // ============================================================
  // DATABASE CONNECTION CONFIG
  // To connect your real MySQL database, set USE_REAL_API = true
  // and point API_ENDPOINT to your PHP backend below.
  //
  // Your PHP file (get_student.php) should return JSON like:
  // { "found": true, "first_name":"...", "last_name":"...", ... }
  // ============================================================
  const USE_REAL_API = false; // ← Set to TRUE when deploying with PHP backend
  const API_ENDPOINT = 'get_student.php'; // ← Path to your PHP API file

  // --- REAL STUDENT DATABASE (from students table) ---
  // Keys = student_number (as string)
  const MOCK_STUDENTS = {
    '508':  { first_name:'Bertram',    middle_name:'Zemlak',      last_name:'Stehr',       course:'BSCrim', year_level:'2004', section:'', gender:'male',   academic_status:'graduated' },
    '529':  { first_name:'Zackary',    middle_name:'Koelpin',     last_name:'Parisian',    course:'BSCrim', year_level:'2004', section:'', gender:'male',   academic_status:'inactive' },
    '555':  { first_name:'Emilia',     middle_name:'Bailey',      last_name:'Lakin',       course:'BSTM',   year_level:'2004', section:'', gender:'female', academic_status:'graduated' },
    '625':  { first_name:'Maximilian', middle_name:'Rosenbaum',   last_name:'Fritsch',     course:'BSAIS',  year_level:'2004', section:'', gender:'male',   academic_status:'graduated' },
    '684':  { first_name:'Cole',       middle_name:'Trantow',     last_name:'Heller',      course:'BSTM',   year_level:'2001', section:'', gender:'male',   academic_status:'active' },
    '685':  { first_name:'Danielle',   middle_name:'Nitzsche',    last_name:'Sauer',       course:'BSIS',   year_level:'2004', section:'', gender:'male',   academic_status:'graduated' },
    '769':  { first_name:'Dallin',     middle_name:'Spencer',     last_name:'Heidenreich', course:'BSAIS',  year_level:'2004', section:'', gender:'female', academic_status:'graduated' },
    '807':  { first_name:'Archibald',  middle_name:'Leuschke',    last_name:'Crona',       course:'BSAIS',  year_level:'2002', section:'', gender:'female', academic_status:'active' },
    '846':  { first_name:'Morris',     middle_name:'Stamm',       last_name:'McKenzie',    course:'BSIS',   year_level:'2003', section:'', gender:'female', academic_status:'inactive' },
    '881':  { first_name:'Lauren',     middle_name:'Dooley',      last_name:'Beahan',      course:'BSAIS',  year_level:'2004', section:'', gender:'female', academic_status:'active' },
    '885':  { first_name:'Kamren',     middle_name:'Oberbrunner', last_name:'Langworth',   course:'BSTM',   year_level:'2001', section:'', gender:'male',   academic_status:'inactive' },
    '1070': { first_name:'Anna',       middle_name:'Anderson',    last_name:'Kautzer',     course:'BSIS',   year_level:'2004', section:'', gender:'female', academic_status:'inactive' },
    '1087': { first_name:'Kailey',     middle_name:'Herman',      last_name:'Terry',       course:'BSTM',   year_level:'2003', section:'', gender:'male',   academic_status:'inactive' },
    '1105': { first_name:'Kameron',    middle_name:'Yeum',        last_name:'Runolfsson',  course:'BSIS',   year_level:'2003', section:'', gender:'female', academic_status:'active' },
  };

  function buildFullName(s) {
    const mid = s.middle_name ? ' ' + s.middle_name.charAt(0) + '.' : '';
    return `${s.first_name}${mid} ${s.last_name}`;
  }

  function buildCourseYear(s) {
    // year_level in DB is a 4-digit year; section may be empty
    const sec = s.section ? ' ' + s.section : '';
    return `${s.course} (${s.year_level}${sec})`;
  }

  function applyStudentToForm(val, student) {
    const nameEl   = document.getElementById('student_name');
    const courseEl = document.getElementById('course');
    const label    = document.getElementById('fetchLabel');
    const spinner  = document.getElementById('fetchSpinner');
    const card     = document.getElementById('studentCard');

    spinner.style.display = 'none';
    const fullName   = buildFullName(student);
    const courseYear = buildCourseYear(student);

    nameEl.value   = fullName;
    courseEl.value = courseYear;
    nameEl.classList.add('fetched-ok');
    courseEl.classList.add('fetched-ok');
    nameEl.setAttribute('readonly', '');
    courseEl.setAttribute('readonly', '');
    document.getElementById('student_id').classList.add('fetched-ok');
    label.className   = 'fetch-label found';
    label.textContent = '✓ Found';

    // Student card
    document.getElementById('studentAvatar').textContent =
      student.first_name.charAt(0) + student.last_name.charAt(0);
    document.getElementById('studentFullname').textContent = fullName;
    document.getElementById('studentMeta').textContent =
      `${courseYear}  ·  ID: ${val}  ·  ${student.gender === 'female' ? '♀' : '♂'}`;
    const stTag = document.getElementById('studentStatusTag');
    stTag.textContent = student.academic_status;
    stTag.className = 'student-status-tag' +
      (student.academic_status !== 'active' ? ' ' + student.academic_status : '');
    card.style.display = 'flex';
  }

  function markNotFound(val) {
    const hint    = document.getElementById('fetchHint');
    const label   = document.getElementById('fetchLabel');
    const nameEl  = document.getElementById('student_name');
    const courseEl= document.getElementById('course');
    document.getElementById('fetchSpinner').style.display = 'none';
    document.getElementById('student_id').classList.add('fetched-fail');
    label.className   = 'fetch-label notfound';
    label.textContent = '✗ Not found';
    hint.innerHTML = '⚠️ Student ID <strong>' + val + '</strong> was not found in the database. You can still fill in the details manually.';
    hint.style.display = 'flex';
    nameEl.removeAttribute('readonly');
    courseEl.removeAttribute('readonly');
  }

  // --- AUTO-FETCH LOGIC (supports real API or mock) ---
  let fetchTimeout = null;

  document.getElementById('student_id').addEventListener('input', function() {
    const val = this.value.trim();
    const nameEl   = document.getElementById('student_name');
    const courseEl = document.getElementById('course');
    const card     = document.getElementById('studentCard');
    const spinner  = document.getElementById('fetchSpinner');
    const label    = document.getElementById('fetchLabel');
    const hint     = document.getElementById('fetchHint');

    // Reset state
    clearTimeout(fetchTimeout);
    card.style.display   = 'none';
    hint.style.display   = 'none';
    nameEl.value         = '';
    courseEl.value       = '';
    nameEl.classList.remove('fetched-ok','fetched-fail');
    courseEl.classList.remove('fetched-ok','fetched-fail');
    this.classList.remove('fetched-ok','fetched-fail');
    label.className      = 'fetch-label';
    label.textContent    = '';
    spinner.style.display= 'none';

    if (val.length < 2) return;

    // Show searching
    spinner.style.display = 'inline';
    label.className   = 'fetch-label searching';
    label.textContent = 'searching…';

    fetchTimeout = setTimeout(async () => {

      if (USE_REAL_API) {
        // ── REAL DATABASE via PHP API ──
        try {
          const res  = await fetch(`${API_ENDPOINT}?student_number=${encodeURIComponent(val)}`);
          const data = await res.json();
          if (data.found) {
            applyStudentToForm(val, data);
          } else {
            markNotFound(val);
          }
        } catch(err) {
          spinner.style.display = 'none';
          label.className   = 'fetch-label notfound';
          label.textContent = '✗ API error';
          hint.innerHTML = '⚠️ Could not connect to the database. Check that <code>get_student.php</code> is running.';
          hint.style.display = 'flex';
        }

      } else {
        // ── LOCAL MOCK LOOKUP ──
        const student = MOCK_STUDENTS[val];
        if (student) {
          applyStudentToForm(val, student);
        } else {
          markNotFound(val);
        }
      }

    }, 350);
  });

  // --- TOAST ---
  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  // --- CLEAR FORM ---
  window.clearForm = function() {
    document.getElementById('incident_id').value = '';
    const sidEl = document.getElementById('student_id');
    sidEl.value = '';
    sidEl.classList.remove('fetched-ok','fetched-fail');
    const nameEl = document.getElementById('student_name');
    nameEl.value = '';
    nameEl.classList.remove('fetched-ok','fetched-fail');
    nameEl.setAttribute('readonly','');
    const courseEl = document.getElementById('course');
    courseEl.value = '';
    courseEl.classList.remove('fetched-ok','fetched-fail');
    courseEl.setAttribute('readonly','');
    document.getElementById('studentCard').style.display = 'none';
    document.getElementById('fetchHint').style.display = 'none';
    document.getElementById('fetchLabel').textContent = '';
    document.getElementById('fetchLabel').className = 'fetch-label';
    document.getElementById('visit_date').value = today.toISOString().slice(0,10);
    document.getElementById('incident_type').value = '';
    document.getElementById('incident_ref').value = '';
    document.getElementById('time_allowed').value = '';
    document.getElementById('reported_by').value = '';
    document.getElementById('status').value = 'Open';
    document.getElementById('guardian_notified').value = 'Yes';
    document.getElementById('resolved_date').value = '';
    document.getElementById('description').value = '';
    document.getElementById('editBanner').classList.remove('show');
    document.getElementById('exportNewPdfBtn').style.display = 'none';
  };

  window.cancelEdit = clearForm;

  // --- SAVE INCIDENT ---
  window.saveIncident = function() {
    const student_id = document.getElementById('student_id').value.trim();
    const student_name = document.getElementById('student_name').value.trim();
    const incident_type = document.getElementById('incident_type').value.trim();

    if (!student_id || !student_name || !incident_type) {
      showToast('⚠️ Please fill in Student ID, Name, and Reason.');
      return;
    }

    const incId = document.getElementById('incident_id').value;
    const record = {
      id: incId ? parseInt(incId) : null,
      student_id,
      student_name,
      course: document.getElementById('course').value.trim(),
      visit_date: document.getElementById('visit_date').value,
      incident_type,
      incident_ref: document.getElementById('incident_ref').value.trim(),
      time_allowed: document.getElementById('time_allowed').value.trim() || '—',
      reported_by: document.getElementById('reported_by').value.trim(),
      status: document.getElementById('status').value,
      guardian: document.getElementById('guardian_notified').value,
      resolved_date: document.getElementById('resolved_date').value || null,
      description: document.getElementById('description').value.trim(),
      date_reported: new Date().toISOString(),
    };

    let arr = getData();
    if (record.id) {
      const idx = arr.findIndex(i=>i.id===record.id);
      if (idx !== -1) arr[idx] = record;
    } else {
      record.id = nextId(arr);
      arr.push(record);
    }
    saveData(arr);
    loadIncidents();
    showToast(record.id && incId ? '✏️ Record updated!' : ' Gate pass saved!');
    clearForm();
  };

  // --- LOAD TABLE ---
  window.loadIncidents = function() {
    const arr = getData().slice().sort((a,b) => b.id - a.id);
    const tbody = document.getElementById('incidentTbody');

    if (arr.length === 0) {
      tbody.innerHTML = '<tr><td colspan="12" class="empty-state">No records yet. Add a gate pass above.</td></tr>';
      return;
    }

    tbody.innerHTML = arr.map(inc => {
      const dateStr = inc.visit_date ? new Date(inc.visit_date + 'T12:00:00').toLocaleDateString('en-PH', {month:'short',day:'numeric',year:'numeric'}) : '—';
      const statusClass = inc.status === 'Resolved' ? 'status-resolved' : inc.status === 'In Progress' ? 'status-progress' : 'status-open';
      const statusDot = inc.status === 'Resolved' ? '●' : inc.status === 'In Progress' ? '◐' : '○';
      return `<tr>
        <td style="color:var(--muted);font-size:0.75rem;">${inc.id}</td>
        <td><strong>${inc.student_id}</strong></td>
        <td>${inc.student_name}</td>
        <td style="color:var(--muted);">${inc.course || '—'}</td>
        <td>${inc.incident_type}</td>
        <td style="color:var(--muted);">${inc.incident_ref || '—'}</td>
        <td>${inc.time_allowed}</td>
        <td style="color:var(--muted);">${inc.reported_by || '—'}</td>
        <td style="color:var(--muted);">${dateStr}</td>
        <td><span class="status-badge ${statusClass}">${statusDot} ${inc.status}</span></td>
        <td>${inc.guardian === 'Yes' ? ' Yes' : ' No'}</td>
        <td>
          <div class="action-group">
            <button class="btn-print" onclick="showPdfModal(${inc.id})">📄 PDF</button>
            <button class="btn-edit" onclick="editIncident(${inc.id})">✏️ Edit</button>
            <button class="btn-del" onclick="deleteIncident(${inc.id})">🗑</button>
          </div>
        </td>
      </tr>`;
    }).join('');
  };

  // --- EDIT ---
  window.editIncident = function(id) {
    const rec = getData().find(i=>i.id===id);
    if (!rec) return;
    document.getElementById('incident_id').value = rec.id;

    // Student ID + restore fetched state
    const sidEl = document.getElementById('student_id');
    sidEl.value = rec.student_id || '';
    sidEl.classList.add('fetched-ok');

    const nameEl = document.getElementById('student_name');
    nameEl.value = rec.student_name || '';
    nameEl.classList.add('fetched-ok');
    nameEl.setAttribute('readonly','');

    const courseEl = document.getElementById('course');
    courseEl.value = rec.course || '';
    courseEl.classList.add('fetched-ok');
    courseEl.setAttribute('readonly','');

    // Restore student card if found in mock DB
    const student = MOCK_STUDENTS[rec.student_id];
    if (student) {
      const fullName = rec.student_name || buildFullName(student);
      document.getElementById('studentAvatar').textContent = fullName.charAt(0) + (fullName.split(' ').pop() || '').charAt(0);
      document.getElementById('studentFullname').textContent = fullName;
      document.getElementById('studentMeta').textContent = `${rec.course || ''}  ·  ID: ${rec.student_id}`;
      const stTag = document.getElementById('studentStatusTag');
      stTag.textContent = student.academic_status;
      stTag.className = 'student-status-tag' + (student.academic_status !== 'active' ? ' ' + student.academic_status : '');
      document.getElementById('studentCard').style.display = 'flex';
      const label = document.getElementById('fetchLabel');
      label.className = 'fetch-label found';
      label.textContent = '✓ Found';
    }

    document.getElementById('visit_date').value = rec.visit_date || '';
    document.getElementById('incident_type').value = rec.incident_type || '';
    document.getElementById('incident_ref').value = rec.incident_ref || '';
    document.getElementById('time_allowed').value = rec.time_allowed || '';
    document.getElementById('reported_by').value = rec.reported_by || '';
    document.getElementById('status').value = rec.status || 'Open';
    document.getElementById('guardian_notified').value = rec.guardian || 'Yes';
    document.getElementById('resolved_date').value = rec.resolved_date ? rec.resolved_date.slice(0,16) : '';
    document.getElementById('description').value = rec.description || '';
    document.getElementById('editBanner').classList.add('show');
    document.getElementById('editBannerText').textContent = `Editing Gate Pass #${rec.id} — ${rec.student_name}`;
    document.getElementById('exportNewPdfBtn').style.display = 'inline-flex';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // --- DELETE ---
  window.deleteIncident = function(id) {
    if (!confirm('Delete this gate pass record? This cannot be undone.')) return;
    saveData(getData().filter(i=>i.id!==id));
    loadIncidents();
    showToast('🗑 Record deleted.');
  };

  // --- PDF MODAL ---
  window.showPdfModal = function(id) {
    let rec;
    if (id === null) {
      // from form
      rec = {
        id: document.getElementById('incident_id').value || 'NEW',
        student_id: document.getElementById('student_id').value || '_____',
        student_name: document.getElementById('student_name').value || '[Student Name]',
        course: document.getElementById('course').value || '[Course]',
        visit_date: document.getElementById('visit_date').value || new Date().toISOString().slice(0,10),
        incident_type: document.getElementById('incident_type').value || '[Medical Concern]',
        incident_ref: document.getElementById('incident_ref').value || '—',
        time_allowed: document.getElementById('time_allowed').value || '—',
        guardian: document.getElementById('guardian_notified').value || 'Yes',
        description: document.getElementById('description').value || '',
      };
    } else {
      rec = getData().find(i=>i.id===id);
    }
    if (!rec) return;
    currentPdfData = rec;

    const visitDateStr = rec.visit_date ? new Date(rec.visit_date + 'T12:00:00').toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric'}) : '—';

    const preview = `BESTLINK COLLEGE OF THE PHILIPPINES
IPO Road, Brgy. Minuyan Proper, City of San Jose Del Monte, Bulacan
────────────────────────────────────────────────────────

        SCHOOL CLINIC MEDICAL GATE PASS

DATE: ${visitDateStr}

This is to certify that ${rec.student_name}, from ${rec.course || '[Course]'} with
Student ID ${rec.student_id}, visited the school clinic due to a
medical concern. Based on the clinic assessment, the student is
allowed to leave the campus for medical reasons.

Reason:               ${rec.incident_type}
Incident Reference:   ${rec.incident_ref || '—'}
Time Allowed to Leave:${rec.time_allowed || '—'}
Guardian Notified:    ${rec.guardian}

${rec.description ? 'Notes: ' + rec.description + '\n' : ''}
────────────────────────────────────────────────────────
Approved by:

MARIA CARLA P. SERENAS, LPT, RN    ROSEMARIE SALVADOR
       Clinic Nurse                  College Coordinator

Security Verification:
_____________________________
         Security Officer
────────────────────────────────────────────────────────`;

    document.getElementById('pdfPreview').textContent = preview;
    document.getElementById('pdfModal').classList.add('show');
  };

  window.closePdfModal = function() {
    document.getElementById('pdfModal').classList.remove('show');
    currentPdfData = null;
  };

  // --- DOWNLOAD PDF using jsPDF ---
  window.downloadPdf = function() {
    if (!currentPdfData) return;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit: 'mm', format: 'letter' });
    const rec = currentPdfData;
    const pageW = doc.internal.pageSize.getWidth();
    const margin = 25;
    const contentW = pageW - margin * 2;

    const visitDateStr = rec.visit_date
      ? new Date(rec.visit_date + 'T12:00:00').toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric'})
      : new Date().toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric'});

    // ---- HEADER ----
    // School Name bold
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(13);
    doc.setTextColor(13, 35, 64);
    doc.text('BESTLINK COLLEGE OF THE PHILIPPINES', pageW / 2, 30, { align: 'center' });

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.setTextColor(80, 80, 80);
    doc.text('IPO Road, Barangay Minuyan Proper, City of San Jose Del Monte, Bulacan', pageW / 2, 36, { align: 'center' });

    // Horizontal rule
    doc.setDrawColor(13, 35, 64);
    doc.setLineWidth(0.6);
    doc.line(margin, 40, pageW - margin, 40);
    doc.setLineWidth(0.2);
    doc.line(margin, 42, pageW - margin, 42);

    // Title
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(14);
    doc.setTextColor(13, 35, 64);
    doc.text('SCHOOL CLINIC MEDICAL GATE PASS', pageW / 2, 54, { align: 'center' });

    // Date
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.setTextColor(30, 30, 30);
    doc.text('DATE:', margin, 68);
    doc.setFont('helvetica', 'normal');
    doc.text(visitDateStr, margin + 14, 68);

    // Body text
    doc.setFontSize(10.5);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(30, 30, 30);

    const bodyText = `This is to certify that ${rec.student_name}, from ${rec.course || '[Course]'} with Student ID ${rec.student_id}, visited the school clinic due to a medical concern. Based on the clinic assessment, the student is allowed to leave the campus for medical reasons.`;

    const splitBody = doc.splitTextToSize(bodyText, contentW);
    doc.text(splitBody, margin, 78);

    // Fields
    let fieldY = 78 + splitBody.length * 6 + 8;

    function fieldLine(label, value) {
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(10.5);
      doc.setTextColor(30, 30, 30);
      doc.text(label, margin, fieldY);
      // underline for value
      doc.setDrawColor(80, 80, 80);
      doc.setLineWidth(0.3);
      doc.line(margin + 48, fieldY + 1, margin + 48 + 70, fieldY + 1);
      doc.setFont('helvetica', 'normal');
      doc.setTextColor(50, 50, 50);
      doc.text(value || '—', margin + 50, fieldY);
      fieldY += 9;
    }

    fieldLine('Reason:', rec.incident_type);
    fieldLine('Incident Reference:', rec.incident_ref || '—');
    fieldLine('Time Allowed to Leave:', rec.time_allowed || '—');
    fieldLine('Guardian Notified:', rec.guardian);

    if (rec.description) {
      fieldY += 4;
      doc.setFont('helvetica', 'italic');
      doc.setFontSize(9.5);
      doc.setTextColor(80, 80, 80);
      const noteLines = doc.splitTextToSize('Notes: ' + rec.description, contentW);
      doc.text(noteLines, margin, fieldY);
      fieldY += noteLines.length * 5.5 + 4;
    }

    // Horizontal rule before signatures
    fieldY += 6;
    doc.setDrawColor(180, 180, 180);
    doc.setLineWidth(0.3);
    doc.line(margin, fieldY, pageW - margin, fieldY);
    fieldY += 10;

    // Approved by
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(30, 30, 30);
    doc.text('Approved by:', margin, fieldY);
    fieldY += 16;

    // Two signatures
    const col1X = margin;
    const col2X = pageW / 2 + 5;

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.setTextColor(13, 35, 64);
    doc.text('MARIA CARLA P. SERENAS, LPT, RN', col1X, fieldY);
    doc.text('ROSEMARIE SALVADOR', col2X, fieldY);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.setTextColor(80, 80, 80);
    doc.text('Clinic Nurse', col1X + 18, fieldY + 6);
    doc.text('College Coordinator', col2X + 8, fieldY + 6);

    // underlines under names
    doc.setDrawColor(13, 35, 64);
    doc.setLineWidth(0.4);
    const nameW1 = doc.getTextWidth('MARIA CARLA P. SERENAS, LPT, RN');
    const nameW2 = doc.getTextWidth('ROSEMARIE SALVADOR');
    doc.line(col1X, fieldY + 1.5, col1X + nameW1, fieldY + 1.5);
    doc.line(col2X, fieldY + 1.5, col2X + nameW2, fieldY + 1.5);

    fieldY += 26;

    // Security verification
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(30, 30, 30);
    doc.text('Security Verification:', margin, fieldY);
    fieldY += 16;
    doc.setDrawColor(80, 80, 80);
    doc.setLineWidth(0.4);
    doc.line(margin, fieldY, margin + 70, fieldY);
    fieldY += 6;
    doc.setFontSize(9);
    doc.setTextColor(80, 80, 80);
    doc.text('Security Officer', margin + 12, fieldY);

    // Footer
    const footerY = doc.internal.pageSize.getHeight() - 15;
    doc.setDrawColor(180, 180, 180);
    doc.setLineWidth(0.2);
    doc.line(margin, footerY - 4, pageW - margin, footerY - 4);
    doc.setFontSize(7.5);
    doc.setTextColor(130, 130, 130);
    doc.text('BCP School Clinic Gate Pass · Generated: ' + new Date().toLocaleString('en-PH'), pageW / 2, footerY, { align: 'center' });

    // Save
    const filename = `GatePass_${rec.student_id || 'student'}_${(rec.visit_date || new Date().toISOString().slice(0,10)).replace(/-/g,'')}.pdf`;
    doc.save(filename);
    closePdfModal();
    showToast(' PDF downloaded!');
  };

  // --- INIT ---
  loadIncidents();

  // Close modal on overlay click
  document.getElementById('pdfModal').addEventListener('click', function(e) {
    if (e.target === this) closePdfModal();
  });

})();
</script>
</body>
</html>