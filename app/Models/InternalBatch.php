<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalBatch extends Model
{
    protected $guarded = [];
    // protected $fillable = [
    //     'batch_fk', 
    //     'transaction_count',
    //     'status',
    //     'tid',
    //     'mid',
    //     'merchant_name',
    //     'processor',
    //     'batch_running_no',
    //     'merchant_id',
    //     'mid_ppn',
    //     'transaction_amount',
    //     'settlement_audit_id',
    //     'tax_payment',
    //     'fee_mdr_merchant',
    //     'fee_bank_merchant',
    //     'bank_transfer',
    //     'total_sales_amount',
    //     'bank_id',
    //     'created_by',
    //     'created_at',
    //     'updated_at',
    //     'settlement_date',
    // ];

    public function merchant()
    {
        return $this->belongsTo(InternalMerchant::class, 'merchant_id', 'id');
    }
}
