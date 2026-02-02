<x-app-layout>
    <x-slot name="head">
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <style>
            .prose h3 { font-size: 1.1em; font-weight: 700; margin-top: 1em; margin-bottom: 0.5em; color: #1e293b; }
            .prose ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1em; }
            .prose li { margin-bottom: 0.25em; }
            .prose p { margin-bottom: 0.8em; line-height: 1.6; }
            .prose strong { color: #0f172a; font-weight: 700; }
        </style>
    </x-slot>

    <div class="min-h-screen bg-gray-50 pb-20" x-data="detailPerhitungan({
        kriterias: {{ json_encode($kriterias) }},
        matriksX: {{ json_encode($matriksX) }},
        matriksR: {{ json_encode($matriksR) }},
        ranking: {{ json_encode($ranking) }}
    })">
        
        <!-- Header -->
        <div class="bg-[#232f3e] border-b border-[#232f3e] sticky top-0 z-30 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="bg-white/10 text-white p-2.5 rounded shadow-sm hover:bg-white/20 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h1 class="font-bold text-white text-xl leading-none tracking-tight">{{ __('Calculation Details') }}</h1>
                        <span class="text-xs text-gray-400 font-medium mt-1 block">SAW Method Breakdown</span>
                    </div>
                </div>
            </div>
        </div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            <!-- AI Insight Section -->
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-indigo-100 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-indigo-500 to-purple-600"></div>
                <div class="p-6 md:p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-900 text-lg">AI Executive Insight</h2>
                                <p class="text-sm text-gray-500">Automated analysis of calculation results</p>
                            </div>
                        </div>
                        <button @click="explainCalculation" :disabled="loading" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                Analyze with AI
                            </span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Analyzing...
                            </span>
                        </button>
                    </div>

                    <div x-show="result" class="prose prose-sm max-w-none text-slate-600 bg-slate-50 p-6 rounded-xl border border-slate-100" x-html="marked.parse(result)"></div>
                    
                    <div x-show="!result && !loading" class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <p class="text-slate-400 text-sm">Click "Analyze with AI" to generate a detailed explanation of the ranking results.</p>
                    </div>
                </div>
            </div>

            <!-- Steps Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- 1. Kriteria -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">1</span>
                        Criteria Weights (W)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3">Code</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3 text-right">Weight</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($kriterias as $k)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $k->kode }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $k->nama }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $k->jenis == 'benefit' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">{{ $k->jenis }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-700">{{ $k->bobot }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Data Awal -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">2</span>
                        Initial Matrix (X)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3">Supplier</th>
                                    @foreach($kriterias as $k)
                                    <th class="px-4 py-3 text-center">{{ $k->kode }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($matriksX as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $row['nama'] }}</td>
                                    @foreach($kriterias as $k)
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $row[$k->kode] }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Normalisasi -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">3</span>
                        Normalized Matrix (R)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3">Supplier</th>
                                    @foreach($kriterias as $k)
                                    <th class="px-4 py-3 text-center">{{ $k->kode }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($matriksR as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $row['nama'] }}</td>
                                    @foreach($kriterias as $k)
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $row[$k->kode] }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Hasil Akhir -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">4</span>
                        Final Ranking (V)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3">Rank</th>
                                    <th class="px-4 py-3">Supplier</th>
                                    <th class="px-4 py-3 text-right">Final Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($ranking as $index => $r)
                                <tr class="{{ $index == 0 ? 'bg-emerald-50/50' : '' }}">
                                    <td class="px-4 py-3 font-bold {{ $index == 0 ? 'text-emerald-600' : 'text-slate-500' }}">#{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $r['nama'] }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">{{ $r['skor_kalkulasi'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script>

        document.addEventListener('alpine:init', () => {
            Alpine.data('detailPerhitungan', (initialData) => ({
                loading: false,
                result: '',
                // Data from PHP passed via x-data
                calculationData: initialData,

                async explainCalculation() {
                    this.loading = true;
                    this.result = '';
                    
                    try {
                        const response = await fetch("{{ route('chat.explain') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                data: this.calculationData
                            })
                        });

                        const data = await response.json();
                        
                        if (response.ok) {
                            this.result = data.reply;
                        } else {
                            this.result = '_Error: ' + (data.reply || 'Something went wrong') + '_';
                        }
                    } catch (error) {
                        console.error(error);
                        this.result = '_Error connecting to AI service._';
                    } finally {
                        this.loading = false;
                    }
                }
            }))
        })
    </script>
</x-app-layout>