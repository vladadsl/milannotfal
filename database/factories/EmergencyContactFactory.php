<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmergencyContact>
 */
class EmergencyContactFactory extends Factory
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
            'name' => fake()->name(),
            'relationship' => fake()->randomElement(['Spouse', 'Parent', 'Sibling', 'Friend']),
            'phone' => fake()->e164PhoneNumber(),
            'email' => fake()->safeEmail(),
            'priority' => fake()->numberBetween(1, 3),
            'is_primary' => false,
        ];
    }
}
