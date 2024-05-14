<?php

namespace App\Http\Middlewares;

use App\Kernel\Cache\NotFoundCacheSavePatchException;
use App\Kernel\Csrf\Csrf;
use App\Kernel\Middlewares\Middleware;
use App\Kernel\Throttle\Throtlle;
use Random\RandomException;

class CsrfMiddleware extends Middleware
{
    /**
     * @throws RandomException
     */
    public function handle(): void
    {
        $request = request();

        $allHeaders = getallheaders();

        /**
         * Если это api, а не web.php - не csrf не распространяется
         */
        if (
            $request->server['REQUEST_METHOD'] === "POST" &&
            $allHeaders && $allHeaders['Accept'] !== 'application/json'
        ) {

            if (!isset($request->post['csrf_token']) || !Csrf::checkCsrfToken($request->post['csrf_token'])) {
                die("Invalid csrf token. You need to add csrf token in your post request.");
            }

            /**
             * После успешной проверки удаляем использованный CSRF токен
             */
            unset($_SESSION['csrf_token']);

            /**
             * Создаем новый токен для следующей операции на сервере
             */
            $_SESSION['csrf_token'] = Csrf::generateCsrfToken();
        }
    }
}