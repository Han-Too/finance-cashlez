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
}
