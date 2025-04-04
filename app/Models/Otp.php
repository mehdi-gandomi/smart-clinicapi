<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'mobile',
        'otp',
        'verified',
        'expires_at'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'expires_at' => 'datetime'
    ];
}
