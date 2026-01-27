<x-app-layout>
    @php
        $totalUsers = $users->count();
        $pelamarCount = $users->where('role', 'pelamar')->count();
        $hrdCount = $users->where('role', 'hrd')->count();
        $adminCount = $users->where('role', 'admin')->count();
        
        $pelamarPct = $totalUsers > 0 ? ($pelamarCount / $totalUsers) * 100 : 0;
        $hrdPct = $totalUsers > 0 ? ($hrdCount / $totalUsers) * 100 : 0;
        $adminPct = $totalUsers > 0 ? ($adminCount / $totalUsers) * 100 : 0;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#232f3e] leading-tight">
                    {{ __('Admin Dashboard') }}
                </h2>
                <p class="text-gray-500 text-sm mt-1">{{ __('Manage users, system logs, and security settings.') }}</p>
            </div>
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded border border-gray-300 shadow-sm">
                <div class="p-1.5 bg-gray-100 rounded text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-500 uppercase">{{ __('Today') }}</span>
                    <span class="text-sm font-bold text-[#232f3e]">{{ now()->isoFormat('D MMMM Y') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen" x-data="adminDashboard">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded">
                    <div class="p-5 border-l-4 border-[#232f3e]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Total Users') }}</p>
                                <p class="text-2xl font-bold text-[#232f3e] mt-1">{{ $totalUsers }}</p>
                            </div>
                            <div class="p-2 bg-gray-100 rounded">
                                <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Candidates -->
                <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded">
                    <div class="p-5 border-l-4 border-[#232f3e]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Pelamar') }}</p>
                                <p class="text-2xl font-bold text-[#232f3e] mt-1">{{ $pelamarCount }}</p>
                            </div>
                            <div class="p-2 bg-slate-50 rounded">
                                <svg class="w-6 h-6 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HRD -->
                <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded">
                    <div class="p-5 border-l-4 border-blue-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('HRD') }}</p>
                                <p class="text-2xl font-bold text-[#232f3e] mt-1">{{ $hrdCount }}</p>
                            </div>
                            <div class="p-2 bg-blue-50 rounded">
                                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin -->
                 <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded">
                    <div class="p-5 border-l-4 border-red-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Admin') }}</p>
                                <p class="text-2xl font-bold text-[#232f3e] mt-1">{{ $adminCount }}</p>
                            </div>
                            <div class="p-2 bg-red-50 rounded">
                                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Management Section -->
            <div class="bg-white shadow-sm border border-gray-200 rounded">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-[#232f3e] flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ __('User Management') }}
                            </h3>
                            <p class="text-gray-500 mt-1 text-sm">{{ __('Create, edit, and manage user accounts and permissions.') }}</p>
                        </div>
                        <button @click="resetForm()" 
                                class="inline-flex items-center justify-center px-4 py-2 bg-[#232f3e] hover:bg-[#1a232e] text-white text-sm font-bold rounded shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#232f3e]">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add User') }}
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 tracking-wider">{{ __('Full Name') }}</th>
                                    <th class="px-6 py-3 tracking-wider">{{ __('Email Address') }}</th>
                                    <th class="px-6 py-3 tracking-wider">{{ __('Role') }}</th>
                                    <th class="px-6 py-3 tracking-wider text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($users as $u)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 font-medium text-[#232f3e]">
                                        <div class="flex items-center gap-3">
                                            @if($u->profile_photo_path)
                                                <img src="{{ Storage::disk('public')->url($u->profile_photo_path) }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 shadow-sm">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600 border border-gray-200 shadow-sm">
                                                    {{ substr($u->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="flex flex-col">
                                                <span>{{ $u->name }}</span>
                                                <span class="text-xs text-gray-400 sm:hidden">{{ $u->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 hidden sm:table-cell">{{ $u->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold border {{ $u->role=='admin' ? 'bg-red-50 text-red-700 border-red-200' : ($u->role=='hrd' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-green-50 text-green-700 border-green-200') }}">
                                            {{ strtoupper($u->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="editUser($el.dataset.user)" 
                                                    data-user="{{ json_encode($u) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium text-xs uppercase tracking-wide">
                                                {{ __('Edit') }}
                                            </button>
                                            @if($u->id != Auth::id())
                                            <form action="{{ route('user.destroy', $u->id) }}" method="POST" class="inline" data-confirm="{{ __('Delete this user?') }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs uppercase tracking-wide ml-2">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Audit Trail Section -->
            <div class="bg-white shadow-sm border border-gray-200 rounded">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-[#232f3e] flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Audit Trail') }}
                        </h3>
                        <p class="text-gray-500 mt-1 text-sm">{{ __('Recent system activities and security logs.') }}</p>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-bold text-xs uppercase tracking-wider border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">{{ __('Timestamp') }}</th>
                                    <th class="px-6 py-3">{{ __('User') }}</th>
                                    <th class="px-6 py-3">{{ __('Activity') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <span class="font-mono text-xs text-gray-600">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($log->user)
                                                @if($log->user->profile_photo_path)
                                                    <img src="{{ Storage::disk('public')->url($log->user->profile_photo_path) }}" alt="{{ $log->user->name }}" class="w-6 h-6 rounded-full object-cover border border-gray-200">
                                                @else
                                                    <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500 border border-gray-200">
                                                        {{ substr($log->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-gray-50 flex items-center justify-center text-[10px] font-bold text-gray-300 border border-gray-200">?</div>
                                            @endif
                                            <span class="font-semibold text-[#232f3e]">{{ $log->user->name ?? __('Deleted User') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-gray-600">{{ $log->description }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">{{ __('No logs found.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity" @click="showModal = false">
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="showModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200">
                    
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-[#232f3e]" x-text="editMode ? '{{ __('Edit User') }}' : '{{ __('Add New User') }}'"></h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="editMode ? '{{ url('user') }}/' + form.id : '{{ route('user.store') }}'" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('Full Name') }}</label>
                                <input type="text" name="name" x-model="form.name" required class="w-full rounded border-gray-300 focus:border-[#232f3e] focus:ring focus:ring-[#232f3e] focus:ring-opacity-50 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('Email Address') }}</label>
                                <input type="email" name="email" x-model="form.email" required class="w-full rounded border-gray-300 focus:border-[#232f3e] focus:ring focus:ring-[#232f3e] focus:ring-opacity-50 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('Password') }}</label>
                                <input type="password" name="password" x-model="form.password" :required="!editMode" class="w-full rounded border-gray-300 focus:border-[#232f3e] focus:ring focus:ring-[#232f3e] focus:ring-opacity-50 shadow-sm" placeholder="{{ __('Leave blank to keep current password') }}">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('Role') }}</label>
                                <select name="role" x-model="form.role" class="w-full rounded border-gray-300 focus:border-[#232f3e] focus:ring focus:ring-[#232f3e] focus:ring-opacity-50 shadow-sm">
                                    <option value="pelamar">{{ __('Pelamar') }}</option>
                                    <option value="hrd">{{ __('HRD') }}</option>
                                    <option value="admin">{{ __('Admin') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded border border-gray-300 transition-colors">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#232f3e] hover:bg-[#1a232e] text-white font-bold rounded shadow-sm transition-colors">
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="admin-dashboard-config" data-config="{{ json_encode([
        'stats' => [
            'pelamar' => $pelamarPct,
            'hrd' => $hrdPct,
            'admin' => $adminPct
        ]
    ]) }}"></div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminDashboard', () => ({
                showModal: false, 
                editMode: false, 
                form: { id: null, name: '', email: '', password: '', role: 'pelamar' },
                stats: {},

                init() {
                    const configEl = document.getElementById('admin-dashboard-config');
                    if(configEl) {
                        const config = JSON.parse(configEl.dataset.config);
                        this.stats = config.stats;
                    }
                },

                resetForm() {
                    this.showModal = true;
                    this.editMode = false;
                    this.form = { id: null, name: '', email: '', password: '', role: 'pelamar' };
                },

                editUser(userJson) {
                    const user = JSON.parse(userJson);
                    this.showModal = true;
                    this.editMode = true;
                    this.form = {
                        id: user.id,
                        name: user.name,
                        email: user.email,
                        role: user.role,
                        password: ''
                    };
                }
            }));
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    var message = form.getAttribute('data-confirm');
                    if (message && !window.confirm(message)) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
</x-app-layout>
