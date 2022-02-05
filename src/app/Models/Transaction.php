<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory, HasUuid;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'payer_wallet_id',
        'payee_wallet_id',
        'value'
    ];

    public function walletPayer() : belongsTo
    {
        return $this->belongsTo(Wallet::class, 'payer_wallet_id');
    }

    public function walletPayee() : belongsTo
    {
        return $this->belongsTo(Wallet::class,'payee_wallet_id');
    }
}
