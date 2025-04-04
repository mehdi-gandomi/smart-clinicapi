<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDrug extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_assessment_id',
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
