<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelamar>
 */
class PelamarFactory extends Factory
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
            'nama' => fake()->name(),
            'file_berkas' => 'dummy.pdf',
            'nilai_kriteria' => [],
            'status_lamaran' => 'Pending',
            'skor_akhir' => 0,
        ];
    }
}
