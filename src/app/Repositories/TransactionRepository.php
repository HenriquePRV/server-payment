<?php

namespace App\Repositories;

use App\Exceptions\ServiceUnavailableException;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\TransactionAuthService;
use App\Services\TransactionValidateService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

/**
 * Core functions of transaction model
 */
class TransactionRepository
{
    protected $notificationService;
    protected $transactionAuthService;
    protected $transactionValidateService;
    protected $userRepository;
    protected $walletRepository;
    public function __construct(
        NotificationService $notificationService,
        TransactionAuthService $transactionAuthService,
        TransactionValidateService $transactionValidateService,
        UserRepository $userRepository,
        WalletRepository $walletRepository
    ) {
        $this->notificationService = $notificationService;
        $this->transactionAuthService = $transactionAuthService;
        $this->transactionValidateService = $transactionValidateService;
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
    }

    public function listTransactions(): AnonymousResourceCollection
    {
        return TransactionResource::collection(Transaction::all());
    }

    public function newTransaction(array $data): Transaction
    {
        $this->transactionValidateService->validate($data);

        if (!$this->authorizeTransaction()) {
            throw new ServiceUnavailableException('Service is unavailable! Try again in few minutes.', 503);
        }

        $payer = $this->walletRepository->findWallet($data['payer_id']);
        $payee = $this->walletRepository->findWallet($data['payee_id']);

        $transaction = $this->startTransaction($payer, $payee, $data['value']);

        $this->sendNotification();

        return $transaction;
    }

    public function startTransaction($payer, $payee ,$value): Transaction
    {
        $payload = [
            'payer_wallet_id' => $payer[0]->id,
            'payee_wallet_id' => $payee[0]->id,
            'value' => $value
        ];
        return DB::transaction(function () use($payload) {
            $transaction = Transaction::create($payload);
            $this->walletRepository->plusMoney($payload['payee_wallet_id'], $payload['value']);
            $this->walletRepository->minusMoney($payload['payer_wallet_id'], $payload['value']);
            return $transaction;
        });
    }

    public function authorizeTransaction(): bool
    {
        $response = $this->transactionAuthService->authTransaction();
        return $response['message'] === 'Autorizado';
    }

    public function sendNotification(): bool
    {
        $response = $this->notificationService->send();
        return $response['message'] === 'Success';
    }
}
