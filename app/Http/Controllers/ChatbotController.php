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

        // 1. System Prompt - ELITE EXPERT MODE
        $systemPrompt = "ROLE:Elite Chief Human Capital Officer & Senior Data Scientist.
        TONE:Highly Professional, Strategic, Detailed, and Insightful.
        CTX:$infoKriteria
        $knowledgeContext
        RULES:
        1.DEEP_ANALYSIS: Provide comprehensive, multi-layered answers. Don't just answer 'what', answer 'why' and 'how'.
        2.STRATEGIC VALUE: Connect every insight to business impact and long-term organizational goals.
        3.CUSTOMIZATION: Use the specific criteria context to tailor advice perfectly.
        4.PROACTIVE: Suggest related concepts, risks, or opportunities user might have missed.
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
                'max_tokens' => 4096 // MAXIMUM POWER
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
        OUT(Markdown):
        1. **Strategic Executive Summary**: Why this ranking matters for the business.
        2. **Winner's Competitive Advantage**: Detailed breakdown of the top candidate's strengths relative to the specific criteria weights.
        3. **Gap Analysis (Rank 1 vs Rank 2)**: Precise statistical difference explanation. What exactly did #2 miss?
        4. **Sensitivity Note**: How stable is this result? (e.g., if criteria X changed slightly, would the winner change?)
        5. **Actionable Recommendation**: Next steps (Interview focus, negotiation leverage, etc.).
        
        Use bolding, lists, and professional formatting. Be exhaustive.";

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
                'max_tokens'  => 4096,
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
            $text = substr($text, 0, 50000); // ULTIMATE limit: 50k chars (approx 12.5k tokens)

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

        $prompt = "ROLE:Elite Talent Auditor & Psychologist. 
        TASK:Perform a Deep-Dive Forensic Analysis of the Candidate's CV.
        
        METHODOLOGY (CHAIN OF THOUGHT):
        1. First, scan for 'Red Flags' (gaps, inconsistencies, formatting errors).
        2. Second, evaluate 'Hard Skills' against the specific CRITERIA provided.
        3. Third, analyze 'Soft Skills' & 'Psychometrics' based on language patterns, hobbies, and achievements.
        4. Finally, synthesize all data into a strictly structured JSON report.

        RULES:
        1. ACCURACY: Do not hallucinate. If evidence is missing, score LOW (1).
        2. EVIDENCE: You MUST quote the exact text from the CV that justifies every score.
        3. CRITICALITY: Be strict. Do not give high scores easily. High scores require concrete proof of excellence.
        
        $knowledgeContext
        
        =========================================
        CRITERIA REFERENCE:
        $criteriaContext
        =========================================
        
        CANDIDATE CV ({$pelamar->nama}):
        $text
        
        OUTPUT FORMAT (JSON ONLY):
        {
            \"_analysis_chain\": \"Briefly explain your thinking process here before scoring...\",
            \"summary\": \"Executive Summary (Professional & Insightful)\",
            \"recommendation\": \"HIGHLY RECOMMENDED / RECOMMENDED / CONSIDER / NOT RECOMMENDED\",
            \"match_confidence\": \"HIGH / MEDIUM / LOW\",
            \"red_flags\": [\"Flag 1\", \"Flag 2\"],
            \"psychometrics\": {
                \"leadership_potential\": \"High/Med/Low - Reason\",
                \"culture_fit_score\": 1-100,
                \"work_style\": \"Description\",
                \"dominant_traits\": [\"Trait 1\", \"Trait 2\"]
            },
            \"interview_questions\": [\"Behavioral Q1\", \"Technical Q2\", \"Cultural Q3\"],
            \"competency_gap\": [\"Major Gap 1\", \"Minor Gap 2\"],
            \"details\": {
                \"KODE\":{\"score\":1-5,\"reason\":\"Deep reasoning linking experience to criteria.\",\"evidence\":\"Verbatim quote\"}
            }
        }";

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
                'max_tokens' => 4096, // Limit output MAXIMUM
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
                return response()->json(['success' => false, 'message' => 'Gagal koneksi ke AI: ' . $response->body()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}