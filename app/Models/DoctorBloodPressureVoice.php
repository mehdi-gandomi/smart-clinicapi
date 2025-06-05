<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorBloodPressureVoice extends Model
{
    protected $fillable = [
        'user_id',
        'doctor_id',
        'voice_path',
        'notes',
        'blood_pressure_ids'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
} 