<?php

namespace Jarvis\Vendor;

use Jarvis\Vendor\Input\CommandData;
use Jarvis\Vendor\Command\AbstractCommand;

class Application
{
    protected $commandData;

    public function __construct($params)
    {
        $this->commandData = new CommandData($params);
    }

    public function run()
    {
        // Определяем класс команды
        $commandClass = AbstractCommand::getCommandClass($this->commandData->command);
        // Создаем экземпляр команды
        $commandEntity = new $commandClass($this->commandData);
        // Вызываем метод конфигурации команды
        $commandEntity->configure();
        // Если аргумент только один = help, вызываем метод отображения информации о команде
        if (count($this->commandData->arguments) === 1 &&
            strtolower($this->commandData->arguments[0]) === 'help' &&
            empty($this->commandData->options)) {
            $commandEntity->help();
        }
        // В противном случае валидируем и вызываем команду
        else {

            $commandEntity->validate();
            $commandEntity->execute();
        }
    }
}