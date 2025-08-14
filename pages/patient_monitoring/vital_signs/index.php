<?php

include '../../../api/vital_signs.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Vital Signs</title>
    <link rel="stylesheet" href="../../css/pages.css">
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="stylesheet" href="../../css/cards.css">
    <link rel="stylesheet" href="../../css/table.css">
    <link rel="stylesheet" href="../../css/search.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/vital_signs.css">
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $currentPage = 'vital_signs'; 
        include '../../includes/navbar2.php'; 
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Alerts -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <?php echo $_SESSION['success_message']; ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <?php echo $_SESSION['error_message']; ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <div class="page-header">
                <h1 class="page-title">Vital Signs Monitoring</h1>
                <p class="section-subtitle">Track and monitor patient vital signs and health indicators</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon medicines">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['total_records'] ?? 0; ?></div>
                        <div class="stat-label">Total Records</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon available">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['normal_vitals'] ?? 0; ?></div>
                        <div class="stat-label">Normal Readings</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon expired">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['critical_readings'] ?? 0; ?></div>
                        <div class="stat-label">Critical Readings</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon low-stock">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['today_records'] ?? 0; ?></div>
                        <div class="stat-label">Today's Records</div>
                    </div>
                </div>
            </div>

            <!-- Search and Actions -->
            <div class="table-actions">
                <div class="search-container">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search by patient name or number..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <div class="filter-group">
                    <select id="patientFilter" class="filter-select">
                        <option value="all" <?php echo $patient_filter === 'all' ? 'selected' : ''; ?>>All Patients</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?php echo $patient['patient_id']; ?>" <?php echo $patient_filter == $patient['patient_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($patient['patient_number'] . ' - ' . $patient['first_name'] . ' ' . $patient['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="date" id="dateFilter" class="filter-select" value="<?php echo $date_filter; ?>">

                    <button class="btn btn-primary" onclick="openModal('vitalSignsModal')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Record Vital Signs
                    </button>
                </div>
            </div>

            <!-- Vital Signs Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Patient</th>
                            <th>Blood Pressure</th>
                            <th>Heart Rate</th>
                            <th>Temperature</th>
                            <th>O₂ Saturation</th>
                            <th>Resp. Rate</th>
                            <th>Pain Scale</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vital_signs)): ?>
                            <tr>
                                <td colspan="9" class="empty-row">No vital signs records found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vital_signs as $record): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $date = new DateTime($record['recorded_at']);
                                        echo $date->format('M d, Y h:i A'); 
                                        ?>
                                    </td>
                                    <td>
                                        <div class="patient-info">
                                            <span class="patient-number"><?php echo htmlspecialchars($record['patient_number']); ?></span>
                                            <span class="patient-name"><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></span>
                                            <?php if ($record['assigned_room']): ?>
                                                <span class="room-badge"><?php echo htmlspecialchars($record['assigned_room']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="vital-signs-cell">
                                        <?php if ($record['systolic_bp'] && $record['diastolic_bp']): ?>
                                            <?php 
                                            $bp_class = 'vital-normal';
                                            if ($record['systolic_bp'] >= 140 || $record['diastolic_bp'] >= 90) {
                                                $bp_class = 'vital-critical';
                                            } elseif ($record['systolic_bp'] >= 130 || $record['diastolic_bp'] >= 80) {
                                                $bp_class = 'vital-warning';
                                            }
                                            ?>
                                            <span class="<?php echo $bp_class; ?>">
                                                <?php echo $record['systolic_bp'] . '/' . $record['diastolic_bp']; ?> mmHg
                                            </span>
                                        <?php else: ?>
                                            <span class="not-recorded">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vital-signs-cell">
                                        <?php if ($record['heart_rate']): ?>
                                            <?php 
                                            $hr_class = 'vital-normal';
                                            if ($record['heart_rate'] < 60 || $record['heart_rate'] > 100) {
                                                $hr_class = 'vital-warning';
                                            }
                                            if ($record['heart_rate'] < 50 || $record['heart_rate'] > 120) {
                                                $hr_class = 'vital-critical';
                                            }
                                            ?>
                                            <span class="<?php echo $hr_class; ?>">
                                                <?php echo $record['heart_rate']; ?> bpm
                                            </span>
                                        <?php else: ?>
                                            <span class="not-recorded">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vital-signs-cell">
                                        <?php if ($record['temperature']): ?>
                                            <?php 
                                            $temp = $record['temperature'];
                                            $unit = $record['temperature_unit'];
                                            if ($unit === 'F') {
                                                $temp_c = ($temp - 32) * 5/9;
                                            } else {
                                                $temp_c = $temp;
                                            }
                                            $temp_class = 'vital-normal';
                                            if ($temp_c < 36 || $temp_c > 37.5) {
                                                $temp_class = 'vital-warning';
                                            }
                                            if ($temp_c < 35 || $temp_c > 38.5) {
                                                $temp_class = 'vital-critical';
                                            }
                                            ?>
                                            <span class="<?php echo $temp_class; ?>">
                                                <?php echo $record['temperature']; ?>°<?php echo $unit; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="not-recorded">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vital-signs-cell">
                                        <?php if ($record['oxygen_saturation']): ?>
                                            <?php 
                                            $o2_class = 'vital-normal';
                                            if ($record['oxygen_saturation'] < 95) {
                                                $o2_class = 'vital-warning';
                                            }
                                            if ($record['oxygen_saturation'] < 90) {
                                                $o2_class = 'vital-critical';
                                            }
                                            ?>
                                            <span class="<?php echo $o2_class; ?>">
                                                <?php echo $record['oxygen_saturation']; ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="not-recorded">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vital-signs-cell">
                                        <?php if ($record['respiratory_rate']): ?>
                                            <?php 
                                            $rr_class = 'vital-normal';
                                            if ($record['respiratory_rate'] < 12 || $record['respiratory_rate'] > 20) {
                                                $rr_class = 'vital-warning';
                                            }
                                            if ($record['respiratory_rate'] < 10 || $record['respiratory_rate'] > 30) {
                                                $rr_class = 'vital-critical';
                                            }
                                            ?>
                                            <span class="<?php echo $rr_class; ?>">
                                                <?php echo $record['respiratory_rate']; ?> /min
                                            </span>
                                        <?php else: ?>
                                            <span class="not-recorded">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($record['pain_scale'] !== null): ?>
                                            <?php 
                                            $pain_class = 'low';
                                            if ($record['pain_scale'] >= 4 && $record['pain_scale'] <= 6) {
                                                $pain_class = 'moderate';
                                            } elseif ($record['pain_scale'] > 6) {
                                                $pain_class = 'high';
                                            }
                                            ?>
                                            <span class="pain-scale <?php echo $pain_class; ?>">
                                                <?php echo $record['pain_scale']; ?>/10
                                            </span>
                                        <?php else: ?>
                                            <span class="not-recorded">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="action-btn view" onclick="viewVitalDetails(<?php echo htmlspecialchars(json_encode($record)); ?>)" title="View Details">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        <button class="action-btn edit" onclick="editVitalSigns(<?php echo htmlspecialchars(json_encode($record)); ?>)" title="Edit">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteVitalSigns(<?php echo $record['vital_id']; ?>)" title="Delete">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
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

    <!-- Vital Signs Details Modal -->
    <div id="vitalDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Vital Signs Details</h2>
                <span class="close" onclick="closeModal('vitalDetailsModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="vital-details-grid">
                    <div class="detail-section">
                        <h3>Patient Information</h3>
                        <div class="detail-row">
                            <label>Patient:</label>
                            <div id="detailPatient" class="detail-value">N/A</div>
                        </div>
                        <div class="detail-row">
                            <label>Recorded At:</label>
                            <div id="detailRecordedAt" class="detail-value">N/A</div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3>Cardiovascular</h3>
                        <div class="detail-row">
                            <label>Blood Pressure:</label>
                            <div id="detailBP" class="detail-value">N/A</div>
                        </div>
                        <div class="detail-row">
                            <label>Heart Rate:</label>
                            <div id="detailHR" class="detail-value">N/A</div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3>Respiratory</h3>
                        <div class="detail-row">
                            <label>Respiratory Rate:</label>
                            <div id="detailRR" class="detail-value">N/A</div>
                        </div>
                        <div class="detail-row">
                            <label>Oxygen Saturation:</label>
                            <div id="detailO2" class="detail-value">N/A</div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3>Other Measurements</h3>
                        <div class="detail-row">
                            <label>Temperature:</label>
                            <div id="detailTemp" class="detail-value">N/A</div>
                        </div>
                        <div class="detail-row">
                            <label>Blood Glucose:</label>
                            <div id="detailGlucose" class="detail-value">N/A</div>
                        </div>
                        <div class="detail-row">
                            <label>Weight:</label>
                            <div id="detailWeight" class="detail-value">N/A</div>
                        </div>
                        <div class="detail-row">
                            <label>Height:</label>
                            <div id="detailHeight" class="detail-value">N/A</div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3>Assessment</h3>
                        <div class="detail-row">
                            <label>Pain Scale:</label>
                            <div id="detailPain" class="detail-value">N/A</div>
                        </div>
                        <div class="detail-row">
                            <label>Consciousness Level:</label>
                            <div id="detailConsciousness" class="detail-value">N/A</div>
                        </div>
                    </div>

                    <div class="detail-section full-width">
                        <h3>Notes</h3>
                        <div class="detail-row">
                            <div id="detailNotes" class="detail-value">N/A</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-primary" onclick="closeModal('vitalDetailsModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Vital Signs Modal -->
    <div id="vitalSignsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Record Vital Signs</h2>
                <span class="close" onclick="closeModal('vitalSignsModal')">&times;</span>
            </div>
            <form id="vitalSignsForm" method="POST">
                <input type="hidden" name="action" value="add_vital_signs">
                <input type="hidden" name="vital_id" id="vitalId">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label required">Patient</label>
                        <select name="patient_id" class="form-input" required>
                            <option value="">Select Patient</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?php echo $patient['patient_id']; ?>">
                                    <?php echo htmlspecialchars($patient['patient_number'] . ' - ' . $patient['first_name'] . ' ' . $patient['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <h3 class="form-section-title">Respiratory</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Respiratory Rate (/min)</label>
                            <input type="number" name="respiratory_rate" class="form-input" min="8" max="60">
                        </div>
                        <div class="form-group">
                            <label class="form-label">O₂ Saturation (%)</label>
                            <input type="number" name="oxygen_saturation" class="form-input" min="70" max="100">
                        </div>
                    </div>

                    <h3 class="form-section-title">Other Measurements</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Temperature</label>
                            <div class="input-group">
                                <input type="number" name="temperature" class="form-input" step="0.1" min="30" max="45">
                                <select name="temperature_unit" class="form-input" style="width: auto;">
                                    <option value="C">°C</option>
                                    <option value="F">°F</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Blood Glucose (mg/dL)</label>
                            <input type="number" name="blood_glucose" class="form-input" step="0.1" min="20" max="600">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" name="v_weight" class="form-input" step="0.1" min="1" max="300">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Height (cm)</label>
                            <input type="number" name="v_height" class="form-input" step="0.1" min="30" max="250">
                        </div>
                    </div>

                    <h3 class="form-section-title">Assessment</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Pain Scale (0-10)</label>
                            <input type="number" name="pain_scale" class="form-input" min="0" max="10">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Consciousness Level</label>
                            <select name="consciousness_level" class="form-input">
                                <option value="">Not Assessed</option>
                                <option value="alert">Alert</option>
                                <option value="drowsy">Drowsy</option>
                                <option value="confused">Confused</option>
                                <option value="unconscious">Unconscious</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-input" rows="3" placeholder="Enter any observations or notes..."></textarea>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('vitalSignsModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Vital Signs</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirm Delete</h2>
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this vital signs record? This action cannot be undone.</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteVitalSigns()">Delete</button>
            </div>
        </div>
    </div>

    <script src="../../js/navbar.js"></script>
    <script src="../../js/modal.js"></script>
    <script src="../../js/vital_signs.js"></script>
</body>
</html>