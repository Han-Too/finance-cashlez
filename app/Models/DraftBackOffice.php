<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftBackOffice extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function merchant()
    {
        return $this->belongsTo(InternalMerchant::class, 'merchant_id', 'id');
    }
}
