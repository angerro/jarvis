<?php

namespace Jarvis\Vendor;

use Jarvis\Vendor\Input\CommandData;
use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Output\AbstractOutput;

class Application
{
    protected $commandData;
    protected $output;

    public function __construct(CommandData $params, AbstractOutput $output)
    {
        $this->commandData = $params;
        $this->output = $output;
    }

    public function run()
    {
        // Определяем класс команды
        $commandClass = AbstractCommand::getCommandClass($this->commandData->command);
        // Создаем экземпляр команды
        $commandEntity = new $commandClass($this->commandData);
        // Вызываем команду
        $commandEntity->execute($this->output);
    }
}