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
    <link rel="stylesheet" href="/assets/css/tabler.min.css">
    <link rel="stylesheet" href="/assets/css/tabler-icons.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="/assets/css/jquery.dataTables.min.css">

    @livewireStyles
    @stack('styles')

    <style>
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

        /* Tabler icons styling */
        .ti {
            width: 1.25rem;
            height: 1.25rem;
            font-size: 1.25rem;
        }

        .icon.ti {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Enhanced sidebar styles */
        :root {
            --sidebar-width: 240px;
            --sidebar-min-width: 70px;
            --sidebar-transition: 0.3s ease;
            --sidebar-border: rgba(98, 105, 118, 0.16);
            --sidebar-active-color: var(--tblr-primary, #066fd1);
            --sidebar-active-bg: rgba(6, 111, 209, 0.1);
            --sidebar-hover-bg: rgba(6, 111, 209, 0.05);
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
    <x-user_sidebar />

    <!-- Page Content -->
    <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">{{ $pageTitle }}</h2>
                    </div>
                    <div class="col-auto">
                        <div class="text-muted">"Tools don't build things—people do."</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages Section - Hanya untuk non-profile pages -->
        @if (session('email_sent') ||
                session('email_failed') ||
                (session('success') && !request()->routeIs('user.profile.*')) ||
                (session('error') && !request()->routeIs('user.profile.*')) ||
                session('warning'))
            <div class="container-xl mt-3">
                {{-- Email Success Message --}}
                @if (session('email_sent'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                    <path d="M3 7l9 6l9 -6" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Welcome to ATMI System!</h4>
                                <div class="text-secondary">{{ session('email_sent') }}</div>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Email Failed Message --}}
                @if (session('email_failed'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 9v2m0 4v.01" />
                                    <path
                                        d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Registration Complete</h4>
                                <div class="text-secondary">{{ session('email_failed') }}</div>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- General Success Message - Tidak ditampilkan untuk profile pages --}}
                @if (session('success') && !request()->routeIs('user.profile.*'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Success!</h4>
                                <div class="text-secondary">{{ session('success') }}</div>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- General Error Message - Tidak ditampilkan untuk profile pages --}}
                @if (session('error') && !request()->routeIs('user.profile.*'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                    <path d="M12 8v4" />
                                    <path d="M12 16h.01" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Error!</h4>
                                <div class="text-secondary">{{ session('error') }}</div>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- General Warning Message --}}
                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 9v2m0 4v.01" />
                                    <path
                                        d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Warning!</h4>
                                <div class="text-secondary">{{ session('warning') }}</div>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif
            </div>
        @endif

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
                                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel App') }}. All rights
                                reserved.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/tabler.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>

    <!-- Enhanced Unified Toast System dengan Tabler Icons - SAMA SEPERTI SUPERADMIN -->
    <script>
        // Enhanced Unified Toast System - Global dengan Tabler Icons
        window.UnifiedToastSystem = {
            // Initialize toast system
            init: function() {
                this.toastElement = document.getElementById('notificationToast');
                this.toastIcon = document.getElementById('toastIcon');
                this.toastTitle = document.getElementById('toastTitle');
                this.toastMessage = document.getElementById('toastMessage');
                this.toastTime = document.getElementById('toastTime');

                console.log('Unified Toast System with Tabler Icons initialized');
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

            // Set toast styling based on type (MENGGUNAKAN TABLER ICONS)
            setToastStyle: function(type) {
                // Reset classes
                this.toastElement.className = 'toast';

                // Set icon menggunakan Tabler Icons
                const icons = {
                    'success': 'ti ti-circle-check',
                    'error': 'ti ti-alert-circle',
                    'warning': 'ti ti-alert-triangle',
                    'info': 'ti ti-info-circle'
                };

                // Set icon class langsung (Tabler Icons menggunakan CSS class)
                this.toastIcon.className = `${icons[type] || icons.info} me-2`;

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

            console.log('Layout and toast system ready with Tabler Icons');
        });

        // Auto-hide flash messages after 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide flash messages after 10 seconds (except errors)
            const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 10000); // 10 seconds
            });
        });
    </script>

    @livewireScripts
    @stack('scripts')
</body>

</html>
