<?php
// Detect current folder name (e.g., "vital_signs" if URL is ".../vital_signs/index.php")
$currentPage = basename(dirname($_SERVER['PHP_SELF']));
?>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>MediTrack</h2>
    </div>
    <div class="nav-menu">
        <a href="../../dashboard/" class="nav-item <?php if ($currentPage === 'dashboard')
            echo 'active'; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Dashboard
        </a>

        <!-- Patient Monitoring Dropdown -->
        <div class="nav-dropdown <?php if (in_array($currentPage, ['patient_consultation', 'vital_signs', 'medical_records', 'nursing_notes']))
            echo 'active'; ?>">
            <a href="#" class="nav-item dropdown-toggle" onclick="toggleDropdown(event)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Patient Monitoring
                <svg class="dropdown-arrow" width="25" height="25" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </a>
            <div class="dropdown-content">
                <a href="../patient_consultation/" class="<?php if ($currentPage === 'patient_consultation')
                    echo 'active'; ?>">Patient
                    Consultation</a>
                <a href="../vital_signs/" class="<?php if ($currentPage === 'vital_signs')
                    echo 'active'; ?>">Vital
                    Signs</a>
                <a href="../medical_records/" class="<?php if ($currentPage === 'medical_records')
                    echo 'active'; ?>">Medical Records</a>
                <a href="../nursing_notes/" class="<?php if ($currentPage === 'nursing_notes')
                    echo 'active'; ?>">Nursing Notes</a>
            </div>
        </div>

        <a href="../../medicines/" class="nav-item <?php if ($currentPage === 'medicines')
            echo 'active'; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="2" />
                <path d="M21 15.5C21 15.5 16 10.5 12 15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Medicines
        </a>

        <a href="../../supplies/" class="nav-item <?php if ($currentPage === 'supplies')
            echo 'active'; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                <line x1="12" y1="18" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                <line x1="9" y1="15" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Supplies
        </a>

        <a href="../../equipment/" class="nav-item <?php if ($currentPage === 'equipment')
            echo 'active'; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                <path
                    d="M16 21V5C16 4.46957 15.7893 3.96086 15.4142 3.58579C15.0391 3.21071 14.5304 3 14 3H10C9.46957 3 8.96086 3.21071 8.58579 3.58579C8.21071 3.96086 8 4.46957 8 5V21"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Equipment
        </a>
    </div>
    <div class="logout">
        <a href="../logout/" class="nav-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <polyline points="16,17 21,12 16,7" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Logout
        </a>
    </div>
</nav>