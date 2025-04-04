<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentSeries extends Model
{
    use HasFactory;

    protected $table = 'assessment_series';
    protected $primaryKey = 'series_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'series_id',
        'title',
        'description',
        'order'
    ];

    /**
     * Get the questions for this series.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class, 'series_id', 'series_id');
    }
}
