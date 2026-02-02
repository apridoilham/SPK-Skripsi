<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Kriteria;
use App\Models\Supplier; // Updated Model
use App\Models\AiKnowledgeBase;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ChatbotController extends Controller
{
    /**
     * AI FEATURE 1: DATA EXTRACTOR (AUTOMATION)
     * Admin copies text -> AI Extracts to JSON -> Form Auto-fill
     */
    public function extractSupplierData(Request $request)
    {
        $request->validate(['text' => 'required|string']);
        $rawText = $request->input('text');
        $apiKey = env('GROQ_API_KEY');

        if (empty($apiKey)) return response()->json(['error' => 'API Key Missing'], 500);

        $systemPrompt = "ROLE: Intelligent Data Entry Automation Specialist.
        TASK: Extract structured data from raw unstructured text (WhatsApp chat, Email, or Offer Letter).
        OUTPUT: JSON ONLY. No preamble.
        
        FIELDS TO EXTRACT:
        - nama_barang (String, infer from context)
        - harga (Number, remove currency symbols. If multiple prices, take the base price)
        - tempo_pembayaran (String, e.g., '30 Hari', 'COD', 'Net 60')
        - estimasi_pengiriman (String, e.g., '2 Hari', '1 Minggu')
        - nama_supplier (String, if mentioned, else null)
        
        RULES:
        - If data is missing, use null.
        - Normalize numbers (e.g., '15rb' -> 15000).
        - If multiple items, just extract the first/main one or create a summary.
        
        JSON FORMAT:
        {
            \"nama_barang\": \"...\",
            \"harga\": 15000,
            \"tempo_pembayaran\": \"...\",
            \"estimasi_pengiriman\": \"...\",
            \"nama_supplier\": \"...\"
        }";

        try {
            /** @var Response $response */
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $rawText]
                    ],
                    'temperature' => 0.1, // Precision is key
                    'response_format' => ['type' => 'json_object']
                ]);

            return response()->json($response->json()['choices'][0]['message']['content']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * AI FEATURE 2: DECISION EXPLAINER (MANAGER REPORT)
     * Explains SAW Calculation Results in Plain Business Language
     */
    public function explainDecision(Request $request)
    {
        $request->validate(['ranking_data' => 'required|array']);
        $data = $request->input('ranking_data');
        $apiKey = env('GROQ_API_KEY');

        // Contextualize the data
        $promptContext = "SAW CALCULATION RESULTS:\n";
        foreach (array_slice($data, 0, 3) as $index => $row) {
            $rank = $index + 1;
            $promptContext .= "Rank {$rank}: {$row['nama']} (Score: {$row['skor_akhir']}). Details: " . json_encode($row['nilai_kriteria']) . "\n";
        }

        $systemPrompt = "ROLE: Senior Purchasing Manager & Strategic Analyst.
        TASK: Explain to the Director why the top supplier won and why others lost.
        LANGUAGE: INDONESIAN (Business Formal).
        
        STRUCTURE:
        1. **Rekomendasi Utama**: State clearly who won and why (focus on their strengths in high-weight criteria).
        2. **Perbandingan Kompetitor**: Compare Rank 1 vs Rank 2. Why did Rank 2 lose? (e.g., 'Expensive', 'Slow Delivery').
        3. **Risiko**: Are there any downsides to the winner? (e.g., 'Quality is good but Price is slightly high').
        
        TONE:
        - Persuasive, Data-Driven, Concise.
        - Do NOT mention 'SAW Calculation' or 'Normalization'. Speak about Business Value (Price, Quality, Speed).";

        try {
            /** @var Response $response */
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $promptContext]
                    ],
                    'temperature' => 0.4
                ]);

            return response()->json(['reply' => $response->json()['choices'][0]['message']['content']]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * AI FEATURE 3: NEGOTIATION COACH (STAFF ASSISTANT)
     * Generates Negotiation Scripts based on Competitor Data
     */
    public function negotiationCoach(Request $request)
    {
        $request->validate([
            'winner_name' => 'required|string',
            'competitor_name' => 'required|string', // The cheaper competitor
            'price_gap' => 'required|string' // e.g., "500 Rupiah"
        ]);
        
        $winner = $request->input('winner_name');
        $competitor = $request->input('competitor_name');
        $gap = $request->input('price_gap');
        $apiKey = env('GROQ_API_KEY');

        $systemPrompt = "ROLE: Master Negotiator & Procurement Specialist.
        TASK: Draft a negotiation message to send to the Winning Supplier ($winner).
        GOAL: Ask $winner to match the price of the competitor ($competitor) to close the deal immediately.
        
        CONTEXT:
        - We want to buy from $winner because of their Quality/Speed.
        - But $competitor offered a cheaper price (Difference: $gap).
        
        OUTPUT:
        Provide 3 Options:
        1. **Polite/Soft**: Maintaining relationship.
        2. **Firm/Direct**: Strictly business.
        3. **Urgency**: 'Close deal today if you match'.
        
        LANGUAGE: Indonesian (Professional WhatsApp/Email style).";

        try {
            /** @var Response $response */
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => "Winner: $winner, Competitor: $competitor, Gap: $gap"]
                    ],
                    'temperature' => 0.5
                ]);

            return response()->json(['reply' => $response->json()['choices'][0]['message']['content']]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // --- LEGACY / SHARED METHODS ---

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
                'author' => Auth::user()->name ?? 'Staff',
                'is_active' => true
            ]);

            return response()->json(['success' => true, 'message' => 'AI berhasil mempelajari aturan baru!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal melatih AI: ' . $e->getMessage()]);
        }
    }

    /**
     * General Chatbot for Q&A
     */
    public function sendMessage(Request $request)
    {
        // 1. Validasi Input
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->input('message');
        $history = $request->input('history', []); 
        
        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) return response()->json(['reply' => 'Error: API Key Missing.'], 500);

        // Load Knowledge
        $dbKriteria = Kriteria::all();
        $infoKriteria = $dbKriteria->map(fn($k) => "- {$k->nama} (Bobot: ".($k->bobot*100)."%)")->join("\n");
        
        $knowledge = AiKnowledgeBase::where('is_active', true)->get();
        $knowledgeContext = $knowledge->map(fn($k) => "- [{$k->topic}]: {$k->content}")->join("\n");

        $systemPrompt = "ROLE: Intelligent Purchasing Assistant.
        TONE: Professional, Efficient, Helpful.
        LANGUAGE: INDONESIAN ONLY.
        CTX: $infoKriteria
        $knowledgeContext
        
        RULES:
        1. Help with Procurement, Supplier Selection, and Negotiation.
        2. If asked about a supplier, refer to data.
        3. Be concise.";

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        if (!empty($history)) {
            foreach (array_slice($history, -5) as $msg) {
                $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            /** @var Response $response */
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => $messages,
                    'temperature' => 0.3,
                    'max_tokens' => 1024,
                ]);

            return response()->json(['reply' => $response->json()['choices'][0]['message']['content'] ?? 'No reply.']);
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}