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
                'topic' => 'Protokol Rekrutmen Hukum (Legal)',
                'content' => 'Untuk posisi Legal/Hukum: 1. Prioritaskan kandidat dengan pemahaman mendalam tentang Hukum Korporasi dan Litigasi. 2. Wajib memiliki integritas 100% (Cek latar belakang kriminal). 3. Soft skill utama: Negosiasi dan Ketelitian. 4. Jangan pernah merekomendasikan profil IT untuk posisi Hukum kecuali ada kebutuhan khusus (Legal Tech).',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
            [
                'topic' => 'Protokol Rekrutmen IT (Tech)',
                'content' => 'Untuk posisi IT/Tech: 1. Validasi skill coding via GitHub atau Portfolio (jangan percaya CV saja). 2. Cek pemahaman System Design untuk level Senior. 3. Soft skill utama: Problem Solving dan Continuous Learning. 4. Bedakan antara "Coder" (hanya ngetik kode) dan "Engineer" (membangun solusi).',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
            [
                'topic' => 'Standar Penilaian Eksekutif',
                'content' => 'Untuk level Manajer ke atas: 1. Bobot Kepemimpinan & Visi Strategis > Skill Teknis. 2. Gunakan metode STAR (Situation, Task, Action, Result) untuk menggali pengalaman masa lalu. 3. Cari indikator "Growth Mindset" dan "Resilience" (AQ tinggi).',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
            [
                'topic' => 'Deteksi Red Flag (Universal)',
                'content' => 'Waspadai: 1. "Job Hopping" (pindah tiap < 1 tahun) tanpa alasan jelas. 2. Gap masa kerja > 6 bulan tanpa aktivitas produktif. 3. Inkonsistensi tanggal di CV vs LinkedIn. 4. Jawaban wawancara yang terlalu umum/normatif.',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
            [
                'topic' => 'Filosofi Perusahaan',
                'content' => 'Kita mencari "Missionaries" bukan "Mercenaries". Orang yang peduli dengan misi perusahaan akan bertahan lebih lama daripada yang hanya mengejar gaji. Utamakan Culture Fit.',
                'author' => 'System (Grandmaster Rule)',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            AiKnowledgeBase::create($rule);
        }
    }
}
