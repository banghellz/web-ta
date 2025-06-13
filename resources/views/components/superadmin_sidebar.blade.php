<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark" id="main-sidebar">
    <div class="container-fluid">
        <!-- BEGIN NAVBAR TOGGLER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- END NAVBAR TOGGLER -->

        <!-- BEGIN MOBILE NAVBAR HEADER -->
        <div class="d-flex align-items-center d-lg-none">


            <!-- Center: Logo -->
            <div class="flex-grow-1 d-flex justify-content-center">
                <a href="." aria-label="Cabinex" class="navbar-brand m-0">
                    <img src="/logo/darkmode_logo.png" alt="Cabinex Logo" class="navbar-brand-image"
                        style="width: 120px; height: auto;">
                </a>
            </div>

            <!-- Right: Mobile Profile Dropdown -->
            <div class="nav-item dropdown"
                style="position: relative; width: 48px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-2" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false" onclick="event.stopPropagation();">
                    @php
                        $user = auth()->user();
                        $photo = $user->detail?->pict
                            ? asset('profile_pictures/' . $user->detail->pict)
                            : 'https://www.gravatar.com/avatar/' . md5($user->email ?? 'default@example.com');
                    @endphp
                    <span class="avatar avatar-sm" style="background-image: url({{ $photo }})"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow"
                    style="position: absolute !important; 
                            top: 100% !important; 
                            right: 0 !important; 
                            left: auto !important;
                            z-index: 9999 !important; 
                            min-width: 200px; 
                            margin-top: 0.5rem;
                            transform: none !important;"
                    onclick="event.stopPropagation();">
                    <div class="dropdown-header">
                        <span class="fw-bold">{{ auth()->user()->name ?? 'User' }}</span>
                        <small class="text-muted d-block">{{ auth()->user()->role ?? 'Admin' }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" onclick="event.stopPropagation();">
                        <i class="ti ti-user me-2"></i>
                        Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="GET" action="{{ route('logout') }}" onclick="event.stopPropagation();">
                        @csrf
                        <button type="submit"
                            class="dropdown-item text-danger w-100 text-start border-0 bg-transparent"
                            style="outline: none !important;" onclick="event.stopPropagation();">
                            <i class="ti ti-logout me-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Desktop Logo -->
        <div class="navbar-brand navbar-brand-autodark d-none d-lg-block text-center w-100">
            <a href="." aria-label="Cabinex" class="navbar-brand d-inline-block">
                <img src="/logo/darkmode_logo.png" alt="Cabinex Logo" class="navbar-brand-image"
                    style="width: 120px; height: auto;">
            </a>
        </div>
        <!-- END NAVBAR LOGO -->

        <!-- BEGIN SIDEBAR MENU -->
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link text-gray-400 fw-semibold" href="{{ route('superadmin.dashboard.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-home"></i>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <!-- Master Data -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-gray-400 fw-medium" href="#navbar-base"
                        data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-package"></i>
                        </span>
                        <span class="nav-link-title">Master Data</span>
                    </a>
                    <div class="dropdown-menu bg-dark">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item text-gray-400 fw-semibold"
                                    href="{{ route('superadmin.users.index') }}">
                                    <i class="ti ti-users me-2"></i>
                                    Users
                                </a>
                                <a class="dropdown-item text-gray-400 fw-semibold"
                                    href="{{ route('superadmin.items.index') }}">
                                    <i class="ti ti-tool me-2"></i>
                                    Tools
                                </a>
                                <a class="dropdown-item text-gray-400 fw-semibold"
                                    href="{{ route('superadmin.rfid-tags.index') }}">
                                    <i class="ti ti-device-mobile me-2"></i>
                                    RFID
                                </a>
                                <a class="dropdown-item text-gray-400 fw-semibold"
                                    href="{{ route('superadmin.log_peminjaman.index') }}">
                                    <i class="ti ti-history me-2"></i>
                                    Borrowing History
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Activity Log -->
                <li class="nav-item">
                    <a class="nav-link text-gray-400 fw-semibold" href="{{ route('superadmin.activity-logs.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-file-text"></i>
                        </span>
                        <span class="nav-link-title">Activity Log</span>
                    </a>
                </li>

                <!-- Missing Tools -->
                <li class="nav-item">
                    <a class="nav-link text-gray-400 fw-semibold"
                        href="{{ route('superadmin.missing-tools.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-alert-triangle"></i>
                        </span>
                        <span class="nav-link-title">Missing Tools</span>
                    </a>
                </li>
            </ul>

            <!-- Logout Button (Bottom) -->
            <div class="mt-auto">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="#logout" class="nav-link text-red fw-semibold">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-logout"></i>
                            </span>
                            <span class="nav-link-title">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- END SIDEBAR MENU -->
    </div>
</aside>
