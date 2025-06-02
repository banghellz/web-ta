<!-- resources/views/admin/dashboard/index.blade.php -->
<x-layouts.superadmin_layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-activity me-2 text-primary"></i>{{ $title ?? 'Activity Logs' }}
                    </h2>
                    <div class="text-muted mt-1">{{ $content ?? 'Track user login activities' }}</div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block">
                            <i class="ti ti-refresh me-1"></i>
                            Refresh Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="container-xl mt-4">
        <!-- Statistics Row -->
        <div class="row row-deck row-cards mb-4">
            <!-- User Count Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="subheader text-muted">Total Users</div>
                                <div class="h3 m-0">{{ $userCount ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="d-flex mt-3 align-items-center">
                            <span class="text-success me-2">
                                <i class="ti ti-trending-up"></i> {{ $userCountPercentage ?? '0%' }}
                            </span>
                            <span class="text-muted fs-6">from last week</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <i class="ti ti-device-desktop"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="subheader text-muted">Active Sessions</div>
                                <div class="h3 m-0">{{ $activeSessions ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="d-flex mt-3 align-items-center">
                            <div class="progress progress-sm flex-grow-1">
                                <div class="progress-bar bg-green"
                                    style="width: {{ $activeSessionsPercentage ?? '0%' }}"></div>
                            </div>
                            <span class="text-muted ms-2">{{ $activeSessionsPercentage ?? '0%' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RFID Tags -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-azure text-white avatar">
                                    <i class="ti ti-id-badge"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="subheader text-muted">RFID Tags</div>
                                <div class="h3 m-0">{{ $rfidTagCount ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Available: {{ $availableRfidTags ?? 0 }}</span>
                                <span class="text-muted">Used: {{ $usedRfidTags ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-yellow text-white avatar">
                                    <i class="ti ti-status-change"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="subheader text-muted">System Status</div>
                                <div class="h3 m-0">{{ $systemStatus ?? 'Online' }}</div>
                            </div>
                        </div>
                        <div class="d-flex mt-3 align-items-center">
                            <span class="status-indicator status-green status-indicator-animated me-2"></span>
                            <span class="text-muted">All systems operational</span>
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
                            <i class="ti ti-dashboard me-2 text-primary"></i>Dashboard Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h4 class="mb-3">Welcome to your admin dashboard!</h4>
                            <p class="text-muted">This dashboard provides an overview of your system status and usage
                                statistics.</p>
                        </div>

                        <div class="row g-3 mt-2">
                            <!-- Quick action buttons -->
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="ti ti-user-plus me-2 text-primary"></i>User Management
                                        </h5>
                                        <p class="text-muted">Add, edit or remove system users</p>
                                        <a href="{{ route('superadmin.users.index') }}"
                                            class="btn btn-outline-primary btn-sm">
                                            Manage Users
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="ti ti-report-analytics me-2 text-primary"></i>Reports
                                        </h5>
                                        <p class="text-muted">View system activity and analytics</p>
                                        <a href="#" class="btn btn-outline-primary btn-sm">
                                            View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-clock me-2 text-primary"></i>Recent Activity
                        </h3>
                    </div>
                    <div class="card-body p-0 flex-grow-1 d-flex flex-column">
                        <div class="list-group list-group-flush flex-grow-1">
                            @forelse($recentActivities ?? [] as $activity)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span
                                                class="avatar">{{ substr($activity->user->name ?? 'Unknown', 0, 1) }}</span>
                                        </div>
                                        <div class="col text-truncate">
                                            <span
                                                class="text-body d-block">{{ $activity->description ?? 'Unknown action' }}</span>
                                            <div class="d-flex align-items-center text-muted mt-1">
                                                <div class="me-2">{{ $activity->user->name ?? 'Unknown user' }}
                                                </div>
                                                <div class="me-auto">
                                                    <i
                                                        class="ti ti-clock me-1 opacity-50"></i>{{ $activity->created_at->diffForHumans() ?? 'Unknown time' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="list-group-item py-4 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <div class="text-center text-muted">
                                        <i class="ti ti-mood-empty mb-2" style="font-size: 2rem;"></i>
                                        <p class="mb-0">No recent activities</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        @if (!empty($recentActivities ?? []))
                            <div class="card-footer mt-auto">
                                <a href="#" class="btn btn-link btn-sm">View all activities</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Add RFID Tag Card -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-id-badge me-2 text-primary"></i>Add RFID Tag
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.rfid-tags.store') }}" method="POST">
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
                                    <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>
                                        Available</option>
                                    <option value="Used" {{ old('status') == 'Used' ? 'selected' : '' }}>Used
                                    </option>
                                    <option value="Damaged" {{ old('status') == 'Damaged' ? 'selected' : '' }}>Damaged
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3"
                                    placeholder="Optional notes about this RFID tag">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="#" class="btn btn-link me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i> Add RFID Tag
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
                        <form action="{{ route('superadmin.items.store') }}" method="POST">
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

                            <div class="mb-3">
                                <label class="form-label required">Available Quantity</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-hash"></i>
                                    </span>
                                    <input type="number"
                                        class="form-control @error('available') is-invalid @enderror" name="available"
                                        placeholder="Enter quantity" value="{{ old('available', 1) }}"
                                        min="0" required>
                                </div>
                                @error('available')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('superadmin.items.index') }}" class="btn btn-link me-2">Cancel</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="ti ti-plus me-1"></i> Add Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.superadmin_layout>
