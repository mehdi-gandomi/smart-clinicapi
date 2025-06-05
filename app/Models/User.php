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
        'doctor',
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
    /**
     * Get the user's wallet.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the user's wallet transactions.
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
    public function bloodPressures(){
        return $this->hasMany(BloodPressure::class);
    }

    /**
     * Get the doctor profile associated with the user.
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }
}
