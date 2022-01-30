<?php

namespace Jarvis\Vendor;

use Exception;

class Config
{
    private static $data = null;

    private function __clone()
    {

    }

    private function __construct()
    {

    }

    /**
     * @param array $data
     * @throws Exception
     */
    public static function init(array $data)
    {
        if (self::$data !== null) {
            throw new Exception('Конфигурационный файл уже был проинициализирован');
        }
        self::$data = $data;
    }

    public static function get(...$keys)
    {
        $config = self::$data;
        foreach ($keys as $key) {
            if (isset($config[$key])) {
                $config = $config[$key];
            }
        }

        return $config;
    }
}