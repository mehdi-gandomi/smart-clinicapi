<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use Illuminate\Support\Str;

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
     * Charge money into wallet
     */
    public function charge(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
            'description' => 'nullable|string'
        ]);

        $wallet = Auth::user()->wallet ?? Wallet::create(['user_id' => Auth::id()]);
        $transaction = $wallet->transactions()->create([
            'amount' => $request->amount,
            'description' => $request->description ?? 'شارژ کیف پول',
            'status' => 'pending'
        ]);
        return Payment::callbackUrl(route('wallet.charge.callback', $transaction->id))->purchase(
            (new Invoice)
            ->amount($request->amount),
            function($driver, $transactionId)use($transaction) {
                // Store transactionId in database.
                // We need the transactionId to verify payment in the future.
                $transaction->update([
                    'transaction_id' => $transactionId,
                ]);
            }
        )->pay()->toJson();
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000',
            'description' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();

            // Create a pending wallet transaction
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'description' => $request->description ?? 'شارژ کیف پول',
                'status' => 'pending',
                'order_id' => Str::random(32), // Generate a unique order ID
            ]);

            // Get payment URL from your payment gateway
            $paymentUrl = $this->getPaymentUrl($transaction);

            return response()->json([
                'payment_url' => $paymentUrl,
                'order_id' => $transaction->order_id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در ایجاد تراکنش',
            ], 500);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'order_id' => 'required|string',
            'status' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            // Find the pending transaction
            $transaction = WalletTransaction::where('order_id', $request->order_id)
                ->where('status', 'pending')
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Update transaction with payment gateway response
            $transaction->transaction_id = $request->transaction_id;
            $transaction->payment_data = $request->all();

            // If payment was successful
            if ($request->status === 'success') {
                $transaction->status = 'completed';

                // Update user's wallet balance
                $user->increment('balance', $transaction->amount);

                $message = 'پرداخت با موفقیت انجام شد';
            } else {
                $transaction->status = 'failed';
                $message = 'پرداخت ناموفق بود';
            }

            $transaction->save();

            return response()->json([
                'message' => $message,
                'status' => $transaction->status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تایید تراکنش',
            ], 500);
        }
    }

    private function getPaymentUrl(WalletTransaction $transaction)
    {
        // Implement your payment gateway integration here
        // This is just a placeholder - replace with your actual payment gateway implementation
        $baseUrl = config('services.payment.url');

        return $baseUrl . '?' . http_build_query([
            'amount' => $transaction->amount,
            'order_id' => $transaction->order_id,
            'callback' => route('wallet.callback'),
        ]);
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
