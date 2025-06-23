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
                                enctype="multipart/form-data">
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
                                            <div class="form-hint">JPG, JPEG, or PNG format. Maximum file size: 2MB.
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
                                        Nomor Koin
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
                                        Program Studi
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
                                    <i class="ti ti-arrow-left me-1"></i>Batal
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Simpan Profil
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

                // Preview uploaded image
                pictInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewAvatar.style.backgroundImage = `url(${e.target.result})`;
                        };
                        reader.readAsDataURL(file);
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

                // Handle form submission to format no_koin
                document.querySelector('form').addEventListener('submit', function(e) {
                    const noKoinValue = noKoinInput.value;
                    if (noKoinValue) {
                        // Pad with zeros to make it 4 digits
                        const paddedValue = noKoinValue.padStart(4, '0');
                        noKoinInput.value = paddedValue;
                    }
                });

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
            });
        </script>
    @endpush
</x-complete>
