{{-- resources/views/superadmin/users/index.blade.php --}}
<x-layouts.superadmin_layout>
    <x-slot name="title">{{ $title ?? 'Users Management' }}</x-slot>
    <x-slot name="content">{{ $content ?? 'Manage system users and their roles' }}</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-users me-2 text-primary"></i>
                        {{ $title ?? 'Users Management' }}
                    </h2>
                    <div class="text-muted mt-1">
                        {{ $content ?? 'Manage system users and their roles' }}
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <button id="refresh-btn" class="btn btn-primary">
                            <i class="ti ti-refresh me-1"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Page Body --}}
    <div class="page-body">
        <div class="container-xl">
            <!-- User Statistics Cards -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Total Users -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-primary text-white avatar">
                                                <i class="ti ti-users"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="total-users">{{ number_format($totalUsers ?? 0) }}</span>
                                                Users
                                            </div>
                                            <div class="text-muted">
                                                Total Users
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Admin Users -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-warning text-white avatar">
                                                <i class="ti ti-shield"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="admin-users">{{ number_format($adminUsers ?? 0) }}</span>
                                                Users
                                            </div>
                                            <div class="text-muted">
                                                Admin
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Guest Users -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-info text-white avatar">
                                                <i class="ti ti-user"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="guest-users">{{ number_format($guestUsers ?? 0) }}</span>
                                                Users
                                            </div>
                                            <div class="text-muted">
                                                Guest
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Users List</h3>
                    <div class="card-actions">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-filter"></i>
                                    </span>
                                    <select id="role-filter" class="form-select">
                                        <option value="">All Roles</option>
                                        <option value="guest">Guest</option>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                        <option value="superadmin">Super Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th width="120">Role</th>
                                    <th width="100">RFID</th>
                                    <th width="120">Created At</th>
                                    <th width="80">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will fill this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Detail Modal --}}
    <div class="modal modal-blur fade" id="user-detail-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="user-detail-content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2 text-muted">Loading...</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete User Modal --}}
    <div class="modal modal-blur fade" id="modal-delete-user" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="ti ti-alert-triangle text-danger" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Are you sure?</h3>
                        <p>Are you sure you want to delete the user account for <span id="user-to-delete"
                                class="fw-bold"></span>?</p>
                        <p class="text-danger">This action cannot be undone and will:</p>
                        <ul class="text-start text-danger">
                            <li>Permanently delete the user account and all associated data</li>
                            <li>Release any assigned RFID tags</li>
                            <li>Send a deletion notification email to the user</li>
                        </ul>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Deletion Reason (Optional)</label>
                        <textarea id="deletion-reason" class="form-control" rows="3"
                            placeholder="Enter reason for account deletion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger ms-auto" id="btn-confirm-delete">
                        <i class="ti ti-trash me-1"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Unassign RFID Modal --}}
    <div class="modal modal-blur fade" id="modal-unassign-rfid" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Unassign RFID Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="ti ti-credit-card-off text-warning" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Unassign RFID Tag?</h3>
                        <p>Are you sure you want to unassign the RFID tag from <span id="user-to-unassign-rfid"
                                class="fw-bold"></span>?</p>
                        <p class="text-warning">The RFID tag will be marked as available for reassignment.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning ms-auto" id="btn-confirm-unassign-rfid">
                        <i class="ti ti-credit-card-off me-1"></i>Unassign RFID
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .name-detail {
                cursor: pointer;
                transition: color 0.15s ease-in-out;
            }

            .name-detail:hover {
                color: var(--tblr-primary) !important;
            }

            .role-select {
                min-width: 120px;
            }

            .dropdown-menu-actions {
                min-width: 140px;
            }

            .dropdown-item:hover {
                background-color: var(--tblr-hover-bg);
            }

            .dropdown-item.text-danger:hover {
                background-color: var(--tblr-red-lt);
                color: var(--tblr-red) !important;
            }

            .dropdown-item.text-warning:hover {
                background-color: var(--tblr-yellow-lt);
                color: var(--tblr-yellow) !important;
            }

            .btn-actions {
                border: none;
                background: transparent;
                color: var(--tblr-body-color);
                font-size: 1.2rem;
                padding: 0.375rem 0.5rem;
                border-radius: var(--tblr-border-radius);
                transition: all 0.15s ease-in-out;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-actions:hover {
                background-color: var(--tblr-hover-bg);
                color: var(--tblr-primary);
            }

            /* Pastikan kolom actions benar-benar center */
            #usersTable thead th:last-child,
            #usersTable tbody td:last-child {
                text-align: center !important;
                vertical-align: middle !important;
            }

            /* Force center alignment untuk DataTables */
            .dataTables_wrapper .text-center {
                text-align: center !important;
            }

            .modal-loading {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 200px;
            }

            /* Force center alignment untuk DataTables */
            #users-table thead th:last-child,
            #users-table tbody td:last-child {
                text-align: center !important;
                vertical-align: middle !important;
            }

            /* Remove dropdown arrow */
            .dropdown-toggle::after {
                display: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                const csrf = "{{ csrf_token() }}";
                let userToDelete = null;
                let userToUnassignRfid = null;

                // Initialize DataTable
                const usersTable = $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('superadmin.users.data') }}",
                        type: 'GET',
                        dataSrc: function(json) {
                            // Update statistics if provided in response
                            updateStats(json.stats || {});
                            return json.data;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'name_link',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'role_select',
                            name: 'role',
                            orderable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'rfid_status',
                            name: 'rfid_status',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at',
                            className: 'text-center'
                        },
                        {
                            data: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    order: [
                        [5, 'desc']
                    ],
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search users...',
                        lengthMenu: '_MENU_ entries per page',
                        processing: "Loading users...",
                        emptyTable: "No users found",
                        info: "Showing _START_ to _END_ of _TOTAL_ users",
                        infoEmpty: "Showing 0 to 0 of 0 users",
                        infoFiltered: "(filtered from _MAX_ total users)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 25,
                    responsive: true
                });

                // Refresh button
                $('#refresh-btn').on('click', function() {
                    usersTable.ajax.reload();
                    showNotification('success', 'Data refreshed successfully!');
                });

                // Role filter
                $('#role-filter').on('change', function() {
                    const selectedRole = $(this).val();
                    usersTable.column(3).search(selectedRole).draw();
                });

                // Role change handler
                $(document).on('change', '.role-select', function() {
                    const $select = $(this);
                    const userId = $select.data('user-id');
                    const newRole = $select.val();
                    const originalRole = $select.data('original-role');

                    updateUserRole(userId, newRole, originalRole, $select);
                });

                // User detail modal
                $(document).on('click', '.name-detail', function(e) {
                    e.preventDefault();
                    const userId = $(this).data('id');
                    showUserDetail(userId);
                });

                // Delete user handler
                $(document).on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    const userId = $(this).data('id');
                    const userName = $(this).data('name');
                    const userEmail = $(this).data('email');

                    userToDelete = {
                        id: userId,
                        name: userName,
                        email: userEmail
                    };

                    $('#user-to-delete').text(userName + ' (' + userEmail + ')');
                    $('#deletion-reason').val('');
                    $('#modal-delete-user').modal('show');
                });

                // Unassign RFID handler
                $(document).on('click', '.unassign-rfid', function(e) {
                    e.preventDefault();
                    const userId = $(this).data('id');
                    const userName = $(this).data('name');

                    userToUnassignRfid = {
                        id: userId,
                        name: userName
                    };

                    $('#user-to-unassign-rfid').text(userName);
                    $('#modal-unassign-rfid').modal('show');
                });

                // Confirm delete
                $('#btn-confirm-delete').on('click', function() {
                    if (!userToDelete) return;

                    const deletionReason = $('#deletion-reason').val().trim();

                    $.ajax({
                        url: `/superadmin/users/${userToDelete.id}`,
                        method: 'DELETE',
                        data: {
                            _token: csrf,
                            deletion_reason: deletionReason
                        },
                        success: function(response) {
                            if (response.success) {
                                usersTable.ajax.reload();
                                showNotification('success', response.message);
                            } else {
                                showNotification('error', response.message);
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Failed to delete user. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            showNotification('error', errorMessage);
                        }
                    });

                    $('#modal-delete-user').modal('hide');
                });

                // Confirm unassign RFID
                $('#btn-confirm-unassign-rfid').on('click', function() {
                    if (!userToUnassignRfid) return;

                    $.ajax({
                        url: `/superadmin/users/${userToUnassignRfid.id}/unassign-rfid`,
                        method: 'POST',
                        data: {
                            _token: csrf
                        },
                        success: function(response) {
                            if (response.success) {
                                usersTable.ajax.reload();
                                showNotification('success', response.message);
                            } else {
                                showNotification('error', response.message);
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Failed to unassign RFID. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            showNotification('error', errorMessage);
                        }
                    });

                    $('#modal-unassign-rfid').modal('hide');
                });

                // Clear variables when modals are hidden
                $('#modal-delete-user').on('hidden.bs.modal', function() {
                    userToDelete = null;
                });

                $('#modal-unassign-rfid').on('hidden.bs.modal', function() {
                    userToUnassignRfid = null;
                });

                // Functions
                function updateUserRole(userId, newRole, originalRole, $select) {
                    $.ajax({
                        url: "{{ route('superadmin.users.update-role') }}",
                        method: 'PATCH',
                        data: {
                            user_id: userId,
                            role: newRole
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        beforeSend: function() {
                            $select.prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.success) {
                                $select.data('original-role', newRole);
                                showNotification('success', response.message ||
                                    'Role updated successfully!');
                            }
                        },
                        error: function(xhr) {
                            $select.val(originalRole);
                            showNotification('error', 'Failed to update role. Please try again.');
                            console.error('Role update error:', xhr.responseText);
                        },
                        complete: function() {
                            $select.prop('disabled', false);
                        }
                    });
                }

                function showUserDetail(userId) {
                    const $modal = $('#user-detail-modal');
                    const $content = $('#user-detail-content');

                    $modal.modal('show');
                    $content.html(`
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <div class="mt-2 text-muted">Loading...</div>
                        </div>
                    `);

                    $.ajax({
                        url: `/superadmin/users/${userId}`,
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        success: function(response) {
                            $content.html(response.html);
                        },
                        error: function(xhr) {
                            $content.html(`
                                <div class="alert alert-danger mb-0">
                                    <i class="ti ti-alert-circle me-2"></i>
                                    Failed to load user details. Please try again.
                                </div>
                            `);
                            console.error('User detail error:', xhr.responseText);
                        }
                    });
                }

                function showNotification(type, message) {
                    if (typeof toastr !== 'undefined') {
                        toastr[type](message);
                    } else {
                        alert(message);
                    }
                }

                function updateStats(stats) {
                    if (stats.total_users !== undefined) {
                        $('#total-users').text(stats.total_users.toLocaleString());
                    }
                    if (stats.admin_users !== undefined) {
                        $('#admin-users').text(stats.admin_users.toLocaleString());
                    }
                    if (stats.guest_users !== undefined) {
                        $('#guest-users').text(stats.guest_users.toLocaleString());
                    }
                }
            });
        </script>
    @endpush
</x-layouts.superadmin_layout>
