<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PinService
{
    public function generate(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function assignPin(User $user, ?string $plainPin = null): string
    {
        $pin = $plainPin ?? $this->generate();

        $user->forceFill([
            'pin_hash' => Hash::make($pin),
            'pin_set_at' => now(),
        ])->save();

        return $pin;
    }

    public function verify(?string $pin, ?string $hash): bool
    {
        if ($pin === null || $hash === null) {
            return false;
        }

        return Hash::check($pin, $hash);
    }
}
