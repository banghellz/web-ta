<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark" id="user-sidebar">
    <div class="container-fluid">
        <div class="navbar-brand d-flex justify-content-between align-items-center py-2 ps-2">
            <a href="." class="navbar-brand-link">
                <img src="/logo/darkmode_logo.png" alt="Cabinex Logo" class="navbar-brand-image"
                    style="width: 120px; height: auto;">
            </a>
            <!-- Notification Icon -->
            <div class="position-relative me-2">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                    aria-label="Show notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                    </svg>
                </a>
                <span class="badge bg-red position-absolute top-0 start-100 translate-middle"></span>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow bg-dark">
                    <div class="dropdown-header">
                        <span class="fw-bold text-white">Notifications</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="text-center py-3">
                        <span class="text-muted">No new notifications</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mobile toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu content -->
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <div class="navbar-nav-scroll">
                <!-- Profile section - Clickable -->
                <div class="d-flex flex-column align-items-center py-2 border-bottom border-secondary">
                    <!-- Clickable Profile Avatar and Info -->
                    <a href="{{ route('user.profile.index') }}" class="profile-link text-decoration-none"
                        title="Edit Profile">
                        <div class="d-flex flex-column align-items-center">
                            <div class="avatar avatar-lg mb-2 avatar-rounded profile-avatar"
                                style="background-image: url({{ auth()->user()->detail?->pict ? asset('profile_pictures/' . auth()->user()->detail->pict) : asset('assets/img/default-avatar.png') }})">
                                <!-- Edit overlay icon -->
                                <div class="avatar-overlay">
                                    <i class="ti ti-edit"></i>
                                </div>
                            </div>
                            <h4 class="m-0 mb-1 text-break text-center text-white profile-name">
                                Hello, {{ auth()->user()->name ?? 'User' }}
                            </h4>
                            <div class="text-secondary small profile-role">{{ auth()->user()->role ?? 'User' }}</div>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <ul class="navbar-nav pt-2">
                    <li class="nav-item">
                        <a class="nav-link text-gray-400 fw-semibold" href="{{ route('user.dashboard.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-home"></i>
                            </span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-gray-400 fw-semibold" href="{{ route('user.storage.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-package"></i>
                            </span>
                            <span class="nav-link-title">Storage</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-gray-400 fw-semibold" href="{{ route('user.log-peminjaman.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-history"></i>
                            </span>
                            <span class="nav-link-title">History</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-gray-400 fw-semibold" href="{{ route('user.items.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-tool"></i>
                            </span>
                            <span class="nav-link-title">Stock</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-gray-400 fw-semibold" href="{{ route('user.missing-tools.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-alert-triangle"></i>
                            </span>
                            <span class="nav-link-title">Missing Tools</span>
                        </a>
                    </li>

                    <!-- Profile Menu Item -->
                    <li class="nav-item">
                        <a class="nav-link text-gray-400 fw-semibold" href="{{ route('user.profile.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-user"></i>
                            </span>
                            <span class="nav-link-title">Profile</span>
                        </a>
                    </li>

                    <!-- Logout - Fixed: Menggunakan POST method dan action yang benar -->
                    <li class="nav-item mt-auto">
                        <a class="nav-link text-red fw-semibold w-100 bg-transparent border-0"
                            href="{{ route('logout') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-logout"></i>
                            </span>
                            <span class="nav-link-title">Profile</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>

<style>
    /* User Sidebar Dark Theme */
    #user-sidebar {
        background-color: var(--tblr-dark);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Navigation Links */
    #user-sidebar .navbar-nav .nav-link {
        color: #8a9ba8;
        transition: all 0.15s ease-in-out;
        border-radius: 0.375rem;
        margin: 0.125rem 0.5rem;
        padding: 0.75rem 1rem;
        font-weight: 600;
    }

    #user-sidebar .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    #user-sidebar .navbar-nav .nav-link.active {
        background-color: var(--tblr-primary);
        color: #ffffff;
    }

    /* Profile Section - Clickable */
    #user-sidebar .profile-link {
        transition: all 0.2s ease-in-out;
        border-radius: 0.5rem;
        padding: 0.5rem;
        margin: 0 0.5rem;
        cursor: pointer;
        position: relative;
    }

    #user-sidebar .profile-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
    }

    #user-sidebar .profile-link:hover .profile-name {
        color: #ffffff !important;
    }

    #user-sidebar .profile-link:hover .profile-role {
        color: #b8c5d1 !important;
    }

    #user-sidebar .profile-link:hover .profile-avatar .avatar-overlay {
        opacity: 1;
    }

    /* Profile Avatar with Overlay */
    #user-sidebar .profile-avatar {
        position: relative;
        transition: all 0.2s ease-in-out;
        border: 2px solid rgba(255, 255, 255, 0.1);
    }

    #user-sidebar .profile-avatar:hover {
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Avatar Overlay Icon */
    #user-sidebar .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }

    #user-sidebar .avatar-overlay i {
        color: #ffffff;
        font-size: 1.25rem;
    }

    /* Profile Text Styling */
    #user-sidebar .profile-name {
        transition: color 0.2s ease-in-out;
    }

    #user-sidebar .profile-role {
        transition: color 0.2s ease-in-out;
    }

    /* Logout button styling */
    #user-sidebar .nav-link.text-red {
        color: var(--tblr-red) !important;
        text-align: left;
        display: flex;
        align-items: center;
    }

    #user-sidebar .nav-link.text-red:hover {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--tblr-red) !important;
    }

    /* Profile section */
    #user-sidebar .border-bottom {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    #user-sidebar h4 {
        color: #ffffff !important;
    }

    #user-sidebar .text-secondary {
        color: #8a9ba8 !important;
    }

    /* Dropdown styling */
    #user-sidebar .dropdown-menu.bg-dark {
        background-color: var(--tblr-dark) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    #user-sidebar .dropdown-item {
        color: #8a9ba8;
    }

    #user-sidebar .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    #user-sidebar .dropdown-header .text-white {
        color: #ffffff !important;
    }

    /* Icon sizing - consistent with superadmin */
    #user-sidebar .nav-link-icon {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    #user-sidebar .nav-link-icon i {
        font-size: 1.25rem;
    }

    /* Avatar sizing */
    #user-sidebar .avatar-lg {
        width: 4rem;
        height: 4rem;
    }

    /* Logo sizing - consistent with superadmin */
    #user-sidebar .navbar-brand-image {
        max-height: 2.5rem;
        width: 120px !important;
    }

    /* Badge styling */
    #user-sidebar .badge.bg-red {
        background-color: var(--tblr-red) !important;
        width: 0.5rem;
        height: 0.5rem;
        padding: 0;
        border-radius: 50%;
    }

    /* Notification icon */
    #user-sidebar .icon.text-white {
        color: #ffffff !important;
    }

    /* Mobile responsiveness */
    @media (max-width: 991.98px) {
        #user-sidebar .navbar-brand {
            padding: 0.75rem;
        }

        #user-sidebar .profile-link {
            margin: 0 0.25rem;
            padding: 0.25rem;
        }
    }

    /* Typography consistency */
    #user-sidebar .nav-link-title {
        font-size: 0.875rem;
        font-weight: 600;
    }

    #user-sidebar .fw-semibold {
        font-weight: 600 !important;
    }

    /* Active state handling */
    #user-sidebar .nav-link.active,
    #user-sidebar .nav-link[aria-current="page"] {
        background-color: var(--tblr-primary);
        color: #ffffff;
    }

    /* Button reset for logout */
    #user-sidebar button.nav-link {
        text-align: left;
        background: transparent;
        border: none;
        width: 100%;
        padding: 0.75rem 1rem;
        margin: 0.125rem 0.5rem;
        border-radius: 0.375rem;
    }

    #user-sidebar button.nav-link:focus {
        outline: none;
        box-shadow: none;
    }

    /* Accessibility improvements */
    #user-sidebar .profile-link:focus {
        outline: 2px solid var(--tblr-primary);
        outline-offset: 2px;
    }

    /* Tooltip for better UX */
    #user-sidebar .profile-link[title]:hover::after {
        content: attr(title);
        position: absolute;
        bottom: -2rem;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 1000;
        opacity: 0;
        animation: fadeIn 0.2s ease-in-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>
