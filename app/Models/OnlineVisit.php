<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visit_type',
        'description',
        'voice_note_path',
        'medical_documents',
        'status',
        'answer',
        'voice_answer',
        'voice_answer_duration',
        'answered_at'
    ];

    protected $casts = [
        'medical_documents' => 'array',
        'voice_note_path' => 'array',
        'answered_at' => 'datetime',
        'voice_answer' => 'string',
        'voice_answer_duration' => 'string'
    ];

    /**
     * Get the user that owns the visit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
