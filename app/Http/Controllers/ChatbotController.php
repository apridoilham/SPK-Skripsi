<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
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

        // 1. System Prompt - ELITE EXPERT MODE
        $systemPrompt = "ROLE: Elite Chief Human Capital Officer & Senior Data Scientist (Indonesian Speaking).
        TONE: Highly Professional, Strategic, Detailed, and Insightful.
        LANGUAGE: INDONESIAN (BAHASA INDONESIA) ONLY.
        CTX:$infoKriteria
        $knowledgeContext
        RULES:
        1.DEEP_ANALYSIS: Provide comprehensive, multi-layered answers. Don't just answer 'what', answer 'why' and 'how'.
        2.STRATEGIC VALUE: Connect every insight to business impact and long-term organizational goals.
        3.CUSTOMIZATION: Use the specific criteria context to tailor advice perfectly.
        4.PROACTIVE: Suggest related concepts, risks, or opportunities user might have missed.
        5.STRICT_LANGUAGE: All responses MUST be in formal, professional Indonesian.
        JSON_FMT:[{\"kode\":\"C1\",\"nama\":\"..\",\"bobot\":30,\"jenis\":\"benefit\",\"opsi\":[\"..\"]}]";

        // 2. Chat History - EXTENDED CONTEXT
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        if (!empty($history) && is_array($history)) {
            $limitedHistory = array_slice($history, -20); // Keep last 20 messages for deep context
            foreach ($limitedHistory as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
                }
            }
        }
        
        // Add current user message
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
                'max_tokens' => 8192 // MAXIMUM POWER (Upgraded from 4096)
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
        $promptContext = "KRITERIA:\n";
        foreach ($data['kriterias'] as $k) {
            $promptContext .= "- {$k['nama']} ({$k['kode']}): Bobot {$k['bobot']}, Jenis {$k['jenis']}\n";
        }

        // 2. Data Awal (Sampel 10 teratas - Extended Context)
        $promptContext .= "\nSAMPEL DATA AWAL (Top 10):\n";
        foreach (array_slice($data['matriksX'], 0, 10) as $x) {
            $promptContext .= "- {$x['nama']}: " . json_encode(array_diff_key($x, ['nama' => ''])) . "\n";
        }

        // 3. Hasil Akhir (Top 10 - Extended Context)
        $promptContext .= "\nHASIL PERANGKINGAN (TOP 10):\n";
        foreach (array_slice($data['ranking'], 0, 10) as $i => $r) {
            $rank = $i + 1;
            $promptContext .= "{$rank}. {$r['nama']} (Skor: {$r['skor_kalkulasi']})\n";
        }

        $systemPrompt = "ROLE:Lead Data Scientist & HR Strategist. GOAL:Provide Deep-Dive Analytical Explanation of SAW Results.
        LANGUAGE: INDONESIAN (BAHASA INDONESIA) ONLY.
        OUT(Markdown):
        1. **Ringkasan Eksekutif Strategis**: Mengapa peringkat ini penting bagi bisnis.
        2. **Keunggulan Kompetitif Pemenang**: Rincian kekuatan kandidat teratas relatif terhadap bobot kriteria tertentu.
        3. **Analisis Kesenjangan (Peringkat 1 vs Peringkat 2)**: Penjelasan perbedaan statistik yang tepat. Apa sebenarnya yang dilewatkan #2?
        4. **Catatan Sensitivitas**: Seberapa stabil hasil ini? (misalnya, jika kriteria X sedikit berubah, apakah pemenang akan berubah?)
        5. **Rekomendasi Tindakan**: Langkah selanjutnya (Fokus wawancara, leverage negosiasi, dll).
        
        Use bolding, lists, and professional formatting. Be exhaustive. Generate a LONG, DETAILED report in INDONESIAN.";

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
                'max_tokens'  => 8192, // Upgraded from 4096
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
            $text = preg_replace('/\s+/', ' ', $text); // Compress whitespace
            $text = substr($text, 0, 100000); // ULTIMATE limit: 100k chars (approx 25k tokens)

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

        // 3. Persiapkan Context Kriteria (ULTIMATE DETAIL)
        // Format: C1:Name (Options with Labels)
        $criteriaContext = $kriterias->map(function($k) {
            $opsiStr = collect($k->opsi)->map(function($val, $key) {
                // Asumsi val adalah label seperti "Sangat Baik"
                $score = $key + 1;
                return "Score {$score}={$val}"; 
            })->join(", ");
            return "CRITERIA {$k->nama} (Code: {$k->kode}) [Weight: {$k->bobot}%, Type: {$k->jenis}]\n   Scoring Guide: {$opsiStr}";
        })->join("\n\n");

        // LOAD AI KNOWLEDGE BASE
        $knowledge = AiKnowledgeBase::where('is_active', true)->get();
        $knowledgeContext = "";
        if ($knowledge->isNotEmpty()) {
            $knowledgeContext = "ORGANIZATIONAL KNOWLEDGE BASE:\n" . $knowledge->map(fn($k) => "- " . $k->content)->join("\n");
        }

        $prompt = "ROLE: Elite Talent Auditor & Psychologist. 
        TASK: Perform a Deep-Dive Forensic Analysis of the Candidate's CV.
        LANGUAGE: INDONESIAN (BAHASA INDONESIA) ONLY.
        
        METHODOLOGY (CHAIN OF THOUGHT):
        1. TIME CALCULATION (CRITICAL):
           - Look for Start Date and End Date for EACH job.
           - Calculate duration mathematically (e.g., Jan 2024 to Feb 2025 = 1 year 1 month).
           - Sum up TOTAL relevant experience.
           - IF 'Present' or 'Sekarang' is used, use Today's Date (" . date('F Y') . ").
        2. EVIDENCE CHECK:
           - Scan for specific keywords matching the CRITERIA.
           - If a criterion asks for 'Leadership' and CV has no team lead roles, score LOW.
        3. SCORING:
           - Assign scores (1-5) strictly based on the Evidence.
           - Score 5 = Perfect Match (Exceeds expectations).
           - Score 1 = No Evidence Found.

        RULES:
        1. NO FLUFF: Do not use filler words like 'Berdasarkan analisis...' or 'Kandidat ini...'. Go STRAIGHT to the point.
        2. ACCURACY: If experience is < 1 year, DO NOT say 'Experienced'. Say 'Fresh Graduate' or 'Junior'.
        3. REALITY: Use the calculated duration. Do not hallucinate years of experience.
        4. CONSISTENCY: Identical CV content MUST yield identical scores.
        5. LANGUAGE: All output MUST be in clear, professional Indonesian.
        
        $knowledgeContext
        
        =========================================
        CRITERIA REFERENCE:
        $criteriaContext
        =========================================
        
        CANDIDATE CV ({$pelamar->nama}):
        $text
        
        OUTPUT FORMAT (JSON ONLY):
        {
            \"_analysis_chain\": \"[Internal Thought] Durasi: Jan 2020-Jan 2022 (2th) + Mar 2022-Skrg (..). Total = X tahun.\",
            \"summary\": \"[Ringkasan Padat] Arif adalah Junior Web Developer dengan total pengalaman 1.5 tahun di industri...\",
            \"recommendation\": \"SANGAT DIREKOMENDASIKAN / DIREKOMENDASIKAN / DIPERTIMBANGKAN / TIDAK DIREKOMENDASIKAN\",
            \"match_confidence\": \"TINGGI / SEDANG / RENDAH\",
            \"red_flags\": [\"Durasi kerja pendek (<1 thn) di PT X\", \"Gap year 2021-2022\"],
            \"psychometrics\": {
                \"leadership_potential\": \"Tinggi/Sedang/Rendah - Alasan singkat\",
                \"culture_fit_score\": 1-100,
                \"work_style\": \"Analitis, Terstruktur, Berorientasi Hasil\",
                \"dominant_traits\": [\"Teliti\", \"Ambisius\"]
            },
            \"interview_questions\": [\"Jelaskan gap karier Anda di tahun 2021?\", \"Ceritakan proyek tersulit di PT X?\"],
            \"competency_gap\": [\"Belum ada pengalaman di Cloud\", \"Bahasa Inggris Pasif\"],
            \"details\": {
                \"KODE\":{\"score\":1-5,\"reason\":\"[Poin Utama] Memiliki pengalaman 2 tahun (Sesuai Kriteria >1 thn).\",\"evidence\":\"'Web Dev at PT X (2020-2022)'\"}
            }
        }";

        // 4. Kirim ke Groq
        $apiKey = env('GROQ_API_KEY');
        try {
            /** @var Response $response */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey, 
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // Ganti ke Llama 3.3 (Model Aktif)
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a JSON generator. Always return valid JSON. Be deterministic.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.0, // NOL MUTLAK untuk konsistensi maksimum (DETERMINISTIC)
                'max_tokens' => 8192, // Limit output MAXIMUM (Upgraded from 4096)
                // 'seed' => 42, // Seed dinonaktifkan sementara karena isu kompatibilitas
                'response_format' => ['type' => 'json_object'] // Paksa mode JSON
            ]);

            if ($response->successful()) {
                $result = $response->json()['choices'][0]['message']['content'];
                return response()->json([
                    'success' => true,
                    'data' => json_decode($result)
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal koneksi ke AI: ' . $response->body()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}