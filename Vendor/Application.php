<?php

namespace Jarvis\Vendor;

use Jarvis\Vendor\Command\Registry;
use Jarvis\Vendor\Input\CommandData;
use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Output\AbstractOutput;

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

    public function __construct(CommandData $params, AbstractOutput $output)
    {
        $this->commandData = $params;
        $this->output = $output;
        Registry::init();
    }

    public function run()
    {
        $command = $this->commandData->getCommand();
        Registry::checkCommand($command);
        /**
         * @var AbstractCommand $commandInstance
         */
        $commandInstance = Registry::getCommandInstance($command);
        $commandInstance->run($this->commandData, $this->output);
    }
}