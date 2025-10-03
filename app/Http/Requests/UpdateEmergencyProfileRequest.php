<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmergencyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:160'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'blood_type' => ['nullable', 'string', 'max:8'],
            'allergies' => ['nullable', 'string'],
            'primary_physician' => ['nullable', 'string', 'max:160'],
            'primary_physician_phone' => ['nullable', 'string', 'max:30'],
            'general_notes' => ['nullable', 'string'],
            'profile_settings' => ['nullable', 'array'],
            'profile_settings.share_contacts_publicly' => ['nullable', 'boolean'],
            'profile_settings.show_pin_protected_banner' => ['nullable', 'boolean'],
        ];
    }
}
