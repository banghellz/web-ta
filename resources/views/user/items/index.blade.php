<!-- resources/views/user/items/index.blade.php -->
<x-layouts.user_layout title="Tool Stocks" pageTitle="">

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-package me-2 text-primary"></i>{{ $title ?? 'Tool Stocks' }}
                    </h2>
                    <div class="text-muted mt-1">{{ $content ?? 'View available tools and their current status' }}</div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <span class="d-none d-sm-inline">
                            <button id="reload-items" class="btn btn-primary">
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
            <!-- Statistics Cards -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tool Inventory Overview</h3>
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
                                                Tools
                                            </div>
                                            <div class="text-muted">
                                                Total in System
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
                                                Tools
                                            </div>
                                            <div class="text-muted">
                                                Available to Borrow
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
                                                Tools
                                            </div>
                                            <div class="text-muted">
                                                Currently Borrowed
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
                                                Tools
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
                    <h3 class="card-title">Available Tools</h3>
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
                                    <th>Tool Name</th>
                                    <th>Status</th>
                                    <th>Borrower</th>
                                    <th>Last Updated</th>
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

    <!-- Tool Detail Modal -->
    <div class="modal modal-blur fade" id="modal-tool-detail" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tool Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="tool-detail-content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

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

            /* Status update animation */
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

            /* Stat update animation */
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

            /* Refresh button animation - FIXED */
            .btn.refreshing .ti-refresh {
                animation: spin 1s linear infinite;
            }

            /* Clickable tool names */
            .tool-name-clickable {
                color: var(--tblr-primary);
                text-decoration: none;
                cursor: pointer;
                transition: color 0.15s ease-in-out;
            }

            .tool-name-clickable:hover {
                color: var(--tblr-primary-dark);
                text-decoration: underline;
            }

            /* Badge styling */
            .badge-lg {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                font-weight: 500;
            }

            /* Performance optimization for animations */
            .status-updating,
            .stat-updating {
                will-change: transform, opacity;
            }

            /* Reduced motion for accessibility */
            @media (prefers-reduced-motion: reduce) {

                .status-updating,
                .stat-updating {
                    animation: none !important;
                }
            }

            /* Mobile responsiveness */
            @media (max-width: 768px) {
                .card-actions .row {
                    gap: 0.5rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(function() {
                const csrfToken = "{{ csrf_token() }}";

                // === STATUS-ONLY POLLING SYSTEM (Similar to SuperAdmin) ===
                let pollingInterval = null;
                let isPollingEnabled = true;
                let pollingFailureCount = 0;

                const POLLING_INTERVAL = 2000; // 2 seconds for user view (slightly slower than superadmin)
                const MAX_FAILURES = 3;

                // Store current item statuses for comparison
                let currentItemStatuses = new Map();

                let currentStats = {
                    total_items: {{ $totalItems ?? 0 }},
                    available_items: {{ $availableItems ?? 0 }},
                    borrowed_items: {{ $borrowedItems ?? 0 }},
                    missing_items: {{ $missingItems ?? 0 }}
                };

                // Request tracking for performance monitoring
                let requestTracker = {
                    durations: [],
                    requestCount: 0,
                    totalDuration: 0,
                    averageDuration: 0
                };

                // === START/STOP POLLING ===
                function startPolling() {
                    if (pollingInterval) clearInterval(pollingInterval);

                    console.log('Starting user status-only polling...');

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

                // === CHECK STATUS UPDATES ===
                function checkStatusUpdates() {
                    const checkStartTime = performance.now();
                    const checkTimestamp = new Date();
                    const timeString = checkTimestamp.toLocaleTimeString('id-ID', {
                        hour12: false,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        fractionalSecondDigits: 3
                    });

                    // Send current statuses to server for comparison
                    const currentStatusesObj = Object.fromEntries(currentItemStatuses);

                    $.ajax({
                        url: "/user/items/check-status-updates",
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            current_statuses: currentStatusesObj
                        },
                        timeout: 8000,
                        success: function(response) {
                            // Calculate request duration
                            const checkEndTime = performance.now();
                            const requestDuration = (checkEndTime - checkStartTime).toFixed(2);

                            // Track request performance
                            const durationMs = parseFloat(requestDuration);
                            requestTracker.durations.push(durationMs);
                            requestTracker.requestCount++;
                            requestTracker.totalDuration += durationMs;

                            console.log(
                                `⏱️ [USER] Status check completed in ${requestDuration}ms at ${timeString}`,
                                response);

                            pollingFailureCount = 0;

                            if (response.has_status_changes === true) {
                                const changeTime = new Date().toLocaleTimeString('id-ID', {
                                    hour12: false,
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                    fractionalSecondDigits: 3
                                });

                                console.log(
                                    `🔄 [USER] Status changes detected at ${changeTime} (Request: ${requestDuration}ms)`
                                );

                                // Log detailed changes
                                if (response.changed_items && response.changed_items.length > 0) {
                                    console.group(
                                        `📦 [USER] ${response.changed_items.length} Item(s) Changed - ${changeTime}`
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
                                            `${prevEmoji} ➡️ ${newEmoji} Tool #${item.id}: ${previousStatus} → ${item.status} (${changeTime})`
                                        );
                                    });

                                    // Performance report every 10 requests
                                    if (requestTracker.requestCount % 10 === 0) {
                                        requestTracker.averageDuration = requestTracker.totalDuration /
                                            requestTracker.requestCount;
                                        const last10Average = requestTracker.durations.slice(-10).reduce((a,
                                            b) => a + b, 0) / 10;

                                        console.log(
                                            `📊 [USER] PERFORMANCE REPORT (${requestTracker.requestCount} requests):`
                                        );
                                        console.log(
                                            `   📈 Overall Average: ${requestTracker.averageDuration.toFixed(2)}ms`
                                        );
                                        console.log(
                                            `   🔟 Last 10 Average: ${last10Average.toFixed(2)}ms`
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
                                // Occasional "no changes" log for monitoring
                                if (Math.random() < 0.03) { // 3% chance
                                    console.log(
                                        `✅ [USER] No status changes - checked in ${requestDuration}ms at ${timeString}`
                                    );
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            const checkEndTime = performance.now();
                            const requestDuration = (checkEndTime - checkStartTime).toFixed(2);
                            const errorTime = new Date().toLocaleTimeString('id-ID', {
                                hour12: false,
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                fractionalSecondDigits: 3
                            });

                            pollingFailureCount++;
                            console.error(
                                `❌ [USER] Status check failed after ${requestDuration}ms at ${errorTime}:`,
                                xhr.status, error);

                            if (pollingFailureCount >= MAX_FAILURES) {
                                console.warn(
                                    `🔌 [USER] Connection lost at ${errorTime} after ${MAX_FAILURES} failures.`
                                );
                                showToast('Connection lost. Auto-refresh disabled.', 'warning');
                                stopPolling();

                                // Retry after delay
                                setTimeout(() => {
                                    const retryTime = new Date().toLocaleTimeString('id-ID', {
                                        hour12: false,
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit',
                                        fractionalSecondDigits: 3
                                    });
                                    console.log(
                                        `🔄 [USER] Attempting to reconnect at ${retryTime}...`);
                                    pollingFailureCount = 0;
                                    startPolling();
                                }, 10000);
                            }
                        }
                    });
                }

                // === UPDATE CHANGED ITEM STATUSES ===
                function updateItemStatuses(changedItems) {
                    if (!changedItems || changedItems.length === 0) return;

                    console.log('[USER] Updating statuses for items:', changedItems);

                    changedItems.forEach(item => {
                        // Find the status badge for this item
                        const $badge = $(`span.badge[data-item-id="${item.id}"]`);

                        if ($badge.length > 0) {
                            updateStatusBadge($badge, item.status);
                            console.log(`[USER] Updated status for item ${item.id}: ${item.status}`);
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
                        }
                    };

                    const config = statusConfig[newStatus] || {
                        icon: 'ti-help',
                        class: 'bg-secondary',
                        text: 'Unknown'
                    };

                    $badge.removeClass('bg-success bg-warning bg-dark bg-secondary')
                        .addClass('status-updating')
                        .addClass(config.class)
                        .attr('data-status', newStatus)
                        .html(`<i class="ti ${config.icon} me-1"></i>${config.text}`);

                    setTimeout(() => $badge.removeClass('status-updating'), 800);
                }

                // === MANUAL REFRESH ===
                function performManualRefresh() {
                    const $refreshBtn = $('#reload-items');
                    const $icon = $refreshBtn.find('.ti-refresh');

                    $refreshBtn.addClass('refreshing').prop('disabled', true);

                    table.ajax.reload(function(json) {
                        $refreshBtn.removeClass('refreshing').prop('disabled', false);
                        showToast('Data refreshed successfully!', 'success');
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
                function showToast(message, type = 'success', skipAutoUpdate = false) {
                    if (skipAutoUpdate) {
                        console.log('[USER] Auto-update (silent):', message);
                        return;
                    }

                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.show(type, message);
                    } else if (typeof window.showNotificationToast === 'function') {
                        window.showNotificationToast(message, type);
                    } else {
                        console.log(`[USER] ${type.toUpperCase()}: ${message}`);
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
                        url: "/user/items/data",
                        type: 'GET',
                        timeout: 10000,
                        dataSrc: function(json) {
                            console.log('[USER] DataTable loaded successfully');

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
                            console.error('[USER] DataTable Ajax Error:', {
                                status: xhr.status,
                                error: error,
                                code: code
                            });
                            pollingFailureCount++;

                            if (pollingFailureCount >= MAX_FAILURES) {
                                showToast('Connection lost. Please refresh manually.', 'warning');
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
                                    '<a href="#" class="tool-name-clickable" data-tool-id="' + row.id +
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
                            data: 'borrower_name',
                            name: 'borrower_name',
                            className: 'text-center',
                            render: function(data, type, row) {
                                if (data && data.trim() !== '') {
                                    return '<span class="text-muted">' + data + '</span>';
                                } else {
                                    return '<span class="text-muted">-</span>';
                                }
                            }
                        },
                        {
                            data: 'updated_at_formatted',
                            name: 'updated_at',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<div class="text-muted small">' + data + '</div>';
                            }
                        }
                    ],
                    order: [
                        [5, 'desc']
                    ],
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search tools...',
                        lengthMenu: '_MENU_ entries per page',
                        processing: "Loading tools...",
                        emptyTable: "No tools found",
                        info: "Showing _START_ to _END_ of _TOTAL_ tools",
                        infoEmpty: "Showing 0 to 0 of 0 tools",
                        infoFiltered: "(filtered from _MAX_ total tools)",
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
                        console.log('[USER] Page hidden - pausing activities');
                        isPollingEnabled = false;
                    } else {
                        console.log('[USER] Page visible - resuming activities');
                        isPollingEnabled = true;
                        pollingFailureCount = Math.max(0, pollingFailureCount - 1);

                        // Resume polling after a short delay
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

                // Tool detail modal - simplified without AJAX
                $(document).on('click', '.tool-name-clickable', function(e) {
                    e.preventDefault();

                    // Get data from the table row
                    const $row = $(this).closest('tr');
                    const rowData = table.row($row).data();

                    if (rowData) {
                        showToolDetailModal(rowData);
                    }
                });

                function showToolDetailModal(toolData) {
                    const statusConfig = {
                        'available': {
                            icon: 'ti-check',
                            class: 'bg-success',
                            text: 'Available for Borrowing'
                        },
                        'borrowed': {
                            icon: 'ti-user',
                            class: 'bg-warning',
                            text: 'Currently Borrowed'
                        },
                        'missing': {
                            icon: 'ti-alert-triangle',
                            class: 'bg-dark',
                            text: 'Missing/Lost'
                        }
                    };

                    const config = statusConfig[toolData.status] || {
                        icon: 'ti-help',
                        class: 'bg-secondary',
                        text: 'Unknown'
                    };

                    const borrowerInfo = toolData.borrower_name ? `
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Current Borrower</label>
                                    <div class="fw-bold">${toolData.borrower_name}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Borrowed Since</label>
                                    <div class="text-muted">
                                        <i class="ti ti-clock me-1"></i>
                                        ${toolData.updated_at_formatted}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ` : '';

                    const modalContent = `
                        <div class="row">
                            <div class="col-12">
                                <!-- Tool Information -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">
                                            <i class="ti ti-tool me-2"></i>Tool Information
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Tool Name</label>
                                                    <div class="fw-bold fs-5">${toolData.nama_barang}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">EPC Code</label>
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-sm me-2 bg-secondary text-white">
                                                            <i class="ti ti-qrcode"></i>
                                                        </span>
                                                        <code class="bg-light px-2 py-1 rounded">${toolData.epc}</code>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Current Status</label>
                                                    <div>
                                                        <span class="badge ${config.class} badge-lg">
                                                            <i class="ti ${config.icon} me-1"></i>
                                                            ${config.text}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Last Updated</label>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-clock me-2 text-muted"></i>
                                                        <span>${toolData.updated_at_formatted}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        ${borrowerInfo}
                                    </div>
                                </div>

                                ${toolData.status === 'available' ? `
                                                    <!-- Quick Info -->
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="alert alert-success d-flex align-items-center mb-0">
                                                                <i class="ti ti-check-circle me-2"></i>
                                                                <div>
                                                                    <strong>Available for Borrowing</strong><br>
                                                                    <small>Use the physical RFID system to borrow this tool</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ` : ''}
                            </div>
                        </div>
                    `;

                    $('#tool-detail-content').html(modalContent);
                    $('#modal-tool-detail').modal('show');
                }

                // === WINDOW UNLOAD HANDLING ===
                window.addEventListener('beforeunload', function() {
                    stopPolling();
                });

                // === INITIALIZATION ===
                updateLastRefreshTime();
                startPolling();

                // Show Laravel session messages
                @if (session('success'))
                    showToast("{{ session('success') }}", 'success');
                @endif

                @if (session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

                // === GLOBAL FUNCTIONS ===
                window.refreshToolsTable = function(silent = true) {
                    if (silent) {
                        checkStatusUpdates();
                    } else {
                        performManualRefresh();
                    }
                };

                window.debugUserRealTime = function() {
                    console.log('=== USER STATUS-ONLY REAL-TIME DEBUG ===');
                    console.log('Polling Interval:', POLLING_INTERVAL);
                    console.log('Failure Count:', pollingFailureCount);
                    console.log('Active Polling:', !!pollingInterval);
                    console.log('Polling Enabled:', isPollingEnabled);
                    console.log('Current Item Statuses:', Object.fromEntries(currentItemStatuses));
                    console.log('Current Stats:', currentStats);
                    console.log('Request Performance:', requestTracker);
                };

                console.log('[USER] Status-only real-time system initialized');
            });
        </script>
    @endpush
</x-layouts.user_layout>
