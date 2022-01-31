<?php

namespace Jarvis\Vendor;

use Jarvis\Vendor\Command\Registry;
use Jarvis\Vendor\Exception\FormatException;
use Jarvis\Vendor\Input\CommandData;
use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Output\AbstractOutput;
use ReflectionException;

class Application
{
    /**
     * @var CommandData
     */
    protected $commandData;
    /**
     * @var AbstractOutput
     */
    protected $output;

    /**
     * @throws ReflectionException
     * @throws Exception\ConfigException
     */
    public function __construct(CommandData $params, AbstractOutput $output)
    {
        $this->commandData = $params;
        $this->output = $output;
        Registry::init();
    }

    /**
     * @throws FormatException
     */
    public function run()
    {
        $command = $this->commandData->getCommand();

        if (empty($command)) {
            throw new FormatException("Укажите команду для выполнения");
        }
        if (!Registry::hasCommand($command)) {
            throw new FormatException("Класс, описывающий команду '$command', не найден");
        }

        /**
         * @var AbstractCommand $commandInstance
         */
        $commandInstance = Registry::getCommandInstance($command);
        $commandInstance->run($this->commandData, $this->output);
    }
}