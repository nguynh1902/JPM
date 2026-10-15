<?php

namespace Database\Factories;

use App\Domain\CMS\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => fake()->sentence(),
            'answer' => fake()->paragraph(),
            'category' => fake()->randomElement(['General', 'Support', 'Billing']),
            'status' => 'ACTIVE',
        ];
    }
}
