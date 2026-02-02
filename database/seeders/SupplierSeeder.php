<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Supplier::truncate();
        User::where('email', 'like', '%@vendor.com')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data_dummy = [
            [
                'nama' => 'PT Sumber Makmur',
                'status' => 'Pending',
                // C1: Harga (Cost) - Lower is better
                // C2: Kecepatan (Cost) - Lower is better (days)
                // C3: Tempo (Benefit) - Higher is better (days)
                // C4: Kualitas (Benefit) - Higher is better (score 1-5)
                'nilai' => ['C1' => 5000000, 'C2' => 2, 'C3' => 30, 'C4' => 4]
            ],
            [
                'nama' => 'CV Jaya Abadi',
                'status' => 'Pending',
                'nilai' => ['C1' => 4800000, 'C2' => 3, 'C3' => 14, 'C4' => 3]
            ],
            [
                'nama' => 'UD Berkah Utama',
                'status' => 'Pending',
                'nilai' => ['C1' => 5200000, 'C2' => 1, 'C3' => 7, 'C4' => 5]
            ],
            [
                'nama' => 'PT Teknologi Maju',
                'status' => 'Pending',
                'nilai' => ['C1' => 6000000, 'C2' => 1, 'C3' => 60, 'C4' => 5]
            ],
            [
                'nama' => 'CV Lancar Jaya',
                'status' => 'Pending',
                'nilai' => ['C1' => 4500000, 'C2' => 5, 'C3' => 0, 'C4' => 2]
            ],
        ];

        foreach ($data_dummy as $data) {
            $user = User::create([
                'name' => $data['nama'],
                'email' => strtolower(str_replace(' ', '', $data['nama'])) . '@vendor.com',
                'password' => Hash::make('password'),
                'role' => 'staff', // Staff role represents the supplier contact person in this context or the staff inputting it
            ]);

            Supplier::create([
                'user_id' => $user->id,
                'nama' => $data['nama'],
                'email' => $user->email,
                'telepon' => '08123456789',
                'nama_barang' => 'Barang Elektronik',
                'harga' => $data['nilai']['C1'],
                'tempo_pembayaran' => $data['nilai']['C3'] . ' Hari',
                'estimasi_pengiriman' => $data['nilai']['C2'] . ' Hari',
                'catatan_negosiasi' => '-',
                'file_berkas' => 'dummy.pdf',
                'status_supplier' => $data['status'],
                'nilai_kriteria' => $data['nilai'], 
                'skor_akhir' => 0 
            ]);
        }
    }
}
