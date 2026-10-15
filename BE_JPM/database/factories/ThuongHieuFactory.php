<?php

namespace Database\Factories;

use App\Domain\MarineCatalog\Models\ThuongHieu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ThuongHieu>
 */
class ThuongHieuFactory extends Factory
{
    protected $model = ThuongHieu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('TH???'),
            'name' => fake()->company(),
            'logo_path' => fake()->imageUrl(),
            'status' => true,
        ];
    }
}
