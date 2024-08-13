<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconcileList extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function detail()
    {
        return $this->hasMany(ReconcileList::class, 'token_applicant', 'token_applicant');
    }
}
