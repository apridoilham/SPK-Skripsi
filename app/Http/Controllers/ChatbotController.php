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

        // 4. System Prompt (ULTRA COMPRESSED MODE)
        // Telegraphic style: Removes grammar fluff, keeps semantic meaning.
        $systemPrompt = "ROLE:CHCO/Consultant. TONE:Pro-Human.
        CTX:$infoKriteria
        $knowledgeContext
        RULES:
        1.DEEP_CUSTOM:Role-specific criteria(Dr->STR,IT->Code). No generic.
        2.ADAPT:Forget old ctx if topic changes.
        3.ACT:If user needs NEW criteria->ID skills->Create 4-5 specific->JSON end.
        JSON_FMT:[{\"kode\":\"C1\",\"nama\":\"..\",\"bobot\":30,\"jenis\":\"benefit\",\"opsi\":[\"..\"]}]";

        // Construct Messages Array with History
        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        
        // Append history (Ultra-Optimized: Limit last 4 messages & short truncation)
        if (!empty($history) && is_array($history)) {
            $limitedHistory = array_slice($history, -4); // Cuma ingat 4 chat terakhir
            foreach ($limitedHistory as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    // Truncate extreme to 150 chars. Hemat token gila-gilaan.
                    $content = strlen($msg['content']) > 150 ? substr($msg['content'], 0, 150) . '..' : $msg['content'];
                    $messages[] = ['role' => $msg['role'], 'content' => $content];
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

        $systemPrompt = "ROLE:HR. GOAL:Explain SAW simply.
        OUT(Markdown):
        1.Why this result?
        2.Winner strength?
        3.Rank1 vs 2 diff?
        4.Advice?";

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
            $text = preg_replace('/\s+/', ' ', $text); // Compress whitespace
            $text = substr($text, 0, 5000); // Extreme limit (was 10k)

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
                return ($key+1) . "=$val"; // Ultra short format: 1=Buruk
            })->join(",");
            
            return "{$k->nama}({$k->kode}):$opsiStr";
        })->join("|");

        // LOAD AI KNOWLEDGE BASE
        $knowledge = AiKnowledgeBase::where('is_active', true)->get();
        $knowledgeContext = "";
        if ($knowledge->isNotEmpty()) {
            $knowledgeContext = "RULES:" . $knowledge->pluck('content')->join("|");
        }

        $prompt = "ROLE:Auditor. TASK:Score CV.
        RULES:
        1.CONSISTENT:Input=Output same.
        2.EVIDENCE:No text=Score 1. Quote evidence.
        3.CALC:Duration=End-Start. Round down.
        4.MATCH:Literal scale match.
        
        $knowledgeContext
        CRITERIA:$criteriaContext
        
        CV({$pelamar->nama}):
        $text
        
        OUT(JSON):
        {
            \"summary\": \"Profile & Exp Duration(Calc)\",
            \"recommendation\": \"HIGH/CONSIDER/LOW\",
            \"match_confidence\": \"HIGH/MED/LOW\",
            \"red_flags\": [\"..\"],
            \"psychometrics\": {\"leadership_potential\":\"..\",\"culture_fit_score\":1-100,\"work_style\":\"..\",\"dominant_traits\":[\"..\"]},
            \"interview_questions\": [\"..\"],
            \"competency_gap\": [\"..\"],
            \"details\": {
                \"KODE\":{\"score\":1-5,\"reason\":\"..\",\"evidence\":\"Quote\"}
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