<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testPayerIsDifferentThanPayee()
    {
        $payload = [
            'payer' => 1,
            'payee' => 1,
            'value' => 10
        ];
        $response = $this->post(route('create-transaction'), $payload, );
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                "payer" => [ "The selected payer is invalid."],
                "payee" => [
                    "The selected payee is invalid.",
                    "The payee and payer must be different."
                ],
            ],
        ], 422);
    }

    public function testValueNeedsToBeHigherThanZero()
    {
        $this->createUser(['type' => 'client']);
        $this->createUser();
        $payload = [
            'payer' => 1,
            'payee' => 2,
            'value' => 0.00
        ];

        $response = $this->post(route('create-transaction'), $payload);
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                "value" => [ "The value must be at least 0.01."],
            ],
        ], 422);
    }

    public function testPayerNeedsToBeAClient()
    {
        $this->createUser(['type' => 'shopkeeper']);
        $this->createUser();

        $payload = [
            'payer' => 1,
            'payee' => 2,
            'value' => 0.01
        ];

        $response = $this->post(route('create-transaction'), $payload);
        $response->assertStatus(401);
        $response->assertJson([
            'errors' =>
                ['message' => 'Shopkeepers are not allowed to send money']
        ], 401);

    }

    public function testPayerInsufficientMoney()
    {
        $this->createUser(['type' => 'client']);
        $this->createUser();
        $this->plusMoney(1, 150);

        $payload = [
            'payer' => 1,
            'payee' => 2,
            'value' => 1000.05
        ];
        $response = $this->post(route('create-transaction'), $payload);
        $response->assertStatus(422);
        $response->assertJson([
            'errors' =>
                ['message' => 'Insufficient balance, need to do a deposit before a transaction']
        ],
            422);
    }

    public function testCertifyMoneyHasSent()
    {
        $this->createUser(['type' => 'client']);
        $this->createUser();
        $this->plusMoney(1, 150);

        $payload = [
            'payer' => 1,
            'payee' => 2,
            'value' => 100
        ];

        $response = $this->post(route('create-transaction'), $payload);
        $response->assertStatus(201);
        $this->assertDatabaseHas('wallets',[
            'user_id' => 1,
            'balance' => 50
        ]);
    }

    public function testCertifyMoneyHasReceived()
    {
        $this->createUser(['type' => 'client']);
        $this->createUser();
        $this->plusMoney(1, 350);

        $payload = [
            'payer' => 1,
            'payee' => 2,
            'value' => 55
        ];

        $response = $this->post(route('create-transaction'), $payload);
        $response->assertStatus(201);
        $this->assertDatabaseHas('wallets',[
            'user_id' => 2,
            'balance' => 55
        ]);
    }
}
