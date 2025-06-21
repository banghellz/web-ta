<!-- resources/views/user/complete-profile.blade.php -->
<x-complete>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lengkapi Profil
        </h2>
        <p class="mt-1 text-sm text-gray-600">Silakan lengkapi data profil untuk melanjutkan</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-6">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 p-4 rounded-md border border-red-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Terdapat {{ $errors->count() }}
                                        kesalahan pada form</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
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
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- NIM Field -->
                        <div>
                            <label for="nim" class="block text-sm font-medium text-gray-700">
                                NIM <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input id="nim" type="text" name="nim"
                                    value="{{ old('nim', $extractedNim ?? '') }}" required readonly
                                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed @error('nim') border-red-500 @enderror"
                                    placeholder="NIM akan diambil dari email Anda">
                                @error('nim')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">NIM diambil otomatis dari email mahasiswa</p>
                            </div>
                        </div>

                        <!-- Coin Number Field -->
                        <div>
                            <label for="no_koin" class="block text-sm font-medium text-gray-700">
                                Nomor Koin <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">0</span>
                                </div>
                                <input type="text" name="no_koin" id="no_koin" value="{{ old('no_koin') }}"
                                    required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full pl-6 pr-3 py-2 sm:text-sm border-gray-300 rounded-lg @error('no_koin') border-red-500 @enderror"
                                    placeholder="188" maxlength="4" pattern="[0-9]{1,4}">
                                @error('no_koin')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Masukkan 3-4 digit angka (contoh: 188 akan menjadi
                                    0188)</p>
                            </div>
                        </div>

                        <!-- Program Studi Field -->
                        <div>
                            <label for="prodi" class="block text-sm font-medium text-gray-700">
                                Program Studi <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select name="prodi" id="prodi" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-lg @error('prodi') border-red-500 @enderror">
                                    <option value="">Pilih Program Studi</option>
                                    <option value="TMK" {{ old('prodi') == 'TMK' ? 'selected' : '' }}>
                                        Teknik Mesin Konversi Energi (TMK)
                                    </option>
                                    <option value="TRMK" {{ old('prodi') == 'TRMK' ? 'selected' : '' }}>
                                        Teknologi Rekayasa Mesin Konversi Energi (TRMK)
                                    </option>
                                    <option value="TMI" {{ old('prodi') == 'TMI' ? 'selected' : '' }}>
                                        Teknik Mesin Industri (TMI)
                                    </option>
                                    <option value="RTM" {{ old('prodi') == 'RTM' ? 'selected' : '' }}>
                                        Rekayasa Teknologi Manufaktur (RTM)
                                    </option>
                                </select>
                                @error('prodi')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Profile Picture Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Foto Profil <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <img id="preview-image"
                                            class="h-20 w-20 rounded-full object-cover border-2 border-gray-200"
                                            src="{{ $userPhoto ?? asset('images/default-avatar.png') }}"
                                            alt="Preview foto profil">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <label for="pict"
                                                class="relative cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Ubah Foto</span>
                                                <input id="pict" name="pict" type="file" class="sr-only"
                                                    accept="image/jpeg,image/png,image/jpg">
                                            </label>
                                            <button type="button" id="use-google-photo"
                                                class="py-2 px-4 border border-blue-300 rounded-lg shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Gunakan Foto Google
                                            </button>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">
                                            JPG, JPEG, atau PNG. Maksimal 2MB. Foto dari Google Account akan digunakan
                                            secara default.
                                        </p>
                                        @error('pict')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('home') }}"
                                    class="bg-white py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Profil
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
            document.addEventListener('DOMContentLoaded', function() {
                const pictInput = document.getElementById('pict');
                const previewImage = document.getElementById('preview-image');
                const useGooglePhotoBtn = document.getElementById('use-google-photo');
                const noKoinInput = document.getElementById('no_koin');

                // Store original Google photo URL
                const googlePhotoUrl = '{{ $userPhoto ?? asset('images/default-avatar.png') }}';
                let isUsingGooglePhoto = true;

                // Preview uploaded image
                pictInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                            isUsingGooglePhoto = false;
                            useGooglePhotoBtn.textContent = 'Gunakan Foto Google';
                            useGooglePhotoBtn.classList.remove('bg-green-50', 'text-green-700',
                                'border-green-300');
                            useGooglePhotoBtn.classList.add('bg-blue-50', 'text-blue-700',
                                'border-blue-300');
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Use Google photo button
                useGooglePhotoBtn.addEventListener('click', function() {
                    if (!isUsingGooglePhoto) {
                        previewImage.src = googlePhotoUrl;
                        pictInput.value = '';
                        isUsingGooglePhoto = true;
                        this.textContent = 'Menggunakan Foto Google';
                        this.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-300');
                        this.classList.add('bg-green-50', 'text-green-700', 'border-green-300');
                    }
                });

                // Format no_koin input (add 0 prefix)
                noKoinInput.addEventListener('input', function(e) {
                    // Remove any non-digit characters
                    let value = e.target.value.replace(/\D/g, '');

                    // Limit to 4 digits
                    if (value.length > 4) {
                        value = value.substring(0, 4);
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

                // Initialize button state
                if (isUsingGooglePhoto) {
                    useGooglePhotoBtn.textContent = 'Menggunakan Foto Google';
                    useGooglePhotoBtn.classList.add('bg-green-50', 'text-green-700', 'border-green-300');
                }
            });
        </script>
    @endpush
</x-complete>
