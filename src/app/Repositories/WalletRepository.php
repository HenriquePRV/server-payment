<?php

namespace App\Repositories;

use App\Models\Wallet;

/**
 * Core functions of model wallet
 */
class WalletRepository
{
    public function plusMoney($id, $value): void
    {
        $wallet = Wallet::find($id);
        $wallet->balance = $wallet->balance + $value;
        $wallet->save();
    }

    public function minusMoney($id, $value): void
    {
        $wallet = Wallet::find($id);
        $wallet->balance = $wallet->balance - $value;
        $wallet->save();
    }

    public function walletBalance(Wallet $wallet, $value): bool
    {
        return $wallet->balance >= $value;
    }

    public function findWallet($user_id)
    {
        try {
            return Wallet::where('user_id',$user_id)->get();
        } catch (\Exception $exception) {
            return $exception;
        }
    }
}
