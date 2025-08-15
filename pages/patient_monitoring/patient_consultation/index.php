<?php
include '../../../api/patient_consultation.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Patient Consultation</title>
    <?php include '../../includes/styles2.php'; ?>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $currentPage = 'patient_consultation';
        include '../../includes/navbar2.php';
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Success/Error Messages -->
            <?php include '../../includes/alert.php'; ?>

            <div class="page-header">
                <h1 class="page-title">Patient Consultation</h1>
                <p class="section-subtitle">Manage patient registration and consultation records</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon medicines">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['total_patients'] ?? 0; ?></div>
                        <div class="stat-label">Total Patients</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon available">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['admitted'] ?? 0; ?></div>
                        <div class="stat-label">Admitted</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4l2 3h9a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['discharged'] ?? 0; ?></div>
                        <div class="stat-label">Discharged</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon danger">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 21h10l2-12H5l2 12zM5 7h14l-1-4H6L5 7z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['transferred'] ?? 0; ?></div>
                        <div class="stat-label">Transferred</div>
                    </div>
                </div>
            </div>

            <!-- Section Header -->
            <div class="section-header">
                <h2 class="section-title">Patient List</h2>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        Register Patient
                    </button>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; width: 100%;">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search patients..."
                        value="<?php echo htmlspecialchars($search); ?>" onkeyup="enhancedSearch()" autocomplete="off">

                    <select id="statusFilter" class="filter-select" onchange="enhancedSearch()">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Patients
                        </option>
                        <option value="admitted" <?php echo $status_filter === 'admitted' ? 'selected' : ''; ?>>Admitted
                        </option>
                        <option value="discharged" <?php echo $status_filter === 'discharged' ? 'selected' : ''; ?>>
                            Discharged</option>
                        <option value="transferred" <?php echo $status_filter === 'transferred' ? 'selected' : ''; ?>>
                            Transferred</option>
                        <option value="deceased" <?php echo $status_filter === 'deceased' ? 'selected' : ''; ?>>Deceased
                        </option>
                    </select>

                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
                </div>
            </div>

            <!-- Patients Table -->
            <div class="table-container">
                <table class="table" id="patientsTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable(0)">Patient Number</th>
                            <th class="sortable" onclick="sortTable(1)">Name</th>
                            <th class="sortable" onclick="sortTable(2)">Age</th>
                            <th class="sortable" onclick="sortTable(3)">Gender</th>
                            <th class="sortable" onclick="sortTable(4)">Contact</th>
                            <th class="sortable" onclick="sortTable(5)">Status</th>
                            <th class="sortable" onclick="sortTable(6)">Admission Date</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($patients)): ?>
                            <tr id="noResultsRow">
                                <td colspan="8" class="empty-state">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    <p>No patients found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($patient['patient_number'] ?? 'N/A'); ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')); ?></strong>
                                        <?php if (!empty($patient['blood_group'])): ?>
                                            <div class="patient-number">Blood Type:
                                                <?php echo htmlspecialchars($patient['blood_group']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($patient['date_of_birth'])) {
                                            $dob = new DateTime($patient['date_of_birth']);
                                            $today = new DateTime();
                                            $age = $today->diff($dob)->y;
                                            echo $age . ' years';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($patient['gender'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if (!empty($patient['phone'])): ?>
                                            <div><?php echo htmlspecialchars($patient['phone']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($patient['email'])): ?>
                                            <div class="patient-number"><?php echo htmlspecialchars($patient['email']); ?></div>
                                        <?php endif; ?>
                                        <?php if (empty($patient['phone']) && empty($patient['email'])): ?>N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span
                                            class="patient-status <?php echo strtolower($patient['patient_status'] ?? 'admitted'); ?>">
                                            <?php echo ucfirst($patient['patient_status'] ?? 'Admitted'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($patient['admission_date'])) {
                                            echo date('M d, Y', strtotime($patient['admission_date']));
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="action-btn view-btn"
                                            onclick="showPatientDetails(event, <?php echo htmlspecialchars(json_encode($patient)); ?>)"
                                            title="View Patient Details">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 4.5C7.305 4.5 3.195 7.385 1.5 12c1.695 4.615 5.805 7.5 10.5 7.5s8.805-2.885 10.5-7.5c-1.695-4.615-5.805-7.5-10.5-7.5z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </button>
                                        <button class="action-btn edit-btn"
                                            onclick="editPatient(<?php echo $patient['patient_id']; ?>)" title="Edit Patient">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Patient Details Modal -->
    <div id="patientDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="detailsModalTitle">Patient Details</h2>
                <span class="close" onclick="closeModal('patientDetailsModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="detail-row">
                        <label>Patient Number:</label>
                        <div id="detailPatientNumber" class="detail-value">N/A</div>
                    </div>
                    <div class="detail-row">
                        <label>Status:</label>
                        <div id="detailStatus" class="detail-value">N/A</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="detail-row">
                        <label>First Name:</label>
                        <div id="detailFirstName" class="detail-value">N/A</div>
                    </div>
                    <div class="detail-row">
                        <label>Last Name:</label>
                        <div id="detailLastName" class="detail-value">N/A</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="detail-row">
                        <label>Date of Birth:</label>
                        <div id="detailDOB" class="detail-value">N/A</div>
                    </div>
                    <div class="detail-row">
                        <label>Gender:</label>
                        <div id="detailGender" class="detail-value">N/A</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="detail-row">
                        <label>Blood Group:</label>
                        <div id="detailBloodGroup" class="detail-value">N/A</div>
                    </div>
                    <div class="detail-row">
                        <label>Phone:</label>
                        <div id="detailPhone" class="detail-value">N/A</div>
                    </div>
                </div>

                <div class="detail-row">
                    <label>Email:</label>
                    <div id="detailEmail" class="detail-value">N/A</div>
                </div>

                <div class="detail-row">
                    <label>Address:</label>
                    <div id="detailAddress" class="detail-value">N/A</div>
                </div>

                <div class="form-row">
                    <div class="detail-row">
                        <label>Emergency Contact Name:</label>
                        <div id="detailEmergencyName" class="detail-value">N/A</div>
                    </div>
                    <div class="detail-row">
                        <label>Emergency Contact Phone:</label>
                        <div id="detailEmergencyPhone" class="detail-value">N/A</div>
                    </div>
                </div>

                <div class="detail-row">
                    <label>Allergies:</label>
                    <div id="detailAllergies" class="detail-value">N/A</div>
                </div>

                <div class="detail-row">
                    <label>Medical Conditions:</label>
                    <div id="detailMedicalConditions" class="detail-value">N/A</div>
                </div>

                <div class="form-row">
                    <div class="detail-row">
                        <label>Assigned Room:</label>
                        <div id="detailRoom" class="detail-value">N/A</div>
                    </div>
                    <div class="detail-row">
                        <label>Assigned Bed:</label>
                        <div id="detailBed" class="detail-value">N/A</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="detail-row">
                        <label>Admission Date:</label>
                        <div id="detailAdmissionDate" class="detail-value">N/A</div>
                    </div>
                    <div class="detail-row">
                        <label>Discharge Date:</label>
                        <div id="detailDischargeDate" class="detail-value">N/A</div>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-primary" onclick="closeModal('patientDetailsModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Patient Modal -->
    <div id="patientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Register Patient</h2>
                <span class="close" onclick="closeModal('patientModal')">&times;</span>
            </div>
            <form id="patientForm" method="POST">
                <input type="hidden" id="patient_id" name="patient_id">
                <input type="hidden" id="form_action" name="action" value="add_patient">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-input" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-input" autocomplete="off" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-input" required>
                            <option value="">Select Gender</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-input">
                            <option value="">Select Blood Group</option>
                            <optgroup label="A">
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                            </optgroup>

                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-input" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="patient_address" class="form-input" rows="3"
                        placeholder="Enter patient address..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-input" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone" class="form-input" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Allergies</label>
                    <textarea name="allergies" class="form-input" rows="2"
                        placeholder="List any known allergies..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Medical Conditions</label>
                    <textarea name="medical_conditions" class="form-input" rows="3"
                        placeholder="List any existing medical conditions..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Assigned Room</label>
                        <input type="text" name="assigned_room" class="form-input" placeholder="e.g., Room 101"
                            autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Assigned Bed</label>
                        <input type="text" name="assigned_bed" class="form-input" placeholder="e.g., Bed A"
                            autocomplete="off">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Admission Date</label>
                        <input type="date" name="admission_date" class="form-input"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Patient Status</label>
                        <select name="patient_status" class="form-input">
                            <option value="admitted">Admitted</option>
                            <option value="discharged">Discharged</option>
                            <option value="transferred">Transferred</option>
                            <option value="deceased">Deceased</option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('patientModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Patient</button>
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
            <div class="delete-message">Are you sure you want to delete this patient?</div>
            <div class="delete-submessage">This action cannot be undone and will remove all patient records.</div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeletePatient()">Delete</button>
            </div>
        </div>
    </div>
    <script src="../../js/sort.js"></script>
    <script src="../../js/patient_consultation.js"></script>
</body>

</html>