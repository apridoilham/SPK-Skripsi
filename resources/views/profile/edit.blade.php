<x-app-layout>
    <x-slot name="head">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>body { font-family: 'Inter', sans-serif; }</style>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-[#232f3e]">Account Settings</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage your profile information and account security.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Profile Card Side -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded border border-gray-200 shadow-sm text-center">
                        <div class="w-full h-1 bg-[#232f3e] mb-6 rounded-t"></div>
                        
                        @if($user->profile_photo_path)
                            <div class="mx-auto mb-4 h-24 w-24 rounded-full overflow-hidden border border-gray-200">
                                <img src="{{ Storage::disk('public')->url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            </div>
                        @else
                            <div class="h-24 w-24 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-3xl font-bold mx-auto mb-4 border border-gray-200">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        
                        <h3 class="text-xl font-bold text-[#232f3e]">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-center">
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded uppercase tracking-wider border border-gray-200">
                                {{ $user->role ?? __('User') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Forms Side -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Profile Information -->
                    <div class="p-6 bg-white shadow-sm rounded border border-gray-200">
                        <div class="max-w-xl">
                            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                                <div class="text-[#232f3e]">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-[#232f3e]">Profile Information</h3>
                            </div>
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Password Update -->
                    <div class="p-6 bg-white shadow-sm rounded border border-gray-200">
                        <div class="max-w-xl">
                            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                                <div class="text-[#232f3e]">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-[#232f3e]">Password Security</h3>
                            </div>
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account Section -->
                    <div class="bg-red-50 rounded-xl p-6 border border-red-100">
                        <div class="max-w-xl">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-red-700">{{ __('Delete Account') }}</h3>
                                    <p class="text-sm text-red-600/80">{{ __('Permanently delete your account and all of its data.') }}</p>
                                </div>
                            </div>
                            <div class="w-full h-1 bg-red-200 mb-6 rounded-t"></div>
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>