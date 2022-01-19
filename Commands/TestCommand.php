<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Output\Message;

class TestCommand extends AbstractCommand
{
    public static $name = 'test';
    public static $description = 'Тестовая команда для демонстрации работы консольного приложения Jarvis';

    public function configure()
    {
        $this->addArgument('name', 'имя')
             ->addArgument('surname', 'фамилия', false);

        $this->addOption('second-name', 'отчество', true);
    }

    public function execute()
    {
        Message::success('Результат выполнения команды:');
        Message::info("Имя: ".$this->getArgument('name'));
        if ($this->hasArgument('surname')){
            Message::info("Фамилия: ".$this->getArgument('name'));
        }
        if ($this->hasOption('second-name')){
            Message::info("Отчество: ".$this->getOption('second-name'));
        }
    }
}