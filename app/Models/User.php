<?php

namespace App\Models;

use App\Kernel\Database\Model;

class User extends Model
{
    protected string $table = 'users';

    protected bool $guard = true;

    protected array $fillable = [
        'id', 'name', 'email', 'password'
    ];
    // TODO
    // добавить $casts = [];
}