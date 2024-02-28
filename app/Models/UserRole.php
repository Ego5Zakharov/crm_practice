<?php

namespace App\Models;

use App\Kernel\Database\Model;

class UserRole extends Model
{
    protected string $primaryKey = "id";

    protected string $table = 'user_role';

    protected bool $guard = true;

    protected array $fillable = [
        'id',
        'user_id',
        'role_id',
    ];

}