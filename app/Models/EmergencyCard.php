<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_uuid',
        'qr_token',
        'status',
        'activated_at',
        'deactivated_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
