<x-app-layout>
    <x-slot name="head">
        <script src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            [x-cloak] { display: none !important; }
            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            /* Animasi Typing */
            .typing-dot { width: 5px; height: 5px; background: #64748b; border-radius: 50%; animation: typing 1.4s infinite ease-in-out; }
            .typing-dot:nth-child(1) { animation-delay: 0s; }
            .typing-dot:nth-child(2) { animation-delay: 0.2s; }
            .typing-dot:nth-child(3) { animation-delay: 0.4s; }
            @keyframes typing { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        </style>
        <script>
        </script>
    </x-slot>

    <div class="min-h-screen pb-20" x-data="hrdDashboard">
         
        <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-[0_1px_2px_0_rgba(0,0,0,0.05)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-slate-900 text-white p-2 rounded-lg shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-slate-900 text-lg leading-none">HRD Console</h1>
                        <span class="text-[10px] text-slate-500 font-medium">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button @click="showSettings = true" class="px-3 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Kriteria
                    </button>
                    <button @click="showMatrix = true" class="px-3 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Detail Hitungan
                    </button>
                    <button @click="generatePdf()" class="px-3 py-2 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-700 shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Laporan
                    </button>
                </div>
            </div>
        </div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($kriterias as $k)
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-[0_2px_8px_rgba(0,0,0,0.04)] relative overflow-hidden group hover:border-blue-400 transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 {{ $k->jenis == 'benefit' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200">{{ $k->kode }}</span>
                        <span class="text-[10px] font-bold uppercase {{ $k->jenis == 'benefit' ? 'text-emerald-700 bg-emerald-50 border border-emerald-100' : 'text-rose-700 bg-rose-50 border border-rose-100' }} px-2 py-1 rounded tracking-wide">{{ $k->jenis }}</span>
                    </div>
                    <h3 class="font-bold text-slate-800 truncate text-sm" title="{{ $k->nama }}">{{ $k->nama }}</h3>
                    <div class="mt-4 flex justify-between items-end">
                        <div class="flex items-baseline gap-0.5">
                            <span class="text-2xl font-black text-slate-900">{{ $k->bobot * 100 }}</span>
                            <span class="text-xs text-slate-400 font-bold">%</span>
                        </div>
                        <span class="text-[10px] font-medium text-slate-400">{{ count($k->opsi ?? []) }} Level</span>
                    </div>
                </div>
                @endforeach
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
                            <div>
                                <h2 class="font-bold text-slate-800 text-sm">Input Penilaian Kandidat</h2>
                                <p class="text-[11px] text-slate-500">Beri nilai sesuai berkas yang diupload.</p>
                            </div>
                            <span class="text-[10px] bg-white border border-slate-200 px-2 py-1 rounded font-bold text-slate-600 shadow-sm">{{ count($pelamars) }} Pelamar</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($pelamars as $p)
                            <div class="p-6 hover:bg-slate-50/50 transition-colors group" x-data="{ sending: false }">
                                <div class="flex justify-between items-start mb-5">
                                    <div class="flex gap-4">
                                        <div class="w-10 h-10 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-sm shadow-sm">{{ substr($p->nama, 0, 2) }}</div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-bold text-slate-900 text-sm">{{ $p->nama }}</h3>
                                                <button type="button" @click="analyzeCv({{ $p->id }})" class="text-[9px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded border border-purple-200 hover:bg-purple-200 transition-colors flex items-center gap-1" title="Analisa Otomatis dengan AI">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                                    AI Scan
                                                </button>
                                            </div>
                                            <a href="#" @click.prevent="viewPdf('{{ route('view.pdf', $p->file_berkas) }}')" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 mt-0.5 group/link">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> 
                                                <span class="group-hover/link:underline">Lihat Berkas PDF</span>
                                            </a>
                                        </div>
                                    </div>
                                    <button form="form-{{ $p->id }}" type="submit" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-500 hover:bg-emerald-50 transition-all flex items-center justify-center shadow-sm" title="Simpan Nilai">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                                
                                <form id="form-{{ $p->id }}" action="{{ route('nilai.update', $p->id) }}" method="POST" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @csrf @method('PUT')
                                    @foreach($kriterias as $k)
                                        @php 
                                            $val = $p->nilai_kriteria[$k->kode] ?? 1; 
                                            $opsi = $k->opsi ?? ['1','2','3','4','5'];
                                        @endphp
                                        <div class="relative">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">{{ $k->nama }}</label>
                                            <select name="{{ $k->kode }}" class="w-full text-xs font-medium text-slate-700 border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-slate-50/50 hover:bg-white transition-colors cursor-pointer">
                                                @foreach($opsi as $idx => $label)
                                                    <option value="{{ $idx + 1 }}" {{ $val == ($idx + 1) ? 'selected' : '' }}>
                                                        {{ $idx + 1 }} - {{ is_numeric(substr($label,0,1)) ? substr($label,2) : $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </form>
                            </div>
                            @empty
                            <div class="p-12 text-center text-slate-400 text-sm italic">Belum ada data pelamar yang masuk.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-1">
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm sticky top-24 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-white z-10">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="font-bold text-slate-800 text-sm">Live Ranking</h2>
                                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span><span class="text-[10px] font-bold text-emerald-600 uppercase">Realtime</span></div>
                            </div>
                            <form action="{{ route('ranking.hitung') }}" method="POST">
                                @csrf
                                <button class="w-full bg-slate-900 text-white text-xs font-bold py-3 rounded-lg hover:bg-slate-800 shadow-lg shadow-slate-200 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Hitung Ulang SAW
                                </button>
                            </form>
                        </div>
                        
                        <div class="divide-y divide-slate-100 max-h-[600px] overflow-y-auto custom-scrollbar bg-slate-50/30">
                            @forelse($ranking as $index => $r)
                            <div class="p-4 flex items-center justify-between hover:bg-blue-50/30 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 flex items-center justify-center rounded-lg text-xs font-bold shadow-sm border {{ $index == 0 ? 'bg-amber-100 text-amber-700 border-amber-200' : ($index == 1 ? 'bg-slate-200 text-slate-600 border-slate-300' : ($index == 2 ? 'bg-orange-100 text-orange-800 border-orange-200' : 'bg-white text-slate-400 border-slate-200')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 truncate w-28" title="{{ $r->nama }}">{{ $r->nama }}</div>
                                        <div class="text-[10px] font-mono font-bold text-blue-600 bg-blue-50 px-1.5 rounded w-fit mt-0.5">{{ number_format($r->skor_akhir, 4) }}</div>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    @if($r->status_lamaran == 'Pending')
                                        <form action="{{ route('status.update', $r->id) }}" method="POST">@csrf @method('PUT')<button name="status" value="Lulus" class="w-7 h-7 rounded flex items-center justify-center bg-white border border-emerald-200 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm" title="Terima"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></button></form>
                                        <form action="{{ route('status.update', $r->id) }}" method="POST">@csrf @method('PUT')<button name="status" value="Gagal" class="w-7 h-7 rounded flex items-center justify-center bg-white border border-rose-200 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm" title="Tolak"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></form>
                                    @else
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="text-[9px] font-bold px-2 py-0.5 rounded border uppercase {{ $r->status_lamaran == 'Lulus' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                                                {{ $r->status_lamaran == 'Lulus' ? 'DITERIMA' : 'DITOLAK' }}
                                            </span>
                                            <form action="{{ route('status.update', $r->id) }}" method="POST">@csrf @method('PUT')<button name="status" value="Pending" class="text-[9px] text-slate-400 hover:text-blue-600 underline">Reset</button></form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="p-8 text-center text-slate-400 text-xs italic">Belum ada perhitungan.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div x-show="showSettings" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-sm" x-cloak x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden" @click.away="showSettings = false"
                 x-data="{
                    items: {{ Js::from($kriterias->map(function($k){ return ['kode' => $k->kode, 'nama' => $k->nama, 'bobot' => $k->bobot * 100, 'jenis' => $k->jenis, 'opsi' => $k->opsi ?? ['','','','','']]; })) }},
                    add() { this.items.push({ kode: 'C'+(this.items.length+1), nama: '', bobot: 0, jenis: 'benefit', opsi: ['Buruk','Kurang','Cukup','Baik','Sangat Baik'] }); },
                    remove(idx) { this.items.splice(idx, 1); this.items.forEach((item, i) => item.kode = 'C'+(i+1)); },
                    addOpsi(idx) { this.items[idx].opsi.push(''); },
                    removeOpsi(kIdx, oIdx) { if(this.items[kIdx].opsi.length > 1) this.items[kIdx].opsi.splice(oIdx, 1); },
                    get total() { return this.items.reduce((a,b) => a + parseFloat(b.bobot||0), 0); }
                 }">
                
                <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">Konfigurasi Kriteria</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Atur bobot dan parameter perhitungan SAW.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-slate-500">Total Bobot:</span>
                        <span class="text-xl font-black" :class="Math.abs(total - 100) < 0.1 ? 'text-emerald-600' : 'text-rose-600'" x-text="total.toFixed(1) + '%'"></span>
                    </div>
                </div>

                <form action="{{ route('kriteria.update') }}" method="POST" class="flex-1 overflow-hidden flex flex-col">
                    @csrf @method('PUT')
                    <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-slate-50/50 custom-scrollbar">
                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative group hover:border-blue-300 transition-colors">
                                <button type="button" @click="remove(idx)" class="absolute top-4 right-4 text-slate-300 hover:text-rose-500 transition-colors bg-slate-50 p-1.5 rounded-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                
                                <div class="flex gap-5 mb-5">
                                    <div class="w-12 h-12 bg-slate-800 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-lg shadow-slate-200">
                                        <input type="hidden" :name="`kriteria[${idx}][kode]`" x-model="item.kode">
                                        <span x-text="item.kode"></span>
                                    </div>
                                    <div class="flex-1 grid grid-cols-12 gap-4">
                                        <div class="col-span-6">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">Nama Kriteria</label>
                                            <input type="text" :name="`kriteria[${idx}][nama]`" x-model="item.nama" class="w-full text-sm font-semibold text-slate-700 border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                        </div>
                                        <div class="col-span-3">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">Bobot (%)</label>
                                            <input type="number" step="0.1" :name="`kriteria[${idx}][bobot]`" x-model="item.bobot" class="w-full text-sm font-bold text-slate-700 border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                        </div>
                                        <div class="col-span-3">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">Sifat</label>
                                            <select :name="`kriteria[${idx}][jenis]`" x-model="item.jenis" class="w-full text-sm font-medium text-slate-700 border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                                <option value="benefit">Benefit (+)</option>
                                                <option value="cost">Cost (-)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                                    <div class="flex justify-between mb-3 items-center">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase flex items-center gap-2"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> Skala Penilaian (1 - Max)</label>
                                        <button type="button" @click="addOpsi(idx)" class="text-[10px] text-blue-600 font-bold bg-white border border-blue-100 px-2 py-1 rounded hover:bg-blue-50 transition">+ Tambah Opsi</button>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                        <template x-for="(opt, oIdx) in item.opsi" :key="oIdx">
                                            <div class="relative group/opt">
                                                <span class="absolute left-0 top-0 bottom-0 w-8 flex items-center justify-center text-[10px] font-bold text-slate-400 bg-slate-100 border-r border-slate-200 rounded-l-md" x-text="oIdx+1"></span>
                                                <input type="text" :name="`kriteria[${idx}][opsi][]`" x-model="item.opsi[oIdx]" class="w-full pl-10 pr-6 py-2 text-xs font-medium text-slate-600 border-slate-200 rounded-md focus:border-blue-500 focus:ring-blue-500" placeholder="Label">
                                                <button type="button" @click="removeOpsi(idx, oIdx)" class="absolute right-1 top-2 text-slate-300 hover:text-rose-500 opacity-0 group-hover/opt:opacity-100 transition"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="add()" class="w-full py-4 border-2 border-dashed border-slate-300 rounded-xl text-slate-400 font-bold text-sm hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all flex items-center justify-center gap-2 group">
                            <div class="bg-slate-100 p-1.5 rounded-full group-hover:bg-blue-100 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></div>
                            Tambah Kriteria Baru
                        </button>
                    </div>
                    <div class="px-8 py-5 border-t border-slate-100 flex justify-end gap-3 bg-white">
                        <button type="button" @click="showSettings = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 border border-slate-200 rounded-lg transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showMatrix" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-sm" x-cloak x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden" @click.away="showMatrix = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">Matriks Normalisasi (R)</h3>
                        <p class="text-xs text-slate-500">Nilai murni yang telah dinormalisasi (0-1) sesuai jenis kriteria.</p>
                    </div>
                    <button @click="showMatrix = false" class="text-slate-400 hover:text-rose-500 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="p-6 overflow-auto custom-scrollbar bg-slate-50 flex-1">
                    <table class="w-full text-left border-collapse bg-white rounded-lg shadow-sm overflow-hidden border border-slate-200">
                        <thead class="bg-slate-100 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider pl-6">Nama Kandidat</th>
                                @foreach($kriterias as $k)
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center">
                                    {{ $k->kode }}
                                    <span class="block text-[9px] font-normal lowercase text-slate-400">({{ $k->jenis }})</span>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($matriks as $m)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3 text-sm font-bold text-slate-700 pl-6">{{ $m['nama'] }}</td>
                                @foreach($kriterias as $k)
                                <td class="px-4 py-3 text-sm text-slate-600 text-center font-mono">
                                    {{ $m[$k->kode] }}
                                </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr><td colspan="{{ count($kriterias) + 1 }}" class="px-6 py-12 text-center text-slate-400 text-sm italic">Belum ada data untuk dihitung.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800 flex items-start gap-3">
                        <div class="bg-blue-100 p-1.5 rounded text-blue-600 shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                        <div>
                            <strong class="block mb-1 text-blue-900">Rumus SAW:</strong>
                            <ul class="list-disc ml-4 space-y-0.5 text-blue-700/80">
                                <li>Jika Kriteria <strong>Benefit</strong>: Nilai Kandidat / Nilai Maksimum Kolom</li>
                                <li>Jika Kriteria <strong>Cost</strong>: Nilai Minimum Kolom / Nilai Kandidat</li>
                                <li>Skor Akhir = Σ (Nilai Normalisasi × Bobot Kriteria)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="chatBot()"
             class="fixed bottom-8 right-8 z-50 flex flex-col items-end gap-4" x-cloak>

            <div x-show="chatOpen" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-10 scale-95"
                 class="w-[380px] h-[550px] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden ring-1 ring-black/5">
                
                <div class="bg-slate-900 p-5 flex justify-between items-center shrink-0 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">AI</div>
                        <div><h3 class="text-white font-bold text-sm">Asisten HRD</h3><p class="text-slate-400 text-[10px] flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Online System</p></div>
                    </div>
                    
                    <div class="flex items-center gap-1">
                        <button @click="clearHistory()" class="text-slate-400 hover:text-rose-400 transition-colors bg-white/10 p-2 rounded-full hover:bg-white/20" title="Hapus Riwayat Chat">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        <button @click="chatOpen = false" class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-full hover:bg-white/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 bg-slate-50 p-5 overflow-y-auto custom-scrollbar space-y-5" x-ref="chatBody">
                    <template x-for="(msg, index) in messages" :key="index">
                        <div>
                            <div x-show="msg.role === 'user'" class="flex justify-end"><div class="bg-blue-600 text-white text-xs py-3 px-4 rounded-2xl rounded-tr-sm max-w-[85%] shadow-md leading-relaxed font-medium" x-html="msg.text"></div></div>
                            <div x-show="msg.role === 'bot'" class="flex justify-start items-end gap-3"><div class="bg-white border border-slate-200 text-slate-700 text-xs py-3 px-4 rounded-2xl rounded-tl-sm max-w-[85%] shadow-sm leading-relaxed font-medium" x-html="msg.text"></div></div>
                            <div x-show="msg.role === 'proposal'" class="pl-0 mt-3">
                                <div class="bg-white border border-blue-100 rounded-xl p-4 shadow-sm w-full ring-1 ring-blue-500/10">
                                    <div class="text-xs font-bold text-slate-800 mb-3 border-b border-slate-100 pb-2 flex items-center gap-2"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Rekomendasi AI</div>
                                    <div class="space-y-2 mb-4"><template x-for="item in msg.data"><div class="flex justify-between text-[10px] bg-slate-50 p-2.5 rounded-lg border border-slate-100"><span class="font-bold text-slate-600" x-text="item.nama"></span><span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded" x-text="item.bobot + '%'"></span></div></template></div>
                                    <button @click="applyConfig(msg.data)" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold py-3 rounded-lg transition-all shadow-md shadow-blue-500/20 hover:-translate-y-0.5 flex justify-center items-center gap-2"><span>Terapkan Konfigurasi</span><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="isLoading" class="flex justify-start"><div class="bg-white border border-slate-200 px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex gap-1"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div></div>
                </div>

                <div class="p-4 bg-white border-t border-slate-200 shrink-0">
                    <div class="relative flex items-center">
                        <input type="text" x-model="userInput" @keydown.enter="sendMessage()" :disabled="isLoading" 
                               placeholder="Ketik pesan untuk asisten..." 
                               class="w-full text-xs font-medium bg-slate-50 border border-slate-200 rounded-full py-3.5 pl-5 pr-12 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400 disabled:bg-slate-200 disabled:cursor-not-allowed">
                        <button @click="sendMessage()" :disabled="!userInput || isLoading" class="absolute right-2 p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-md"><svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg></button>
                    </div>
                </div>
            </div>

            <button @click="chatOpen = !chatOpen" class="w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] transition-all transform hover:scale-110 flex items-center justify-center hover:shadow-blue-500/40">
                <svg x-show="!chatOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                <svg x-show="chatOpen" class="w-7 h-7 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <span class="absolute top-0 right-0 w-4 h-4 bg-rose-500 border-2 border-white rounded-full animate-bounce"></span>
            </button>
        </div>

        <x-pdf-modal />
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatBot', () => ({
                chatOpen: false,
                messages: JSON.parse(localStorage.getItem('chat_history')) || [{ role: 'bot', text: 'Halo! 👋<br>Saya Asisten AI. Ada yang bisa saya bantu terkait kriteria rekrutmen?' }],
                userInput: '',
                isLoading: false,

                // Fungsi untuk Simpan ke LocalStorage
                saveHistory() {
                    localStorage.setItem('chat_history', JSON.stringify(this.messages));
                },

                // Fungsi Hapus Riwayat
                clearHistory() {
                    Swal.fire({
                        title: 'Hapus Riwayat?',
                        text: "Semua percakapan akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            localStorage.removeItem('chat_history');
                            this.messages = [{ role: 'bot', text: 'Halo! 👋<br>Riwayat telah dihapus. Ada yang bisa saya bantu lagi?' }];
                            Swal.fire('Terhapus!', 'Riwayat percakapan telah dibersihkan.', 'success');
                        }
                    });
                },

                async sendMessage() {
                    if(!this.userInput.trim() || this.isLoading) return;
                    
                    // 1. Push pesan user
                    this.messages.push({ role: 'user', text: this.userInput });
                    let currentMsg = this.userInput; 
                    this.userInput = ''; 
                    this.isLoading = true;
                    this.saveHistory(); // Simpan
                    
                    this.$nextTick(() => { this.$refs.chatBody.scrollTop = this.$refs.chatBody.scrollHeight; });

                    try {
                        const response = await fetch("{{ route('chat.send') }}", { 
                            method: 'POST', 
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" }, 
                            body: JSON.stringify({ message: currentMsg }) 
                        });
                        const data = await response.json();
                        this.isLoading = false; 
                        this.processResponse(data.reply);
                    } catch (e) { 
                        this.isLoading = false; 
                        this.messages.push({ role: 'bot', text: '⚠️ Maaf, koneksi terputus.' }); 
                        this.saveHistory(); // Simpan error juga
                    }
                },

                processResponse(rawReply) {
                    const jsonStart = '|||JSON_START|||'; const jsonEnd = '|||JSON_END|||';
                    
                    if (rawReply.includes(jsonStart)) {
                        let parts = rawReply.split(jsonStart);
                        if(parts[0].trim()) {
                            this.messages.push({ role: 'bot', text: this.formatText(parts[0].trim()) });
                        }
                        try {
                            let jsonData = JSON.parse(rawReply.split(jsonStart)[1].split(jsonEnd)[0]);
                            this.messages.push({ role: 'proposal', data: jsonData });
                        } catch(e) { console.error(e); }
                    } else { 
                        this.messages.push({ role: 'bot', text: this.formatText(rawReply) }); 
                    }
                    
                    this.saveHistory(); // Simpan balasan bot
                    this.$nextTick(() => { this.$refs.chatBody.scrollTop = this.$refs.chatBody.scrollHeight; });
                },

                formatText(text) { return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>'); },

                async applyConfig(data) {
                    const result = await Swal.fire({
                        title: 'Terapkan Rekomendasi?',
                        text: "Data kriteria lama akan diganti dengan rekomendasi AI ini.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Terapkan!',
                        cancelButtonText: 'Batal'
                    });

                    if(!result.isConfirmed) return;

                    try {
                        Swal.fire({ title: 'Menyimpan...', didOpen: () => { Swal.showLoading() } });
                        const res = await fetch("{{ route('chat.apply') }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" }, body: JSON.stringify({ criteria: data }) });
                        const apiResult = await res.json();
                        
                        if(apiResult.success) { 
                            await Swal.fire({
                                title: 'Berhasil!',
                                text: 'Konfigurasi telah diterapkan. Halaman akan dimuat ulang.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                            window.location.reload(); 
                        }
                    } catch(e) { 
                        Swal.fire('Error', 'Gagal menerapkan perubahan.', 'error'); 
                    }
                }
            }));
        });

        document.addEventListener('alpine:init', () => {
             Alpine.data('hrdDashboard', () => ({
                    showSettings: false,
                    showPrint: false,
                    showMatrix: false,
                    showPdfModal: false,
                    pdfUrl: '',
                    viewPdf(url) {
                        this.pdfUrl = url;
                        this.showPdfModal = true;
                    },
                    generatePdf() { window.open("{{ route('laporan.cetak') }}", '_blank'); },
                    async analyzeCv(pelamarId) {
                        Swal.fire({
                            title: 'Menganalisis CV...',
                            text: 'AI sedang membaca file PDF dan mencocokkan dengan kriteria.',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading() }
                        });

                        try {
                            const res = await fetch("{{ route('chat.analyze') }}", {
                                method: 'POST',
                                headers: { 
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ pelamar_id: pelamarId })
                            });
                            const result = await res.json();

                            if(result.success) {
                                // Tampilkan Summary
                                await Swal.fire({
                                    title: 'Analisis Selesai!',
                                    html: `<div class="text-left text-sm text-slate-600 space-y-2">
                                        <p><strong>Ringkasan Profil:</strong><br>${result.data.summary}</p>
                                        <p class="text-xs italic mt-2">*Nilai telah diisi otomatis ke formulir. Silakan review sebelum simpan.*</p>
                                    </div>`,
                                    icon: 'success'
                                });

                                // Auto-fill Form
                                const form = document.getElementById('form-' + pelamarId);
                                if(form) {
                                    const scores = result.data.scores;
                                    for (const [kode, nilai] of Object.entries(scores)) {
                                        const select = form.querySelector(`select[name="${kode}"]`);
                                        if(select) {
                                            select.value = nilai;
                                        }
                                    }
                                }
                            } else {
                                Swal.fire('Gagal', result.message, 'error');
                            }
                        } catch(e) {
                            console.error(e);
                            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                        }
                    }
                 }));
        });
    </script>
</x-app-layout>
