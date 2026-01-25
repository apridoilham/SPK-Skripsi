<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Kriteria;

class ChatbotController extends Controller
{
    /**
     * Menangani pesan chat dari user ke AI
     */
    public function sendMessage(Request $request)
    {
        // 1. Validasi Input
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->input('message');
        
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

        // 4. System Prompt (Instruksi Utama untuk AI)
        $systemPrompt = "
        PERAN: 
        Anda adalah Asisten HRD Virtual cerdas untuk aplikasi 'Smart-SPK' (Sistem Pendukung Keputusan SAW).
        Tugas Anda adalah membantu HRD menganalisis kebutuhan rekrutmen atau menjawab pertanyaan seputar kriteria yang ada.

        DATA KONTEKS SISTEM SAAT INI (REAL-TIME):
        Berikut adalah kriteria penilaian yang sedang aktif digunakan di database perusahaan saat ini:
        $infoKriteria
        
        ATURAN MERESPONS:
        1. MODE INFORMATIF: Jika user bertanya tentang kondisi sistem saat ini (contoh: 'Apa kriteria bobot terbesar?', 'Kriteria apa saja yang dipakai?'), jawablah BERDASARKAN DATA KONTEKS di atas. JANGAN mengarang data.
        
        2. MODE ANALISIS/KERJA: JIKA DAN HANYA JIKA user meminta rekomendasi kriteria BARU untuk posisi tertentu (contoh: 'Buatkan kriteria untuk Staff IT', 'Saya mau rekrut Marketing'):
           - Berikan analisis singkat mengapa kriteria itu cocok.
           - WAJIB sertakan JSON konfigurasi di bagian paling akhir jawaban untuk fitur 'Terapkan Otomatis'.
        
        FORMAT JSON (Hanya untuk Mode Kerja/Rekomendasi Baru):
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

        try {
            // 5. Request ke Groq API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey, 
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // Model Llama 3 yang cepat
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => 0.6, // Kreativitas seimbang
                'max_tokens' => 1024
            ]);

            if ($response->successful()) {
                return response()->json([
                    'reply' => $response->json()['choices'][0]['message']['content']
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
}