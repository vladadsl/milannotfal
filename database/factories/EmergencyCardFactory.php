<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmergencyCard>
 */
class EmergencyCardFactory extends Factory
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
            'card_uuid' => (string) Str::uuid(),
            'qr_token' => Str::random(32),
            'status' => 'active',
            'activated_at' => now(),
            'metadata' => [
                'issued_by' => 'factory',
            ],
        ];
    }
}
