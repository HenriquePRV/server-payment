<?php

namespace Tests;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUser(array $options = [])
    {
        return User::factory()->create($options);
    }

    protected  function createTransaction(array $options = [])
    {
        return Transaction::factory()->create($options);
    }

    protected function plusMoney($user_id, $value): void
    {
        Wallet::where('user_id', $user_id)
            ->update(['balance' => $value]);
    }

    protected function getWallet($user_id)
    {
       return Wallet::where('user_id', $user_id);
    }
}

