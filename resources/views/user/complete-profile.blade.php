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
                                        kesalahan pada
                                        form</h3>
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

                        <div>
                            <label for="nim"
                                class="col-md-4 col-form-label text-md-right">{{ __('NIM') }}</label>

                            <div class="mt-1">
                                <input id="nim" type="text"
                                    class="form-control @error('nim') is-invalid @enderror" name="nim"
                                    value="{{ old('nim', $extractedNim ?? '') }}" required autocomplete="nim" autofocus>

                                @error('nim')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="no_koin" class="block text-sm font-medium text-gray-700">Coin Number</label>
                            <div class="mt-1">
                                <input type="text" name="no_koin" id="no_koin" value="{{ old('no_koin') }}"
                                    required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Insert Your Coin Number">
                            </div>
                        </div>

                        <div>
                            <label for="prodi" class="block text-sm font-medium text-gray-700">Program Studi</label>
                            <div class="mt-1">
                                <select name="prodi" id="prodi" required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">Pilih Program Studi</option>
                                    <option value="TMK" {{ old('prodi') == 'TMK' ? 'selected' : '' }}>TMK
                                    </option>
                                    <option value="TRMK" {{ old('prodi') == 'TRMK' ? 'selected' : '' }}>
                                        TRMK</option>
                                    <option value="TMI" {{ old('prodi') == 'TMI' ? 'selected' : '' }}>
                                        TMI</option>
                                    <option value="RTM" {{ old('prodi') == 'RTM' ? 'selected' : '' }}>
                                        RTM</option>
                                    <!-- Tambahkan program studi lainnya sesuai kebutuhan -->
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto Profil</label>
                            <div class="mt-1 flex items-center">
                                <div
                                    class="flex-shrink-0 h-16 w-16 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                                    <img id="preview-image" class="h-full w-full object-cover"
                                        src="{{ asset('images/default-avatar.png') }}" alt="Preview">
                                </div>
                                <div class="ml-5">
                                    <div class="relative">
                                        <input type="file" name="pict" id="pict" required
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            accept="image/jpeg,image/png,image/jpg">
                                        <label for="pict"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Pilih Foto
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">JPG, JPEG, atau PNG. Maks 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-5">
                            <div class="flex justify-end">
                                <a href="{{ route('home') }}"
                                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Simpan
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
            // Script untuk preview gambar
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('pict').addEventListener('change', function(event) {
                    var reader = new FileReader();
                    reader.onload = function() {
                        document.getElementById('preview-image').src = reader.result;
                    }
                    reader.readAsDataURL(event.target.files[0]);
                });
            });
        </script>
    @endpush
    </x-app-layout>
