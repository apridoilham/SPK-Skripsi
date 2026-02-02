<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Kriteria;
use App\Models\Pelamar;
use App\Models\AiKnowledgeBase;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ChatbotController extends Controller
{
    // Method index() dihapus karena UI chatbot sudah pindah ke floating button di Dashboard HRD

    /**
     * Menyimpan aturan baru ke Knowledge Base AI (Training Loop)
     */
    public function teachAi(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'content' => 'required|string',
        ]);

        try {
            AiKnowledgeBase::create([
                'topic' => $request->topic,
                'content' => $request->content,
                'author' => Auth::user()->name ?? 'HRD',
                'is_active' => true
            ]);

            return response()->json(['success' => true, 'message' => 'AI berhasil mempelajari aturan baru!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal melatih AI: ' . $e->getMessage()]);
        }
    }

    /**
     * Menangani pesan chat dari user ke AI
     */
    public function sendMessage(Request $request)
    {
        // 1. Validasi Input
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->input('message');
        $history = $request->input('history', []); // Terima history dari frontend
        
        // 2. Cek API Key
        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['reply' => 'Error: API Key belum dikonfigurasi di file .env.'], 500);
        }

        // 3. INTELLIGENCE UPGRADE: Suntikkan Data Database ke Otak AI
        // AI akan membaca kriteria yang sedang aktif agar jawabannya relevan
        try {
            $dbKriteria = Kriteria::all();
            $infoKriteria = $dbKriteria->isEmpty() 
                ? "Saat ini belum ada kriteria yang diatur di dalam sistem." 
                : $dbKriteria->map(function($k) {
                    $bobotPersen = $k->bobot * 100;
                    return "- {$k->nama} (Bobot: {$bobotPersen}%, Sifat: {$k->jenis})";
                })->join("\n");
        } catch (\Exception $e) {
            $infoKriteria = "Gagal membaca database kriteria.";
        }

        // LOAD AI KNOWLEDGE BASE (LEARNED PATTERNS) - Agar Chatbot juga pintar seperti Scanner CV
        $knowledge = AiKnowledgeBase::where('is_active', true)->get();
        $knowledgeContext = "";
        if ($knowledge->isNotEmpty()) {
            $knowledgeContext = "ATURAN KHUSUS PERUSAHAAN (LEARNED MEMORY):\n";
            $knowledgeContext .= $knowledge->map(function($k) {
                return "- [{$k->topic}]: {$k->content}";
            })->join("\n");
        }

        // 4. System Prompt (Instruksi Utama untuk AI - LEVEL MAX/GRANDMASTER)
        $systemPrompt = "
        PERAN: 
        Anda adalah CHCO (Chief Human Capital Officer) & Senior Recruitment Consultant.
        Anda bukan robot kaku, melainkan partner diskusi yang cerdas, luwes, dan sangat paham industri spesifik.
        Gaya bicara: Profesional tapi Humanis (seperti berbicara dengan rekan kerja senior yang asik). Gunakan bahasa Indonesia yang mengalir, tidak baku/kaku, tapi tetap sopan.

        FILOSOFI 'DEEP CUSTOMIZATION':
        1.  **HINDARI TEMPLATE GENERIK**:
            -   Jika user minta kriteria untuk 'DOKTER', JANGAN GUNAKAN kriteria umum (Komunikasi, Kerjasama).
            -   GUNAKAN KRITERIA SPESIFIK: 'STR Aktif', 'Pengalaman Klinis', 'Penanganan Gawat Darurat'.
            -   Jika user minta 'PROGRAMMER', gunakan: 'Algoritma', 'Clean Code', 'Tech Stack Match'.
            -   Jika user minta 'AKUNTAN', gunakan: 'Sertifikasi Brevet', 'Ketelitian', 'Penguasaan PSAK'.
            
        2.  **STRATEGIC FORESIGHT**: Selalu pikirkan dampak jangka panjang (3-5 tahun ke depan) dari setiap keputusan HR.
        3.  **RISK AVERSION**: Selalu peringatkan user tentang potensi risiko hukum, budaya, atau finansial.
        4.  **CONTEXT AWARENESS & ADAPTABILITY**: 
            - Anda HARUS mengingat apa yang dibicarakan sebelumnya. 
            - **CRITICAL**: Jika user mengubah topik (misal: dari 'Hukum' ke 'IT', atau sebaliknya), SEGERA BERADAPTASI. Jangan terjebak pada konteks sebelumnya atau data kriteria lama.
            - Jika user meminta rekomendasi untuk role tertentu (misal: 'Legal Staff'), ABAIKAN kriteria 'Staff IT' yang mungkin ada di database saat ini. Buat rekomendasi BARU yang relevan.

        KEMAMPUAN KHUSUS:
        1.  **Audit Forensik**: Mampu mendeteksi kebohongan atau ketidakkonsistenan dalam pertanyaan user.
        2.  **Desain Organisasi**: Mampu merancang struktur tim yang efisien.
        3.  **Resolusi Konflik**: Memberikan solusi psikologis untuk masalah tim.

        DATA KONTEKS SISTEM SAAT INI (REAL-TIME):
        Berikut adalah kriteria penilaian yang sedang aktif digunakan di database perusahaan saat ini:
        $infoKriteria
        
        (CATATAN PENTING: Data di atas hanya referensi. Jika user membahas role yang BERBEDA dengan kriteria di atas, ABAIKAN data di atas dan gunakan pengetahuan umum Anda).

        $knowledgeContext
        
        ATURAN MERESPONS:
        1.  **HUMAN TOUCH**: Mulailah dengan sapaan atau tanggapan yang natural. Jangan langsung menyodorkan data. Contoh: 'Wah, posisi Dokter Umum ya? Posisi ini krusial banget di akurasi diagnosis...'
        2.  **JAWABAN BERBOBOT**: Jangan menjawab pendek. Berikan konteks 'Why', 'How', dan 'Risk'.
        
        3.  **MODE PERANCANGAN KRITERIA (ACTION)**: 
            JIKA DAN HANYA JIKA user meminta rekomendasi kriteria BARU (contoh: 'Buatkan kriteria untuk Staff IT' atau 'Staff Legal'):
            -   **STEP 1**: Identifikasi Hard Skill & Soft Skill unik untuk role tersebut.
            -   **STEP 2**: Buat 4-5 Kriteria yang SANGAT SPESIFIK (Jangan gunakan nama generik seperti 'Wawancara' atau 'Tes Tulis').
                *   Salah: 'Tes Kemampuan'
                *   Benar: 'Live Coding Test' (untuk IT) atau 'Studi Kasus Medis' (untuk Dokter).
            -   **STEP 3**: Berikan alasan strategis untuk setiap bobot.
            -   **STEP 4**: WAJIB sertakan JSON konfigurasi di bagian paling akhir jawaban.
        
        FORMAT JSON (Hanya untuk Mode Perancangan Kriteria):
        |||JSON_START|||
        [
            { 
                \"kode\": \"C1\", 
                \"nama\": \"Nama Kriteria Spesifik (Misal: Penguasaan PHP)\", 
                \"bobot\": 30, 
                \"jenis\": \"benefit\",
                \"opsi\": [\"Pemula\", \"Junior\", \"Middle\", \"Senior\", \"Expert\"]
            },
            ... (Pastikan total bobot 100)
        ]
        |||JSON_END|||
        ";

        // Construct Messages Array with History
        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        
        // Append history (limit to last 10 messages for context window efficiency)
        if (!empty($history) && is_array($history)) {
            foreach ($history as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
                }
            }
        }
        
        // Append current user message
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            // 5. Request ke Groq API
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey, 
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // Kembali ke Llama 3.3 (Model Aktif)
                'messages' => $messages, // Use the full message history
                'temperature' => 0.7, // Sedikit lebih kreatif untuk variasi
                'max_tokens' => 1024
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $replyContent = $responseData['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respon teks dari AI.';

                return response()->json([
                    'reply' => $replyContent
                ]);
            } else {
                return response()->json([
                    'reply' => "Maaf, AI sedang sibuk (Error dari Provider API)."
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Koneksi Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menerapkan rekomendasi JSON dari AI langsung ke Database
     */
    /**
     * Menjelaskan detail perhitungan SAW
     */
    public function explainCalculation(Request $request)
    {
        $request->validate([
            'data' => 'required|array'
        ]);

        $data = $request->input('data');
        $apiKey = env('GROQ_API_KEY');

        if (empty($apiKey)) {
            return response()->json(['reply' => 'Error: API Key belum dikonfigurasi.'], 500);
        }

        // Build prompt from data
        $promptContext = "Berikut adalah data perhitungan SPK metode SAW:\n\n";
        
        // 1. Kriteria
        $promptContext .= "KRITERIA:\n";
        foreach ($data['kriterias'] as $k) {
            $promptContext .= "- {$k['nama']} ({$k['kode']}): Bobot {$k['bobot']}, Jenis {$k['jenis']}\n";
        }

        // 2. Data Awal (Sampel 3 teratas)
        $promptContext .= "\nSAMPEL DATA AWAL:\n";
        foreach (array_slice($data['matriksX'], 0, 3) as $x) {
            $promptContext .= "- {$x['nama']}: " . json_encode(array_diff_key($x, ['nama' => ''])) . "\n";
        }

        // 3. Hasil Akhir (Top 3)
        $promptContext .= "\nHASIL PERANGKINGAN (TOP 3):\n";
        foreach (array_slice($data['ranking'], 0, 3) as $i => $r) {
            $rank = $i + 1;
            $promptContext .= "{$rank}. {$r['nama']} (Skor: {$r['skor_kalkulasi']})\n";
        }

        $systemPrompt = "
        PERAN:
        Anda adalah Asisten HRD yang ramah dan cerdas.
        Tugas Anda adalah menjelaskan hasil perhitungan metode SAW dengan bahasa Indonesia yang santai, mudah dipahami, dan tidak kaku.
        Hindari istilah matematika yang terlalu rumit. Fokus pada cerita di balik angka.

        TUGAS:
        1. Jelaskan secara sederhana kenapa metode SAW memberikan hasil seperti ini.
        2. Ceritakan kenapa kandidat Peringkat 1 bisa menang (apa kelebihan utamanya?).
        3. Bandingkan Peringkat 1 dengan Peringkat 2 (apa bedanya?).
        4. Berikan saran praktis untuk HRD.

        Gunakan format Markdown yang rapi.
        ";

        try {
            $client = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ]);

            /** @var \Illuminate\Http\Client\Response $response */
            $response = $client->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'    => 'llama-3.3-70b-versatile', // Ganti ke Llama 3.3 (Model Aktif)
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $promptContext]
                ],
                'temperature' => 0.7,
                'max_tokens'  => 1000,
            ]);

            $responseData = $response->json();

            // DEBUG: Log response jika gagal
            if (!isset($responseData['choices'][0]['message']['content'])) {
                \Illuminate\Support\Facades\Log::error('Groq API Error', ['response' => $responseData]);
                return response()->json([
                    'reply' => 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi nanti.'
                ], 500);
            }

            return response()->json([
                'reply' => $responseData['choices'][0]['message']['content']
            ]);
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function applyCriteria(Request $request)
    {
        $data = $request->input('criteria');
        
        // Validasi Total Bobot (Toleransi 0.1 karena floating point)
        $total = array_sum(array_column($data, 'bobot'));
        if(abs($total - 100) > 0.1) {
            return response()->json(['success' => false, 'message' => 'Total bobot tidak 100%.']);
        }

        try {
            // Hapus kriteria lama
            Kriteria::truncate(); 
            
            // Masukkan kriteria baru hasil rekomendasi AI
            foreach ($data as $item) {
                Kriteria::create([
                    'kode' => $item['kode'],
                    'nama' => $item['nama'],
                    'bobot' => $item['bobot'] / 100, // Konversi ke desimal (30 -> 0.3)
                    'jenis' => $item['jenis'] ?? 'benefit',
                    'opsi' => $item['opsi'] ?? ['1','2','3','4','5']
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Sistem berhasil di-update dengan kriteria baru!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal update database: ' . $e->getMessage()]);
        }
    }

    /**
     * Menganalisis CV Pelamar dan memberikan rekomendasi nilai berdasarkan Kriteria
     */
    public function analyzeCv(Request $request)
    {
        $request->validate([
            'pelamar_id' => 'required|exists:pelamars,id'
        ]);

        $pelamar = Pelamar::find($request->pelamar_id);
        
        // Cek file CV
        if (!$pelamar->file_berkas || !Storage::disk('public')->exists($pelamar->file_berkas)) {
            return response()->json(['success' => false, 'message' => 'File CV tidak ditemukan.']);
        }

        // 1. Ekstrak Teks dari PDF
        try {
            $parser = new Parser();
            $pdfPath = Storage::disk('public')->path($pelamar->file_berkas);
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();
            
            // Limit text untuk menghindari token limit
            $text = substr($text, 0, 10000); 

            // Validasi: Jika teks terlalu pendek (berarti PDF mungkin hasil scan gambar)
            if (strlen(trim($text)) < 50) {
                return response()->json(['success' => false, 'message' => 'Teks PDF tidak terbaca. Pastikan file adalah PDF berbasis teks, bukan hasil scan gambar.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membaca file PDF: ' . $e->getMessage()]);
        }

        // 2. Ambil Kriteria Aktif
        $kriterias = Kriteria::all();
        if ($kriterias->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Belum ada kriteria penilaian di sistem.']);
        }

        // 3. Susun Prompt dengan Detail Kriteria yang Lebih Spesifik
        $criteriaContext = $kriterias->map(function($k) {
            $opsiStr = collect($k->opsi)->map(function($val, $key) {
                return "   - Skala " . ($key+1) . ": $val";
            })->join("\n");
            
            return "KRITERIA: {$k->nama} (Kode: {$k->kode})\nDESKRIPSI SKALA PENILAIAN:\n$opsiStr";
        })->join("\n\n");

        // LOAD AI KNOWLEDGE BASE
        $knowledge = AiKnowledgeBase::where('is_active', true)->get();
        $knowledgeContext = "";
        if ($knowledge->isNotEmpty()) {
            $knowledgeContext = "ATURAN TAMBAHAN PERUSAHAAN:\n";
            $knowledgeContext .= $knowledge->map(function($k) {
                return "- [{$k->topic}]: {$k->content}";
            })->join("\n");
        }

        $prompt = "
        PERAN:
        Anda adalah Auditor Senior HRD (Manusia, bukan Robot).
        Tugas: Membaca CV pelamar dengan teliti seolah-olah Anda akan merekrut mereka untuk perusahaan Anda sendiri.
        
        INSTRUKSI UTAMA (STRICT & EVIDENCE-BASED):
        0.  **DETERMINISTIC MODE (WAJIB KONSISTEN)**:
            -   Untuk input CV yang sama, hasil analisis (Skor, Ringkasan, dan Rekomendasi) HARUS SELALU SAMA PERSIS 100%.
            -   Jangan mengubah penilaian hanya karena mencoba variasi. Gunakan logika baku.

        1.  **PEMBUKTIAN WAJIB (NO HALLUCINATION)**:
            - Setiap skor yang Anda berikan HARUS ada buktinya di teks CV.
            - JIKA BUKTI TIDAK DITEMUKAN DI TEKS, BERIKAN SKOR 1 (SATU). JANGAN MENGARANG/ASUMSI.
            - Kutip kalimat asli dari CV untuk kolom 'evidence'.
        
        2.  **HITUNGAN MATEMATIKA NYATA**:
            - Pengalaman Kerja: Hitung manual (Tahun Selesai - Tahun Mulai) untuk setiap posisi.
            - Jangan terkecoh dengan 'Senior' jika pengalaman total < 3 tahun.
            - Hati-hati dengan overlap tanggal.
        
        3.  **PENILAIAN KRITERIA (SANGAT KETAT & LOGIKA MATEMATIKA)**:
            -   **PENGALAMAN KERJA**:
                *   Cari semua Riwayat Pekerjaan. Catat Tanggal Mulai & Selesai (Bulan/Tahun).
                *   Jika hanya ada Tahun (misal: 2020-2021), asumsikan durasi MINIMAL (Jan 2020 - Jan 2021 = 1 tahun, BUKAN 2 tahun).
                *   Total Durasi = Jumlahkan semua pengalaman (dikurangi overlap).
                *   **JANGAN MEMBULATKAN KE ATAS**. 1 tahun 9 bulan itu BELUM 2 tahun.
                *   Jika total pengalaman < 1 tahun, berikan skor terendah.
            -   **PENDIDIKAN**:
                *   Cek gelar tertinggi yang SUDAH LULUS (ada ijazah/tahun lulus).
                *   Mahasiswa aktif (belum lulus) dihitung sebagai ijazah terakhir (SMA/SMK).
            -   Baca 'DESKRIPSI SKALA PENILAIAN' di bawah untuk setiap kriteria.
            -   Cocokkan data pelamar dengan skala tersebut secara literal.
            -   Contoh: Jika kriteria bilang 'Nilai 5 = Min. 5 tahun', dan pelamar cuma 4.8 tahun, MAKA BERIKAN NILAI 4. JANGAN KASIH 5.

        4.  **BAHASA SEDERHANA (LAYMAN TERMS)**:
            - Gunakan Bahasa Indonesia yang lugas, tidak kaku, dan mudah dimengerti orang awam.
            - Hindari istilah teknis HR yang rumit.

        $knowledgeContext

        DATA KRITERIA DAN SKALA PENILAIAN (WAJIB DIIKUTI):
        $criteriaContext
        
        IDENTITAS PELAMAR:
        Nama: {$pelamar->nama}

        ISI TEXT CV PELAMAR:
        $text
        
        FORMAT OUTPUT JSON (HANYA JSON):
        {
            \"summary\": \"[Ringkasan] Jelaskan profil kandidat. WAJIB SEBUTKAN: 'Total Pengalaman Kerja: X Tahun Y Bulan' (Hasil hitungan manual).\",
            \"recommendation\": \"HIGHLY RECOMMENDED / CONSIDER / NOT RECOMMENDED\",
            \"match_confidence\": \"TINGGI / SEDANG / RENDAH\",
            \"red_flags\": [
                 \"Total pengalaman kerja hanya 1 tahun (Kurang dari syarat 2 tahun).\",
                 \"Ada jeda menganggur (gap) selama 8 bulan di tahun 2023.\",
                 \"Tidak melampirkan portofolio yang diminta.\"
            ],
            \"psychometrics\": {
                 \"leadership_potential\": \"Tinggi/Sedang/Rendah\",
                 \"culture_fit_score\": 1-100,
                 \"work_style\": \"Mandiri / Butuh Arahan / Team-Player\",
                 \"dominant_traits\": [\"Teliti\", \"Ambisius\", \"Kreatif\"]
            },
            \"interview_questions\": [
                 \"Coba ceritakan apa saja yang Anda lakukan saat menganggur di tahun 2023?\",
                 \"Di CV tertulis Anda menguasai X, bisa berikan contoh proyek nyata yang menggunakan X?\"
            ],
            \"competency_gap\": [
                 \"Belum menguasai skill X.\",
                 \"Bahasa Inggris masih level pasif.\"
            ],
            \"details\": {
                \"KODE_KRITERIA_SESUAI_DATABASE\": { 
                    \"score\": ANGKA_1_SD_5, 
                    \"reason\": \"[Analisis] Tuliskan cara perhitungan di sini. Contoh: '2019-2020 (1th) + 2022 (6bln) = 1.5 tahun'.\",
                    \"evidence\": \"[Bukti] Kutipan langsung dari CV: 'Bekerja sebagai Manager (2018-2022)'\"
                },
                ... (Ulangi untuk SEMUA kriteria yang ada di DATA KRITERIA)
            }
        }
        ";

        // 4. Kirim ke Groq
        $apiKey = env('GROQ_API_KEY');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey, 
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // Ganti ke Llama 3.3 (Model Aktif)
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a JSON generator. Always return valid JSON. Be deterministic.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.1, // Rendah untuk konsistensi, tapi tidak nol mutlak
                // 'seed' => 42, // Seed dinonaktifkan sementara karena isu kompatibilitas
                'response_format' => ['type' => 'json_object'] // Paksa mode JSON
            ]);

            /** @var \Illuminate\Http\Client\Response $response */
            if ($response->successful()) {
                $result = $response->json()['choices'][0]['message']['content'];
                return response()->json([
                    'success' => true,
                    'data' => json_decode($result)
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal koneksi ke AI: ' . $response->body()]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}