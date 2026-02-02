<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kriteria;
use App\Models\Pelamar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Kriteria::truncate();
        Pelamar::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Akun Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kantor.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Akun HRD
        User::create([
            'name' => 'Bapak HRD',
            'email' => 'hrd@kantor.com',
            'password' => Hash::make('password'),
            'role' => 'hrd',
        ]);

        // 3. Akun Pelamar (Dihapus sesuai permintaan: Hanya Admin & HRD)
        /*
        $pelamarUser = User::create([
            'name' => 'Aprido Ilham',
            'email' => 'aprido@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pelamar',
        ]);

        Pelamar::create([
            'user_id' => $pelamarUser->id,
            'nama' => $pelamarUser->name,
            'file_berkas' => 'dummy.pdf',
            'status_lamaran' => 'Pending',
            'nilai_kriteria' => ['C1' => 3, 'C2' => 3, 'C3' => 3, 'C4' => 3],
            'skor_akhir' => 0
        ]);
        */
        
        // 4. Seeder Kriteria Default (Sesuai Permintaan User)
        Kriteria::insert([
            ['kode' => 'C1', 'nama' => 'Pengalaman Kerja', 'bobot' => 0.30, 'jenis' => 'benefit', 'opsi' => json_encode(['< 1 Tahun', '1-2 Tahun', '2-4 Tahun', '4-6 Tahun', '> 6 Tahun'])],
            ['kode' => 'C2', 'nama' => 'Pendidikan Terakhir', 'bobot' => 0.20, 'jenis' => 'benefit', 'opsi' => json_encode(['SMA', 'D3', 'S1', 'S2', 'S3'])],
            ['kode' => 'C3', 'nama' => 'Skill Teknis', 'bobot' => 0.20, 'jenis' => 'benefit', 'opsi' => json_encode(['Pemula', 'Dasar', 'Menengah', 'Mahir', 'Expert'])],
            ['kode' => 'C4', 'nama' => 'Ekspektasi Gaji (Juta)', 'bobot' => 0.20, 'jenis' => 'cost', 'opsi' => json_encode(['< 5 Juta', '5-8 Juta', '8-10 Juta', '10-15 Juta', '> 15 Juta'])],
            ['kode' => 'C5', 'nama' => 'Jarak Rumah (KM)', 'bobot' => 0.10, 'jenis' => 'cost', 'opsi' => json_encode(['< 5 KM', '5-10 KM', '10-20 KM', '20-30 KM', '> 30 KM'])],
        ]);

        // 5. Seed Knowledge Base AI
        $this->call(AiKnowledgeBaseSeeder::class);
        
        // 6. Seed Data Pelamar (Budi, Siti, Andi) - DIHAPUS SESUAI PERMINTAAN
        // $this->call(PelamarSeeder::class);
    }
}
