<x-app-layout>
    <div class="min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Halo, {{ Auth::user()->name }} 👋</h1>
                <p class="text-gray-500">Selamat datang di portal rekrutmen PT. BMW.</p>
            </div>

            @if($pelamar)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
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
                            <div class="col-span-2">
                                <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Berkas</label>
                                <a href="{{ route('view.pdf', $pelamar->file_berkas) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                                    Lihat CV / Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Formulir Lamaran Kerja</h2>
                        <p class="text-sm text-gray-500">Silakan lengkapi data diri dan upload berkas terbaru.</p>
                    </div>
                    <div class="p-6">
                        @if(session('success'))
                            <div class="mb-4 bg-green-50 text-green-700 p-3 rounded text-sm">{{ session('success') }}</div>
                        @endif

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
        </div>
    </div>
</x-app-layout>