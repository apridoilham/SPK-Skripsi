<x-app-layout>
    {{-- Centralized Configuration --}}
    <div id="pelamar-dashboard-config" data-config="{{ json_encode([
        'showUpdateForm' => $errors->any(),
        'trans' => [
            'file_too_large' => __('File size too large! Max 5MB. Please compress your PDF or use a smaller file.'),
            'saving' => __('Saving...'),
            'save_changes' => __('Save Changes')
        ]
    ]) }}"></div>

    <div class="min-h-screen py-12 bg-gray-50" x-data="pelamarDashboard">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Header -->
            <div class="mb-10 text-center sm:text-left">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('Hello') }}, {{ Auth::user()->name }} <span class="animate-pulse inline-block">👋</span></h1>
                <p class="mt-2 text-lg text-gray-600">{{ __('Welcome to the recruitment portal of') }} <span class="font-semibold text-[#232f3e]">PT. BMW</span>.</p>
            </div>

            @if(session('success'))
                <div class="mb-8 bg-white border-l-4 border-green-500 p-4 rounded-r-xl shadow-md flex items-center animate-fade-in-down">
                    <div class="flex-shrink-0 bg-green-100 p-2 rounded-full">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 bg-white border-l-4 border-red-500 p-4 rounded-r-xl shadow-md flex items-center animate-fade-in-down">
                    <div class="flex-shrink-0 bg-red-100 p-2 rounded-full">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($pelamar)
                <!-- Status Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8 transition-all duration-300 hover:shadow-xl">
                    <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                        <div>
                            <h2 class="font-bold text-xl text-gray-800">{{ __('Application Status') }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Monitor your application progress here') }}</p>
                        </div>
                        <span class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs font-mono text-gray-500 shadow-sm">
                            ID: #{{ $pelamar->id }}
                        </span>
                    </div>
                    <div class="p-6 sm:p-8">
                        <div class="rounded-xl p-6 mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-6 
                            {{ $pelamar->status_lamaran == 'Lulus' ? 'bg-green-50 border border-green-100' : ($pelamar->status_lamaran == 'Gagal' ? 'bg-red-50 border border-red-100' : 'bg-blue-50 border border-blue-100') }}">
                            
                            <div class="shrink-0 p-4 bg-white rounded-full shadow-sm">
                                @if($pelamar->status_lamaran == 'Lulus') <span class="text-4xl">🎉</span>
                                @elseif($pelamar->status_lamaran == 'Gagal') <span class="text-4xl">😢</span>
                                @else <span class="text-4xl">⏳</span> @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-1
                                    {{ $pelamar->status_lamaran == 'Lulus' ? 'text-green-800' : ($pelamar->status_lamaran == 'Gagal' ? 'text-red-800' : 'text-blue-800') }}">
                                    {{ $pelamar->status_lamaran == 'Lulus' ? __('Congratulations! Accepted') : ($pelamar->status_lamaran == 'Gagal' ? __('Sorry, Not Accepted') : __('Processing')) }}
                                </h3>
                                <p class="text-base leading-relaxed {{ $pelamar->status_lamaran == 'Lulus' ? 'text-green-700' : ($pelamar->status_lamaran == 'Gagal' ? 'text-red-700' : 'text-blue-700') }}">
                                    {{ $pelamar->status_lamaran == 'Lulus' ? __('HRD will contact you via email for the next steps.') : ($pelamar->status_lamaran == 'Gagal' ? __('Keep up the spirit! Don\'t give up and try again next time.') : __('Your file is being reviewed by the HRD team. Please wait!')) }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <label class="block text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('Registered Name') }}</label>
                                <p class="font-semibold text-gray-900 text-lg">{{ $pelamar->nama }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <label class="block text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('Submission Date') }}</label>
                                <p class="font-semibold text-gray-900 text-lg">{{ $pelamar->created_at->format('d F Y, H:i') }}</p>
                            </div>
                            @if(!empty($pelamar->file_berkas))
                            <div class="col-span-1 md:col-span-2 mt-2">
                                <label class="block text-gray-500 text-xs font-bold uppercase tracking-wider mb-3">{{ __('File Preview') }}</label>
                                <div class="relative group">
                                    <div class="absolute -inset-0.5 bg-gradient-to-r from-[#232f3e] to-[#232f3e] rounded-2xl opacity-10 group-hover:opacity-20 transition duration-200"></div>
                                    <div class="relative flex items-center p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all">
                                        <div class="p-3 rounded-lg bg-[#232f3e]/10 text-[#232f3e]">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-500">{{ __('File Document') }}</p>
                                            <a href="{{ route('view.pdf', $pelamar->file_berkas) }}" target="_blank" class="text-lg font-bold text-[#232f3e] hover:underline decoration-2 underline-offset-2">
                                                {{ __('View Document') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($pelamar && $pelamar->status_lamaran == 'Pending')
                    @if(!Auth::user()->profile_photo_path)
                         <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-xl shadow-md mb-8">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-bold text-yellow-800 uppercase tracking-wide">{{ __('Attention Required') }}</h3>
                                    <p class="mt-1 text-sm text-yellow-700">
                                        {{ __('To update your application, you must complete your profile photo first.') }}
                                    </p>
                                    <div class="mt-3">
                                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-yellow-100 border border-yellow-200 rounded-md font-semibold text-xs text-yellow-800 uppercase tracking-widest hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition ease-in-out duration-150">
                                            {{ __('Upload Profile Photo') }} &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Tombol Buka Form -->
                        <div x-show="!showUpdateForm" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-8 transition-all hover:shadow-md">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('Update Application') }}</h2>
                                    <p class="text-gray-500 mb-4 sm:mb-0">{{ __('Want to change your data or application file?') }}</p>
                                    
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-700 text-xs font-medium mt-2">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ __('Only available when status is') }} <strong>Pending</strong></span>
                                    </div>
                                </div>
                                <button @click="showUpdateForm = true" class="w-full sm:w-auto px-6 py-3 bg-[#232f3e] text-white rounded-xl hover:bg-[#1a232e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e] transition-all shadow-lg shadow-gray-200 font-medium flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ __('Update Application') }}
                                </button>
                            </div>
                        </div>

                        <!-- Form Perbarui Lamaran -->
                        <div x-show="showUpdateForm" class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-8 transform transition-all" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-4">
                             
                            <div class="p-6 sm:p-8 border-b border-gray-100 flex justify-between items-start bg-gray-50/50">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">{{ __('Update Application') }}</h2>
                                    <p class="text-sm text-gray-500 mt-1">{{ __('Update your latest information to increase your chances of being accepted.') }}</p>
                                </div>
                                <button @click="showUpdateForm = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="p-6 sm:p-8">
                                <form action="{{ route('lamar.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8" @submit="isSubmitting = true">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Full Name (As per KTP)') }}</label>
                                        <input type="text" name="nama" value="{{ old('nama', $pelamar->nama) }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#232f3e] focus:border-[#232f3e] py-3 px-4 transition-colors" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Update File (PDF) - Optional') }}</label>
                                        
                                        <!-- Preview File Baru (Client Side) -->
                                        <div x-show="newFileName" class="mb-6" style="display: none;">
                                            <div class="flex justify-between items-center mb-3 bg-[#232f3e]/5 p-3 rounded-lg border border-[#232f3e]/10">
                                                <div class="flex items-center gap-3">
                                                    <div class="p-2 bg-[#232f3e]/10 rounded-lg">
                                                        <svg class="w-5 h-5 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <span class="block text-xs text-gray-500 uppercase font-bold tracking-wider">{{ __('Selected File') }}</span>
                                                        <span class="block text-sm text-[#232f3e] font-semibold" x-text="newFileName"></span>
                                                    </div>
                                                </div>
                                                <button type="button" @click="resetFile($refs.fileInput)" class="px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    {{ __('Remove / Replace') }}
                                                </button>
                                            </div>
                                            <div class="w-full h-[500px] bg-gray-100 rounded-xl overflow-hidden border border-gray-200 shadow-inner">
                                                <iframe :src="newFileUrl" class="w-full h-full" frameborder="0"></iframe>
                                            </div>
                                        </div>

                                        <!-- Upload Box -->
                                        <!-- Input File Hidden (Outside x-show) -->
                                        <input 
                                            name="file_berkas" 
                                            type="file" 
                                            class="sr-only" 
                                            accept=".pdf" 
                                            x-ref="fileInput" 
                                            @change="handleFileChange($event, $refs.fileInput)"
                                        >

                                        <div x-show="!newFileName" class="mt-2 flex justify-center px-6 pt-10 pb-12 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 hover:border-[#232f3e] transition-all duration-300 group cursor-pointer relative" @click="$refs.fileInput.click()">
                                            <div class="space-y-2 text-center">
                                                <div class="mx-auto h-16 w-16 text-gray-400 group-hover:text-[#232f3e] transition-colors bg-gray-50 group-hover:bg-white rounded-full flex items-center justify-center shadow-sm">
                                                    <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <div class="flex text-sm text-gray-600 justify-center">
                                                    <label class="relative cursor-pointer rounded-md font-medium text-[#232f3e] hover:text-[#1a232e] focus-within:outline-none">
                                                        <span>{{ __('Upload file document') }}</span>
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500">{{ __('PDF Max 5MB (CV, Diploma, Transcript)') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-6 border-t border-gray-100">
                                        <button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto flex justify-center py-3 px-8 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-[#232f3e] hover:bg-[#1a232e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e] transition-all transform hover:-translate-y-1 disabled:opacity-70 disabled:cursor-not-allowed">
                                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span x-text="isSubmitting ? trans.saving : trans.save_changes"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif

            @else
                {{-- Form Submission Baru --}}
                @if(!Auth::user()->profile_photo_path)
                     <div class="max-w-xl mx-auto bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-8 text-center">
                            <div class="mx-auto w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                                <svg class="h-10 w-10 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ __('Profile Photo Missing') }}</h3>
                            <p class="text-gray-500 mb-8 leading-relaxed">
                                {{ __('Hello future colleague! Please complete your profile photo before submitting your application. This helps us get to know you better.') }}
                            </p>
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-6 py-3 bg-yellow-500 border border-transparent rounded-xl font-bold text-white uppercase tracking-wide hover:bg-yellow-600 focus:outline-none focus:ring-4 focus:ring-yellow-300 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{ __('Upload Profile Photo Now') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <div class="p-8 border-b border-gray-100 bg-gray-50/30">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Job Application Form') }}</h2>
                            <p class="text-gray-500">{{ __('Please complete your personal data and upload your latest documents to join us.') }}</p>
                        </div>
                        <div class="p-8">
                            <form action="{{ route('lamar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                                @csrf
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Full Name (As per KTP)') }}</label>
                                    <input type="text" name="nama" value="{{ Auth::user()->name }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#232f3e] focus:border-[#232f3e] py-3 px-4 transition-colors" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Upload File (PDF)') }}</label>

                                    <!-- Input File Hidden (Outside x-show to keep it focusable/valid) -->
                                    <input 
                                        name="file_berkas" 
                                        type="file" 
                                        class="sr-only" 
                                        accept=".pdf" 
                                        x-ref="fileInputNew" 
                                        @change="handleFileChange($event, $refs.fileInputNew)" 
                                        required
                                    >

                                    <!-- Preview File Baru (Client Side) -->
                                    <div x-show="newFileName" class="mb-6" style="display: none;">
                                        <div class="flex justify-between items-center mb-3 bg-[#232f3e]/5 p-3 rounded-lg border border-[#232f3e]/10">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-[#232f3e]/10 rounded-lg">
                                                    <svg class="w-5 h-5 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <span class="block text-xs text-gray-500 uppercase font-bold tracking-wider">{{ __('Selected File') }}</span>
                                                    <span class="block text-sm text-[#232f3e] font-semibold" x-text="newFileName"></span>
                                                </div>
                                            </div>
                                            <button type="button" @click="resetFile($refs.fileInputNew)" class="px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                {{ __('Remove / Replace') }}
                                            </button>
                                        </div>
                                        <div class="w-full h-[500px] bg-gray-100 rounded-xl overflow-hidden border border-gray-200 shadow-inner">
                                            <iframe :src="newFileUrl" class="w-full h-full" frameborder="0"></iframe>
                                        </div>
                                    </div>

                                    <div x-show="!newFileName" class="mt-2 flex justify-center px-6 pt-10 pb-12 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 hover:border-[#232f3e] transition-all duration-300 group cursor-pointer relative" @click="$refs.fileInputNew.click()">
                                        <div class="space-y-2 text-center">
                                            <div class="mx-auto h-16 w-16 text-gray-400 group-hover:text-[#232f3e] transition-colors bg-gray-50 group-hover:bg-white rounded-full flex items-center justify-center shadow-sm">
                                                <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label class="relative cursor-pointer rounded-md font-medium text-[#232f3e] hover:text-[#1a232e] focus-within:outline-none">
                                                    <span>{{ __('Upload file document') }}</span>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">{{ __('PDF Max 5MB (CV, Diploma, Transcript)') }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" class="w-full flex justify-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-[#232f3e] hover:bg-[#1a232e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e] transition-all transform hover:-translate-y-1">
                                        {{ __('Submit Application Now') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pelamarDashboard', () => ({
                showPdfModal: false,
                pdfUrl: '',
                showUpdateForm: false,
                isSubmitting: false,
                newFileName: null,
                newFileUrl: null,
                trans: {
                    file_too_large: '',
                    saving: '',
                    save_changes: ''
                },

                init() {
                    const configEl = document.getElementById('pelamar-dashboard-config');
                    if (configEl) {
                        const config = JSON.parse(configEl.dataset.config);
                        this.showUpdateForm = config.showUpdateForm;
                        this.trans = config.trans;
                    }
                },

                handleFileChange(event, inputRef) {
                    const file = event.target.files[0];
                    if (file) {
                        if (file.size > 5242880) { // 5MB
                            alert(this.trans.file_too_large);
                            inputRef.value = '';
                            this.newFileName = null;
                            this.newFileUrl = null;
                        } else {
                            this.newFileName = file.name;
                            this.newFileUrl = URL.createObjectURL(file);
                        }
                    }
                },

                resetFile(inputRef) {
                    this.newFileName = null;
                    this.newFileUrl = null;
                    if (inputRef) inputRef.value = '';
                }
            }));
        });
    </script>

    <style>
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }
        @keyframes fadeInDown {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-app-layout>