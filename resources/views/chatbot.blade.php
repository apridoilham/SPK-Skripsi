<x-app-layout>
    <x-slot name="head">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            .chat-bubble { max-width: 80%; border-radius: 1.25rem; padding: 1rem 1.25rem; position: relative; font-size: 0.95rem; line-height: 1.5; }
            .chat-user { background: #4f46e5; color: white; border-bottom-right-radius: 0.25rem; margin-left: auto; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }
            .chat-bot { background: white; color: #1f2937; border-bottom-left-radius: 0.25rem; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
            
            /* Animasi Typing */
            .typing-dot { width: 6px; height: 6px; background: #9ca3af; border-radius: 50%; animation: typing 1.4s infinite ease-in-out; }
            .typing-dot:nth-child(1) { animation-delay: 0s; }
            .typing-dot:nth-child(2) { animation-delay: 0.2s; }
            .typing-dot:nth-child(3) { animation-delay: 0.4s; }
            @keyframes typing { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
            @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .msg-enter { animation: slideIn 0.3s ease-out forwards; }
        </style>
    </x-slot>

    <div class="flex flex-col h-[calc(100vh-65px)] bg-gray-100">
        
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm z-20">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">Asisten HRD Cerdas</h1>
                    <p class="text-xs text-gray-500 font-medium flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Online • Siap Membantu
                    </p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600 font-medium transition-colors bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:border-indigo-200">
                Kembali ke Dashboard
            </a>
        </div>

        <div id="chat-container" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 scroll-smooth bg-[#f8fafc]">
            <div class="max-w-4xl mx-auto space-y-6 pb-4">
                
                <div class="flex justify-center">
                    <span class="bg-gray-200 text-gray-600 text-[10px] uppercase font-bold tracking-wider py-1 px-3 rounded-full">Hari ini</span>
                </div>

                <div class="flex items-end gap-3 msg-enter">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 border border-indigo-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <div class="chat-bubble chat-bot">
                        Halo <strong>{{ Auth::user()->name }}</strong>! 👋<br>
                        Saya bisa diajak ngobrol santai atau diminta bantuan teknis.<br><br>
                        Contoh perintah:
                        <ul class="list-disc ml-4 mt-1 text-gray-600 text-sm space-y-1">
                            <li>"Halo, apa kabar?" (Ngobrol)</li>
                            <li>"Saya ingin rekrut Programmer, buatkan kriterianya" (Kerja)</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white border-t border-gray-200 p-4 z-20">
            <div class="max-w-4xl mx-auto">
                <form id="chat-form" class="relative flex items-end gap-3 bg-gray-50 border border-gray-300 rounded-2xl px-2 py-2 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-transparent transition-all shadow-sm">
                    @csrf
                    <textarea id="user-input" rows="1" class="w-full bg-transparent border-none text-gray-800 text-sm focus:ring-0 block p-3 resize-none custom-scrollbar max-h-32 placeholder-gray-400" placeholder="Ketik pesan Anda..." required></textarea>
                    
                    <button type="submit" id="send-btn" class="p-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed mb-0.5 group">
                        <svg class="w-5 h-5 rotate-90 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
                <div class="text-center mt-2">
                    <p class="text-[10px] text-gray-400">AI Asisten HRD - Powered by Groq Llama 3</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        const chatContainer = document.getElementById('chat-container');
        const containerInner = chatContainer.querySelector('div');
        const chatForm = document.getElementById('chat-form');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');

        // Auto Resize Textarea
        userInput.addEventListener('input', function() {
            this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';
            if(this.value === '') this.style.height = 'auto';
        });
        
        // Enter to Submit
        userInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault(); chatForm.dispatchEvent(new Event('submit'));
            }
        });

        // Submit Handler
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = userInput.value.trim();
            if (!message) return;

            // 1. Tampilkan Pesan User
            appendMessage('user', message);
            userInput.value = ''; userInput.style.height = 'auto';
            userInput.disabled = true; sendBtn.disabled = true;
            
            // 2. Loading
            const loadingId = appendLoading();

            try {
                // 3. Request ke API
                const response = await fetch("{{ route('chat.send') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                removeLoading(loadingId);
                
                // 4. Proses Jawaban (Cek apakah ada JSON)
                const rawReply = data.reply;
                const jsonStart = "|||JSON_START|||";
                const jsonEnd = "|||JSON_END|||";

                if (rawReply.includes(jsonStart)) {
                    // --- MODE KERJA: Ada Rekomendasi ---
                    const parts = rawReply.split(jsonStart);
                    const textPart = parts[0].trim(); // Teks penjelasan
                    
                    // Tampilkan teks dulu
                    if(textPart) appendMessage('bot', textPart);

                    try {
                        // Parsing JSON
                        const jsonString = rawReply.split(jsonStart)[1].split(jsonEnd)[0];
                        const jsonData = JSON.parse(jsonString);
                        // Tampilkan Kartu Proposal
                        appendProposalCard(jsonData);
                    } catch(err) {
                        console.error("JSON Error", err);
                        appendMessage('bot', "⚠️ Maaf, ada kesalahan teknis membaca data rekomendasi.");
                    }
                } else {
                    // --- MODE OBROLAN: Teks Biasa ---
                    appendMessage('bot', rawReply);
                }

            } catch (error) {
                removeLoading(loadingId);
                appendMessage('bot', "⚠️ Maaf, koneksi terputus. Silakan coba lagi.");
            } finally {
                userInput.disabled = false; sendBtn.disabled = false; userInput.focus();
            }
        });

        // Helper: Tampilkan Pesan Teks
        function appendMessage(role, text) {
            if(!text) return;
            const div = document.createElement('div');
            div.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start items-end gap-3'} msg-enter`;
            
            let content = '';
            if (role === 'user') {
                content = `<div class="chat-bubble chat-user">${escapeHtml(text).replace(/\n/g, '<br>')}</div>`;
            } else {
                content = `
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 border border-indigo-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <div class="chat-bubble chat-bot">${formatBotResponse(text)}</div>
                `;
            }

            div.innerHTML = content;
            containerInner.appendChild(div);
            scrollToBottom();
        }

        // Helper: Tampilkan Kartu Rekomendasi
        function appendProposalCard(criteriaData) {
            const div = document.createElement('div');
            div.className = "flex justify-start items-end gap-3 msg-enter mt-2 pl-11"; // Indentasi biar sejajar
            
            let itemsHtml = '';
            criteriaData.forEach(item => {
                itemsHtml += `
                    <div class="flex justify-between items-center bg-gray-50 p-2.5 rounded-lg border border-gray-200 text-xs">
                        <div>
                            <span class="block font-bold text-gray-700">${item.nama}</span>
                            <span class="text-[10px] text-gray-400">Skala: 1-${item.opsi ? item.opsi.length : 5}</span>
                        </div>
                        <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">${item.bobot}%</span>
                    </div>
                `;
            });

            const cardHtml = `
                <div class="bg-white border-2 border-indigo-100 px-5 py-4 rounded-2xl rounded-bl-none shadow-md w-full max-w-sm hover:border-indigo-300 transition-colors">
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                        <div class="bg-indigo-100 p-1.5 rounded text-indigo-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-800 text-sm">Rekomendasi Konfigurasi SPK</h3>
                    </div>
                    <div class="space-y-2 mb-4">
                        ${itemsHtml}
                    </div>
                    <button onclick='applyConfig(${JSON.stringify(criteriaData)})' class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all shadow-sm flex justify-center items-center gap-2 transform active:scale-95">
                        <span>Terapkan Ke Sistem</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    </button>
                    <p class="text-[10px] text-center text-gray-400 mt-2">Data kriteria akan otomatis diperbarui.</p>
                </div>
            `;

            div.innerHTML = cardHtml;
            containerInner.appendChild(div);
            scrollToBottom();
        }

        // Logic Apply Config
        async function applyConfig(data) {
            if(!confirm("Yakin ingin mengganti seluruh kriteria sistem dengan rekomendasi ini?")) return;

            try {
                const response = await fetch("{{ route('chat.apply') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({ criteria: data })
                });
                const result = await response.json();
                if(result.success) {
                    alert("✅ Berhasil! " + result.message);
                    window.location.href = "{{ route('dashboard') }}";
                } else {
                    alert("❌ Gagal: " + result.message);
                }
            } catch (e) { alert("Terjadi kesalahan koneksi."); }
        }

        // Helper: Animasi Typing ...
        function appendLoading() {
            const id = 'loading-' + Date.now();
            const div = document.createElement('div');
            div.id = id;
            div.className = "flex justify-start items-end gap-3 msg-enter";
            div.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                <div class="bg-white border border-gray-200 px-4 py-3 rounded-2xl rounded-bl-none shadow-sm flex items-center gap-1">
                    <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
                </div>
            `;
            containerInner.appendChild(div);
            scrollToBottom();
            return id;
        }

        function removeLoading(id) { document.getElementById(id)?.remove(); }
        function scrollToBottom() { chatContainer.scrollTop = chatContainer.scrollHeight; }
        function escapeHtml(text) { return text.replace(/[&<>"']/g, function(m) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]; }); }
        
        // Format Markdown Bold & Newline
        function formatBotResponse(text) {
            let formatted = escapeHtml(text);
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>'); // Bold
            formatted = formatted.replace(/\n/g, '<br>'); // Newline
            return formatted;
        }
    </script>
</x-app-layout>