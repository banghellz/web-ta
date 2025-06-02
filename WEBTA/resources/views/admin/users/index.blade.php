<!-- resources/views/admin/users/index.blade.php -->
<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-activity me-2 text-primary"></i>{{ $title ?? 'Activity Logs' }}
                    </h2>
                    <div class="text-muted mt-1">{{ $content ?? 'Track user login activities' }}</div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <span class="d-none d-sm-inline">
                            <button id="reload-logs" class="btn btn-primary">
                                <i class="ti ti-refresh me-1"></i>
                                Refresh Data
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $title ?? 'Users List' }}</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-vcenter table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
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

    <!-- Modal for Student Details -->
    <div class="modal modal-blur fade" id="studentDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="studentDetailContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status"></div>
                        <p>Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                const csrfToken = "{{ csrf_token() }}";

                // Initialize DataTable
                const table = $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.users.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name_link', // Use the new name_link column
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
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                // Handle role change
                $(document).on('change', '.role-select', function() {
                    const userId = $(this).data('user-id');
                    const role = $(this).val();

                    $.ajax({
                        url: "{{ route('admin.users.update-role') }}",
                        type: 'PATCH',
                        data: {
                            user_id: userId,
                            role: role
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show success notification using Tabler notifications
                                toastr.success('Role updated successfully!');
                            }
                        },
                        error: function(xhr) {
                            // Show error notification
                            toastr.error('Error updating role. Please try again.');
                            console.error(xhr.responseText);
                        }
                    });
                });

                // Handle name click for details - using event delegation
                $(document).on('click', '.name-detail', function(e) {
                    e.preventDefault();

                    const userId = $(this).data('id');

                    // Show modal with loading spinner
                    $('#studentDetailModal').modal('show');

                    // Load user details
                    $.ajax({
                        url: `/admin/users/${userId}`,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            // Update modal content with user details
                            $('#studentDetailContent').html(response.html);
                        },
                        error: function(xhr) {
                            $('#studentDetailContent').html(
                                '<div class="alert alert-danger">Failed to load data. Please try again.</div>'
                            );
                            console.error(xhr.responseText);
                        }
                    });
                });

                // Handle refresh button
                $('#reload-logs').on('click', function() {
                    table.ajax.reload();
                    toastr.success('Data refreshed successfully!');
                });

                // Handle delete confirmation
                $(document).on('click', '.btn-delete', function() {
                    const userId = $(this).data('id');
                    const userName = $(this).data('name');

                    if (confirm(`Are you sure you want to delete user "${userName}"?`)) {
                        $.ajax({
                            url: `/admin/users/${userId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Refresh the datatable
                                    table.ajax.reload();
                                    toastr.success('User deleted successfully!');
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Error deleting user. Please try again.');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
