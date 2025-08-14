// ========================================
// MEDICINE PAGE SPECIFIC FUNCTIONS
// File: pages/js/medicines.js
// ========================================

// Global variables for medicine page only
let deleteUserId = null;
// Remove conflicting currentSort variable - use TableSort's internal state

// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Medicine';
    document.getElementById('form_action').value = 'add_medicine';
    document.getElementById('medicineForm').reset();
    document.getElementById('medicine_id').value = '';
    document.getElementById('medicineModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.classList.remove('modal-open');
    if (modalId === 'deleteModal') {
        deleteUserId = null;
    }
}

function showDescription(event, medicine) {
    event.preventDefault();
    document.getElementById('descName').textContent = medicine.medicine_name || 'N/A';
    document.getElementById('descGeneric').textContent = medicine.medicine_generic_name || 'N/A';
    document.getElementById('descBrand').textContent = medicine.medicine_brand_name || 'N/A';
    document.getElementById('descType').textContent = medicine.medicine_type || 'N/A';
    document.getElementById('descClassification').textContent = medicine.medicine_classification || 'N/A';
    document.getElementById('descDescription').textContent = medicine.medicine_description || 'N/A';
    document.getElementById('descriptionModal').style.display = 'block';
}

function editMedicine(medicineId) {
    // Get medicine data via AJAX
    const formData = new FormData();
    formData.append('action', 'get_medicine');
    formData.append('medicine_id', medicineId);

    fetch(window.location.pathname, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const medicine = data.data;
                document.getElementById('modalTitle').textContent = 'Edit Medicine';
                document.getElementById('form_action').value = 'update_medicine';
                document.getElementById('medicine_id').value = medicine.medicine_id;
                document.getElementById('medicine_name').value = medicine.medicine_name || '';
                document.getElementById('medicine_generic_name').value = medicine.medicine_generic_name || '';
                document.getElementById('medicine_brand_name').value = medicine.medicine_brand_name || '';
                document.getElementById('medicine_type').value = medicine.medicine_type || '';
                document.getElementById('medicine_classification').value = medicine.medicine_classification || '';
                document.getElementById('medicine_dosage').value = medicine.medicine_dosage || '';
                document.getElementById('medicine_unit').value = medicine.medicine_unit || '';
                document.getElementById('medicine_stock').value = medicine.medicine_stock || '';
                document.getElementById('medicine_expiry_date').value = medicine.medicine_expiry_date || '';
                document.getElementById('medicine_description').value = medicine.medicine_description || '';
                document.getElementById('medicineModal').style.display = 'block';
                document.body.classList.add('modal-open');
            } else {
                alert('Error loading medicine data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading medicine data');
        });
}

function confirmDelete(medicineId) {
    deleteUserId = medicineId;
    document.getElementById('deleteModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function deleteMedicine() {
    if (!deleteUserId) return;

    const formData = new FormData();
    formData.append('action', 'delete_medicine');
    formData.append('medicine_id', deleteUserId);

    fetch(window.location.pathname, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('deleteModal');
                location.reload();
            } else {
                alert('Error deleting medicine: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the medicine');
        });
}

// Initialize medicine page
document.addEventListener('DOMContentLoaded', function() {
    // Set up form submission
    const medicineForm = document.getElementById('medicineForm');
    if (medicineForm) {
        medicineForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('medicineModal');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the medicine');
            });
        });
    }
    
    // ESC key to close modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal[style*="block"]');
            openModals.forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target.id);
        }
    });
    
    // Initialize table sorting if TableSort is available
    if (window.TableSort && typeof window.TableSort.initialize === 'function') {
        window.TableSort.initialize();
    }
});

// Note: Search and filter functions are now handled by sort.js
// The following functions are deprecated but kept for backward compatibility
// They will redirect to the TableSort functions if available

// Override with TableSort functions if available
if (window.TableSort) {
    // These will be handled by sort.js auto-initialization
    console.log('TableSort initialized for medicines page');
}