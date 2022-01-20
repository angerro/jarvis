<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Input\CommandData;
use Jarvis\Vendor\Output\Message;

class Help extends AbstractCommand
{
    public static $name = 'help';
    public static $description = 'Команда для отображения информации о командах Jarvis';

    public function configure()
    {
        $this->addArgument('name', 'имя команды', false);
    }

    public function execute()
    {
        if ($this->hasArgument('name')) {
            $this->help($this->getArgument('name'));
        } else {
            $this->list();
        }
    }

    private function list()
    {
        Message::success("Консольная утилита Jarvis");
        $config = include dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'config.php';
        if (empty($config['commands'])) {
            Message::info('Ни одной команды не зарегистрировано. Для начала работы создайте хотя бы одну команду.');
        } else {
            Message::warning("Доступные команды:");
        }
        foreach ($config['commands'] as $commandClass) {
            Message::info("-- {$commandClass::$name}: {$commandClass::$description}");
        }
        Message::info('Для вывода подробной информации о команде можно воспользоваться следующим вызовом: "{название команды} help" ');
    }

    private function help($command)
    {
        // Определяем класс команды
        $commandClass = AbstractCommand::getCommandClass($command);
        // Создаем экземпляр команды
        $commandEntity = new $commandClass(new CommandData([]), false);

        Message::success("Команда $command");
        if ($commandClass::$description) {
            Message::info($commandClass::$description);
        }
        if (!empty($commandEntity->getConfig('arguments'))) {
            Message::warning("Аргументы:");
            foreach ($commandEntity->getConfig('arguments') as $key => $argument) {
                $isRequired = $argument->isRequired() ? '(обязательный)' : '(необязательный)';
                $number = $key + 1;
                Message::info("{$number}. {$argument->getName()} {$isRequired}: {$argument->getDescription()}");
            }
        }
        if (!empty($commandEntity->getConfig('options'))) {
            Message::warning("Опции:");
            foreach ($commandEntity->getConfig('options') as $option) {
                $isValueRequired = $option->isValueRequired() ? '(значение обязательно)' : '';
                Message::info("-- {$option->getName()} {$isValueRequired}: {$option->getDescription()}");
            }
        }
    }
}