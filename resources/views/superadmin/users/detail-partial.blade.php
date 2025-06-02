<!-- resources/views/admin/users/detail-partial.blade.php -->
<div class="user-details">
    <div class="row g-3">
        <div class="col-12">
            <h4 class="mb-3">User Information</h4>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 30%">Name</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ ucfirst($user->role) }}</td>
                </tr>
                <tr>
                    <th>Registration Date</th>
                    <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                </tr>
                <tr>
                    <th>Last Login</th>
                    <td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Never logged in' }}</td>
                </tr>
            </table>
        </div>

        @if ($user->detail)
            <div class="col-12">
                <h4 class="mb-3">Student Details</h4>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Profile Picture</th>
                        <td>
                            @if ($user->detail->pict)
                                <img src="{{ asset('profile_pictures/' . $user->detail->pict) }}" alt="Profile Picture"
                                    class="avatar avatar-md"
                                    style="max-width: 60px; max-height: 60px; object-fit: cover;">
                            @else
                                <span class="avatar avatar-md"
                                    style="background-image: url({{ asset('assets/img/default-avatar.png') }})"></span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>NIM</th>
                        <td>{{ $user->detail->nim ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Coin Number</th>
                        <td>{{ $user->detail->no_koin ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>{{ $user->detail->prodi ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>RFID Tag</th>
                        <td>
                            @if ($user->detail->rfid_uid)
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $user->detail->rfid_uid }}</span>
                                    @php
                                        $rfidTag = \App\Models\RfidTag::where('uid', $user->detail->rfid_uid)->first();
                                    @endphp
                                    @if ($rfidTag)
                                        <span
                                            class="badge bg-{{ $rfidTag->status === 'Used' ? 'success' : ($rfidTag->status === 'Available' ? 'primary' : 'danger') }}">
                                            {{ $rfidTag->status }}
                                        </span>
                                        @if ($rfidTag->notes)
                                            <small class="text-muted ms-2">{{ $rfidTag->notes }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-warning">Tag Not Found</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">No RFID tag assigned</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- RFID Tag Details Section -->
            @if ($user->detail->rfid_uid)
                @php
                    $rfidTag = \App\Models\RfidTag::where('uid', $user->detail->rfid_uid)->first();
                @endphp
                @if ($rfidTag)
                    <div class="col-12">
                        <h4 class="mb-3">RFID Tag Details</h4>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th style="width: 40%">UID</th>
                                                <td>
                                                    <code>{{ $rfidTag->uid }}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $rfidTag->status === 'Used' ? 'success' : ($rfidTag->status === 'Available' ? 'primary' : 'danger') }}">
                                                        {{ $rfidTag->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Assigned Date</th>
                                                <td>{{ $rfidTag->updated_at->format('d M Y, H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        @if ($rfidTag->notes)
                                            <div class="mb-2">
                                                <strong>Notes:</strong>
                                                <p class="text-muted mb-0">{{ $rfidTag->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    <div class="d-flex">
                        <div>
                            <i class="ti ti-info-circle me-2"></i>
                        </div>
                        <div>
                            <strong>No student details available</strong><br>
                            This user has not completed their profile information yet.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="{{ route('superadmin.users.edit', $user->uuid) }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-edit me-1"></i>Edit User
                        </a>

                        @if (!$user->detail || !$user->detail->rfid_uid)
                            <button type="button" class="btn btn-outline-success btn-sm"
                                onclick="assignRfidTag('{{ $user->uuid }}')">
                                <i class="ti ti-tag me-1"></i>Assign RFID
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-warning btn-sm"
                                onclick="unassignRfidTag('{{ $user->uuid }}', '{{ $user->detail->rfid_uid }}')">
                                <i class="ti ti-tag-off me-1"></i>Remove RFID
                            </button>
                        @endif

                        <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="deleteUser('{{ $user->uuid }}', '{{ $user->name }}')">
                            <i class="ti ti-trash me-1"></i>Delete User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function assignRfidTag(userUuid) {
        // This function would open a modal or redirect to assign RFID
        window.location.href = `/superadmin/users/${userUuid}/edit#rfid-section`;
    }

    function unassignRfidTag(userUuid, rfidUid) {
        if (confirm(`Are you sure you want to remove RFID tag "${rfidUid}" from this user?`)) {
            // Add AJAX call to unassign RFID tag
            fetch(`/superadmin/users/${userUuid}/unassign-rfid`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to remove RFID tag');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing RFID tag');
                });
        }
    }

    function deleteUser(userUuid, userName) {
        if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
            // Add AJAX call to delete user
            fetch(`/superadmin/users/${userUuid}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/superadmin/users';
                    } else {
                        alert('Failed to delete user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting user');
                });
        }
    }
</script>
