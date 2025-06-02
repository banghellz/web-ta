
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('main-sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');

        // Check localStorage for saved state
        const sidebarState = localStorage.getItem('sidebarMinimized');
        if (sidebarState === 'true') {
            sidebar.classList.add('minimized');
        }

        // Toggle sidebar on button click
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('minimized');
            // Save state to localStorage
            localStorage.setItem('sidebarMinimized', sidebar.classList.contains('minimized'));
        });

        // Add active class to current page link
        const currentLocation = window.location.href;
        const menuItems = document.querySelectorAll('.nav-link');
        const dropdownItems = document.querySelectorAll('.dropdown-item');

        menuItems.forEach(item => {
            if (item.href === currentLocation) {
                item.classList.add('active');

                // If in dropdown, expand the dropdown
                const dropdownParent = item.closest('.nav-item.dropdown');
                if (dropdownParent) {
                    const dropdownToggle = dropdownParent.querySelector('.dropdown-toggle');
                    if (dropdownToggle) {
                        dropdownToggle.setAttribute('aria-expanded', 'true');
                        const dropdownMenu = dropdownParent.querySelector('.dropdown-menu');
                        if (dropdownMenu) {
                            dropdownMenu.classList.add('show');
                        }
                    }
                }
            }
        });

        dropdownItems.forEach(item => {
            if (item.href === currentLocation) {
                item.classList.add('active');

                // Expand the parent dropdown
                const dropdownParent = item.closest('.nav-item.dropdown');
                if (dropdownParent) {
                    const dropdownToggle = dropdownParent.querySelector('.dropdown-toggle');
                    if (dropdownToggle) {
                        dropdownToggle.classList.add('active');
                        dropdownToggle.setAttribute('aria-expanded', 'true');
                        const dropdownMenu = item.closest('.dropdown-menu');
                        if (dropdownMenu) {
                            dropdownMenu.classList.add('show');
                        }
                    }
                }
            }
        });
    });
