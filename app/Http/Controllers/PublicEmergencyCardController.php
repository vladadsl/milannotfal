<?php

namespace App\Http\Controllers;

use App\Models\EmergencyCard;
use App\Services\EmergencyCardService;
use App\Services\PinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PublicEmergencyCardController extends Controller
{
    public function __construct(
        private readonly EmergencyCardService $cards,
        private readonly PinService $pins,
    ) {
    }

    public function show(string $token, Request $request): Response
    {
        $card = $this->cards->resolveByToken($token);

        abort_unless($card, 404);

        $card->loadMissing('user.emergencyProfile.contacts', 'user.emergencyProfile.medicalConditions', 'user.emergencyProfile.medications');

        $profile = optional($card->user)->emergencyProfile;

        $shareContacts = (bool) data_get($profile?->profile_settings, 'share_contacts_publicly', true);

    $verifiedTokens = (array) $request->session()->get('card.pin_verified_tokens', []);
    $pinVerified = (bool) ($verifiedTokens[$token] ?? false);

        return Inertia::render('Public/Card', [
            'card' => [
                'card_uuid' => $card->card_uuid,
                'status' => $card->status,
                'qr_token' => $card->qr_token,
            ],
            'profile' => $profile ? [
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'blood_type' => $profile->blood_type,
                'allergies' => $profile->allergies,
                'general_notes' => $profile->general_notes,
                'contacts' => $shareContacts ? $profile->contacts->map->only(['name', 'relationship', 'phone', 'priority'])->sortBy('priority')->values() : [],
                'pin_protected' => $pinVerified ? [
                    'conditions' => $profile->medicalConditions->map->only(['name', 'details', 'is_critical'])->values(),
                    'medications' => $profile->medications->map->only(['name', 'dosage', 'frequency', 'notes'])->values(),
                ] : null,
            ] : null,
            'pinVerified' => $pinVerified,
        ]);
    }

    public function unlock(string $token, Request $request): RedirectResponse
    {
        $card = $this->cards->resolveByToken($token);

        abort_unless($card, 404);

        $validated = $request->validate([
            'pin' => ['required', 'digits:4'],
        ]);

        if (! $this->pins->verify($validated['pin'], $card->user->pin_hash)) {
            return Redirect::back()->withErrors([
                'pin' => 'The provided PIN is incorrect.',
            ]);
        }

        $verifiedTokens = (array) $request->session()->get('card.pin_verified_tokens', []);
        $verifiedTokens[$token] = true;
        $request->session()->put('card.pin_verified_tokens', $verifiedTokens);

        return Redirect::route('card.public.show', $token)->with('card.pin_verified_tokens', $verifiedTokens);
    }
}
