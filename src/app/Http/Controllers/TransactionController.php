<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Repositories\MailRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function transactions(): TransactionCollection
    {
        return new TransactionCollection($this->transactionRepository->listTransactions());
    }

    public function create(TransactionRequest $request)
    {
        $payload = [
            'payer_id' => $request->payer,
            'payee_id' => $request->payee,
            'value' => $request->value
        ];
        try {
            $transaction = $this->transactionRepository->newTransaction($payload);
            return new TransactionResource($transaction);
        } catch (\Exception $exception) {
            return response()->json(['errors' => ['message' => $exception->getMessage()]], $exception->getCode());
        }
    }

    public function filterTransaction(Transaction $transaction)
    {
        try {
            $response = new TransactionResource($transaction);
        } catch (\Exception $exception) {
            return response()->json(["message" => $exception->getMessage()], 500);
        }
        return $response;
    }

}
