<!-- resources/views/user/dashboard/index.blade.php -->
<x-layouts.user_layout title="Dashboard" pageTitle="Dashboard">

    <div class="container-xl">
        <!-- Page body -->
        <div class="page-body">
            <div class="container-xl">

                <!-- Stats Cards Row -->
                <div class="row row-deck row-cards mb-4">
                    <!-- Total Coins Card -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar avatar-lg bg-yellow text-white mb-3 mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-coin">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path
                                            d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                        <path d="M12 7v10" />
                                    </svg>
                                </div>
                                <h3 class="mb-1">{{ $quickStats['coins'] ?? '7' }}</h3>
                                <div class="text-uppercase text-muted fw-bold small">TOTAL COINS</div>
                                <div class="text-muted small mt-1">Used: {{ $quickStats['coins_used'] ?? '3' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Borrowed Tool Card -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar avatar-lg bg-red text-white mb-3 mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-tool">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M7 10h3v-3l-3.5 -3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1 -3 3l-6 -6a6 6 0 0 1 -8 -8l3.5 3.5" />
                                    </svg>
                                </div>
                                <h3 class="mb-1">{{ $quickStats['tools'] ?? '3' }}</h3>
                                <div class="text-uppercase text-muted fw-bold small">BORROWED TOOL</div>
                                <div class="mt-3">
                                    <a href="#" class="btn btn-outline-primary btn-sm">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Borrowing Activity Chart -->
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20"
                                        height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="4" y1="19" x2="20" y2="19" />
                                        <polyline points="4 15 8 9 12 11 16 6 20 10" />
                                    </svg>
                                    Borrowing Activity this Month
                                </h4>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="placeholder placeholder-lg w-100" style="height: 160px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row row-deck row-cards mb-4">
                    <!-- Left Column: Reminder and Quick Action (Stacked) -->
                    <div class="col-lg-6">
                        <div class="row row-deck row-cards">
                            <!-- Reminder Card -->
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex flex-column">
                                            <h4 class="card-title mb-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20"
                                                    height="20" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <circle cx="12" cy="12" r="9" />
                                                    <path d="M12 8v4" />
                                                    <path d="M12 16h.01" />
                                                </svg>
                                                Reminder
                                            </h4>
                                            <div class="text-muted small text-start">
                                                Don't miss your returns or upcoming tasks
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if (isset($reminders) && count($reminders) > 0)
                                            <div class="divide-y">
                                                @foreach ($reminders as $reminder)
                                                    <div class="row align-items-center py-2">
                                                        <div class="col-auto">
                                                            <span class="avatar avatar-sm bg-red-lt">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                    width="16" height="16" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor"
                                                                    fill="none" stroke-linecap="round"
                                                                    stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <circle cx="12" cy="12" r="9" />
                                                                    <path d="M12 8v4" />
                                                                    <path d="M12 16h.01" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <div class="fw-medium small">{{ $reminder['message'] }}
                                                            </div>
                                                            <div class="text-muted small">{{ $reminder['time'] }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <div class="text-muted">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2"
                                                        width="32" height="32" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <circle cx="12" cy="12" r="9" />
                                                        <path d="M9 12l2 2l4 -4" />
                                                    </svg>
                                                    <div class="small">No reminders at the moment</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions Card -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex flex-column">
                                            <h4 class="card-title mb-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2"
                                                    width="20" height="20" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" />
                                                </svg>
                                                Quick Action
                                            </h4>
                                            <div class="text-muted small text-start">
                                                Instant access to key features
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Borrowing History -->
                                            <div class="col-md-6">
                                                <div class="card bg-light border-0">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="icon me-2 text-primary" width="20"
                                                                height="20" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <circle cx="12" cy="12" r="9" />
                                                                <polyline points="12 7 12 12 15 15" />
                                                            </svg>
                                                            Borrowing History
                                                        </h5>
                                                        <p class="text-muted small">Track all your past activities</p>
                                                        <a href="#" class="btn btn-outline-primary btn-sm">
                                                            View Report
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Stocks -->
                                            <div class="col-md-6">
                                                <div class="card bg-light border-0">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="icon me-2 text-primary" width="20"
                                                                height="20" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <polyline
                                                                    points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3" />
                                                                <line x1="12" y1="12" x2="20"
                                                                    y2="7.5" />
                                                                <line x1="12" y1="12" x2="12"
                                                                    y2="21" />
                                                                <line x1="12" y1="12" x2="4"
                                                                    y2="7.5" />
                                                            </svg>
                                                            Stocks
                                                        </h5>
                                                        <p class="text-muted small">Check tool availability in
                                                            real-time</p>
                                                        <a href="#" class="btn btn-outline-primary btn-sm">
                                                            View Stocks
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Recent Activity -->
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20"
                                        height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="12" cy="12" r="9" />
                                        <polyline points="12 7 12 12 15 15" />
                                    </svg>
                                    Recent Activity
                                </h4>
                            </div>
                            <div class="card-body">
                                @if (isset($recentActivities) && count($recentActivities) > 0)
                                    <div class="divide-y">
                                        @foreach ($recentActivities as $activity)
                                            <div class="row align-items-center py-2">
                                                <div class="col-auto">
                                                    <span
                                                        class="avatar avatar-sm {{ $activity['type'] == 'borrow' ? 'bg-blue-lt' : 'bg-green-lt' }}">
                                                        @if ($activity['type'] == 'borrow')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                width="16" height="16" viewBox="0 0 24 24"
                                                                stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path d="M7 10l5 5l5 -5" />
                                                                <path d="M12 15l0 -15" />
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                width="16" height="16" viewBox="0 0 24 24"
                                                                stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path d="M7 14l5 -5l5 5" />
                                                                <path d="M12 9l0 15" />
                                                            </svg>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="fw-medium small">{{ $activity['message'] }}</div>
                                                    <div class="text-muted small">{{ $activity['date'] }} •
                                                        {{ $activity['time'] }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2"
                                                width="32" height="32" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M12 7v5l3 3" />
                                            </svg>
                                            <div class="small">No recent activities</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.user_layout>
