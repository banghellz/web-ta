<!-- resources/views/superadmin/items/index.blade.php -->
<x-layouts.superadmin_layout>
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
                                Refresh
                            </button>
                        </span>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#addItemModal">
                            <i class="ti ti-plus me-1"></i>
                            Add New Item
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
                    <h3 class="card-title">Inventory Statistics</h3>
                    <div class="card-actions">
                        <small class="text-muted" id="last-updated">
                            Last updated: <span id="last-updated-time">Just now</span>
                        </small>
                    </div>
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
                        <!-- Borrowed Items -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-yellow text-white avatar">
                                                <i class="ti ti-user"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span
                                                    id="borrowed-items">{{ number_format($borrowedItems ?? 0) }}</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                Borrowed
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Missing Items -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-dark text-white avatar">
                                                <i class="ti ti-alert-triangle"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="missing-items">{{ number_format($missingItems ?? 0) }}</span>
                                                Items
                                            </div>
                                            <div class="text-muted">
                                                Missing
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
                                        <option value="borrowed">Borrowed</option>
                                        <option value="missing">Missing</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Status Refresh Indicator -->
                                <div id="status-refresh-indicator" class="d-flex align-items-center me-3">
                                    <div id="status-refresh-dot" class="bg-info rounded-circle me-2"
                                        style="width: 6px; height: 6px;" title="Status Auto-Refresh Active"></div>
                                    <small class="text-muted">Auto-Refresh: <span
                                            id="status-refresh-text">Active</span></small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Connection Status Indicator -->
                                <div id="connection-status" class="d-flex align-items-center">
                                    <div id="connection-indicator" class="bg-success rounded-circle me-2"
                                        style="width: 8px; height: 8px;" title="Connected"></div>
                                    <small class="text-muted">Connected</small>
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
                                    <th>Status</th>
                                    <th>Created At</th>
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

    <!-- Existing Modals (item detail, delete, mark missing) -->
    <div class="modal modal-blur fade" id="modal-item-detail" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="item-detail-content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-delete-item" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="ti ti-alert-triangle text-danger" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Are you sure?</h3>
                        <p>Are you sure you want to delete the item <span id="item-to-delete" class="fw-bold"></span>?
                        </p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger ms-auto" id="btn-confirm-delete">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-mark-missing" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <p>Are you sure you want to mark the item <span id="item-to-mark-missing"
                                class="fw-bold"></span> as missing?</p>
                        <p class="text-warning">This will remove the item from the borrower's responsibility and mark
                            it as missing in the system.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning ms-auto" id="btn-confirm-mark-missing">
                        <i class="ti ti-alert-triangle me-1"></i>Mark as Missing
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Create Modal --}}
    @include('superadmin.items.create-modal')

    @push('styles')
        <style>
            /* ==== IMPROVED STYLES - NO DARK TABLE OVERLAY ==== */

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
        </style>
    @endpush


    @push('scripts')
        <script>
            $(function() {
                const csrfToken = "{{ csrf_token() }}";
                let itemToDelete = null;
                let itemToMarkMissing = null;

                // DATABASE-BASED: Real-time polling configuration
                let pollingInterval = null;
                let isPollingEnabled = true;
                let clientLastUpdate = null; // Track database timestamp, bukan cache
                let pollingFailureCount = 0;
                const maxPollingFailures = 3;

                // NEW: Status Auto-Refresh Configuration
                let statusRefreshInterval = null;
                let isStatusRefreshEnabled = true;
                let statusRefreshFailureCount = 0;
                const maxStatusRefreshFailures = 3;

                let currentStats = {
                    total_items: {{ $totalItems ?? 0 }},
                    available_items: {{ $availableItems ?? 0 }},
                    borrowed_items: {{ $borrowedItems ?? 0 }},
                    missing_items: {{ $missingItems ?? 0 }}
                };

                // Toast function - NO auto-update toasts
                function showItemsToast(message, type = 'success', skipAutoUpdate = false) {
                    // Skip toast for auto-updates to prevent spam
                    if (skipAutoUpdate) {
                        console.log('Auto-update (silent):', message);
                        return;
                    }

                    console.log('Manual action toast:', {
                        type,
                        message
                    });

                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.show(type, message);
                    } else if (typeof window.showNotificationToast === 'function') {
                        window.showNotificationToast(message, type);
                    } else if (typeof showToast === 'function') {
                        showToast(message, type);
                    } else {
                        const toast = $('#notificationToast');
                        const toastIcon = $('#toastIcon');
                        const toastMessage = $('#toastMessage');

                        if (toast.length && toastIcon.length && toastMessage.length) {
                            const icons = {
                                'success': 'ti-check',
                                'error': 'ti-alert-circle',
                                'warning': 'ti-alert-triangle',
                                'info': 'ti-info-circle'
                            };

                            toastIcon.attr('class', `ti ${icons[type] || icons.success} me-2`);
                            toastMessage.text(message);

                            toast.removeClass('text-success text-danger text-warning text-info');
                            if (type !== 'info') {
                                toast.addClass(`text-${type === 'error' ? 'danger' : type}`);
                            }

                            const bsToast = new bootstrap.Toast(toast[0], {
                                autohide: true,
                                delay: 4000
                            });
                            bsToast.show();
                        } else {
                            alert(`${type.toUpperCase()}: ${message}`);
                        }
                    }
                }

                // Function to refresh notifications if available
                function refreshNotifications() {
                    if (typeof window.refreshNotifications === 'function') {
                        window.refreshNotifications();
                    }
                }

                // Initialize DataTable
                const table = $('#itemsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('superadmin.items.data') }}",
                        type: 'GET',
                        dataSrc: function(json) {
                            console.log('DataTable loaded successfully');

                            // Update stats and UI
                            updateStats(json.stats || {});
                            updateLastRefreshTime();
                            setConnectionStatus('connected');

                            // IMPORTANT: Initialize clientLastUpdate dengan database timestamp
                            if (!clientLastUpdate && json.last_db_update) {
                                clientLastUpdate = json.last_db_update;
                                console.log('Initialized clientLastUpdate with DB timestamp:',
                                    clientLastUpdate);
                            }

                            // Reset failure count
                            pollingFailureCount = 0;

                            return json.data;
                        },
                        error: function(xhr, error, code) {
                            console.error('DataTable Ajax Error:', error, xhr);
                            setConnectionStatus('disconnected');
                            showItemsToast('Failed to load data. Connection error.', 'error');
                            pollingFailureCount++;
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
                                return '<div class="text-wrap">' +
                                    '<a href="#" class="item-name-clickable" data-item-id="' + row.id +
                                    '">' +
                                    data +
                                    '</a>' +
                                    '</div>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center',
                            render: function(data, type, row) {
                                let iconClass = '';
                                let badgeClass = '';
                                let statusText = '';

                                if (data === 'available') {
                                    iconClass = 'ti-check';
                                    badgeClass = 'bg-success';
                                    statusText = 'Available';
                                } else if (data === 'borrowed') {
                                    iconClass = 'ti-user';
                                    badgeClass = 'bg-warning';
                                    statusText = 'Borrowed';
                                } else if (data === 'missing') {
                                    iconClass = 'ti-alert-triangle';
                                    badgeClass = 'bg-dark';
                                    statusText = 'Missing';
                                } else if (data === 'out_of_stock') {
                                    iconClass = 'ti-x';
                                    badgeClass = 'bg-danger';
                                    statusText = 'Out of Stock';
                                } else {
                                    iconClass = 'ti-help';
                                    badgeClass = 'bg-secondary';
                                    statusText = 'Unknown';
                                }

                                return '<span class="badge ' + badgeClass + '" data-item-id="' + row
                                    .id + '" data-status="' + data + '"><i class="ti ' +
                                    iconClass + ' me-1"></i>' + statusText + '</span>';
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
                            data: null,
                            name: 'actions',
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
                                <a class="dropdown-item" href="/superadmin/items/${row.id}/edit">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                            </li>`;

                                if (row.status === 'borrowed') {
                                    actions += `
                        <li>
                            <a class="dropdown-item text-warning mark-missing" href="#" 
                               data-item-id="${row.id}" 
                               data-item-name="${row.nama_barang}">
                                <i class="ti ti-alert-triangle me-2"></i>Mark as Missing
                            </a>
                        </li>`;
                                }

                                actions += `
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger delete-item" href="#" 
                               data-item-id="${row.id}" 
                               data-item-name="${row.nama_barang}"
                               data-item-status="${row.status}">
                                <i class="ti ti-trash me-2"></i>Delete
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        `;

                                return actions;
                            }
                        }
                    ],
                    order: [
                        [4, 'desc']
                    ],
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

                window.itemsDataTable = table;

                // ==== NEW: STATUS AUTO-REFRESH SYSTEM ====

                function startStatusAutoRefresh() {
                    if (statusRefreshInterval) {
                        clearInterval(statusRefreshInterval);
                    }

                    console.log('Starting STATUS AUTO-REFRESH every 5 seconds...');
                    setStatusRefreshIndicator('active');

                    statusRefreshInterval = setInterval(function() {
                        if (!isStatusRefreshEnabled || document.hidden || statusRefreshFailureCount >=
                            maxStatusRefreshFailures) {
                            if (statusRefreshFailureCount >= maxStatusRefreshFailures) {
                                console.warn('Status auto-refresh disabled due to failures');
                                setStatusRefreshIndicator('paused');
                            }
                            return;
                        }

                        refreshStatusOnly();
                    }, 3000); // Every 5 seconds
                }

                function refreshStatusOnly() {
                    console.log('Refreshing status fields only...');
                    setStatusRefreshIndicator('refreshing');

                    $.ajax({
                        url: "{{ route('superadmin.items.data') }}",
                        type: 'GET',
                        data: {
                            _: new Date().getTime(), // Prevent caching
                            status_only: true // Optional parameter to indicate status-only refresh
                        },
                        timeout: 10000,
                        success: function(json) {
                            if (json.data && Array.isArray(json.data)) {
                                console.log(`Updating status for ${json.data.length} items...`);

                                let updatedCount = 0;

                                // Update status badges in the table
                                json.data.forEach(function(item) {
                                    const $statusBadge = $(`.badge[data-item-id="${item.id}"]`);
                                    if ($statusBadge.length > 0) {
                                        const currentStatus = $statusBadge.data('status');

                                        if (currentStatus !== item.status) {
                                            updateStatusBadge($statusBadge, item.status);
                                            updatedCount++;
                                        }
                                    }
                                });

                                // Update stats if provided
                                if (json.stats) {
                                    updateStats(json.stats);
                                }

                                if (updatedCount > 0) {
                                    console.log(`Status updated for ${updatedCount} items`);
                                }

                                statusRefreshFailureCount = 0;
                                setStatusRefreshIndicator('active');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.warn('Status refresh failed:', {
                                status: xhr.status,
                                error: error
                            });

                            statusRefreshFailureCount++;

                            if (statusRefreshFailureCount >= maxStatusRefreshFailures) {
                                setStatusRefreshIndicator('paused');
                                console.warn('Status auto-refresh paused due to failures');
                            } else {
                                setStatusRefreshIndicator('active');
                            }
                        }
                    });
                }

                function updateStatusBadge($badge, newStatus) {
                    let iconClass = '';
                    let badgeClass = '';
                    let statusText = '';

                    // Remove old classes
                    $badge.removeClass('bg-success bg-warning bg-dark bg-danger bg-secondary');

                    if (newStatus === 'available') {
                        iconClass = 'ti-check';
                        badgeClass = 'bg-success';
                        statusText = 'Available';
                    } else if (newStatus === 'borrowed') {
                        iconClass = 'ti-user';
                        badgeClass = 'bg-warning';
                        statusText = 'Borrowed';
                    } else if (newStatus === 'missing') {
                        iconClass = 'ti-alert-triangle';
                        badgeClass = 'bg-dark';
                        statusText = 'Missing';
                    } else if (newStatus === 'out_of_stock') {
                        iconClass = 'ti-x';
                        badgeClass = 'bg-danger';
                        statusText = 'Out of Stock';
                    } else {
                        iconClass = 'ti-help';
                        badgeClass = 'bg-secondary';
                        statusText = 'Unknown';
                    }

                    // Add animation class
                    $badge.addClass('status-updating');

                    // Update badge content and classes
                    $badge.addClass(badgeClass)
                        .attr('data-status', newStatus)
                        .html(`<i class="ti ${iconClass} me-1"></i>${statusText}`);

                    // Remove animation class after animation completes
                    setTimeout(() => {
                        $badge.removeClass('status-updating');
                    }, 800);
                }

                function setStatusRefreshIndicator(status) {
                    const $indicator = $('#status-refresh-dot');
                    const $container = $('#status-refresh-indicator');
                    const $text = $('#status-refresh-text');

                    $container.removeClass('active refreshing paused').addClass(status);

                    switch (status) {
                        case 'active':
                            $indicator.attr('title', 'Status auto-refresh active - Updates every 5 seconds');
                            $text.text('Active');
                            break;
                        case 'refreshing':
                            $indicator.attr('title', 'Refreshing status data...');
                            $text.text('Refreshing...');
                            break;
                        case 'paused':
                            $indicator.attr('title', 'Status auto-refresh paused');
                            $text.text('Paused');
                            break;
                    }
                }

                function stopStatusAutoRefresh() {
                    if (statusRefreshInterval) {
                        clearInterval(statusRefreshInterval);
                        statusRefreshInterval = null;
                    }
                    setStatusRefreshIndicator('paused');
                    console.log('Status auto-refresh stopped');
                }

                // ==== DATABASE-BASED REAL-TIME POLLING ====

                function startRealTimePolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                    }

                    console.log('Starting DATABASE-BASED real-time polling...');

                    pollingInterval = setInterval(function() {
                        if (!isPollingEnabled || document.hidden || pollingFailureCount >= maxPollingFailures) {
                            if (pollingFailureCount >= maxPollingFailures) {
                                console.warn('Polling disabled due to failures');
                                setConnectionStatus('disconnected');
                            }
                            return;
                        }

                        checkForDatabaseUpdates();
                    }, 5000); // Check every 5 seconds
                }

                function checkForDatabaseUpdates() {
                    console.log('Checking for DATABASE updates...', {
                        clientLastUpdate: clientLastUpdate,
                        method: 'database_timestamp_detection'
                    });

                    setConnectionStatus('connecting');

                    $.ajax({
                        url: "{{ route('superadmin.items.check-updates') }}",
                        type: 'GET',
                        data: {
                            last_update: clientLastUpdate
                        },
                        timeout: 15000, // Longer timeout untuk database query
                        success: function(response) {
                            console.log('Database update check response:', response);

                            setConnectionStatus('connected');
                            pollingFailureCount = 0;

                            if (response.has_updates) {
                                console.log('DATABASE CHANGES DETECTED!', response.updates);
                                console.log('Detection method:', response.debug?.detection_method);

                                // SILENT refresh - no toast, no dark overlay
                                performSilentRefresh();

                                // Update stats if provided
                                if (response.stats) {
                                    updateStats(response.stats);
                                }

                                // Show context info di console untuk debugging
                                if (response.updates && response.updates.length > 0) {
                                    console.log('Changes detected:', response.updates.map(u =>
                                        `${u.name} (${u.action})`).join(', '));
                                }
                            } else {
                                console.log('No database changes detected');
                            }

                            // IMPORTANT: Always update client timestamp dengan latest database timestamp
                            if (response.latest_db_update) {
                                clientLastUpdate = response.latest_db_update;
                                console.log('Updated clientLastUpdate with latest DB timestamp:',
                                    clientLastUpdate);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.warn('Database polling check failed:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                error: error
                            });

                            pollingFailureCount++;

                            if (pollingFailureCount >= maxPollingFailures) {
                                setConnectionStatus('disconnected');
                                showItemsToast('Connection lost. Real-time updates disabled.', 'warning');
                            } else {
                                setConnectionStatus('disconnected');
                            }
                        }
                    });
                }

                // SILENT refresh without any visual interference
                function performSilentRefresh() {
                    console.log('Performing SILENT table refresh due to database changes...');

                    // NO overlays, NO toasts, NO visual indicators
                    table.ajax.reload(function(json) {
                        updateLastRefreshTime();

                        // Update client timestamp dengan database timestamp terbaru
                        if (json.last_db_update) {
                            clientLastUpdate = json.last_db_update;
                            console.log('Client timestamp updated after silent refresh:', clientLastUpdate);
                        }

                        console.log('Silent refresh completed');
                    }, false); // Don't reset paging
                }

                // Manual refresh with user feedback
                function performManualRefresh() {
                    const $refreshBtn = $('#reload-items');

                    $refreshBtn.addClass('refreshing');
                    setConnectionStatus('connecting');

                    table.ajax.reload(function(json) {
                        $refreshBtn.removeClass('refreshing');
                        showItemsToast('Data refreshed successfully!', 'success'); // User feedback
                        updateLastRefreshTime();

                        // Update client timestamp
                        if (json.last_db_update) {
                            clientLastUpdate = json.last_db_update;
                            console.log('Client timestamp updated after manual refresh:', clientLastUpdate);
                        }

                        console.log('Manual refresh completed');
                    }, false);
                }

                function stopRealTimePolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                    }
                    console.log('Database-based polling stopped');
                }

                // ==== PAGE VISIBILITY HANDLING ====

                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        isPollingEnabled = false;
                        isStatusRefreshEnabled = false;
                        setStatusRefreshIndicator('paused');
                        console.log('Page hidden - database polling and status refresh paused');
                    } else {
                        isPollingEnabled = true;
                        isStatusRefreshEnabled = true;
                        pollingFailureCount = 0;
                        statusRefreshFailureCount = 0;
                        setConnectionStatus('connecting');
                        setStatusRefreshIndicator('active');
                        console.log('Page visible - resuming database polling and status refresh');

                        // Check immediately when page becomes visible
                        setTimeout(function() {
                            checkForDatabaseUpdates();
                            refreshStatusOnly();
                        }, 500);
                    }
                });

                // ==== CONNECTION STATUS & UI UPDATES ====

                function setConnectionStatus(status) {
                    const $indicator = $('#connection-indicator');
                    const $statusContainer = $('#connection-status');

                    $statusContainer.removeClass('connected connecting disconnected').addClass(status);

                    switch (status) {
                        case 'connected':
                            $indicator.attr('title', 'Connected - Database monitoring active');
                            $statusContainer.find('small').text('Live');
                            break;
                        case 'connecting':
                            $indicator.attr('title', 'Checking database for updates...');
                            $statusContainer.find('small').text('Checking...');
                            break;
                        case 'disconnected':
                            $indicator.attr('title', 'Offline - Database monitoring paused');
                            $statusContainer.find('small').text('Offline');
                            break;
                    }
                }

                function updateLastRefreshTime() {
                    const now = new Date();
                    $('#last-updated-time').text(now.toLocaleTimeString());
                }

                function updateStats(stats) {
                    function animateStatUpdate(element, newValue) {
                        const $element = $(element);
                        const currentValue = parseInt($element.text().replace(/,/g, ''));

                        if (currentValue !== newValue) {
                            $element.addClass('stat-updating');
                            $element.text(newValue.toLocaleString());
                            setTimeout(() => $element.removeClass('stat-updating'), 500);
                        }
                    }

                    if (stats.total_items !== undefined && stats.total_items !== currentStats.total_items) {
                        animateStatUpdate('#total-items', stats.total_items);
                        currentStats.total_items = stats.total_items;
                    }
                    if (stats.available_items !== undefined && stats.available_items !== currentStats.available_items) {
                        animateStatUpdate('#available-items', stats.available_items);
                        currentStats.available_items = stats.available_items;
                    }
                    if (stats.borrowed_items !== undefined && stats.borrowed_items !== currentStats.borrowed_items) {
                        animateStatUpdate('#borrowed-items', stats.borrowed_items);
                        currentStats.borrowed_items = stats.borrowed_items;
                    }
                    if (stats.missing_items !== undefined && stats.missing_items !== currentStats.missing_items) {
                        animateStatUpdate('#missing-items', stats.missing_items);
                        currentStats.missing_items = stats.missing_items;
                    }
                }

                // Force immediate update after user actions (for current user)
                function triggerImmediateUpdate() {
                    console.log('Triggering immediate update for current user...');

                    // Perform silent refresh for immediate feedback
                    performSilentRefresh();

                    // Also refresh status immediately
                    setTimeout(() => {
                        refreshStatusOnly();
                    }, 1000);
                }

                // ==== EVENT HANDLERS ====

                $('#reload-items').on('click', function() {
                    performManualRefresh();
                });

                $('#status-filter').on('change', function() {
                    const selectedStatus = $(this).val();
                    table.column(3).search(selectedStatus).draw();
                });

                // Item detail modal
                $(document).on('click', '.item-name-clickable', function(e) {
                    e.preventDefault();
                    const itemId = $(this).data('item-id');

                    $('#item-detail-content').html(`
            <div class="modal-loading">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading item details...</span>
            </div>
        `);

                    $('#modal-item-detail').modal('show');

                    $.ajax({
                        url: `/superadmin/items/${itemId}`,
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                $('#item-detail-content').html(response.html);
                            } else {
                                $('#item-detail-content').html(`
                        <div class="alert alert-danger">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Failed to load item details. Please try again.
                        </div>
                    `);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading item details:', error);
                            $('#item-detail-content').html(`
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Error loading item details. Please try again.
                    </div>
                `);
                        }
                    });
                });

                // Delete item
                $(document).on('click', '.delete-item', function(e) {
                    e.preventDefault();
                    const itemId = $(this).data('item-id');
                    const itemName = $(this).data('item-name');
                    const itemStatus = $(this).data('item-status');

                    if (itemStatus === 'borrowed' || itemStatus === 'missing') {
                        const statusText = itemStatus === 'borrowed' ? 'borrowed' : 'missing';
                        showItemsToast(`Cannot delete item that is ${statusText}.`, 'error');
                        return;
                    }

                    itemToDelete = {
                        id: itemId,
                        name: itemName
                    };
                    $('#item-to-delete').text(itemName);
                    $('#modal-delete-item').modal('show');
                });

                // Mark as missing
                $(document).on('click', '.mark-missing', function(e) {
                    e.preventDefault();
                    const itemId = $(this).data('item-id');
                    const itemName = $(this).data('item-name');

                    itemToMarkMissing = {
                        id: itemId,
                        name: itemName
                    };
                    $('#item-to-mark-missing').text(itemName);
                    $('#modal-mark-missing').modal('show');
                });

                // Confirm delete
                $('#btn-confirm-delete').on('click', function() {
                    if (!itemToDelete) return;

                    const $btn = $(this);
                    const originalText = $btn.html();
                    $btn.prop('disabled', true).html('<i class="ti ti-loader-2 me-1 spinning"></i>Deleting...');

                    $.ajax({
                        url: `/superadmin/items/${itemToDelete.id}`,
                        type: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                showItemsToast(response.message || 'Item deleted successfully!',
                                    'success');
                                refreshNotifications();

                                // Trigger immediate update for current user
                                triggerImmediateUpdate();
                            } else {
                                showItemsToast(response.message || 'Failed to delete item.',
                                    'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'Failed to delete item. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            showItemsToast(errorMessage, 'error');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(originalText);
                            $('#modal-delete-item').modal('hide');
                        }
                    });
                });

                // Confirm mark missing
                $('#btn-confirm-mark-missing').on('click', function() {
                    if (!itemToMarkMissing) return;

                    const $btn = $(this);
                    const originalText = $btn.html();
                    $btn.prop('disabled', true).html(
                        '<i class="ti ti-loader-2 me-1 spinning"></i>Processing...');

                    $.ajax({
                        url: `/superadmin/missing-tools/mark-missing/${itemToMarkMissing.id}`,
                        type: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                showItemsToast(response.message ||
                                    'Item marked as missing successfully!', 'warning');
                                refreshNotifications();

                                // Trigger immediate update for current user
                                triggerImmediateUpdate();
                            } else {
                                showItemsToast(response.message ||
                                    'Failed to mark item as missing.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error marking item as missing:', error);
                            showItemsToast('Failed to mark item as missing. Please try again.',
                                'error');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(originalText);
                            $('#modal-mark-missing').modal('hide');
                        }
                    });
                });

                // Modal cleanup
                $('#modal-delete-item').on('hidden.bs.modal', function() {
                    itemToDelete = null;
                });

                $('#modal-mark-missing').on('hidden.bs.modal', function() {
                    itemToMarkMissing = null;
                });

                // Handle successful item creation from modal
                $(document).on('itemAdded', function(event, data) {
                    console.log('Item added event received:', data);

                    // Trigger immediate update for current user
                    triggerImmediateUpdate();
                });

                // Show Laravel session messages as toast
                @if (session('success'))
                    showItemsToast("{{ session('success') }}", 'success');
                @endif

                @if (session('error'))
                    showItemsToast("{{ session('error') }}", 'error');
                @endif

                @if (session('warning'))
                    showItemsToast("{{ session('warning') }}", 'warning');
                @endif

                @if (session('info'))
                    showItemsToast("{{ session('info') }}", 'info');
                @endif

                // ==== INITIALIZATION ====

                updateLastRefreshTime();
                setConnectionStatus('connected');
                setStatusRefreshIndicator('active');
                // clientLastUpdate akan di-set otomatis dari response DataTables

                // Start database-based real-time polling
                startRealTimePolling();

                // Start status auto-refresh
                startStatusAutoRefresh();

                // Cleanup on page unload
                window.addEventListener('beforeunload', function() {
                    stopRealTimePolling();
                    stopStatusAutoRefresh();
                    isPollingEnabled = false;
                    isStatusRefreshEnabled = false;
                });

                // Connection recovery
                setInterval(function() {
                    if (pollingFailureCount >= maxPollingFailures && isPollingEnabled && !document.hidden) {
                        console.log('Attempting to recover database connection...');
                        pollingFailureCount = 0;
                        checkForDatabaseUpdates();
                    }

                    if (statusRefreshFailureCount >= maxStatusRefreshFailures && isStatusRefreshEnabled && !
                        document.hidden) {
                        console.log('Attempting to recover status refresh...');
                        statusRefreshFailureCount = 0;
                        refreshStatusOnly();
                    }
                }, 30000);

                console.log('DATABASE-BASED Items real-time system with STATUS AUTO-REFRESH initialized');
            });

            // ==== GLOBAL FUNCTIONS ====

            function refreshTable() {
                if ($('#itemsTable').DataTable()) {
                    $('#itemsTable').DataTable().ajax.reload(null, false);
                }
            }

            window.refreshItemsTable = function(silent = true) {
                if (silent) {
                    if ($('#itemsTable').DataTable()) {
                        $('#itemsTable').DataTable().ajax.reload(null, false);
                    }
                } else {
                    refreshTable();
                }
            };

            // ==== DEBUG FUNCTIONS ====
            window.debugRealTime = function() {
                console.log('=== REAL-TIME DEBUG INFO ===');
                console.log('Client Last Update:', clientLastUpdate);
                console.log('Polling Enabled:', isPollingEnabled);
                console.log('Status Refresh Enabled:', isStatusRefreshEnabled);
                console.log('Failure Count:', pollingFailureCount);
                console.log('Status Refresh Failure Count:', statusRefreshFailureCount);
                console.log('Detection Method: DATABASE TIMESTAMP + STATUS AUTO-REFRESH');

                // Test check updates manually
                checkForDatabaseUpdates();
                refreshStatusOnly();
            };
        </script>
    @endpush
</x-layouts.superadmin_layout>
