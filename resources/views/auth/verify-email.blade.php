<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50">
        
        <!-- Main Card -->
        <div class="relative w-full max-w-md bg-white shadow-lg rounded-lg border border-gray-200 overflow-hidden p-8 m-4 z-10">
            <!-- Header Icon -->
            <div class="flex justify-center mb-8">
                <div class="p-4 bg-[#232f3e]/10 rounded-full border border-[#232f3e]/20">
                    <svg class="w-10 h-10 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-[#232f3e]">Verifikasi Email</h2>
                <p class="text-gray-500 mt-2 text-sm">Silakan verifikasi alamat email Anda.</p>
            </div>

            <div class="mb-6 text-sm text-gray-600 text-center leading-relaxed">
                {{ __('Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda? Jika Anda tidak menerima email tersebut, kami akan dengan senang hati mengirimkan yang baru.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-100 flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm font-medium text-green-700">
                        {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
                    </div>
                </div>
            @endif

            <div class="flex flex-col gap-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <button type="submit" class="w-full py-3 px-4 bg-[#232f3e] hover:bg-[#1a232f] text-white font-bold rounded shadow-sm transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e] flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            {{ __('Kirim Ulang Email Verifikasi') }}
        </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf

                    <button type="submit" class="underline text-sm text-slate-600 hover:text-[#232f3e] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e]">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
