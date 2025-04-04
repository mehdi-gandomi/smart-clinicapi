<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssessmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_assessment_id',
        'series_id',
        'question_id',
        'answer',
        'selected_options'
    ];

    protected $casts = [
        'selected_options' => 'array',
    ];

    public function userAssessment()
    {
        return $this->belongsTo(UserAssessment::class);
    }

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id', 'question_id');
    }

    public function series()
    {
        return $this->belongsTo(AssessmentSeries::class, 'series_id', 'series_id');
    }
}
