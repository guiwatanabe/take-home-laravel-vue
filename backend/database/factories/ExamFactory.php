<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    private array $lateralityValues = [null, 'OD', 'OE', 'AO'];

    private array $groupValues = ['Individual', 'Grupo 1', 'Grupo 2', 'Grupo 3', 'Grupo 4', 'Grupo 5'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->word()).' '.ucfirst(fake()->word()),
            'laterality' => fake()->randomElement($this->lateralityValues),
            'group' => fake()->randomElement($this->groupValues),
            'comment' => fake()->text(),
        ];
    }
}
