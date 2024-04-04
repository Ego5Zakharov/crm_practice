<?php

namespace App\Models;

use App\Kernel\Database\Model;

class Token extends Model
{
    protected string $primaryKey = "id";

    protected string $table = 'user_auth_tokens';

    protected bool $guard = true;

    protected array $fillable = [
        'id', 'name',
        'access_token', 'expires_at',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}