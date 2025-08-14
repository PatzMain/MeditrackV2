// Vital Signs Management JavaScript

let currentVitalId = null;

// Search and Filter Functionality
document.getElementById('searchInput').addEventListener('input', function() {
    filterVitalSigns();
});

document.getElementById('patientFilter').addEventListener('change', function() {
    filterVitalSigns();
});

document.getElementById('dateFilter').addEventListener('change', function() {
    filterVitalSigns();
});

function filterVitalSigns() {
    const search = document.getElementById('searchInput').value;
    const patientFilter = document.getElementById('patientFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    // Build query parameters
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (patientFilter !== 'all') params.append('patient', patientFilter);
    if (dateFilter) params.append('date', dateFilter);
    
    // Redirect with filters
    const queryString = params.toString();
    window.location.href = queryString ? `?${queryString}` : window.location.pathname;
}

// View Vital Signs Details
function viewVitalDetails(record) {
    // Populate patient info
    document.getElementById('detailPatient').textContent = 
        `${record.patient_number} - ${record.first_name} ${record.last_name}`;
    
    // Format and display recorded time
    const date = new Date(record.recorded_at);
    document.getElementById('detailRecordedAt').textContent = 
        date.toLocaleString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
    
    // Cardiovascular details
    if (record.systolic_bp && record.diastolic_bp) {
        const bpValue = `${record.systolic_bp}/${record.diastolic_bp} mmHg`;
        let bpClass = 'vital-normal';
        if (record.systolic_bp >= 140 || record.diastolic_bp >= 90) {
            bpClass = 'vital-critical';
        } else if (record.systolic_bp >= 130 || record.diastolic_bp >= 80) {
            bpClass = 'vital-warning';
        }
        document.getElementById('detailBP').innerHTML = `<span class="${bpClass}">${bpValue}</span>`;
    } else {
        document.getElementById('detailBP').textContent = 'Not recorded';
    }
    
    if (record.heart_rate) {
        const hrValue = `${record.heart_rate} bpm`;
        let hrClass = 'vital-normal';
        if (record.heart_rate < 50 || record.heart_rate > 120) {
            hrClass = 'vital-critical';
        } else if (record.heart_rate < 60 || record.heart_rate > 100) {
            hrClass = 'vital-warning';
        }
        document.getElementById('detailHR').innerHTML = `<span class="${hrClass}">${hrValue}</span>`;
    } else {
        document.getElementById('detailHR').textContent = 'Not recorded';
    }
    
    // Respiratory details
    document.getElementById('detailRR').textContent = 
        record.respiratory_rate ? `${record.respiratory_rate} /min` : 'Not recorded';
    
    if (record.oxygen_saturation) {
        const o2Value = `${record.oxygen_saturation}%`;
        let o2Class = 'vital-normal';
        if (record.oxygen_saturation < 90) {
            o2Class = 'vital-critical';
        } else if (record.oxygen_saturation < 95) {
            o2Class = 'vital-warning';
        }
        document.getElementById('detailO2').innerHTML = `<span class="${o2Class}">${o2Value}</span>`;
    } else {
        document.getElementById('detailO2').textContent = 'Not recorded';
    }
    
    // Other measurements
    document.getElementById('detailTemp').textContent = 
        record.temperature ? `${record.temperature}°${record.temperature_unit}` : 'Not recorded';
    document.getElementById('detailGlucose').textContent = 
        record.blood_glucose ? `${record.blood_glucose} mg/dL` : 'Not recorded';
    document.getElementById('detailWeight').textContent = 
        record.v_weight ? `${record.v_weight} kg` : 'Not recorded';
    document.getElementById('detailHeight').textContent = 
        record.v_height ? `${record.v_height} cm` : 'Not recorded';
    
    // Assessment
    if (record.pain_scale !== null) {
        const painValue = `${record.pain_scale}/10`;
        let painClass = 'pain-scale low';
        if (record.pain_scale >= 4 && record.pain_scale <= 6) {
            painClass = 'pain-scale moderate';
        } else if (record.pain_scale > 6) {
            painClass = 'pain-scale high';
        }
        document.getElementById('detailPain').innerHTML = `<span class="${painClass}">${painValue}</span>`;
    } else {
        document.getElementById('detailPain').textContent = 'Not assessed';
    }
    
    const consciousnessLabels = {
        'alert': 'Alert',
        'drowsy': 'Drowsy',
        'confused': 'Confused',
        'unconscious': 'Unconscious'
    };
    document.getElementById('detailConsciousness').textContent = 
        record.consciousness_level ? consciousnessLabels[record.consciousness_level] : 'Not assessed';
    
    // Notes
    document.getElementById('detailNotes').textContent = record.notes || 'No notes recorded';
    
    openModal('vitalDetailsModal');
}

// Edit Vital Signs
function editVitalSigns(record) {
    document.getElementById('modalTitle').textContent = 'Edit Vital Signs';
    document.querySelector('input[name="action"]').value = 'update_vital_signs';
    document.getElementById('vitalId').value = record.vital_id;
    
    // Populate form fields
    document.querySelector('select[name="patient_id"]').value = record.patient_id;
    document.querySelector('select[name="patient_id"]').disabled = true; // Can't change patient
    
    // Cardiovascular
    document.querySelector('input[name="systolic_bp"]').value = record.systolic_bp || '';
    document.querySelector('input[name="diastolic_bp"]').value = record.diastolic_bp || '';
    document.querySelector('input[name="heart_rate"]').value = record.heart_rate || '';
    
    // Respiratory
    document.querySelector('input[name="respiratory_rate"]').value = record.respiratory_rate || '';
    document.querySelector('input[name="oxygen_saturation"]').value = record.oxygen_saturation || '';
    
    // Other measurements
    document.querySelector('input[name="temperature"]').value = record.temperature || '';
    document.querySelector('select[name="temperature_unit"]').value = record.temperature_unit || 'C';
    document.querySelector('input[name="blood_glucose"]').value = record.blood_glucose || '';
    document.querySelector('input[name="v_weight"]').value = record.v_weight || '';
    document.querySelector('input[name="v_height"]').value = record.v_height || '';
    
    // Assessment
    document.querySelector('input[name="pain_scale"]').value = record.pain_scale !== null ? record.pain_scale : '';
    document.querySelector('select[name="consciousness_level"]').value = record.consciousness_level || '';
    
    // Notes
    document.querySelector('textarea[name="notes"]').value = record.notes || '';
    
    openModal('vitalSignsModal');
}

// Delete Vital Signs
function deleteVitalSigns(vitalId) {
    currentVitalId = vitalId;
    openModal('deleteModal');
}

function confirmDeleteVitalSigns() {
    if (currentVitalId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_vital_signs">
            <input type="hidden" name="vital_id" value="${currentVitalId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Form Validation
document.getElementById('vitalSignsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Basic validation for blood pressure
    const systolic = this.querySelector('input[name="systolic_bp"]').value;
    const diastolic = this.querySelector('input[name="diastolic_bp"]').value;
    
    if ((systolic && !diastolic) || (!systolic && diastolic)) {
        alert('Please enter both systolic and diastolic blood pressure values.');
        return;
    }
    
    // Validate temperature unit is selected if temperature is entered
    const temperature = this.querySelector('input[name="temperature"]').value;
    const tempUnit = this.querySelector('select[name="temperature_unit"]').value;
    
    if (temperature && !tempUnit) {
        alert('Please select temperature unit.');
        return;
    }
    
    // Check if at least one vital sign is entered
    const vitalInputs = [
        'systolic_bp', 'diastolic_bp', 'heart_rate', 'respiratory_rate',
        'temperature', 'oxygen_saturation', 'blood_glucose', 'v_weight',
        'v_height', 'pain_scale'
    ];
    
    const hasVitalData = vitalInputs.some(name => {
        const input = this.querySelector(`[name="${name}"]`);
        return input && input.value;
    });
    
    if (!hasVitalData) {
        alert('Please enter at least one vital sign measurement.');
        return;
    }
    
    // Show loading state
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;
    
    // Submit form
    this.submit();
});

// Reset modal when closing
function resetVitalSignsModal() {
    document.getElementById('modalTitle').textContent = 'Record Vital Signs';
    document.querySelector('input[name="action"]').value = 'add_vital_signs';
    document.getElementById('vitalId').value = '';
    document.querySelector('select[name="patient_id"]').disabled = false;
    document.getElementById('vitalSignsForm').reset();
}

// Add event listener for modal close
document.querySelectorAll('.close').forEach(closeBtn => {
    closeBtn.addEventListener('click', function() {
        if (this.closest('#vitalSignsModal')) {
            resetVitalSignsModal();
        }
    });
});

// Real-time vital signs validation feedback
function validateVitalSign(input, min, max, warningMin, warningMax) {
    const value = parseFloat(input.value);
    input.classList.remove('input-normal', 'input-warning', 'input-critical');
    
    if (!value) return;
    
    if (value < min || value > max) {
        input.classList.add('input-critical');
    } else if (value < warningMin || value > warningMax) {
        input.classList.add('input-warning');
    } else {
        input.classList.add('input-normal');
    }
}

// Add validation listeners
document.querySelector('input[name="systolic_bp"]')?.addEventListener('input', function() {
    validateVitalSign(this, 60, 250, 90, 140);
});

document.querySelector('input[name="diastolic_bp"]')?.addEventListener('input', function() {
    validateVitalSign(this, 40, 150, 60, 90);
});

document.querySelector('input[name="heart_rate"]')?.addEventListener('input', function() {
    validateVitalSign(this, 30, 200, 60, 100);
});

document.querySelector('input[name="oxygen_saturation"]')?.addEventListener('input', function() {
    validateVitalSign(this, 70, 100, 95, 100);
});

document.querySelector('input[name="respiratory_rate"]')?.addEventListener('input', function() {
    validateVitalSign(this, 8, 60, 12, 20);
});