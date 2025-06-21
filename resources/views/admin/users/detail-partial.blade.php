{{-- resources/views/superadmin/users/detail-partial.blade.php --}}
<div class="row">
    <!-- User Avatar and Basic Info -->
    <div class="col-md-4">
        <div class="text-center mb-4">
            @if ($user->detail && $user->detail->pict)
                <img src="{{ asset('profile_pictures/' . $user->detail->pict) }}" class="avatar avatar-xl mb-3"
                    alt="Profile Picture" style="width: 100px; height: 100px; object-fit: cover;">
            @else
                <span class="avatar avatar-xl mb-3 bg-primary text-white"
                    style="width: 100px; height: 100px; font-size: 2rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
            @endif
            <h4 class="mb-1">{{ $user->name }}</h4>

            <!-- Status and Role Badges -->
            <div class="mt-2">
                @php
                    $roleColors = [
                        'guest' => 'secondary',
                        'user' => 'primary',
                        'admin' => 'warning',
                        'superadmin' => 'danger',
                    ];
                    $roleColor = $roleColors[$user->role] ?? 'secondary';

                    // Check if user is admin
                    $adminRoles = [
                        'admin',
                        'superadmin',
                        'super_admin',
                        'Admin',
                        'SuperAdmin',
                        'Super_Admin',
                        'ADMIN',
                        'SUPERADMIN',
                        'SUPER_ADMIN',
                    ];
                    $isAdminUser = in_array(trim($user->role ?? ''), $adminRoles, true);
                @endphp

                <span class="badge bg-{{ $roleColor }}">
                    @if ($isAdminUser)
                        <i class="ti ti-crown me-1"></i>
                    @else
                        <i class="ti ti-shield me-1"></i>
                    @endif
                    {{ ucfirst($user->role) }}
                </span>

                @if ($user->uuid === auth()->user()->uuid)
                    <span class="badge bg-info ms-1">
                        <i class="ti ti-user me-1"></i>You
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- User Details -->
    <div class="col-md-8">
        <div class="row">
            <!-- Account Information -->
            <div class="col-12 mb-4">
                <h5 class="text-primary mb-3">
                    <i class="ti ti-user me-2"></i>Account Information
                </h5>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-medium text-muted">Full Name:</td>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Email:</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Created At:</td>
                            <td>{{ $user->created_at->format('d F Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Last Updated:</td>
                            <td>{{ $user->updated_at->format('d F Y, H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($user->detail)
                <!-- Student Information -->
                <div class="col-12 mb-4">
                    <h5 class="text-info mb-3">
                        <i class="ti ti-school me-2"></i>Student Information
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            @if ($user->detail->nim)
                                <tr>
                                    <td class="fw-medium text-muted" style="width: 120px;">NIM:</td>
                                    <td>{{ $user->detail->nim }}</td>
                                </tr>
                            @endif
                            @if ($user->detail->no_koin)
                                <tr>
                                    <td class="fw-medium text-muted">No. Koin:</td>
                                    <td>{{ $user->detail->no_koin }}</td>
                                </tr>
                            @endif
                            @if ($user->detail->prodi)
                                <tr>
                                    <td class="fw-medium text-muted">Program:</td>
                                    <td>{{ $user->detail->prodi }}</td>
                                </tr>
                            @endif
                            @if (!$user->detail->nim && !$user->detail->no_koin && !$user->detail->prodi)
                                <tr>
                                    <td colspan="2" class="text-muted fst-italic">No student information available
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Coin Management Section -->
                <div class="col-12 mb-4">
                    <h5 class="text-success mb-3">
                        <i class="ti ti-coins me-2"></i>Coin Management
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-medium text-muted" style="width: 140px;">Current Coins:</td>
                                <td id="coin-info-{{ $user->uuid }}">
                                    @if ($isAdminUser)
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-warning me-2">
                                                <i class="ti ti-crown me-1"></i>Admin - Unlimited Access
                                            </span>
                                            <small class="text-muted">
                                                No coin restrictions for admin users
                                            </small>
                                        </div>
                                    @else
                                        @php
                                            $koinValue = $user->detail->koin ?? 0;
                                            $borrowedCount = $user->detail->borrowedItems()->count();
                                            $badgeClass =
                                                $koinValue > 5 ? 'success' : ($koinValue > 2 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-{{ $badgeClass }} me-2">
                                                <i class="ti ti-coins me-1"></i>{{ $koinValue }} coins
                                            </span>
                                            @if ($koinValue <= 2)
                                                <small class="text-danger">
                                                    <i class="ti ti-alert-triangle me-1"></i>Low coin balance
                                                </small>
                                            @elseif($koinValue <= 5)
                                                <small class="text-warning">
                                                    <i class="ti ti-alert-circle me-1"></i>Consider topping up coins
                                                </small>
                                            @else
                                                <small class="text-success">
                                                    <i class="ti ti-check me-1"></i>Good coin balance
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">Borrowed Items:</td>
                                <td>
                                    @php
                                        $borrowedCount = $user->detail->borrowedItems()->count();
                                    @endphp
                                    <span class="badge bg-{{ $borrowedCount > 0 ? 'info' : 'secondary' }}">
                                        <i class="ti ti-package me-1"></i>{{ $borrowedCount }} items
                                    </span>
                                    @if (!$isAdminUser && $borrowedCount > 0)
                                        <small class="text-muted ms-2">
                                            ({{ $borrowedCount }} coins used)
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    @if (!$isAdminUser)
                        <!-- Coin Actions -->
                        <div class="mt-3">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-info"
                                    onclick="refreshUserCoinInfo({{ $user->uuid }})">
                                    <i class="ti ti-refresh me-1"></i>Refresh Coins
                                </button>
                                <button type="button" class="btn btn-sm btn-success"
                                    onclick="syncUserCoinInfo({{ $user->uuid }})">
                                    <i class="ti ti-sync me-1"></i>Sync Coins
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="ti ti-info-circle me-1"></i>
                                Refresh: Get latest coin data | Sync: Recalculate based on borrowed items
                            </small>
                        </div>
                    @endif
                </div>

                <!-- RFID Information -->
                <div class="col-12 mb-4">
                    <h5 class="text-warning mb-3">
                        <i class="ti ti-credit-card me-2"></i>RFID Information
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            @if ($user->detail->rfid_uid)
                                <tr>
                                    <td class="fw-medium text-muted" style="width: 120px;">RFID UID:</td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $user->detail->rfid_uid }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Status:</td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="ti ti-check me-1"></i>Assigned
                                        </span>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="fw-medium text-muted" style="width: 120px;">RFID Status:</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="ti ti-credit-card-off me-1"></i>No RFID Assigned
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            @else
                <!-- No Details Available -->
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No additional details available for this user.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if ($user->detail && ($user->detail->nim || $user->detail->no_koin || $user->detail->prodi || $user->detail->rfid_uid))
    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="border-top pt-3">
                <h6 class="text-muted mb-3">Quick Actions</h6>
                <div class="btn-group" role="group">
                    <a href="{{ route('superadmin.users.edit', $user->uuid) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-edit me-1"></i>Edit User
                    </a>
                    @if ($user->detail->rfid_uid)
                        <button type="button" class="btn btn-sm btn-warning"
                            onclick="unassignRfid('{{ $user->uuid }}', '{{ $user->name }}')">
                            <i class="ti ti-credit-card-off me-1"></i>Unassign RFID
                        </button>
                    @endif

                    @if ($user->uuid === auth()->user()->uuid)
                        <!-- Current user cannot delete their own account -->
                        <button type="button" class="btn btn-sm btn-secondary" disabled
                            title="You cannot delete your own account">
                            <i class="ti ti-trash me-1"></i>Delete User (Not allowed)
                        </button>
                    @else
                        <!-- Other users can be deleted -->
                        <button type="button" class="btn btn-sm btn-danger"
                            onclick="deleteUser('{{ $user->uuid }}', '{{ $user->name }}', '{{ $user->email }}')">
                            <i class="ti ti-trash me-1"></i>Delete User
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    // Enhanced toast function
    function showUserDetailToast(message, type = 'info') {
        console.log('User detail toast:', {
            type,
            message
        });

        if (window.UnifiedToastSystem) {
            window.UnifiedToastSystem.show(type, message);
        } else if (typeof window.showNotificationToast === 'function') {
            window.showNotificationToast(message, type);
        } else if (typeof showToast === 'function') {
            showToast(message, type);
        } else {
            const toast = $('#notificationToast');
            const toastIcon = $('#toastIcon');
            const toastMessage = $('#toastMessage');

            if (toast.length && toastIcon.length && toastMessage.length) {
                const icons = {
                    'success': 'ti-check',
                    'error': 'ti-alert-circle',
                    'warning': 'ti-alert-triangle',
                    'info': 'ti-info-circle'
                };

                toastIcon.attr('class', `ti ${icons[type] || icons.info} me-2`);
                toastMessage.text(message);

                toast.removeClass('text-success text-danger text-warning text-info');
                if (type !== 'info') {
                    toast.addClass(`text-${type === 'error' ? 'danger' : type}`);
                }

                const bsToast = new bootstrap.Toast(toast[0], {
                    autohide: true,
                    delay: 4000
                });
                bsToast.show();
            } else {
                alert(`${type.toUpperCase()}: ${message}`);
            }
        }
    }

    // Function to refresh user coin information
    window.refreshUserCoinInfo = function(userId) {
        const $coinInfo = $(`#coin-info-${userId}`);
        const $refreshBtn = $('button[onclick*="refreshUserCoinInfo"]');

        $refreshBtn.prop('disabled', true).html(
            '<i class="ti ti-loader-2 me-1 spinning"></i>Refreshing...');

        $.ajax({
            url: `/superadmin/users/${userId}/coin-info`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            success: function(data) {
                if (data.success) {
                    const userDetail = data.user_detail;
                    const isAdmin = data.is_admin;
                    const borrowedCount = data.borrowed_count || 0;

                    let coinHtml = '';
                    if (isAdmin) {
                        coinHtml = `
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">
                                    <i class="ti ti-crown me-1"></i>Admin - Unlimited Access
                                </span>
                                <small class="text-muted">
                                    No coin restrictions for admin users
                                </small>
                            </div>
                        `;
                    } else {
                        const badgeClass = userDetail.koin > 5 ? 'success' : (userDetail.koin > 2 ?
                            'warning' : 'danger');
                        let statusText = '';
                        let statusClass = '';

                        if (userDetail.koin <= 2) {
                            statusText = 'Low coin balance';
                            statusClass = 'danger';
                        } else if (userDetail.koin <= 5) {
                            statusText = 'Consider topping up coins';
                            statusClass = 'warning';
                        } else {
                            statusText = 'Good coin balance';
                            statusClass = 'success';
                        }

                        coinHtml = `
                            <div class="d-flex align-items-center">
                                <span class="badge bg-${badgeClass} me-2">
                                    <i class="ti ti-coins me-1"></i>${userDetail.koin} coins
                                </span>
                                <small class="text-${statusClass}">
                                    <i class="ti ti-${statusClass === 'danger' ? 'alert-triangle' : (statusClass === 'warning' ? 'alert-circle' : 'check')} me-1"></i>
                                    ${statusText}
                                </small>
                            </div>
                        `;
                    }

                    $coinInfo.html(coinHtml).addClass('animate-update');
                    setTimeout(() => $coinInfo.removeClass('animate-update'), 1000);

                    showUserDetailToast(data.message || 'Coin information refreshed successfully!',
                        'success');
                } else {
                    showUserDetailToast(data.message || 'Failed to refresh coin information', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error refreshing coin info:', xhr);
                showUserDetailToast('Error refreshing coin information. Please try again.', 'error');
            },
            complete: function() {
                $refreshBtn.prop('disabled', false).html(
                    '<i class="ti ti-refresh me-1"></i>Refresh Coins');
            }
        });
    };

    // Function to sync user coin information
    window.syncUserCoinInfo = function(userId) {
        const $coinInfo = $(`#coin-info-${userId}`);
        const $syncBtn = $('button[onclick*="syncUserCoinInfo"]');

        $syncBtn.prop('disabled', true).html(
            '<i class="ti ti-loader-2 me-1 spinning"></i>Syncing...');

        $.ajax({
            url: `/superadmin/users/${userId}/sync-koin`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            success: function(data) {
                if (data.success) {
                    const userDetail = data.user_detail;
                    const isAdmin = data.is_admin;
                    const borrowedCount = data.borrowed_count || 0;
                    const oldKoin = data.old_koin || 0;

                    let coinHtml = '';
                    if (isAdmin) {
                        coinHtml = `
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">
                                    <i class="ti ti-crown me-1"></i>Admin - Unlimited Access
                                </span>
                                <small class="text-muted">
                                    No coin restrictions for admin users
                                </small>
                            </div>
                        `;
                    } else {
                        const badgeClass = userDetail.koin > 5 ? 'success' : (userDetail.koin > 2 ?
                            'warning' : 'danger');

                        coinHtml = `
                            <div class="d-flex align-items-center">
                                <span class="badge bg-${badgeClass} me-2">
                                    <i class="ti ti-coins me-1"></i>${userDetail.koin} coins
                                </span>
                                <small class="text-info">
                                    <i class="ti ti-sync me-1"></i>
                                    Synced: ${oldKoin} → ${userDetail.koin}
                                </small>
                            </div>
                        `;
                    }

                    $coinInfo.html(coinHtml).addClass('animate-update');
                    setTimeout(() => $coinInfo.removeClass('animate-update'), 1000);

                    showUserDetailToast(data.message || 'Coins synchronized successfully!', 'success');
                } else {
                    showUserDetailToast(data.message || 'Failed to sync coins', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error syncing coins:', xhr);
                let errorMessage = 'Error syncing coins. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showUserDetailToast(errorMessage, 'error');
            },
            complete: function() {
                $syncBtn.prop('disabled', false).html('<i class="ti ti-sync me-1"></i>Sync Coins');
            }
        });
    };

    // Existing functions for RFID and deletion
    function unassignRfid(userId, userName) {
        $('#user-detail-modal').modal('hide');

        if (typeof userToUnassignRfid !== 'undefined') {
            userToUnassignRfid = {
                id: userId,
                name: userName
            };
        }

        $('#user-to-unassign-rfid').text(userName);
        $('#modal-unassign-rfid').modal('show');
    }

    function deleteUser(userId, userName, userEmail) {
        @if ($user->uuid === auth()->user()->uuid)
            alert('You cannot delete your own account');
            return false;
        @endif

        $('#user-detail-modal').modal('hide');

        if (typeof userToDelete !== 'undefined') {
            userToDelete = {
                id: userId,
                name: userName,
                email: userEmail
            };
        }

        $('#user-to-delete').text(userName + ' (' + userEmail + ')');
        $('#deletion-reason').val('');
        $('#modal-delete-user').modal('show');
    }
</script>

<style>
    /* Animation for coin updates */
    .animate-update {
        animation: coinUpdate 0.8s ease-in-out;
    }

    @keyframes coinUpdate {
        0% {
            transform: scale(1);
            background-color: transparent;
        }

        50% {
            transform: scale(1.05);
            background-color: rgba(13, 202, 240, 0.1);
        }

        100% {
            transform: scale(1);
            background-color: transparent;
        }
    }

    /* Spinning animation for loading buttons */
    .spinning {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Coin badge improvements */
    .badge {
        font-size: 0.875em;
    }

    /* Table improvements */
    .table-borderless td {
        border: none;
        padding: 0.5rem 0.75rem;
    }

    .table-borderless td:first-child {
        padding-left: 0;
    }

    /* Code styling */
    code {
        font-size: 0.875em;
        color: #e83e8c;
        background-color: #f8f9fa !important;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }
</style>
