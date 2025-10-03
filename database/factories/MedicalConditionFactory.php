<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalCondition>
 */
class MedicalConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'emergency_profile_id' => \App\Models\EmergencyProfile::factory(),
            'name' => fake()->randomElement(['Asthma', 'Diabetes', 'Hypertension']),
            'details' => fake()->sentence(),
            'is_critical' => fake()->boolean(20),
            'requires_pin' => true,
        ];
    }
}
