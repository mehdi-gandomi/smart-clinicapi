<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'integer',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Deposit money into the wallet.
     */
    public function deposit(int $amount, array $metadata = []): WalletTransaction
    {
        $transaction = $this->transactions()->create([
            'amount' => $amount,
            'type' => 'deposit',
            'status' => 'completed',
            'metadata' => $metadata,
        ]);

        $this->increment('balance', $amount);

        return $transaction;
    }

    /**
     * Withdraw money from the wallet.
     */
    public function withdraw(int $amount, array $metadata = []): WalletTransaction
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $transaction = $this->transactions()->create([
            'amount' => $amount,
            'type' => 'withdraw',
            'status' => 'completed',
            'metadata' => $metadata,
        ]);

        $this->decrement('balance', $amount);

        return $transaction;
    }

    /**
     * Transfer money to another wallet.
     */
    public function transfer(Wallet $toWallet, int $amount, array $metadata = []): array
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $withdrawTransaction = $this->withdraw($amount, array_merge($metadata, [
            'transfer_to_wallet_id' => $toWallet->id,
        ]));

        $depositTransaction = $toWallet->deposit($amount, array_merge($metadata, [
            'transfer_from_wallet_id' => $this->id,
        ]));

        return [
            'withdraw' => $withdrawTransaction,
            'deposit' => $depositTransaction,
        ];
    }
}
