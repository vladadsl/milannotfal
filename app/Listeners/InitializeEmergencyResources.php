<?php

namespace App\Listeners;

use App\Services\EmergencyResourceService;
use Illuminate\Auth\Events\Registered;

class InitializeEmergencyResources
{
    public function __construct(private readonly EmergencyResourceService $resources)
    {
    }

    public function handle(Registered $event): void
    {
        $user = $event->user;

        if (method_exists($user, 'getAttribute')) {
            $this->resources->bootstrap($user);
        }
    }
}
