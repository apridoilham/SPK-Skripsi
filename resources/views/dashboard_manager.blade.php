<x-app-layout>
    <x-slot name="head">
        <script src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
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
            
            /* Markdown Styles */
            .prose h1 { font-size: 1.25em; font-weight: 700; margin-bottom: 0.5em; }
            .prose h2 { font-size: 1.1em; font-weight: 600; margin-bottom: 0.4em; }
            .prose ul { list-style-type: disc; padding-left: 1.2em; margin-bottom: 0.5em; }
            .prose ol { list-style-type: decimal; padding-left: 1.2em; margin-bottom: 0.5em; }
            .prose p { margin-bottom: 0.5em; }
            .prose strong { font-weight: 600; color: #1e293b; }
        </style>
    </x-slot>

    <div class="min-h-screen bg-gray-50 pb-20" x-data="hrdDashboard">
         
        <!-- Header Section -->
        <div class="bg-[#232f3e] border-b border-[#232f3e] sticky top-0 z-30 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-white/10 text-white p-2.5 rounded shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-white text-xl leading-none tracking-tight">Purchasing Console</h1>
                        <span class="text-xs text-gray-400 font-medium mt-1 block">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button @click="showSettings = true" class="px-4 py-2.5 text-xs font-bold text-[#232f3e] bg-white border border-gray-300 rounded hover:bg-gray-100 shadow-sm transition-all flex items-center gap-2 group">
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-[#232f3e] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ __('Set Criteria') }}
                    </button>
                    <a href="{{ route('detail.perhitungan') }}" class="px-4 py-2.5 text-xs font-bold text-[#232f3e] bg-white border border-gray-300 rounded hover:bg-gray-100 shadow-sm transition-all flex items-center gap-2 group">
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-[#232f3e] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        {{ __('Calculation Details') }}
                    </a>
                    <button @click="generatePdf()" class="px-4 py-2.5 text-xs font-bold text-white bg-[#232f3e] border border-gray-600 rounded hover:bg-[#1a232e] shadow-sm transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        {{ __('Print Report') }}
                    </button>
                </div>
            </div>
        </div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            
            <!-- AI Decision Explainer Section -->
            <div class="bg-gradient-to-br from-[#232f3e] to-[#1a232e] rounded-2xl p-8 text-white shadow-xl relative overflow-hidden border border-gray-700" 
                 x-data="aiExplainer({{ Js::from($ranking ?? []) }})">
                
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                <div class="absolute -bottom-32 -left-20 w-64 h-64 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                        <div class="max-w-2xl">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-indigo-500/20 rounded-lg backdrop-blur-sm border border-indigo-400/30">
                                    <svg class="w-6 h-6 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <h2 class="text-2xl font-bold tracking-tight text-white">AI Decision Explainer</h2>
                            </div>
                            <p class="text-indigo-200 text-sm leading-relaxed mb-6">
                                {{ __('Get a professional business justification for the current supplier ranking. The AI analyzes scores, criteria weights, and trade-offs to explain why the top supplier won.') }}
                            </p>
                            
                            <button @click="explainDecision()" :disabled="explaining" class="px-6 py-3 bg-white text-[#232f3e] font-bold rounded-xl shadow-lg hover:bg-gray-50 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                                <svg x-show="!explaining" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                <svg x-show="explaining" class="animate-spin w-5 h-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="explaining ? 'Analyzing Ranking...' : 'Generate Executive Summary'"></span>
                            </button>
                        </div>

                        <!-- Result Display -->
                        <div x-show="explanation" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="flex-1 bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/10 md:min-w-[400px]">
                            <h3 class="text-xs font-bold text-indigo-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                                AI Analysis Result
                            </h3>
                            <div class="prose prose-invert prose-sm max-w-none text-gray-200" x-html="marked.parse(explanation)"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kriteria Cards -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($kriterias as $k)
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] relative overflow-hidden group hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.1)] transition-all duration-300">
                    <div class="absolute top-0 left-0 w-full h-1 {{ $k->jenis == 'benefit' ? 'bg-gradient-to-r from-emerald-400 to-emerald-600' : 'bg-gradient-to-r from-red-600 to-red-700' }}"></div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 group-hover:bg-slate-100 transition-colors">{{ $k->kode }}</span>
                        <span class="text-[10px] font-bold uppercase {{ $k->jenis == 'benefit' ? 'text-emerald-700 bg-emerald-50 border border-emerald-100' : 'text-red-700 bg-red-50 border border-red-100' }} px-2.5 py-1 rounded-full tracking-wide shadow-sm">{{ $k->jenis }}</span>
                    </div>
                    <h3 class="font-bold text-slate-800 truncate text-sm mb-4" title="{{ $k->nama }}">{{ $k->nama }}</h3>
                    <div class="flex justify-between items-end border-t border-slate-50 pt-3">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $k->bobot * 100 }}</span>
                            <span class="text-xs text-slate-400 font-bold mb-1">%</span>
                        </div>
                        <span class="text-[10px] font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded-md">{{ count($k->opsi ?? []) }} {{ __('Level') }}</span>
                    </div>
                </div>
                @endforeach
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                
                <!-- Input Penilaian -->
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center backdrop-blur-sm">
                            <div>
                                <h2 class="font-bold text-slate-800 text-base">{{ __('Supplier Assessment Input') }}</h2>
                                <p class="text-xs text-slate-500 mt-0.5">{{ __('Rate according to uploaded documents.') }}</p>
                            </div>
                            <span class="text-xs bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-bold text-slate-600 shadow-sm">{{ count($suppliers) }} {{ __('Suppliers') }}</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($suppliers as $p)
                            <div class="p-6 hover:bg-slate-50 transition-colors group relative" x-data="{ sending: false }">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="flex gap-4">
                                        @if($p->user && $p->user->profile_photo_path)
                                            <img src="{{ Storage::disk('public')->url($p->user->profile_photo_path) }}" alt="{{ $p->nama }}" class="w-12 h-12 rounded-xl object-cover shadow-md shadow-slate-200 border border-gray-100">
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-slate-200">{{ substr($p->nama, 0, 2) }}</div>
                                        @endif
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-bold text-slate-900 text-base">{{ $p->nama }}</h3>
                                            </div>
                                            <a href="#" @click.prevent="viewPdf('{{ route('view.pdf', $p->file_berkas) }}')" class="text-xs font-semibold text-[#232f3e] hover:text-[#232f3e] flex items-center gap-1.5 mt-1 group/link transition-colors">
                                                <svg class="w-3.5 h-3.5 text-[#232f3e] group-hover/link:text-[#1a232e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> 
                                                <span class="group-hover/link:underline decoration-[#232f3e] underline-offset-2">{{ __('View Offer Document') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <button form="form-{{ $p->id }}" type="submit" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-white hover:border-emerald-500 hover:bg-emerald-500 transition-all flex items-center justify-center shadow-sm group/btn" title="{{ __('Save Score') }}">
                                        <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                                
                                <form id="form-{{ $p->id }}" action="{{ route('nilai.update', $p->id) }}" method="POST" class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                                    @csrf @method('PUT')
                                    @foreach($kriterias as $k)
                                        @php 
                                            $val = $p->nilai_kriteria[$k->kode] ?? 1; 
                                            $opsi = $k->opsi ?? ['1','2','3','4','5'];
                                        @endphp
                                        <div class="relative">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5 ml-1">{{ $k->nama }}</label>
                                            <div class="relative">
                                                <select name="{{ $k->kode }}" class="w-full text-xs font-semibold text-slate-700 border-slate-200 rounded focus:ring-[#232f3e] focus:border-[#232f3e] bg-white hover:bg-slate-50 transition-colors cursor-pointer appearance-none pl-3 pr-8 py-2.5">
                                                    @foreach($opsi as $idx => $label)
                                                        <option value="{{ $idx + 1 }}" {{ $val == ($idx + 1) ? 'selected' : '' }}>
                                                            {{ $idx + 1 }} - {{ is_numeric(substr($label,0,1)) ? substr($label,2) : $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </form>
                            </div>
                            @empty
                            <div class="p-16 text-center">
                                <div class="inline-block p-4 rounded-full bg-slate-50 text-slate-300 mb-4">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="text-slate-500 font-medium">{{ __('No supplier data received yet.') }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ __('Supplier data will appear here once they register.') }}</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Live Ranking -->
                <div class="xl:col-span-1">
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm sticky top-24 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-white z-10">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#232f3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        {{ __('Live Ranking') }}
                    </h2>
                                <div class="flex items-center gap-1.5 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide">{{ __('Realtime') }}</span>
                                </div>
                            </div>
                            <form action="{{ route('ranking.hitung') }}" method="POST">
                                @csrf
                                <button class="w-full bg-slate-900 text-white text-xs font-bold py-3 rounded-xl hover:bg-slate-800 shadow-lg shadow-slate-900/20 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    {{ __('Recalculate SAW') }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="divide-y divide-slate-100 max-h-[600px] overflow-y-auto custom-scrollbar bg-slate-50/30">
                            @forelse($ranking as $index => $r)
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors group relative overflow-hidden">
                                @if($index < 3)
                                <div class="absolute right-0 top-0 w-12 h-12 bg-gradient-to-bl {{ $index == 0 ? 'from-[#232f3e]/20' : ($index == 1 ? 'from-slate-200/50' : 'from-amber-100/50') }} to-transparent rounded-bl-3xl -mr-2 -mt-2"></div>
                                @endif
                                <div class="flex items-center gap-4 relative z-10">
                                    @if($r->user && $r->user->profile_photo_path)
                                        <div class="relative">
                                            <img src="{{ Storage::disk('public')->url($r->user->profile_photo_path) }}" alt="{{ $r->nama }}" class="w-8 h-8 rounded object-cover shadow-sm border border-gray-200">
                                            <div class="absolute -top-1.5 -left-1.5 w-4 h-4 rounded-full flex items-center justify-center text-[8px] font-bold shadow-sm border {{ $index == 0 ? 'bg-[#232f3e] text-white border-[#232f3e]' : ($index == 1 ? 'bg-slate-200 text-slate-600 border-slate-300' : ($index == 2 ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-white text-slate-400 border-slate-200')) }}">
                                                {{ $index + 1 }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="w-8 h-8 flex items-center justify-center rounded text-xs font-bold shadow-sm border {{ $index == 0 ? 'bg-[#232f3e] text-white border-[#232f3e]' : ($index == 1 ? 'bg-slate-200 text-slate-600 border-slate-300' : ($index == 2 ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-white text-slate-400 border-slate-200')) }}">
                                            {{ $index + 1 }}
                                        </span>
                                    @endif
                                    <div>
                                        <div class="text-sm font-bold text-slate-800 truncate w-32" title="{{ $r->nama }}">{{ $r->nama }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="text-[10px] font-mono font-bold text-[#232f3e] bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">{{ number_format($r->skor_akhir, 4) }}</div>
                                            @if($index == 0) <span class="text-[10px] text-[#232f3e] font-bold flex items-center"><svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>{{ __('Top 1') }}</span> @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-1 relative z-10">
                                    @if($r->status_supplier == 'Pending')
                                        <form action="{{ route('status.update', $r->id) }}" method="POST">@csrf @method('PUT')<button name="status" value="Lulus" class="w-8 h-8 rounded-lg flex items-center justify-center bg-white border border-emerald-200 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5" title="{{ __('Accept') }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></button></form>
                                        <form action="{{ route('status.update', $r->id) }}" method="POST">@csrf @method('PUT')<button name="status" value="Gagal" class="w-8 h-8 rounded-lg flex items-center justify-center bg-white border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5" title="{{ __('Reject') }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></form>
                                    @else
                                        <div class="flex flex-col items-end gap-1.5">
                                            <span class="text-[9px] font-bold px-2.5 py-1 rounded-full border uppercase tracking-wider {{ $r->status_supplier == 'Lulus' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                                {{ $r->status_supplier == 'Lulus' ? __('ACCEPTED') : __('REJECTED') }}
                                            </span>
                                            <form action="{{ route('status.update', $r->id) }}" method="POST">@csrf @method('PUT')<button name="status" value="Pending" class="text-[10px] text-slate-400 hover:text-[#232f3e] font-medium underline decoration-slate-300 underline-offset-2 hover:decoration-[#232f3e]/50 transition-all">{{ __('Reset Status') }}</button></form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="p-12 text-center">
                                <p class="text-slate-400 text-xs italic">{{ __('No ranking calculation yet.') }}</p>
                                <p class="text-[10px] text-slate-300 mt-1">{{ __('Click Recalculate button to start.') }}</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Settings Modal -->
        <div x-show="showSettings" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden ring-1 ring-slate-900/5" @click.away="showSettings = false"
                 x-data="{
                    items: {{ Js::from($kriterias->map(function($k){ return ['kode' => $k->kode, 'nama' => $k->nama, 'bobot' => $k->bobot * 100, 'jenis' => $k->jenis, 'opsi' => $k->opsi ?? ['','','','','']]; })) }},
                    add() { this.items.push({ kode: 'C'+(this.items.length+1), nama: '', bobot: 0, jenis: 'benefit', opsi: ['{{ __('Bad') }}','{{ __('Poor') }}','{{ __('Fair') }}','{{ __('Good') }}','{{ __('Excellent') }}'] }); },
                    remove(idx) { this.items.splice(idx, 1); this.items.forEach((item, i) => item.kode = 'C'+(i+1)); },
                    addOpsi(idx) { this.items[idx].opsi.push(''); },
                    removeOpsi(kIdx, oIdx) { if(this.items[kIdx].opsi.length > 1) this.items[kIdx].opsi.splice(oIdx, 1); },
                    get total() { return this.items.reduce((a,b) => a + parseFloat(b.bobot||0), 0); }
                 }">
                
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-10">
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">{{ __('Criteria Configuration') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ __('Set weights and SAW calculation parameters.') }}</p>
                    </div>
                    <div class="flex items-center gap-4 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">{{ __('Total Weight') }}</span>
                        <span class="text-xl font-black" :class="Math.abs(total - 100) < 0.1 ? 'text-emerald-600' : 'text-red-600'" x-text="total.toFixed(1) + '%'"></span>
                    </div>
                </div>

                <form action="{{ route('kriteria.update') }}" method="POST" class="flex-1 overflow-hidden flex flex-col bg-slate-50/50">
                    @csrf @method('PUT')
                    <div class="flex-1 overflow-y-auto p-8 space-y-6 custom-scrollbar">
                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative group hover:border-[#232f3e] hover:shadow-md transition-all duration-300">
                                <button type="button" @click="remove(idx)" class="absolute top-4 right-4 text-slate-300 hover:text-red-500 transition-colors bg-slate-50 p-2 rounded-lg hover:bg-red-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                
                                <div class="flex gap-6 mb-6">
                                    <div class="w-14 h-14 bg-slate-800 text-white rounded-2xl flex items-center justify-center font-bold text-base shadow-lg shadow-slate-200 ring-4 ring-slate-50">
                                        <input type="hidden" :name="`kriteria[${idx}][kode]`" x-model="item.kode">
                                        <span x-text="item.kode"></span>
                                    </div>
                                    <div class="flex-1 grid grid-cols-12 gap-5">
                                        <div class="col-span-6">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2">{{ __('Criteria Name') }}</label>
                                            <input type="text" :name="`kriteria[${idx}][nama]`" x-model="item.nama" class="w-full text-sm font-semibold text-slate-700 border-slate-200 rounded-xl focus:ring-[#232f3e] focus:border-[#232f3e] placeholder-slate-300" placeholder="{{ __('Example: Work Experience') }}" required>
                                        </div>
                                        <div class="col-span-3">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2">{{ __('Weight') }} (%)</label>
                                            <div class="relative">
                                                <input type="number" step="0.1" :name="`kriteria[${idx}][bobot]`" x-model="item.bobot" class="w-full text-sm font-bold text-slate-700 border-slate-200 rounded-xl focus:ring-[#232f3e] focus:border-[#232f3e] pl-4 pr-8" required>
                                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                                            </div>
                                        </div>
                                        <div class="col-span-3">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2">{{ __('Type') }}</label>
                                            <select :name="`kriteria[${idx}][jenis]`" x-model="item.jenis" class="w-full text-sm font-medium text-slate-700 border-slate-200 rounded-xl focus:ring-[#232f3e] focus:border-[#232f3e] cursor-pointer">
                                                <option value="benefit">{{ __('Benefit (+)') }}</option>
                                                <option value="cost">{{ __('Cost (-)') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                                    <div class="flex justify-between mb-4 items-center">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase flex items-center gap-2"><span class="w-2 h-2 bg-[#232f3e] rounded-full"></span> {{ __('Rating Scale (1 - Max)') }}</label>
                                        <button type="button" @click="addOpsi(idx)" class="text-[10px] text-[#232f3e] font-bold bg-white border border-slate-100 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">+ {{ __('Add Option') }}</button>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                        <template x-for="(opt, oIdx) in item.opsi" :key="oIdx">
                                            <div class="relative group/opt">
                                                <span class="absolute left-0 top-0 bottom-0 w-8 flex items-center justify-center text-[10px] font-bold text-slate-400 bg-slate-100 border-r border-slate-200 rounded-l-lg" x-text="oIdx+1"></span>
                                                <input type="text" :name="`kriteria[${idx}][opsi][]`" x-model="item.opsi[oIdx]" class="w-full pl-11 pr-7 py-2 text-xs font-medium text-slate-600 border-slate-200 rounded-lg focus:border-[#232f3e] focus:ring-[#232f3e] transition-shadow" placeholder="{{ __('Label') }}">
                                                <button type="button" @click="removeOpsi(idx, oIdx)" class="absolute right-1 top-2 text-slate-300 hover:text-red-500 opacity-0 group-hover/opt:opacity-100 transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="add()" class="w-full py-5 border-2 border-dashed border-slate-300 rounded-2xl text-slate-400 font-bold text-sm hover:border-[#232f3e] hover:text-[#232f3e] hover:bg-slate-50/30 transition-all flex items-center justify-center gap-3 group">
                            <div class="bg-slate-100 p-2 rounded-full group-hover:bg-slate-100 group-hover:text-[#232f3e] transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></div>
                            {{ __('Add New Criteria') }}
                        </button>
                    </div>
                    <div class="px-8 py-5 border-t border-slate-200 flex justify-end gap-3 bg-white">
                        <button type="button" @click="showSettings = false" class="px-6 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 border border-slate-200 rounded-xl transition-all">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-[#232f3e] text-white rounded-xl hover:bg-[#1a232f] shadow-lg shadow-blue-900/20 transition-all transform hover:-translate-y-0.5">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Matrix Modal -->
        <div x-show="showMatrix" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden ring-1 ring-slate-900/5" @click.away="showMatrix = false">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">{{ __('Normalization Matrix (R)') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ __('Pure values normalized (0-1) according to criteria type.') }}</p>
                    </div>
                    <button @click="showMatrix = false" class="text-slate-400 hover:text-red-500 transition-colors bg-slate-50 p-2 rounded-full hover:bg-red-50"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="p-8 overflow-auto custom-scrollbar bg-slate-50/50 flex-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Supplier Name') }}</th>
                                    @foreach($kriterias as $k)
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center group cursor-help" title="{{ $k->nama }}">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>{{ $k->kode }}</span>
                                            <span class="text-[9px] font-medium px-1.5 py-0.5 rounded {{ $k->jenis == 'benefit' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $k->jenis }}</span>
                                        </div>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($matriks as $m)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $m['nama'] }}</td>
                                    @foreach($kriterias as $k)
                                    <td class="px-6 py-4 text-sm text-slate-600 text-center font-mono">
                                        {{ $m[$k->kode] }}
                                    </td>
                                    @endforeach
                                </tr>
                                @empty
                                <tr><td colspan="{{ count($kriterias) + 1 }}" class="px-6 py-16 text-center text-slate-400 text-sm italic">{{ __('No data to calculate.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-8 p-5 bg-blue-50 border border-blue-100 rounded-2xl text-xs text-blue-800 flex items-start gap-4 shadow-sm">
                        <div class="bg-blue-100 p-2 rounded-lg text-blue-600 shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                        <div class="space-y-2">
                            <strong class="block text-sm text-blue-900">{{ __('SAW Formula:') }}</strong>
                            <ul class="space-y-1.5 text-blue-700/80 ml-1">
                                <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span> {{ __('If Criteria') }} <strong>Benefit</strong>: {{ __('Candidate Value / Column Maximum Value') }}</li>
                                <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span> {{ __('If Criteria') }} <strong>Cost</strong>: {{ __('Column Minimum Value / Candidate Value') }}</li>
                                <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span> {{ __('Final Score') }} = Σ ({{ __('Normalized Value') }} × {{ __('Criteria Weight') }})</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ChatBot UI -->
        <div x-data="chatBot()" class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-4" x-cloak>

            <!-- Chat Window -->
            <div x-show="chatOpen" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-10 scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-10 scale-95"
                 class="w-[500px] h-[700px] max-w-[calc(100vw-2rem)] max-h-[calc(100vh-2rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden ring-1 ring-slate-900/5 shadow-slate-900/20 font-sans">
                
                <!-- Header -->
                <div class="bg-[#232f3e] p-5 flex justify-between items-center shrink-0 shadow-md relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center text-white font-bold text-xl backdrop-blur-md border border-white/20 shadow-inner">
                            🤖
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base tracking-wide shadow-black drop-shadow-sm">{{ __('HRD Assistant') }}</h3>
                            <p class="text-emerald-400 text-[10px] font-bold flex items-center gap-1.5 mt-0.5 uppercase tracking-wider">
                                <span class="relative flex h-2.5 w-2.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 border border-white/20"></span>
                                </span>
                                {{ __('Online & Ready') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5 relative z-10">
                        <button @click="trainAi()" class="text-slate-300 hover:text-white hover:bg-white/10 p-2 rounded-lg transition-all transform hover:scale-105" title="{{ __('Teach AI New Rules') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </button>
                        <button @click="clearHistory()" class="text-slate-300 hover:text-red-400 hover:bg-white/10 p-2 rounded-lg transition-all transform hover:scale-105" title="{{ __('Clear Chat History') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        <button @click="chatOpen = false" class="text-slate-300 hover:text-white hover:bg-white/10 p-2 rounded-lg transition-all transform hover:scale-105 ml-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Chat Body -->
                <div class="flex-1 bg-slate-50 p-5 overflow-y-auto custom-scrollbar space-y-6" x-ref="chatBody">
                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="flex flex-col gap-1 transition-all duration-300 ease-in-out">
                            <!-- User Message -->
                            <div x-show="msg.role === 'user'" class="flex justify-end pl-12">
                                <div class="bg-[#232f3e] text-white text-sm py-3.5 px-5 rounded-2xl rounded-tr-sm shadow-lg shadow-slate-200 leading-relaxed font-medium transform transition-all hover:shadow-xl" x-html="msg.text"></div>
                            </div>
                            
                            <!-- Bot Message -->
                            <div x-show="msg.role === 'bot'" class="flex justify-start pr-12 items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-sm text-xl ring-2 ring-slate-50">🤖</div>
                                <div class="bg-white border border-slate-200 text-slate-700 text-sm py-4 px-6 rounded-2xl rounded-tl-none shadow-md shadow-slate-100 prose prose-sm max-w-none prose-p:leading-relaxed prose-li:marker:text-[#232f3e] prose-headings:text-[#232f3e] prose-strong:text-[#232f3e] prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline" x-html="marked.parse(msg.text)"></div>
                            </div>

                            <!-- Proposal/Recommendation Card -->
                            <div x-show="msg.role === 'proposal'" class="pl-11 pr-4 mt-1">
                                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm w-full ring-1 ring-slate-100 hover:shadow-md transition-shadow">
                                    <div class="text-xs font-bold text-[#232f3e] mb-3 border-b border-slate-100 pb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 
                                        {{ __('Recommended Criteria Configuration') }}
                                    </div>
                                    <div class="space-y-2 mb-4">
                                        <template x-for="item in msg.data">
                                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 mb-2">
                                                <div class="flex justify-between items-center text-xs mb-2">
                                                    <span class="font-bold text-slate-700 text-sm" x-text="item.nama"></span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider" :class="item.jenis === 'benefit' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200'" x-text="item.jenis"></span>
                                                        <span class="font-black text-[#232f3e] bg-white px-2 py-1 rounded border border-slate-200 shadow-sm" x-text="item.bobot + '%'"></span>
                                                    </div>
                                                </div>
                                                <!-- Show Options/Scale if available -->
                                                <div x-show="item.opsi && item.opsi.length" class="flex flex-wrap gap-1 mt-1">
                                                    <template x-for="(opt, idx) in item.opsi">
                                                        <span class="text-[9px] text-slate-500 bg-white border border-slate-200 px-1.5 py-0.5 rounded" x-text="(idx+1) + '. ' + opt"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <button @click="applyConfig(msg.data)" class="w-full bg-[#232f3e] hover:bg-[#1a232e] text-white text-xs font-bold py-3 rounded-xl transition-all shadow-md shadow-slate-900/10 hover:-translate-y-0.5 flex justify-center items-center gap-2 group">
                                        <span>{{ __('Apply Configuration') }}</span>
                                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Loading State -->
                    <div x-show="isLoading" class="flex justify-start items-center gap-3 pr-10">
                        <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-sm text-lg">🤖</div>
                        <div class="bg-white border border-slate-200 px-4 py-3.5 rounded-2xl rounded-tl-none shadow-sm flex gap-1.5 items-center">
                            <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-5 bg-white border-t border-slate-200 shrink-0">
                    <div class="relative flex items-center shadow-inner rounded-3xl bg-slate-50 border border-slate-200 focus-within:ring-2 focus-within:ring-[#232f3e] focus-within:bg-white transition-all duration-300">
                        <textarea x-model="userInput" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()" :disabled="isLoading" 
                               placeholder="Tanya strategi HR, analisis kandidat, atau lainnya..." 
                               rows="1"
                               @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                               class="w-full text-sm font-medium bg-transparent border-transparent rounded-3xl py-4 pl-5 pr-14 focus:ring-0 focus:border-transparent transition-all placeholder-slate-400 disabled:bg-slate-100 disabled:cursor-not-allowed resize-none overflow-hidden" style="min-height: 56px; max-height: 150px;"></textarea>
                        <button @click="sendMessage()" :disabled="!userInput || isLoading" 
                                class="absolute right-2 bottom-2 w-10 h-10 bg-[#232f3e] text-white rounded-full hover:bg-[#1a232f] transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg flex items-center justify-center group transform hover:scale-110 active:scale-95">
                            <svg class="w-5 h-5 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                    <div class="text-center mt-3 flex justify-between items-center px-2">
                         <p class="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Enter to send, Shift+Enter for new line') }}</span>
                         </p>
                        <p class="text-[10px] text-slate-400 font-medium">Powered by <span class="text-[#232f3e] font-bold">Llama 3.3 Elite</span></p>
                    </div>
                </div>
            </div>

            <!-- Toggle Button -->
            <button @click="chatOpen = !chatOpen" 
                    class="group w-14 h-14 bg-[#232f3e] hover:bg-[#1a232e] text-white rounded-2xl shadow-[0_8px_30px_rgba(35,47,62,0.4)] transition-all duration-300 transform hover:scale-105 flex items-center justify-center border-2 border-white/20 hover:border-white/40 z-50">
                <div class="relative">
                    <svg x-show="!chatOpen" class="w-7 h-7 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    <svg x-show="chatOpen" class="w-7 h-7 rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    <span x-show="!chatOpen" class="absolute -top-1.5 -right-1.5 flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border border-white"></span>
                    </span>
                </div>
            </button>
        </div>

        <x-pdf-modal />
        <div id="hrd-dashboard-config" data-config="{{ json_encode([
            'routes' => [
                'chat_send' => route('chat.send'),
                'chat_apply' => route('chat.apply'),
                'chat_teach' => route('chat.teach'),
                'chat_analyze' => route('chat.analyze'),
                'chat_explain' => route('chat.explain.decision'),
                'laporan_cetak' => route('laporan.cetak'),
            ],
            'csrf' => csrf_token(),
            'trans' => [
                'welcome' => __('Hello! 👋<br>I am CHCO & Grandmaster HR Auditor.<br>I am ready to perform **Candidate Forensic Audit** or design executive recruitment strategies.'),
                'clear_title' => __('Clear History?'),
                'clear_text' => __('All conversations will be permanently deleted.'),
                'clear_confirm' => __('Yes, Delete!'),
                'clear_cancel' => __('Cancel'),
                'cleared' => __('Hello! 👋<br>History has been cleared. Can I help you with anything else?'),
                'deleted_title' => __('Deleted!'),
                'deleted_text' => __('Conversation history has been cleared.'),
                'connection_lost' => '⚠️ ' . __('Sorry, connection lost.'),
                'apply_title' => __('Apply Recommendation?'),
                'apply_text' => __('Old criteria data will be replaced with this AI recommendation.'),
                'apply_confirm' => __('Yes, Apply!'),
                'saving_title' => __('Saving...'),
                'success_title' => __('Success!'),
                'success_text' => __('Configuration applied. Page will reload.'),
                'failed_apply' => __('Failed to apply changes.'),
                'teach_title' => __('Teach AI New Rules'),
                'topic_placeholder' => __('Topic (e.g., Work Experience)'),
                'rule_placeholder' => __('Rule (e.g., If experience < 1 year, max score 2)'),
                'save_rule' => __('Save Rule'),
                'topic_content_error' => __('Topic and Content cannot be empty'),
                'system_error' => __('System error occurred.'),
                'failed_label' => __('Failed'),
                'analyzing_title' => __('Analyzing CV...'),
                'analyzing_text' => __('AI is reading the PDF file and matching with criteria.'),
                'critical_red_flags' => __('CRITICAL RED FLAGS'),
                'deep_psychometrics' => __('DEEP PSYCHOMETRICS'),
                'leadership_potential' => __('Leadership Potential'),
                'culture_fit_score' => __('Culture Fit Score'),
                'work_style' => __('Work Style'),
                'competency_gaps' => __('Competency Gaps'),
                'interview_questions' => __('Suggested Interview Questions'),
                'ai_analysis_title' => __('AI Analysis (Forensic & Psychometric)'),
                'recommendation_label' => __('AI Recommendation'),
                'confidence_label' => __('Confidence'),
                'auto_filled_note' => __('*Scores have been auto-filled. Please review before saving.*'),
                'ai_recommendation_header' => __('AI Recommendation'),
                'apply_configuration' => __('Apply Configuration'),
                'considered' => __('CONSIDERED'),
                'medium' => __('MEDIUM'),
            ]
        ]) }}"></div>
    </div>
    <script>
        const configEl = document.getElementById('hrd-dashboard-config');
        const APP_CONFIG = configEl ? JSON.parse(configEl.dataset.config) : { routes: {}, trans: {} };
        const { routes, trans } = APP_CONFIG;

        document.addEventListener('alpine:init', () => {
            Alpine.data('chatBot', () => ({
                chatOpen: false,
                messages: JSON.parse(localStorage.getItem('chat_history')) || [{ role: 'bot', text: trans.welcome }],
                userInput: '',
                isLoading: false,

                // Fungsi untuk Simpan ke LocalStorage
                saveHistory() {
                    localStorage.setItem('chat_history', JSON.stringify(this.messages));
                },

                // Fungsi Hapus Riwayat
                clearHistory() {
                    Swal.fire({
                        title: trans.clear_title,
                        text: trans.clear_text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: trans.clear_confirm,
                        cancelButtonText: trans.clear_cancel
                    }).then((result) => {
                        if (result.isConfirmed) {
                            localStorage.removeItem('chat_history');
                            this.messages = [{ role: 'bot', text: trans.cleared }];
                            Swal.fire(trans.deleted_title, trans.deleted_text, 'success');
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
                        const historyPayload = this.messages.slice(-10).map(m => ({
                            role: m.role === 'bot' ? 'assistant' : 'user',
                            content: m.text
                        }));

                        const response = await fetch(routes.chat_send, { 
                            method: 'POST', 
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': APP_CONFIG.csrf }, 
                            body: JSON.stringify({ 
                                message: currentMsg,
                                history: historyPayload 
                            }) 
                        });
                        const data = await response.json();
                        this.isLoading = false; 
                        this.processResponse(data.reply);
                    } catch (e) { 
                        this.isLoading = false; 
                        this.messages.push({ role: 'bot', text: trans.connection_lost }); 
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
                        title: trans.apply_title,
                        text: trans.apply_text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: trans.apply_confirm,
                        cancelButtonText: trans.clear_cancel
                    });

                    if(!result.isConfirmed) return;

                    try {
                        Swal.fire({ title: trans.saving_title, didOpen: () => { Swal.showLoading() } });
                        const res = await fetch(routes.chat_apply, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': APP_CONFIG.csrf }, body: JSON.stringify({ criteria: data }) });
                        const apiResult = await res.json();
                        
                        if(apiResult.success) { 
                            await Swal.fire({
                                title: trans.success_title,
                                text: trans.success_text,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                            window.location.reload(); 
                        }
                    } catch(e) { 
                        Swal.fire('Error', trans.failed_apply, 'error'); 
                    }
                },

                async trainAi() {
                    const { value: formValues } = await Swal.fire({
                        title: trans.teach_title,
                        html:
                            '<input id="swal-topic" class="swal2-input" placeholder="' + trans.topic_placeholder + '">' +
                            '<textarea id="swal-content" class="swal2-textarea" placeholder="' + trans.rule_placeholder + '"></textarea>',
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: trans.save_rule,
                        cancelButtonText: trans.clear_cancel,
                        confirmButtonColor: '#10b981',
                        preConfirm: () => {
                            return {
                                topic: document.getElementById('swal-topic').value,
                                content: document.getElementById('swal-content').value
                            }
                        }
                    });

                    if (formValues) {
                        if(!formValues.topic || !formValues.content) {
                            Swal.fire('Error', trans.topic_content_error, 'error');
                            return;
                        }

                        try {
                            const res = await fetch(routes.chat_teach, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': APP_CONFIG.csrf },
                                body: JSON.stringify(formValues)
                            });
                            const result = await res.json();
                            if(result.success) {
                                Swal.fire(trans.success_title, result.message, 'success');
                            } else {
                                Swal.fire(trans.failed_label, result.message, 'error');
                            }
                        } catch(e) {
                            Swal.fire('Error', trans.system_error, 'error');
                        }
                    }
                }
            }));
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('aiExplainer', (initialRanking = []) => ({
                explaining: false,
                explanation: '',
                ranking: initialRanking,
                async explainDecision() {
                    this.explaining = true;
                    this.explanation = '';
                    
                    try {
                        const res = await fetch(routes.chat_explain, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': APP_CONFIG.csrf 
                            },
                            body: JSON.stringify({ ranking_data: this.ranking })
                        });
                        
                        const data = await res.json();
                        
                        if(data.error) throw new Error(data.error);
                        
                        this.explanation = data.reply;
                    } catch (err) {
                        console.error(err);
                        Swal.fire('AI Error', 'Failed to generate explanation. ' + (err.message || 'Unknown error'), 'error');
                    } finally {
                        this.explaining = false;
                    }
                }
            }));

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
                    generatePdf() { window.open(routes.laporan_cetak, '_blank'); },
                    async analyzeCv(pelamarId) {
                        Swal.fire({
                            title: trans.analyzing_title,
                            text: trans.analyzing_text,
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading() }
                        });

                        try {
                            const res = await fetch(routes.chat_analyze, {
                                method: 'POST',
                                headers: { 
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': APP_CONFIG.csrf
                                },
                                body: JSON.stringify({ pelamar_id: pelamarId })
                            });
                            const result = await res.json();

                            if(result.success) {
                                // Extract Data
                                const details = result.data.details || {}; 
                                const psychometrics = result.data.psychometrics || {};
                                const redFlags = result.data.red_flags || result.data.timeline_audit || [];
                                const interviewQuestions = result.data.interview_questions || [];
                                const competencyGap = result.data.competency_gap || [];

                                // --- COMPONENT BUILDERS ---
                                
                                // 1. Risk Analysis (Red Flags & Gaps)
                                let risksHtml = '';
                                if (redFlags.length > 0 || competencyGap.length > 0) {
                                    risksHtml += '<div class="mb-4">';
                                    
                                    if (redFlags.length > 0) {
                                        risksHtml += `<div class="bg-red-50 border border-red-100 rounded-lg p-3 mb-2">
                                            <h4 class="text-xs font-bold text-red-800 flex items-center gap-1 mb-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                CRITICAL RED FLAGS
                                            </h4>
                                            <ul class="space-y-1">
                                                ${redFlags.map(flag => `<li class="text-[11px] text-red-700 flex items-start gap-1.5 font-medium"><span class="mt-0.5">•</span>${flag}</li>`).join('')}
                                            </ul>
                                        </div>`;
                                    }

                                    if(competencyGap.length > 0) {
                                       risksHtml += `<div class="bg-amber-50 border border-amber-100 rounded-lg p-3">
                                           <h4 class="text-xs font-bold text-amber-800 mb-2 uppercase tracking-wide flex items-center gap-1">
                                               <span class="text-amber-500 text-sm">⚠️</span> ${trans.competency_gaps}
                                           </h4>
                                           <ul class="space-y-1">
                                               ${competencyGap.map(g => `<li class="text-[11px] text-amber-700 flex items-start gap-1.5"><span class="mt-0.5">•</span>${g}</li>`).join('')}
                                           </ul>
                                       </div>`;
                                    }
                                    risksHtml += '</div>';
                                }

                                // 2. Psychometrics Card (Left Sidebar)
                                let psychometricsHtml = '';
                                if (Object.keys(psychometrics).length > 0) {
                                    psychometricsHtml += `<div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm h-fit">
                                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                            ${trans.deep_psychometrics}
                                        </h4>
                                        
                                        <div class="space-y-4">
                                            <div>
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="text-slate-500 font-medium">${trans.leadership_potential}</span>
                                                    <span class="text-indigo-700 font-bold">${psychometrics.leadership_potential || psychometrics.leadership || '-'}</span>
                                                </div>
                                                <div class="w-full bg-slate-100 rounded-full h-1.5">
                                                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: ${(psychometrics.leadership_potential === 'High' ? 90 : (psychometrics.leadership_potential === 'Medium' ? 60 : 30))}%"></div>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="text-slate-500 font-medium">${trans.culture_fit_score}</span>
                                                    <span class="text-indigo-700 font-bold">${psychometrics.culture_fit_score || 0}%</span>
                                                </div>
                                                <div class="w-full bg-slate-100 rounded-full h-1.5">
                                                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: ${psychometrics.culture_fit_score || 0}%"></div>
                                                </div>
                                            </div>

                                            <div class="bg-slate-50 p-2 rounded border border-slate-100">
                                                <span class="text-xs text-slate-400 block mb-1 uppercase tracking-wider font-bold">Work Style</span>
                                                <span class="text-xs text-slate-700 font-medium block leading-relaxed">${psychometrics.work_style || '-'}</span>
                                            </div>

                                            <div class="flex flex-wrap gap-1.5 pt-2">
                                                ${(psychometrics.dominant_traits || psychometrics.traits || []).map(t => `<span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-[10px] font-bold border border-indigo-100">${t}</span>`).join('')}
                                            </div>
                                        </div>
                                    </div>`;
                                }

                                // 3. Detail Scores (Main Content)
                                let scoresHtml = '';
                                if (Object.keys(details).length > 0) {
                                    scoresHtml += '<div class="space-y-4">'; // Increased spacing
                                    for (const [key, item] of Object.entries(details)) {
                                        let scoreClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                        let reasonIcon = '';
                                        let scoreColor = '#64748b'; // default slate
                                        
                                        if (item.score >= 4) {
                                            scoreClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            scoreColor = '#10b981'; // emerald
                                            reasonIcon = '<span class="text-emerald-500 mr-2 text-lg">✓</span>';
                                        } else if (item.score === 3) {
                                            scoreClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                            scoreColor = '#f59e0b'; // amber
                                            reasonIcon = '<span class="text-amber-500 mr-2 text-lg">●</span>';
                                        } else {
                                            scoreClass = 'bg-red-50 text-red-700 border-red-200';
                                            scoreColor = '#ef4444'; // red
                                            reasonIcon = '<span class="text-red-500 mr-2 text-lg">⚠️</span>';
                                        }

                                        scoresHtml += `
                                            <div class="relative bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all group overflow-hidden">
                                                <!-- Side Color Indicator -->
                                                <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background-color: ${scoreColor}"></div>
                                                
                                                <div class="p-5 flex items-start gap-5">
                                                    <!-- Main Content -->
                                                    <div class="flex-1 space-y-3">
                                                        <div class="flex items-center gap-3">
                                                            <span class="font-bold text-slate-700 text-sm uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 shadow-sm">${key}</span>
                                                            <span class="h-px flex-1 bg-slate-100"></span>
                                                        </div>
                                                        
                                                        <div class="text-slate-800 text-[15px] font-medium leading-relaxed flex items-start">
                                                            ${item.reason}
                                                        </div>

                                                        ${item.evidence ? `
                                                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 mt-2">
                                                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                Bukti dari CV (Verbatim):
                                                            </div>
                                                            <div class="text-slate-600 italic text-xs font-serif leading-relaxed border-l-2 border-slate-300 pl-3">
                                                                "${item.evidence}"
                                                            </div>
                                                        </div>` : ''}
                                                    </div>

                                                    <!-- Score Circle -->
                                                    <div class="flex flex-col items-center justify-center gap-1 pt-1">
                                                        <div class="w-16 h-16 rounded-full flex items-center justify-center border-4 shadow-sm" style="border-color: ${scoreColor}20; background-color: ${scoreColor}05;">
                                                            <span class="text-2xl font-black" style="color: ${scoreColor}">${item.score}</span>
                                                        </div>
                                                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Score</span>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                    scoresHtml += '</div>';
                                }

                                // 4. Interview Questions
                                let interviewHtml = '';
                                if(interviewQuestions.length > 0) {
                                   interviewHtml += `<div class="bg-slate-800 text-slate-300 rounded-xl p-4 shadow-sm mt-4">
                                       <h4 class="text-xs font-bold text-white mb-3 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-slate-700">
                                           <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                           ${trans.interview_questions}
                                       </h4>
                                       <ul class="space-y-3">
                                            ${interviewQuestions.map(q => `<li class="text-[11px] flex items-start gap-2 italic leading-relaxed">
                                                <span class="text-emerald-400 font-bold not-italic shrink-0 text-lg">?</span> <span>"${q}"</span>
                                            </li>`).join('')}
                                        </ul>
                                    </div>`;
                                }

                                // --- ASSEMBLE GRID ---
                                // 2-Column Layout: Left (35%) | Right (65%)
                                let gridHtml = `
                                <div class="grid grid-cols-12 gap-6 mt-2 text-left">
                                    <!-- Column 1: Profile & Questions (4/12) -->
                                    <div class="col-span-4 flex flex-col">
                                        ${psychometricsHtml}
                                        ${interviewHtml}
                                    </div>

                                    <!-- Column 2: Detailed Scores & Risks (8/12) -->
                                    <div class="col-span-8">
                                        <!-- Risk Analysis First (If Critical) -->
                                        ${risksHtml}
                                        
                                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                            <h4 class="text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide flex items-center gap-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                Scoring Analysis
                                            </h4>
                                            ${scoresHtml}
                                        </div>
                                    </div>
                                </div>
                                `;
                                
                                // Determine Header Color based on Recommendation
                                let headerColor = 'bg-slate-800';
                                let headerText = 'text-white';
                                let rec = (result.data.recommendation || '').toUpperCase();
                                
                                if(rec.includes('HIGHLY') || rec.includes('RECOMMENDED')) {
                                    headerColor = 'bg-emerald-600';
                                } else if (rec.includes('NOT')) {
                                    headerColor = 'bg-red-600';
                                }

                                await Swal.fire({
                                    title: '',
                                    html: `<div class="text-left text-slate-600 font-sans">
                                        <!-- Header Hero -->
                                        <div class="${headerColor} ${headerText} p-6 -mx-5 -mt-5 mb-6 rounded-t-lg shadow-md flex justify-between items-center relative overflow-hidden">
                                            <div class="relative z-10">
                                                <div class="text-xs uppercase tracking-widest opacity-80 font-bold mb-1">AI Recommendation</div>
                                                <div class="font-extrabold text-3xl tracking-tight">${result.data.recommendation || trans.considered}</div>
                                            </div>
                                            <div class="text-right relative z-10">
                                                <div class="text-xs uppercase tracking-widest opacity-80 font-bold mb-1">Confidence Score</div>
                                                <div class="font-bold text-2xl bg-white/20 px-3 py-1 rounded-lg backdrop-blur-sm inline-block">
                                                    ${result.data.match_confidence || trans.medium}
                                                </div>
                                            </div>
                                            
                                            <!-- Decor element -->
                                            <div class="absolute -right-10 -bottom-10 opacity-10">
                                                <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z"></path></svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Summary Box -->
                                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
                                            <div class="flex gap-3">
                                                <div class="shrink-0 text-blue-500 mt-1">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <div>
                                                    <h5 class="font-bold text-blue-900 text-sm mb-1 uppercase tracking-wide">Executive Summary</h5>
                                                    <p class="text-sm text-blue-800 leading-relaxed italic">
                                                        "${result.data.summary}"
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        ${gridHtml}
                                        
                                        <p class="text-[10px] text-slate-400 italic text-center mt-6 border-t border-slate-100 pt-3">${trans.auto_filled_note}</p>
                                    </div>`,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Terapkan Hasil',
                                    confirmButtonColor: '#10b981', // Emerald 500
                                    width: '1100px',
                                    padding: '0',
                                    customClass: {
                                        popup: 'swal-wide-popup rounded-xl overflow-hidden'
                                    }
                                });

                                // Auto-fill Form
                                const form = document.getElementById('form-' + pelamarId);
                                if(form) {
                                    console.log("AI Results:", details);

                                    // Gunakan 'details' jika ada, jika tidak fallback ke 'scores'
                                    const dataToProcess = Object.keys(details).length > 0 ? details : scores;

                                    for (const [key, val] of Object.entries(dataToProcess)) {
                                        // Handle struktur baru vs lama
                                        const nilai = (typeof val === 'object' && val.score) ? val.score : val;
                                        
                                        // Coba cari select dengan nama exact match
                                        let select = form.querySelector(`select[name="${key}"]`);
                                        
                                        // Jika tidak ketemu, coba uppercase
                                        if (!select) {
                                            select = form.querySelector(`select[name="${key.toUpperCase()}"]`);
                                        }

                                        if(select) {
                                            select.value = nilai;
                                            // Trigger visual feedback
                                            select.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50');
                                            setTimeout(() => {
                                                select.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50');
                                            }, 2000);
                                        } else {
                                            console.warn(`Field kriteria tidak ditemukan: ${key}`);
                                        }
                                    }
                                }
                            } else {
                                Swal.fire(trans.failed_label, result.message, 'error');
                            }
                        } catch(e) {
                            console.error(e);
                            Swal.fire('Error', trans.system_error, 'error');
                        }
                    }
                 }));
        });
    </script>
</x-app-layout>
