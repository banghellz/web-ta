<!-- resources/views/superadmin/items/detail-partial.blade.php -->
<div class="item-details">
    <div class="row g-3">
        <!-- Item Information -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-info-circle me-2"></i>Item Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th style="width: 30%">EPC Code</th>
                            <td><code class="bg-light px-2 py-1 rounded">{{ $item->epc }}</code></td>
                        </tr>
                        <tr>
                            <th>Item Name</th>
                            <td class="fw-semibold">{{ $item->nama_barang }}</td>
                        </tr>
                        <tr>
                            <th>Current Status</th>
                            <td>
                                <span class="badge {{ $item->status_badge_class }} fs-6">
                                    <i
                                        class="ti ti-{{ $item->status === 'missing' ? 'alert-triangle' : ($item->status === 'borrowed' ? 'user' : ($item->status === 'available' ? 'check' : 'x')) }} me-1"></i>
                                    {{ $item->status_text }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Added Date</th>
                            <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $item->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Current Status -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-status-change me-2"></i>Current Status
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if ($item->status === 'missing')
                            <i class="ti ti-alert-triangle text-dark" style="font-size: 3rem;"></i>
                        @elseif($item->status === 'borrowed')
                            <i class="ti ti-user-check text-warning" style="font-size: 3rem;"></i>
                        @elseif($item->status === 'available')
                            <i class="ti ti-check-circle text-success" style="font-size: 3rem;"></i>
                        @else
                            <i class="ti ti-x-circle text-danger" style="font-size: 3rem;"></i>
                        @endif
                    </div>
                    <h4
                        class="text-{{ $item->status === 'missing' ? 'dark' : ($item->status === 'borrowed' ? 'warning' : ($item->status === 'available' ? 'success' : 'danger')) }}">
                        {{ $item->status_text }}
                    </h4>
                    <p class="text-muted mb-0">
                        @if ($item->status === 'missing')
                            Item reported as missing
                        @elseif($item->status === 'borrowed')
                            Currently borrowed by user
                        @elseif($item->status === 'available')
                            Ready for borrowing
                        @else
                            Not available for borrowing
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Borrower/Last Borrower Information -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-user me-2"></i>
                        @if ($item->status === 'missing')
                            Last Borrower Information
                        @else
                            Borrower Information
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $borrowerDetail = null;
                        $borrowerUser = null;
                        $missingToolInfo = null;

                        if ($item->status === 'missing') {
                            // Get last borrower from missing_tools table
                            $missingToolInfo = $item->activeMissingTool ?? $item->missingTools()->latest()->first();
                            if ($missingToolInfo && $missingToolInfo->user_id) {
                                $borrowerDetail = \App\Models\UserDetail::where(
                                    'user_id',
                                    $missingToolInfo->user_id,
                                )->first();
                                $borrowerUser = \App\Models\User::find($missingToolInfo->user_id);
                            }
                        } elseif ($item->user_id) {
                            // Current borrower - Get fresh data to ensure coin info is up-to-date
                            $borrowerDetail = \App\Models\UserDetail::where('user_id', $item->user_id)->first();
                            $borrowerUser = \App\Models\User::find($item->user_id);
                        }

                        // Check if borrower is admin
                        $isAdminUser = false;
                        if ($borrowerUser) {
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
                            $isAdminUser = in_array(trim($borrowerUser->role ?? ''), $adminRoles, true);
                        }
                    @endphp

                    @if ($borrowerDetail && $borrowerUser)
                        <div class="d-flex align-items-center mb-3">
                            @php
                                $profilePhoto = $borrowerDetail->pict
                                    ? asset('profile_pictures/' . $borrowerDetail->pict)
                                    : 'https://www.gravatar.com/avatar/' .
                                        md5($borrowerDetail->nama ?? 'default@example.com');
                            @endphp
                            <span class="avatar avatar-lg me-3"
                                style="background-image: url({{ $profilePhoto }})"></span>
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    {{ $borrowerDetail->nama }}
                                </h6>
                                @if ($item->status === 'missing')
                                    <br><small class="badge bg-warning">Last Borrower</small>
                                @endif
                            </div>
                        </div>

                        <table class="table table-sm">
                            <tr>
                                <th style="width: 40%">NIM</th>
                                <td>{{ $borrowerDetail->nim }}</td>
                            </tr>
                            <tr>
                                <th>Program Studi</th>
                                <td>{{ $borrowerDetail->prodi }}</td>
                            </tr>
                            <tr>
                                <th>User Role</th>
                                <td>
                                    <span class="badge bg-{{ $isAdminUser ? 'purple' : 'primary' }}">
                                        @if ($isAdminUser)
                                            <i class="ti ti-crown me-1"></i>
                                        @else
                                            <i class="ti ti-user me-1"></i>
                                        @endif
                                        {{ ucfirst($borrowerUser->role ?? 'User') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>No. Koin</th>
                                <td>{{ $borrowerDetail->no_koin }}</td>
                            </tr>
                            <tr>
                                <th>Coin Status</th>
                                <td id="coin-info-{{ $borrowerUser->id }}">
                                    @if ($isAdminUser)
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-purple mb-1">
                                                <i class="ti ti-crown me-1"></i>Admin - Unlimited Access
                                            </span>
                                            <small class="text-muted">
                                                <i class="ti ti-info-circle me-1"></i>
                                                Admins don't require coins for borrowing
                                            </small>
                                        </div>
                                    @else
                                        <div class="d-flex flex-column">
                                            <span
                                                class="badge bg-{{ $borrowerDetail->koin > 5 ? 'success' : ($borrowerDetail->koin > 2 ? 'warning' : 'danger') }}">
                                                <i class="ti ti-coins me-1"></i>{{ $borrowerDetail->koin }} coins
                                            </span>
                                            @if ($borrowerDetail->koin <= 2)
                                                <small class="text-danger mt-1">
                                                    <i class="ti ti-alert-triangle me-1"></i>Low coin balance
                                                </small>
                                            @elseif($borrowerDetail->koin <= 5)
                                                <small class="text-warning mt-1">
                                                    <i class="ti ti-alert-circle me-1"></i>Consider topping up coins
                                                </small>
                                            @else
                                                <small class="text-success mt-1">
                                                    <i class="ti ti-check me-1"></i>Good coin balance
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @if ($item->status === 'missing' && $missingToolInfo)
                                <tr>
                                    <th>Reported Missing</th>
                                    <td>
                                        <span class="text-muted">
                                            {{ $missingToolInfo->reported_at->format('d M Y, H:i') }}
                                        </span>
                                        <br>
                                        <small
                                            class="text-muted">({{ $missingToolInfo->reported_at->diffForHumans() }})</small>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Missing Status</th>
                                    <td>
                                        <span
                                            class="badge bg-{{ $missingToolInfo->status === 'pending' ? 'danger' : 'success' }}">
                                            {{ ucfirst($missingToolInfo->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        </table>

                        @if ($item->status === 'missing' && $missingToolInfo && $missingToolInfo->status === 'pending')
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-sm w-100"
                                    onclick="showReclaimModal({{ $missingToolInfo->id }}, '{{ $item->nama_barang }}')">
                                    <i class="ti ti-check me-1"></i>Mark as Found/Reclaimed
                                </button>
                            </div>
                        @elseif($item->status === 'borrowed')
                            <div class="mt-3">
                                <button type="button" class="btn btn-warning btn-sm w-100"
                                    onclick="showMarkMissingModal({{ $item->id }}, '{{ $item->nama_barang }}')">
                                    <i class="ti ti-alert-triangle me-1"></i>Mark as Missing
                                </button>
                                @if (!$isAdminUser)
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-info btn-sm w-100"
                                            onclick="refreshCoinInfo({{ $borrowerUser->id }})">
                                            <i class="ti ti-refresh me-1"></i>Refresh Coin Info
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-user-off text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">
                                @if ($item->status === 'missing')
                                    No borrower information available
                                @else
                                    No active borrower
                                @endif
                            </p>
                            <small class="text-muted">
                                @if ($item->status === 'missing')
                                    Unable to retrieve last borrower details
                                @else
                                    This item is currently not borrowed by anyone
                                @endif
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Item Statistics Summary -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-chart-bar me-2"></i>Item Statistics Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <i class="ti ti-package text-primary mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">1</h5>
                                <small class="text-muted">Item Unit</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <i class="ti ti-{{ $item->status === 'borrowed' ? 'user-check' : ($item->status === 'missing' ? 'alert-triangle' : 'user-off') }} 
                                   text-{{ $item->status === 'borrowed' ? 'warning' : ($item->status === 'missing' ? 'dark' : 'muted') }} mb-2"
                                    style="font-size: 2rem;"></i>
                                <h5 class="mb-1">{{ $item->status === 'borrowed' ? '1' : '0' }}</h5>
                                <small class="text-muted">Currently Borrowed</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <i class="ti ti-calendar text-info mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">{{ $item->created_at->diffForHumans() }}</h5>
                                <small class="text-muted">Item Age</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <i class="ti ti-clock text-success mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">{{ $item->updated_at->diffForHumans() }}</h5>
                                <small class="text-muted">Last Updated</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Availability Status Alert -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-info-circle me-2"></i>Status Information
                    </h5>
                </div>
                <div class="card-body">
                    @if ($item->status === 'missing')
                        <div class="alert alert-dark d-flex align-items-center">
                            <i class="ti ti-alert-triangle me-3 fs-4"></i>
                            <div>
                                <strong>Item Reported Missing</strong><br>
                                <span>This item has been reported as missing
                                    @if ($missingToolInfo)
                                        on {{ $missingToolInfo->reported_at->format('d M Y') }}
                                        @if ($borrowerDetail)
                                            by <strong>{{ $borrowerDetail->nama }}</strong>
                                            ({{ $borrowerDetail->nim }})
                                            @if ($isAdminUser)
                                                - <span
                                                    class="badge bg-purple">{{ ucfirst($borrowerUser->role) }}</span>
                                            @endif
                                        @endif
                                    @endif
                                    . The item is not available for borrowing until found and reclaimed.
                                </span>
                            </div>
                        </div>
                    @elseif ($item->status === 'borrowed')
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="ti ti-user-check me-3 fs-4"></i>
                            <div>
                                <strong>Item Currently Borrowed</strong><br>
                                <span>This item is currently borrowed by
                                    <strong>{{ $borrowerDetail->nama ?? 'Unknown User' }}</strong>
                                    ({{ $borrowerDetail->nim ?? 'N/A' }})
                                    @if ($isAdminUser)
                                        - <span class="badge bg-purple">{{ ucfirst($borrowerUser->role) }}</span>
                                        <br><small class="text-info">
                                            <i class="ti ti-info-circle me-1"></i>
                                            This user is an admin and doesn't require coins for borrowing.
                                        </small>
                                    @endif
                                    . Contact the borrower or use force return if necessary.
                                </span>
                            </div>
                        </div>
                    @elseif ($item->status === 'available')
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="ti ti-check-circle me-3 fs-4"></i>
                            <div>
                                <strong>Item Available</strong><br>
                                <span>This item is currently available for borrowing.</span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="ti ti-x-circle me-3 fs-4"></i>
                            <div>
                                <strong>Out of Stock</strong><br>
                                <span>This item is currently out of stock and unavailable for borrowing.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-settings me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('superadmin.items.edit', $item->id) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Edit Item
                        </a>
                        @if ($item->status !== 'borrowed' && $item->status !== 'missing')
                            <button type="button" class="btn btn-outline-danger"
                                onclick="showDeleteModal({{ $item->id }}, '{{ $item->nama_barang }}')">
                                <i class="ti ti-trash me-1"></i>Delete Item
                            </button>
                        @else
                            @php
                                $disabledReason =
                                    $item->status === 'borrowed'
                                        ? 'Cannot delete borrowed item'
                                        : 'Cannot delete missing item';
                            @endphp
                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $disabledReason }}">
                                <button type="button" class="btn btn-outline-secondary" disabled>
                                    <i class="ti ti-trash me-1"></i>Delete Item
                                </button>
                            </span>
                        @endif
                        @if ($borrowerUser && !$isAdminUser && $item->status === 'borrowed')
                            <button type="button" class="btn btn-info"
                                onclick="refreshCoinInfo({{ $borrowerUser->id }})">
                                <i class="ti ti-refresh me-1"></i>Refresh Coin Data
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Missing Confirmation Modal -->
<div class="modal modal-blur fade" id="markMissingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Item as Missing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="ti ti-alert-triangle text-warning" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">Mark as Missing?</h3>
                    <p>Are you sure you want to mark the item <span id="missing-item-name" class="fw-bold"></span> as
                        missing?</p>
                    <p class="text-warning">This will remove the item from the borrower's responsibility and mark it as
                        missing in the system.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmMarkMissing">
                    <i class="ti ti-alert-triangle me-1"></i>Mark as Missing
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reclaim Item Confirmation Modal -->
<div class="modal modal-blur fade" id="reclaimModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reclaim Missing Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="ti ti-check-circle text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">Mark as Found?</h3>
                    <p>Are you sure you want to mark the item <span id="reclaim-item-name" class="fw-bold"></span> as
                        found/reclaimed?</p>
                    <p class="text-success">This will mark the missing report as completed and make the item available
                        again.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmReclaim">
                    <i class="ti ti-check me-1"></i>Mark as Found
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Item Confirmation Modal -->
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="ti ti-trash text-danger" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">Delete Item?</h3>
                    <p>Are you sure you want to delete the item <span id="delete-item-name" class="fw-bold"></span>?
                    </p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="ti ti-trash me-1"></i>Delete Item
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let currentItemId = null;
        let currentMissingToolId = null;
        let currentItemName = '';

        // Enhanced toast function using unified system
        function showDetailToast(message, type = 'info') {
            console.log('Detail toast request:', {
                type,
                message
            });

            // Try multiple toast methods in order of preference
            if (window.UnifiedToastSystem) {
                window.UnifiedToastSystem.show(type, message);
            } else if (typeof window.showNotificationToast === 'function') {
                window.showNotificationToast(message, type);
            } else if (typeof showToast === 'function') {
                showToast(message, type);
            } else {
                // Fallback: trigger the toast manually
                const toast = $('#notificationToast');
                const toastIcon = $('#toastIcon');
                const toastMessage = $('#toastMessage');

                if (toast.length && toastIcon.length && toastMessage.length) {
                    // Set icon based on type
                    const icons = {
                        'success': 'ti-check',
                        'error': 'ti-alert-circle',
                        'warning': 'ti-alert-triangle',
                        'info': 'ti-info-circle'
                    };

                    toastIcon.attr('class', `ti ${icons[type] || icons.info} me-2`);
                    toastMessage.text(message);

                    // Add color class based on type
                    toast.removeClass('text-success text-danger text-warning text-info');
                    if (type !== 'info') {
                        toast.addClass(`text-${type === 'error' ? 'danger' : type}`);
                    }

                    // Show toast using Bootstrap
                    const bsToast = new bootstrap.Toast(toast[0], {
                        autohide: true,
                        delay: 4000
                    });
                    bsToast.show();
                } else {
                    // Ultimate fallback
                    alert(`${type.toUpperCase()}: ${message}`);
                }
            }
        }

        // NEW: Function to refresh coin information
        window.refreshCoinInfo = function(userId) {
            const $coinInfo = $(`#coin-info-${userId}`);
            const $refreshBtn = $('button[onclick*="refreshCoinInfo"]');

            // Show loading state
            $refreshBtn.prop('disabled', true).html(
                '<i class="ti ti-loader-2 me-1 spinning"></i>Refreshing...');

            $.ajax({
                url: `/superadmin/users/${userId}/coin-info`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                success: function(data) {
                    if (data.success) {
                        const userDetail = data.user_detail;
                        const isAdmin = data.is_admin;

                        let coinHtml = '';
                        if (isAdmin) {
                            coinHtml = `
                                <div class="d-flex flex-column">
                                    <span class="badge bg-purple mb-1">
                                        <i class="ti ti-crown me-1"></i>Admin - Unlimited Access
                                    </span>
                                    <small class="text-muted">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Admins don't require coins for borrowing
                                    </small>
                                </div>
                            `;
                        } else {
                            const badgeClass = userDetail.koin > 5 ? 'success' : (userDetail
                                .koin > 2 ? 'warning' : 'danger');
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
                                <div class="d-flex flex-column">
                                    <span class="badge bg-${badgeClass}">
                                        <i class="ti ti-coins me-1"></i>${userDetail.koin} coins
                                    </span>
                                    <small class="text-${statusClass} mt-1">
                                        <i class="ti ti-${statusClass === 'danger' ? 'alert-triangle' : (statusClass === 'warning' ? 'alert-circle' : 'check')} me-1"></i>
                                        ${statusText}
                                    </small>
                                </div>
                            `;
                        }

                        $coinInfo.html(coinHtml).addClass('animate-update');
                        setTimeout(() => $coinInfo.removeClass('animate-update'), 1000);

                        showDetailToast(data.message ||
                            'Coin information updated successfully!', 'success');
                    } else {
                        showDetailToast(data.message || 'Failed to refresh coin information',
                            'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error refreshing coin info:', xhr);
                    showDetailToast('Error refreshing coin information. Please try again.',
                        'error');
                },
                complete: function() {
                    $refreshBtn.prop('disabled', false).html(
                        '<i class="ti ti-refresh me-1"></i>Refresh Coin Info');
                }
            });
        };

        // Show mark as missing modal
        window.showMarkMissingModal = function(itemId, itemName) {
            currentItemId = itemId;
            currentItemName = itemName;
            $('#missing-item-name').text(itemName);
            $('#markMissingModal').modal('show');
        };

        // Show reclaim modal
        window.showReclaimModal = function(missingToolId, itemName) {
            currentMissingToolId = missingToolId;
            currentItemName = itemName;
            $('#reclaim-item-name').text(itemName);
            $('#reclaimModal').modal('show');
        };

        // Show delete modal
        window.showDeleteModal = function(itemId, itemName) {
            currentItemId = itemId;
            currentItemName = itemName;
            $('#delete-item-name').text(itemName);
            $('#deleteModal').modal('show');
        };

        // Handle mark as missing confirmation
        $('#confirmMarkMissing').on('click', function() {
            if (!currentItemId) return;

            const $btn = $(this);
            const originalText = $btn.html();
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Processing...');

            $.ajax({
                url: `/superadmin/missing-tools/mark-missing/${currentItemId}`,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .getAttribute('content')
                },
                success: function(data) {
                    if (data.success) {
                        showDetailToast(data.message ||
                            'Item successfully marked as missing', 'warning');
                        $('#markMissingModal').modal('hide');

                        // Refresh the modal content and DataTable if available
                        setTimeout(() => {
                            if (typeof window.itemsDataTable !== 'undefined') {
                                window.itemsDataTable.ajax.reload(null, false);
                            }
                            // Refresh the current modal content
                            location.reload();
                        }, 1500);
                    } else {
                        showDetailToast(data.message || 'Failed to mark item as missing',
                            'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error marking item as missing:', xhr);
                    let errorMessage = 'Error marking item as missing';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showDetailToast(errorMessage, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Handle reclaim confirmation
        $('#confirmReclaim').on('click', function() {
            if (!currentMissingToolId) return;

            const $btn = $(this);
            const originalText = $btn.html();
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Processing...');

            $.ajax({
                url: `/superadmin/missing-tools/${currentMissingToolId}/reclaim`,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .getAttribute('content')
                },
                success: function(data) {
                    if (data.success) {
                        showDetailToast(data.message ||
                            'Missing item successfully reclaimed', 'success');
                        $('#reclaimModal').modal('hide');

                        // Refresh the modal content and DataTable if available
                        setTimeout(() => {
                            if (typeof window.itemsDataTable !== 'undefined') {
                                window.itemsDataTable.ajax.reload(null, false);
                            }
                            // Refresh the current modal content
                            location.reload();
                        }, 1500);
                    } else {
                        showDetailToast(data.message || 'Failed to reclaim missing item',
                            'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error reclaiming missing item:', xhr);
                    let errorMessage = 'Error reclaiming missing item';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showDetailToast(errorMessage, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Handle delete confirmation
        $('#confirmDelete').on('click', function() {
            if (!currentItemId) return;

            const $btn = $(this);
            const originalText = $btn.html();
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

            $.ajax({
                url: `/superadmin/items/${currentItemId}`,
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .getAttribute('content')
                },
                success: function(data) {
                    if (data.success) {
                        showDetailToast(data.message || 'Item successfully deleted',
                            'success');
                        $('#deleteModal').modal('hide');

                        // Close the detail modal and refresh main page
                        setTimeout(() => {
                            $('#modal-item-detail').modal('hide');
                            if (typeof window.itemsDataTable !== 'undefined') {
                                window.itemsDataTable.ajax.reload(null, false);
                            }
                        }, 1500);
                    } else {
                        showDetailToast(data.message || 'Failed to delete item', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error deleting item:', xhr);
                    let errorMessage = 'Error deleting item';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showDetailToast(errorMessage, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Clear variables when modals are hidden
        $('#markMissingModal, #reclaimModal, #deleteModal').on('hidden.bs.modal', function() {
            currentItemId = null;
            currentMissingToolId = null;
            currentItemName = '';
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>
    .item-details .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .item-details .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .item-details .badge {
        font-size: 0.875em;
    }

    .item-details .border {
        border-color: #dee2e6 !important;
    }

    .item-details .btn {
        border-radius: 0.375rem;
    }

    .item-details .alert {
        border-radius: 0.5rem;
    }

    .item-details .avatar-lg {
        width: 3.5rem;
        height: 3.5rem;
        font-size: 1.25rem;
    }

    /* NEW: Admin badge styling */
    .badge.bg-purple {
        background-color: #6f42c1 !important;
        color: white;
    }

    /* NEW: Coin status styling improvements */
    .badge.bg-purple .ti-crown {
        color: #ffc107;
    }

    /* NEW: Animation for coin updates */
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

    /* Modal styling improvements */
    .modal-content {
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        background-color: #f8f9fa;
    }

    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.125);
        background-color: #f8f9fa;
    }

    /* Loading spinner for buttons */
    .spinner-border-sm {
        width: 0.875rem;
        height: 0.875rem;
        border-width: 0.1rem;
    }

    /* Enhanced coin status cards */
    .coin-status-good {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .coin-status-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    }

    .coin-status-danger {
        background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    }

    .coin-status-admin {
        background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
    }

    @media (max-width: 768px) {
        .item-details .d-flex.flex-wrap.gap-2 .btn {
            flex: 1;
            min-width: calc(50% - 0.25rem);
        }

        .item-details .col-md-3 {
            margin-bottom: 1rem;
        }
    }
</style>
