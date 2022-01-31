<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Command\Registry;
use Jarvis\Vendor\Exception\FormatException;
use Jarvis\Vendor\Output\AbstractOutput;

class Help extends AbstractCommand
{
    public function configure()
    {
        $this->setName('help')
             ->setDescription('Команда для отображения информации о командах Jarvis')
             ->addArgument('name', 'имя команды', false);
    }

    public function validate()
    {
        if ($this->hasArgument('name')) {
            $command = $this->getArgument('name');
            if (!Registry::hasCommand($command)){
                throw new FormatException("Класс, описывающий команду '$command', не найден");
            }
        }
    }

    public function execute(AbstractOutput $output)
    {
        if ($this->hasArgument('name')) {
            $this->help($this->getArgument('name'), $output);
            return;
        }

        $this->list($output);
    }

    private function list(AbstractOutput $output)
    {
        $output->success("Консольная утилита Jarvis");
        $output->success("-------------------------");
        if (!Registry::hasCommands()) {
            $output->success('Ни одной команды не зарегистрировано. Для начала работы создайте хотя бы одну команду.');
        } else {
            $output->success("Доступные команды:");
        }
        foreach (Registry::getCommands() as $command) {
            $output->info("- {$command->getName()}: {$command->getDescription()}");
        }
        $output->success('Для вывода подробной информации о команде можно воспользоваться следующим вызовом: "php jarvis {название команды} help" ');
    }

    private function help($command, AbstractOutput $output)
    {
        // Получим экземпляр команды
        /**
         * @var AbstractCommand $commandInstance
         */
        $commandInstance = Registry::getCommandInstance($command);

        $output->success("Команда $command");
        $output->success("----------------");
        if ($commandInstance->getDescription()) {
            $output->success($commandInstance->getDescription());
        }
        if (!empty($commandInstance->getArguments())) {
            $output->success("Аргументы:");
            foreach ($commandInstance->getArguments() as $key => $argument) {
                $isRequired = $argument->isRequired() ? '(обязательный)' : '(необязательный)';
                $number = $key + 1;
                $output->info("$number. {$argument->getName()} $isRequired: {$argument->getDescription()}");
            }
        }
        if (!empty($commandInstance->getOptions())) {
            $output->success("Опции:");
            foreach ($commandInstance->getOptions() as $option) {
                $isValueRequired = $option->isValueRequired() ? '(значение обязательно)' : '(значение необязательно)';
                $output->info("-- {$option->getName()} $isValueRequired: {$option->getDescription()}");
            }
        }
    }
}