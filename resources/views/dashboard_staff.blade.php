<x-app-layout>
    <x-slot name="head">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </x-slot>

    <div class="min-h-screen bg-gray-50 pb-20" x-data="staffDashboard()">
        
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 text-white p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h1 class="font-bold text-gray-800 text-lg">Staff Admin Purchasing</h1>
                </div>
                <button @click="openAddModal()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Supplier
                </button>
            </div>
        </div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            
            <!-- AI Data Extractor Section -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h2 class="font-bold text-gray-800">AI Data Extractor</h2>
                    </div>
                    <p class="text-sm text-gray-500">Paste raw text from WhatsApp, Email, or Offer Letters below. AI will extract supplier details automatically.</p>
                </div>
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <textarea x-model="rawText" class="w-full h-40 rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Paste text here... (e.g. 'Halo, kami dari PT Maju Jaya menawarkan Laptop Asus seharga Rp 15.000.000, pengiriman 2 hari...')"></textarea>
                        <div class="mt-3 flex justify-end">
                            <button @click="extractData()" :disabled="loadingExtract" class="px-4 py-2 bg-gray-800 text-white text-xs font-bold rounded-lg hover:bg-gray-900 transition flex items-center gap-2 disabled:opacity-50">
                                <svg x-show="loadingExtract" class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="loadingExtract ? 'Extracting...' : 'Extract Data'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 relative">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Extracted Result</h3>
                        <div x-show="!extractedData" class="text-center py-8 text-gray-400 text-sm">
                            Result will appear here...
                        </div>
                        <div x-show="extractedData" class="space-y-3" style="display: none;">
                            <template x-for="(value, key) in extractedData" :key="key">
                                <div class="flex justify-between text-sm border-b border-gray-100 pb-2 last:border-0">
                                    <span class="text-gray-500 capitalize" x-text="key.replace('_', ' ')"></span>
                                    <span class="font-semibold text-gray-800" x-text="value"></span>
                                </div>
                            </template>
                            <button @click="fillForm()" class="w-full mt-4 px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">
                                Use This Data
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Supplier List -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Supplier List</h2>
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-full">{{ count($suppliers) }} Suppliers</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Contact</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($suppliers as $s)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $s->nama }}</td>
                                <td class="px-6 py-4 text-gray-500">
                                    <div class="flex flex-col">
                                        <span>{{ $s->email ?? '-' }}</span>
                                        <span class="text-xs">{{ $s->telepon ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold 
                                        {{ $s->status_supplier == 'Pending' ? 'bg-amber-100 text-amber-700' : 
                                           ($s->status_supplier == 'Lulus' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $s->status_supplier }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <button @click="openNegotiation('{{ $s->nama }}')" class="px-3 py-1.5 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                        Negotiate
                                    </button>
                                    <button @click="editSupplier({{ $s->id }}, '{{ $s->nama }}', '{{ $s->email }}', '{{ $s->telepon }}')" class="px-3 py-1.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-lg text-xs font-bold transition">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No suppliers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <!-- Add/Edit Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" @click="showModal = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="isEdit ? 'Edit Supplier' : 'Add New Supplier'"></h3>
                    <form :action="formAction" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Company/Supplier Name</label>
                            <input type="text" name="nama" x-model="form.nama" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" x-model="form.email" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Phone</label>
                            <input type="text" name="telepon" x-model="form.telepon" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Proposal PDF</label>
                            <input type="file" name="file_berkas" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-200">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700">Save Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Negotiation Modal -->
        <div x-show="showNegotiation" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showNegotiation" @click="showNegotiation = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">AI Negotiation Coach</h3>
                        <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2 py-1 rounded-lg" x-text="negotiationTarget"></span>
                    </div>
                    
                    <div class="space-y-4" x-show="!negotiationResult">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Competitor Name</label>
                            <input type="text" x-model="negCompetitor" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="e.g. Toko Sebelah Murah">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Price Difference / Gap</label>
                            <input type="text" x-model="negGap" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="e.g. Rp 500.000 cheaper">
                        </div>
                        <button @click="generateNegotiation()" :disabled="loadingNeg" class="w-full py-2.5 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 transition flex justify-center items-center gap-2">
                            <svg x-show="loadingNeg" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Generate Script
                        </button>
                    </div>

                    <div x-show="negotiationResult" class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed" x-text="negotiationResult"></div>
                        <div class="flex justify-end gap-3">
                            <button @click="negotiationResult = null" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-200">Back</button>
                            <button @click="copyToClipboard()" class="px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-lg hover:bg-purple-700">Copy Text</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function staffDashboard() {
            return {
                showModal: false,
                isEdit: false,
                form: { nama: '', email: '', telepon: '' },
                formAction: '{{ route("supplier.store") }}',
                
                // AI Data Extractor State
                rawText: '',
                loadingExtract: false,
                extractedData: null,

                // AI Negotiation State
                showNegotiation: false,
                negotiationTarget: '',
                negCompetitor: '',
                negGap: '',
                loadingNeg: false,
                negotiationResult: null,

                openAddModal() {
                    this.isEdit = false;
                    this.form = { nama: '', email: '', telepon: '' };
                    this.formAction = '{{ route("supplier.store") }}';
                    this.showModal = true;
                },

                editSupplier(id, nama, email, telepon) {
                    this.isEdit = true;
                    this.form = { nama, email, telepon };
                    this.formAction = '{{ url("supplier") }}/' + id;
                    this.showModal = true;
                },

                // AI Function 1: Extract Data
                async extractData() {
                    if(!this.rawText) return alert('Please enter text first!');
                    this.loadingExtract = true;
                    try {
                        const res = await fetch('{{ route("chat.extract") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ text: this.rawText })
                        });
                        const data = await res.json();
                        this.extractedData = typeof data === 'string' ? JSON.parse(data) : data;
                    } catch (e) {
                        alert('Error extracting data');
                    } finally {
                        this.loadingExtract = false;
                    }
                },

                fillForm() {
                    if(this.extractedData) {
                        this.form.nama = this.extractedData.nama_supplier || '';
                        this.openAddModal();
                    }
                },

                // AI Function 2: Negotiation Coach
                openNegotiation(name) {
                    this.negotiationTarget = name;
                    this.negCompetitor = '';
                    this.negGap = '';
                    this.negotiationResult = null;
                    this.showNegotiation = true;
                },

                async generateNegotiation() {
                    if(!this.negCompetitor || !this.negGap) return alert('Please fill all fields');
                    this.loadingNeg = true;
                    try {
                        const res = await fetch('{{ route("chat.negotiate") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ 
                                winner_name: this.negotiationTarget,
                                competitor_name: this.negCompetitor,
                                price_gap: this.negGap
                            })
                        });
                        const data = await res.json();
                        this.negotiationResult = data.reply;
                    } catch (e) {
                        alert('Error generating script');
                    } finally {
                        this.loadingNeg = false;
                    }
                },
                
                copyToClipboard() {
                    navigator.clipboard.writeText(this.negotiationResult);
                    alert('Copied!');
                }
            }
        }
    </script>
</x-app-layout>
