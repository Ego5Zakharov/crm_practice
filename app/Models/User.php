<?php

namespace App\Models;

use App\Kernel\Database\Model;

class User extends Model
{
    protected string $primaryKey = "id";

    protected string $table = 'users';

    protected bool $guard = true;

    protected array $with = [
        'role'
    ];

    protected array $fillable = [
        'id',
        'name', 'email', 'password',
        'role_id'
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