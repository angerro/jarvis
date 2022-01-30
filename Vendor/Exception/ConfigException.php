<?php

namespace Jarvis\Vendor\Exception;

class ConfigException extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct("Ошибка конфигурации команды: " . $message, $code);
    }
}