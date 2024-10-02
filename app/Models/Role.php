<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use HasFactory;
    protected $guarded = [];

    // public function permission()
    // {
    //     return $this->belongsTo(role_has_permission::class, 'id', 'role_id');
    // }

    public function permission()
    {
        return $this->hasManyThrough(Permission::class, role_has_permission::class, 'role_id', 'id', 'id', 'permission_id');
    }
}
