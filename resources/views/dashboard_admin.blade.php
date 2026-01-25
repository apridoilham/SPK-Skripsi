<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false, editMode: false, form: { id: null, name: '', email: '', password: '', role: 'pelamar' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-700">Manajemen Pengguna</h3>
                    <button @click="showModal=true; editMode=false; form={id:null, name:'', email:'', password:'', role:'pelamar'}" class="bg-slate-900 text-white px-4 py-2 rounded text-sm hover:bg-slate-800 transition">
                        + Tambah User
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 border border-gray-100 rounded-lg">
                        <thead class="bg-gray-50 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($users as $u)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $u->name }}</td>
                                <td class="px-4 py-3">{{ $u->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase border {{ $u->role=='admin' ? 'bg-red-50 text-red-600 border-red-100' : ($u->role=='hrd' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100') }}">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right flex justify-end gap-2">
                                    <button @click="showModal=true; editMode=true; form={id:'{{$u->id}}', name:'{{$u->name}}', email:'{{$u->email}}', role:'{{$u->role}}', password:''}" class="text-blue-600 hover:text-blue-800 text-xs font-bold">Edit</button>
                                    @if($u->id != Auth::id())
                                    <form action="{{ route('user.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus user ini beserta data lamarannya?');">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-600 hover:text-rose-800 text-xs font-bold">Hapus</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-slate-900">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Log Aktivitas (Audit Trail)
                    </h3>
                    <p class="text-xs text-gray-500">Mencatat 20 aktivitas terakhir di dalam sistem.</p>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">User (Pelaku)</th>
                                <th class="px-4 py-3">Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-slate-400 text-xs">
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-bold">
                                            {{ substr($log->user->name ?? '?', 0, 1) }}
                                        </span>
                                        <span class="font-medium text-slate-700">{{ $log->user->name ?? 'User Terhapus' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase mr-2 {{ $log->type == 'danger' ? 'bg-rose-100 text-rose-700' : ($log->type == 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ $log->type }}
                                    </span>
                                    <span class="text-slate-600">{{ $log->description }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-400 italic">Belum ada aktivitas terekam.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
            <div class="bg-white rounded-lg w-full max-w-md p-6" @click.away="showModal=false">
                <h3 class="text-lg font-bold mb-4" x-text="editMode ? 'Edit User' : 'Tambah User Baru'"></h3>
                <form :action="editMode ? '{{ url('user') }}/' + form.id : '{{ route('user.store') }}'" method="POST">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" name="name" x-model="form.name" class="w-full border-gray-300 rounded-md text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                            <input type="email" name="email" x-model="form.email" class="w-full border-gray-300 rounded-md text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password <span x-show="editMode" class="text-gray-400 font-normal lowercase">(kosongkan jika tidak ubah)</span></label>
                            <input type="password" name="password" x-model="form.password" class="w-full border-gray-300 rounded-md text-sm" :required="!editMode">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Role</label>
                            <select name="role" x-model="form.role" class="w-full border-gray-300 rounded-md text-sm">
                                <option value="admin">Admin</option>
                                <option value="hrd">HRD</option>
                                <option value="pelamar">Pelamar</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="showModal=false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded text-sm hover:bg-slate-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>