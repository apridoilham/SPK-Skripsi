<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50">
        
        <!-- Main Card -->
        <div class="relative w-full max-w-5xl bg-white shadow-lg rounded-lg overflow-hidden flex flex-col md:flex-row m-4 z-10 border border-gray-200">
            
            <!-- Left Side: Brand/Info -->
            <div class="w-full md:w-5/12 p-10 flex flex-col justify-between bg-[#232f3e] text-white relative overflow-hidden">
                
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-white/10 rounded flex items-center justify-center mb-8">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold leading-tight mb-4">PT. Bhandawa Metafora Warsoyo</h1>
                    <p class="text-gray-300 leading-relaxed font-light">
                        {{ __('Employee Recruitment Decision Support System Platform using SAW method.') }}
                    </p>
                </div>
                
                <div class="relative z-10 mt-12">
                    <div class="flex items-center gap-3 text-sm text-gray-400 font-medium">
                        <span>&copy; {{ date('Y') }} HR System</span>
                        <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
                        <span>Ver 2.0</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="w-full md:w-7/12 p-10 bg-white relative">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-[#232f3e]">{{ __('Welcome Back 👋') }}</h2>
                    <p class="text-gray-500 mt-2 text-sm">{{ __('Log in to your account to continue.') }}</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-bold text-gray-700">{{ __('Email Address') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus 
                                class="w-full pl-11 pr-4 py-3 bg-white border border-gray-300 rounded focus:ring-2 focus:ring-[#232f3e] focus:border-[#232f3e] transition-all text-gray-800 placeholder-gray-400 shadow-sm"
                                placeholder="name@company.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label for="password" class="text-sm font-bold text-gray-700">{{ __('Password') }}</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-[#232f3e] hover:text-blue-600 transition-colors">{{ __('Forgot Password?') }}</a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-text-input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full pl-11 pr-4 py-3 bg-white border border-gray-300 rounded focus:ring-2 focus:ring-[#232f3e] focus:border-[#232f3e] transition-all text-gray-800 placeholder-gray-400 shadow-sm"
                                placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#232f3e] shadow-sm focus:ring-[#232f3e]" name="remember">
                            <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 bg-[#232f3e] hover:bg-[#1a232e] text-white font-bold rounded shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e]">
                        {{ __('Login Now') }}
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-gray-500 text-sm">
                        {{ __('Don\'t have an account?') }} 
                        <a href="{{ route('register') }}" class="font-bold text-[#232f3e] hover:text-blue-600 hover:underline transition-colors">
                            {{ __('Register as Applicant') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>