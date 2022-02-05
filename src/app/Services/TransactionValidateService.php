<?php


namespace App\Services;


use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\IsEqualException;
use App\Exceptions\ShopkeeperException;
use App\Models\Wallet;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;

/**
 * Internal validation for a transaction following the business rules
 */
class TransactionValidateService
{
    protected $userRepository;
    protected $walletRepository;

    public function __construct(UserRepository $userRepository, WalletRepository $walletRepository)
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
    }

    public function validate(array $data)
    {
        $this->validateShopkeeper($data['payer_id']);
        $this->validateBalance($data['payer_id'], $data['value']);
    }

    private function validateShopkeeper($user)
    {
        if ($this->userRepository->isShopkeeper($user)) {
            throw new ShopkeeperException('Shopkeepers are not allowed to send money', 401);
        }
    }

    private function validateBalance($user, $value)
    {
        $wallet  = Wallet::where('user_id', $user)->first();
        if (!$this->walletRepository->walletBalance($wallet, $value)) {
            throw new InsufficientBalanceException('Insufficient balance, need to do a deposit before a transaction', 422);
        }
    }
}
