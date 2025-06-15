<!-- resources/views/admin/dashboard/index.blade.php -->
<x-layouts.superadmin_layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Overview
                    </div>
                    <h2 class="page-title">
                        <i class="ti ti-dashboard me-2 text-blue"></i>{{ $title ?? 'Master Admin Dashboard' }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button type="button" class="btn btn-primary" onclick="refreshDashboard()">
                            <i class="ti ti-refresh me-1"></i>
                            Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="page-body">
        <div class="container-xl">
            <!-- Statistics Row -->
            <div class="row row-deck row-cards mb-4">
                <!-- Total Users Card -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Users</div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <div class="h1 mb-0 me-2">{{ $userCount ?? 0 }}</div>
                                <div class="me-auto">
                                    <span class="text-green d-inline-flex align-items-center lh-1">
                                        {{ $userCountPercentage ?? '0%' }}
                                        <i class="ti ti-trending-up ms-1"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ str_replace(['%', '+'], '', $userCountPercentage ?? '0') }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted">
                                <i class="ti ti-users me-1"></i>
                                System users
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Items Card -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Items</div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <div class="h1 mb-0 me-2">{{ $totalTools ?? 0 }}</div>
                                <div class="me-auto">
                                    <span class="text-blue d-inline-flex align-items-center lh-1">
                                        {{ $recentToolsPercentage ?? '0%' }}
                                        <i class="ti ti-trending-up ms-1"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $toolAvailabilityRate ?? '0%' }}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted">
                                <i class="ti ti-package me-1"></i>
                                Available: {{ $availableTools ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Borrowed Tools Card -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Borrowed Tools</div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <div class="h1 mb-0 me-2">{{ $borrowedTools ?? 0 }}</div>
                                <div class="me-auto">
                                    <span class="text-yellow d-inline-flex align-items-center lh-1">
                                        {{ $borrowedToolsPercentage ?? '0%' }}
                                        <i class="ti ti-trending-up ms-1"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            style="width: {{ $borrowedToolsPercentage ?? '0%' }}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted">
                                <i class="ti ti-hand-grab me-1"></i>
                                Currently borrowed
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RFID Tags Card -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">RFID Tags</div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <div class="h1 mb-0 me-2">{{ $rfidTagCount ?? 0 }}</div>
                                <div class="me-auto">
                                    <span class="badge bg-green-lt">{{ $availableRfidPercentage ?? '0%' }}</span>
                                </div>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-azure" role="progressbar"
                                            style="width: {{ $availableRfidPercentage ?? '0%' }}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted">
                                <i class="ti ti-id-badge me-1"></i>
                                Available: {{ $availableRfidTags ?? 0 }} | Used: {{ $usedRfidTags ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- System Overview -->
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-dashboard me-2 text-blue"></i>System Overview
                            </h3>
                            <div class="card-actions">
                                <div class="dropdown">
                                    <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="#" class="dropdown-item">
                                            <i class="ti ti-refresh me-2"></i>Refresh
                                        </a>
                                        <a href="#" class="dropdown-item">
                                            <i class="ti ti-download me-2"></i>Export
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h3 class="mb-3">Welcome to Admin Dashboard!</h3>
                                <p class="text-muted">Monitor and manage your system resources, user activities, and
                                    inventory status from this centralized dashboard.</p>
                            </div>

                            <!-- Quick Actions -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card card-link card-link-pop">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar bg-primary-lt me-3">
                                                    <i class="ti ti-user-plus"></i>
                                                </span>
                                                <div>
                                                    <div class="font-weight-medium">User Management</div>
                                                    <div class="text-muted">Add, edit or manage system users</div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('superadmin.users.index') }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="ti ti-arrow-right me-1"></i>Manage Users
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card card-link card-link-pop">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar bg-success-lt me-3">
                                                    <i class="ti ti-chart-bar"></i>
                                                </span>
                                                <div>
                                                    <div class="font-weight-medium">Analytics & Reports</div>
                                                    <div class="text-muted">View detailed system analytics</div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="#" class="btn btn-success btn-sm">
                                                    <i class="ti ti-arrow-right me-1"></i>View Reports
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-clock me-2 text-blue"></i>Recent Activity
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @forelse($recentActivities ?? [] as $activity)
                                <div class="list-group list-group-flush list-group-hoverable">
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar">
                                                    @if (isset($activity->user) && $activity->user)
                                                        {{ substr($activity->user->name, 0, 1) }}
                                                    @else
                                                        <i class="ti ti-user"></i>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col text-truncate">
                                                <div class="text-reset d-block">
                                                    @if (isset($activity->description) && $activity->description)
                                                        {{ $activity->description }}
                                                    @elseif(isset($activity->activity) && $activity->activity)
                                                        {{ $activity->activity }}
                                                    @else
                                                        System activity
                                                    @endif
                                                </div>
                                                <div class="d-block text-muted text-truncate mt-n1">
                                                    @if (isset($activity->user) && $activity->user && isset($activity->user->name))
                                                        {{ $activity->user->name }}
                                                    @else
                                                        System user
                                                    @endif
                                                    •
                                                    @if (isset($activity->created_at) && $activity->created_at)
                                                        {{ $activity->created_at->diffForHumans() }}
                                                    @else
                                                        Just now
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty">
                                    <div class="empty-img">
                                        <img src="./static/illustrations/undraw_printing_invoices_5r4r.svg"
                                            height="128" alt="">
                                    </div>
                                    <p class="empty-title">No recent activities</p>
                                    <p class="empty-subtitle text-muted">
                                        System activities will appear here once users start interacting with the
                                        system.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                        @if (!empty($recentActivities) && count($recentActivities) > 0)
                            <div class="card-footer">
                                <a href="#" class="btn btn-link">View all activities</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add RFID Tag Card -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-id-badge me-2 text-azure"></i>Add RFID Tag
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="rfidForm" action="{{ route('superadmin.rfid-tags.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label required">RFID UID</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-tag"></i>
                                        </span>
                                        <input type="text" class="form-control @error('uid') is-invalid @enderror"
                                            name="uid" placeholder="Enter RFID UID" value="{{ old('uid') }}"
                                            required>
                                    </div>
                                    @error('uid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status"
                                        required>
                                        <option value="">Select status</option>
                                        <option value="Available"
                                            {{ old('status') == 'Available' ? 'selected' : '' }}>
                                            Available
                                        </option>
                                        <option value="Used" {{ old('status') == 'Used' ? 'selected' : '' }}>
                                            Used
                                        </option>
                                        <option value="Damaged" {{ old('status') == 'Damaged' ? 'selected' : '' }}>
                                            Damaged
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('superadmin.rfid-tags.index') }}" class="btn">
                                        <i class="ti ti-arrow-right me-1"></i>Go to RFID
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-plus me-1"></i>Add RFID Tag
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Add Item Card -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-package me-2 text-success"></i>Add Item
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="itemForm" action="{{ route('superadmin.items.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label required">EPC Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-qrcode"></i>
                                        </span>
                                        <input type="text" class="form-control @error('epc') is-invalid @enderror"
                                            name="epc" placeholder="Enter EPC code" value="{{ old('epc') }}"
                                            required>
                                    </div>
                                    @error('epc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Item Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-package"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control @error('nama_barang') is-invalid @enderror"
                                            name="nama_barang" placeholder="Enter item name"
                                            value="{{ old('nama_barang') }}" required>
                                    </div>
                                    @error('nama_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('superadmin.items.index') }}" class="btn">
                                        <i class="ti ti-arrow-right me-1"></i>Go to Items
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="ti ti-plus me-1"></i>Add Item
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Form Handling dengan Toast System yang Diperbaiki -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!csrfToken) {
                console.error('CSRF token not found. Please add meta tag to layout head.');
            }

            console.log('Dashboard initialized');

            // Show session messages using layout toast system
            @if (session('success'))
                if (typeof showToast === 'function') {
                    showToast('{{ session('success') }}', 'success');
                } else if (typeof window.showNotificationToast === 'function') {
                    window.showNotificationToast('{{ session('success') }}', 'success');
                }
            @endif

            @if (session('error'))
                if (typeof showToast === 'function') {
                    showToast('{{ session('error') }}', 'error');
                } else if (typeof window.showNotificationToast === 'function') {
                    window.showNotificationToast('{{ session('error') }}', 'error');
                }
            @endif

            @if (session('warning'))
                if (typeof showToast === 'function') {
                    showToast('{{ session('warning') }}', 'warning');
                } else if (typeof window.showNotificationToast === 'function') {
                    window.showNotificationToast('{{ session('warning') }}', 'warning');
                }
            @endif

            @if (session('info'))
                if (typeof showToast === 'function') {
                    showToast('{{ session('info') }}', 'info');
                } else if (typeof window.showNotificationToast === 'function') {
                    window.showNotificationToast('{{ session('info') }}', 'info');
                }
            @endif

            // Function to show toast using layout system
            function showDashboardToast(message, type = 'info') {
                console.log('Dashboard toast request:', {
                    type,
                    message
                });

                // Try multiple toast functions available in layout
                if (typeof showToast === 'function') {
                    showToast(message, type);
                } else if (typeof window.showNotificationToast === 'function') {
                    window.showNotificationToast(message, type);
                } else {
                    // Fallback to alert if no toast system available
                    console.warn('No toast system available, using alert fallback');
                    alert(`${type.toUpperCase()}: ${message}`);
                }
            }

            // Handle Item form submission
            const itemForm = document.getElementById('itemForm');
            if (itemForm) {
                itemForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Get form data and clean it
                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;

                    // Clean input values
                    const epcInput = this.querySelector('input[name="epc"]');
                    const namaInput = this.querySelector('input[name="nama_barang"]');

                    if (epcInput) {
                        const cleanEpc = epcInput.value.trim();
                        epcInput.value = cleanEpc;
                        formData.set('epc', cleanEpc);
                    }

                    if (namaInput) {
                        const cleanNama = namaInput.value.trim();
                        namaInput.value = cleanNama;
                        formData.set('nama_barang', cleanNama);
                    }

                    console.log('Item Form Submit:', {
                        epc: formData.get('epc'),
                        nama_barang: formData.get('nama_barang')
                    });

                    // Show loading state
                    submitButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span>Adding...';
                    submitButton.disabled = true;

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            console.log('Item Response status:', response.status);

                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                console.error('Non-JSON response received');
                                throw new Error('Server returned non-JSON response');
                            }

                            return response.json().then(data => {
                                data._httpStatus = response.status;
                                return data;
                            });
                        })
                        .then(data => {
                            console.log('Item Response data:', data);

                            const isSuccess = (data._httpStatus >= 200 && data._httpStatus < 300) &&
                                (data.success === true || data.success === 'true');

                            if (isSuccess) {
                                // Success case
                                const message = data.message || 'Item added successfully!';
                                console.log('Showing item success toast:', message);

                                showDashboardToast(message, 'success');

                                // Reset form and clear validation errors
                                itemForm.reset();
                                clearValidationErrors();

                                // Refresh notifications if available
                                if (window.refreshNotifications) {
                                    window.refreshNotifications();
                                }

                                // Auto refresh stats after delay
                                setTimeout(() => {
                                    refreshStats();
                                }, 1500);

                            } else {
                                // Error case
                                const errorMessage = data.message || data.error || 'Failed to add Item';
                                console.log('Showing item error toast:', errorMessage);

                                showDashboardToast(errorMessage, 'error');

                                // Handle validation errors
                                if (data.errors) {
                                    handleValidationErrors(data.errors);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Item Fetch Error:', error);

                            let errorMessage = 'An error occurred while adding Item';

                            if (error.message.includes('non-JSON response')) {
                                errorMessage = 'Server returned an unexpected response format';
                            } else if (error.message.includes('Failed to fetch')) {
                                errorMessage = 'Network error - please check your connection';
                            } else if (error.message) {
                                errorMessage = error.message;
                            }

                            console.log('Showing item error toast from catch:', errorMessage);
                            showDashboardToast(errorMessage, 'error');
                        })
                        .finally(() => {
                            submitButton.innerHTML = originalText;
                            submitButton.disabled = false;
                        });
                });
            }

            // Handle RFID form submission
            const rfidForm = document.getElementById('rfidForm');
            if (rfidForm) {
                rfidForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;

                    // Clean input values
                    const uidInput = this.querySelector('input[name="uid"]');
                    if (uidInput) {
                        const cleanUid = uidInput.value.trim();
                        uidInput.value = cleanUid;
                        formData.set('uid', cleanUid);
                    }

                    console.log('RFID Form Submit:', {
                        uid: formData.get('uid'),
                        status: formData.get('status')
                    });

                    submitButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span>Adding...';
                    submitButton.disabled = true;

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            console.log('RFID Response status:', response.status);

                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('Server returned non-JSON response');
                            }

                            return response.json().then(data => {
                                data._httpStatus = response.status;
                                return data;
                            });
                        })
                        .then(data => {
                            console.log('RFID Response data:', data);

                            const isSuccess = (data._httpStatus >= 200 && data._httpStatus < 300) &&
                                (data.success === true || data.success === 'true');

                            if (isSuccess) {
                                // Success case
                                const message = data.message || 'RFID Tag added successfully!';
                                console.log('Showing RFID success toast:', message);

                                showDashboardToast(message, 'success');

                                // Reset form and clear validation errors
                                rfidForm.reset();
                                clearValidationErrors();

                                // Refresh notifications if available
                                if (window.refreshNotifications) {
                                    window.refreshNotifications();
                                }

                                // Auto refresh stats after delay
                                setTimeout(() => {
                                    refreshStats();
                                }, 1500);

                            } else {
                                const errorMessage = data.message || data.error ||
                                    'Failed to add RFID Tag';
                                console.log('Showing RFID error toast:', errorMessage);

                                showDashboardToast(errorMessage, 'error');

                                if (data.errors) {
                                    handleValidationErrors(data.errors);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('RFID Fetch Error:', error);

                            let errorMessage = 'An error occurred while adding RFID Tag';
                            if (error.message.includes('non-JSON response')) {
                                errorMessage = 'Server returned an unexpected response format';
                            } else if (error.message.includes('Failed to fetch')) {
                                errorMessage = 'Network error - please check your connection';
                            } else if (error.message) {
                                errorMessage = error.message;
                            }

                            console.log('Showing RFID error toast from catch:', errorMessage);
                            showDashboardToast(errorMessage, 'error');
                        })
                        .finally(() => {
                            submitButton.innerHTML = originalText;
                            submitButton.disabled = false;
                        });
                });
            }

            // Function to handle validation errors
            function handleValidationErrors(errors) {
                clearValidationErrors();

                Object.keys(errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    const feedback = input?.parentElement.querySelector('.invalid-feedback') ||
                        input?.parentElement.parentElement.querySelector('.invalid-feedback');

                    if (input) {
                        input.classList.add('is-invalid');
                    }
                    if (feedback) {
                        feedback.textContent = errors[field][0];
                    }
                });
            }

            // Function to clear validation errors
            function clearValidationErrors() {
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.textContent = '';
                });
            }
        });

        // Function to refresh dashboard data
        function refreshDashboard() {
            location.reload();
        }

        // Function to refresh statistics
        function refreshStats() {
            setTimeout(() => {
                refreshDashboard();
            }, 1000);
        }
    </script>
</x-layouts.superadmin_layout>
