<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    private function formatTransaction($transaction)
    {
        return [
            'id' => $transaction->id,
            'amount' => (int) $transaction->amount,
            'type' => $transaction->type,
            'status' => $transaction->status,
            'description' => $transaction->description,
            'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
        ];
    }

    private function formatWallet($wallet)
    {
        return [
            'id' => $wallet->id,
            'balance' => (int) $wallet->balance,
            'is_active' => $wallet->is_active,
        ];
    }

    /**
     * Get user's wallet information
     */
    public function show()
    {
        $wallet = Auth::user()->wallet ?? Wallet::create(['user_id' => Auth::id()]);
        $recentTransactions = $wallet->transactions()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($transaction) => $this->formatTransaction($transaction));

        return response()->json([
            'status' => 'success',
            'data' => [
                'wallet' => $this->formatWallet($wallet),
                'recent_transactions' => $recentTransactions
            ]
        ]);
    }

    /**
     * Get wallet transaction history
     */
    public function transactions(Request $request)
    {
        $wallet = Auth::user()->wallet;

        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet not found'
            ], 404);
        }

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => [
                'transactions' => $transactions->map(fn($transaction) => $this->formatTransaction($transaction)),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total()
                ]
            ]
        ]);
    }

    /**
     * Deposit money into wallet
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
            'description' => 'nullable|string'
        ]);

        try {
            $wallet = Auth::user()->wallet ?? Wallet::create(['user_id' => Auth::id()]);

            DB::beginTransaction();

            $transaction = $wallet->deposit(
                $request->amount,
                [
                    'description' => $request->description,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Amount deposited successfully',
                'data' => [
                    'transaction' => $this->formatTransaction($transaction),
                    'wallet' => $this->formatWallet($wallet->fresh())
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet deposit failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'amount' => $request->amount
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process deposit'
            ], 500);
        }
    }

    /**
     * Withdraw money from wallet
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
            'description' => 'nullable|string'
        ]);

        try {
            $wallet = Auth::user()->wallet;

            if (!$wallet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet not found'
                ], 404);
            }

            DB::beginTransaction();

            $transaction = $wallet->withdraw(
                $request->amount,
                [
                    'description' => $request->description,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Amount withdrawn successfully',
                'data' => [
                    'transaction' => $this->formatTransaction($transaction),
                    'wallet' => $this->formatWallet($wallet->fresh())
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet withdrawal failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'amount' => $request->amount
            ]);

            $message = $e->getMessage() === 'Insufficient balance'
                ? 'Insufficient balance in wallet'
                : 'Failed to process withdrawal';

            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 400);
        }
    }
}
