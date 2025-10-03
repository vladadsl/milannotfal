<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_profile_id',
        'name',
        'details',
        'is_critical',
        'requires_pin',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
        'requires_pin' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(EmergencyProfile::class, 'emergency_profile_id');
    }
}
