<?php

namespace App\Repositories;

use App\Http\Resources\WalletResource;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

/**
 * Core functions of model users
 */
class UserRepository
{
    public static $_SHOPKEEPER = 'shopkeeper';
    private $walletRepository;

    public function __construct(WalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    public function listUsers(): AnonymousResourceCollection
    {
        return WalletResource::collection(Wallet::all());
    }

    public function createUser(): Model
    {
        $user = User::factory()->create();
        return $user;
    }

    public function isShopkeeper($user_id): bool
    {
        $user = User::find($user_id);
        return $user->type === self::$_SHOPKEEPER;
    }

    public function userExists($user_id): bool
    {
        $user = User::find($user_id);
        return (bool) $user;
    }

    public function find($user_id)
    {
        try {
            return User::find($user_id);
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function plusMoneyByUser($user_id, $value)
    {
        $wallet = $this->walletRepository->findWallet($user_id);
        return DB::transaction(function () use($wallet, $value) {
            $this->walletRepository->plusMoney($wallet[0]->id, $value);
        });
    }
}
