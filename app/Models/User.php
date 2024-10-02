<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'username',
        'image',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roleHasModel()
    {
        return $this->belongsTo(model_has_roles::class, 'id', 'model_id');
    }
    public function roleHasPermission()
    {
        return $this->belongsTo(model_has_permission::class, 'id', 'model_id');
    }

    // public function roles()
    // {
    //     return $this->hasManyThrough(
    //         Role::class,                // Model tujuan (Role)
    //         model_has_roles::class,         // Model perantara (model_has_roles)
    //         'model_id',                  // Foreign key di tabel model_has_roles yang mengacu pada user_id
    //         'id',                        // Primary key di tabel Role (role_id di model_has_roles mengacu pada id di tabel Role)
    //         'id',                        // Primary key di tabel User
    //         'role_id'                    // Foreign key di tabel model_has_roles yang mengacu pada role_id
    //     );
    // }

}
