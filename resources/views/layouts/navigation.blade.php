<nav x-data="{ open: false }" class="bg-[#232f3e] border-b border-[#232f3e] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-2">
                        <div class="w-8 h-8 flex items-center justify-center text-white font-bold">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-white font-bold text-lg tracking-tight group-hover:text-gray-200 transition-colors">SPK System</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="text-gray-300 hover:text-white transition-colors duration-200 font-medium text-sm">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded text-gray-300 hover:text-white transition ease-in-out duration-150 group">
                            <div class="flex items-center gap-2">
                                @if (Auth::user()->profile_photo_path)
                                    <img class="w-8 h-8 rounded-full object-cover border border-gray-500 shadow-sm" src="{{ Storage::disk('public')->url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" />
                                @else
                                    @php
                                        $roleColor = match(Auth::user()->role) {
                                            'admin' => 'EF4444', // Red-500
                                            'hrd' => '8B5CF6',   // Violet-500
                                            'pelamar' => '3B82F6', // Blue-500
                                            default => '6B7280' // Gray-500
                                        };
                                        $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode(Auth::user()->name) . "&background=" . $roleColor . "&color=fff&size=128&bold=true";
                                    @endphp
                                    <img class="w-8 h-8 rounded-full object-cover border border-gray-500 shadow-sm" src="{{ $avatarUrl }}" alt="{{ Auth::user()->name }}" />
                                @endif
                                <div class="flex flex-col items-start leading-none">
                                    <span class="text-sm font-medium text-gray-200 group-hover:text-white transition-colors">{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">{{ __(ucfirst(Auth::user()->role)) }}</span>
                                </div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Language') }}</span>
                            <div class="flex gap-2 text-xs font-bold">
                                <a href="{{ route('lang.switch', 'id') }}" class="{{ app()->getLocale() == 'id' ? 'text-[#232f3e]' : 'text-gray-400 hover:text-gray-600' }}">ID</a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'text-[#232f3e]' : 'text-gray-400 hover:text-gray-600' }}">EN</a>
                            </div>
                        </div>
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Signed in as') }}</p>
                            <p class="text-sm font-bold text-[#232f3e] truncate">{{ Auth::user()->email }}</p>
                        </div>
                        
                        <x-dropdown-link :href="route('profile.edit')" class="hover:bg-gray-50 hover:text-[#232f3e]">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    class="text-red-600 hover:bg-red-50 hover:text-red-700"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#232f3e] border-t border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-300 hover:text-white hover:bg-gray-800 border-l-4 border-transparent hover:border-white">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4 flex items-center gap-3 mb-3">
                @if (Auth::user()->profile_photo_path)
                    <img class="w-9 h-9 rounded-full object-cover border border-gray-500" src="{{ Storage::disk('public')->url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" />
                @else
                    <div class="w-9 h-9 rounded bg-gray-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="font-bold text-base text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-300 hover:text-white hover:bg-gray-800">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            class="text-red-400 hover:text-red-300 hover:bg-gray-800"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>