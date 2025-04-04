<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMedicalDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_assessment_id',
        'doc_type',
        'description',
        'files'
    ];

    protected $casts = [
        'files' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
