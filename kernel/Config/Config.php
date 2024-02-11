<?php

namespace App\Kernel\Config;

class Config
{
    public function config(string $key, $default = null)
    {
        $keyParts = explode('.', $key);

        $configPath = APP_PATH . "/config/$keyParts[0].php";

        if (!file_exists($configPath)) {
            return $default;
        }

        $config = require_once $configPath;

        // убираем 1 элемент из массива
        $slicedKeyParts = array_slice($keyParts, 1);

        foreach ($slicedKeyParts as $part) {
            if (isset($config[$part])) {
                $config = $config[$part];
            } else {
                return $default;
            }
        }

        return $config ?? $default;
    }
}