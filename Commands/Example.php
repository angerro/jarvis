<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Output\AbstractOutput;

class Example extends AbstractCommand
{
    public function configure()
    {
        $this->setName('example')
             ->setDescription('Команда для демонстрации работы консольного приложения Jarvis')
             ->addArgument('name', 'имя')
             ->addArgument('surname', 'фамилия', false)
             ->addArgument('second-name', 'отчество', false)
             ->addOption('excellence', 'положительные качества', true)
             ->addOption('job', 'есть работа');
    }

    public function execute(AbstractOutput $output)
    {
        $output->success('Результат выполнения команды:');
        $output->info("Имя: ".$this->getArgument('name'));
        if ($this->hasArgument('surname')){
            $output->info("Фамилия: ".$this->getArgument('surname'));
        }
        if ($this->hasArgument('second-name')){
            $output->info("Отчество: ".$this->getArgument('second-name'));
        }
        if ($this->hasOption('excellence')){
            $output->info("Положительные качества: ". implode(', ', $this->getOption('excellence')));
        }
        if ($this->hasOption('job')){
            $output->info("Работа: есть");
        }
    }
}