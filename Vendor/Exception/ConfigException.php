<?php

namespace Jarvis\Vendor\Exception;

/**
 * Этот класс ошибок применяется при реализации неверной конфигурации команды в методе configure()
 */
class ConfigException extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct("Ошибка конфигурации команды: " . $message, $code);
    }
}