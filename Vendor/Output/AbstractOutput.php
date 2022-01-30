<?php

namespace Jarvis\Vendor\Output;

abstract class AbstractOutput
{
    abstract public function write(string $str, string $color = 'info');

    public function info(string $str)
    {
        static::write($str);
    }

    public function error(string $str)
    {
        static::write($str, 'error');
    }

    public function success(string $str)
    {
        static::write($str, 'success');
    }

    public function warning(string $str)
    {
        static::write($str, 'warning');
    }
}