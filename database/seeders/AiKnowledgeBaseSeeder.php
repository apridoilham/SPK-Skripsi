<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiKnowledgeBase;

class AiKnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'topic' => 'Protokol Seleksi Supplier',
                'content' => 'Untuk pemilihan Supplier: 1. Prioritaskan harga yang kompetitif namun tetap menjaga kualitas. 2. Wajib memverifikasi legalitas usaha supplier. 3. Kecepatan pengiriman adalah faktor krusial untuk menjaga stok.',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
            [
                'topic' => 'Standar Kualitas Barang',
                'content' => 'Kualitas Barang: 1. Barang harus sesuai dengan spesifikasi teknis yang diminta. 2. Toleransi cacat produk maksimal 1%. 3. Supplier wajib memberikan garansi atau kebijakan retur yang jelas.',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
            [
                'topic' => 'Evaluasi Tempo Pembayaran',
                'content' => 'Tempo Pembayaran: 1. Utamakan supplier yang memberikan tempo pembayaran (TOP) minimal 30 hari. 2. Diskon tunai (Cash Discount) bisa dipertimbangkan jika signifikan. 3. Hindari supplier yang meminta DP besar di awal tanpa rekam jejak jelas.',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
            [
                'topic' => 'Indikator Red Flag Supplier',
                'content' => 'Waspadai: 1. Perubahan harga mendadak tanpa pemberitahuan. 2. Sering terlambat mengirim barang > 3 kali dalam sebulan. 3. Komunikasi yang sulit dan respon lambat saat ada komplain.',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
            [
                'topic' => 'Filosofi Kemitraan',
                'content' => 'Kita mencari "Strategic Partners" bukan sekadar penjual. Supplier yang proaktif memberikan solusi dan inovasi produk akan diprioritaskan untuk kontrak jangka panjang.',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            AiKnowledgeBase::create($rule);
        }
    }
}
