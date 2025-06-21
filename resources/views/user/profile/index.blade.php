{{-- resources/views/user/profile/index.blade.php --}}
<x-layouts.user_layout>
    <x-slot name="title">My Profile</x-slot>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Account Settings
                    </div>
                    <h2 class="page-title">
                        <i class="ti ti-user me-2"></i>My Profile
                    </h2>
                    <p class="text-muted">Edit and manage your personal information</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <span class="badge bg-blue-lt">
                            <i class="ti ti-user me-1"></i>Student
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <form action="{{ route('user.profile.update') }}" method="POST" class="card"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-user me-2"></i>Profile Information
                            </h3>
                        </div>

                        <div class="card-body">
                            <!-- Profile Picture Section -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="avatar avatar-xl mb-3"
                                            style="background-image: url({{ $user->detail && $user->detail->pict ? asset('profile_pictures/' . $user->detail->pict) : 'https://www.gravatar.com/avatar/' . md5($user->email) }})"></span>
                                    </div>
                                    <div class="col">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="pict" id="pictInput"
                                            class="form-control @error('pict') is-invalid @enderror"
                                            accept="image/jpeg,image/png,image/jpg">
                                        <div class="form-hint">Upload new photo (JPG, PNG, max 10MB)</div>
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
                                        <div class="form-hint">Name is synchronized with your Google account</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" value="{{ $user->email }}" class="form-control" readonly>
                                        <div class="form-hint">Email cannot be changed</div>
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
                                        <div class="form-hint">
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
                                                value="{{ old('no_koin', $user->detail && $user->detail->no_koin ? ltrim($user->detail->no_koin, '0') : '') }}"
                                                placeholder="188" maxlength="3" pattern="[0-9]{1,3}">
                                        </div>
                                        <div class="form-hint">Enter 3 digits (e.g., 188 becomes 0188)</div>
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
                                        <div class="form-hint">
                                            Available coins for borrowing items
                                            @if ($user->detail)
                                                ({{ $user->detail->borrowed_items_count ?? 0 }} items currently
                                                borrowed)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RFID Access Section - Read Only -->
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="mb-3">
                                        <i class="ti ti-nfc me-2"></i>RFID Access
                                    </h4>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">RFID Tag Status</label>
                                        @if ($user->detail && $user->detail->rfid_uid)
                                            @php
                                                $currentRfidTag = \App\Models\RfidTag::where(
                                                    'uid',
                                                    $user->detail->rfid_uid,
                                                )->first();
                                            @endphp
                                            <div class="input-group">
                                                <input type="text" value="{{ $user->detail->rfid_uid }}"
                                                    class="form-control" readonly>
                                                <span class="input-group-text">
                                                    <span class="badge bg-success">
                                                        <i class="ti ti-check me-1"></i>Assigned
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="form-hint">
                                                <i class="ti ti-info-circle me-1"></i>
                                                Your RFID tag is assigned and ready to use
                                            </div>

                                            <!-- RFID Status Details -->
                                            @if ($currentRfidTag)
                                                <div class="alert alert-success mt-3">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <i class="ti ti-shield-check me-2"></i>
                                                        </div>
                                                        <div>
                                                            <strong>RFID Tag Information:</strong><br>
                                                            <small class="text-muted">
                                                                UID: {{ $currentRfidTag->uid }} |
                                                                Status: <span
                                                                    class="badge bg-success-lt">{{ $currentRfidTag->status }}</span>
                                                                @if ($currentRfidTag->notes)
                                                                    | Notes: {{ $currentRfidTag->notes }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div class="input-group">
                                                <input type="text" value="Not Assigned" class="form-control"
                                                    readonly>
                                                <span class="input-group-text">
                                                    <span class="badge bg-warning">
                                                        <i class="ti ti-alert-triangle me-1"></i>Pending
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="form-hint">
                                                <i class="ti ti-info-circle me-1"></i>
                                                No RFID tag assigned yet. Contact administrator for assignment.
                                            </div>

                                            <!-- No RFID Alert -->
                                            <div class="alert alert-warning mt-3">
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <i class="ti ti-alert-triangle me-2"></i>
                                                    </div>
                                                    <div>
                                                        <strong>RFID Assignment Required</strong><br>
                                                        <small class="text-muted">
                                                            Please contact your administrator to get an RFID tag
                                                            assigned for cabinet access.
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                <a href="{{ route('user.dashboard.index') }}" class="btn btn-outline-secondary">
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
</x-layouts.user_layout>
