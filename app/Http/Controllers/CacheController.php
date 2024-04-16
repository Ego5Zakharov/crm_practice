<?php

namespace App\Http\Controllers;

use App\Kernel\Cache\Cache;
use App\Models\User;

class CacheController
{
    public function cache()
    {
        $cache = new Cache();

        $cachePath = $cache->getCacheSavePath();

        $users = User::query()->limit(12)->get();

        $cache->set("users",$users);
    }
}