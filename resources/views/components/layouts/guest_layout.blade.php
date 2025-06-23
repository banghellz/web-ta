<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard - ' . config('app.name', 'Laravel App') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tabler CSS and Icons -->
    <link rel="stylesheet" href="/assets/css/tabler.min.css">
    <link rel="stylesheet" href="/assets/css/tabler-icons.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="/assets/css/jquery.dataTables.min.css">

    @stack('styles')

    <style>
        :root {
            --primary-color: #4DA1A9;
            --primary-light: #E6F4F5;
            --success-color: #2dd36f;
            --warning-color: #ffc409;
            --danger-color: #eb445a;
            --secondary-color: #92949c;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        /* Custom navbar styling */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #e9ecef;
        }

        /* Custom card styling */
        .stats-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: white;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Custom icon backgrounds */
        .icon-bg-primary {
            background: linear-gradient(135deg, var(--primary-color), #5cbdc4);
        }

        .icon-bg-success {
            background: linear-gradient(135deg, var(--success-color), #42e88a);
        }

        .icon-bg-warning {
            background: linear-gradient(135deg, var(--warning-color), #ffce3a);
        }

        .icon-bg-danger {
            background: linear-gradient(135deg, var(--danger-color), #f16976);
        }

        /* Table styling */
        .table-custom {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .table-custom thead th {
            background: #f8fafc;
            border: none;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
        }

        .table-custom tbody td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }

        .table-custom tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody tr:last-child {
            border-bottom: none;
        }

        /* Status badges */
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .status-available {
            background: #dcfce7;
            color: #166534;
        }

        .status-borrowed {
            background: #fef3c7;
            color: #92400e;
        }

        .status-missing {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Search box styling */
        .search-box {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: border-color 0.2s ease;
        }

        .search-box:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(77, 161, 169, 0.1);
        }

        /* Profile dropdown */
        .profile-dropdown {
            border: none;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
        }

        .profile-dropdown .dropdown-item {
            padding: 0.75rem 1.5rem;
            color: #374151;
            transition: background-color 0.2s ease;
        }

        .profile-dropdown .dropdown-item:hover {
            background-color: #f8fafc;
            color: var(--primary-color);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .stats-card .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animation for stats update */
        .stat-updating {
            animation: statUpdate 0.6s ease-in-out;
        }

        @keyframes statUpdate {
            0% {
                background-color: transparent;
                transform: scale(1);
            }

            50% {
                background-color: var(--primary-light);
                transform: scale(1.02);
            }

            100% {
                background-color: transparent;
                transform: scale(1);
            }
        }

        /* Loading states */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }
    </style>
</head>

<body>
    <!-- Minimal Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid px-4">
            <!-- Brand -->
            <a class="navbar-brand fw-bold" href="#" style="color: var(--primary-color);">
                <i class="ti ti-building-warehouse me-2"></i>
                {{ config('app.name', 'Tool Management') }}
            </a>

            <!-- Profile Section -->
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex align-items-center p-0" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        @php
                            $user = auth()->user();
                            $photo = $user->detail?->pict
                                ? asset('profile_pictures/' . $user->detail->pict)
                                : 'https://www.gravatar.com/avatar/' . md5($user->email ?? 'default@example.com');
                        @endphp
                        <span class="avatar avatar-sm me-2" style="background-image: url({{ $photo }})"></span>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold">{{ auth()->user()->name ?? 'Guest User' }}</div>
                            <div class="text-muted small">{{ auth()->user()->role ?? 'Guest' }}</div>
                        </div>
                        <i class="ti ti-chevron-down ms-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="ti ti-logout me-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="page-wrapper" style="margin-top: 80px;">
        <div class="page-body">
            <div class="container-fluid px-4">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer footer-transparent mt-5">
            <div class="container-fluid px-4">
                <div class="row text-center align-items-center">
                    <div class="col-12">
                        <div class="text-muted">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Tool Management') }}. All rights
                            reserved.
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/tabler.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>

    <!-- Toast System for Notifications -->
    <script>
        // Simple toast system for guest
        window.showToast = function(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 100px; right: 20px; z-index: 1050; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        };

        // Setup CSRF token for AJAX requests
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
