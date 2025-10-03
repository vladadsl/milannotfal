<?php

namespace App\Services;

use App\Models\EmergencyProfile;
use App\Models\User;

class EmergencyResourceService
{
    public function __construct(
        private readonly PinService $pinService,
        private readonly EmergencyCardService $cardService,
    ) {
    }

    public function bootstrap(User $user, ?string $pin = null): ?string
    {
        $generatedPin = $this->ensurePin($user, $pin);
        $this->ensureProfile($user);
        $this->cardService->ensureCard($user);

        return $generatedPin;
    }

    protected function ensurePin(User $user, ?string $pin = null): ?string
    {
        if ($pin === null && $user->pin_hash) {
            return null;
        }

        return $this->pinService->assignPin($user, $pin);
    }

    protected function ensureProfile(User $user): EmergencyProfile
    {
        if ($user->emergencyProfile) {
            return $user->emergencyProfile;
        }

        [$firstName, $lastName] = $this->splitName($user->name);

        return EmergencyProfile::create([
            'user_id' => $user->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'profile_settings' => [
                'share_contacts_publicly' => true,
                'show_pin_protected_banner' => true,
            ],
        ]);
    }

    protected function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name));

        if (empty($parts)) {
            return ['Patient', 'Unknown'];
        }

        $first = array_shift($parts) ?? 'Patient';
        $last = count($parts) ? implode(' ', $parts) : 'Unknown';

        return [$first, $last];
    }
}
