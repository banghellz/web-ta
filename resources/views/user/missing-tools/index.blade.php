<x-layouts.user_layout title="Missing Tools" pageTitle="">
    <x-slot name="title">Missing Tools</x-slot>
    <x-slot name="content">View your missing tools history</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-alert-triangle me-2 text-warning"></i>
                        Missing Tools
                    </h2>
                    <div class="text-muted mt-1">
                        Track tools that have been marked as missing
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        <button id="refresh-btn" class="btn btn-primary">
                            <i class="ti ti-refresh me-1"></i>
                            Refresh
                        </button>
                        <a href="{{ route('user.missing-tools.export') }}" class="btn btn-outline-primary">
                            <i class="ti ti-download me-1"></i>
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="page-body">
        <div class="container-xl">
            <!-- Missing Tools Statistics -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Missing Tools Overview</h3>
                    <div class="card-actions">
                        <small class="text-muted" id="last-updated">
                            Last updated: <span id="last-updated-time">Just now</span>
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Total Missing -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-warning text-white avatar">
                                                <i class="ti ti-alert-triangle"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="total-missing">0</span>
                                                Tools
                                            </div>
                                            <div class="text-muted">
                                                Total Missing
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pending -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-danger text-white avatar">
                                                <i class="ti ti-clock"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="pending-missing">0</span>
                                                Tools
                                            </div>
                                            <div class="text-muted">
                                                Pending
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Reclaimed -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-success text-white avatar">
                                                <i class="ti ti-check"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="reclaimed-missing">0</span>
                                                Tools
                                            </div>
                                            <div class="text-muted">
                                                Reclaimed
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Cancelled -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-secondary text-white avatar">
                                                <i class="ti ti-x"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="cancelled-missing">0</span>
                                                Tools
                                            </div>
                                            <div class="text-muted">
                                                Cancelled
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
                    <h3 class="card-title">My Missing Tools</h3>
                    <div class="card-actions">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-filter"></i>
                                    </span>
                                    <select id="status-filter" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Reclaimed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="missingToolsTable" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>EPC Code</th>
                                    <th>Tool Name</th>
                                    <th>Reported Date</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Action Date</th>
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

    @push('styles')
        <style>
            /* Status badges */
            .badge-status {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }

            /* Duration badges */
            .badge-duration {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }

            .badge-duration.short {
                background-color: var(--tblr-green);
                color: white;
            }

            .badge-duration.medium {
                background-color: var(--tblr-orange);
                color: white;
            }

            .badge-duration.long {
                background-color: var(--tblr-red);
                color: white;
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

            /* Empty state styling */
            .empty-state {
                text-align: center;
                padding: 3rem 1rem;
                color: var(--tblr-muted);
            }

            .empty-state .empty-state-icon {
                width: 4rem;
                height: 4rem;
                margin: 0 auto 1rem;
                background-color: var(--tblr-muted-lt);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                color: var(--tblr-muted);
            }

            /* Refresh button animation */
            .btn.refreshing {
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(function() {
                let pollingInterval = null;
                let isPollingEnabled = true;
                let pollingFailureCount = 0;
                let currentPollingInterval = 30000; // 30 seconds

                // Current stats cache
                let currentStats = {
                    total_missing: 0,
                    pending_missing: 0,
                    reclaimed_missing: 0,
                    cancelled_missing: 0
                };

                // === UTILITY FUNCTIONS ===
                function showToast(message, type = 'success') {
                    if (typeof toastr !== 'undefined') {
                        toastr[type](message);
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
                        const currentValue = parseInt($element.text()) || 0;

                        if (currentValue !== newValue) {
                            $element.addClass('stat-updating');
                            $element.text(newValue);
                            setTimeout(() => $element.removeClass('stat-updating'), 500);
                        }
                    }

                    Object.keys(stats).forEach(key => {
                        const elementMap = {
                            'total_missing': '#total-missing',
                            'pending_missing': '#pending-missing',
                            'reclaimed_missing': '#reclaimed-missing',
                            'cancelled_missing': '#cancelled-missing'
                        };

                        const element = elementMap[key];
                        if (element && stats[key] !== undefined && stats[key] !== currentStats[key]) {
                            animateStatUpdate(element, stats[key]);
                            currentStats[key] = stats[key];
                        }
                    });
                }

                // === POLLING SYSTEM FOR LIVE UPDATES ===
                function startPolling() {
                    if (pollingInterval) clearInterval(pollingInterval);

                    pollingInterval = setInterval(() => {
                        if (!isPollingEnabled || document.hidden) return;
                        checkForUpdates();
                    }, currentPollingInterval);
                }

                function stopPolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                    }
                }

                function checkForUpdates() {
                    $.ajax({
                        url: "{{ route('user.missing-tools.data') }}",
                        type: 'GET',
                        timeout: 10000,
                        data: {
                            _: Date.now()
                        },
                        success: function(response) {
                            if (response.stats) {
                                updateStats(response.stats);
                            }

                            updateLastRefreshTime();
                            pollingFailureCount = 0;
                        },
                        error: function(xhr, status, error) {
                            pollingFailureCount++;
                            console.warn('Polling failed:', error);

                            if (pollingFailureCount >= 3) {
                                currentPollingInterval = Math.min(60000, currentPollingInterval + 10000);
                                startPolling();
                            }
                        }
                    });
                }

                // Initialize DataTable
                const table = $('#missingToolsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.missing-tools.data') }}",
                        type: 'GET',
                        dataSrc: function(json) {
                            updateStats(json.stats || {});
                            updateLastRefreshTime();
                            pollingFailureCount = 0;
                            return json.data;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTables error:', error, thrown);
                            pollingFailureCount++;
                            showToast('Error loading data. Please refresh the page.', 'error');
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
                                    '<code class="bg-light px-2 py-1 rounded">' + data + '</code>' +
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
                            data: 'reported_at_formatted',
                            name: 'reported_at',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<div class="text-muted small">' + data + '</div>';
                            }
                        },
                        {
                            data: 'status_badge',
                            name: 'status',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<span class="badge ' + data.class + '">' +
                                    '<i class="ti ti-alert-triangle me-1"></i>' + data.text +
                                    '</span>';
                            }
                        },
                        {
                            data: 'duration_days',
                            name: 'duration_days',
                            className: 'text-center',
                            render: function(data, type, row) {
                                const days = parseInt(data);
                                let badgeClass = 'short';
                                let text = row.duration_text;

                                if (days > 30) {
                                    badgeClass = 'long';
                                } else if (days > 7) {
                                    badgeClass = 'medium';
                                }

                                return '<span class="badge badge-duration ' + badgeClass + '">' + text +
                                    '</span>';
                            }
                        },
                        {
                            data: 'action_date',
                            name: 'action_date',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<div class="text-muted small">' + (data || '-') + '</div>';
                            }
                        }
                    ],
                    order: [
                        [3, 'desc']
                    ], // Order by reported_at descending
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search missing tools...',
                        lengthMenu: '_MENU_ entries per page',
                        processing: "Loading missing tools...",
                        emptyTable: `
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="ti ti-check-circle"></i>
                                </div>
                                <h3>No Missing Tools</h3>
                                <p class="text-muted">Great! You don't have any tools marked as missing.</p>
                            </div>
                        `,
                        info: "Showing _START_ to _END_ of _TOTAL_ missing tools",
                        infoEmpty: "No missing tools to show",
                        infoFiltered: "(filtered from _MAX_ total tools)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    lengthMenu: [10, 25, 50],
                    pageLength: 10,
                    responsive: true
                });

                // Handle refresh button
                $('#refresh-btn').on('click', function() {
                    const $btn = $(this);
                    $btn.addClass('refreshing');
                    $btn.prop('disabled', true);
                    $btn.html('<i class="ti ti-loader me-1"></i>Refreshing...');

                    table.ajax.reload(function() {
                        $btn.removeClass('refreshing');
                        $btn.prop('disabled', false);
                        $btn.html('<i class="ti ti-refresh me-1"></i>Refresh');
                        showToast('Data refreshed successfully!', 'success');
                    });
                });

                // Status filter
                $('#status-filter').on('change', function() {
                    const selectedStatus = $(this).val();
                    table.column(4).search(selectedStatus).draw();
                });

                // === PAGE VISIBILITY HANDLING ===
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        isPollingEnabled = false;
                    } else {
                        isPollingEnabled = true;
                        pollingFailureCount = Math.max(0, pollingFailureCount - 1);
                        setTimeout(() => {
                            checkForUpdates();
                        }, 2000);
                    }
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

                @if (session('warning'))
                    showToast("{{ session('warning') }}", 'warning');
                @endif

                // === GLOBAL FUNCTIONS ===
                window.refreshMissingToolsTable = function() {
                    if ($('#missingToolsTable').DataTable()) {
                        $('#missingToolsTable').DataTable().ajax.reload(null, false);
                    }
                };

                console.log('Missing Tools view initialized with live updates');
            });
        </script>
    @endpush
</x-layouts.user_layout>
