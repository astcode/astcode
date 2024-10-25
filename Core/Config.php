<?php

namespace App\Core;

use App\Models\User;

class Config
{
    private static function getEnv($key)
    {
        return $_ENV[$key];
    }

    public static function getUserClass() 
    {
        return self::getConfig()['userClass'];
    }

    public static function getDbConfig()
    {
        return self::getConfig()['db'];
    }

    // return a multi array
    public static function getConfig()
    {
        return [
            'userClass' => User::class,
            'db' => [
                'connection' => self::getEnv('DB_CONNECTION'),
                'host' => self::getEnv('DB_HOST'),
                'port' => self::getEnv('DB_PORT'),
                'database' => self::getEnv('DB_DATABASE'),
                'user' => self::getEnv('DB_USERNAME'),
                'password' => self::getEnv('DB_PASSWORD'),
            ]
        ];
    }
}
