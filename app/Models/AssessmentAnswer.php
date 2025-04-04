<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'series_id',
        'question_id',
    ];

    /**
     * Get the assessment that owns the answer.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
