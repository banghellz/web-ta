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
                    <!-- Simplified status indicators -->
                    <div class="card-actions">
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

    <!-- Existing Modals -->
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
                    <h5 class="modal-title">Move Item to Trash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="ti ti-trash text-warning" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Move to Trash?</h3>
                        <p>Are you sure you want to move the item <span id="item-to-delete" class="fw-bold"></span> to
                            trash?</p>
                        <p class="text-muted">You can restore it later from the trash if needed.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning ms-auto" id="btn-confirm-delete">
                        <i class="ti ti-trash me-1"></i>Move to Trash
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
            /* Improved animations */
            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                    transform: scale(1);
                }

                50% {
                    opacity: 0.6;
                    transform: scale(1.1);
                }
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            @keyframes blink {

                0%,
                50% {
                    opacity: 1;
                }

                51%,
                100% {
                    opacity: 0.3;
                }
            }

            /* Optimized stat update animation */
            .stat-updating {
                animation: statUpdate 0.6s ease-in-out;
                transform-origin: center;
            }

            @keyframes statUpdate {
                0% {
                    background-color: transparent;
                    transform: scale(1);
                }

                50% {
                    background-color: var(--tblr-green-lt);
                    transform: scale(1.02);
                }

                100% {
                    background-color: transparent;
                    transform: scale(1);
                }
            }

            /* Status badge update animation */
            .status-updating {
                animation: statusUpdate 0.8s ease-in-out;
                transform-origin: center;
            }

            @keyframes statusUpdate {
                0% {
                    transform: scale(1);
                }

                30% {
                    transform: scale(1.05);
                }

                100% {
                    transform: scale(1);
                }
            }

            /* Refresh button animation */
            .btn.refreshing {
                animation: spin 1s linear infinite;
            }

            /* Performance optimization for animations */
            .status-updating,
            .stat-updating,
            #connection-indicator {
                will-change: transform, opacity;
            }

            /* Dropdown actions styling */
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

            /* Reduced motion for accessibility */
            @media (prefers-reduced-motion: reduce) {

                .status-updating,
                .stat-updating,
                #connection-indicator {
                    animation: none !important;
                }
            }

            /* Mobile responsiveness */
            @media (max-width: 768px) {
                #connection-status small {
                    font-size: 0.7rem;
                }

                .card-actions .row {
                    gap: 0.5rem;
                }

                .card-actions .col-auto {
                    margin-bottom: 0.5rem;
                }
            }

            /* Loading states */
            .modal-loading {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                min-height: 100px;
            }

            /* Toast enhancement for better visibility */
            .toast-container {
                z-index: 1060;
            }

            /* Improved table responsiveness */
            @media (max-width: 576px) {
                .table-responsive {
                    font-size: 0.875rem;
                }

                .btn-actions {
                    width: 28px;
                    height: 28px;
                    font-size: 1rem;
                }

                .badge {
                    font-size: 0.75rem;
                    padding: 0.25rem 0.5rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // === IMPROVED REAL-TIME SYSTEM ===
            $(function() {
                // Ensure jQuery is loaded
                if (typeof $ === 'undefined') {
                    console.error('jQuery is not loaded! Real-time updates will not work.');
                    return;
                }

                const csrfToken = "{{ csrf_token() }}";
                let itemToDelete = null;
                let itemToMarkMissing = null;

                // === ENHANCED REAL-TIME CONFIGURATION ===
                let pollingInterval = null;
                let clientLastUpdate = null;
                let isPollingEnabled = true;
                let pollingFailureCount = 0;
                let lastKnownItemCount = {{ $totalItems ?? 0 }};

                // Optimized intervals
                const POLLING_INTERVAL = 3000; // 3 seconds
                const MAX_FAILURES = 3;
                const RETRY_DELAY = 2000; // 2 seconds

                let currentStats = {
                    total_items: {{ $totalItems ?? 0 }},
                    available_items: {{ $availableItems ?? 0 }},
                    borrowed_items: {{ $borrowedItems ?? 0 }},
                    missing_items: {{ $missingItems ?? 0 }}
                };

                // === ENHANCED AJAX WITH BETTER DEBUGGING ===
                function makeOptimizedRequest(url, options = {}) {
                    const defaultOptions = {
                        timeout: 8000,
                        retries: 1,
                        retryDelay: 1000
                    };

                    const config = {
                        ...defaultOptions,
                        ...options
                    };

                    function attemptRequest(attempt = 1) {
                        return new Promise((resolve, reject) => {
                            console.log(`Making request to: ${url} (attempt ${attempt})`);

                            $.ajax({
                                    url: url,
                                    timeout: config.timeout,
                                    cache: false, // Prevent caching
                                    ...config.ajaxOptions
                                })
                                .done((data, textStatus, jqXHR) => {
                                    console.log('Request successful:', {
                                        url,
                                        status: jqXHR.status,
                                        data
                                    });
                                    resolve(data);
                                })
                                .fail((xhr, status, error) => {
                                    console.warn(`Request failed (attempt ${attempt}):`, {
                                        url,
                                        status: xhr.status,
                                        statusText: xhr.statusText,
                                        error,
                                        responseText: xhr.responseText
                                    });

                                    if (attempt < config.retries && xhr.status >= 500) {
                                        setTimeout(() => {
                                            attemptRequest(attempt + 1).then(resolve).catch(reject);
                                        }, config.retryDelay * attempt);
                                    } else {
                                        reject(xhr);
                                    }
                                });
                        });
                    }

                    return attemptRequest();
                }

                // === ENHANCED POLLING SYSTEM ===
                function startPolling() {
                    if (pollingInterval) clearInterval(pollingInterval);

                    console.log(`🚀 Starting enhanced polling - interval: ${POLLING_INTERVAL}ms`);

                    pollingInterval = setInterval(() => {
                        if (!isPollingEnabled || document.hidden) {
                            console.log('⏸️ Polling skipped - page hidden or disabled');
                            return;
                        }
                        checkForDatabaseUpdates();
                    }, POLLING_INTERVAL);
                }

                function stopPolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                        console.log('⏹️ Polling stopped');
                    }
                }

                function checkForDatabaseUpdates() {
                    console.log('🔍 Checking for database updates...', {
                        clientLastUpdate,
                        isPollingEnabled,
                        pollingFailureCount
                    });

                    makeOptimizedRequest("/superadmin/items/check-updates", {
                            ajaxOptions: {
                                type: 'GET',
                                data: {
                                    last_update: clientLastUpdate,
                                    _: Date.now() // Cache buster
                                }
                            }
                        })
                        .then(response => {
                            console.log('✅ Update check response:', response);

                            pollingFailureCount = 0;

                            // ALWAYS update stats regardless of has_updates
                            if (response.stats) {
                                updateStats(response.stats);
                            }

                            if (response.has_updates) {
                                console.log('🔄 Changes detected - refreshing table');
                                performSilentRefresh();

                                // Show subtle notification for major changes
                                if (response.updates && response.updates.length > 0) {
                                    console.log('📝 Recent changes:', response.updates);
                                    showItemsToast('Data updated in real-time', 'info', true);
                                }
                            } else {
                                console.log('✨ No changes detected');
                            }

                            // Update client timestamp
                            if (response.latest_db_update) {
                                const oldTimestamp = clientLastUpdate;
                                clientLastUpdate = response.latest_db_update;

                                if (oldTimestamp !== clientLastUpdate) {
                                    console.log('⏰ Updated client timestamp:', {
                                        old: oldTimestamp,
                                        new: clientLastUpdate
                                    });
                                }
                            }

                            // Debug info
                            if (response.debug) {
                                console.log('🔧 Debug info:', response.debug);
                            }
                        })
                        .catch(xhr => {
                            pollingFailureCount++;
                            console.error('❌ Update check failed:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                failureCount: pollingFailureCount
                            });

                            if (pollingFailureCount >= MAX_FAILURES) {
                                console.warn('🚨 Max failures reached - temporarily stopping polling');
                                showItemsToast('Connection lost. Auto-refresh disabled temporarily.', 'warning');
                                stopPolling();

                                // Retry after delay
                                setTimeout(() => {
                                    console.log('🔄 Retrying polling after connection failure...');
                                    pollingFailureCount = 0;
                                    startPolling();
                                }, RETRY_DELAY * 3);
                            }
                        });
                }

                // === ENHANCED UTILITY FUNCTIONS ===
                function showItemsToast(message, type = 'success', isAutoUpdate = false) {
                    if (isAutoUpdate) {
                        console.log(`🔔 Auto-update: ${message}`);
                        // Only show toast for significant changes
                        return;
                    }

                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.show(type, message);
                    } else if (typeof window.showNotificationToast === 'function') {
                        window.showNotificationToast(message, type);
                    } else {
                        console.log(`${type.toUpperCase()}: ${message}`);
                    }
                }

                function updateStats(stats) {
                    console.log('📊 Updating stats:', stats);

                    function animateStatUpdate(element, newValue) {
                        const $element = $(element);
                        const currentValue = parseInt($element.text().replace(/,/g, '')) || 0;

                        if (currentValue !== newValue) {
                            console.log(`📈 Stat change: ${element} ${currentValue} → ${newValue}`);

                            $element.addClass('stat-updating');
                            $element.text(newValue.toLocaleString());

                            setTimeout(() => $element.removeClass('stat-updating'), 600);
                            return true;
                        }
                        return false;
                    }

                    let hasChanges = false;
                    const elementMap = {
                        'total_items': '#total-items',
                        'available_items': '#available-items',
                        'borrowed_items': '#borrowed-items',
                        'missing_items': '#missing-items'
                    };

                    Object.keys(stats).forEach(key => {
                        const element = elementMap[key];
                        if (element && stats[key] !== undefined) {
                            if (animateStatUpdate(element, stats[key])) {
                                hasChanges = true;
                                currentStats[key] = stats[key];
                            }
                        }
                    });

                    if (hasChanges) {
                        console.log('📊 Stats updated with changes');
                        updateLastRefreshTime();
                    }
                }

                function updateLastRefreshTime() {
                    const now = new Date();
                    $('#last-updated-time').text(now.toLocaleTimeString());
                }

                function performSilentRefresh() {
                    console.log('🔄 Performing silent table refresh...');

                    if (!table || !table.ajax) {
                        console.error('❌ DataTable not available for refresh');
                        return;
                    }

                    table.ajax.reload(function(json) {
                        console.log('✅ DataTable reloaded:', json);

                        updateLastRefreshTime();

                        // Update client timestamp from DataTable response
                        if (json && (json.refresh_timestamp || json.last_db_update)) {
                            clientLastUpdate = json.refresh_timestamp || json.last_db_update;
                            console.log('⏰ Updated timestamp from DataTable:', clientLastUpdate);
                        }

                        // Update stats from DataTable response
                        if (json && json.stats) {
                            updateStats(json.stats);
                        }
                    }, false); // false = don't reset pagination
                }

                function performManualRefresh() {
                    console.log('🔄 Performing manual refresh...');

                    const $refreshBtn = $('#reload-items');
                    $refreshBtn.addClass('refreshing').prop('disabled', true);

                    table.ajax.reload(function(json) {
                        $refreshBtn.removeClass('refreshing').prop('disabled', false);
                        showItemsToast('Data refreshed successfully!', 'success');
                        updateLastRefreshTime();

                        if (json && (json.refresh_timestamp || json.last_db_update)) {
                            clientLastUpdate = json.refresh_timestamp || json.last_db_update;
                        }

                        if (json && json.stats) {
                            updateStats(json.stats);
                        }
                    }, false);
                }

                // === DATATABLE INITIALIZATION WITH ENHANCED DEBUGGING ===
                const table = $('#itemsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/superadmin/items/data/items",
                        type: 'GET',
                        timeout: 10000,
                        cache: false, // Prevent caching
                        data: function(d) {
                            // Add cache buster
                            d._ = Date.now();
                            return d;
                        },
                        dataSrc: function(json) {
                            console.log('📋 DataTable loaded successfully:', json);

                            // Initialize or update stats
                            if (json.stats) {
                                updateStats(json.stats);
                            }

                            updateLastRefreshTime();

                            // Initialize clientLastUpdate if not set
                            if (!clientLastUpdate && (json.refresh_timestamp || json.last_db_update)) {
                                clientLastUpdate = json.refresh_timestamp || json.last_db_update;
                                console.log('🏁 Initialized clientLastUpdate:', clientLastUpdate);
                            }

                            pollingFailureCount = 0;
                            return json.data;
                        },
                        error: function(xhr, error, code) {
                            console.error('❌ DataTable Ajax Error:', {
                                status: xhr.status,
                                error: error,
                                code: code,
                                responseText: xhr.responseText
                            });

                            pollingFailureCount++;

                            if (pollingFailureCount >= MAX_FAILURES) {
                                showItemsToast('Connection lost. Please refresh manually.', 'warning');
                            }
                        }
                    },
                    columns: [
                        // ... (kolom columns sama seperti sebelumnya)
                        {
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

                                switch (data) {
                                    case 'available':
                                        iconClass = 'ti-check';
                                        badgeClass = 'bg-success';
                                        statusText = 'Available';
                                        break;
                                    case 'borrowed':
                                        iconClass = 'ti-user';
                                        badgeClass = 'bg-warning';
                                        statusText = 'Borrowed';
                                        break;
                                    case 'missing':
                                        iconClass = 'ti-alert-triangle';
                                        badgeClass = 'bg-dark';
                                        statusText = 'Missing';
                                        break;
                                    case 'out_of_stock':
                                        iconClass = 'ti-x';
                                        badgeClass = 'bg-danger';
                                        statusText = 'Out of Stock';
                                        break;
                                    default:
                                        iconClass = 'ti-help';
                                        badgeClass = 'bg-secondary';
                                        statusText = 'Unknown';
                                }

                                return '<span class="badge ' + badgeClass + '" data-item-id="' + row
                                    .id +
                                    '" data-status="' + data + '"><i class="ti ' + iconClass +
                                    ' me-1"></i>' + statusText + '</span>';
                            }
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at',
                            className: 'text-center'
                        },
                        {
                            data: null,
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                // Actions dropdown (sama seperti sebelumnya)
                                let actions = `
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="dropdown">
                                <button class="btn btn-actions" type="button" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="/superadmin/items/${row.id}/edit">
                                        <i class="ti ti-edit me-2"></i>Edit</a></li>`;

                                if (row.status === 'borrowed') {
                                    actions += `<li><a class="dropdown-item text-warning mark-missing" href="#" 
                                       data-item-id="${row.id}" data-item-name="${row.nama_barang}">
                                       <i class="ti ti-alert-triangle me-2"></i>Mark as Missing</a></li>`;
                                }

                                actions += `<li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger delete-item" href="#" 
                                       data-item-id="${row.id}" data-item-name="${row.nama_barang}">
                                       <i class="ti ti-trash me-2"></i>Move to Trash</a></li>
                                </ul></div></div>`;

                                return actions;
                            }
                        }
                    ],
                    order: [
                        [4, 'desc']
                    ],
                    pageLength: 25,
                    responsive: true,
                    language: {
                        processing: "Loading items...",
                        emptyTable: "No items found"
                    }
                });

                // Store table reference globally
                window.itemsDataTable = table;

                // === PAGE VISIBILITY HANDLING ===
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        console.log('👁️ Page hidden - pausing activities');
                        isPollingEnabled = false;
                    } else {
                        console.log('👁️ Page visible - resuming activities');
                        isPollingEnabled = true;
                        pollingFailureCount = Math.max(0, pollingFailureCount - 1);

                        // Immediate check when page becomes visible
                        setTimeout(() => {
                            console.log('🔄 Immediate check after page visibility');
                            checkForDatabaseUpdates();
                        }, 500);
                    }
                });

                // === EVENT HANDLERS ===
                $('#reload-items').on('click', function() {
                    performManualRefresh();
                });

                $('#status-filter').on('change', function() {
                    const selectedStatus = $(this).val();
                    console.log('🔍 Filtering by status:', selectedStatus);
                    table.column(3).search(selectedStatus).draw();
                });

                // === INITIALIZATION ===
                updateLastRefreshTime();

                // Start polling immediately
                console.log('🚀 Initializing enhanced real-time system...');
                startPolling();

                // === GLOBAL DEBUG FUNCTION ===
                window.debugItemsRealTime = function() {
                    console.log('=== ENHANCED REAL-TIME DEBUG INFO ===');
                    console.log('Client Last Update:', clientLastUpdate);
                    console.log('Polling Interval:', POLLING_INTERVAL);
                    console.log('Failure Count:', pollingFailureCount);
                    console.log('Active Polling:', !!pollingInterval);
                    console.log('Polling Enabled:', isPollingEnabled);
                    console.log('Current Stats:', currentStats);
                    console.log('Page Hidden:', document.hidden);
                    console.log('DataTable Available:', !!table);

                    // Test immediate update check
                    console.log('--- Testing immediate update check ---');
                    checkForDatabaseUpdates();
                };

                console.log('✅ Enhanced real-time system initialized successfully!');
            });
        </script>
    @endpush
</x-layouts.superadmin_layout>
