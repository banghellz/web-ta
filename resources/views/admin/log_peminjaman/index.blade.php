<x-layouts.superadmin_layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-history me-2 text-primary"></i>{{ $title ?? 'Borrowing History' }}
                    </h2>
                    <div class="text-muted mt-1">{{ $content ?? 'Complete log of all borrowing activities' }}</div>
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
                    <h3 class="card-title">Borrowing Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Total Logs -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-primary text-white avatar">
                                                <i class="ti ti-list"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="total-logs-count">0</span> Logs
                                            </div>
                                            <div class="text-muted">
                                                Total Activities
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Total Borrowed -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-blue text-white avatar">
                                                <i class="ti ti-arrow-up-right"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="total-borrowed-count">0</span> Times
                                            </div>
                                            <div class="text-muted">
                                                Total Borrowed
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Total Returned -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-success text-white avatar">
                                                <i class="ti ti-arrow-down-left"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="total-returned-count">0</span> Times
                                            </div>
                                            <div class="text-muted">
                                                Total Returned
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Currently Borrowed -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-warning text-white avatar">
                                                <i class="ti ti-clock"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="currently-borrowed-count">0</span> Items
                                            </div>
                                            <div class="text-muted">
                                                Currently Borrowed
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
                    <h3 class="card-title">Complete Borrowing History</h3>
                    <div class="card-actions">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-filter"></i>
                                    </span>
                                    <select id="activity-filter" class="form-select">
                                        <option value="">All Activities</option>
                                        <option value="pinjam">Borrowed</option>
                                        <option value="kembali">Returned</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
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
                        <table id="borrowing-logs-table" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>Username</th>
                                    <th>Item Name</th>
                                    <th>Activity</th>
                                    <th>Date & Time</th>
                                    <th>Time Ago</th>
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

                // Initialize DataTable with error handling
                const table = $('#borrowing-logs-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('superadmin.log_peminjaman.data') }}",
                        type: 'GET',
                        data: function(d) {
                            d.date = $('#date-filter').val();
                            d.activity_type = $('#activity-filter').val();
                        },
                        dataSrc: function(json) {
                            // Check if stats exist in response
                            if (json.stats) {
                                updateStats(json.stats);
                            }
                            return json.data || [];
                        },
                        error: function(xhr, error, code) {
                            console.error('DataTables Ajax Error:', {
                                xhr: xhr,
                                error: error,
                                code: code,
                                responseText: xhr.responseText
                            });

                            // Show user-friendly error message
                            toastr.error('Failed to load data. Please refresh the page.');

                            // Update stats with zeros on error
                            updateStats({
                                total_logs: 0,
                                total_borrowed: 0,
                                total_returned: 0,
                                currently_borrowed: 0
                            });
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
                            name: 'username',
                            render: function(data, type, row) {
                                if (!data) return 'Unknown User';
                                return '<div class="d-flex align-items-center">' +
                                    '<div class="avatar avatar-sm me-2" style="background-color: ' +
                                    stringToColor(data) + '; color: white;">' +
                                    getInitials(data) + '</div>' +
                                    '<div>' + data + '</div>' +
                                    '</div>';
                            }
                        },
                        {
                            data: 'item_display',
                            name: 'item_name',
                            render: function(data, type, row) {
                                return data || 'Unknown Item';
                            }
                        },
                        {
                            data: 'activity_badge',
                            name: 'activity_type',
                            orderable: false,
                            render: function(data, type, row) {
                                return data || '<span class="badge bg-secondary">Unknown</span>';
                            }
                        },
                        {
                            data: 'timestamp_formatted',
                            name: 'timestamp',
                            render: function(data, type, row) {
                                return '<div class="text-muted">' + (data || '-') + '</div>';
                            }
                        },
                        {
                            data: 'time_ago',
                            name: 'time_ago',
                            orderable: false,
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        }
                    ],
                    order: [
                        [4, 'desc']
                    ],
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search...',
                        lengthMenu: '_MENU_ entries per page',
                        emptyTable: 'No borrowing activities found',
                        zeroRecords: 'No matching records found',
                        processing: '<div class="text-center"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading...</div>'
                    },
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 25,
                });

                // Handle activity type filter
                $('#activity-filter').on('change', function() {
                    table.ajax.reload();
                });

                // Handle date filter
                $('#date-filter').on('change', function() {
                    table.ajax.reload();
                });

                // Handle refresh button
                $('#reload-logs').on('click', function() {
                    table.ajax.reload();
                    toastr.success('Data refreshed successfully!');
                });

                // Handle clear logs button
                $('#clear-logs').on('click', function() {
                    if (confirm(
                            'Are you sure you want to clear all borrowing logs? This action cannot be undone and will remove all historical data.'
                        )) {
                        $.ajax({
                            url: "{{ route('superadmin.log_peminjaman.clear') }}",
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
                                const response = xhr.responseJSON;
                                toastr.error(response?.message ||
                                    'Error clearing logs. Please try again.');
                                console.error('Clear logs error:', xhr.responseText);
                            }
                        });
                    }
                });

                // Helper function to update the statistics
                function updateStats(stats) {
                    $('#total-logs-count').text(stats.total_logs || 0);
                    $('#total-borrowed-count').text(stats.total_borrowed || 0);
                    $('#total-returned-count').text(stats.total_returned || 0);
                    $('#currently-borrowed-count').text(stats.currently_borrowed || 0);
                }

                // Helper function to get initials from name
                function getInitials(name) {
                    if (!name) return '?';
                    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
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
                        todayHighlight: true,
                        orientation: 'bottom auto'
                    });
                }

                // Auto-refresh every 30 seconds
                setInterval(function() {
                    table.ajax.reload(null, false); // false = don't reset paging
                }, 30000);
            });
        </script>
    @endpush
</x-layouts.superadmin_layout>
