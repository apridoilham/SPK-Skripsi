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

        // 3. Akun Pelamar
        $pelamarUser = User::create([
            'name' => 'Aprido Ilham',
            'email' => 'aprido@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pelamar',
        ]);

        // Buat data pelamar untuk Aprido Ilham
        Pelamar::create([
            'user_id' => $pelamarUser->id,
            'nama' => $pelamarUser->name,
            'file_berkas' => 'dummy.pdf',
            'status_lamaran' => 'Pending',
            'nilai_kriteria' => ['C1' => 3, 'C2' => 3, 'C3' => 3, 'C4' => 3], // Nilai default rata-rata
            'skor_akhir' => 0
        ]);
        
        // 4. Seeder Kriteria Default (Agar sistem langsung bisa dipakai)
        Kriteria::insert([
            ['kode' => 'C1', 'nama' => 'Pendidikan', 'bobot' => 0.25, 'jenis' => 'benefit', 'opsi' => json_encode(['SMA','D3','S1','S2','S3'])],
            ['kode' => 'C2', 'nama' => 'Pengalaman', 'bobot' => 0.25, 'jenis' => 'benefit', 'opsi' => json_encode(['0 Tahun','1 Tahun','2 Tahun','3 Tahun','>4 Tahun'])],
            ['kode' => 'C3', 'nama' => 'Sertifikat', 'bobot' => 0.25, 'jenis' => 'benefit', 'opsi' => json_encode(['0','1','2','3','>3'])],
            ['kode' => 'C4', 'nama' => 'Kesehatan', 'bobot' => 0.25, 'jenis' => 'benefit', 'opsi' => json_encode(['Buruk','Kurang','Cukup','Baik','Sangat Baik'])],
        ]);
    }
}
