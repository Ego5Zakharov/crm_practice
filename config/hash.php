<?php

/**
 * Алгоритм шифрования который будет использоваться для
 * шифрования паролей по умолчанию
 */

/*
 * поддерживаемые алгоритмы:
 * PASSWORD_DEFAULT
 * BCRYPT
 * ARGON2I
 * ARGON2ID
 */

return [
    'hash_algorithm' => 'ARGON2ID',
];