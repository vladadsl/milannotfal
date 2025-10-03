<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmergencyProfileRequest;
use App\Services\EmergencyCardService;
use App\Services\EmergencyResourceService;
use App\Services\PinService;
use App\Services\QrCodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class EmergencyProfileController extends Controller
{
    public function __construct(
        private readonly QrCodeGenerator $qrCodes,
        private readonly EmergencyCardService $cards,
        private readonly PinService $pins,
        private readonly EmergencyResourceService $resources,
    ) {
    }

    public function show(Request $request): Response
    {
        $user = $request->user();

        $this->resources->bootstrap($user);

        $user->loadMissing([
            'emergencyProfile.contacts',
            'emergencyProfile.medicalConditions',
            'emergencyProfile.medications',
            'emergencyCard',
        ]);

    $card = $user->emergencyCard;
        $profile = $user->emergencyProfile;

        $qrDataUri = null;

        if ($card) {
            $qrDataUri = $this->qrCodes->generateDataUri(
                route('card.public.show', $card->qr_token),
                scale: 6,
            );
        }

        return Inertia::render('Emergency/Profile', [
            'profile' => $profile,
            'card' => $card ? $card->only([
                'card_uuid',
                'status',
                'activated_at',
                'deactivated_at',
                'qr_token',
            ]) : null,
            'qrDataUri' => $qrDataUri,
            'pinSetAt' => optional($user->pin_set_at)->toIso8601String(),
            'publicUrl' => $card ? route('card.public.show', $card->qr_token) : null,
        ]);
    }

    public function update(UpdateEmergencyProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->emergencyProfile;

        if (! $profile) {
            $this->resources->bootstrap($user);
            $profile = $user->refresh()->emergencyProfile;
        }

        $profile->fill($request->validated());
        $profile->save();

        return Redirect::back()->with('flash.banner', 'Emergency profile updated successfully.');
    }

    public function regeneratePin(Request $request): RedirectResponse
    {
        $pin = $this->pins->assignPin($request->user());

        return Redirect::back()->with('flash.pin', $pin);
    }

    public function regenerateQr(Request $request): RedirectResponse
    {
        $this->cards->regenerateQr($request->user());

        return Redirect::back()->with('flash.qr_rotated', true);
    }
}
