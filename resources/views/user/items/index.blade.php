<!-- resources/views/admin/dashboard/index.blade.php -->
<x-layouts.user_layout title="Stocks" pageTitle="Stocks">
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-package me-2 text-primary"></i>{{ $title ?? 'Items Management' }}
                    </h2>
                    <div class="text-muted mt-1">{{ $content ?? 'Manage inventory items and stock levels' }}</div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <span class="d-none d-sm-inline">
                            <button id="reload-items" class="btn btn-primary">
                                <i class="ti ti-refresh me-1"></i>
                                Refresh Data
                            </button>
                        </span>
                        <a href="{{ route('user.items.create') }}" class="btn btn-success">
                            <i class="ti ti-plus me-1"></i>
                            Add New Item
                        </a>
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
                    <h3 class="card-title">Inventory Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Total Items -->
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
                                                <span id="total-items">{{ number_format($totalItems ?? 0) }}</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                Total Items
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Available Items -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar">
                                                <i class="ti ti-check"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span
                                                    id="available-items">{{ number_format($availableItems ?? 0) }}</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                Available
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Out of Stock -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-red text-white avatar">
                                                <i class="ti ti-x"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span
                                                    id="out-of-stock-items">{{ number_format($outOfStockItems ?? 0) }}</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                Out of Stock
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Availability Rate -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-info text-white avatar">
                                                <i class="ti ti-percentage"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span
                                                    id="availability-rate">{{ $totalItems > 0 ? round(($availableItems / $totalItems) * 100) : 0 }}</span>%
                                            </div>
                                            <div class="text-muted">
                                                Availability Rate
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Items List</h3>
                    <div class="card-actions">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-filter"></i>
                                    </span>
                                    <select id="status-filter" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="available">Available</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="itemsTable" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>EPC Code</th>
                                    <th>Item Name</th>
                                    <th>Available Quantity</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th width="150">Actions</th>
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
                const table = $('#itemsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.items.data') }}",
                        type: 'GET',
                        dataSrc: function(json) {
                            // Update statistics if provided in response
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
                                return '<div class="text-wrap">' + data + '</div>';
                            }
                        },
                        {
                            data: 'available',
                            name: 'available',
                            className: 'text-center',
                            render: function(data, type, row) {
                                let badgeClass = 'bg-success';
                                if (data == 0) {
                                    badgeClass = 'bg-danger';
                                } else if (data <= 5) {
                                    badgeClass = 'bg-warning';
                                }
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center',
                            render: function(data, type, row) {
                                let iconClass = '';
                                let badgeClass = '';

                                if (data === 'available') {
                                    iconClass = 'ti-check';
                                    badgeClass = 'bg-success';
                                } else if (data === 'out_of_stock') {
                                    iconClass = 'ti-x';
                                    badgeClass = 'bg-danger';
                                } else if (data === 'low_stock') {
                                    iconClass = 'ti-alert-triangle';
                                    badgeClass = 'bg-warning';
                                } else {
                                    iconClass = 'ti-help';
                                    badgeClass = 'bg-secondary';
                                }

                                return '<span class="badge ' + badgeClass + '"><i class="ti ' +
                                    iconClass + ' me-1"></i>' +
                                    data.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) +
                                    '</span>';
                            }
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<div class="text-muted">' + data + '</div>';
                            }
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    order: [
                        [5, 'desc']
                    ], // Order by created_at descending
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search items...',
                        lengthMenu: '_MENU_ entries per page',
                        processing: "Loading items...",
                        emptyTable: "No items found",
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
                $('#reload-items').on('click', function() {
                    table.ajax.reload();
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Data refreshed successfully!');
                    }
                });

                // Status filter
                $('#status-filter').on('change', function() {
                    const selectedStatus = $(this).val();
                    table.column(4).search(selectedStatus).draw();
                });

                // Delete confirmation using SweetAlert2
                $(document).on('click', '.delete-item', function(e) {
                    e.preventDefault();

                    const form = $(this).closest('form');
                    const itemName = $(this).data('item-name');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Delete Item',
                            text: `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Delete',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        // Fallback to confirm dialog if SweetAlert2 is not available
                        if (confirm(
                                `Are you sure you want to delete "${itemName}"? This action cannot be undone.`
                            )) {
                            form.submit();
                        }
                    }
                });

                // Helper function to update the statistics
                function updateStats(stats) {
                    if (stats.total_items !== undefined) {
                        $('#total-items').text(stats.total_items.toLocaleString());
                    }
                    if (stats.available_items !== undefined) {
                        $('#available-items').text(stats.available_items.toLocaleString());
                    }
                    if (stats.out_of_stock_items !== undefined) {
                        $('#out-of-stock-items').text(stats.out_of_stock_items.toLocaleString());
                    }
                    if (stats.availability_rate !== undefined) {
                        $('#availability-rate').text(Math.round(stats.availability_rate));
                    }
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
            });

            // Global refresh function for backward compatibility
            function refreshTable() {
                if ($('#itemsTable').DataTable()) {
                    $('#itemsTable').DataTable().ajax.reload(null, false);
                }
            }
        </script>
    @endpush
</x-layouts.user_layout>
