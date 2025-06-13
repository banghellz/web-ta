{{-- resources/views/superadmin/rfid/partials/detail.blade.php --}}
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="ti ti-nfc me-2 text-primary"></i>
                    RFID Tag Information
                </h4>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">RFID UID</div>
                        <div class="datagrid-content">
                            <span class="badge bg-secondary">{{ $rfidTag->uid }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Tag Name/Notes</div>
                        <div class="datagrid-content">
                            {{ $rfidTag->notes ?? 'No notes provided' }}
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Status</div>
                        <div class="datagrid-content">
                            @if ($rfidTag->status === 'Available')
                                <span class="badge bg-success">
                                    <i class="ti ti-check me-1"></i>Available
                                </span>
                            @elseif($rfidTag->status === 'Used')
                                <span class="badge bg-warning">
                                    <i class="ti ti-user me-1"></i>Used
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="ti ti-help me-1"></i>Unknown
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Created At</div>
                        <div class="datagrid-content">
                            <span class="text-muted">{{ $rfidTag->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="ti ti-user me-2 text-info"></i>
                    User Assignment
                </h4>
            </div>
            <div class="card-body">
                @if ($rfidTag->userDetail && $rfidTag->userDetail->user)
                    {{-- RFID tag is assigned to a user --}}
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <h3 class="mb-0">{{ $rfidTag->userDetail->user->name }}</h3>
                            <p class="text-muted mb-0">{{ $rfidTag->userDetail->user->email }}</p>
                        </div>
                    </div>

                    <div class="datagrid">
                        @if ($rfidTag->userDetail->nim)
                            <div class="datagrid-item">
                                <div class="datagrid-title">NIM</div>
                                <div class="datagrid-content">
                                    <span class="badge bg-info">{{ $rfidTag->userDetail->nim }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($rfidTag->userDetail->prodi)
                            <div class="datagrid-item">
                                <div class="datagrid-title">Program Study</div>
                                <div class="datagrid-content">
                                    {{ $rfidTag->userDetail->prodi }}
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- RFID tag is not assigned --}}
                    <div class="empty">
                        <div class="empty-img">
                            <i class="ti ti-user-off" style="font-size: 4rem; color: var(--tblr-muted);"></i>
                        </div>
                        <p class="empty-title">No User Assigned</p>
                        <p class="empty-subtitle text-muted">
                            This RFID tag is currently not assigned to any user and is available for assignment.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize tooltips when content loads
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
