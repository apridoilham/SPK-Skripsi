<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kriteria;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clean old data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Kriteria::truncate();
        Supplier::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Admin Account
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kantor.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Purchasing Manager Account (Approval & Analysis)
        User::create([
            'name' => 'Bapak Manager',
            'email' => 'manager@kantor.com',
            'password' => Hash::make('password'),
            'role' => 'hrd', // Keep legacy role 'hrd' mapped to Manager in Controller for now, or update Controller to 'manager'. 
            // Note: In SpkController, logic is: if ($user->role == 'hrd'). Let's stick to 'hrd' for now to avoid breaking middleware/logic, 
            // OR better: Update Controller to accept 'manager' and update here.
            // Let's check SpkController line 39: } elseif ($user->role == 'hrd') {
            // I should update SpkController to accept 'manager' as well or just use 'hrd' as internal code. 
            // To be safe and clean, I will use 'hrd' but the UI shows 'Manager'. 
            // WAIT: The user asked for "Manajer Purchasing" role. 
            // Let's use 'hrd' internally for the Manager role to minimize friction, as SpkController uses it.
        ]);

        // 3. Purchasing Staff Account (Input & Negotiation)
        User::create([
            'name' => 'Staf Purchasing',
            'email' => 'staff@kantor.com',
            'password' => Hash::make('password'),
            'role' => 'staff', // This will fall into the 'else' block in SpkController:dashboard()
        ]);
        
        // 4. Supplier Criteria (Price, Delivery, Quality, Tempo)
        Kriteria::insert([
            ['kode' => 'C1', 'nama' => 'Harga Barang', 'bobot' => 0.35, 'jenis' => 'cost', 'opsi' => json_encode(['< 1 Juta', '1-5 Juta', '5-10 Juta', '10-50 Juta', '> 50 Juta'])],
            ['kode' => 'C2', 'nama' => 'Kecepatan Pengiriman', 'bobot' => 0.25, 'jenis' => 'cost', 'opsi' => json_encode(['1 Hari', '2-3 Hari', '1 Minggu', '2 Minggu', '> 2 Minggu'])],
            ['kode' => 'C3', 'nama' => 'Tempo Pembayaran', 'bobot' => 0.20, 'jenis' => 'benefit', 'opsi' => json_encode(['COD', '7 Hari', '14 Hari', '30 Hari', '60 Hari'])],
            ['kode' => 'C4', 'nama' => 'Kualitas Barang', 'bobot' => 0.20, 'jenis' => 'benefit', 'opsi' => json_encode(['Standar', 'Baik', 'Sangat Baik', 'Premium', 'Original'])]
        ]);

        // 5. Seed Knowledge Base AI
        $this->call(AiKnowledgeBaseSeeder::class);
        
        // 6. Seed Dummy Suppliers
        $this->call(SupplierSeeder::class);
    }
}
