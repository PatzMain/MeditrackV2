// Sidebar and Navigation Functions

// Toggle dropdown menu
function toggleDropdown(event) {
    event.preventDefault();
    const dropdown = event.currentTarget.parentElement;
    dropdown.classList.toggle('active');
    
    // Close other dropdowns
    const allDropdowns = document.querySelectorAll('.nav-dropdown');
    allDropdowns.forEach(d => {
        if (d !== dropdown) {
            d.classList.remove('active');
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set active state for current page
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href.replace('../', ''))) {
            item.classList.add('active');
        }
    });
    
    // Check if current page is in a dropdown
    const dropdownLinks = document.querySelectorAll('.dropdown-content a');
    dropdownLinks.forEach(link => {
        if (currentPath.includes(link.getAttribute('href').replace('../', ''))) {
            link.closest('.nav-dropdown').classList.add('active');
            link.closest('.nav-dropdown').querySelector('.dropdown-toggle').classList.add('active');
        }
    });
});