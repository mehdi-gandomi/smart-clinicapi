<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'completed',
        'completed_at',
        'status',
        'notes',
        'full_response',
        'full_prompt',
        'documents_prompt',
        'documents_response',
        'assessment_prompt',
        'assessment_response'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(UserAssessmentAnswer::class);
    }

    public function medicalDocuments()
    {
        return $this->hasMany(UserMedicalDoc::class, 'user_assessment_id');
    }
    public function drugsDocuments()
    {
        return $this->hasMany(UserDrug::class, 'user_assessment_id');
    }




    public function notes()
    {
        return $this->hasMany(UserAssessmentNote::class, 'user_assessment_id');
    }
}
