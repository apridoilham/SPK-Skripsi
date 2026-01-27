<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50">
        
        <!-- Main Card -->
        <div class="relative w-full max-w-md bg-white shadow-lg rounded-lg border border-gray-200 overflow-hidden p-8 m-4 z-10">
            <!-- Header Icon -->
            <div class="flex justify-center mb-8">
                <div class="p-4 bg-[#232f3e]/10 rounded-full border border-[#232f3e]/20">
                    <svg class="w-10 h-10 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-[#232f3e]">Konfirmasi Password</h2>
                <p class="text-gray-500 mt-2 text-sm">Ini adalah area aman dari aplikasi.</p>
            </div>

            <div class="mb-6 text-sm text-gray-600 text-center leading-relaxed">
                {{ __('Silakan konfirmasi password Anda sebelum melanjutkan.') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="text-sm font-bold text-[#232f3e]">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-text-input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-11 pr-4 py-3 bg-white border border-gray-300 rounded focus:ring-2 focus:ring-[#232f3e] focus:border-[#232f3e] transition-all text-[#232f3e] placeholder-gray-400 shadow-sm"
                            placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="w-full py-3 px-4 bg-[#232f3e] hover:bg-[#1a232e] text-white font-bold rounded shadow-sm transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e] flex items-center justify-center gap-2">
                        {{ __('Konfirmasi') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>