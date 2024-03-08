<?php

namespace App\Kernel\Helpers\Hash;

class Hash
{
    public static function generate(int $length = 40): string
    {
        $uniqueString = uniqid('', true);

        while (strlen($uniqueString) < $length) {
            $uniqueString .= mt_rand();
        }

        return substr($uniqueString, 0, $length);
    }
}