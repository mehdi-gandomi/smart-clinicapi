<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssessmentAdditionalInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_assessment_id',
        'text_description',
        'voice_path',
        'voice_duration'
    ];

    public function assessment()
    {
        return $this->belongsTo(UserAssessment::class, 'user_assessment_id');
    }
}
