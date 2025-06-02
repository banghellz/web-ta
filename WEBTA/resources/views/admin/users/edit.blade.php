<!-- resources/views/admin/users/edit.blade.php -->
<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <form action="{{ route('admin.users.update', $user->uuid) }}" method="POST" class="card">
                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h3 class="card-title">Edit User: {{ $user->name }}</h3>
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Role</label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        Admin</option>
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                        User</option>
                                    <option value="super_admin"
                                        {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <!-- Student Details Section -->
                            <div class="mt-4">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="has_student_details"
                                        name="has_student_details"
                                        {{ $user->detail || old('has_student_details') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_student_details">
                                        Student Details
                                    </label>
                                </div>
                            </div>

                            <div id="student_details_fields"
                                class="{{ $user->detail || old('has_student_details') ? '' : 'd-none' }}">
                                <h4 class="mb-3">Student Details</h4>

                                <div class="mb-3">
                                    <label class="form-label">NIM</label>
                                    <input type="text" name="nim"
                                        class="form-control @error('nim') is-invalid @enderror"
                                        value="{{ old('nim', $user->detail->nim ?? '') }}">
                                    @error('nim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

    @push('scripts')
        <script>
            $(function() {
                // Toggle student details fields when checkbox changes
                $('#has_student_details').change(function() {
                    if ($(this).is(':checked')) {
                        $('#student_details_fields').removeClass('d-none');
                    } else {
                        $('#student_details_fields').addClass('d-none');
                    }
                });

                // Toggle password visibility
                $('#togglePassword').click(function() {
                    const passwordInput = $('#password');
                    const toggleIcon = $('#toggleIcon');

                    if (passwordInput.attr('type') === 'password') {
                        passwordInput.attr('type', 'text');
                        toggleIcon.removeClass('ti-eye').addClass('ti-eye-off');
                    } else {
                        passwordInput.attr('type', 'password');
                        toggleIcon.removeClass('ti-eye-off').addClass('ti-eye');
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
