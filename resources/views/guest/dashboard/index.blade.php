<x-layouts.guest_layout title="Dashboard">

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="mb-1" style="color: var(--tblr-body-color); font-weight: 700;">
                        Welcome back, {{ auth()->user()->name ?? 'Guest' }}! 👋
                    </h1>
                    <p class="text-muted mb-0">Here's an overview of the tool inventory system</p>
                </div>
                <div class="d-none d-md-block">
                    <div class="text-end">
                        <div class="text-muted small">Last updated</div>
                        <div class="fw-semibold" id="last-updated-time">Just now</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5" style="gap: 1.5rem 0;">
        <!-- Total Tools -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card stats-card border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-primary text-white">
                                <i class="ti ti-package fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small text-uppercase fw-semibold mb-1">Total Tools</div>
                            <div class="h3 mb-0 fw-bold" id="total-items">
                                {{ number_format($totalItems ?? 0) }}
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="ti ti-trending-up"></i>
                                All items in system
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Tools -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card stats-card border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-success text-white">
                                <i class="ti ti-check-circle fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small text-uppercase fw-semibold mb-1">Available</div>
                            <div class="h3 mb-0 fw-bold" id="available-items">
                                {{ number_format($availableItems ?? 0) }}
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="ti ti-circle-check"></i>
                                Ready to borrow
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrowed Tools -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card stats-card border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-warning text-white">
                                <i class="ti ti-user-check fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small text-uppercase fw-semibold mb-1">Borrowed</div>
                            <div class="h3 mb-0 fw-bold" id="borrowed-items">
                                {{ number_format($borrowedItems ?? 0) }}
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="ti ti-clock"></i>
                                Currently in use
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Missing Tools -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card stats-card border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-danger text-white">
                                <i class="ti ti-alert-triangle fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small text-uppercase fw-semibold mb-1">Missing</div>
                            <div class="h3 mb-0 fw-bold" id="missing-items">
                                {{ number_format($missingItems ?? 0) }}
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="ti ti-alert-circle"></i>
                                Lost or damaged
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tools Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card table-custom border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <h3 class="card-title mb-1" style="color: var(--tblr-body-color); font-weight: 700;">
                                <i class="ti ti-list me-2" style="color: var(--tblr-primary);"></i>
                                Tools Inventory
                            </h3>
                            <p class="text-muted small mb-0">Browse through all available tools in the system</p>
                        </div>
                        <div class="d-flex gap-2">
                            <!-- Search Input -->
                            <div class="input-group" style="width: 250px;">
                                <span class="input-group-text bg-white border-end-0"
                                    style="border-color: var(--tblr-border-color);">
                                    <i class="ti ti-search text-muted"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control border-start-0"
                                    placeholder="Search tools..." style="border-color: var(--tblr-border-color);">
                            </div>
                            <!-- Status Filter -->
                            <select id="statusFilter" class="form-select"
                                style="width: 130px; border-color: var(--tblr-border-color);">
                                <option value="">All Status</option>
                                <option value="available">Available</option>
                                <option value="borrowed">Borrowed</option>
                                <option value="missing">Missing</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table id="toolsTable" class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Tool Name</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 150px;">Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
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
                // Current stats cache for real-time updates
                let currentStats = {
                    total_items: {{ $totalItems ?? 0 }},
                    available_items: {{ $availableItems ?? 0 }},
                    borrowed_items: {{ $borrowedItems ?? 0 }},
                    missing_items: {{ $missingItems ?? 0 }}
                };

                // === ADVANCED STATUS-ONLY POLLING SYSTEM ===
                let pollingInterval = null;
                let isPollingEnabled = true;
                let pollingFailureCount = 0;

                const POLLING_INTERVAL = 2000; // 2 seconds for guest view
                const MAX_FAILURES = 3;

                // Store current item statuses for comparison
                let currentItemStatuses = new Map();

                // Performance tracking
                let requestTracker = {
                    durations: [],
                    requestCount: 0,
                    totalDuration: 0,
                    averageDuration: 0
                };

                // === STATUS-ONLY POLLING SYSTEM ===
                function startPolling() {
                    if (pollingInterval) clearInterval(pollingInterval);

                    console.log('Starting advanced guest status-only polling...');

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
                function checkStatusUpdates() {
                    // Record start time
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
                        url: "/guest/tools/check-status-updates",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            current_statuses: currentStatusesObj
                        },
                        timeout: 8000,
                        success: function(response) {
                            // Calculate request duration in seconds
                            const checkEndTime = performance.now();
                            const requestDuration = ((checkEndTime - checkStartTime) / 1000).toFixed(3);

                            // Track request duration for average calculation
                            const durationSec = parseFloat(requestDuration);
                            requestTracker.durations.push(durationSec);
                            requestTracker.requestCount++;
                            requestTracker.totalDuration += durationSec;

                            console.log(
                                `⏱️ Guest status check completed in ${requestDuration}s at ${timeString}`,
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
                                    `🔄 Guest status changes detected at ${changeTime} (Request: ${requestDuration}s)`
                                );

                                // Log detail perubahan untuk setiap item
                                if (response.changed_items && response.changed_items.length > 0) {
                                    console.group(
                                        `📦 ${response.changed_items.length} Tool(s) Changed - ${changeTime} [Guest View]`
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
                                            `${prevEmoji} ➡️ ${newEmoji} Tool #${item.id}: ${previousStatus} → ${item.status} (${changeTime}) [Request: ${requestDuration}s]`
                                        );
                                    });

                                    // Performance report every 10 requests
                                    if (requestTracker.requestCount % 10 === 0) {
                                        requestTracker.averageDuration = requestTracker.totalDuration /
                                            requestTracker.requestCount;
                                        const last10Average = requestTracker.durations.slice(-10).reduce((a,
                                            b) => a + b, 0) / 10;

                                        console.log(
                                            `📊 GUEST REQUEST PERFORMANCE REPORT (${requestTracker.requestCount} requests):`
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
                                // Track request duration even when no changes
                                if (requestTracker.requestCount % 10 === 0 && requestTracker.requestCount >
                                    0) {
                                    requestTracker.averageDuration = requestTracker.totalDuration /
                                        requestTracker.requestCount;
                                    const last10Average = requestTracker.durations.slice(-10).reduce((a,
                                        b) => a + b, 0) / 10;

                                    console.log(
                                        `📊 GUEST REQUEST PERFORMANCE REPORT (${requestTracker.requestCount} requests):`
                                    );
                                    console.log(
                                        `   📈 Overall Average: ${requestTracker.averageDuration.toFixed(3)}s`
                                    );
                                    console.log(
                                        `   🔟 Last 10 Requests Average: ${last10Average.toFixed(3)}s`
                                    );
                                    console.log('─'.repeat(50));
                                }

                                // Log occasional "no changes" for monitoring
                                if (Math.random() < 0.05) { // 5% chance
                                    console.log(
                                        `✅ No status changes - checked in ${requestDuration}s at ${timeString} [Guest]`
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
                                `❌ Guest status check failed after ${requestDuration}s at ${errorTime}:`,
                                xhr
                                .status, error);

                            if (pollingFailureCount >= MAX_FAILURES) {
                                console.warn(
                                    `🔌 Guest connection lost at ${errorTime} after ${MAX_FAILURES} failures. Auto-refresh disabled.`
                                );
                                showToast('Connection lost. Auto-refresh disabled.', 'warning');
                                stopPolling();

                                // Retry after delay
                                setTimeout(() => {
                                    const retryTime = new Date().toLocaleTimeString('id-ID', {
                                        hour12: false,
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    });
                                    console.log(
                                        `🔄 Guest attempting to reconnect at ${retryTime}...`);
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

                    console.log('Updating guest tool statuses:', changedItems);

                    changedItems.forEach(item => {
                        // Find the status badge for this item (guest table uses different structure)
                        const $badge = $(`.status-badge[data-item-id="${item.id}"]`);

                        if ($badge.length > 0) {
                            updateStatusBadge($badge, item.status);
                            console.log(`Updated guest tool status for item ${item.id}: ${item.status}`);
                        }
                    });

                    // Update last refresh time
                    updateLastRefreshTime();
                }

                // === UPDATE STATUS BADGE FOR GUEST VIEW ===
                function updateStatusBadge($badge, newStatus) {
                    const statusConfig = {
                        'available': {
                            icon: 'ti-check-circle',
                            class: 'status-available',
                            text: 'Available'
                        },
                        'borrowed': {
                            icon: 'ti-user-check',
                            class: 'status-borrowed',
                            text: 'Borrowed'
                        },
                        'missing': {
                            icon: 'ti-alert-triangle',
                            class: 'status-missing',
                            text: 'Missing'
                        },
                        'out_of_stock': {
                            icon: 'ti-x',
                            class: 'status-out-of-stock',
                            text: 'Out of Stock'
                        }
                    };

                    const config = statusConfig[newStatus] || {
                        icon: 'ti-help',
                        class: 'status-unknown',
                        text: 'Unknown'
                    };

                    // Add subtle animation and update status
                    $badge.addClass('status-updating')
                        .removeClass(
                            'status-available status-borrowed status-missing status-out-of-stock status-unknown')
                        .addClass(config.class)
                        .attr('data-status', newStatus)
                        .html(`<i class="ti ${config.icon} me-1"></i>${config.text}`);

                    setTimeout(() => $badge.removeClass('status-updating'), 600);
                }

                // Initialize DataTable with improved configuration
                const table = $('#toolsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/guest/tools/data",
                        type: 'GET',
                        timeout: 10000,
                        dataSrc: function(json) {
                            console.log('Guest DataTable loaded successfully');

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
                            console.error('Guest DataTable Ajax Error:', {
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
                            className: 'text-center fw-semibold'
                        },
                        {
                            data: 'nama_barang',
                            name: 'nama_barang',
                            render: function(data, type, row) {
                                return '<div class="d-flex align-items-center">' +
                                    '<div class="avatar avatar-sm me-3" style="background: var(--tblr-primary-lt); color: var(--tblr-primary);">' +
                                    '<i class="ti ti-tool"></i>' +
                                    '</div>' +
                                    '<div>' +
                                    '<div class="fw-semibold">' + data + '</div>' +
                                    '<div class="text-muted small">' + row.epc + '</div>' +
                                    '</div>' +
                                    '</div>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center',
                            render: function(data, type, row) {
                                let badgeClass = '';
                                let statusText = '';
                                let iconClass = '';

                                switch (data) {
                                    case 'available':
                                        badgeClass = 'status-available';
                                        statusText = 'Available';
                                        iconClass = 'ti-check-circle';
                                        break;
                                    case 'borrowed':
                                        badgeClass = 'status-borrowed';
                                        statusText = 'Borrowed';
                                        iconClass = 'ti-user-check';
                                        break;
                                    case 'missing':
                                        badgeClass = 'status-missing';
                                        statusText = 'Missing';
                                        iconClass = 'ti-alert-triangle';
                                        break;
                                    default:
                                        badgeClass = 'status-unknown';
                                        statusText = 'Unknown';
                                        iconClass = 'ti-help';
                                }

                                return '<span class="status-badge ' + badgeClass + '" data-item-id="' +
                                    row.id + '" data-status="' + data + '">' +
                                    '<i class="ti ' + iconClass + ' me-1"></i>' + statusText +
                                    '</span>';
                            }
                        },
                        {
                            data: 'updated_at_formatted',
                            name: 'updated_at',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<div class="text-muted small">' +
                                    '<i class="ti ti-clock me-1"></i>' + data +
                                    '</div>';
                            }
                        }
                    ],
                    order: [
                        [3, 'desc']
                    ],
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search tools...',
                        lengthMenu: 'Show _MENU_ entries',
                        processing: '<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading tools...</div>',
                        emptyTable: '<div class="text-center py-4"><i class="ti ti-package text-muted" style="font-size: 3rem;"></i><div class="h5 mt-3 text-muted">No tools found</div><p class="text-muted">Try adjusting your search or filter criteria</p></div>',
                        info: "Showing _START_ to _END_ of _TOTAL_ tools",
                        infoEmpty: "No tools to display",
                        infoFiltered: "(filtered from _MAX_ total tools)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: '<i class="ti ti-chevron-right"></i>',
                            previous: '<i class="ti ti-chevron-left"></i>'
                        }
                    },
                    responsive: true,
                    searching: true,
                    lengthChange: true,
                    info: true,
                    paging: true
                });

                // Custom search functionality
                $('#searchInput').on('keyup', function() {
                    table.search(this.value).draw();
                });

                // Status filter functionality
                $('#statusFilter').on('change', function() {
                    const selectedStatus = $(this).val();
                    table.column(2).search(selectedStatus).draw();
                });

                // Utility functions
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
                            setTimeout(() => $element.removeClass('stat-updating'), 600);
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

                // Show toast messages
                function showToast(message, type = 'success') {
                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.show(type, message);
                    } else if (typeof window.showNotificationToast === 'function') {
                        window.showNotificationToast(message, type);
                    } else {
                        console.log(`${type.toUpperCase()}: ${message}`);
                    }
                }

                // Page visibility handling
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        console.log('Guest page hidden - pausing polling');
                        isPollingEnabled = false;
                    } else {
                        console.log('Guest page visible - resuming polling');
                        isPollingEnabled = true;

                        // Check for updates immediately when page becomes visible
                        setTimeout(() => {
                            checkStatusUpdates();
                        }, 1000);
                    }
                });

                // Window unload handling
                window.addEventListener('beforeunload', function() {
                    stopPolling();
                });

                // === INITIALIZATION ===
                updateLastRefreshTime();
                startPolling();

                // Show session messages
                @if (session('success'))
                    showToast("{{ session('success') }}", 'success');
                @endif

                @if (session('error'))
                    showToast("{{ session('error') }}", 'danger');
                @endif

                // Global debug function
                window.debugGuestRealTime = function() {
                    console.log('=== GUEST STATUS-ONLY REAL-TIME DEBUG ===');
                    console.log('Polling Interval:', POLLING_INTERVAL);
                    console.log('Failure Count:', pollingFailureCount);
                    console.log('Active Polling:', !!pollingInterval);
                    console.log('Polling Enabled:', isPollingEnabled);
                    console.log('Current Tool Statuses:', Object.fromEntries(currentItemStatuses));
                    console.log('Current Stats:', currentStats);
                    console.log('Request Tracker:', requestTracker);
                };

                console.log('Advanced guest status-only real-time system initialized');
            });
        </script>
    @endpush

</x-layouts.guest_layout>
