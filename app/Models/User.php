<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'google_id',
        'avatar',
        'password',
        'mobile',
        'national_id',
        'gender',
        'age',
        'weight',
        'height',
        'user_type',
        'primary_insurance',
        'done_assessment',
        'done_assessment_at',
        'supplementary_insurance',
        'occupation',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->user_type,['doctor','admin']);
    }
    public function assessments()
    {
        return $this->hasMany(UserAssessment::class);
    }
    public function getNameAttribute(){
        return $this->first_name." ".$this->last_name;
    }
}
