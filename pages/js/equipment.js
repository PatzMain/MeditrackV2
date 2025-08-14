let deleteEquipmentId = null;

// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Equipment';
    document.getElementById('form_action').value = 'add_equipment';
    document.getElementById('equipmentForm').reset();
    document.getElementById('equipment_id').value = '';
    document.getElementById('equipmentModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.classList.remove('modal-open');
    if (modalId === 'deleteModal') {
        deleteEquipmentId = null;
    }
}

function showDescription(event, equipment) {
    event.preventDefault();
    document.getElementById('descName').textContent = equipment.equipment_name || 'N/A';
    document.getElementById('descStock').textContent = equipment.equipment_stock || 'N/A';
    document.getElementById('descDescription').textContent = equipment.equipment_description || 'N/A';
    document.getElementById('descriptionModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function editEquipment(equipmentId) {
    const formData = new FormData();
    formData.append('action', 'get_equipment');
    formData.append('equipment_id', equipmentId);

    fetch(window.location.pathname, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const equipment = data.data;
                document.getElementById('modalTitle').textContent = 'Edit Equipment';
                document.getElementById('form_action').value = 'edit_equipment';
                document.getElementById('equipment_id').value = equipment.equipment_id;
                document.querySelector('[name="equipment_name"]').value = equipment.equipment_name || '';
                document.querySelector('[name="equipment_stock"]').value = equipment.equipment_stock || '';
                document.querySelector('[name="equipment_description"]').value = equipment.equipment_description || '';
                document.getElementById('equipmentModal').style.display = 'block';
                document.body.classList.add('modal-open');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading equipment data');
        });
}

function deleteEquipment(equipmentId) {
    deleteEquipmentId = equipmentId;
    document.getElementById('deleteModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function confirmDeleteEquipment() {
    if (!deleteEquipmentId) return;

    const formData = new FormData();
    formData.append('action', 'delete_equipment');
    formData.append('equipment_id', deleteEquipmentId);

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
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the equipment');
        });
}