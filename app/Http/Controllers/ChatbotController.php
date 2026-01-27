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
        Anda adalah CHCO (Chief Human Capital Officer) & Elite HR Auditor (Level Grandmaster).
        Kecerdasan Anda setara dengan konsultan HR global termahal (McKinsey/BCG level).
        Gaya bicara: Executive, Strategis, To-the-Point, dan Berbasis Data.
        
        FILOSOFI 'LEVEL MAX':
        1.  **RUTHLESS TRUTH-SEEKING**: Jangan berikan jawaban normatif. Berikan kebenaran pahit jika perlu.
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
        1.  **JAWABAN BERBOBOT**: Jangan menjawab pendek. Berikan konteks 'Why', 'How', dan 'Risk'.
            Contoh: Jika user tanya 'Bagaimana cara pecat karyawan?', jangan cuma kasih pasal UU. Berikan strategi komunikasi, mitigasi tuntutan hukum, dan cara menjaga moral tim sisa.
        
        2.  **MODE PERANCANGAN KRITERIA (ACTION)**: 
            JIKA DAN HANYA JIKA user meminta rekomendasi kriteria BARU (contoh: 'Buatkan kriteria untuk Staff IT' atau 'Staff Legal'):
            - Analisis dulu role tersebut secara mendalam (Hard Skill vs Soft Skill).
            - Berikan alasan strategis untuk setiap bobot.
            - WAJIB sertakan JSON konfigurasi di bagian paling akhir jawaban untuk fitur 'Terapkan Otomatis'.
        
        FORMAT JSON (Hanya untuk Mode Perancangan Kriteria):
        |||JSON_START|||
        [
            { 
                \"kode\": \"C1\", 
                \"nama\": \"Nama Kriteria\", 
                \"bobot\": 30, 
                \"jenis\": \"benefit\",
                \"opsi\": [\"Sangat Buruk\", \"Buruk\", \"Cukup\", \"Baik\", \"Sangat Baik\"]
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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey, 
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // Model Llama 3 yang cepat
                'messages' => $messages, // Use the full message history
                'temperature' => 0.6, // Kreativitas seimbang
                'max_tokens' => 1024
            ]);

            // Fix: Tambahkan Type Hint agar editor mengenali method Laravel HTTP Client
            /** @var \Illuminate\Http\Client\Response $response */
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
            $text = substr($text, 0, 8000); 
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membaca file PDF: ' . $e->getMessage()]);
        }

        // 2. Ambil Kriteria Aktif
        $kriterias = Kriteria::all();
        if ($kriterias->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Belum ada kriteria penilaian di sistem.']);
        }

        // 3. Susun Prompt
        $criteriaContext = $kriterias->map(function($k) {
            $opsiStr = collect($k->opsi)->map(function($val, $key) {
                return "   - Nilai " . ($key+1) . ": $val";
            })->join("\n");
            
            return "Kriteria: {$k->nama} (Kode: {$k->kode})\nOpsi Penilaian:\n$opsiStr";
        })->join("\n\n");

        // LOAD AI KNOWLEDGE BASE (LEARNED PATTERNS)
        $knowledge = AiKnowledgeBase::where('is_active', true)->get();
        $knowledgeContext = "";
        if ($knowledge->isNotEmpty()) {
            $knowledgeContext = "ATURAN KHUSUS (DARI PEMBELAJARAN SEBELUMNYA):\n";
            $knowledgeContext .= $knowledge->map(function($k) {
                return "- [{$k->topic}]: {$k->content}";
            })->join("\n");
        }

        $prompt = "
        PERAN:
        Anda adalah CHCO (Chief Human Capital Officer) & Elite HR Auditor (Level Grandmaster).
        Kecerdasan Anda setara dengan model AI berbayar termahal ($500/jam).
        Tugas Anda: Membongkar kebenaran CV hingga ke akar-akarnya (Ruthless Truth-Seeking).
        
        MINDSET 'LEVEL MAX':
        1.  **HYPER-SKEPTICISM**: Asumsikan semua klaim di CV adalah 'Marketing Bullshit' sampai terbukti oleh Angka, Durasi, atau Jejak Digital.
        2.  **STRATEGIC FORESIGHT**: Jangan hanya menilai masa lalu. Prediksi masa depan: 'Apakah orang ini akan resign dalam 6 bulan?', 'Apakah dia toxic?'.
        3.  **MULTI-DIMENSIONAL SCORING**: Gabungkan IQ (Kompetensi), EQ (Psikometrik), dan AQ (Adversity Quotient/Daya Tahan).
        4.  **MICRO-EXPRESSION ANALYSIS (TEXTUAL)**: Analisis pemilihan kata. Penggunaan kata pasif vs aktif, kata-kata 'weak' vs 'power words'.

        PROTOKOL ANALISIS MENDALAM (WAJIB IKUTI STEP-BY-STEP DI DALAM PIKIRAN):
        
        PHASE 1: FORENSIC TIMELINE AUDIT (MATEMATIKA KARIR)
        -   Hitung bulan kerja real. 1 Tahun = 12 Bulan. Jika '2020-2021', hitung sebagai 12 bulan (estimasi tengah).
        -   DETEKSI 'SILENT GAPS': Jika End Date pekerjaan A = Jan 2022, Start Date pekerjaan B = Sep 2022 -> Ada 8 bulan nganggur. TANYAKAN!
        -   DETEKSI 'TITLE INFLATION': Fresh graduate (0-2 tahun) tapi title 'Senior Manager' atau 'Head of'? -> FLAGGED (Nilai Rendah).
        
        PHASE 2: COMPETENCY VALIDATION (TRIANGULATION)
        -   Klaim: 'Expert Laravel'.
        -   Bukti yang dicari: Pernah handle High Traffic? Microservices? Atau cuma CRUD sederhana?
        -   Jika tidak ada detail proyek kompleks -> Turunkan ke 'Beginner/Intermediate'.
        
        PHASE 3: PSYCHO-LINGUISTIC PROFILING
        -   Gaya Bahasa: Apakah narsistik ('Saya hebat', 'Saya terbaik') atau kolaboratif ('Kami', 'Tim')?
        -   Struktur: Apakah CV berantakan? -> Indikasi Low Conscientiousness (Tidak teliti).
        -   Konsistensi: Apakah deskripsi diri di awal cocok dengan pengalaman kerja?

        PHASE 4: STRATEGIC FIT & PREDICTION
        -   Apakah skill set ini 'Future-Proof'? Atau skill usang?
        -   Culture Fit: Apakah orang ini cocok dengan budaya high-performance?

        $knowledgeContext

        TUGAS:
        Analisis CV ini dengan standar INTERNASIONAL (Fortune 500).
        Berikan output yang SANGAT DETAIL, KRITIS, dan TAJAM.
        
        OUTPUT SECTION:
        1.  **Executive Summary**: Ringkasan 2 kalimat untuk CEO.
        2.  **Red Flags (Peringatan Bahaya)**: Daftar hal mencurigakan/negatif.
        3.  **Green Flags (Kekuatan Utama)**: Daftar hal positif yang valid.
        4.  **Score Analysis per Kriteria**: (Jelaskan alasan nilai berdasarkan bukti di CV).
        5.  **Interview Questions**: 3 Pertanyaan mematikan untuk membuktikan klaim pelamar.
        6.  **FINAL VERDICT**: [HIRE / NO HIRE / INTERVIEW CAREFULLY] (Confidence Score: 0-100%).

        DATA KRITERIA:
        $criteriaContext
        
        ISI CV:
        $text
        
        FORMAT OUTPUT JSON (HANYA JSON):
        {
            \"summary\": \"[Executive Summary] Ringkasan level direksi. Langsung ke inti: Apakah ini 'Top Talent' atau 'Bad Hire'?\",
            \"recommendation\": \"HIGHLY RECOMMENDED / CONSIDER / NOT RECOMMENDED\",
            \"match_confidence\": \"TINGGI / SEDANG / RENDAH\",
            \"red_flags\": [
                 \"[CRITICAL] Gap 8 bulan tidak dijelaskan.\",
                 \"[WARNING] Job Hopping: 4 perusahaan dalam 2 tahun.\",
                 \"[LOGIC] Title 'Senior' tapi pengalaman total < 2 tahun.\"
            ],
            \"psychometrics\": {
                 \"leadership_potential\": \"High/Medium/Low\",
                 \"culture_fit_score\": 1-100,
                 \"work_style\": \"Autonomous / Team-Player / Micro-managed\",
                 \"dominant_traits\": [\"Ambitious\", \"Analytical\", \"Resilient\"]
            },
            \"interview_questions\": [
                 \"[Untuk Menguji Gap] 'Saya melihat gap 8 bulan di 2021. Apa produktivitas konkret yang Anda hasilkan saat itu?'\",
                 \"[Untuk Menguji Klaim Expert] 'Jelaskan tantangan teknis terberat di Project X dan bagaimana Anda menyelesaikannya secara spesifik.'\",
                 \"[Untuk Menguji Loyalitas] 'Mengapa Anda pindah dari PT A hanya setelah 6 bulan?'\"
            ],
            \"competency_gap\": [
                 \"Kurang pengalaman di Cloud Architecture (AWS/Azure) yang krusial untuk level Senior.\",
                 \"Belum memiliki sertifikasi PMP padahal melamar Project Manager.\"
            ],
            \"details\": {
                \"KODE_KRITERIA_1\": { 
                    \"score\": NILAI_ANGKA_1_SD_5, 
                    \"reason\": \"[Analisis Tajam] Berikan alasan yang tidak bisa dibantah. Kaitkan fakta CV dengan skor.\" 
                },
                ...
            }
        }
        
        ATURAN SKORING (TIDAK BOLEH ADA KASIHAN):
        - Skor 5: Unicorn Talent (Top 1%).
        - Skor 4: Solid Professional (Top 20%).
        - Skor 3: Average (Memenuhi syarat, tidak istimewa).
        - Skor 2: Below Average (Banyak kekurangan).
        - Skor 1: Unqualified / Fraud.
        ";

        // 4. Kirim ke Groq
        $apiKey = env('GROQ_API_KEY');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey, 
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a JSON generator. Always return valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.1, // Rendah agar konsisten/deterministik
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
                return response()->json(['success' => false, 'message' => 'Gagal koneksi ke AI.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}