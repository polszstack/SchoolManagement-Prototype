// js/pages/admin-convert.js

// Section Management for Convert to Student Page
const SectionManager = {
    convertModal: null,
    
    init() {
        // Initialize Bootstrap modal
        if (typeof bootstrap !== 'undefined') {
            this.convertModal = new bootstrap.Modal(document.getElementById('convertModal'));
        }
        
        // Add change event listener for section selection
        const sectionSelect = document.getElementById('section_id');
        if (sectionSelect) {
            sectionSelect.addEventListener('change', () => this.updateSectionInfo());
        }
        
        // Add course and year level change listeners
        const courseSelect = document.getElementById('course_id');
        const yearLevelSelect = document.getElementById('year_level');
        
        if (courseSelect) {
            courseSelect.addEventListener('change', () => this.loadSections());
        }
        
        if (yearLevelSelect) {
            yearLevelSelect.addEventListener('change', () => this.loadSections());
        }
        
        console.log('SectionManager initialized');
    },
    
    /**
     * Opens the convert modal with applicant details
     */
    openConvertModal(applicantId, applicantName) {
        const applicantIdField = document.getElementById('convert_applicant_id');
        const applicantNameField = document.getElementById('applicant_name');
        
        if (applicantIdField) applicantIdField.value = applicantId;
        if (applicantNameField) applicantNameField.textContent = applicantName;
        
        // Reset form
        this.resetModal();
        
        // Show modal
        if (this.convertModal) {
            this.convertModal.show();
        }
    },
    
    /**
     * Resets the modal to its initial state
     */
    resetModal() {
        const convertForm = document.getElementById('convertForm');
        if (convertForm) convertForm.reset();
        
        this.resetSectionUI();
    },
    
    /**
     * Resets the section UI elements
     */
    resetSectionUI() {
        // Hide all section-related containers
        const loadingEl = document.getElementById('section-loading');
        const containerEl = document.getElementById('section-container');
        const noSectionsEl = document.getElementById('no-sections-message');
        const convertBtn = document.getElementById('convertBtn');
        const sectionSelect = document.getElementById('section_id');
        const sectionInfo = document.getElementById('selected-section-info');
        
        if (loadingEl) loadingEl.classList.remove('active');
        if (containerEl) containerEl.classList.remove('active');
        if (noSectionsEl) noSectionsEl.classList.remove('active');
        if (convertBtn) convertBtn.disabled = true;
        if (sectionSelect) sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
        if (sectionInfo) sectionInfo.innerHTML = '';
    },
    
    /**
     * Loads sections based on selected course and year level
     */
    loadSections() {
        const courseId = document.getElementById('course_id')?.value;
        const yearLevel = document.getElementById('year_level')?.value;
        
        if (!courseId || !yearLevel) {
            this.resetSectionUI();
            return;
        }
        
        // Show loading, hide others
        this.toggleSectionElements('loading', true);
        
        // Fetch sections via AJAX
        fetch(`get_sections.php?course_id=${courseId}&year_level=${yearLevel}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                this.toggleSectionElements('loading', false);
                
                if (data.success && data.sections && data.sections.length > 0) {
                    this.populateSectionsDropdown(data.sections);
                    this.toggleSectionElements('container', true);
                    this.enableConvertButton(false);
                } else {
                    this.showNoSectionsMessage();
                }
            })
            .catch(error => {
                console.error('Error loading sections:', error);
                this.handleSectionLoadError(error);
            });
    },
    
    /**
     * Toggles section UI elements
     */
    toggleSectionElements(element, show) {
        const loadingEl = document.getElementById('section-loading');
        const containerEl = document.getElementById('section-container');
        const noSectionsEl = document.getElementById('no-sections-message');
        
        switch(element) {
            case 'loading':
                if (loadingEl) loadingEl.classList.toggle('active', show);
                if (containerEl) containerEl.classList.remove('active');
                if (noSectionsEl) noSectionsEl.classList.remove('active');
                break;
            case 'container':
                if (loadingEl) loadingEl.classList.remove('active');
                if (containerEl) containerEl.classList.toggle('active', show);
                if (noSectionsEl) noSectionsEl.classList.remove('active');
                break;
            case 'no-sections':
                if (loadingEl) loadingEl.classList.remove('active');
                if (containerEl) containerEl.classList.remove('active');
                if (noSectionsEl) noSectionsEl.classList.toggle('active', show);
                break;
        }
    },
    
    /**
     * Populates the sections dropdown with data
     */
    populateSectionsDropdown(sections) {
        const select = document.getElementById('section_id');
        if (!select) return;
        
        select.innerHTML = '<option value="">-- Select Section --</option>';
        
        sections.forEach(section => {
            const option = this.createSectionOption(section);
            select.appendChild(option);
        });
    },
    
    /**
     * Creates an option element for a section
     */
    createSectionOption(section) {
        const option = document.createElement('option');
        option.value = section.id;
        
        // Calculate section status
        const availableSlots = section.available_slots || 0;
        const currentStudents = section.current_students || 0;
        const maxStudents = section.max_students || 0;
        
        let statusText = '';
        let statusClass = '';
        
        if (availableSlots <= 0) {
            option.disabled = true;
            statusText = ' (FULL)';
            statusClass = 'section-option-full';
        } else if (availableSlots <= 5) {
            statusText = ` (${availableSlots} slots left - LIMITED)`;
            statusClass = 'section-option-warning';
        } else {
            statusText = ` (${availableSlots} slots available)`;
            statusClass = 'section-option-available';
        }
        
        // Format display text
        let displayText = section.section_code;
        if (section.section_name && section.section_name !== section.section_code) {
            displayText += ` - ${section.section_name}`;
        }
        displayText += statusText;
        
        option.textContent = displayText;
        option.className = statusClass;
        
        // Add data attributes
        option.dataset.sectionId = section.id;
        option.dataset.sectionCode = section.section_code;
        option.dataset.sectionName = section.section_name;
        option.dataset.currentStudents = currentStudents;
        option.dataset.maxStudents = maxStudents;
        option.dataset.availableSlots = availableSlots;
        option.dataset.academicYear = section.academic_year || '';
        option.dataset.semester = section.semester || '';
        
        return option;
    },
    
    /**
     * Updates section info display when a section is selected
     */
    updateSectionInfo() {
        const select = document.getElementById('section_id');
        const convertBtn = document.getElementById('convertBtn');
        const sectionInfo = document.getElementById('selected-section-info');
        
        if (!select || !convertBtn || !sectionInfo) return;
        
        const selectedOption = select.options[select.selectedIndex];
        
        if (select.value && selectedOption) {
            convertBtn.disabled = false;
            
            const availableSlots = selectedOption.dataset.availableSlots;
            const maxStudents = selectedOption.dataset.maxStudents;
            const academicYear = selectedOption.dataset.academicYear;
            const semester = selectedOption.dataset.semester;
            
            let infoHtml = `<i class="fas fa-info-circle"></i> `;
            infoHtml += `Selected section has ${availableSlots} available slot(s) out of ${maxStudents}.`;
            
            if (academicYear && semester) {
                infoHtml += `<br><small class="text-muted">${academicYear} | ${semester}</small>`;
            }
            
            sectionInfo.innerHTML = infoHtml;
        } else {
            convertBtn.disabled = true;
            sectionInfo.innerHTML = '';
        }
    },
    
    /**
     * Shows the "no sections available" message
     */
    showNoSectionsMessage() {
        this.toggleSectionElements('no-sections', true);
        
        const convertBtn = document.getElementById('convertBtn');
        if (convertBtn) convertBtn.disabled = true;
        
        const sectionInfo = document.getElementById('selected-section-info');
        if (sectionInfo) {
            sectionInfo.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> No sections available. Please create a section first.';
        }
    },
    
    /**
     * Handles errors during section loading
     */
    handleSectionLoadError(error) {
        this.toggleSectionElements('no-sections', true);
        
        const noSectionsEl = document.getElementById('no-sections-message');
        if (noSectionsEl) {
            noSectionsEl.innerHTML = `
                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                <h5>Error Loading Sections</h5>
                <p>There was an error loading sections. Please try again.</p>
                <small class="text-muted">${error.message}</small>
                <div class="mt-3">
                    <button onclick="SectionManager.loadSections()" class="btn btn-sm btn-primary">
                        <i class="fas fa-sync-alt"></i> Retry
                    </button>
                </div>
            `;
        }
        
        const convertBtn = document.getElementById('convertBtn');
        if (convertBtn) convertBtn.disabled = true;
    },
    
    /**
     * Enables or disables the convert button
     */
    enableConvertButton(enable) {
        const convertBtn = document.getElementById('convertBtn');
        if (convertBtn) convertBtn.disabled = !enable;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    SectionManager.init();
});

// Make functions available globally for onclick events
window.openConvertModal = (applicantId, applicantName) => {
    SectionManager.openConvertModal(applicantId, applicantName);
};

window.resetModal = () => {
    SectionManager.resetModal();
};

window.loadSections = () => {
    SectionManager.loadSections();
};

export default SectionManager;