{{-- resources/views/admin/users/detail-partial.blade.php --}}
<div class="row">
    <!-- User Profile Section -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3"
                    style="background-image: url({{ $user->detail && $user->detail->pict ? asset('profile_pictures/' . $user->detail->pict) : asset('assets/img/default-avatar.png') }})">
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <div class="text-muted mb-3">{{ $user->email }}</div>

                <!-- Role Badge -->
                @if ($user->role === 'superadmin')
                    <span class="badge bg-danger fs-3">
                        <i class="ti ti-shield-lock me-1"></i>Super Admin
                    </span>
                    <div class="alert alert-danger mt-3">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-shield-lock me-2"></i>
                            </div>
                            <div>
                                <strong>Protected Account</strong><br>
                                <small>This Super Admin account is protected from modifications by regular
                                    admins.</small>
                            </div>
                        </div>
                    </div>
                @elseif($user->role === 'admin')
                    <span class="badge bg-warning fs-3">
                        <i class="ti ti-shield me-1"></i>Admin
                    </span>
                @elseif($user->role === 'user')
                    <span class="badge bg-primary fs-3">
                        <i class="ti ti-user me-1"></i>User
                    </span>
                @else
                    <span class="badge bg-secondary fs-3">
                        <i class="ti ti-user-question me-1"></i>Guest
                    </span>
                @endif

                <div class="mt-3">
                    <div class="row">
                        <div class="col">
                            <div class="text-muted">Member Since</div>
                            <div class="fw-bold">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Details Section -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">User Information</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $user->email }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Role:</strong>
                    </div>
                    <div class="col-sm-9">
                        @if ($user->role === 'superadmin')
                            <span class="badge bg-danger">
                                <i class="ti ti-shield-lock me-1"></i>Super Admin
                            </span>
                        @elseif($user->role === 'admin')
                            <span class="badge bg-warning">
                                <i class="ti ti-shield me-1"></i>Admin
                            </span>
                        @elseif($user->role === 'user')
                            <span class="badge bg-primary">
                                <i class="ti ti-user me-1"></i>User
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="ti ti-user-question me-1"></i>Guest
                            </span>
                        @endif
                    </div>
                </div>

                @if ($user->detail)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>NIM:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $user->detail->nim ?? 'Not set' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Coin Number:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $user->detail->no_koin ?? 'Not set' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Program Studi:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $user->detail->prodi ?? 'Not set' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Current Koin:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if ($user->role === 'superadmin')
                                <span class="badge bg-success">
                                    <i class="ti ti-infinity me-1"></i>Unlimited (Super Admin)
                                </span>
                            @elseif($user->role === 'admin')
                                <span class="badge bg-warning">
                                    <i class="ti ti-infinity me-1"></i>Unlimited (Admin)
                                </span>
                            @else
                                <span class="badge bg-primary">
                                    <i class="ti ti-coin me-1"></i>{{ $user->detail->koin ?? 0 }} Koin
                                </span>
                                @if ($user->detail->borrowedItems && $user->detail->borrowedItems->count() > 0)
                                    <div class="text-muted small mt-1">
                                        Calculation: 10 - {{ $user->detail->borrowedItems->count() }} borrowed items =
                                        {{ $user->detail->koin ?? 0 }} koin
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>RFID Tag:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if ($user->detail->rfid_uid)
                                <span class="badge bg-info">
                                    <i class="ti ti-credit-card me-1"></i>{{ $user->detail->rfid_uid }}
                                </span>
                                @if ($user->role === 'superadmin')
                                    <div class="text-muted small mt-1">
                                        <i class="ti ti-shield-lock me-1"></i>Protected from unassignment
                                    </div>
                                @endif
                            @else
                                <span class="badge bg-secondary">
                                    <i class="ti ti-credit-card-off me-1"></i>No RFID Assigned
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($user->detail->borrowedItems && $user->detail->borrowedItems->count() > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Borrowed Items:</strong>
                            </div>
                            <div class="col-sm-9">
                                <span class="badge bg-danger">
                                    <i class="ti ti-package me-1"></i>{{ $user->detail->borrowedItems->count() }} Items
                                </span>
                                @if ($user->role !== 'superadmin' && $user->role !== 'admin')
                                    <div class="text-muted small mt-1">
                                        This affects the user's available koin balance
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No detailed profile information available for this user.
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-3">
                        <strong>Last Login:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $user->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if ($user->role !== 'superadmin')
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="btn-list">
                        <a href="{{ route('admin.users.edit', $user->uuid) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Edit User
                        </a>
                        @if ($user->detail && $user->detail->rfid_uid)
                            <button type="button" class="btn btn-warning unassign-rfid-modal"
                                data-id="{{ $user->uuid }}" data-name="{{ $user->name }}">
                                <i class="ti ti-credit-card-off me-1"></i>Unassign RFID
                            </button>
                        @endif
                        @if ($user->uuid !== auth()->user()->uuid)
                            <button type="button" class="btn btn-danger delete-user-modal"
                                data-id="{{ $user->uuid }}" data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}">
                                <i class="ti ti-trash me-1"></i>Delete User
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="card mt-3">
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-shield-lock me-2"></i>
                            </div>
                            <div>
                                <strong>Super Admin Protection</strong><br>
                                This account cannot be modified or deleted by regular administrators. Only other Super
                                Admins can manage this account.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
