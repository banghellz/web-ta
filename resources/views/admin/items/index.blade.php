<!-- resources/views/admin/items/index.blade.php -->
<x-layouts.admin_layout>
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
    @include('admin.items.create-modal')

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

            /* Refresh button animation */
            .btn.refreshing {
                animation: spin 1s linear infinite;
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
            $(function() {
                const csrfToken = "{{ csrf_token() }}";
                let itemToDelete = null;
                let itemToMarkMissing = null;

                // === SIMPLIFIED STATUS-ONLY REFRESH ===
                let pollingInterval = null;
                let isPollingEnabled = true;
                let pollingFailureCount = 0;

                const POLLING_INTERVAL = 1500; // 1 seconds
                const MAX_FAILURES = 3;

                // Store current item statuses for comparison
                let currentItemStatuses = new Map();

                let currentStats = {
                    total_items: {{ $totalItems ?? 0 }},
                    available_items: {{ $availableItems ?? 0 }},
                    borrowed_items: {{ $borrowedItems ?? 0 }},
                    missing_items: {{ $missingItems ?? 0 }}
                };

                // === STATUS-ONLY POLLING SYSTEM ===
                function startPolling() {
                    if (pollingInterval) clearInterval(pollingInterval);

                    console.log('Starting status-only polling...');

                    pollingInterval = setInterval(() => {
                        if (!isPollingEnabled || document.hidden) return;
                        checkStatusUpdates();
                    }, POLLING_INTERVAL);
                }

                function stopPolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                    }
                }

                // === CHECK ONLY STATUS CHANGES ===
                // Tracking untuk average request duration calculation
                let requestTracker = {
                    durations: [],
                    requestCount: 0,
                    totalDuration: 0,
                    averageDuration: 0
                };

                function checkStatusUpdates() {
                    // Catat waktu mulai check
                    const checkStartTime = performance.now();
                    const checkTimestamp = new Date();
                    const timeString = checkTimestamp.toLocaleTimeString('id-ID', {
                        hour12: false,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });

                    // Send current statuses to server for comparison
                    const currentStatusesObj = Object.fromEntries(currentItemStatuses);

                    $.ajax({
                        url: "/admin/items/check-status-updates",
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            current_statuses: currentStatusesObj
                        },
                        timeout: 8000,
                        success: function(response) {
                            // Hitung durasi request dalam second
                            const checkEndTime = performance.now();
                            const requestDuration = ((checkEndTime - checkStartTime) / 1000).toFixed(3);

                            // Track request duration untuk average calculation
                            const durationSec = parseFloat(requestDuration);
                            requestTracker.durations.push(durationSec);
                            requestTracker.requestCount++;
                            requestTracker.totalDuration += durationSec;

                            console.log(
                                `⏱️ Status check completed in ${requestDuration}s at ${timeString}`,
                                response);

                            pollingFailureCount = 0;

                            if (response.has_status_changes === true) {
                                const changeTime = new Date().toLocaleTimeString('id-ID', {
                                    hour12: false,
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                });

                                console.log(
                                    `🔄 Status changes detected at ${changeTime} (Request: ${requestDuration}s)`
                                );

                                // Log detail perubahan untuk setiap item
                                if (response.changed_items && response.changed_items.length > 0) {
                                    console.group(
                                        `📦 ${response.changed_items.length} Item(s) Changed - ${changeTime}`
                                    );

                                    response.changed_items.forEach((item, index) => {
                                        const previousStatus = currentItemStatuses.get(item.id
                                            .toString()) || 'unknown';
                                        const statusEmoji = {
                                            'available': '✅',
                                            'borrowed': '👤',
                                            'missing': '⚠️',
                                            'out_of_stock': '❌'
                                        };

                                        const prevEmoji = statusEmoji[previousStatus] || '❓';
                                        const newEmoji = statusEmoji[item.status] || '❓';

                                        console.log(
                                            `${prevEmoji} ➡️ ${newEmoji} Item #${item.id}: ${previousStatus} → ${item.status} (${changeTime}) [Request: ${requestDuration}s]`
                                        );
                                    });

                                    // Hitung dan tampilkan average setiap 10 request
                                    if (requestTracker.requestCount % 10 === 0) {
                                        requestTracker.averageDuration = requestTracker.totalDuration /
                                            requestTracker.requestCount;
                                        const last10Average = requestTracker.durations.slice(-10).reduce((a,
                                            b) => a + b, 0) / 10;

                                        console.log(
                                            `📊 REQUEST PERFORMANCE REPORT (${requestTracker.requestCount} requests):`
                                        );
                                        console.log(
                                            `   📈 Overall Average: ${requestTracker.averageDuration.toFixed(3)}s`
                                        );
                                        console.log(
                                            `   🔟 Last 10 Requests Average: ${last10Average.toFixed(3)}s`
                                        );
                                        console.log(
                                            `   📊 Min/Max in last 10: ${Math.min(...requestTracker.durations.slice(-10)).toFixed(3)}s / ${Math.max(...requestTracker.durations.slice(-10)).toFixed(3)}s`
                                        );
                                        console.log(
                                            `   🔍 Debug - Total duration: ${requestTracker.totalDuration.toFixed(3)}s, Count: ${requestTracker.requestCount}`
                                        );
                                        console.log('─'.repeat(50));
                                    }

                                    console.groupEnd();
                                }

                                updateItemStatuses(response.changed_items || []);

                                // Update stats if provided
                                if (response.stats) {
                                    updateStats(response.stats);
                                }

                                // Update current statuses map
                                if (response.current_statuses) {
                                    currentItemStatuses = new Map(Object.entries(response
                                        .current_statuses));
                                }
                            } else {
                                // Track request duration meski tidak ada perubahan
                                if (requestTracker.requestCount % 10 === 0 && requestTracker.requestCount >
                                    0) {
                                    requestTracker.averageDuration = requestTracker.totalDuration /
                                        requestTracker.requestCount;
                                    const last10Average = requestTracker.durations.slice(-10).reduce((a,
                                        b) => a + b, 0) / 10;

                                    console.log(
                                        `📊 REQUEST PERFORMANCE REPORT (${requestTracker.requestCount} requests):`
                                    );
                                    console.log(
                                        `   📈 Overall Average: ${requestTracker.averageDuration.toFixed(3)}s`
                                    );
                                    console.log(
                                        `   🔟 Last 10 Requests Average: ${last10Average.toFixed(3)}s`
                                    );
                                    console.log(
                                        `   📊 Min/Max in last 10: ${Math.min(...requestTracker.durations.slice(-10)).toFixed(3)}s / ${Math.max(...requestTracker.durations.slice(-10)).toFixed(3)}s`
                                    );
                                    console.log('─'.repeat(50));
                                }

                                // Log occasional "no changes" untuk monitoring dengan second
                                if (Math.random() < 0.05) { // 5% chance
                                    console.log(
                                        `✅ No status changes - checked in ${requestDuration}s at ${timeString}`
                                    );
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            const checkEndTime = performance.now();
                            const requestDuration = ((checkEndTime - checkStartTime) / 1000).toFixed(3);
                            const errorTime = new Date().toLocaleTimeString('id-ID', {
                                hour12: false,
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            pollingFailureCount++;
                            console.error(
                                `❌ Status check failed after ${requestDuration}s at ${errorTime}:`, xhr
                                .status, error);

                            if (pollingFailureCount >= MAX_FAILURES) {
                                console.warn(
                                    `🔌 Connection lost at ${errorTime} after ${MAX_FAILURES} failures. Auto-refresh disabled.`
                                );
                                showItemsToast('Connection lost. Auto-refresh disabled.', 'warning');
                                stopPolling();

                                // Retry after delay
                                setTimeout(() => {
                                    const retryTime = new Date().toLocaleTimeString('id-ID', {
                                        hour12: false,
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    });
                                    console.log(`🔄 Attempting to reconnect at ${retryTime}...`);
                                    pollingFailureCount = 0;
                                    startPolling();
                                }, 10000);
                            }
                        }
                    });
                }

                // === UPDATE ONLY CHANGED ITEM STATUSES ===
                function updateItemStatuses(changedItems) {
                    if (!changedItems || changedItems.length === 0) return;

                    console.log('Updating statuses for items:', changedItems);

                    changedItems.forEach(item => {
                        // Find the status badge for this item
                        const $badge = $(`span.badge[data-item-id="${item.id}"]`);

                        if ($badge.length > 0) {
                            updateStatusBadge($badge, item.status);
                            console.log(`Updated status for item ${item.id}: ${item.status}`);
                        }
                    });

                    // Update last refresh time
                    updateLastRefreshTime();
                }

                // === UPDATE STATUS BADGE ===
                function updateStatusBadge($badge, newStatus) {
                    const statusConfig = {
                        'available': {
                            icon: 'ti-check',
                            class: 'bg-success',
                            text: 'Available'
                        },
                        'borrowed': {
                            icon: 'ti-user',
                            class: 'bg-warning',
                            text: 'Borrowed'
                        },
                        'missing': {
                            icon: 'ti-alert-triangle',
                            class: 'bg-dark',
                            text: 'Missing'
                        },
                        'out_of_stock': {
                            icon: 'ti-x',
                            class: 'bg-danger',
                            text: 'Out of Stock'
                        }
                    };

                    const config = statusConfig[newStatus] || {
                        icon: 'ti-help',
                        class: 'bg-secondary',
                        text: 'Unknown'
                    };

                    // Add subtle animation
                    $badge.addClass('status-updating')
                        .removeClass('bg-success bg-warning bg-dark bg-danger bg-secondary')
                        .addClass(config.class)
                        .attr('data-status', newStatus)
                        .html(`<i class="ti ${config.icon} me-1"></i>${config.text}`);

                    setTimeout(() => $badge.removeClass('status-updating'), 600);
                }

                // === FULL TABLE REFRESH (for manual refresh only) ===
                function performManualRefresh() {
                    const $refreshBtn = $('#reload-items');
                    $refreshBtn.addClass('refreshing');

                    table.ajax.reload(function(json) {
                        $refreshBtn.removeClass('refreshing');
                        showItemsToast('Data refreshed successfully!', 'success');
                        updateLastRefreshTime();

                        // Update current statuses map from full data
                        if (json && json.data) {
                            currentItemStatuses.clear();
                            json.data.forEach(item => {
                                currentItemStatuses.set(item.id.toString(), item.status);
                            });
                        }

                        // Update stats
                        if (json && json.stats) {
                            updateStats(json.stats);
                        }
                    }, false);
                }

                // === UTILITY FUNCTIONS ===
                function showItemsToast(message, type = 'success') {
                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.show(type, message);
                    } else if (typeof window.showNotificationToast === 'function') {
                        window.showNotificationToast(message, type);
                    } else {
                        console.log(`${type.toUpperCase()}: ${message}`);
                    }
                }

                function updateLastRefreshTime() {
                    const now = new Date();
                    $('#last-updated-time').text(now.toLocaleTimeString());
                }

                function updateStats(stats) {
                    function animateStatUpdate(element, newValue) {
                        const $element = $(element);
                        const currentValue = parseInt($element.text().replace(/,/g, '')) || 0;

                        if (currentValue !== newValue) {
                            $element.addClass('stat-updating');
                            $element.text(newValue.toLocaleString());
                            setTimeout(() => $element.removeClass('stat-updating'), 500);
                        }
                    }

                    Object.keys(stats).forEach(key => {
                        const elementMap = {
                            'total_items': '#total-items',
                            'available_items': '#available-items',
                            'borrowed_items': '#borrowed-items',
                            'missing_items': '#missing-items'
                        };

                        const element = elementMap[key];
                        if (element && stats[key] !== undefined && stats[key] !== currentStats[key]) {
                            animateStatUpdate(element, stats[key]);
                            currentStats[key] = stats[key];
                        }
                    });
                }

                // === DATATABLE INITIALIZATION ===
                const table = $('#itemsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/admin/items/data/items",
                        type: 'GET',
                        timeout: 10000,
                        dataSrc: function(json) {
                            console.log('DataTable loaded successfully');

                            // Initialize current statuses map
                            if (json && json.data) {
                                currentItemStatuses.clear();
                                json.data.forEach(item => {
                                    currentItemStatuses.set(item.id.toString(), item.status);
                                });
                            }

                            updateStats(json.stats || {});
                            updateLastRefreshTime();
                            pollingFailureCount = 0;

                            return json.data;
                        },
                        error: function(xhr, error, code) {
                            console.error('DataTable Ajax Error:', xhr.status, error);
                            pollingFailureCount++;

                            if (pollingFailureCount >= MAX_FAILURES) {
                                showItemsToast('Connection lost. Please refresh manually.', 'warning');
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
                                        <a class="dropdown-item" href="/admin/items/${row.id}/edit">
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
                                    <i class="ti ti-trash me-2"></i>Move to Trash
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>`;

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

                // === PAGE VISIBILITY HANDLING ===
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        console.log('Page hidden - pausing polling');
                        isPollingEnabled = false;
                    } else {
                        console.log('Page visible - resuming polling');
                        isPollingEnabled = true;

                        // Check for updates immediately when page becomes visible
                        setTimeout(() => {
                            checkStatusUpdates();
                        }, 1000);
                    }
                });

                // === EVENT HANDLERS ===
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
                        url: `/admin/items/${itemId}`,
                        type: 'GET',
                        timeout: 10000,
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
                    $btn.prop('disabled', true).html('<i class="ti ti-loader-2 me-1 spinning"></i>Moving...');

                    $.ajax({
                        url: `/admin/items/${itemToDelete.id}`,
                        type: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        timeout: 10000,
                        success: function(response) {
                            if (response.success) {
                                showItemsToast(response.message ||
                                    'Item moved to trash successfully!', 'success');

                                // Remove from current statuses
                                currentItemStatuses.delete(itemToDelete.id.toString());

                                // Trigger immediate status check
                                setTimeout(() => checkStatusUpdates(), 500);

                                if (response.stats) {
                                    updateStats(response.stats);
                                }
                            } else {
                                showItemsToast(response.message || 'Failed to move item to trash.',
                                    'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'Failed to move item to trash. Please try again.';
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
                        url: `/admin/missing-tools/mark-missing/${itemToMarkMissing.id}`,
                        type: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        timeout: 10000,
                        success: function(response) {
                            if (response.success) {
                                showItemsToast(response.message ||
                                    'Item marked as missing successfully!', 'warning');

                                // Update status in map
                                currentItemStatuses.set(itemToMarkMissing.id.toString(), 'missing');

                                // Trigger immediate status check
                                setTimeout(() => checkStatusUpdates(), 500);

                                if (response.stats) {
                                    updateStats(response.stats);
                                }
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

                    // Add new item to statuses map
                    if (data.item && data.item.id) {
                        currentItemStatuses.set(data.item.id.toString(), data.item.status || 'available');
                    }

                    // Trigger status check
                    setTimeout(() => checkStatusUpdates(), 500);

                    if (data.stats) {
                        updateStats(data.stats);
                    }
                });

                // Window unload handling
                window.addEventListener('beforeunload', function() {
                    stopPolling();
                });

                // === INITIALIZATION ===
                updateLastRefreshTime();
                startPolling();

                // Laravel session messages
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

                // Global functions
                window.refreshItemsTable = function(silent = true) {
                    if (silent) {
                        checkStatusUpdates();
                    } else {
                        performManualRefresh();
                    }
                };

                window.debugItemsRealTime = function() {
                    console.log('=== STATUS-ONLY REAL-TIME DEBUG ===');
                    console.log('Polling Interval:', POLLING_INTERVAL);
                    console.log('Failure Count:', pollingFailureCount);
                    console.log('Active Polling:', !!pollingInterval);
                    console.log('Polling Enabled:', isPollingEnabled);
                    console.log('Current Item Statuses:', Object.fromEntries(currentItemStatuses));
                    console.log('Current Stats:', currentStats);
                };

                console.log('Status-only real-time system initialized');
            });
        </script>
    @endpush
</x-layouts.admin_layout>
