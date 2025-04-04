<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssessmentDocument extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_assessment_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_assessment_id',
        'document_type',
        'name',
        'file_path',
        // other fields...
    ];

    /**
     * Get the assessment that owns the document.
     */
    public function assessment()
    {
        return $this->belongsTo(UserAssessment::class, 'user_assessment_id');
    }
}
