<?php

namespace App\Services;

use App\Models\EmergencyCard;
use App\Models\User;
use Illuminate\Support\Str;

class EmergencyCardService
{
    public function ensureCard(User $user): EmergencyCard
    {
        return $user->emergencyCard()->firstOrCreate([], [
            'card_uuid' => (string) Str::uuid(),
            'qr_token' => Str::random(40),
            'status' => 'active',
            'activated_at' => now(),
        ]);
    }

    public function regenerateQr(User $user): EmergencyCard
    {
        $card = $this->ensureCard($user);

        $card->forceFill([
            'qr_token' => Str::random(40),
            'metadata' => array_merge($card->metadata ?? [], [
                'qr_rotated_at' => now()->toIso8601String(),
            ]),
        ])->save();

        return $card->refresh();
    }

    public function resolveByToken(string $token): ?EmergencyCard
    {
        return EmergencyCard::with('user.emergencyProfile')
            ->where('qr_token', $token)
            ->first();
    }
}
