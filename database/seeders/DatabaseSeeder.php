<?php

namespace Database\Seeders;

use App\Models\EmergencyContact;
use App\Models\EmergencyProfile;
use App\Models\MedicalCondition;
use App\Models\Medication;
use App\Models\User;
use App\Services\EmergencyResourceService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $resources = app(EmergencyResourceService::class);

        $admin = User::factory()
            ->has(
                EmergencyProfile::factory()
                    ->has(EmergencyContact::factory()->count(2), 'contacts')
                    ->has(MedicalCondition::factory()->count(2), 'medicalConditions')
                    ->has(Medication::factory()->count(2), 'medications')
            , 'emergencyProfile')
            ->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'is_admin' => true,
            ]);

        $resources->bootstrap($admin, '0000');

        $user = User::factory()
            ->has(
                EmergencyProfile::factory()
                    ->has(EmergencyContact::factory()->count(1), 'contacts')
                    ->has(MedicalCondition::factory()->count(1), 'medicalConditions')
                    ->has(Medication::factory()->count(1), 'medications')
            , 'emergencyProfile')
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $resources->bootstrap($user, '1234');
    }
}
