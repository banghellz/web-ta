{{-- resources/views/superadmin/profile/index.blade.php --}}
<x-layouts.superadmin_layout>
    <x-slot name="title">My Profile - SuperAdmin</x-slot>
    <x-slot name="content">Edit and manage your personal information</x-slot>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <form action="{{ route('superadmin.profile.update') }}" method="POST" class="card"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-user me-2"></i>My Profile
                            </h3>
                            <div class="card-actions">
                                <span class="badge bg-danger">
                                    <i class="ti ti-crown me-1"></i>Super Admin
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Profile Picture Section -->
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

                            <!-- Account Information - Read Only -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3">
                                        <i class="ti ti-user-circle me-2"></i>Account Information
                                    </h4>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" value="{{ $user->name }}" class="form-control" readonly>
                                        <div class="text-muted mt-1">Name is synchronized with your Google account</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" value="{{ $user->email }}" class="form-control" readonly>
                                        <div class="text-muted mt-1">Email cannot be changed</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Details Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3">
                                        <i class="ti ti-school me-2"></i>Student Details
                                    </h4>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">NIM (Student ID)</label>
                                        <input type="text" value="{{ $user->detail->nim ?? 'Not set' }}"
                                            class="form-control" readonly>
                                        <div class="text-muted mt-1">
                                            @if ($user->detail && $user->detail->nim)
                                                NIM is set during registration and cannot be changed
                                            @else
                                                NIM can only be set during initial profile completion
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Coin Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text">0</span>
                                            <input type="text" name="no_koin" id="no_koin"
                                                class="form-control @error('no_koin') is-invalid @enderror"
                                                value="{{ old('no_koin', $user->detail ? substr($user->detail->no_koin, 1) : '') }}"
                                                placeholder="188" maxlength="3" pattern="[0-9]{1,3}">
                                        </div>
                                        <div class="text-muted mt-1">Enter 3 digits (e.g., 188 becomes 0188)</div>
                                        @error('no_koin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Study Program (Prodi)</label>
                                        <select name="prodi" class="form-select @error('prodi') is-invalid @enderror">
                                            <option value="">-- Select Study Program --</option>
                                            <option value="TMK"
                                                {{ old('prodi', $user->detail->prodi ?? '') == 'TMK' ? 'selected' : '' }}>
                                                Teknik Mesin Konversi Energi (TMK)
                                            </option>
                                            <option value="TRMK"
                                                {{ old('prodi', $user->detail->prodi ?? '') == 'TRMK' ? 'selected' : '' }}>
                                                Teknologi Rekayasa Mesin Konversi Energi (TRMK)
                                            </option>
                                            <option value="TMI"
                                                {{ old('prodi', $user->detail->prodi ?? '') == 'TMI' ? 'selected' : '' }}>
                                                Teknik Mesin Industri (TMI)
                                            </option>
                                            <option value="RTM"
                                                {{ old('prodi', $user->detail->prodi ?? '') == 'RTM' ? 'selected' : '' }}>
                                                Rekayasa Teknologi Manufaktur (RTM)
                                            </option>
                                        </select>
                                        @error('prodi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Available Coins</label>
                                        <div class="input-group">
                                            <input type="text"
                                                value="{{ $user->detail ? $user->detail->koin : '10' }}"
                                                class="form-control" readonly>
                                            <span class="input-group-text">
                                                <i class="ti ti-coin text-yellow"></i>
                                            </span>
                                        </div>
                                        <div class="text-muted mt-1">
                                            Available coins for borrowing items
                                            @if ($user->detail)
                                                ({{ $user->detail->borrowed_items_count ?? 0 }} items currently
                                                borrowed)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RFID Tag Section -->
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="mb-3">
                                        <i class="ti ti-nfc me-2"></i>RFID Access
                                    </h4>
                                </div>
                                <div class="col-md-8">
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
                                                    <i class="ti ti-refresh me-1"></i>Refresh
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
                                </div>
                            </div>

                            <!-- Current RFID Status Info -->
                            @if ($user->detail && $user->detail->rfid_uid)
                                @php
                                    $currentRfidTag = \App\Models\RfidTag::where(
                                        'uid',
                                        $user->detail->rfid_uid,
                                    )->first();
                                @endphp
                                @if ($currentRfidTag)
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <i class="ti ti-info-circle me-2"></i>
                                                    </div>
                                                    <div>
                                                        <strong>Current RFID Tag Status:</strong><br>
                                                        UID: {{ $currentRfidTag->uid }} |
                                                        Status: <span
                                                            class="badge bg-{{ $currentRfidTag->status === 'Used' ? 'success' : ($currentRfidTag->status === 'Available' ? 'primary' : 'danger') }}">{{ $currentRfidTag->status }}</span>
                                                        @if ($currentRfidTag->notes)
                                                            | Notes: {{ $currentRfidTag->notes }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                <a href="{{ route('superadmin.dashboard.index') }}"
                                    class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
                                </a>
                            </div>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-1">
                                    <i class="ti ti-refresh me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Profile functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const refreshButton = document.getElementById('refresh-rfid-tags');
            const rfidSelect = document.getElementById('rfid_uid');
            const noKoinInput = document.getElementById('no_koin');
            const form = document.querySelector('form');

            // Format no_koin input
            if (noKoinInput) {
                noKoinInput.addEventListener('input', function(e) {
                    // Remove any non-digit characters
                    let value = e.target.value.replace(/\D/g, '');

                    // Limit to 3 digits
                    if (value.length > 3) {
                        value = value.substring(0, 3);
                    }

                    e.target.value = value;
                });

                // Format on form submission
                form.addEventListener('submit', function(e) {
                    const noKoinValue = noKoinInput.value;
                    if (noKoinValue && noKoinValue.length > 0) {
                        // Pad with zeros to make it 4 digits (including the prefix 0)
                        const paddedValue = noKoinValue.padStart(3, '0');
                        noKoinInput.value = paddedValue;
                    }
                });
            }

            // Handle form submission with AJAX and toast
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const formData = new FormData(form);

                    // Show loading state
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="ti ti-loader me-1"></i>Updating...';
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
                                    window.location.reload();
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
                                window.UnifiedToastSystem.error(
                                    'An error occurred while updating profile');
                            } else {
                                showToast('An error occurred while updating profile', 'error');
                            }
                        })
                        .finally(() => {
                            // Reset button state
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML =
                                    '<i class="ti ti-device-floppy me-1"></i>Update Profile';
                            }
                        });
                });
            }

            // Refresh RFID tags functionality
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
                            refreshButton.innerHTML = '<i class="ti ti-refresh me-1"></i>Refresh';
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
</x-layouts.superadmin_layout>
