<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconcileDraft extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function header()
    {
        return $this->belongsTo(ReconcileDraft::class, 'token_applicant', 'token_applicant');
    }

    public function merchant()
    {
        return $this->belongsTo(InternalMerchant::class, 'merchant_id', 'id');
    }
    public function bank_account()
    {
        return $this->hasOneThrough(
            BankAccount::class,
            InternalMerchant::class,
            'id',
            'merchant_id',
            'merchant_id'
        );
    }
}
