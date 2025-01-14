<?php

namespace App\Models;

use App\Kernel\Auth\HasApiTokens;
use App\Kernel\Database\Model;

class User extends Model
{
    use HasApiTokens;

    protected string $primaryKey = "id";

    protected string $table = 'users';

//    protected array $with = [
//        'role'
//    ];
    protected bool $guard = true;

    protected array $fillable = [
        'id',
        'name', 'email', 'password',
        'role_id'
    ];

    protected array $casts = [
        'name' => 'string',
        'email' => 'string',
        'password' => 'string',
    ];

    public function role()
    {
        return $this->hasOne(Role::class, 'role_id', 'id');
    }

//    public function roles(): ?array
//    {
//        return $this->belongsToMany(
//            Role::class,
//            'user_role',
//            'user_id',
//            'role_id',
//        );
//    }
}