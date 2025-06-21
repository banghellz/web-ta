{{-- resources/views/superadmin/missing-tools/detail-partial.blade.php --}}
<div class="row">
    <div class="col-md-6">
        <h5 class="mb-3">
            <i class="ti ti-package me-2 text-primary"></i>Item Information
        </h5>
        <table class="table table-sm table-borderless">
            <tr>
                <td width="40%" class="text-muted"><strong>Item Name:</strong></td>
                <td>{{ $missingTool->nama_barang }}</td>
            </tr>
            <tr>
                <td class="text-muted"><strong>EPC Code:</strong></td>
                <td><code>{{ $missingTool->epc }}</code></td>
            </tr>
            <tr>
                <td class="text-muted"><strong>Item ID:</strong></td>
                <td>{{ $missingTool->item_id }}</td>
            </tr>
            <tr>
                <td class="text-muted"><strong>Status:</strong></td>
                <td>
                    @php
                        $badgeClass = match ($missingTool->status) {
                            'pending' => 'bg-danger',
                            'completed' => 'bg-success',
                            'cancelled' => 'bg-warning',
                            default => 'bg-secondary',
                        };

                        $statusText = match ($missingTool->status) {
                            'pending' => 'Pending',
                            'completed' => 'Reclaimed',
                            'cancelled' => 'Cancelled',
                            default => 'Unknown',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-6">
        <h5 class="mb-3">
            <i class="ti ti-user me-2 text-warning"></i>Responsible User
        </h5>

        {{-- Debug info (remove in production) --}}
        @if (config('app.debug'))
            <div class="alert alert-info small mb-2">
                <strong>Debug Info:</strong><br>
                User ID: {{ $missingTool->user_id ?? 'NULL' }}<br>
                User exists: {{ $missingTool->user ? 'Yes' : 'No' }}<br>
                User detail (via user): {{ $missingTool->user && $missingTool->user->detail ? 'Yes' : 'No' }}<br>
                UserDetail (direct): {{ $missingTool->userDetail ? 'Yes' : 'No' }}
            </div>
        @endif

        @php
            // Try multiple ways to get user detail
            $userDetail = null;
            $user = null;

            // Method 1: Direct userDetail relation
            if ($missingTool->userDetail) {
                $userDetail = $missingTool->userDetail;
                $user = $missingTool->user;
            }
            // Method 2: Via user relation
            elseif ($missingTool->user && $missingTool->user->detail) {
                $userDetail = $missingTool->user->detail;
                $user = $missingTool->user;
            }
            // Method 3: Manual query as fallback
            elseif ($missingTool->user_id) {
                $userDetail = \App\Models\UserDetail::where('user_id', $missingTool->user_id)->first();
                $user = \App\Models\User::find($missingTool->user_id);
            }
        @endphp

        @if ($userDetail && $user)
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="40%" class="text-muted"><strong>Name:</strong></td>
                    <td>{{ $userDetail->nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>NIM:</strong></td>
                    <td>{{ $userDetail->nim ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Program:</strong></td>
                    <td>{{ $userDetail->prodi ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Email:</strong></td>
                    <td>{{ $user->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Current Coins:</strong></td>
                    <td>
                        <span class="badge bg-info">
                            {{ $userDetail->koin ?? 0 }} coins
                        </span>
                    </td>
                </tr>
            </table>
        @elseif($missingTool->user_id)
            <div class="alert alert-warning mb-0">
                <i class="ti ti-alert-triangle me-2"></i>
                <strong>User ID {{ $missingTool->user_id }} found, but user details are missing.</strong><br>
                <small class="text-muted">This user may have been deleted or has incomplete profile data.</small>
            </div>
        @else
            <div class="alert alert-warning mb-0">
                <i class="ti ti-alert-triangle me-2"></i>
                <strong>No user information available</strong><br>
                <small class="text-muted">No user ID associated with this missing tool.</small>
            </div>
        @endif
    </div>
</div>

<hr class="my-4">

<div class="row">
    <div class="col-md-6">
        <h5 class="mb-3">
            <i class="ti ti-clock me-2 text-info"></i>Timeline
        </h5>
        <table class="table table-sm table-borderless">
            <tr>
                <td width="40%" class="text-muted"><strong>Reported Date:</strong></td>
                <td>
                    <span class="text-red">
                        <i class="ti ti-calendar me-1"></i>
                        {{ $missingTool->reported_at ? $missingTool->reported_at->format('d M Y, H:i') : 'N/A' }}
                    </span>
                </td>
            </tr>
            @if ($missingTool->reclaimed_at)
                <tr>
                    <td class="text-muted"><strong>{{ $missingTool->action_date_text }}:</strong></td>
                    <td>
                        @if ($missingTool->status === 'completed')
                            <span class="text-green">
                                <i class="ti ti-calendar-check me-1"></i>
                                {{ $missingTool->reclaimed_at->format('d M Y, H:i') }}
                            </span>
                        @elseif ($missingTool->status === 'cancelled')
                            <span class="text-orange">
                                <i class="ti ti-calendar-x me-1"></i>
                                {{ $missingTool->reclaimed_at->format('d M Y, H:i') }}
                            </span>
                        @endif
                    </td>
                </tr>
            @endif
            <tr>
                <td class="text-muted"><strong>Duration:</strong></td>
                <td>
                    <span class="badge bg-secondary">
                        {{ $missingTool->duration_text }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-6">
        <h5 class="mb-3">
            <i class="ti ti-info-circle me-2 text-secondary"></i>Additional Information
        </h5>
        <table class="table table-sm table-borderless">
            <tr>
                <td width="40%" class="text-muted"><strong>Missing Tool ID:</strong></td>
                <td><code>{{ $missingTool->id }}</code></td>
            </tr>
            <tr>
                <td class="text-muted"><strong>Created:</strong></td>
                <td>{{ $missingTool->created_at ? $missingTool->created_at->format('d M Y, H:i') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="text-muted"><strong>Last Updated:</strong></td>
                <td>{{ $missingTool->updated_at ? $missingTool->updated_at->format('d M Y, H:i') : 'N/A' }}</td>
            </tr>
        </table>

        @if ($missingTool->status === 'pending')
            <div class="mt-3">
                <button type="button" class="btn btn-success btn-sm btn-reclaim me-2" data-id="{{ $missingTool->id }}"
                    data-name="{{ $missingTool->nama_barang }}">
                    <i class="ti ti-check me-1"></i>Reclaim This Item
                </button>
                <button type="button" class="btn btn-warning btn-sm btn-cancel-missing"
                    data-id="{{ $missingTool->id }}" data-name="{{ $missingTool->nama_barang }}">
                    <i class="ti ti-x me-1"></i>Cancel Missing Report
                </button>
            </div>
        @endif
    </div>
</div>

@if ($missingTool->status === 'completed')
    <hr class="my-4">
    <div class="alert alert-success">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="ti ti-circle-check" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h4 class="alert-title">Item Successfully Reclaimed!</h4>
                <div class="text-muted">
                    This missing tool has been successfully reclaimed on
                    {{ $missingTool->reclaimed_at->format('d M Y, H:i') }}.
                    The responsible user has been released from their responsibility and their coins have been updated.
                </div>
            </div>
        </div>
    </div>
@elseif ($missingTool->status === 'cancelled')
    <hr class="my-4">
    <div class="alert alert-warning">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="ti ti-alert-triangle" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h4 class="alert-title">Missing Report Cancelled</h4>
                <div class="text-muted">
                    This missing tool report was cancelled on
                    {{ $missingTool->reclaimed_at->format('d M Y, H:i') }}.
                    The item has been restored to its previous status and the user's coins have been updated.
                </div>
            </div>
        </div>
    </div>
@else
    <hr class="my-4">
    <div class="alert alert-danger">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="ti ti-alert-triangle" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h4 class="alert-title">Pending Missing Item</h4>
                <div class="text-muted">
                    This item is currently marked as missing. The responsible user's coins remain affected until the
                    item is reclaimed or the report is cancelled.
                    Use the "Reclaim" button if the item was found, or "Cancel" if the report was made in error.
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    // Handle reclaim button in modal
    $(document).on('click', '.btn-reclaim', function() {
        const missingToolId = $(this).data('id');
        const itemName = $(this).data('name');

        // Close current modal first
        $('#missing-tool-detail-modal').modal('hide');

        // Show reclaim modal
        $('#reclaim-item-name').text(itemName);
        $('#confirm-reclaim-btn').data('missing-tool-id', missingToolId);
        $('#reclaim-modal').modal('show');
    });

    // Handle cancel missing button in modal
    $(document).on('click', '.btn-cancel-missing', function() {
        const missingToolId = $(this).data('id');
        const itemName = $(this).data('name');

        // Close current modal first
        $('#missing-tool-detail-modal').modal('hide');

        // Show cancel missing modal
        $('#cancel-item-name').text(itemName);
        $('#confirm-cancel-btn').data('missing-tool-id', missingToolId);
        $('#cancel-missing-modal').modal('show');
    });
</script>
