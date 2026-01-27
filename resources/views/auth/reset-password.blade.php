<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50">
        
        <!-- Main Card -->
        <div class="relative w-full max-w-lg bg-white shadow-lg rounded-lg border border-gray-200 overflow-hidden p-8 m-4 z-10">
            
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-[#232f3e]/10 rounded-lg flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[#232f3e] mb-2">Buat Password Baru</h2>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Silakan masukkan password baru Anda yang aman.
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="space-y-2">
                    <label for="email" class="text-sm font-bold text-[#232f3e] ml-1">Email Address</label>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username"
                            class="w-full pl-11 pr-4 py-3 bg-white border border-gray-300 rounded focus:ring-2 focus:ring-[#232f3e] focus:border-[#232f3e] transition-all text-[#232f3e] placeholder-gray-400 shadow-sm" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-bold text-[#232f3e] ml-1">Password Baru</label>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-text-input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full pl-11 pr-4 py-3 bg-white border border-gray-300 rounded focus:ring-2 focus:ring-[#232f3e] focus:border-[#232f3e] transition-all text-[#232f3e] placeholder-gray-400 shadow-sm"
                            placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-bold text-[#232f3e] ml-1">Konfirmasi Password</label>
                     <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full pl-11 pr-4 py-3 bg-white border border-gray-300 rounded focus:ring-2 focus:ring-[#232f3e] focus:border-[#232f3e] transition-all text-[#232f3e] placeholder-gray-400 shadow-sm"
                            placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-[#232f3e] hover:bg-[#1a232e] text-white font-bold rounded shadow-sm transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e]">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
