<?php

namespace Database\Factories;

use App\Models\Observer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Observer>
 */
class ObserverFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Observer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Observer テーブルには created_at のみが含まれていて、
            // それは自動的に設定されるため、ここでは空の配列を返します
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        // Observer を作成した後に ObserverDetail も一緒に作成するよう設定
        return $this->afterCreating(function (Observer $observer) {
            \App\Models\ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
            ]);
        });
    }
}
