<x-layouts.superadmin_layout>
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
                        <button id="clear-logs" class="btn btn-danger">
                            <i class="ti ti-trash me-1"></i>
                            Clear All Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Statistics Cards -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Login Activity Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Login Stats -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-primary text-white avatar">
                                                <i class="ti ti-login"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="login-count">0</span> Logins
                                            </div>
                                            <div class="text-muted">
                                                Today
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Registration Stats -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar">
                                                <i class="ti ti-user-plus"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="register-count">0</span> Registrations
                                            </div>
                                            <div class="text-muted">
                                                Today
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Logout Stats -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-red text-white avatar">
                                                <i class="ti ti-logout"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="logout-count">0</span> Logouts
                                            </div>
                                            <div class="text-muted">
                                                Today
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Activity Log Detail</h3>
                    <div class="card-actions">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-calendar"></i>
                                    </span>
                                    <input type="text" id="date-filter" class="form-control"
                                        placeholder="Filter by date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="activity-logs-table" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Activity</th>
                                    <th>Time</th>
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

    @push('scripts')
        <script>
            $(function() {
                const csrfToken = "{{ csrf_token() }}";

                // Initialize DataTable with custom styling
                const table = $('#activity-logs-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/superadmin/activity-logs/data",
                        dataSrc: function(json) {
                            updateStats(json.stats || {});
                            return json.data;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'user_name',
                            name: 'user.name',
                            render: function(data, type, row) {
                                return '<div class="d-flex align-items-center">' +
                                    '</span>' +
                                    '<div>' + data + '</div>' +
                                    '</div>';
                            }
                        },
                        {
                            data: 'user_email',
                            name: 'user.email'
                        },
                        {
                            data: 'user_role',
                            name: 'user.role',
                            render: function(data, type, row) {
                                let badgeClass = 'bg-secondary';

                                if (data === 'admin') {
                                    badgeClass = 'bg-primary';
                                } else if (data === 'superadmin') {
                                    badgeClass = 'bg-danger';
                                } else if (data === 'user') {
                                    badgeClass = 'bg-success';
                                }

                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        {
                            data: 'activity',
                            name: 'activity',
                            render: function(data, type, row) {
                                let iconClass = '';
                                let badgeClass = '';

                                if (data === 'login') {
                                    iconClass = 'ti-login';
                                    badgeClass = 'bg-primary';
                                } else if (data === 'logout') {
                                    iconClass = 'ti-logout';
                                    badgeClass = 'bg-danger';
                                } else if (data === 'register') {
                                    iconClass = 'ti-user-plus';
                                    badgeClass = 'bg-success';
                                } else {
                                    iconClass = 'ti-activity';
                                    badgeClass = 'bg-secondary';
                                }

                                return '<span class="badge ' + badgeClass + '"><i class="ti ' +
                                    iconClass + ' me-1"></i>' +
                                    data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                            }
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at',
                            render: function(data, type, row) {
                                return '<div class="text-muted">' + data + '</div>';
                            }
                        }
                    ],
                    order: [
                        [5, 'desc']
                    ], // Order by timestamp descending
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search...',
                        lengthMenu: '_MENU_ entries per page',
                    },
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 25,
                });

                // Handle refresh button
                $('#reload-logs').on('click', function() {
                    table.ajax.reload();
                    toastr.success('Data refreshed successfully!');
                });

                // Handle clear logs button
                $('#clear-logs').on('click', function() {
                    if (confirm(
                            'Are you sure you want to clear all activity logs? This action cannot be undone.'
                        )) {
                        $.ajax({
                            url: "{{ route('superadmin.activity-logs.clear') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    table.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Error clearing logs. Please try again.');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });

                // Helper function to update the statistics
                function updateStats(stats) {
                    $('#login-count').text(stats.login || 0);
                    $('#register-count').text(stats.register || 0);
                    $('#logout-count').text(stats.logout || 0);
                }

                // Helper function to get initials from name
                function getInitials(name) {
                    if (!name) return '?';
                    return name.split(' ').map(n => n[0]).join('').toUpperCase();
                }

                // Helper function to generate color from string
                function stringToColor(str) {
                    if (!str) return '#607D8B';
                    let hash = 0;
                    for (let i = 0; i < str.length; i++) {
                        hash = str.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    let color = '#';
                    for (let i = 0; i < 3; i++) {
                        const value = (hash >> (i * 8)) & 0xFF;
                        color += ('00' + value.toString(16)).substr(-2);
                    }
                    return color;
                }

                // Initialize datepicker for date filter
                if ($.fn.datepicker) {
                    $('#date-filter').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true
                    }).on('changeDate', function(e) {
                        table.search($(this).val()).draw();
                    });
                } else {
                    // If datepicker is not available, use a regular input
                    $('#date-filter').on('keyup', function() {
                        table.search($(this).val()).draw();
                    });
                }
            });
        </script>
    @endpush
</x-layouts.superadmin_layout>
