<?php

namespace App\Models;

use App\Kernel\Database\Model;

class Role extends Model
{
    protected string $primaryKey = "id";

    protected string $table = 'roles';

    protected bool $guard = true;

    protected array $fillable = [
        'id', 'name',
    ];

//    public function users()
//    {
//        return $this->hasMany(User::class, 'role_id', 'id');
//    }

    public function users()
    {
        return $this->belongsToMany(
            Role::class,
            'user_role',
            'role_id',
            'user_id',
        );
    }

}