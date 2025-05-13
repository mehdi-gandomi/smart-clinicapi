<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'transaction_id',
        'order_id',
        'state',
        'description',
        'payment_data',
    ];

    protected $casts = [
        'amount' => 'integer',
        'state' => 'integer',
        'payment_data' => 'array',
    ];

    // States
    const STATE_PENDING = 0;
    const STATE_SUCCESSFUL = 1;
    const STATE_FAILED = 2;

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
