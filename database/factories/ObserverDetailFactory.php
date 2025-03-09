<?php

namespace Database\Factories;

use App\Models\Observer;
use App\Models\ObserverDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ObserverDetail>
 */
class ObserverDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ObserverDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'observer_id' => Observer::factory(),
            'name' => fake()->name(),
            'description' => fake()->jobTitle() . ' - ' . fake()->sentence(),
        ];
    }
}