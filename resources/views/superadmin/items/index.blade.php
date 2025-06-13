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
                    <!-- Optimized status indicators for multi-tab support -->
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

                            <!-- Multi-Tab Status Indicator -->
                            <div class="col-auto">
                                <div id="tab-status-indicator" class="d-flex align-items-center me-3">
                                    <div id="tab-status-dot" class="bg-info rounded-circle me-2"
                                        style="width: 6px; height: 6px;" title="Tab Status"></div>
                                    <small class="text-muted">Tab: <span id="tab-status-text">Syncing</span></small>
                                </div>
                            </div>

                            <!-- Status Refresh Indicator (Leader Only) -->
                            <div class="col-auto">
                                <div id="status-refresh-indicator" class="d-flex align-items-center me-3"
                                    style="display: none !important;">
                                    <div id="status-refresh-dot" class="bg-success rounded-circle me-2"
                                        style="width: 6px; height: 6px;" title="Auto-Refresh"></div>
                                    <small class="text-muted">Auto: <span
                                            id="status-refresh-text">Active</span></small>
                                </div>
                            </div>

                            <!-- Connection Status Indicator -->
                            <div class="col-auto">
                                <div id="connection-status" class="d-flex align-items-center">
                                    <div id="connection-indicator" class="bg-success rounded-circle me-2"
                                        style="width: 8px; height: 8px;" title="Connection"></div>
                                    <small class="text-muted"><span id="connection-text">Live</span></small>
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
            /* Multi-tab indicators */
            #tab-status-indicator {
                display: flex;
                align-items: center;
            }

            #tab-status-indicator.leader #tab-status-dot {
                background-color: var(--tblr-blue) !important;
                animation: pulse 2s infinite;
            }

            #tab-status-indicator.follower #tab-status-dot {
                background-color: var(--tblr-green) !important;
                animation: pulse 3s infinite;
            }

            #tab-status-indicator.syncing #tab-status-dot {
                background-color: var(--tblr-yellow) !important;
                animation: pulse 1s infinite;
            }

            /* Connection status with better visual feedback */
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

            #connection-status.limited #connection-indicator {
                background-color: var(--tblr-orange) !important;
                animation: pulse 2s infinite;
            }

            /* Status refresh indicator (only visible for leader) */
            #status-refresh-indicator {
                display: none;
            }

            #status-refresh-indicator.active {
                display: flex !important;
            }

            #status-refresh-indicator.active #status-refresh-dot {
                background-color: var(--tblr-green) !important;
                animation: pulse 4s infinite;
            }

            #status-refresh-indicator.refreshing #status-refresh-dot {
                background-color: var(--tblr-blue) !important;
                animation: spin 1s linear infinite;
            }

            #status-refresh-indicator.paused #status-refresh-dot {
                background-color: var(--tblr-gray-600) !important;
                animation: none;
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
            #connection-indicator,
            #tab-status-dot,
            #status-refresh-dot {
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
                #connection-indicator,
                #tab-status-dot,
                #status-refresh-dot {
                    animation: none !important;
                }
            }

            /* Mobile responsiveness */
            @media (max-width: 768px) {

                #tab-status-indicator,
                #status-refresh-indicator {
                    display: none !important;
                }

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

            /* Dark mode compatibility */
            @media (prefers-color-scheme: dark) {
                #tab-status-indicator.leader #tab-status-dot {
                    background-color: #4dabf7 !important;
                }

                #tab-status-indicator.follower #tab-status-dot {
                    background-color: #51cf66 !important;
                }

                #tab-status-indicator.syncing #tab-status-dot {
                    background-color: #ffd43b !important;
                }

                #connection-status.connected #connection-indicator {
                    background-color: #51cf66 !important;
                }

                #connection-status.connecting #connection-indicator {
                    background-color: #ffd43b !important;
                }

                #connection-status.disconnected #connection-indicator {
                    background-color: #ff6b6b !important;
                }

                #connection-status.limited #connection-indicator {
                    background-color: #ff922b !important;
                }
            }

            /* High contrast mode */
            @media (prefers-contrast: high) {

                #tab-status-dot,
                #connection-indicator,
                #status-refresh-dot {
                    border: 1px solid currentColor;
                }

                .badge {
                    border: 1px solid currentColor;
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

                // === MULTI-TAB SUPPORT CONFIGURATION ===
                const TAB_ID = `items_tab_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                const STORAGE_KEY = 'items_realtime_data';
                const LEADER_KEY = 'items_leader_tab';
                const HEARTBEAT_KEY = 'items_heartbeat';

                let isLeaderTab = false;
                let heartbeatInterval = null;
                let leaderCheckInterval = null;

                // === OPTIMIZED POLLING CONFIGURATION ===
                let pollingInterval = null;
                let statusRefreshInterval = null;
                let clientLastUpdate = null;
                let isPollingEnabled = true;
                let isStatusRefreshEnabled = true;
                let pollingFailureCount = 0;
                let statusRefreshFailureCount = 0;

                // Debouncing and rate limiting
                let isRefreshing = false;
                let isCheckingUpdates = false;
                let lastRequestTime = 0;

                // Adaptive intervals
                let currentPollingInterval = 15000; // Start with 15 seconds
                let currentStatusInterval = 12000; // Start with 12 seconds
                const maxPollingInterval = 60000; // Max 60 seconds
                const minPollingInterval = 10000; // Min 10 seconds

                let currentStats = {
                    total_items: {{ $totalItems ?? 0 }},
                    available_items: {{ $availableItems ?? 0 }},
                    borrowed_items: {{ $borrowedItems ?? 0 }},
                    missing_items: {{ $missingItems ?? 0 }}
                };

                // === MULTI-TAB LEADERSHIP SYSTEM ===
                function becomeLeader() {
                    if (isLeaderTab) return;

                    console.log(`Tab ${TAB_ID} becoming leader`);
                    isLeaderTab = true;
                    localStorage.setItem(LEADER_KEY, TAB_ID);

                    // Update UI indicators
                    updateTabIndicator('leader');
                    setConnectionStatus('connected');
                    setStatusRefreshIndicator('active');

                    // Start leader responsibilities
                    startPollingAsLeader();
                    startHeartbeat();

                    showItemsToast('This tab is now managing real-time updates', 'info', true);
                }

                function resignLeadership() {
                    if (!isLeaderTab) return;

                    console.log(`Tab ${TAB_ID} resigning leadership`);
                    isLeaderTab = false;

                    // Stop leader activities
                    stopPolling();
                    stopHeartbeat();

                    // Update UI
                    updateTabIndicator('follower');

                    // Hide leader-only indicators
                    $('#status-refresh-indicator').hide();
                }

                function startHeartbeat() {
                    if (heartbeatInterval) clearInterval(heartbeatInterval);

                    heartbeatInterval = setInterval(() => {
                        if (isLeaderTab) {
                            localStorage.setItem(HEARTBEAT_KEY, Date.now());
                        }
                    }, 3000); // Heartbeat every 3 seconds
                }

                function stopHeartbeat() {
                    if (heartbeatInterval) {
                        clearInterval(heartbeatInterval);
                        heartbeatInterval = null;
                    }
                }

                function checkLeadership() {
                    const currentLeader = localStorage.getItem(LEADER_KEY);
                    const lastHeartbeat = parseInt(localStorage.getItem(HEARTBEAT_KEY) || '0');
                    const now = Date.now();

                    // Check if leader is still alive (heartbeat within 10 seconds)
                    const leaderAlive = (now - lastHeartbeat) < 10000;

                    if (!currentLeader || currentLeader === TAB_ID) {
                        if (!isLeaderTab) becomeLeader();
                    } else if (!leaderAlive) {
                        // Leader is dead, try to become leader
                        if (isLeaderTab) {
                            resignLeadership();
                        }

                        // Random delay to avoid conflicts
                        setTimeout(() => {
                            const newLeader = localStorage.getItem(LEADER_KEY);
                            if (!newLeader || newLeader === currentLeader) {
                                becomeLeader();
                            }
                        }, Math.random() * 2000);
                    } else if (currentLeader !== TAB_ID && isLeaderTab) {
                        // Another tab became leader
                        resignLeadership();
                    }
                }

                // === STORAGE EVENT LISTENER FOR CROSS-TAB COMMUNICATION ===
                window.addEventListener('storage', function(e) {
                    if (e.key === STORAGE_KEY && e.newValue) {
                        try {
                            const data = JSON.parse(e.newValue);
                            if (data.from !== TAB_ID) { // Don't process own messages
                                handleCrossTabUpdate(data);
                            }
                        } catch (error) {
                            console.warn('Failed to parse cross-tab data:', error);
                        }
                    } else if (e.key === LEADER_KEY) {
                        setTimeout(checkLeadership, 100); // Small delay to avoid race conditions
                    }
                });

                function handleCrossTabUpdate(data) {
                    if (isLeaderTab) return; // Leader doesn't need to sync from storage

                    console.log('Received cross-tab update:', data.type);

                    switch (data.type) {
                        case 'stats_update':
                            if (data.stats) {
                                updateStats(data.stats);
                            }
                            break;
                        case 'table_refresh':
                            performSilentRefresh();
                            break;
                        case 'status_update':
                            if (data.items && Array.isArray(data.items)) {
                                data.items.forEach(item => {
                                    const $statusBadge = $(`.badge[data-item-id="${item.id}"]`);
                                    if ($statusBadge.length > 0) {
                                        updateStatusBadge($statusBadge, item.status);
                                    }
                                });
                            }
                            break;
                        case 'item_added':
                        case 'item_updated':
                        case 'item_deleted':
                            performSilentRefresh();
                            if (data.stats) {
                                updateStats(data.stats);
                            }
                            break;
                    }

                    updateLastRefreshTime();
                    setConnectionStatus('connected');
                }

                function broadcastToOtherTabs(data) {
                    if (!isLeaderTab) return;

                    try {
                        localStorage.setItem(STORAGE_KEY, JSON.stringify({
                            ...data,
                            timestamp: Date.now(),
                            from: TAB_ID
                        }));
                    } catch (error) {
                        console.warn('Failed to broadcast to other tabs:', error);
                    }
                }

                // === OPTIMIZED AJAX WITH BETTER ERROR HANDLING ===
                function makeOptimizedRequest(url, options = {}) {
                    const defaultOptions = {
                        timeout: 8000,
                        retries: 2,
                        retryDelay: 1000
                    };

                    const config = {
                        ...defaultOptions,
                        ...options
                    };

                    // Rate limiting - prevent too frequent requests
                    const now = Date.now();
                    if (now - lastRequestTime < 2000) {
                        return Promise.reject(new Error('Rate limited'));
                    }
                    lastRequestTime = now;

                    // Circuit breaker
                    if (pollingFailureCount >= 5) {
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

                                    // Don't retry on client errors (4xx)
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

                // === ADAPTIVE POLLING SYSTEM ===
                function adjustPollingInterval(success) {
                    const oldInterval = currentPollingInterval;

                    if (success) {
                        // Gradually decrease interval on success (faster polling)
                        currentPollingInterval = Math.max(minPollingInterval, currentPollingInterval - 2000);
                        pollingFailureCount = Math.max(0, pollingFailureCount - 1);
                    } else {
                        // Increase interval on failure (slower polling)
                        currentPollingInterval = Math.min(maxPollingInterval, currentPollingInterval + 5000);
                        pollingFailureCount++;
                    }

                    if (oldInterval !== currentPollingInterval) {
                        console.log(
                            `Polling interval adjusted to ${currentPollingInterval}ms (failures: ${pollingFailureCount})`
                        );
                        // Restart polling with new interval
                        if (isLeaderTab) {
                            startPollingAsLeader();
                        }
                    }
                }

                function startPollingAsLeader() {
                    if (!isLeaderTab) return;

                    stopPolling(); // Clear any existing intervals

                    console.log(
                        `Starting leader polling - DB check: ${currentPollingInterval}ms, Status: ${currentStatusInterval}ms`
                    );

                    // Start database update polling
                    pollingInterval = setInterval(() => {
                        if (!isLeaderTab || !isPollingEnabled || document.hidden) return;
                        checkForDatabaseUpdates();
                    }, currentPollingInterval);

                    // Start status refresh polling
                    statusRefreshInterval = setInterval(() => {
                        if (!isLeaderTab || !isStatusRefreshEnabled || document.hidden) return;
                        refreshStatusOnly();
                    }, currentStatusInterval);
                }

                function stopPolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                    }
                    if (statusRefreshInterval) {
                        clearInterval(statusRefreshInterval);
                        statusRefreshInterval = null;
                    }
                }

                function checkForDatabaseUpdates() {
                    if (isCheckingUpdates || !isLeaderTab) return;

                    console.log('Leader checking for database updates...');
                    setConnectionStatus('connecting');
                    isCheckingUpdates = true;

                    makeOptimizedRequest("{{ route('superadmin.items.check-updates') }}", {
                            ajaxOptions: {
                                type: 'GET',
                                data: {
                                    last_update: clientLastUpdate
                                }
                            }
                        })
                        .then(response => {
                            console.log('Update check response:', response);

                            adjustPollingInterval(true);
                            setConnectionStatus('connected');

                            if (response.has_updates) {
                                console.log('Changes detected - broadcasting to other tabs');

                                // Broadcast to other tabs
                                broadcastToOtherTabs({
                                    type: 'table_refresh',
                                    updates: response.updates
                                });

                                // Refresh leader tab
                                performSilentRefresh();

                                if (response.stats) {
                                    updateStats(response.stats);
                                    broadcastToOtherTabs({
                                        type: 'stats_update',
                                        stats: response.stats
                                    });
                                }
                            }

                            // Update timestamp
                            if (response.latest_db_update) {
                                clientLastUpdate = response.latest_db_update;
                            }
                        })
                        .catch(xhr => {
                            adjustPollingInterval(false);

                            if (pollingFailureCount >= 5) {
                                setConnectionStatus('disconnected');
                                showItemsToast('Connection issues detected. Reducing polling frequency.', 'warning',
                                    true);
                            } else {
                                setConnectionStatus('limited');
                            }
                        })
                        .finally(() => {
                            isCheckingUpdates = false;
                        });
                }

                function refreshStatusOnly() {
                    if (isRefreshing || !isLeaderTab) return;

                    console.log('Leader refreshing status fields...');
                    setStatusRefreshIndicator('refreshing');
                    isRefreshing = true;

                    makeOptimizedRequest("{{ route('superadmin.items.data') }}", {
                            timeout: 6000,
                            ajaxOptions: {
                                type: 'GET',
                                data: {
                                    status_only: true,
                                    _: Date.now()
                                }
                            }
                        })
                        .then(response => {
                            if (response.data && Array.isArray(response.data)) {
                                let updatedItems = [];

                                response.data.forEach(item => {
                                    const $statusBadge = $(`.badge[data-item-id="${item.id}"]`);
                                    if ($statusBadge.length > 0) {
                                        const currentStatus = $statusBadge.data('status');
                                        if (currentStatus !== item.status) {
                                            updateStatusBadge($statusBadge, item.status);
                                            updatedItems.push({
                                                id: item.id,
                                                status: item.status
                                            });
                                        }
                                    }
                                });

                                // Broadcast status updates to other tabs
                                if (updatedItems.length > 0) {
                                    broadcastToOtherTabs({
                                        type: 'status_update',
                                        items: updatedItems
                                    });
                                }

                                if (response.stats) {
                                    updateStats(response.stats);
                                    broadcastToOtherTabs({
                                        type: 'stats_update',
                                        stats: response.stats
                                    });
                                }
                            }

                            statusRefreshFailureCount = 0;
                            setStatusRefreshIndicator('active');
                        })
                        .catch(xhr => {
                            statusRefreshFailureCount++;
                            if (statusRefreshFailureCount >= 3) {
                                setStatusRefreshIndicator('paused');
                            }
                        })
                        .finally(() => {
                            isRefreshing = false;
                        });
                }

                // === UTILITY FUNCTIONS ===
                function showItemsToast(message, type = 'success', skipAutoUpdate = false) {
                    if (skipAutoUpdate) {
                        console.log('Auto-update (silent):', message);
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

                function refreshNotifications() {
                    if (typeof window.refreshNotifications === 'function') {
                        window.refreshNotifications();
                    }
                }

                function updateTabIndicator(role) {
                    const $indicator = $('#tab-status-indicator');
                    const $text = $('#tab-status-text');
                    const $refreshIndicator = $('#status-refresh-indicator');

                    $indicator.removeClass('leader follower syncing').addClass(role);

                    const config = {
                        'leader': {
                            text: 'Leader',
                            title: 'This tab is managing real-time updates for all tabs',
                            showRefresh: true
                        },
                        'follower': {
                            text: 'Synced',
                            title: 'This tab is receiving updates from the leader tab',
                            showRefresh: false
                        },
                        'syncing': {
                            text: 'Syncing',
                            title: 'Synchronizing with leader tab...',
                            showRefresh: false
                        }
                    };

                    const roleConfig = config[role];
                    if (roleConfig) {
                        $text.text(roleConfig.text);
                        $indicator.attr('title', roleConfig.title);

                        // Show/hide refresh indicator based on leadership
                        if (roleConfig.showRefresh) {
                            $refreshIndicator.show();
                        } else {
                            $refreshIndicator.hide();
                        }
                    }
                }

                function setConnectionStatus(status) {
                    const $indicator = $('#connection-indicator');
                    const $statusContainer = $('#connection-status');
                    const $text = $('#connection-text');

                    $statusContainer.removeClass('connected connecting disconnected limited').addClass(status);

                    const statusConfig = {
                        'connected': {
                            title: isLeaderTab ? 'Leader tab - Managing live updates' : 'Syncing with leader tab',
                            text: isLeaderTab ? 'Live (Leader)' : 'Live (Sync)'
                        },
                        'connecting': {
                            title: 'Checking for updates...',
                            text: 'Checking'
                        },
                        'disconnected': {
                            title: 'Connection failed - Updates disabled',
                            text: 'Offline'
                        },
                        'limited': {
                            title: 'Limited connection - Reduced frequency',
                            text: 'Limited'
                        }
                    };

                    const config = statusConfig[status];
                    if (config) {
                        $indicator.attr('title', config.title);
                        $text.text(config.text);
                    }
                }

                function setStatusRefreshIndicator(status) {
                    if (!isLeaderTab) return;

                    const $indicator = $('#status-refresh-dot');
                    const $container = $('#status-refresh-indicator');
                    const $text = $('#status-refresh-text');

                    $container.removeClass('active refreshing paused').addClass(status);

                    const statusConfig = {
                        'active': {
                            title: 'Status auto-refresh active',
                            text: 'Active'
                        },
                        'refreshing': {
                            title: 'Refreshing status data...',
                            text: 'Updating...'
                        },
                        'paused': {
                            title: 'Status auto-refresh paused',
                            text: 'Paused'
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

                function performSilentRefresh() {
                    console.log('Performing silent table refresh...');

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
                        showItemsToast('Data refreshed successfully!', 'success');
                        updateLastRefreshTime();

                        if (json && (json.refresh_timestamp || json.last_db_update)) {
                            clientLastUpdate = json.refresh_timestamp || json.last_db_update;
                        }

                        // Broadcast to other tabs if leader
                        if (isLeaderTab) {
                            broadcastToOtherTabs({
                                type: 'table_refresh',
                                manual: true
                            });
                        }
                    }, false);
                }

                function triggerImmediateUpdate() {
                    console.log('Triggering immediate update...');

                    if (isLeaderTab) {
                        performSilentRefresh();
                        setTimeout(() => {
                            if (!isRefreshing) refreshStatusOnly();
                        }, 1000);

                        // Broadcast to other tabs
                        broadcastToOtherTabs({
                            type: 'table_refresh',
                            immediate: true
                        });
                    } else {
                        // Non-leader tabs just refresh themselves
                        performSilentRefresh();
                    }
                }

                // === DATATABLE INITIALIZATION ===
                const table = $('#itemsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('superadmin.items.data') }}",
                        type: 'GET',
                        timeout: 15000,
                        dataSrc: function(json) {
                            console.log('DataTable loaded successfully');

                            updateStats(json.stats || {});
                            updateLastRefreshTime();
                            setConnectionStatus('connected');

                            // Initialize clientLastUpdate
                            if (!clientLastUpdate && (json.refresh_timestamp || json.last_db_update)) {
                                clientLastUpdate = json.refresh_timestamp || json.last_db_update;
                                console.log('Initialized clientLastUpdate:', clientLastUpdate);
                            }

                            pollingFailureCount = 0;
                            return json.data;
                        },
                        error: function(xhr, error, code) {
                            console.error('DataTable Ajax Error:', {
                                status: xhr.status,
                                error: error,
                                code: code
                            });
                            setConnectionStatus('disconnected');
                            pollingFailureCount++;

                            if (pollingFailureCount >= 3) {
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
                        console.log('Page hidden - pausing activities');
                        isPollingEnabled = false;
                        isStatusRefreshEnabled = false;
                        if (isLeaderTab) {
                            setStatusRefreshIndicator('paused');
                        }
                    } else {
                        console.log('Page visible - resuming activities');
                        isPollingEnabled = true;
                        isStatusRefreshEnabled = true;
                        pollingFailureCount = Math.max(0, pollingFailureCount - 1);
                        statusRefreshFailureCount = 0;

                        setConnectionStatus('connecting');
                        if (isLeaderTab) {
                            setStatusRefreshIndicator('active');

                            // Resume polling after a short delay
                            setTimeout(() => {
                                if (!isCheckingUpdates) checkForDatabaseUpdates();
                                if (!isRefreshing) refreshStatusOnly();
                            }, 2000);
                        }
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
                        url: `/superadmin/items/${itemId}`,
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
                        timeout: 15000,
                        success: function(response) {
                            if (response.success) {
                                showItemsToast(response.message || 'Item deleted successfully!',
                                    'success');
                                refreshNotifications();

                                if (response.trigger_refresh) {
                                    triggerImmediateUpdate();

                                    // Broadcast deletion to other tabs
                                    if (isLeaderTab) {
                                        broadcastToOtherTabs({
                                            type: 'item_deleted',
                                            item_id: itemToDelete.id,
                                            stats: response.stats
                                        });
                                    }
                                }
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
                        timeout: 15000,
                        success: function(response) {
                            if (response.success) {
                                showItemsToast(response.message ||
                                    'Item marked as missing successfully!', 'warning');
                                refreshNotifications();
                                triggerImmediateUpdate();

                                // Broadcast to other tabs
                                if (isLeaderTab) {
                                    broadcastToOtherTabs({
                                        type: 'item_updated',
                                        item_id: itemToMarkMissing.id,
                                        stats: response.stats
                                    });
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
                    triggerImmediateUpdate();

                    // Broadcast to other tabs
                    if (isLeaderTab) {
                        broadcastToOtherTabs({
                            type: 'item_added',
                            item: data,
                            stats: data.stats
                        });
                    }
                });

                // === WINDOW UNLOAD HANDLING ===
                window.addEventListener('beforeunload', function() {
                    resignLeadership();
                    stopPolling();
                    stopHeartbeat();

                    if (leaderCheckInterval) {
                        clearInterval(leaderCheckInterval);
                    }
                });

                // === INITIALIZATION ===
                updateLastRefreshTime();

                // Initialize tab indicator
                updateTabIndicator('syncing');
                setConnectionStatus('connecting');

                // Start leadership system
                leaderCheckInterval = setInterval(checkLeadership, 5000);
                checkLeadership(); // Initial check

                // Show Laravel session messages
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

                // === GLOBAL FUNCTIONS ===
                window.refreshItemsTable = function(silent = true) {
                    if ($('#itemsTable').DataTable()) {
                        $('#itemsTable').DataTable().ajax.reload(null, false);
                    }
                };

                window.debugRealTimeMultiTab = function() {
                    console.log('=== MULTI-TAB REAL-TIME DEBUG INFO ===');
                    console.log('Tab ID:', TAB_ID);
                    console.log('Is Leader:', isLeaderTab);
                    console.log('Leader in Storage:', localStorage.getItem(LEADER_KEY));
                    console.log('Last Heartbeat:', new Date(parseInt(localStorage.getItem(HEARTBEAT_KEY) || '0')));
                    console.log('Client Last Update:', clientLastUpdate);
                    console.log('Polling Interval:', currentPollingInterval);
                    console.log('Failure Counts:', {
                        polling: pollingFailureCount,
                        status: statusRefreshFailureCount
                    });
                    console.log('Active Intervals:', {
                        polling: !!pollingInterval,
                        status: !!statusRefreshInterval,
                        heartbeat: !!heartbeatInterval
                    });
                };

                console.log(`Multi-tab real-time system initialized for tab ${TAB_ID}`);
            });
        </script>
    @endpush
</x-layouts.superadmin_layout>
