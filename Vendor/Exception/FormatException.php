<?php

namespace Jarvis\Vendor\Exception;

class FormatException extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct("Ошибка формата команды: " . $message, $code);
    }
}