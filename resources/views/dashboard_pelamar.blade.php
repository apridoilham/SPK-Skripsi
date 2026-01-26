<x-app-layout>
    <div class="min-h-screen py-12" x-data="{ showPdfModal: false, pdfUrl: '', showUpdateForm: {{ $errors->any() ? 'true' : 'false' }}, isSubmitting: false, newFileName: null, newFileUrl: null }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Halo, {{ Auth::user()->name }} 👋</h1>
                <p class="text-gray-500">Selamat datang di portal rekrutmen PT. BMW.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($pelamar)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h2 class="font-bold text-gray-800">Status Lamaran</h2>
                        <span class="text-xs font-mono text-gray-400">ID: #{{ $pelamar->id }}</span>
                    </div>
                    <div class="p-6">
                        <div class="rounded-lg p-4 mb-6 flex gap-4 
                            {{ $pelamar->status_lamaran == 'Lulus' ? 'bg-green-50 border border-green-200' : ($pelamar->status_lamaran == 'Gagal' ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200') }}">
                            
                            <div class="shrink-0">
                                @if($pelamar->status_lamaran == 'Lulus') <span class="text-2xl">🎉</span>
                                @elseif($pelamar->status_lamaran == 'Gagal') <span class="text-2xl">😢</span>
                                @else <span class="text-2xl">⏳</span> @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-lg 
                                    {{ $pelamar->status_lamaran == 'Lulus' ? 'text-green-800' : ($pelamar->status_lamaran == 'Gagal' ? 'text-red-800' : 'text-blue-800') }}">
                                    {{ $pelamar->status_lamaran == 'Lulus' ? 'Selamat! Anda Diterima' : ($pelamar->status_lamaran == 'Gagal' ? 'Mohon Maaf, Belum Lolos' : 'Sedang Diproses') }}
                                </h3>
                                <p class="text-sm mt-1 {{ $pelamar->status_lamaran == 'Lulus' ? 'text-green-600' : ($pelamar->status_lamaran == 'Gagal' ? 'text-red-600' : 'text-blue-600') }}">
                                    {{ $pelamar->status_lamaran == 'Lulus' ? 'HRD akan segera menghubungi Anda via email.' : ($pelamar->status_lamaran == 'Gagal' ? 'Tetap semangat! Coba lagi di kesempatan lain.' : 'Berkas Anda sedang direview oleh tim HRD.') }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Nama Terdaftar</label>
                                <p class="font-medium">{{ $pelamar->nama }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Tanggal Submit</label>
                                <p class="font-medium">{{ $pelamar->created_at->format('d F Y, H:i') }}</p>
                            </div>
                            <div class="col-span-2 mt-4">
                                <label class="block text-gray-500 text-xs font-bold uppercase mb-2">Preview Berkas</label>
                                <div class="w-full h-[500px] bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                    <iframe src="{{ route('view.pdf', $pelamar->file_berkas) }}" class="w-full h-full" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($pelamar->status_lamaran == 'Pending')
                    @if(!Auth::user()->profile_photo_path)
                         <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Untuk memperbarui lamaran, Anda wajib melengkapi foto profil terlebih dahulu.
                                        <a href="{{ route('profile.edit') }}" class="font-bold underline hover:text-yellow-800">Upload Foto Profil</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Tombol Buka Form -->
                        <div x-show="!showUpdateForm" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900 mb-1">Perbarui Lamaran</h2>
                                    <p class="text-sm text-gray-500 mb-3">Ingin mengubah data atau berkas lamaran?</p>
                                    
                                    <div class="inline-flex items-start sm:items-center gap-2 px-3 py-2 rounded-lg bg-amber-50 border border-amber-100 text-amber-800 text-xs max-w-md">
                                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5 sm:mt-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>
                                            Fitur ini hanya tersedia saat status lamaran <strong>Pending</strong>.
                                        </span>
                                    </div>
                                </div>
                                <button @click="showUpdateForm = true" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium text-sm shadow-sm ml-4 shrink-0">
                                    Perbarui Lamaran
                                </button>
                            </div>
                        </div>

                        <!-- Form Perbarui Lamaran -->
                        <div x-show="showUpdateForm" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-transition>
                            <div class="p-6 border-b border-gray-100 flex justify-between items-start">
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Perbarui Lamaran</h2>
                                    <p class="text-sm text-gray-500">Anda masih dapat mengubah data lamaran karena status masih Pending.</p>
                                </div>
                                <button @click="showUpdateForm = false" class="text-gray-400 hover:text-gray-500 transition">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="p-6">
                                <form action="{{ route('lamar.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="isSubmitting = true">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (Sesuai KTP)</label>
                                        <input type="text" name="nama" value="{{ old('nama', $pelamar->nama) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Update Berkas (PDF) - Opsional</label>
                                        
                                        <!-- Preview File Baru (Client Side) -->
                                        <div x-show="newFileName" class="mb-4" style="display: none;">
                                            <div class="flex justify-between items-center mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-gray-700">File Terpilih:</span>
                                                    <span class="text-sm text-indigo-600 font-bold" x-text="newFileName"></span>
                                                </div>
                                                <button type="button" @click="newFileName = null; newFileUrl = null; $refs.fileInput.value = '';" class="text-xs text-red-500 hover:text-red-700 hover:underline">
                                                    Hapus / Ganti File
                                                </button>
                                            </div>
                                            <div class="w-full h-[400px] bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                                <iframe :src="newFileUrl" class="w-full h-full" frameborder="0"></iframe>
                                            </div>
                                        </div>

                                        <!-- Upload Box -->
                                        <div x-show="!newFileName" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600 justify-center">
                                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                                        <span>Upload a file</span>
                                                        <input x-ref="fileInput" name="file_berkas" type="file" class="sr-only" accept=".pdf" 
                                                            @change="const file = $event.target.files[0]; if(file){ newFileName = file.name; newFileUrl = URL.createObjectURL(file); }">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah berkas.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" :disabled="isSubmitting" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span x-show="!isSubmitting">Simpan Perubahan</span>
                                        <span x-show="isSubmitting" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Menyimpan...
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif

            @else
                {{-- Form Submission Baru --}}
                @if(!Auth::user()->profile_photo_path)
                     <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-sm text-center">
                        <div class="flex flex-col items-center">
                            <div class="bg-yellow-100 p-3 rounded-full mb-3">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Foto Profil Belum Ada</h3>
                            <p class="text-gray-600 mb-4 max-w-md">
                                Mohon maaf, Anda diwajibkan untuk melengkapi foto profil terlebih dahulu sebelum dapat mengirimkan lamaran kerja.
                            </p>
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Upload Foto Profil
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-bold text-gray-900">Formulir Lamaran Kerja</h2>
                            <p class="text-sm text-gray-500">Silakan lengkapi data diri dan upload berkas terbaru.</p>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('lamar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (Sesuai KTP)</label>
                                    <input type="text" name="nama" value="{{ Auth::user()->name }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Berkas (PDF)</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                                    <span>Upload a file</span>
                                                    <input name="file_berkas" type="file" class="sr-only" accept=".pdf" required>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">PDF up to 5MB (CV, Ijazah, Transkrip)</p>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                    Kirim Lamaran
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
    <x-pdf-modal />
</x-app-layout>