// Programs & Curriculum Dashboard JavaScript

let currentProgramId = null;
let currentProgramCode = null;
let allSubjects = [];
let editingProgramCode = null;
let programsData = [];

// Toast Notification
globalThis.showToast = function showToast(message, type = 'success') {
    const toast = document.getElementById('toastNotification');
    if (!toast) {
        console.warn('Toast element not found');
        alert(message);
        return;
    }
    toast.textContent = message;
    toast.className = `toast ${type} active`;
    setTimeout(() => {
        toast.classList.remove('active');
    }, 3000);
};

// Load all subjects from API
function loadAllSubjects() {
    console.log('Loading subjects...');
    fetch('./api/get_subjects.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allSubjects = data.subjects;
                console.log('Subjects loaded:', allSubjects.length);
                populateSubjectDropdowns();
            }
        })
        .catch(error => console.error('Error loading subjects:', error));
}

// Populate subject dropdown
function populateSubjectDropdowns() {
    const subjectSelect = document.getElementById('currSubject');
    
    if (!subjectSelect) return;
    
    // Clear and rebuild subject dropdown
    const firstSubjectOption = subjectSelect.options[0];
    subjectSelect.innerHTML = '';
    subjectSelect.appendChild(firstSubjectOption);
    
    allSubjects.forEach(subject => {
        const option = document.createElement('option');
        option.value = subject.subject_id;
        option.textContent = `${subject.subject_code} - ${subject.subject_name}`;
        subjectSelect.appendChild(option);
    });
}

// Handle subject selection - populate read-only fields
function handleSubjectSelection() {
    const subjectId = document.getElementById('currSubject').value;
    
    if (!subjectId) {
        document.getElementById('currSubjectCode').value = '';
        document.getElementById('currSubjectName').value = '';
        document.getElementById('currUnits').value = '';
        return;
    }
    
    const subject = allSubjects.find(s => s.subject_id == subjectId);
    if (subject) {
        document.getElementById('currSubjectCode').value = subject.subject_code;
        document.getElementById('currSubjectName').value = subject.subject_name;
        document.getElementById('currUnits').value = subject.units;
    }
}

// Load curriculum for a program
function loadCurriculumTable(programId) {
    console.log('Loading curriculum for program:', programId);
    
    fetch(`./api/get_curriculum_list.php?program_id=${programId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error:', data.message);
                return;
            }
            
            const tableBody = document.getElementById('curriculumTableBody');
            tableBody.innerHTML = '';
            
            if (data.curriculum && data.curriculum.length > 0) {
                data.curriculum.forEach(item => {
                    const yearLevelText = `${item.year_level}${item.year_level == 1 ? 'st' : item.year_level == 2 ? 'nd' : item.year_level == 3 ? 'rd' : 'th'} Year`;
                    const semesterText = item.semester == 1 ? '1st Semester' : '2nd Semester';
                    
                    const row = `
                        <tr data-curriculum-id="${item.curriculum_id}">
                            <td>${yearLevelText}</td>
                            <td>${semesterText}</td>
                            <td><strong>${item.subject_code}</strong></td>
                            <td>${item.subject_name}</td>
                            <td class="text-center">${item.units}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-danger" onclick="deleteCurriculumEntry(${item.curriculum_id})" title="Remove subject">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
                
                document.getElementById('totalCurriculumSubjects').textContent = data.curriculum.length + ' Subject' + (data.curriculum.length !== 1 ? 's' : '');
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #999;">No subjects added to curriculum</td></tr>';
                document.getElementById('totalCurriculumSubjects').textContent = '0 Subjects';
            }
        })
        .catch(error => {
            console.error('Error loading curriculum:', error);
            showToast('Failed to load curriculum data', 'error');
        });
}

// Delete curriculum entry
globalThis.deleteCurriculumEntry = function(curriculumId) {
    if (!confirm('Are you sure you want to remove this subject from the curriculum?')) return;
    
    fetch('./api/delete_curriculum.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ curriculum_id: curriculumId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Subject removed from curriculum', 'success');
            loadCurriculumTable(currentProgramId);
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error removing subject', 'error');
    });
};

// Add program button handler
function setupAddProgramButton() {
    const addProgramBtn = document.getElementById('addProgramBtn');
    if (addProgramBtn) {
        addProgramBtn.addEventListener('click', function() {
            editingProgramCode = null;
            const form = document.getElementById('addProgramForm');
            if (form) {
                form.reset();
                const codeInput = document.getElementById('programCode');
                if (codeInput) codeInput.disabled = false;
            }
            document.getElementById('addProgramModal').classList.add('active');
            document.querySelector('#addProgramModal .modal-header h3').textContent = 'Add New Program';
        });
    }
}

// Add subject button handler
function setupAddSubjectButton() {
    const addSubjectBtn = document.getElementById('addSubjectBtn');
    if (addSubjectBtn) {
        addSubjectBtn.addEventListener('click', function() {
            if (!currentProgramId) {
                showToast('Please select a program first', 'error');
                return;
            }
            
            // Load subjects and populate dropdowns
            if (allSubjects.length === 0) {
                loadAllSubjects();
            } else {
                populateSubjectDropdowns();
            }
            
            // Clear form
            const form = document.getElementById('addSubjectForm');
            if (form) form.reset();
            
            // Set program name
            const programNameInput = document.getElementById('curriculumProgram');
            if (programNameInput) programNameInput.value = currentProgramCode;
            
            document.getElementById('currSubjectCode').value = '';
            document.getElementById('currSubjectName').value = '';
            document.getElementById('currUnits').value = '';
            
            document.getElementById('addSubjectModal').classList.add('active');
        });
    }
}

// Setup close curriculum button
function setupCloseCurriculumButton() {
    const closeCurriculumBtn = document.getElementById('closeCurriculumBtn');
    if (closeCurriculumBtn) {
        closeCurriculumBtn.addEventListener('click', function() {
            document.getElementById('curriculumSection').style.display = 'none';
            document.getElementById('programsSection').style.display = 'block';
            currentProgramId = null;
            currentProgramCode = null;
        });
    }
}

// Setup add subject form submission
function setupAddSubjectForm() {
    const addSubjectForm = document.getElementById('addSubjectForm');
    if (addSubjectForm) {
        addSubjectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const programId = document.getElementById('curriculumProgramId').value || currentProgramId;
            const yearLevel = document.getElementById('currYearLevel').value;
            const semester = document.getElementById('currSemester').value;
            const subjectId = document.getElementById('currSubject').value;
            
            // Validation
            if (!programId || !yearLevel || !semester || !subjectId) {
                showToast('Please fill in all required fields', 'error');
                return;
            }
            
            const data = {
                program_id: parseInt(programId),
                subject_id: parseInt(subjectId),
                year_level: parseInt(yearLevel),
                semester: parseInt(semester)
            };
            
            fetch('./api/add_curriculum.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Subject added to curriculum successfully', 'success');
                    addSubjectForm.reset();
                    document.getElementById('addSubjectModal').classList.remove('active');
                    
                    // Refresh curriculum table
                    loadCurriculumTable(currentProgramId);
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('API Error:', error);
                showToast('Error: ' + error.message, 'error');
            });
        });
    }
}

// Setup subject selection handler
function setupSubjectSelection() {
    const subjectSelect = document.getElementById('currSubject');
    if (subjectSelect) {
        subjectSelect.addEventListener('change', handleSubjectSelection);
    }
}

// View curriculum function
globalThis.viewCurriculum = function(programId, programCode) {
    currentProgramId = programId;
    currentProgramCode = programCode;
    
    console.log('Viewing curriculum for:', programCode, programId);
    
    // Fetch program details
    fetch(`./api/get_curriculum.php?program_id=${programId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showToast('Error: ' + data.error, 'error');
                return;
            }
            
            // Update program info
            document.getElementById('programCodeDisplay').textContent = data.program_code;
            document.getElementById('programBadge').textContent = data.program_code;
            document.getElementById('programNameDisplay').textContent = data.program_name;
            document.getElementById('programDepartment').textContent = data.description || '';
            
            // Show curriculum section
            document.getElementById('programsSection').style.display = 'none';
            document.getElementById('curriculumSection').style.display = 'block';
            document.getElementById('curriculumTableSection').style.display = 'block';
            
            // Load curriculum table
            loadCurriculumTable(programId);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load curriculum', 'error');
        });
};

// Edit program function
globalThis.editProgram = function(code, name, description) {
    editingProgramCode = code;
    document.getElementById('programCode').value = code;
    document.getElementById('programCode').disabled = true;
    document.getElementById('programName').value = name;
    document.getElementById('description').value = description;
    
    document.querySelector('#addProgramModal .modal-header h3').textContent = 'Edit Program';
    document.getElementById('addProgramModal').classList.add('active');
};

// Delete program function
globalThis.deleteProgram = function(code) {
    if (!confirm(`Are you sure you want to delete program ${code}?`)) return;
    
    fetch('./api/delete_program.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ program_code: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const rows = document.querySelectorAll('#programsTableBody tr');
            rows.forEach(row => {
                if (row.cells[0].textContent.includes(code)) {
                    row.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => row.remove(), 300);
                }
            });
            showToast('Program deleted successfully', 'success');
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting program', 'error');
    });
};

// Setup program form
function attachFormListeners() {
    const addProgramForm = document.getElementById('addProgramForm');
    if (!addProgramForm) return;
    
    const newForm = addProgramForm.cloneNode(true);
    addProgramForm.parentNode.replaceChild(newForm, addProgramForm);
    
    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const code = (document.getElementById('programCode').value || '').trim();
        const name = (document.getElementById('programName').value || '').trim();
        const description = (document.getElementById('description').value || '').trim();
        
        if (!code || !name || !description) {
            showToast('Please fill in all required fields', 'error');
            return;
        }
        
        fetch('./api/save_program.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                program_code: code,
                program_name: name,
                department: description
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const newRow = `
                    <tr>
                        <td>${code}</td>
                        <td>${name}</td>
                        <td>${description}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-info" onclick="viewCurriculum('${data.program_id}', '${code}')"><i class="fas fa-eye"></i> View</button>
                                <button class="btn btn-sm btn-warning" onclick="editProgram('${code}', '${name}', '${description}')"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteProgram('${code}')"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
                document.getElementById('programsTableBody').insertAdjacentHTML('beforeend', newRow);
                showToast('Program added successfully', 'success');
                newForm.reset();
                document.getElementById('addProgramModal').classList.remove('active');
            } else {
                showToast('Error: ' + (data.message || 'Failed to save'), 'error');
            }
        })
        .catch(error => showToast('Error: ' + error.message, 'error'));
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Programs & Curriculum module...');
    
    attachFormListeners();
    setupAddProgramButton();
    setupAddSubjectButton();
    setupCloseCurriculumButton();
    setupAddSubjectForm();
    setupSubjectSelection();
    
    // Setup modal close buttons
    document.querySelectorAll('.modal-close, .modal-close-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal').classList.remove('active');
        });
    });
    
    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
    
    // ESC to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
    
    // Search and filter
    const programSearch = document.getElementById('programSearch');
    if (programSearch) {
        programSearch.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('#programsTableBody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    const departmentFilter = document.getElementById('departmentFilter');
    if (departmentFilter) {
        departmentFilter.addEventListener('change', function() {
            const filterValue = this.value.toLowerCase();
            document.querySelectorAll('#programsTableBody tr').forEach(row => {
                const department = row.cells[2].textContent.toLowerCase();
                row.style.display = (filterValue === '' || department.includes(filterValue)) ? '' : 'none';
            });
        });
    }
    
    console.log('Module initialized successfully');
});

// Re-initialize when page is dynamically loaded
document.addEventListener('page:loaded', function() {
    console.log('Page reloaded - reinitializing...');
    attachFormListeners();
    setupAddSubjectForm();
    setupSubjectSelection();
});
