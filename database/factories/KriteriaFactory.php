<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kriteria>
 */
class KriteriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => 'C' . fake()->unique()->numberBetween(1, 10),
            'nama' => fake()->word(),
            'bobot' => fake()->randomFloat(2, 0, 1),
            'jenis' => fake()->randomElement(['benefit', 'cost']),
            'opsi' => ['Buruk', 'Kurang', 'Cukup', 'Baik', 'Sangat Baik'],
        ];
    }
}
