<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $table = 'assessment_questions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'question_id',
        'series_id',
        'text',
        'type',
        'options',
        'required',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    /**
     * Get the series that owns the question.
     */
    public function series()
    {
        return $this->belongsTo(AssessmentSeries::class, 'series_id', 'series_id');
    }

    public function answers()
    {
        return $this->hasMany(UserAssessmentAnswer::class, 'question_id', 'question_id');
    }
}
