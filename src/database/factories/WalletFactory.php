<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user_id = (array)DB::table('users')->get('id')->max();

        return [
            'id' => $this->faker->uuid,
            'user_id' => $user_id['id'],
            'balance' => 0
        ];
    }
}
