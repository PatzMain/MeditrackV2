<?php
// ========================================
// ADMIN MANAGEMENT - ALL IN ONE FILE
// ========================================
include '../../api/auth.php';

// Check if user is logged in and is a superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: ../../login/login.php');
    exit();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'add_admin':
                $username = trim($_POST['username']);
                $password = trim($_POST['password']);
                $role = 'admin';
                
                $check_stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
                $check_stmt->execute([$username]);
                
                if ($check_stmt->rowCount() > 0) {
                    throw new Exception('Username already exists');
                }
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $insert_stmt->execute([$username, $hashed_password, $role]);
                
                // Log activity
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, logs_item_type, logs_description, logs_status) VALUES (?, 'system', ?, 'add_admin')");
                $log_stmt->execute([$_SESSION['user_id'], "Added new admin: {$username}"]);
                
                echo json_encode(['success' => true, 'message' => 'Admin added successfully']);
                break;
                
            case 'edit_admin':
                $user_id = (int)$_POST['user_id'];
                $username = trim($_POST['username']);
                $password = trim($_POST['password']);
                
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE user_id = ? AND role = 'admin'");
                    $update_stmt->execute([$username, $hashed_password, $user_id]);
                } else {
                    $update_stmt = $pdo->prepare("UPDATE users SET username = ? WHERE user_id = ? AND role = 'admin'");
                    $update_stmt->execute([$username, $user_id]);
                }
                
                echo json_encode(['success' => true, 'message' => 'Admin updated successfully']);
                break;
                
            case 'delete_admin':
                $user_id = (int)$_POST['user_id'];
                $delete_stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ? AND role = 'admin'");
                $delete_stmt->execute([$user_id]);
                
                echo json_encode(['success' => true, 'message' => 'Admin deleted successfully']);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Get admin list
$search = $_GET['search'] ?? '';
$params = [];
$where_clause = "WHERE role = 'admin'";

if (!empty($search)) {
    $where_clause .= " AND username LIKE ?";
    $params[] = "%{$search}%";
}

$stmt = $pdo->prepare("SELECT user_id, username, role, created_at FROM users {$where_clause} ORDER BY created_at DESC");
$stmt->execute($params);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_result = $pdo->query("SELECT COUNT(*) as total_admins FROM users WHERE role = 'admin'");
$stats = $stats_result->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management - MediTrack</title>
    <?php include '../includes/styles.php'; ?>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $currentPage = 'admin_management';
        include '../includes/navbar1.php'; 
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Admin Management</h1>
                <p class="section-subtitle">Manage administrators and their permissions</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_admins']; ?></div>
                    <div class="stat-label">Total Admins</div>
                </div>
            </div>

            <!-- Admin List Section -->
            <div class="section-header">
                <h2 class="section-title">Admin List</h2>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Add Admin
                </button>
            </div>

            <div class="filter-section">
                <input type="text" class="search-input" placeholder="Search admins..." id="searchInput" onkeyup="filterTable()">
            </div>

            <!-- Admin Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($admins)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">N/A</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['username']); ?></td>
                            <td><span class="availability-badge low-stock">Admin</span></td>
                            <td><?php echo date('M d, Y H:i', strtotime($admin['created_at'])); ?></td>
                            <td>
                                <button class="action-btn edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($admin)); ?>)">Edit</button>
                                <button class="action-btn delete-btn" onclick="openDeleteModal(<?php echo $admin['user_id']; ?>, '<?php echo htmlspecialchars($admin['username']); ?>')">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Admin Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Admin</h2>
                <span class="close" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form id="addAdminForm">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-input-container">
                        <input type="password" name="password" class="form-input" id="addPassword" required>
                        <button type="button" class="show-password-btn" onclick="togglePassword('addPassword')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Admin</h2>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form id="editAdminForm">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" id="editUsername" name="username" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password (leave blank to keep current)</label>
                    <div class="password-input-container">
                        <input type="password" name="password" class="form-input" id="editPassword">
                        <button type="button" class="show-password-btn" onclick="togglePassword('editPassword')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal delete-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirm Deletion</h2>
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <div class="delete-icon">⚠</div>
            <div class="delete-message">Are you sure you want to delete this admin?</div>
            <div class="delete-submessage">This action cannot be undone.</div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
    <script src="../js/navbar.js"></script>
    <script src="../js/sort.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>