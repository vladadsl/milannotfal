<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medication>
 */
class MedicationFactory extends Factory
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
            'name' => fake()->randomElement(['Ibuprofen', 'Metformin', 'Atorvastatin']),
            'dosage' => fake()->randomElement(['5mg', '10mg', '500mg']),
            'frequency' => fake()->randomElement(['Once daily', 'Twice daily', 'As needed']),
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            'requires_pin' => true,
        ];
    }
}
