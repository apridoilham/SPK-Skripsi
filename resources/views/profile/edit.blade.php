<x-app-layout>
    <x-slot name="head">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>body { font-family: 'Inter', sans-serif; }</style>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Pengaturan Akun</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm text-center">
                        <div class="h-24 w-24 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-3xl font-bold mx-auto mb-4 border-4 border-blue-50">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-center gap-2">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-md uppercase tracking-wide">{{ $user->role ?? 'User' }}</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="p-6 sm:p-8 bg-white shadow-sm sm:rounded-xl border border-gray-200">
                        <div class="max-w-xl">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Informasi Profil</h3>
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 bg-white shadow-sm sm:rounded-xl border border-gray-200">
                        <div class="max-w-xl">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Keamanan Kata Sandi</h3>
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 bg-white shadow-sm sm:rounded-xl border border-rose-100">
                        <div class="max-w-xl">
                            <h3 class="text-lg font-bold text-rose-600 mb-4 border-b border-rose-100 pb-2">Hapus Akun</h3>
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>