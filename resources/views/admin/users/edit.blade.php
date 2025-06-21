<x-layouts.admin_layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <form action="{{ route('admin.users.update', $user->uuid) }}" method="POST" class="card"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h3 class="card-title">
                                Edit User: {{ $user->name }}
                                @if ($user->uuid === auth()->user()->uuid)
                                    <span class="badge bg-info ms-2">
                                        <i class="ti ti-user me-1"></i>You
                                    </span>
                                @endif
                            </h3>
                        </div>

                        <div class="card-body">
                            <!-- User Photo Section -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="avatar avatar-xl mb-3"
                                            style="background-image: url({{ $user->detail && $user->detail->pict ? asset('profile_pictures/' . $user->detail->pict) : asset('assets/img/default-avatar.png') }})"></span>
                                    </div>
                                    <div class="col">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="pict"
                                            class="form-control @error('pict') is-invalid @enderror"
                                            accept="image/jpeg,image/png,image/jpg">
                                        <div class="text-muted mt-1">Upload new photo (JPG, PNG, max 10MB)</div>
                                        @error('pict')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- User details - read only -->
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" value="{{ $user->name }}" class="form-control" readonly
                                    disabled>
                                <input type="hidden" name="name" value="{{ $user->name }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" value="{{ $user->email }}" class="form-control" readonly
                                    disabled>
                                <input type="hidden" name="email" value="{{ $user->email }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Role</label>
                                @if ($user->uuid === auth()->user()->uuid)
                                    <!-- Current user cannot change their own role -->
                                    <select class="form-select" disabled title="You cannot change your own role">
                                        <option selected>{{ ucfirst($user->role) }} (Cannot change own role)</option>
                                    </select>
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <div class="text-muted mt-1">
                                        <i class="ti ti-info-circle me-1"></i>
                                        You cannot change your own role for security reasons.
                                    </div>
                                @else
                                    <!-- Other users can have their role changed -->
                                    <select name="role" class="form-select @error('role') is-invalid @enderror"
                                        required>
                                        <option value="admin"
                                            {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                            Admin</option>
                                        <option value="user"
                                            {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                            User</option>
                                        <option value="superadmin"
                                            {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super
                                            Admin
                                        </option>
                                    </select>
                                @endif
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Student Details Section -->
                            <div class="mt-4">
                                <h4 class="mb-3">Student Details</h4>

                                <div class="mb-3">
                                    <label class="form-label">NIM</label>
                                    <input type="text" value="{{ $user->detail->nim ?? '' }}" class="form-control"
                                        readonly disabled>
                                    <input type="hidden" name="nim" value="{{ $user->detail->nim ?? '' }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Coin Number</label>
                                    <input type="text" name="no_koin"
                                        class="form-control @error('no_koin') is-invalid @enderror"
                                        value="{{ old('no_koin', $user->detail->no_koin ?? '') }}">
                                    @error('no_koin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Prodi</label>
                                    <select name="prodi" class="form-select @error('prodi') is-invalid @enderror">
                                        <option value="">-- Select Prodi --</option>
                                        <option value="TMK"
                                            {{ old('prodi', $user->detail->prodi ?? '') == 'TMK' ? 'selected' : '' }}>
                                            TMK</option>
                                        <option value="TRMK"
                                            {{ old('prodi', $user->detail->prodi ?? '') == 'TRMK' ? 'selected' : '' }}>
                                            TRMK</option>
                                        <option value="TMI"
                                            {{ old('prodi', $user->detail->prodi ?? '') == 'TMI' ? 'selected' : '' }}>
                                            TMI</option>
                                        <option value="RTM"
                                            {{ old('prodi', $user->detail->prodi ?? '') == 'RTM' ? 'selected' : '' }}>
                                            RTM</option>
                                    </select>
                                    @error('prodi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- RFID Tag Section -->
                                <div class="mb-3">
                                    <label class="form-label">RFID Tag</label>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <select name="rfid_uid" id="rfid_uid"
                                                class="form-select @error('rfid_uid') is-invalid @enderror">
                                                <option value="">-- Select RFID Tag --</option>
                                                @foreach ($availableRfidTags as $tag)
                                                    <option value="{{ $tag->uid }}"
                                                        {{ old('rfid_uid', $user->detail->rfid_uid ?? '') == $tag->uid ? 'selected' : '' }}>
                                                        {{ $tag->uid }}
                                                        @if ($tag->notes)
                                                            - {{ $tag->notes }}
                                                        @endif
                                                        @if ($tag->status === 'Used' && $tag->uid === ($user->detail->rfid_uid ?? ''))
                                                            (Current)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('rfid_uid')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="refresh-rfid-tags">
                                                <i class="ti ti-refresh me-1"></i>Refresh Tags
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-muted mt-1">
                                        @if ($user->detail && $user->detail->rfid_uid)
                                            Current RFID: <strong>{{ $user->detail->rfid_uid }}</strong>
                                        @else
                                            No RFID tag assigned
                                        @endif
                                    </div>
                                </div>

                                <!-- RFID Status Info -->
                                @if ($user->detail && $user->detail->rfid_uid)
                                    @php
                                        $currentRfidTag = \App\Models\RfidTag::where(
                                            'uid',
                                            $user->detail->rfid_uid,
                                        )->first();
                                    @endphp
                                    @if ($currentRfidTag)
                                        <div class="mb-3">
                                            <div class="alert alert-info">
                                                <div class="d-flex">
                                                    <div>
                                                        <i class="ti ti-info-circle me-2"></i>
                                                    </div>
                                                    <div>
                                                        <strong>Current RFID Tag Status:</strong><br>
                                                        UID: {{ $currentRfidTag->uid }}<br>
                                                        Status: <span
                                                            class="badge bg-{{ $currentRfidTag->status === 'Used' ? 'success' : ($currentRfidTag->status === 'Available' ? 'primary' : 'danger') }}">{{ $currentRfidTag->status }}</span><br>
                                                        @if ($currentRfidTag->notes)
                                                            Notes: {{ $currentRfidTag->notes }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Back to Users
                                </a>
                            </div>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-1">
                                    <i class="ti ti-refresh me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for RFID Tag functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const refreshButton = document.getElementById('refresh-rfid-tags');
            const rfidSelect = document.getElementById('rfid_uid');
            const form = document.querySelector('form');

            // Handle form submission with AJAX and toast
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const formData = new FormData(form);

                    // Show loading state
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="ti ti-loader me-1"></i>Saving...';
                    }

                    // Submit via AJAX
                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success toast
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.success(data.message);
                                } else {
                                    showToast(data.message, 'success');
                                }

                                // Redirect after short delay
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 1500);
                            } else {
                                // Handle validation errors
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(key => {
                                        data.errors[key].forEach(error => {
                                            if (window.UnifiedToastSystem) {
                                                window.UnifiedToastSystem.error(error);
                                            } else {
                                                showToast(error, 'error');
                                            }
                                        });
                                    });
                                } else if (data.message) {
                                    if (window.UnifiedToastSystem) {
                                        window.UnifiedToastSystem.error(data.message);
                                    } else {
                                        showToast(data.message, 'error');
                                    }
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error('An error occurred while saving');
                            } else {
                                showToast('An error occurred while saving', 'error');
                            }
                        })
                        .finally(() => {
                            // Reset button state
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML =
                                    '<i class="ti ti-device-floppy me-1"></i>Save Changes';
                            }
                        });
                });
            }

            if (refreshButton) {
                refreshButton.addEventListener('click', function() {
                    // Show loading state
                    refreshButton.disabled = true;
                    refreshButton.innerHTML = '<i class="ti ti-loader me-1"></i>Loading...';

                    // Fetch available RFID tags
                    fetch('{{ route('admin.rfid-tags.available') }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Store current selection
                                const currentValue = rfidSelect.value;

                                // Clear existing options except the first one
                                rfidSelect.innerHTML =
                                    '<option value="">-- Select RFID Tag --</option>';

                                // Add available tags
                                data.data.forEach(tag => {
                                    const option = document.createElement('option');
                                    option.value = tag.uid;
                                    option.textContent = tag.uid + (tag.notes ? ' - ' + tag
                                        .notes : '');
                                    rfidSelect.appendChild(option);
                                });

                                // Restore selection if it still exists
                                if (currentValue) {
                                    rfidSelect.value = currentValue;
                                }

                                // Use unified toast system
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.success(
                                        'RFID tags refreshed successfully');
                                } else {
                                    showToast('RFID tags refreshed successfully', 'success');
                                }
                            } else {
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.error('Failed to refresh RFID tags');
                                } else {
                                    showToast('Failed to refresh RFID tags', 'error');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error('Error refreshing RFID tags');
                            } else {
                                showToast('Error refreshing RFID tags', 'error');
                            }
                        })
                        .finally(() => {
                            // Reset button state
                            refreshButton.disabled = false;
                            refreshButton.innerHTML = '<i class="ti ti-refresh me-1"></i>Refresh Tags';
                        });
                });
            }
        });

        // Fallback toast function if UnifiedToastSystem is not available
        function showToast(message, type) {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'warning'} alert-dismissible fade show`;
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }
    </script>
</x-layouts.admin_layout>
