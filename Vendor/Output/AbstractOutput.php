<?php

namespace Jarvis\Vendor\Output;

abstract class AbstractOutput
{
    abstract public function write(string $str, string $color = 'info');

    public function info(string $str)
    {
        $this->write($str);
    }

    public function error(string $str)
    {
        $this->write($str, 'error');
    }

    public function success(string $str)
    {
        $this->write($str, 'success');
    }

    public function warning(string $str)
    {
        $this->write($str, 'warning');
    }
}