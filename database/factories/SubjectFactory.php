<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Subject テーブルには created_at のみが含まれていて、
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
        // Subject を作成した後に SubjectDetail も一緒に作成するよう設定
        return $this->afterCreating(function (Subject $subject) {
            \App\Models\SubjectDetail::factory()->create([
                'subject_id' => $subject->id,
            ]);
        });
    }
}
