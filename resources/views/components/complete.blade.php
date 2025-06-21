{{-- resources/views/components/complete.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Complete Profile - {{ config('app.name', 'ATMI Portal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tabler CSS and Icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tabler-icons.min.css') }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .complete-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .complete-container {
            width: 100%;
            max-width: 900px;
        }

        .complete-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: 1rem 1rem 0 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .avatar {
            border: 3px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-secondary {
            border-color: rgba(0, 0, 0, 0.1);
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: rgba(0, 0, 0, 0.2);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
            font-weight: 600;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }

        .alert {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
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

    @stack('styles')
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

    <div class="complete-wrapper">
        <div class="complete-container">
            <div class="complete-card">
                {{ $header }}
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/tabler.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>

    <!-- Unified Toast System -->
    <script>
        // Unified Toast System untuk Complete Profile
        window.UnifiedToastSystem = {
            init: function() {
                this.toastElement = document.getElementById('notificationToast');
                this.toastIcon = document.getElementById('toastIcon');
                this.toastTitle = document.getElementById('toastTitle');
                this.toastMessage = document.getElementById('toastMessage');
                this.toastTime = document.getElementById('toastTime');
            },

            show: function(type, message, title = null) {
                if (!this.toastElement) {
                    this.init();
                }

                const cleanMessage = this.cleanMessage(message);
                title = title || this.getDefaultTitle(type);

                this.toastTitle.textContent = title;
                this.toastMessage.textContent = cleanMessage;
                this.toastTime.textContent = 'just now';

                this.setToastStyle(type);
                this.displayToast();
            },

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

            cleanMessage: function(message) {
                if (!message) return '';
                const textarea = document.createElement('textarea');
                textarea.innerHTML = message;
                return textarea.value;
            },

            getDefaultTitle: function(type) {
                const titles = {
                    'success': 'Success',
                    'error': 'Error',
                    'warning': 'Warning',
                    'info': 'Information'
                };
                return titles[type] || 'Notification';
            },

            setToastStyle: function(type) {
                this.toastElement.className = 'toast';

                const icons = {
                    'success': 'ti ti-circle-check',
                    'error': 'ti ti-alert-circle',
                    'warning': 'ti ti-alert-triangle',
                    'info': 'ti ti-info-circle'
                };

                this.toastIcon.className = `${icons[type] || icons.info} me-2`;

                if (type !== 'info') {
                    const colorClass = type === 'error' ? 'text-danger' : `text-${type}`;
                    this.toastElement.classList.add(colorClass);
                }
            },

            displayToast: function() {
                const bsToast = new bootstrap.Toast(this.toastElement, {
                    autohide: true,
                    delay: 5000
                });
                bsToast.show();
            }
        };

        // Initialize when document is ready
        $(document).ready(function() {
            window.UnifiedToastSystem.init();
        });
    </script>

    @stack('scripts')
</body>

</html>
