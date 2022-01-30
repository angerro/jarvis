<?php

namespace Jarvis\Vendor\Exception;

/**
 * Этот класс ошибок применяется при вызове команды с неверным набором аргументов/опций
 */
class FormatException extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct("Ошибка формата команды: " . $message, $code);
    }
}