function showBanner(message, type = 'success') {
    const banner = document.getElementById('assign-head-banner');
    if (!banner) return;

    banner.textContent = message;
    banner.className = `assign-banner assign-banner--${type}`;
    banner.style.display = 'block';

    setTimeout(() => {
        banner.style.display = 'none';
    }, 3000);
}

window.addEventListener('page:loaded', function () {
    const deptSelect     = document.getElementById('dept-select');
    const employeeSelect = document.getElementById('employee-select');
    const loadStatus     = document.getElementById('employee-load-status');
    const assignForm     = document.getElementById('assign-head-form');
    const addDeptForm    = document.getElementById('add-department-form');
    const btnAssign      = document.getElementById('btn-assign-head');
    const base           = window.location.origin + '/sms';

    loadDepartmentTable(); // initial load

    // ─── Add Department ───────────────────────────────────────────────────────

    if (addDeptForm) {
        addDeptForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const departmentName = document.getElementById('dept-name').value.trim();
            if (!departmentName) return;

            const btnSave = addDeptForm.querySelector('.btn-save');
            btnSave.disabled = true;
            btnSave.textContent = 'Saving...';

            fetch(`${base}/modules/school-directress/controllers/DepartmentController.php?action=add_department`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ department_name: departmentName })
            })
                .then(res => res.json())
                .then(data => {
                    showBanner(data.message, data.success ? 'success' : 'error');
                    if (data.success) {
                        addDeptForm.reset();
                        loadDepartmentTable(); // ← refresh table after adding
                    }
                })
                .catch(() => {
                    showBanner('Network error. Please try again.', 'error');
                })
                .finally(() => {
                    btnSave.disabled = false;
                    btnSave.textContent = 'Save Department';
                });
        });
    }

    // ─── Assign Department Head ───────────────────────────────────────────────

    if (!deptSelect) return;

    deptSelect.addEventListener('change', function () {
        const departmentId = this.value;

        employeeSelect.innerHTML = '<option value="">-- Loading... --</option>';
        employeeSelect.disabled = true;
        btnAssign.disabled = true;
        loadStatus.textContent = '';
        loadStatus.className = 'form-hint';

        if (!departmentId) {
            employeeSelect.innerHTML = '<option value="">-- Select a department first --</option>';
            return;
        }

        fetch(`${base}/modules/school-directress/controllers/EmployeeController.php?action=get_by_department&department_id=${departmentId}`)
            .then(res => res.json())
            .then(data => {
                employeeSelect.innerHTML = '<option value="">-- Select Employee --</option>';

                if (!data.success) {
                    loadStatus.textContent = data.message || 'Failed to load employees.';
                    loadStatus.className = 'form-hint hint-error';
                    return;
                }

                if (data.employees.length === 0) {
                    employeeSelect.innerHTML = '<option value="">No employees in this department</option>';
                    loadStatus.className = 'form-hint hint-warning';
                    return;
                }

                data.employees.forEach(emp => {
                    const option = document.createElement('option');
                    option.value = emp.employee_id;
                    option.textContent = `${emp.last_name}, ${emp.first_name}` +
                        (emp.position_name ? ` — ${emp.position_name}` : '');
                    employeeSelect.appendChild(option);
                });

                employeeSelect.disabled = false;
                loadStatus.className = 'form-hint hint-success';
            })
            .catch(() => {
                employeeSelect.innerHTML = '<option value="">-- Error loading employees --</option>';
                loadStatus.className = 'form-hint hint-error';
            });
    });

    employeeSelect.addEventListener('change', function () {
        btnAssign.disabled = !this.value;
    });

    assignForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const departmentId = deptSelect.value;
        const employeeId   = employeeSelect.value;

        if (!departmentId || !employeeId) return;

        btnAssign.disabled = true;
        btnAssign.textContent = 'Assigning...';
        loadStatus.textContent = '';
        loadStatus.className = 'form-hint';

        fetch(`${base}/modules/school-directress/controllers/DepartmentController.php?action=assign_head`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ department_id: departmentId, employee_id: employeeId })
        })
            .then(res => res.json())
            .then(data => {
                showBanner(data.message, data.success ? 'success' : 'error');

                if (data.success) {
                    loadDepartmentTable(); // ← refresh table after assigning head
                    setTimeout(() => {
                        deptSelect.value = '';
                        employeeSelect.innerHTML = '<option value="">-- Select a department first --</option>';
                        employeeSelect.disabled = true;
                        btnAssign.disabled = true;
                        loadStatus.textContent = '';
                        loadStatus.className = 'form-hint';
                    }, 2000);
                }
            })
            .catch(() => {
                showBanner('Network error. Please try again.', 'error');
            })
            .finally(() => {
                btnAssign.disabled = false;
                btnAssign.textContent = 'Assign as Head';
            });
    });
});

// ── LOAD DEPARTMENT TABLE ─────────────────────────────────────────────────────
async function loadDepartmentTable() {
    const tbody = document.querySelector('.department-table tbody');
    if (!tbody) return;

    const base = window.location.origin + '/sms';

    try {
        const res  = await fetch(`${base}/modules/school-directress/controllers/DepartmentController.php?action=get_departments`);
        const json = await res.json();

        if (!json.success || !json.data?.length) {
            tbody.innerHTML = `<tr><td colspan="4" class="muted">No departments found.</td></tr>`;
            return;
        }

        tbody.innerHTML = json.data.map(dept => `
            <tr>
                <td>${esc(dept.department_name)}</td>
                <td>${esc(dept.head_name ?? '—')}</td>
                <td>${esc(dept.employee_count ?? '0')}</td>
            </tr>
        `).join('');

    } catch (err) {
        console.error('loadDepartmentTable error:', err);
    }
}

function esc(str) {
    const d = document.createElement('div');
    d.textContent = String(str ?? '');
    return d.innerHTML;
}