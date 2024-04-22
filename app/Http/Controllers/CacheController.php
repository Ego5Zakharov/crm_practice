<?php

namespace App\Http\Controllers;

use App\Kernel\Cache\Cache;
use App\Kernel\Cache\NotFoundCacheSavePatchException;
use App\Models\User;

class CacheController
{
    /**
     * @throws NotFoundCacheSavePatchException
     */
    public function cache()
    {
        // TODO сделать так чтобы файлы удалялись через некоторое время
        $cache = new Cache();

        $users = User::query()->limit(15)->get();

        $cache->set("users2",$users);

        $users = $cache->get("users2");
        dd($users);
    }
}