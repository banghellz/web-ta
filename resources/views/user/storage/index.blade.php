<x-layouts.user_layout title="My Storage" pageTitle="">
    <x-slot name="title">My Storage</x-slot>
    <x-slot name="content">View your borrowed items</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-package me-2 text-primary"></i>
                        My Storage
                    </h2>
                    <div class="text-muted mt-1">
                        View and manage your borrowed tools
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

    {{-- Statistics Cards --}}
    <div class="page-body">
        <div class="container-xl">
            <!-- My Storage Statistics -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Storage Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Available Coins -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-yellow text-white avatar">
                                                <i class="ti ti-coin"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span
                                                    id="available-coins">{{ $userDetail ? $userDetail->koin : 10 }}</span>
                                                Coins
                                            </div>
                                            <div class="text-muted">
                                                Available Balance
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Currently Borrowed -->
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
                                                <span id="total-borrowed">0</span> Items
                                            </div>
                                            <div class="text-muted">
                                                Currently Borrowed
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Borrowed Today -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar">
                                                <i class="ti ti-calendar-today"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="borrowed-today">0</span> Items
                                            </div>
                                            <div class="text-muted">
                                                Borrowed Today
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- This Week -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-info text-white avatar">
                                                <i class="ti ti-calendar-week"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <span id="borrowed-week">0</span> Items
                                            </div>
                                            <div class="text-muted">
                                                This Week
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
                    <h3 class="card-title">My Borrowed Items</h3>
                    <div class="card-actions">
                        <small class="text-muted" id="last-updated">
                            Last updated: <span id="last-updated-time">Just now</span>
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="storageTable" class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>EPC Code</th>
                                    <th>Tool Name</th>
                                    <th>Borrowed At</th>
                                    <th>Duration</th>
                                    <th>Status</th>
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
            .badge-duration {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }

            .badge-duration.normal {
                background-color: var(--tblr-green);
                color: white;
            }

            .badge-duration.warning {
                background-color: var(--tblr-orange);
                color: white;
            }

            .badge-duration.overdue {
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
                    total_borrowed: 0,
                    borrowed_today: 0,
                    borrowed_week: 0
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
                            'total_borrowed': '#total-borrowed',
                            'borrowed_today': '#borrowed-today',
                            'borrowed_week': '#borrowed-week'
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
                        url: "{{ route('user.storage.data') }}",
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
                const table = $('#storageTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.storage.data') }}",
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
                                    '<code class="bg-light px-2 py-1 rounded">' + (data || 'N/A') +
                                    '</code>' +
                                    '</div>';
                            }
                        },
                        {
                            data: 'nama_barang',
                            name: 'nama_barang',
                            render: function(data, type, row) {
                                return '<div class="text-wrap fw-bold">' + (data || 'Unknown Tool') +
                                    '</div>';
                            }
                        },
                        {
                            data: 'borrowed_at_formatted',
                            name: 'updated_at',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<div class="text-muted small">' + (data || '-') + '</div>';
                            }
                        },
                        {
                            data: 'duration_days',
                            name: 'duration_days',
                            className: 'text-center',
                            render: function(data, type, row) {
                                const days = Math.floor(data || 0);
                                let badgeClass = 'normal';
                                let text = days + ' days';

                                if (days > 7) {
                                    badgeClass = 'overdue';
                                    text = days + ' days (Overdue)';
                                } else if (days >= 5) {
                                    badgeClass = 'warning';
                                    text = days + ' days (Due Soon)';
                                }

                                return '<span class="badge badge-duration ' + badgeClass + '">' + text +
                                    '</span>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<span class="badge bg-warning">' +
                                    '<i class="ti ti-user me-1"></i>Borrowed' +
                                    '</span>';
                            }
                        }
                    ],
                    order: [
                        [3, 'desc']
                    ], // Order by borrowed_at descending
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    language: {
                        search: '',
                        searchPlaceholder: 'Search borrowed items...',
                        lengthMenu: '_MENU_ entries per page',
                        processing: "Loading borrowed items...",
                        emptyTable: `
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="ti ti-package"></i>
                                </div>
                                <h3>No Borrowed Items</h3>
                                <p class="text-muted">You haven't borrowed any tools yet. Use the RFID system to borrow tools.</p>
                            </div>
                        `,
                        info: "Showing _START_ to _END_ of _TOTAL_ borrowed items",
                        infoEmpty: "No borrowed items to show",
                        infoFiltered: "(filtered from _MAX_ total items)",
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
                window.refreshStorageTable = function() {
                    if ($('#storageTable').DataTable()) {
                        $('#storageTable').DataTable().ajax.reload(null, false);
                    }
                };

                console.log('Storage view initialized with live updates');
            });
        </script>
    @endpush
</x-layouts.user_layout>
