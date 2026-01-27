<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50">
        
        <!-- Main Card -->
        <div class="relative w-full max-w-lg bg-white shadow-lg rounded-lg border border-gray-200 overflow-hidden p-8 m-4 z-10">
            
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-[#232f3e]/10 rounded-lg flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 11 9 13.536 9 16l3 3 6-6V9a6 6 0 010-12z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[#232f3e] mb-2">Lupa Password?</h2>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Jangan khawatir. Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="text-sm font-bold text-[#232f3e] ml-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                            class="w-full pl-11 pr-4 py-3 bg-white border border-gray-300 rounded focus:ring-2 focus:ring-[#232f3e] focus:border-[#232f3e] transition-all text-[#232f3e] placeholder-gray-400 shadow-sm"
                            placeholder="name@company.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-[#232f3e] hover:bg-[#1a232e] text-white font-bold rounded shadow-sm transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e]">
                    Kirim Tautan Reset
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-gray-100">
                <a href="{{ route('login') }}" class="text-sm font-bold text-[#232f3e] hover:text-[#1a232e] transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>