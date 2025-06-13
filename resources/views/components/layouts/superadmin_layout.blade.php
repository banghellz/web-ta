<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel App') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tabler CSS and Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.22.0/tabler-icons.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    @livewireStyles
    @stack('styles')

    <style>
        .notification-item {
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: var(--tblr-bg-surface-secondary);
        }

        .notification-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .notification-icon.user_registered {
            background-color: var(--tblr-success-lt);
            color: var(--tblr-success);
        }

        .notification-icon.tool_added {
            background-color: var(--tblr-primary-lt);
            color: var(--tblr-primary);
        }

        .notification-icon.tool_deleted {
            background-color: var(--tblr-danger-lt);
            color: var(--tblr-danger);
        }

        .notification-icon.tool_missing {
            background-color: var(--tblr-warning-lt);
            color: var(--tblr-warning);
        }

        .notification-icon.tool_reclaimed {
            background-color: var(--tblr-info-lt);
            color: var(--tblr-info);
        }

        /* Enhanced toast styling */
        .toast-container {
            z-index: 1070 !important;
        }

        .toast.text-success {
            border-left: 4px solid var(--tblr-success);
        }

        .toast.text-danger {
            border-left: 4px solid var(--tblr-danger);
        }

        .toast.text-warning {
            border-left: 4px solid var(--tblr-warning);
        }

        .toast.text-info {
            border-left: 4px solid var(--tblr-info);
        }
    </style>
</head>

<body>
    <!-- Enhanced toast container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1070;">
        <div id="notificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i id="toastIcon" class="ti ti-bell me-2"></i>
                <strong id="toastTitle" class="me-auto">Notification</strong>
                <small id="toastTime" class="text-muted">just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <span id="toastMessage"></span>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <x-superadmin_sidebar />

    <!-- Page Content -->
    <div class="page-wrapper">
        <!-- Header -->
        <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-nav flex-row order-md-last">
                    <div class="d-none d-md-flex">
                        <!-- Theme Toggle -->
                        <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode">
                            <i class="ti ti-moon icon"></i>
                        </a>
                        <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode">
                            <i class="ti ti-sun icon"></i>
                        </a>

                        <!-- Notification Dropdown -->
                        <div class="nav-item dropdown d-none d-md-flex me-3">
                            <a href="#" class="nav-link px-0 position-relative" data-bs-toggle="dropdown"
                                id="notificationButton">
                                <i class="ti ti-bell icon"></i>
                                <span id="notificationBadge"
                                    class="badge bg-red badge-pill position-absolute top-0 start-100 translate-middle"
                                    style="display: none;">0</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-card" style="width: 350px;">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Recent Activity</h3>
                                        <div class="card-actions">
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="clearAllNotifications()">
                                                <i class="ti ti-trash"></i> Clear all
                                            </button>
                                        </div>
                                    </div>

                                    <div style="max-height: 400px; overflow-y: auto;" id="notificationsList">
                                        <div class="text-center p-3">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                            <div class="mt-2 text-muted">Loading...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User dropdown -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            @php
                                $user = auth()->user();
                                $photo = $user->detail?->pict
                                    ? asset('profile_pictures/' . $user->detail->pict)
                                    : 'https://www.gravatar.com/avatar/' . md5($user->email ?? 'default@example.com');
                            @endphp
                            <span class="avatar avatar-sm" style="background-image: url({{ $photo }})"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ auth()->user()->name ?? 'User' }}</div>
                                <div class="mt-1 small text-muted">{{ auth()->user()->role ?? 'Admin' }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="#" class="dropdown-item">Profile</a>
                            <div class="dropdown-divider"></div>
                            <form method="GET" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Page title actions -->
                <div class="navbar-nav flex-row">
                    <div class="nav-item d-md-flex me-3">
                        <div class="btn-list">
                            @stack('header-actions')
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page body -->
        <div class="page-body">
            <div class="container-xl">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ms-lg-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item"><a href="#" class="link-secondary">Help</a></li>
                            <li class="list-inline-item"><a href="#" class="link-secondary">Support</a></li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel App') }}. All rights reserved.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>

    <!-- Enhanced Unified Toast System -->
    <script>
        // Enhanced Unified Toast System - Global
        window.UnifiedToastSystem = {
            // Initialize toast system
            init: function() {
                this.toastElement = document.getElementById('notificationToast');
                this.toastIcon = document.getElementById('toastIcon');
                this.toastTitle = document.getElementById('toastTitle');
                this.toastMessage = document.getElementById('toastMessage');
                this.toastTime = document.getElementById('toastTime');

                console.log('Unified Toast System initialized');
            },

            // Main show function
            show: function(type, message, title = null) {
                if (!this.toastElement) {
                    this.init();
                }

                console.log('Toast show called:', {
                    type,
                    message,
                    title
                });

                // Clean message
                const cleanMessage = this.cleanMessage(message);
                title = title || this.getDefaultTitle(type);

                // Set content
                this.toastTitle.textContent = title;
                this.toastMessage.textContent = cleanMessage;
                this.toastTime.textContent = 'just now';

                // Set icon and styling
                this.setToastStyle(type);

                // Show toast
                this.displayToast();
            },

            // Convenience methods
            success: function(message, title = 'Success') {
                this.show('success', message, title);
            },

            error: function(message, title = 'Error') {
                this.show('error', message, title);
            },

            warning: function(message, title = 'Warning') {
                this.show('warning', message, title);
            },

            info: function(message, title = 'Info') {
                this.show('info', message, title);
            },

            // Clean HTML entities from message
            cleanMessage: function(message) {
                if (!message) return '';

                // Create temporary element to decode HTML entities
                const textarea = document.createElement('textarea');
                textarea.innerHTML = message;
                return textarea.value;
            },

            // Get default title based on type
            getDefaultTitle: function(type) {
                const titles = {
                    'success': 'Success',
                    'error': 'Error',
                    'warning': 'Warning',
                    'info': 'Information'
                };
                return titles[type] || 'Notification';
            },

            // Set toast styling based on type
            setToastStyle: function(type) {
                // Reset classes
                this.toastElement.className = 'toast';

                // Set icon
                const icons = {
                    'success': 'ti-check',
                    'error': 'ti-alert-circle',
                    'warning': 'ti-alert-triangle',
                    'info': 'ti-info-circle'
                };

                this.toastIcon.className = `ti ${icons[type] || icons.info} me-2`;

                // Add color class
                if (type !== 'info') {
                    const colorClass = type === 'error' ? 'text-danger' : `text-${type}`;
                    this.toastElement.classList.add(colorClass);
                }
            },

            // Display the toast
            displayToast: function() {
                const bsToast = new bootstrap.Toast(this.toastElement, {
                    autohide: true,
                    delay: 5000
                });
                bsToast.show();
            }
        };

        // Backward compatibility functions
        function showToast(message, type = 'info') {
            if (window.UnifiedToastSystem) {
                window.UnifiedToastSystem.show(type, message);
            } else {
                console.warn('Toast system not available');
            }
        }

        // Enhanced public function for external scripts
        window.showNotificationToast = function(message, type = 'info') {
            console.log('Legacy toast function called:', {
                message,
                type
            });

            if (window.UnifiedToastSystem) {
                window.UnifiedToastSystem.show(type, message);
            } else {
                showToast(message, type);
            }
        };

        // Initialize when document is ready
        $(document).ready(function() {
            // Initialize toast system
            window.UnifiedToastSystem.init();

            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Load notifications on page load
            loadNotifications();
            updateNotificationCount();

            // Auto-refresh notifications every 30 seconds
            setInterval(function() {
                loadNotifications();
                updateNotificationCount();
            }, 30000);

            // Load notifications when dropdown is opened
            $('#notificationButton').on('click', function() {
                loadNotifications();
            });

            console.log('Layout and notification system ready');
        });

        // Notification system functions
        function displayNotifications(notifications) {
            const container = $('#notificationsList');

            if (!notifications || notifications.length === 0) {
                container.html('<div class="text-center p-3 text-muted">No notifications</div>');
                return;
            }

            let html = '';
            notifications.forEach(function(notification) {
                const icon = getNotificationIcon(notification.type);
                const timeAgo = notification.time_ago;

                // Decode HTML entities for title and message
                const decodedTitle = $('<div>').html(notification.title).text();
                const decodedMessage = $('<div>').html(notification.message).text();

                html += `
            <div class="notification-item border-bottom p-3" data-id="${notification.id}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon ${notification.type} me-3">
                        <i class="ti ${icon}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${escapeHtml(decodedTitle)}</div>
                        <div class="text-muted small">${escapeHtml(decodedMessage)}</div>
                        <div class="text-muted small mt-1">
                            <i class="ti ti-clock"></i> ${timeAgo}
                        </div>
                    </div>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-sm btn-ghost-secondary" 
                                onclick="deleteNotification(${notification.id})" 
                                title="Delete">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
            });

            container.html(html);
        }

        function escapeHtml(text) {
            if (!text) return '';

            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        function loadNotifications() {
            $.ajax({
                url: '/superadmin/notifications',
                method: 'GET',
                data: {
                    limit: 10
                },
                success: function(response) {
                    if (response.success) {
                        displayNotifications(response.notifications);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading notifications:', error);
                    $('#notificationsList').html(
                        '<div class="text-center p-3 text-muted">Failed to load notifications</div>'
                    );
                }
            });
        }

        function updateNotificationCount() {
            $.ajax({
                url: '/superadmin/notifications/count',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const count = response.total_count;
                        const badge = $('#notificationBadge');

                        if (count > 0) {
                            badge.text(count > 99 ? '99+' : count).show();
                        } else {
                            badge.hide();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error getting notification count:', error);
                }
            });
        }

        function getNotificationIcon(type) {
            const icons = {
                'user_registered': 'ti-user-plus',
                'tool_added': 'ti-plus',
                'tool_deleted': 'ti-trash',
                'tool_missing': 'ti-alert-triangle',
                'tool_reclaimed': 'ti-check'
            };
            return icons[type] || 'ti-bell';
        }

        function deleteNotification(id) {
            if (!confirm('Are you sure you want to delete this notification?')) {
                return;
            }

            $.ajax({
                url: `/superadmin/notifications/${id}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $(`.notification-item[data-id="${id}"]`).fadeOut(300, function() {
                            $(this).remove();
                            if ($('#notificationsList .notification-item').length === 0) {
                                $('#notificationsList').html(
                                    '<div class="text-center p-3 text-muted">No notifications</div>'
                                );
                            }
                        });
                        updateNotificationCount();

                        // Use unified toast system
                        if (window.UnifiedToastSystem) {
                            window.UnifiedToastSystem.success('Notification deleted successfully');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting notification:', error);

                    // Use unified toast system
                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.error('Failed to delete notification');
                    }
                }
            });
        }

        function clearAllNotifications() {
            if (!confirm('Are you sure you want to clear all notifications?')) {
                return;
            }

            $.ajax({
                url: '/superadmin/notifications/clear-all',
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        $('#notificationsList').html(
                            '<div class="text-center p-3 text-muted">No notifications</div>');
                        $('#notificationBadge').hide();

                        // Use unified toast system
                        if (window.UnifiedToastSystem) {
                            window.UnifiedToastSystem.success('All notifications cleared');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error clearing notifications:', error);

                    // Use unified toast system
                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.error('Failed to clear notifications');
                    }
                }
            });
        }

        // Refresh notifications function for external use
        window.refreshNotifications = function() {
            loadNotifications();
            updateNotificationCount();
        };
    </script>

    @livewireScripts
    @stack('scripts')
</body>

</html>
