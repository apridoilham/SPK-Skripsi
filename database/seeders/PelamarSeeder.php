<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PelamarSeeder extends Seeder
{
    public function run(): void
    {
        // PERBAIKAN: Mengisi 'nilai_kriteria' (JSON) agar ranking langsung muncul
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pelamar::truncate();
        User::where('email', 'like', '%@dummy.com')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data_dummy = [
            [
                'nama' => 'Budi Santoso',
                'status' => 'Pending',
                // C1=Pengalaman(Thn), C2=Pendidikan(Skor), C3=Skill(Skor), C4=Gaji(Jt), C5=Jarak(KM)
                'nilai' => ['C1' => 5, 'C2' => 4, 'C3' => 80, 'C4' => 8, 'C5' => 10]
            ],
            [
                'nama' => 'Siti Aminah',
                'status' => 'Pending',
                'nilai' => ['C1' => 2, 'C2' => 3, 'C3' => 70, 'C4' => 5, 'C5' => 5]
            ],
            [
                'nama' => 'Andi Pratama',
                'status' => 'Pending',
                'nilai' => ['C1' => 8, 'C2' => 4, 'C3' => 90, 'C4' => 10, 'C5' => 20]
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