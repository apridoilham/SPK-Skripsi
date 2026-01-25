<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PelamarSeeder extends Seeder
{
    public function run(): void
    {
        // PERBAIKAN: Mengisi 'nilai_kriteria' (JSON) agar ranking langsung muncul
        
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pelamar::truncate();
        User::where('email', 'like', '%@dummy.com')->delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data_dummy = [
            [
                'nama' => 'Andi Pratama',
                'status' => 'Lulus',
                // Nilai JSON (Key harus sesuai Kode Kriteria C1, C2...)
                'nilai' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 4] 
            ],
            [
                'nama' => 'Budi Santoso',
                'status' => 'Ditolak',
                'nilai' => ['C1' => 3, 'C2' => 1, 'C3' => 1, 'C4' => 4]
            ],
            [
                'nama' => 'Citra Lestari',
                'status' => 'Pending',
                'nilai' => ['C1' => 4, 'C2' => 2, 'C3' => 2, 'C4' => 3]
            ],
            [
                'nama' => 'Dewi Anggraini',
                'status' => 'Pending',
                'nilai' => ['C1' => 4, 'C2' => 3, 'C3' => 3, 'C4' => 2]
            ],
            [
                'nama' => 'Eko Kurniawan',
                'status' => 'Pending',
                'nilai' => ['C1' => 3, 'C2' => 4, 'C3' => 4, 'C4' => 4]
            ],
        ];

        foreach ($data_dummy as $data) {
            $user = User::create([
                'name' => $data['nama'],
                'email' => strtolower(str_replace(' ', '', $data['nama'])) . '@dummy.com',
                'password' => Hash::make('password'),
                'role' => 'pelamar',
            ]);

            Pelamar::create([
                'user_id' => $user->id,
                'nama' => $data['nama'],
                'file_berkas' => 'dummy.pdf',
                'status_lamaran' => $data['status'],
                // Masukkan nilai ke kolom JSON
                'nilai_kriteria' => $data['nilai'], 
                'skor_akhir' => 0 // Akan dihitung ulang oleh HRD
            ]);
        }
    }
}