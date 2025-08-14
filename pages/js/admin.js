// Admin Management Functions

let deleteUserId = null;

// Modal functions
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function openEditModal(admin) {
    document.getElementById('editUserId').value = admin.user_id;
    document.getElementById('editUsername').value = admin.username;
    document.getElementById('editPassword').value = '';
    document.getElementById('editModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function openDeleteModal(userId, username) {
    deleteUserId = userId;
    document.querySelector('.delete-submessage').textContent = `Admin "${username}" will be permanently removed.`;
    document.getElementById('deleteModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.classList.remove('modal-open');
    if (modalId === 'deleteModal') {
        deleteUserId = null;
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const button = passwordField.nextElementSibling;
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        button.classList.add('show-active');
    } else {
        passwordField.type = 'password';
        button.classList.remove('show-active');
    }
}

// Filter table function
function filterTable() {
    const search = document.getElementById('searchInput').value;
    const url = new URL(window.location);
    
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    
    clearTimeout(window.filterTimeout);
    window.filterTimeout = setTimeout(() => {
        window.location.href = url.toString();
    }, 500);
}

// Form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Add Admin Form
    const addForm = document.getElementById('addAdminForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_admin');
            
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('addModal');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the admin');
            });
        });
    }
    
    // Edit Admin Form
    const editForm = document.getElementById('editAdminForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'edit_admin');
            
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('editModal');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the admin');
            });
        });
    }
});

// Delete confirmation
function confirmDelete() {
    if (!deleteUserId) return;
    
    const formData = new FormData();
    formData.append('action', 'delete_admin');
    formData.append('user_id', deleteUserId);
    
    fetch(window.location.pathname, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeModal('deleteModal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the admin');
    });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
}

// ESC key to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal[style*="block"]');
        openModals.forEach(modal => {
            closeModal(modal.id);
        });
    }
});