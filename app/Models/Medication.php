<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_profile_id',
        'name',
        'dosage',
        'frequency',
        'notes',
        'requires_pin',
    ];

    protected $casts = [
        'requires_pin' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(EmergencyProfile::class, 'emergency_profile_id');
    }
}
