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

        // 1. System Prompt - ULTRA COMPRESSED
        // Hapus knowledge base dan criteria detail untuk hemat token
        $systemPrompt = "ROLE:HR Helper. Brief.";

        // 2. Chat History - EXTREME LIMIT
        // Cuma ambil 1 chat terakhir (Stateless)
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        if (!empty($history) && is_array($history)) {
            $lastMsg = end($history);
            if ($lastMsg && isset($lastMsg['content'])) {
                // Truncate extreme to 100 chars.
                $content = substr($lastMsg['content'], 0, 100);
                $messages[] = ['role' => 'user', 'content' => $content];
            }
        }
        
        // Add current user message
        $messages[] = ['role' => 'user', 'content' => substr($userMessage, 0, 100)];

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
                'max_tokens' => 150 // Hemat token ekstrem
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
        
        // 1. Kriteria (Names only)
        $promptContext = "KRIT:" . collect($data['kriterias'])->pluck('nama')->join(',') . "\n";

        // 2. Hasil Akhir (Top 2 only for comparison)
        $promptContext .= "TOP 2:\n";
        foreach (array_slice($data['ranking'], 0, 2) as $i => $r) {
            $rank = $i + 1;
            $promptContext .= "{$rank}. {$r['nama']} ({$r['skor_kalkulasi']})\n";
        }

        $systemPrompt = "Explain why #1 won vs #2. Brief.";
        
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
                'max_tokens'  => 150,
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
            $text = substr($text, 0, 300); // Nuclear limit: Only 300 chars (approx 75 tokens)

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

        // 3. Persiapkan Context Kriteria (ULTRA COMPACT)
        // Format: C1:Name|C2:Name
        $criteriaContext = $kriterias->map(function($k) {
            return "{$k->kode}:{$k->nama}";
        })->join("|");

        // LOAD AI KNOWLEDGE BASE (DISABLED FOR SAVING TOKENS)
        $knowledgeContext = "";
        
        $prompt = "TASK:Score CV.
        CRIT:$criteriaContext
        
        CV:
        $text
        
        JSON:
        {
            \"summary\": \"1 sentence\",
            \"recommendation\": \"HIGH/LOW\",
            \"match_confidence\": \"HIGH/LOW\",
            \"details\": {
                \"KODE\":{\"score\":1-5,\"reason\":\"Brief\",\"evidence\":\"Brief\"}
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
                    ['role' => 'system', 'content' => 'JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.1, // Rendah untuk konsistensi, tapi tidak nol mutlak
                'max_tokens' => 150, // Limit output nuclear
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