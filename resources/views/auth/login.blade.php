<x-guest-layout>
    <div class="min-h-screen flex bg-white">
        <div class="hidden lg:flex w-1/2 relative bg-gray-900 overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');"></div>
            <div class="absolute inset-0 bg-black/60 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-90"></div>
            
            <div class="relative z-10 w-full flex flex-col justify-between p-12 text-white h-full">
                <div>
                    <div class="bg-blue-600 w-12 h-12 rounded-xl flex items-center justify-center mb-6 shadow-lg shadow-blue-900/50 border border-white/10">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight mb-4 text-white drop-shadow-md">PT. Bhandawa Metafora Warsoyo</h1>
                    <p class="text-lg text-gray-200 max-w-md leading-relaxed drop-shadow-sm">
                        Sistem Pendukung Keputusan Penerimaan Karyawan Berbasis Metode SAW. Transparan, Akurat, dan Efisien.
                    </p>
                </div>
                
                <div class="flex items-center gap-4 text-sm font-medium text-gray-400">
                    <span>&copy; {{ date('Y') }} HR System</span>
                    <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
                    <span>All Rights Reserved</span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12 overflow-y-auto">
            <div class="w-full max-w-md space-y-8">
                <div class="lg:hidden text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">PT. BMW</h2>
                </div>

                <div class="text-left">
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Selamat Datang 👋</h2>
                    <p class="mt-2 text-sm text-gray-500">Silakan masuk ke akun Anda untuk melanjutkan.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus class="block w-full" placeholder="nama@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <x-text-input id="password" type="password" name="password" required autocomplete="current-password" class="block w-full" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-blue-700 hover:bg-blue-800 focus:outline-none transition-all">
                            Masuk ke Dashboard &rarr;
                        </button>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <a href="{{ route('register') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-500 hover:underline">
                        Belum punya akun? Daftar sebagai Pelamar
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>