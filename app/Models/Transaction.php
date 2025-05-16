<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
        'transaction_reference',
        'is_reverted',
        'original_transaction_reference',
        'reversal_reason',
        'reversed_at'
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'is_reverted' => 'boolean',
        'reversed_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

}