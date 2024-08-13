<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconcileUnmatch extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function header()
    {
        return $this->belongsTo(ReconcileList::class, 'token_applicant', 'token_applicant');
    }
}
