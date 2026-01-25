<x-guest-layout>
    <div class="min-h-screen flex bg-white">
        <div class="hidden lg:flex w-1/2 relative bg-gray-900 overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');"></div>
            <div class="absolute inset-0 bg-black/60 mix-blend-multiply"></div>
            <div class="relative z-10 w-full flex flex-col justify-between p-12 text-white h-full">
                <div>
                    <h1 class="text-4xl font-bold mb-4">Bergabung Bersama Kami</h1>
                    <p class="text-lg text-gray-200">Karir cemerlang menanti Anda di PT. Bhandawa Metafora Warsoyo.</p>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12">
            <div class="w-full max-w-md space-y-8">
                <div class="text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Buat Akun Pelamar</h2>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus class="block w-full" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required class="block w-full" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <x-text-input id="password" type="password" name="password" required class="block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                        <x-text-input id="password_confirmation" type="password" name="password_confirmation" required class="block w-full" />
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 px-4 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                            Daftar Sekarang &rarr;
                        </button>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-indigo-600 hover:underline">
                        Sudah punya akun? Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>