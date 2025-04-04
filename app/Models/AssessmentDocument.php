<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'document_type',
        'name',
        'file_path',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
