<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'series_id',
        'notes',
    ];

    /**
     * Get the assessment that owns the note.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
