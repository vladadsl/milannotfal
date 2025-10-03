<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContact extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'emergency_profile_id',
        'name',
        'relationship',
        'phone',
        'email',
        'priority',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'priority' => 'integer',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(EmergencyProfile::class, 'emergency_profile_id');
    }
}
