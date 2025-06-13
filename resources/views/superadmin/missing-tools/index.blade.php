{{-- resources/views/superadmin/missing-tools/index.blade.php --}}
<x-layouts.superadmin_layout>
    <x-slot name="title">Missing Tools Management</x-slot>
    <x-slot name="content">Manage and track missing tools in the system</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-alert-triangle me-2 text-danger"></i>
                        Missing Tools Management
                    </h2>
                    <div class="text-muted mt-1">
                        Track and manage missing tools in the system
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

    {{-- Stats Cards --}}
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards mb-4">
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pending Missing</div>
                            </div>
                            <div class="h1 mb-0" id="pending-count">-</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Reclaimed</div>
                            </div>
                            <div class="h1 mb-0" id="completed-count">-</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Cancelled</div>
                            </div>
                            <div class="h1 mb-0" id="cancelled-count">-</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Missing</div>
                            </div>
                            <div class="h1 mb-0" id="total-count">-</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Missing Tools List</h3>
                    <div class="card-actions">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="ti ti-filter me-1"></i>Filter Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item filter-status" href="#" data-status="">All Status</a>
                                </li>
                                <li><a class="dropdown-item filter-status" href="#"
                                        data-status="pending">Pending</a></li>
                                <li><a class="dropdown-item filter-status" href="#"
                                        data-status="completed">Reclaimed</a></li>
                                <li><a class="dropdown-item filter-status" href="#"
                                        data-status="cancelled">Cancelled</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="missing-tools-table" class="table table-vcenter table-hover">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>Item Details</th>
                                    <th>Responsible User</th>
                                    <th width="120">Status</th>
                                    <th width="120">Duration</th>
                                    <th width="150">Reported Date</th>
                                    <th width="150">Action Date</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Missing Tool Detail Modal --}}
    <div class="modal modal-blur fade" id="missing-tool-detail-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Missing Tool Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="missing-tool-detail-content">
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

    {{-- Reclaim Confirmation Modal --}}
    <div class="modal modal-blur fade" id="reclaim-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reclaim Missing Tool</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        Are you sure you want to reclaim this missing tool? This action will:
                        <ul class="mt-2 mb-0">
                            <li>Mark the missing tool as completed</li>
                            <li>Release user responsibility</li>
                            <li>Update user's available coins</li>
                        </ul>
                    </div>
                    <p class="mb-0">Missing Tool: <strong id="reclaim-item-name"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirm-reclaim-btn">
                        <i class="ti ti-check me-1"></i>Confirm Reclaim
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Missing Tool Confirmation Modal --}}
    <div class="modal modal-blur fade" id="cancel-missing-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Missing Tool Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Are you sure you want to cancel this missing tool report? This action will:
                        <ul class="mt-2 mb-0">
                            <li>Mark the missing tool report as cancelled</li>
                            <li>Restore the item to its previous status (borrowed/available)</li>
                            <li>Update user's available coins</li>
                        </ul>
                    </div>
                    <p class="mb-0">Missing Tool: <strong id="cancel-item-name"></strong></p>
                    <p class="text-muted small mb-0">Use this if the item was found or the report was made in error.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" id="confirm-cancel-btn">
                        <i class="ti ti-x me-1"></i>Cancel Missing Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .dropdown-menu-actions {
                min-width: 140px;
            }

            .dropdown-item:hover {
                background-color: var(--tblr-hover-bg);
            }

            .dropdown-item.text-success:hover {
                background-color: var(--tblr-green-lt);
                color: var(--tblr-green) !important;
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
            #missing-tools-table thead th:last-child,
            #missing-tools-table tbody td:last-child {
                text-align: center !important;
                vertical-align: middle !important;
            }

            /* Force center alignment untuk DataTables */
            .dataTables_wrapper .text-center {
                text-align: center !important;
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            $(document).ready(function() {
                const csrf = "{{ csrf_token() }}";
                let currentFilter = '';

                // Initialize DataTable
                const missingToolsTable = $('#missing-tools-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('superadmin.missing-tools.data') }}",
                        data: function(d) {
                            d.status = currentFilter;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'item_details',
                            name: 'nama_barang',
                            render: function(data, type, row) {
                                return `
                                    <div>
                                        <strong>${row.nama_barang}</strong>
                                        <div class="text-muted small">EPC: ${row.epc}</div>
                                    </div>
                                `;
                            }
                        },
                        {
                            data: 'user_details',
                            name: 'user.name',
                            render: function(data, type, row) {
                                if (row.user_info && row.user_info.nama && row.user_info.nama !==
                                    'Unknown User') {
                                    return `
                                        <div>
                                            <strong>${row.user_info.nama}</strong>
                                            <div class="text-muted small">NIM: ${row.user_info.nim || 'N/A'}</div>
                                        </div>
                                    `;
                                } else {
                                    return '<span class="text-muted">Unknown User</span>';
                                }
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'bg-secondary';
                                let statusText = 'Unknown';

                                switch (row.status) {
                                    case 'pending':
                                        badgeClass = 'bg-danger';
                                        statusText = 'Pending';
                                        break;
                                    case 'completed':
                                        badgeClass = 'bg-success';
                                        statusText = 'Reclaimed';
                                        break;
                                    case 'cancelled':
                                        badgeClass = 'bg-warning';
                                        statusText = 'Cancelled';
                                        break;
                                }

                                return `<span class="badge ${badgeClass}">${statusText}</span>`;
                            }
                        },
                        {
                            data: 'duration_text',
                            name: 'reported_at',
                            orderable: false
                        },
                        {
                            data: 'reported_at_formatted',
                            name: 'reported_at'
                        },
                        {
                            data: 'reclaimed_at_formatted',
                            name: 'reclaimed_at',
                            render: function(data, type, row) {
                                if (data) {
                                    let label = row.status === 'cancelled' ? 'Cancelled' : 'Reclaimed';
                                    return `<span class="text-muted small">${label}:</span><br>${data}`;
                                }
                                return '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            data: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center align-middle',
                            width: '80px',
                            render: function(data, type, row) {
                                let actions = `
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="dropdown">
                                            <button class="btn btn-actions" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions">
                                                <li>
                                                    <a class="dropdown-item btn-detail" href="#" data-id="${row.id}">
                                                        <i class="ti ti-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                `;

                                if (row.status === 'pending') {
                                    actions += `
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item btn-reclaim text-success" href="#" 
                                                       data-id="${row.id}" data-name="${row.nama_barang}">
                                                        <i class="ti ti-check me-2"></i>Reclaim Item
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item btn-cancel-missing text-warning" href="#" 
                                                       data-id="${row.id}" data-name="${row.nama_barang}">
                                                        <i class="ti ti-x me-2"></i>Cancel Report
                                                    </a>
                                                </li>
                                    `;
                                }

                                actions += `
                                            </ul>
                                        </div>
                                    </div>
                                `;

                                return actions;
                            }
                        }
                    ],
                    order: [
                        [5, 'desc']
                    ],
                    pageLength: 25,
                    responsive: true,
                    drawCallback: function(settings) {
                        updateStats(settings.json);
                    }
                });

                // Filter functionality
                $('.filter-status').on('click', function(e) {
                    e.preventDefault();
                    currentFilter = $(this).data('status');
                    missingToolsTable.ajax.reload();
                });

                // Refresh button
                $('#refresh-btn').on('click', function() {
                    missingToolsTable.ajax.reload();
                    showNotification('success', 'Data refreshed successfully!');
                });

                // View details
                $(document).on('click', '.btn-detail', function() {
                    const missingToolId = $(this).data('id');
                    showMissingToolDetail(missingToolId);
                });

                // Reclaim missing tool
                $(document).on('click', '.btn-reclaim', function() {
                    const missingToolId = $(this).data('id');
                    const itemName = $(this).data('name');
                    showReclaimModal(missingToolId, itemName);
                });

                // Cancel missing tool
                $(document).on('click', '.btn-cancel-missing', function() {
                    const missingToolId = $(this).data('id');
                    const itemName = $(this).data('name');
                    showCancelMissingModal(missingToolId, itemName);
                });

                // Confirm reclaim
                $('#confirm-reclaim-btn').on('click', function() {
                    const missingToolId = $(this).data('missing-tool-id');
                    reclaimMissingTool(missingToolId);
                });

                // Confirm cancel missing
                $('#confirm-cancel-btn').on('click', function() {
                    const missingToolId = $(this).data('missing-tool-id');
                    cancelMissingTool(missingToolId);
                });

                // Functions
                function updateStats(data) {
                    if (data && data.stats) {
                        $('#pending-count').text(data.stats.pending_count || 0);
                        $('#completed-count').text(data.stats.completed_count || 0);
                        $('#cancelled-count').text(data.stats.cancelled_count || 0);
                        $('#total-count').text(data.stats.total_count || 0);
                    }
                }

                function showMissingToolDetail(missingToolId) {
                    const $modal = $('#missing-tool-detail-modal');
                    const $content = $('#missing-tool-detail-content');

                    $modal.modal('show');
                    $content.html(`
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <div class="mt-2 text-muted">Loading...</div>
                        </div>
                    `);

                    $.ajax({
                        url: `/superadmin/missing-tools/${missingToolId}`,
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        success: function(response) {
                            if (response.success) {
                                $content.html(generateDetailHTML(response.data));
                            }
                        },
                        error: function(xhr) {
                            $content.html(`
                                <div class="alert alert-danger mb-0">
                                    <i class="ti ti-alert-circle me-2"></i>
                                    Failed to load missing tool details. Please try again.
                                </div>
                            `);
                        }
                    });
                }

                function generateDetailHTML(data) {
                    const reportedDate = new Date(data.reported_at).toLocaleString();
                    const actionDate = data.reclaimed_at ? new Date(data.reclaimed_at).toLocaleString() :
                        'Not processed yet';
                    let actionLabel = 'Action Date';

                    if (data.status === 'completed') {
                        actionLabel = 'Reclaimed Date';
                    } else if (data.status === 'cancelled') {
                        actionLabel = 'Cancelled Date';
                    }

                    let statusBadge = 'bg-secondary';
                    let statusText = 'Unknown';

                    switch (data.status) {
                        case 'pending':
                            statusBadge = 'bg-danger';
                            statusText = 'Pending';
                            break;
                        case 'completed':
                            statusBadge = 'bg-success';
                            statusText = 'Reclaimed';
                            break;
                        case 'cancelled':
                            statusBadge = 'bg-warning';
                            statusText = 'Cancelled';
                            break;
                    }

                    return `
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Item Information</h5>
                                <table class="table table-sm">
                                    <tr><td><strong>Item Name:</strong></td><td>${data.nama_barang}</td></tr>
                                    <tr><td><strong>EPC Code:</strong></td><td>${data.epc}</td></tr>
                                    <tr><td><strong>Status:</strong></td><td>
                                        <span class="badge ${statusBadge}">${statusText}</span>
                                    </td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>User Information</h5>
                                <table class="table table-sm">
                                    <tr><td><strong>Name:</strong></td><td>${data.user_detail?.nama || 'N/A'}</td></tr>
                                    <tr><td><strong>NIM:</strong></td><td>${data.user_detail?.nim || 'N/A'}</td></tr>
                                    <tr><td><strong>Program:</strong></td><td>${data.user_detail?.prodi || 'N/A'}</td></tr>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Timeline</h5>
                                <table class="table table-sm">
                                    <tr><td><strong>Reported:</strong></td><td>${reportedDate}</td></tr>
                                    <tr><td><strong>${actionLabel}:</strong></td><td>${actionDate}</td></tr>
                                    <tr><td><strong>Duration:</strong></td><td>${data.duration_text}</td></tr>
                                </table>
                            </div>
                        </div>
                    `;
                }

                function showReclaimModal(missingToolId, itemName) {
                    $('#reclaim-item-name').text(itemName);
                    $('#confirm-reclaim-btn').data('missing-tool-id', missingToolId);
                    $('#reclaim-modal').modal('show');
                }

                function showCancelMissingModal(missingToolId, itemName) {
                    $('#cancel-item-name').text(itemName);
                    $('#confirm-cancel-btn').data('missing-tool-id', missingToolId);
                    $('#cancel-missing-modal').modal('show');
                }

                function reclaimMissingTool(missingToolId) {
                    const $btn = $('#confirm-reclaim-btn');
                    const originalText = $btn.html();

                    $.ajax({
                        url: `/superadmin/missing-tools/${missingToolId}/reclaim`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        beforeSend: function() {
                            $btn.prop('disabled', true).html('<i class="ti ti-loader"></i> Processing...');
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#reclaim-modal').modal('hide');
                                missingToolsTable.ajax.reload();
                                showNotification('success', response.message);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showNotification('error', response?.message ||
                                'Failed to reclaim missing tool');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(originalText);
                        }
                    });
                }

                function cancelMissingTool(missingToolId) {
                    const $btn = $('#confirm-cancel-btn');
                    const originalText = $btn.html();

                    $.ajax({
                        url: `/superadmin/missing-tools/${missingToolId}/cancel`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        beforeSend: function() {
                            $btn.prop('disabled', true).html('<i class="ti ti-loader"></i> Processing...');
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#cancel-missing-modal').modal('hide');
                                missingToolsTable.ajax.reload();
                                showNotification('success', response.message);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showNotification('error', response?.message ||
                                'Failed to cancel missing tool');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(originalText);
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
            });
        </script>
    @endpush
</x-layouts.superadmin_layout>
