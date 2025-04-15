<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodPressure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'systolic',
        'diastolic',
        'heart_rate',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        // 'time' => 'datetime:H:i', // Cast time if needed, though often stored as string
    ];

    /**
     * Get the user that owns the blood pressure reading.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
