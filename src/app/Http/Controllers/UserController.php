<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\WalletCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    private $walletRepository;

    public function __construct(UserRepository $userRepository, WalletRepository $walletRepository)
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
    }

    public function users(): WalletCollection
    {
        return new WalletCollection($this->userRepository->listUsers());
    }

    public function create()
    {
        try {
            $response = new UserResource($this->userRepository->createUser());
        } catch (\Exception $exception) {
            return response()->json(["message" => $exception->getMessage()]);
        }
        return $response;
    }

    public function plusMoney(UserRequest $request)
    {
        $charge = [
            'user_id' => $request->user_id,
            'value' => $request->value
        ];

        try {
            $this->userRepository->plusMoneyByUser($charge['user_id'], $charge['value']);
            return response()->json(['success' => ['message' => 'Successfully deposited amount']]);
        } catch (\Exception $exception) {
            return response()->json(['errors' => ['message' => $exception->getMessage()]], $exception->getCode());
        }
    }

    public function filterUser(User $user)
    {
        try {
            $response = new UserResource($user);
        } catch (\Exception $exception) {
            return response()->json(["message" => 'User not found']);
        }
        return $response;
    }

}
