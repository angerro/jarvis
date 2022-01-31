<?php

namespace Jarvis\Vendor\Input;

abstract class CommandData
{
    protected $command = null;
    protected $arguments = [];
    protected $options = [];

    public function __construct(array $params)
    {
        $this->parse($params);
    }

    abstract protected function parse(array $params);

    public function getCommand()
    {
        return $this->command;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}