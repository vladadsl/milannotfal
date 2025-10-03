<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmergencyProfile>
 */
class EmergencyProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years'),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'allergies' => fake()->boolean(40) ? fake()->sentence() : null,
            'primary_physician' => fake()->boolean(30) ? fake()->name() : null,
            'primary_physician_phone' => fake()->boolean(30) ? fake()->e164PhoneNumber() : null,
            'general_notes' => fake()->boolean(30) ? fake()->paragraph() : null,
            'profile_settings' => [
                'share_contacts_publicly' => true,
                'show_pin_protected_banner' => true,
            ],
        ];
    }
}
