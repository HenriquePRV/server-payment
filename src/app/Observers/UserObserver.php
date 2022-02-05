<?php

namespace App\Observers;

use App\Models\Wallet;

class UserObserver
{
    public function created()
    {
        Wallet::factory()->create();
    }
}
