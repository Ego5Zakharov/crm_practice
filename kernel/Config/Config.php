<?php

namespace App\Kernel\Config;

class Config
{
    public function get(string $key, string $default = null): mixed
    {
        $keyParts = explode('.', $key);

        $configPath = APP_PATH . "/config/$keyParts[0].php";

        if (!file_exists($configPath)) {
            return $default;
        }

        $config = require $configPath;

        foreach (array_slice($keyParts, 1) as $part) {
            if (isset($config[$part])) {
                $config = $config[$part];
            } else {
                return $default;
            }
        }

        return $config ?? $default;
    }
}
