<x-layouts.user_layout title="History" pageTitle="My Borrowing History">
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-history me-2 text-primary"></i>{{ $title ?? 'My Borrowing History' }}
                    </h2>
                    <div class="text-muted mt-1">{{ $content ?? 'Your personal borrowing history' }}</div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <span class="d-none d-sm-inline">
                            <button id="reload-logs" class="btn btn-primary">
                                <i class="ti ti-refresh me-1"></i>
                                Refresh Data
                            </button>
                        </span>
                        <button id="show-currently-borrowed" class="btn btn-info">
                            <i class="ti ti-list-check me-1"></i>
                            Currently Borrowed
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- User Statistics Cards -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Borrowing Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
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
                        <!-- Completion Rate -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-purple text-white avatar">
                                                <i class="ti ti-percentage"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="completion-rate">0</span>%
                                            </div>
                                            <div class="text-muted">
                                                Completion Rate
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Borrowing History Table -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">My Borrowing History</h3>
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
                        <table id="user-borrowing-logs-table" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>Item Name</th>
                                    <th>Activity</th>
                                    <th>Status</th>
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

    <!-- Currently Borrowed Modal -->
    <div class="modal modal-blur fade" id="currentlyBorrowedModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Currently Borrowed Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="currently-borrowed-list">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Loading...
                        </div>
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
                // Initialize DataTable with error handling
                const table = $('#user-borrowing-logs-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.log-peminjaman.data') }}",
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
                                total_borrowed: 0,
                                total_returned: 0,
                                currently_borrowed: 0,
                                completion_rate: 0
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
                            data: 'item_display',
                            name: 'item_name',
                            render: function(data, type, row) {
                                return '<div class="d-flex align-items-center">' +
                                    '<div class="avatar avatar-sm me-2 bg-secondary text-white">' +
                                    '<i class="ti ti-package"></i></div>' +
                                    '<div>' + (data || 'Unknown Item') + '</div>' +
                                    '</div>';
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
                            data: 'status',
                            name: 'status',
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
                    lengthMenu: [10, 25, 50],
                    pageLength: 10,
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

                // Handle currently borrowed button
                $('#show-currently-borrowed').on('click', function() {
                    loadCurrentlyBorrowed();
                    $('#currentlyBorrowedModal').modal('show');
                });

                // Load currently borrowed items
                function loadCurrentlyBorrowed() {
                    $('#currently-borrowed-list').html(`
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Loading...
                        </div>
                    `);

                    $.ajax({
                        url: "{{ route('user.log-peminjaman.currently_borrowed') }}",
                        type: 'GET',
                        success: function(response) {
                            if (response.currently_borrowed && response.currently_borrowed.length > 0) {
                                let html = '<div class="list-group">';
                                response.currently_borrowed.forEach(function(item) {
                                    html += `
                                        <div class="list-group-item">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="avatar bg-warning text-white">
                                                        <i class="ti ti-package"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">${item.item_name}</div>
                                                    <div class="text-muted">
                                                        Borrowed: ${item.borrowed_date} (${item.last_borrowed})
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                                html += '</div>';
                                $('#currently-borrowed-list').html(html);
                            } else {
                                $('#currently-borrowed-list').html(`
                                    <div class="text-center py-4">
                                        <div class="empty-icon">
                                            <i class="ti ti-check-circle text-success" style="font-size: 3rem;"></i>
                                        </div>
                                        <div class="mt-3">
                                            <h3>No items currently borrowed</h3>
                                            <p class="text-muted">You have returned all borrowed items!</p>
                                        </div>
                                    </div>
                                `);
                            }
                        },
                        error: function(xhr) {
                            $('#currently-borrowed-list').html(`
                                <div class="text-center py-4">
                                    <div class="text-danger">
                                        <i class="ti ti-alert-circle me-1"></i>
                                        Failed to load currently borrowed items
                                    </div>
                                </div>
                            `);
                        }
                    });
                }

                // Helper function to update the statistics
                function updateStats(stats) {
                    $('#total-borrowed-count').text(stats.total_borrowed || 0);
                    $('#total-returned-count').text(stats.total_returned || 0);
                    $('#currently-borrowed-count').text(stats.currently_borrowed || 0);
                    $('#completion-rate').text(stats.completion_rate || 0);
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

                // Auto-refresh every 60 seconds (less frequent than admin)
                setInterval(function() {
                    table.ajax.reload(null, false); // false = don't reset paging
                }, 60000);
            });
        </script>
    @endpush
</x-layouts.user_layout>
