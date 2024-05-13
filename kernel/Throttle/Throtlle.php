<?php

namespace App\Kernel\Throttle;

use App\Kernel\Cache\Cache;
use App\Kernel\Cache\NotFoundCacheSavePatchException;
use App\Kernel\Request\Request;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class Throtlle
{
    /**
     * Ограничивает количество запросов от 1 пользователя по времени в config.session
     * @throws NotFoundCacheSavePatchException
     */
    public static function rateLimiter($maxCountPerMinute = 1): void
    {
        $request = \request();

        $throtlleCounter = 1;

        $userIp = $request->getIp();

        if (Cache::exists($userIp)) {
            $throtlleCounter = Cache::get($userIp);

            if ($throtlleCounter > $maxCountPerMinute) {
                echo "Ошибка! Слишком частые запросы!";
                die;
            }

            $throtlleCounter++;
        }

        Cache::set($userIp, $throtlleCounter);
    }
}