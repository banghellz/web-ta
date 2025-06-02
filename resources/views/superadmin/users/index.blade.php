{{-- resources/views/admin/users/index.blade.php --}}
<x-app-layout>
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
                <div class="col-auto">
                    <button id="refresh-btn" class="btn btn-primary">
                        <i class="ti ti-refresh me-1"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Page Body --}}
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-vcenter table-hover">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th width="120">Role</th>
                                    <th width="150">Created At</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Detail Modal --}}
    <div class="modal modal-blur fade" id="user-detail-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                const csrf = "{{ csrf_token() }}";

                // Initialize DataTable
                const usersTable = $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('superadmin.users.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
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
                            orderable: false
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at'
                        },
                        {
                            data: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [4, 'desc']
                    ],
                    pageLength: 25,
                    responsive: true
                });

                // Refresh button
                $('#refresh-btn').on('click', function() {
                    usersTable.ajax.reload();
                    showNotification('success', 'Data refreshed successfully!');
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

                // Delete user
                $(document).on('click', '.btn-delete', function() {
                    const userId = $(this).data('id');
                    const userName = $(this).data('name');

                    confirmDelete(userName, () => deleteUser(userId));
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
                                showNotification('success', 'Role updated successfully!');
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

                function deleteUser(userId) {
                    $.ajax({
                        url: `/superadmin/users/${userId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        success: function(response) {
                            if (response.success) {
                                usersTable.ajax.reload();
                                showNotification('success', 'User deleted successfully!');
                            }
                        },
                        error: function(xhr) {
                            showNotification('error', 'Failed to delete user. Please try again.');
                            console.error('Delete error:', xhr.responseText);
                        }
                    });
                }

                function confirmDelete(userName, callback) {
                    if (confirm(
                            `Are you sure you want to delete user "${userName}"?\n\nThis action cannot be undone.`)) {
                        callback();
                    }
                }

                function showNotification(type, message) {
                    if (typeof toastr !== 'undefined') {
                        toastr[type](message);
                    } else {
                        alert(message);
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
