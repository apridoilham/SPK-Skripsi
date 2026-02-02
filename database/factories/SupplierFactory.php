<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'nama' => fake()->company(),
            'email' => fake()->companyEmail(),
            'telepon' => fake()->phoneNumber(),
            'nama_barang' => fake()->word(),
            'harga' => fake()->numberBetween(10000, 1000000),
            'tempo_pembayaran' => fake()->randomElement(['30 Days', '60 Days', 'Cash']),
            'estimasi_pengiriman' => fake()->randomElement(['1 Week', '2 Weeks', '1 Month']),
            'catatan_negosiasi' => fake()->sentence(),
            'file_berkas' => 'dummy.pdf',
            'nilai_kriteria' => [],
            'status_supplier' => 'Pending',
            'skor_akhir' => 0,
        ];
    }
}
