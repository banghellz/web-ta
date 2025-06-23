{{-- resources/views/user/profile/index.blade.php --}}
<x-layouts.user_layout title="Profile" pageTitle="">
    <x-slot name="title">My Profile - user</x-slot>
    <x-slot name="content">Edit and manage your personal information</x-slot>

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
                                <i class="ti ti-user me-2"></i>My Profile
                            </h3>
                            <div class="card-actions">
                                <span class="badge bg-primary">
                                    <i class="ti ti-user me-1"></i>User
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Profile Picture Section -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="avatar avatar-xl mb-3" id="preview-avatar"
                                            style="background-image: url({{ $user->detail && $user->detail->pict ? asset('profile_pictures/' . $user->detail->pict) : asset('assets/img/default-avatar.png') }})"></span>
                                    </div>
                                    <div class="col">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="pict" id="pict"
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
                                            <span class="input-group-text">α</span>
                                            <input type="text" name="no_koin" id="no_koin"
                                                class="form-control @error('no_koin') is-invalid @enderror"
                                                value="{{ old('no_koin', $user->detail && $user->detail->no_koin ? substr(str_pad($user->detail->no_koin, 4, '0', STR_PAD_LEFT), 1) : '') }}"
                                                placeholder="188" maxlength="3" pattern="[0-9]{1,3}">
                                        </div>
                                        <div class="text-muted mt-1">Enter 1-3 digits (e.g., 188 becomes α188)</div>
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

                            <!-- RFID Tag Section - Read Only for Users -->
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="mb-3">
                                        <i class="ti ti-nfc me-2"></i>RFID Access
                                    </h4>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">RFID Tag</label>
                                        <input type="text"
                                            value="{{ $user->detail && $user->detail->rfid_uid ? $user->detail->rfid_uid : 'Not assigned' }}"
                                            class="form-control" readonly>
                                        <div class="text-muted mt-1">
                                            RFID tags can only be assigned by administrators. Contact admin if you need
                                            RFID access.
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
            const pictInput = document.getElementById('pict');
            const previewAvatar = document.getElementById('preview-avatar');
            const form = document.querySelector('form');

            // Preview uploaded image dengan enhanced validation
            if (pictInput && previewAvatar) {
                pictInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        // Enhanced file validation
                        const maxSizeBytes = 10485760; // 10MB
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                        // Validate file size
                        if (file.size > maxSizeBytes) {
                            const sizeMB = (file.size / 1048576).toFixed(2);
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error(
                                    `File size (${sizeMB}MB) exceeds maximum limit of 10MB`);
                            }
                            this.value = '';
                            return;
                        }

                        // Validate file type
                        if (!allowedTypes.includes(file.type)) {
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error(
                                    `Invalid file type: ${file.type}. Only JPG, JPEG, and PNG files are allowed`
                                    );
                            }
                            this.value = '';
                            return;
                        }

                        // Show file info
                        if (window.UnifiedToastSystem) {
                            const sizeMB = (file.size / 1048576).toFixed(2);
                            window.UnifiedToastSystem.info(`Selected file: ${file.name} (${sizeMB}MB)`);
                        }

                        // Preview image
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewAvatar.style.backgroundImage = `url(${e.target.result})`;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Format no_koin input - hanya angka 1-3 digit
            if (noKoinInput) {
                const initialValue = noKoinInput.value;

                noKoinInput.addEventListener('input', function(e) {
                    // Remove any non-digit characters
                    let value = e.target.value.replace(/\D/g, '');

                    // Limit to 3 digits
                    if (value.length > 3) {
                        value = value.substring(0, 3);
                    }

                    e.target.value = value;
                });

                // Handle reset button
                const resetBtn = document.querySelector('button[type="reset"]');
                if (resetBtn) {
                    resetBtn.addEventListener('click', function() {
                        setTimeout(() => {
                            noKoinInput.value = initialValue;
                        }, 10);
                    });
                }

                // Validate on blur
                noKoinInput.addEventListener('blur', function(e) {
                    const value = e.target.value;
                    if (value && (value < 1 || value > 999)) {
                        if (window.UnifiedToastSystem) {
                            window.UnifiedToastSystem.warning('Coin number must be between 1 and 999');
                        }
                    }
                });
            }

            // Handle form submission dengan enhanced error handling - MENGGUNAKAN TOAST SYSTEM SEPERTI SUPERADMIN
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const formData = new FormData(form);

                    // Pre-submission validation
                    const noKoinValue = noKoinInput ? noKoinInput.value : '';
                    if (noKoinValue && (noKoinValue < 1 || noKoinValue > 999)) {
                        if (window.UnifiedToastSystem) {
                            window.UnifiedToastSystem.error('Coin number must be between 1 and 999');
                        }
                        return;
                    }

                    // Check file size again before submitting
                    const fileInput = document.getElementById('pict');
                    if (fileInput && fileInput.files[0]) {
                        const file = fileInput.files[0];
                        if (file.size > 10485760) {
                            const sizeMB = (file.size / 1048576).toFixed(2);
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error(
                                    `File size (${sizeMB}MB) exceeds maximum limit of 10MB`);
                            }
                            return;
                        }
                    }

                    // Show loading state
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="ti ti-loader me-1 spin"></i>Updating...';
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
                        .then(response => {
                            console.log('Response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);

                            if (data.success) {
                                // Tampilkan success toast menggunakan sistem yang sama dengan superadmin
                                if (window.UnifiedToastSystem) {
                                    window.UnifiedToastSystem.success('Profile updated successfully!');
                                }

                                // Update display elements if data provided
                                if (data.data) {
                                    // Update no_koin input
                                    if (data.data.no_koin_display && noKoinInput) {
                                        noKoinInput.value = data.data.no_koin_display;
                                    }

                                    // Update profile picture if changed
                                    if (data.data.pict_url && previewAvatar) {
                                        previewAvatar.style.backgroundImage =
                                            `url(${data.data.pict_url})`;
                                    }
                                }

                                // Refresh halaman setelah delay untuk memastikan semua data terupdate
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                // Handle validation errors
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(key => {
                                        data.errors[key].forEach(error => {
                                            if (window.UnifiedToastSystem) {
                                                window.UnifiedToastSystem.error(error);
                                            }
                                        });
                                    });
                                } else if (data.message) {
                                    if (window.UnifiedToastSystem) {
                                        window.UnifiedToastSystem.error(data.message);
                                    }
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error(
                                    'An error occurred while updating profile');
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

            // Add CSS for spinning loader
            const style = document.createElement('style');
            style.textContent = `
                .spin {
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);

            // Debug: Log when toast system is ready
            console.log('Profile page ready. Toast system available:', !!window.UnifiedToastSystem);
        });
    </script>
</x-layouts.user_layout>
