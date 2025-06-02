<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="content">Smart Cabinet Dashboard</x-slot>

    <div class="page-body">
        <div class="container-xl">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <h1 class="h2 me-4 mb-0">Smart Cabinet</h1>
                    <h2 class="h3 text-muted mb-0">Dashboard</h2>
                </div>
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-sm me-2"
                        style="background-image: url('{{ Auth::user()->detail && Auth::user()->detail->pict ? asset('profile_pictures/' . Auth::user()->detail->pict) : asset('assets/img/default-avatar.png') }}')"></span>
                    <div class="d-flex flex-column">
                        <span class="fw-medium">{{ Auth::user()->name }}</span>
                        <small class="text-muted">{{ Auth::user()->detail->nim ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>

            <div class="row row-cards">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- Profile Card -->
                    <div class="card mb-4"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 200px;">
                        <div class="card-body d-flex align-items-center justify-content-center position-relative">
                            <div class="text-center text-white">
                                <div class="avatar avatar-xl mb-3 mx-auto border border-white"
                                    style="background-image: url('{{ Auth::user()->detail && Auth::user()->detail->pict ? asset('profile_pictures/' . Auth::user()->detail->pict) : asset('assets/img/default-avatar.png') }}')">
                                </div>
                                <h3 class="text-white mb-1">{{ Auth::user()->name }}</h3>
                                <p class="text-white-50 mb-0">{{ Auth::user()->detail->prodi ?? 'Student' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stats</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Tools Stats -->
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Tools</span>
                                            <span class="h2 mb-0">{{ $userStats['borrowed_tools'] ?? 0 }}</span>
                                        </div>
                                        <small
                                            class="text-muted">{{ $userStats['total_borrowed'] ?? '0' }}–{{ $userStats['max_borrow'] ?? '10' }}</small>
                                    </div>

                                    <!-- Overdue Stats -->
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Overdue</span>
                                            <span class="h2 mb-0">{{ $userStats['overdue_count'] ?? 0 }}</span>
                                        </div>
                                        <small class="text-muted">borrowing return</small>
                                    </div>

                                    <!-- Reminder -->
                                    <div class="d-flex align-items-center">
                                        <svg class="icon me-2" width="24" height="24" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                        </svg>
                                        <div>
                                            <div class="fw-medium">Reminder</div>
                                            <small class="text-muted">Return screwwriting by tomorrow</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Chart -->
                                    <div class="d-flex justify-content-center align-items-center"
                                        style="height: 200px;">
                                        <div class="position-relative">
                                            <div class="progress-circle" style="width: 120px; height: 120px;">
                                                <svg class="progress-ring" width="120" height="120">
                                                    <circle class="progress-ring-circle" stroke="#e9ecef"
                                                        stroke-width="8" fill="transparent" r="52" cx="60"
                                                        cy="60" />
                                                    <circle class="progress-ring-circle" stroke="#5c7cfa"
                                                        stroke-width="8" fill="transparent" r="52" cx="60"
                                                        cy="60" stroke-dasharray="326.7"
                                                        stroke-dashoffset="228.7" transform="rotate(-90 60 60)" />
                                                </svg>
                                                <div
                                                    class="position-absolute top-50 start-50 translate-middle text-center">
                                                    <span class="h4 mb-0">30%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tools Borrowed Stats -->
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted">Tools</span>
                                            <span class="fw-medium">30%</span>
                                        </div>
                                        <span class="small text-muted">Borrowed</span>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar" role="progressbar" style="width: 30%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <!-- Quick Stats -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Quick Stats</h3>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="mb-2">
                                        <span class="h3 mb-0">{{ $quickStats['tools'] ?? 10 }}</span>
                                    </div>
                                    <small class="text-muted">Tools</small>
                                </div>
                                <div class="col-4">
                                    <div class="mb-2">
                                        <span class="h3 mb-0">{{ $quickStats['overdue'] ?? 2 }}</span>
                                    </div>
                                    <small class="text-muted">Overdue</small>
                                </div>
                                <div class="col-4">
                                    <div class="mb-2">
                                        <span class="h3 mb-0">{{ $quickStats['coins'] ?? 50 }}</span>
                                    </div>
                                    <small class="text-muted">Coins</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Storage Card -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <svg class="icon icon-lg text-blue" width="48" height="48"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                    <path d="M12 12l8 -4.5" />
                                    <path d="M12 12l0 9" />
                                    <path d="M12 12l-8 -4.5" />
                                </svg>
                            </div>
                            <h3 class="mb-1">Your storage</h3>
                            <p class="text-muted mb-3">Supervise your all storage in the easiest way</p>
                            <div class="progress mb-2">
                                <div class="progress-bar" role="progressbar" style="width: 90%"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ $storageStats['used'] ?? 9 }} tools</small>
                                <small class="text-muted">{{ $storageStats['total'] ?? 10 }} tools</small>
                            </div>
                        </div>
                    </div>

                    <!-- Reminder Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Reminder</h3>
                        </div>
                        <div class="card-body">
                            @if (isset($reminders) && count($reminders) > 0)
                                @foreach ($reminders as $reminder)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <svg class="icon text-blue" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                            </svg>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="fw-medium">{{ $reminder['message'] }}</div>
                                            <small class="text-muted">{{ $reminder['time'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <svg class="icon text-blue" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                        </svg>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="fw-medium">Return screwdriver</div>
                                        <small class="text-muted">by tomorrow</small>
                                    </div>
                                </div>
                            @endif
                            <div class="mt-3">
                                <a href="#" class="btn btn-link btn-sm p-0 text-blue">View details</a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activity</h3>
                        </div>
                        <div class="card-body">
                            @if (isset($recentActivities) && count($recentActivities) > 0)
                                @foreach ($recentActivities as $activity)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            @if ($activity['type'] == 'borrow')
                                                <svg class="icon text-green" width="20" height="20"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 12l14 0" />
                                                    <path d="M13 18l6 -6" />
                                                    <path d="M13 6l6 6" />
                                                </svg>
                                            @else
                                                <svg class="icon text-blue" width="20" height="20"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 12l14 0" />
                                                    <path d="M5 18l6 -6" />
                                                    <path d="M5 6l6 6" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-fill">
                                            <div class="fw-medium">{{ $activity['message'] }}</div>
                                            <small class="text-muted">{{ $activity['time'] }}</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ $activity['date'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <svg class="icon text-green" width="20" height="20"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 12l14 0" />
                                            <path d="M13 18l6 -6" />
                                            <path d="M13 6l6 6" />
                                        </svg>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="fw-medium">You borrowed Soldering Iron</div>
                                        <small class="text-muted">Today, 14:30</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">Today, 14:30</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <svg class="icon text-blue" width="20" height="20"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 12l14 0" />
                                            <path d="M5 18l6 -6" />
                                            <path d="M5 6l6 6" />
                                        </svg>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="fw-medium">You returned Multimeter</div>
                                        <small class="text-muted">Today, 10:12</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">Today, 10:12</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .progress-circle {
            position: relative;
        }

        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            transform-origin: 50% 50%;
        }

        .card {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            transition: all 0.3s cubic-bezier(.25, .8, .25, 1);
        }

        .card:hover {
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
        }

        .btn-link {
            text-decoration: none;
        }

        .btn-link:hover {
            text-decoration: underline;
        }
    </style>
</x-app-layout>
