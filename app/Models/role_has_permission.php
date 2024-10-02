<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class role_has_permission extends Model
{
    use HasFactory;
    protected $table = 'role_has_permissions';

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }
}
