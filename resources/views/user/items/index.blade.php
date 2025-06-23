<!-- resources/views/user/items/index.blade.php -->
<x-layouts.user_layout title="Tool Stocks" pageTitle="">
    <x-slot name="title">Missing Tools</x-slot>
    <x-slot name="content">View your missing tools history</x-slot>

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
                        <div class="d-flex align-items-center">
                            <!-- Connection Status Indicator -->
                            <div id="connection-status" class="d-flex align-items-center me-3">
                                <div id="connection-indicator" class="bg-success rounded-circle me-2"
                                    style="width: 8px; height: 8px;" title="Connection"></div>
                                <small class="text-muted"><span id="connection-text">Live</span></small>
                            </div>
                            <small class="text-muted" id="last-updated">
                                Last updated: <span id="last-updated-time">Just now</span>
                            </small>
                        </div>
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
            /* Connection status indicators */
            #connection-status.connected #connection-indicator {
                background-color: var(--tblr-green) !important;
                animation: pulse 3s infinite;
            }

            #connection-status.connecting #connection-indicator {
                background-color: var(--tblr-yellow) !important;
                animation: spin 1s linear infinite;
            }

            #connection-status.disconnected #connection-indicator {
                background-color: var(--tblr-red) !important;
                animation: blink 1s infinite;
            }

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

            /* Refresh button animation */
            .btn.refreshing {
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
            .stat-updating,
            #connection-indicator {
                will-change: transform, opacity;
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
                .card-actions .d-flex {
                    flex-direction: column;
                    gap: 0.5rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(function() {
                // === REAL-TIME CONFIGURATION (User View Optimized) ===
                let pollingInterval = null;
                let clientLastUpdate = null;
                let isPollingEnabled = true;
                let pollingFailureCount = 0;

                // USER VIEW: Longer intervals for better performance
                const POLLING_INTERVAL = 5000; // 5 seconds (reasonable for user view)
                const MAX_FAILURES = 3;
                const RETRY_DELAY = 3000; // 3 seconds

                let currentStats = {
                    total_items: {{ $totalItems ?? 0 }},
                    available_items: {{ $availableItems ?? 0 }},
                    borrowed_items: {{ $borrowedItems ?? 0 }},
                    missing_items: {{ $missingItems ?? 0 }}
                };

                // === UTILITY FUNCTIONS ===
                function showToast(message, type = 'success', skipAutoUpdate = false) {
                    if (skipAutoUpdate) {
                        console.log('Auto-update (silent):', message);
                        return;
                    }

                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.show(type, message);
                    } else if (typeof window.showNotificationToast === 'function') {
                        window.showNotificationToast(message, type);
                    } else if (typeof toastr !== 'undefined') {
                        toastr[type](message);
                    } else {
                        console.log(`${type.toUpperCase()}: ${message}`);
                    }
                }

                function setConnectionStatus(status) {
                    const $indicator = $('#connection-indicator');
                    const $statusContainer = $('#connection-status');
                    const $text = $('#connection-text');

                    $statusContainer.removeClass('connected connecting disconnected').addClass(status);

                    const statusConfig = {
                        'connected': {
                            title: 'Connected - Live updates active',
                            text: 'Live'
                        },
                        'connecting': {
                            title: 'Checking for updates...',
                            text: 'Syncing'
                        },
                        'disconnected': {
                            title: 'Connection failed - Updates disabled',
                            text: 'Offline'
                        }
                    };

                    const config = statusConfig[status];
                    if (config) {
                        $indicator.attr('title', config.title);
                        $text.text(config.text);
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

                    $badge.removeClass('bg-success bg-warning bg-dark bg-danger bg-secondary')
                        .addClass('status-updating')
                        .addClass(config.class)
                        .attr('data-status', newStatus)
                        .html(`<i class="ti ${config.icon} me-1"></i>${config.text}`);

                    setTimeout(() => $badge.removeClass('status-updating'), 800);
                }

                // === OPTIMIZED AJAX WITH BETTER ERROR HANDLING ===
                function makeOptimizedRequest(url, options = {}) {
                    const defaultOptions = {
                        timeout: 8000, // Slightly longer for user view
                        retries: 1,
                        retryDelay: 2000
                    };

                    const config = {
                        ...defaultOptions,
                        ...options
                    };

                    // Circuit breaker
                    if (pollingFailureCount >= MAX_FAILURES) {
                        return Promise.reject(new Error('Circuit breaker open'));
                    }

                    function attemptRequest(attempt = 1) {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                    url: url,
                                    timeout: config.timeout,
                                    ...config.ajaxOptions
                                })
                                .done(resolve)
                                .fail((xhr, status, error) => {
                                    console.warn(`Request failed (attempt ${attempt}):`, {
                                        status: xhr.status,
                                        statusText: xhr.statusText,
                                        error
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

                // === REAL-TIME POLLING SYSTEM ===
                function startPolling() {
                    if (pollingInterval) clearInterval(pollingInterval);

                    console.log(`Starting user real-time polling - interval: ${POLLING_INTERVAL}ms`);

                    pollingInterval = setInterval(() => {
                        if (!isPollingEnabled || document.hidden) return;
                        checkForDatabaseUpdates();
                    }, POLLING_INTERVAL);
                }

                function stopPolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                    }
                }

                function checkForDatabaseUpdates() {
                    setConnectionStatus('connecting');

                    makeOptimizedRequest("{{ route('user.items.check-updates') }}", {
                            ajaxOptions: {
                                type: 'GET',
                                data: {
                                    last_update: clientLastUpdate
                                }
                            }
                        })
                        .then(response => {
                            console.log('User update check response:', response);

                            pollingFailureCount = 0;
                            setConnectionStatus('connected');

                            if (response.has_updates) {
                                console.log('Changes detected - refreshing user table');
                                performSilentRefresh();

                                if (response.stats) {
                                    updateStats(response.stats);
                                }

                                // Show user-friendly notification for significant updates
                                if (response.updates && response.updates.length > 0) {
                                    const updateCount = response.updates.length;
                                    const message = updateCount === 1 ?
                                        `${response.updates[0].name} ${response.updates[0].action}` :
                                        `${updateCount} tools updated`;

                                    // Only show toast for significant changes, not every update
                                    if (updateCount <= 2) {
                                        showToast(message, 'info', true); // Silent for frequent updates
                                    }
                                }
                            }

                            // Update timestamp
                            if (response.latest_db_update) {
                                clientLastUpdate = response.latest_db_update;
                            }
                        })
                        .catch(xhr => {
                            pollingFailureCount++;
                            console.error('User update check failed:', xhr.status);

                            if (pollingFailureCount >= MAX_FAILURES) {
                                setConnectionStatus('disconnected');
                                showToast('Connection lost. Auto-refresh disabled.', 'warning');
                                stopPolling();

                                // Retry after longer delay for user view
                                setTimeout(() => {
                                    pollingFailureCount = 0;
                                    startPolling();
                                }, RETRY_DELAY * 2);
                            } else {
                                setConnectionStatus('connected');
                            }
                        });
                }

                function performSilentRefresh() {
                    console.log('Performing silent table refresh (user view)...');

                    table.ajax.reload(function(json) {
                        updateLastRefreshTime();

                        if (json && (json.refresh_timestamp || json.last_db_update)) {
                            clientLastUpdate = json.refresh_timestamp || json.last_db_update;
                        }
                    }, false);
                }

                function performManualRefresh() {
                    const $refreshBtn = $('#reload-items');

                    $refreshBtn.addClass('refreshing');
                    setConnectionStatus('connecting');

                    table.ajax.reload(function(json) {
                        $refreshBtn.removeClass('refreshing');
                        setConnectionStatus('connected');
                        showToast('Data refreshed successfully!', 'success');
                        updateLastRefreshTime();

                        if (json && (json.refresh_timestamp || json.last_db_update)) {
                            clientLastUpdate = json.refresh_timestamp || json.last_db_update;
                        }
                    }, false);
                }

                // === DATATABLE INITIALIZATION ===
                const table = $('#itemsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.items.data') }}",
                        type: 'GET',
                        timeout: 15000,
                        dataSrc: function(json) {
                            console.log('User DataTable loaded successfully');

                            updateStats(json.stats || {});
                            updateLastRefreshTime();
                            setConnectionStatus('connected');

                            // Initialize clientLastUpdate
                            if (!clientLastUpdate && (json.refresh_timestamp || json.last_db_update)) {
                                clientLastUpdate = json.refresh_timestamp || json.last_db_update;
                                console.log('Initialized user clientLastUpdate:', clientLastUpdate);
                            }

                            pollingFailureCount = 0;
                            return json.data;
                        },
                        error: function(xhr, error, code) {
                            console.error('User DataTable Ajax Error:', {
                                status: xhr.status,
                                error: error,
                                code: code
                            });
                            pollingFailureCount++;
                            setConnectionStatus('disconnected');

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
                    ], // Order by updated_at descending
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

                window.userItemsDataTable = table;

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
                        },
                        'out_of_stock': {
                            icon: 'ti-x',
                            class: 'bg-danger',
                            text: 'Out of Stock'
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

                // === PAGE VISIBILITY HANDLING ===
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        console.log('User page hidden - pausing activities');
                        isPollingEnabled = false;
                    } else {
                        console.log('User page visible - resuming activities');
                        isPollingEnabled = true;
                        pollingFailureCount = Math.max(0, pollingFailureCount - 1);
                        setConnectionStatus('connecting');

                        // Resume polling after a short delay
                        setTimeout(() => {
                            checkForDatabaseUpdates();
                        }, 2000);
                    }
                });

                // === WINDOW UNLOAD HANDLING ===
                window.addEventListener('beforeunload', function() {
                    stopPolling();
                });

                // === INITIALIZATION ===
                updateLastRefreshTime();
                setConnectionStatus('connected');

                // Start real-time polling
                startPolling();

                // Show Laravel session messages
                @if (session('success'))
                    showToast("{{ session('success') }}", 'success');
                @endif

                @if (session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

                @if (session('warning'))
                    showToast("{{ session('warning') }}", 'warning');
                @endif

                @if (session('info'))
                    showToast("{{ session('info') }}", 'info');
                @endif

                // === GLOBAL FUNCTIONS ===
                window.refreshUserItemsTable = function(silent = true) {
                    if ($('#itemsTable').DataTable()) {
                        $('#itemsTable').DataTable().ajax.reload(null, false);
                    }
                };

                window.debugUserRealTime = function() {
                    console.log('=== USER REAL-TIME DEBUG INFO ===');
                    console.log('Client Last Update:', clientLastUpdate);
                    console.log('Polling Interval:', POLLING_INTERVAL);
                    console.log('Failure Count:', pollingFailureCount);
                    console.log('Active Polling:', !!pollingInterval);
                    console.log('Polling Enabled:', isPollingEnabled);
                };

                console.log(`User real-time system initialized with ${POLLING_INTERVAL}ms interval`);
            });
        </script>
    @endpush
</x-layouts.user_layout>
