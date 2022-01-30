<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Output\AbstractOutput;
use Jarvis\Vendor\Config;

class Help extends AbstractCommand
{
    public static $name = 'help';
    public static $description = 'Команда для отображения информации о командах Jarvis';

    public function configure()
    {
        $this->addArgument('name', 'имя команды', false);
    }

    public function execute(AbstractOutput $output)
    {
        if ($this->hasArgument('name')) {
            $this->help($this->getArgument('name'), $output);
        } else {
            $this->list($output);
        }
    }

    private function list(AbstractOutput $output)
    {
        $output->success("Консольная утилита Jarvis");
        $output->success("-------------------------");
        if (empty(Config::get('commands'))) {
            $output->success('Ни одной команды не зарегистрировано. Для начала работы создайте хотя бы одну команду.');
        } else {
            $output->success("Доступные команды:");
        }
        foreach (Config::get('commands') as $commandClass) {
            $output->info("- {$commandClass::$name}: {$commandClass::$description}");
        }
        $output->success('Для вывода подробной информации о команде можно воспользоваться следующим вызовом: "php jarvis {название команды} help" ');
    }

    private function help($command, AbstractOutput $output)
    {
        // Определяем класс команды
        $commandClass = AbstractCommand::getCommandClass($command);
        // Создаем экземпляр команды
        $commandDataClass= get_class($this->getCommandData());
        $commandEntity = new $commandClass(new $commandDataClass([]), false);

        $output->success("Команда $command");
        $output->success("----------------");
        if ($commandClass::$description) {
            $output->success($commandClass::$description);
        }
        if (!empty($commandEntity->getConfig('arguments'))) {
            $output->success("Аргументы:");
            foreach ($commandEntity->getConfig('arguments') as $key => $argument) {
                $isRequired = $argument->isRequired() ? '(обязательный)' : '(необязательный)';
                $number = $key + 1;
                $output->info("{$number}. {$argument->getName()} {$isRequired}: {$argument->getDescription()}");
            }
        }
        if (!empty($commandEntity->getConfig('options'))) {
            $output->success("Опции:");
            foreach ($commandEntity->getConfig('options') as $option) {
                $isValueRequired = $option->isValueRequired() ? '(значение обязательно)' : '(значение необязательно)';
                $output->info("-- {$option->getName()} {$isValueRequired}: {$option->getDescription()}");
            }
        }
    }
}