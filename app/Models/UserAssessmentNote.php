<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssessmentNote extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_assessment_notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_assessment_id',
        'series_id',
        'notes'
    ];

    /**
     * Get the assessment that owns the note.
     */
    public function assessment()
    {
        return $this->belongsTo(UserAssessment::class, 'user_assessment_id');
    }

    /**
     * Get the assessment series associated with this note.
     */
    public function series()
    {
        return $this->belongsTo(AssessmentSeries::class, 'series_id', 'series_id');
    }
}
