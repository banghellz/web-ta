<!-- resources/views/admin/dashboard/index.blade.php -->
<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-activity me-2 text-primary"></i>{{ $title ?? 'Activity Logs' }}
                    </h2>
                    <div class="text-muted mt-1">{{ $content ?? 'Track user login activities' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <!-- User Count Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Users</div>
                        <div class="ms-auto lh-1">
                            <span class="text-muted">Last 7 days</span>
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $userCount ?? 0 }}</div>
                    <div class="d-flex mb-2">
                        <div>Active Users</div>
                        <div class="ms-auto">
                            <span class="text-green d-inline-flex align-items-center lh-1">
                                {{ $userCountPercentage ?? '0%' }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 17l6 -6l4 4l8 -8" />
                                    <path d="M14 7l7 0l0 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" style="width: {{ $userCountPercentage ?? '0%' }}"
                            role="progressbar" aria-valuenow="{{ $userCountPercentage ?? '0' }}" aria-valuemin="0"
                            aria-valuemax="100" aria-label="User Count">
                            <span class="visually-hidden">{{ $userCountPercentage ?? '0%' }} Complete</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- You can add more cards here for other statistics -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dashboard Overview</h3>
                </div>
                <div class="card-body">
                    <p>Welcome to your admin dashboard!</p>
                    <p>From here you can manage all aspects of your system.</p>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
