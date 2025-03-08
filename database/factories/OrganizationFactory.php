<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Organization テーブルには created_at のみが含まれていて、
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
        // Organization を作成した後に OrganizationDetail も一緒に作成するよう設定
        return $this->afterCreating(function (Organization $organization) {
            \App\Models\OrganizationDetail::factory()->create([
                'organization_id' => $organization->id,
            ]);
        });
    }
}