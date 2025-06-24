{{-- resources/views/user/complete-profile.blade.php --}}
<x-complete>
    <x-slot name="header">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                            Account Setup
                        </div>
                        <h2 class="page-title">
                            <i class="ti ti-user-plus me-2"></i>Complete Your Profile
                        </h2>
                        <p class="text-muted">Kindly fill in your profile details to continue</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-forms me-2"></i>Student Profile Information
                            </h3>
                        </div>

                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <div class="d-flex">
                                        <div>
                                            <i class="ti ti-alert-circle me-2"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">There are {{ $errors->count() }} errors in the form.
                                            </h4>
                                            <div class="text-secondary">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('user.complete-profile.store') }}"
                                enctype="multipart/form-data" id="profileForm">
                                @csrf

                                <!-- Profile Picture Section -->
                                <div class="mb-4">
                                    <div class="row">
                                        <div class="col-auto">
                                            <span class="avatar avatar-xl mb-3" id="preview-avatar"
                                                style="background-image: url({{ $userPhoto ?? 'https://www.gravatar.com/avatar/' . md5(auth()->user()->email ?? 'default') }})"></span>
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Profile Photo</label>
                                            <input type="file" name="pict" id="pict"
                                                class="form-control @error('pict') is-invalid @enderror"
                                                accept="image/jpeg,image/png,image/jpg">

                                            <!-- File size warning alert -->
                                            <div class="alert alert-warning mt-2 d-none" id="fileSizeWarning">
                                                <div class="d-flex">
                                                    <div>
                                                        <i class="ti ti-alert-triangle me-2"></i>
                                                    </div>
                                                    <div>
                                                        <strong>File terlalu besar!</strong>
                                                        <div class="text-secondary">
                                                            Ukuran file yang dipilih melebihi 1MB. Silakan pilih file
                                                            yang lebih kecil.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-hint">JPG, JPEG, or PNG format. Maximum file size: 1MB.
                                                Your Google Account photo will be used automatically.</div>
                                            @error('pict')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- NIM Field -->
                                <div class="mb-3">
                                    <label for="nim" class="form-label required">
                                        NIM (Student ID)
                                    </label>
                                    <input id="nim" type="text" name="nim"
                                        value="{{ old('nim', $extractedNim ?? '') }}" required readonly
                                        class="form-control @error('nim') is-invalid @enderror"
                                        placeholder="NIM akan diambil dari email Anda">
                                    @error('nim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">Your Student ID (NIM) is automatically extracted from your
                                        email.</div>
                                </div>

                                <!-- Coin Number Field -->
                                <div class="mb-3">
                                    <label for="no_koin" class="form-label required">
                                        Coin Number
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">0</span>
                                        <input type="text" name="no_koin" id="no_koin" value="{{ old('no_koin') }}"
                                            required class="form-control @error('no_koin') is-invalid @enderror"
                                            placeholder="188" maxlength="3" pattern="[0-9]{1,3}">
                                        @error('no_koin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-hint">Enter a 3-digit number (e.g., 188 will be saved as 0188).
                                    </div>
                                </div>

                                <!-- Program Studi Field -->
                                <div class="mb-3">
                                    <label for="prodi" class="form-label required">
                                        Major
                                    </label>
                                    <select name="prodi" id="prodi" required
                                        class="form-select @error('prodi') is-invalid @enderror">
                                        <option value="">Select Your Major</option>
                                        <option value="TMK" {{ old('prodi') == 'TMK' ? 'selected' : '' }}>
                                            Teknik Mekatronika (TMK)
                                        </option>
                                        <option value="TRMK" {{ old('prodi') == 'TRMK' ? 'selected' : '' }}>
                                            Teknologi Rekayasa Mekatronika (TRMK)
                                        </option>
                                        <option value="TMI" {{ old('prodi') == 'TMI' ? 'selected' : '' }}>
                                            Teknik Mesin Industri (TMI)
                                        </option>
                                        <option value="RTM" {{ old('prodi') == 'RTM' ? 'selected' : '' }}>
                                            Rekayasa Teknologi Manufaktur (RTM)
                                        </option>
                                    </select>
                                    @error('prodi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Cancel
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="ti ti-check me-1"></i>Save Profile
                                </button>
                            </div>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const pictInput = document.getElementById('pict');
                const previewAvatar = document.getElementById('preview-avatar');
                const noKoinInput = document.getElementById('no_koin');
                const fileSizeWarning = document.getElementById('fileSizeWarning');
                const submitBtn = document.getElementById('submitBtn');
                const profileForm = document.getElementById('profileForm');

                let isFileSizeValid = true;

                // Preview uploaded image and validate file size
                pictInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];

                    if (file) {
                        // Check file size (1MB = 1024 * 1024 bytes)
                        const maxSizeInBytes = 1024 * 1024; // 1MB

                        if (file.size > maxSizeInBytes) {
                            // Show warning
                            fileSizeWarning.classList.remove('d-none');
                            isFileSizeValid = false;

                            // Disable submit button
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="ti ti-alert-triangle me-1"></i>File Terlalu Besar';

                            // Clear the input
                            pictInput.value = '';

                            // Show toast notification if available
                            if (window.UnifiedToastSystem) {
                                window.UnifiedToastSystem.error(
                                    `File terlalu besar! Ukuran file: ${(file.size / (1024 * 1024)).toFixed(2)}MB. Maksimal 1MB.`
                                );
                            }

                            return;
                        } else {
                            // Hide warning and enable submit
                            fileSizeWarning.classList.add('d-none');
                            isFileSizeValid = true;
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="ti ti-check me-1"></i>Save Profile';
                        }

                        // Preview the image
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewAvatar.style.backgroundImage = `url(${e.target.result})`;
                        };
                        reader.readAsDataURL(file);

                        // Show success toast for valid file
                        if (window.UnifiedToastSystem) {
                            window.UnifiedToastSystem.success(
                                `File berhasil dipilih! Ukuran: ${(file.size / (1024 * 1024)).toFixed(2)}MB`
                            );
                        }
                    } else {
                        // Reset when no file selected
                        fileSizeWarning.classList.add('d-none');
                        isFileSizeValid = true;
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="ti ti-check me-1"></i>Save Profile';
                    }
                });

                // Format no_koin input
                noKoinInput.addEventListener('input', function(e) {
                    // Remove any non-digit characters
                    let value = e.target.value.replace(/\D/g, '');

                    // Limit to 3 digits
                    if (value.length > 3) {
                        value = value.substring(0, 3);
                    }

                    e.target.value = value;
                });

                // Handle form submission
                profileForm.addEventListener('submit', function(e) {
                    // Check if file size is valid
                    if (!isFileSizeValid) {
                        e.preventDefault();
                        if (window.UnifiedToastSystem) {
                            window.UnifiedToastSystem.error(
                                'Silakan pilih file gambar dengan ukuran maksimal 1MB.');
                        }
                        return false;
                    }

                    // Format no_koin
                    const noKoinValue = noKoinInput.value;
                    if (noKoinValue) {
                        // Pad with zeros to make it 4 digits
                        const paddedValue = noKoinValue.padStart(4, '0');
                        noKoinInput.value = paddedValue;
                    }

                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
                });

                // Utility function to format file size
                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }

                // Show success message if any
                @if (session('success'))
                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.success('{{ session('success') }}');
                    }
                @endif

                // Show error message if any
                @if (session('error'))
                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.error('{{ session('error') }}');
                    }
                @endif

                // Show email notification messages
                @if (session('email_sent'))
                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.info('{{ session('email_sent') }}');
                    }
                @endif

                @if (session('email_failed'))
                    if (window.UnifiedToastSystem) {
                        window.UnifiedToastSystem.warning('{{ session('email_failed') }}');
                    }
                @endif
            });
        </script>
    @endpush
</x-complete>
