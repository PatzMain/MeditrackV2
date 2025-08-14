let currentPatientId = null;

// Enhanced search functionality
function enhancedSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const tbody = document.querySelector('#patientsTable tbody');
    const rows = tbody.querySelectorAll('tr:not(.no-results)');
    let visibleCount = 0;

    rows.forEach(row => {
        const cells = row.cells;
        const patientName = (cells[1]?.textContent || '').toLowerCase();
        const patientNumber = (cells[0]?.textContent || '').toLowerCase();
        const phone = (cells[4]?.textContent || '').toLowerCase();
        const email = (cells[5]?.textContent || '').toLowerCase();
        const status = (cells[6]?.textContent || '').toLowerCase();

        const matchesSearch = !searchTerm || 
            patientName.includes(searchTerm) || 
            patientNumber.includes(searchTerm) || 
            phone.includes(searchTerm) || 
            email.includes(searchTerm);

        const matchesStatus = statusFilter === 'all' || status.includes(statusFilter);

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    showNoResults(visibleCount === 0);
}

function showNoResults(show) {
    const tbody = document.querySelector('#patientsTable tbody');
    const noResultsRow = tbody.querySelector('.no-results');
    
    if (show && !noResultsRow) {
        const noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results';
        noResultsRow.innerHTML = `
            <td colspan="8" style="text-align: center; padding: 2rem; color: #666;">
                <p>No patients found matching your criteria</p>
            </td>
        `;
        tbody.appendChild(noResultsRow);
    } else if (!show && noResultsRow) {
        noResultsRow.remove();
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = 'all';
    enhancedSearch();
}

// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Register Patient';
    document.getElementById('form_action').value = 'add_patient';
    document.getElementById('patientForm').reset();
    document.getElementById('patient_id').value = '';
    
    // Set default admission date
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="admission_date"]').value = today;
    document.querySelector('select[name="patient_status"]').value = 'admitted';
    
    // Update submit button text
    document.querySelector('#patientForm button[type="submit"]').textContent = 'Register Patient';
    
    openModal('patientModal');
}

function editPatient(patientId) {
    // Set modal title and action
    document.getElementById('modalTitle').textContent = 'Edit Patient';
    document.getElementById('form_action').value = 'edit_patient';
    document.getElementById('patient_id').value = patientId;
    
    // Update submit button text
    document.querySelector('#patientForm button[type="submit"]').textContent = 'Update Patient';
    
    // Show loading state
    const submitBtn = document.querySelector('#patientForm button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Loading...';
    submitBtn.disabled = true;
    
    // Fetch patient data via AJAX
    fetch(`${window.location.pathname}?action=get_patient&patient_id=${patientId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populatePatientForm(data.data);
                openModal('patientModal');
            } else {
                alert('Error loading patient data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading patient data. Please try again.');
        })
        .finally(() => {
            // Restore button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
}

function populatePatientForm(patient) {
    // Populate all form fields with patient data
    document.querySelector('input[name="first_name"]').value = patient.first_name || '';
    document.querySelector('input[name="last_name"]').value = patient.last_name || '';
    document.querySelector('input[name="date_of_birth"]').value = patient.date_of_birth || '';
    document.querySelector('select[name="gender"]').value = patient.gender || '';
    document.querySelector('select[name="blood_group"]').value = patient.blood_group || '';
    document.querySelector('input[name="phone"]').value = patient.phone || '';
    document.querySelector('input[name="email"]').value = patient.email || '';
    document.querySelector('textarea[name="patient_address"]').value = patient.patient_address || '';
    document.querySelector('input[name="emergency_contact_name"]').value = patient.emergency_contact_name || '';
    document.querySelector('input[name="emergency_contact_phone"]').value = patient.emergency_contact_phone || '';
    document.querySelector('textarea[name="allergies"]').value = patient.allergies || '';
    document.querySelector('textarea[name="medical_conditions"]').value = patient.medical_conditions || '';
    document.querySelector('input[name="assigned_room"]').value = patient.assigned_room || '';
    document.querySelector('input[name="assigned_bed"]').value = patient.assigned_bed || '';
    document.querySelector('input[name="admission_date"]').value = patient.admission_date || '';
    document.querySelector('select[name="patient_status"]').value = patient.patient_status || 'admitted';
}

function deletePatient(patientId) {
    currentPatientId = patientId;
    openModal('deleteModal');
}

function confirmDeletePatient() {
    if (currentPatientId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_patient">
            <input type="hidden" name="patient_id" value="${currentPatientId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function showPatientDetails(event, patientData) {
    event.preventDefault();
    
    // Populate modal with patient data
    document.getElementById('detailPatientNumber').textContent = patientData.patient_number || 'N/A';
    document.getElementById('detailStatus').innerHTML = `<span class="patient-status ${(patientData.patient_status || 'admitted').toLowerCase()}">${(patientData.patient_status || 'Admitted').charAt(0).toUpperCase() + (patientData.patient_status || 'admitted').slice(1)}</span>`;
    document.getElementById('detailFirstName').textContent = patientData.first_name || 'N/A';
    document.getElementById('detailLastName').textContent = patientData.last_name || 'N/A';
    
    // Format date of birth and calculate age
    if (patientData.date_of_birth) {
        const dob = new Date(patientData.date_of_birth);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        document.getElementById('detailDOB').textContent = `${dob.toLocaleDateString()} (${age} years old)`;
    } else {
        document.getElementById('detailDOB').textContent = 'N/A';
    }
    
    document.getElementById('detailGender').textContent = patientData.gender || 'N/A';
    document.getElementById('detailBloodGroup').textContent = patientData.blood_group || 'N/A';
    document.getElementById('detailPhone').textContent = patientData.phone || 'N/A';
    document.getElementById('detailEmail').textContent = patientData.email || 'N/A';
    document.getElementById('detailAddress').textContent = patientData.patient_address || 'N/A';
    document.getElementById('detailEmergencyName').textContent = patientData.emergency_contact_name || 'N/A';
    document.getElementById('detailEmergencyPhone').textContent = patientData.emergency_contact_phone || 'N/A';
    document.getElementById('detailAllergies').textContent = patientData.allergies || 'N/A';
    document.getElementById('detailMedicalConditions').textContent = patientData.medical_conditions || 'N/A';
    document.getElementById('detailRoom').textContent = patientData.assigned_room || 'N/A';
    document.getElementById('detailBed').textContent = patientData.assigned_bed || 'N/A';
    
    // Format dates
    if (patientData.admission_date) {
        const admissionDate = new Date(patientData.admission_date);
        document.getElementById('detailAdmissionDate').textContent = admissionDate.toLocaleDateString();
    } else {
        document.getElementById('detailAdmissionDate').textContent = 'N/A';
    }
    
    if (patientData.discharge_date) {
        const dischargeDate = new Date(patientData.discharge_date);
        document.getElementById('detailDischargeDate').textContent = dischargeDate.toLocaleDateString();
    } else {
        document.getElementById('detailDischargeDate').textContent = 'N/A';
    }
    
    openModal('patientDetailsModal');
}

// Form validation
function validatePatientForm() {
    const requiredFields = ['first_name', 'last_name', 'date_of_birth', 'gender'];
    let isValid = true;
    
    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const value = field.value.trim();
        
        if (!value) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Form submission with validation
document.addEventListener('DOMContentLoaded', function() {
    const patientForm = document.getElementById('patientForm');
    if (patientForm) {
        patientForm.addEventListener('submit', function(e) {
            if (!validatePatientForm()) {
                e.preventDefault();
                alert('Please fill in all required fields with valid information.');
            }
        });
    }
    
    // Add real-time validation feedback
    const inputs = document.querySelectorAll('#patientForm input, #patientForm select, #patientForm textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error') && this.value.trim()) {
                this.classList.remove('error');
            }
        });
    });
});