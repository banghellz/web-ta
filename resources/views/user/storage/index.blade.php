<x-layouts.user_layout title="Storage" pageTitle="Storage">
    <x-slot name="title">My Storage</x-slot>
    <x-slot name="content">Manage your borrowed items</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-package me-2 text-primary"></i>
                        My Storage
                    </h2>
                    <div class="text-muted mt-1">
                        Manage your borrowed items
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

    {{-- Statistics Cards --}}
    <div class="page-body">
        <div class="container-xl">
            <!-- Statistics Cards -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Storage Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Total Borrowed -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-primary text-white avatar">
                                                <i class="ti ti-package"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="total-borrowed">0</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                Total Borrowed
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Today -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar">
                                                <i class="ti ti-calendar-today"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="borrowed-today">0</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                Today
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- This Week -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-info text-white avatar">
                                                <i class="ti ti-calendar-week"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="borrowed-week">0</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                This Week
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- This Month -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-warning text-white avatar">
                                                <i class="ti ti-calendar-month"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="borrowed-month">0</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                This Month
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">My Borrowed Items</h3>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="storageTable" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>EPC</th>
                                    <th>Item Name</th>
                                    <th>Borrowed At</th>
                                    <th>Duration (Days)</th>
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

                // Initialize DataTable
                const table = $('#storageTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.storage.data') }}",
                        type: 'GET',
                        dataSrc: function(json) {
                            // Update statistics if provided in response
                            updateStats(json.stats || {});
                            return json.data;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTables error:', error, thrown);
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Error loading data. Please refresh the page.');
                            }
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
                            data: 'epc',
                            name: 'epc',
                            render: function(data, type, row) {
                                return '<div class="d-flex align-items-center">' +
                                    '<span class="avatar avatar-sm me-2 bg-secondary text-white">' +
                                    '<i class="ti ti-qrcode"></i>' +
                                    '</span>' +
                                    '<div><strong>' + data + '</strong></div>' +
                                    '</div>';
                            }
                        },
                        {
                            data: 'nama_barang',
                            name: 'nama_barang',
                            render: function(data, type, row) {
                                return '<div class="text-wrap fw-bold">' + data + '</div>';
                            }
                        },
                        {
                            data: 'borrowed_at_formatted',
                            name: 'updated_at',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<div class="text-muted">' + data + '</div>';
                            }
                        },
                        {
                            data: 'duration_days',
                            name: 'duration_days',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<span class="fw-bold">' + Math.floor(data) + '</span>';
                            }
                        }
                    ],
                    order: [
                        [3, 'desc']
                    ], // Order by borrowed_at descending
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search borrowed items...',
                        lengthMenu: '_MENU_ entries per page',
                        processing: "Loading borrowed items...",
                        emptyTable: "No borrowed items found",
                        info: "Showing _START_ to _END_ of _TOTAL_ items",
                        infoEmpty: "Showing 0 to 0 of 0 items",
                        infoFiltered: "(filtered from _MAX_ total items)",
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

                // Handle refresh button
                $('#refresh-btn').on('click', function() {
                    const btn = $(this);
                    btn.prop('disabled', true);
                    btn.html('<i class="ti ti-loader animate-spin me-1"></i>Refreshing...');

                    table.ajax.reload(function() {
                        btn.prop('disabled', false);
                        btn.html('<i class="ti ti-refresh me-1"></i>Refresh');

                        if (typeof toastr !== 'undefined') {
                            toastr.success('Data refreshed successfully!');
                        }
                    });
                });

                // Status filter
                $('#status-filter').on('change', function() {
                    const selectedStatus = $(this).val();
                    table.column(3).search(selectedStatus).draw();
                });

                // Helper function to update the statistics
                function updateStats(stats) {
                    if (stats.total_borrowed !== undefined) {
                        $('#total-borrowed').text(stats.total_borrowed);
                    }
                    if (stats.borrowed_today !== undefined) {
                        $('#borrowed-today').text(stats.borrowed_today);
                    }
                    if (stats.borrowed_this_week !== undefined) {
                        $('#borrowed-week').text(stats.borrowed_this_week);
                    }
                    if (stats.borrowed_this_month !== undefined) {
                        $('#borrowed-month').text(stats.borrowed_this_month);
                    }
                }

                // Show notifications
                @if (session('success'))
                    if (typeof toastr !== 'undefined') {
                        toastr.success('{{ session('success') }}');
                    } else {
                        alert('{{ session('success') }}');
                    }
                @endif

                @if (session('error'))
                    if (typeof toastr !== 'undefined') {
                        toastr.error('{{ session('error') }}');
                    } else {
                        alert('{{ session('error') }}');
                    }
                @endif
            });

            // Global refresh function for backward compatibility
            function refreshTable() {
                if ($('#storageTable').DataTable()) {
                    $('#storageTable').DataTable().ajax.reload(null, false);
                }
            }
        </script>
    @endpush
</x-layouts.user_layout>
