<?php

namespace Jarvis\Vendor\Command;

use Exception;
use Jarvis\Vendor\Exception\FormatException;
use Jarvis\Vendor\Exception\ConfigException;
use Jarvis\Vendor\Input\CommandData;
use Jarvis\Vendor\Input\Argument;
use Jarvis\Vendor\Input\Option;
use Jarvis\Vendor\Config;
use Jarvis\Vendor\Output\AbstractOutput;

//todo: сделать метод hasCommand

abstract class AbstractCommand
{
    // Название команды. Обязательно для реализации в классах-наследниках.
    public static $name;
    // Описание команды
    public static $description;

    // Экземпляр CommandData
    private $commandData;
    // Массив аргументов, полученных из метода конфигурации команды
    private $arguments = [];
    // Ассоциативный массив опций, полученных из метода конфигурации команды
    private $options = [];

    /**
     * Конструктор экземпляра команды
     * @param CommandData $commandData - экземпляр входящей команды
     * @param bool $controlData - запускать ли метод валидации введенной команды
     * @throws ConfigException
     */
    public function __construct(CommandData $commandData, bool $controlData = true)
    {
        $this->commandData = $commandData;
        if (empty(static::$name)) {
            throw new ConfigException("Не задано название команды");
        }
        $this->configure();
        if ($controlData) {
            $this->controlData();
        }
    }

    /**
     * Метод в котором конфигурируется команда: регистрируются аргументы и опции.
     * Реализация этого метода обязательна в классах-наследниках.
     * @return mixed
     */
    abstract public function configure();

    /**
     * Метод реализующий команду.
     * Реализация этого метода обязательна в классах-наследниках.
     * @param AbstractOutput $output - поток вывода
     * @return mixed
     */
    abstract public function execute(AbstractOutput $output);

    /**
     * Возвращает объявленные аргументы или опции команды
     * @param string $type
     * @return array|void
     * @throws Exception
     */
    protected function getConfig(string $type)
    {
        if (in_array($type, ['arguments', 'options'])) {
            return $this->$type;
        }
        throw new ConfigException("Ошибка аргумента команды getConfig");
    }

    /**
     * Возвращает экземпляр CommandData
     * @return CommandData
     */
    protected function getCommandData(): CommandData
    {
        return $this->commandData;
    }

    /**
     * Получение класса команды по ее названию
     * @param $command - название команды
     * @return string
     * @throws Exception
     */
    public static function getCommandClass($command): string
    {
        foreach (Config::get('commands') as $commandClass) {
            if ($commandClass::$name === $command) {
                return $commandClass;
            }
        }
        throw new Exception("Класс, описывающий команду '{$command}', не найден");
    }

    /**
     * Объявляет аргумент на этапе конфигурации команды.
     * Обязательный аргумент не может быть объявлен после необязательного.
     * @param string $name - название
     * @param string $description - описание
     * @param bool $required - обязательность
     * @return $this
     * @throws Exception
     */
    protected function addArgument(string $name, string $description = '', bool $required = true): AbstractCommand
    {
        // Проверка на дубликат названия аргумента
        if ($this->hasArgument($name)){
            throw new ConfigException("Аргумент '{$name}' не может быть объявлен несколько раз");
        }

        // Проверка на обязательный аргумент после необязательного
        $lastArgument = end($this->arguments);
        if ($lastArgument instanceof Argument && !$lastArgument->isRequired() && $required) {
            throw new ConfigException("Обязательный аргумент '{$name}' объявлен после необязательного аргумента '{$lastArgument->getName()}'");
        }

        $this->arguments[] = new Argument($name, $description, $required);
        return $this;
    }

    /**
     * Возвращает значение аргумента в команде, либо null, если он не найден
     * @param string $argumentName - название аргумента
     * @return mixed|null
     */
    protected function getArgument(string $argumentName)
    {
        foreach ($this->arguments as $key => $argument) {
            if ($argument->getName() === $argumentName) {
                return $this->commandData->arguments[$key];
            }
        }
        return null;
    }

    /**
     * Проверяет наличие аргумента в команде
     * @param string $argumentName - название аргумента
     * @return bool
     */
    protected function hasArgument(string $argumentName): bool
    {
        return $this->getArgument($argumentName) !== null;
    }

    /**
     * Возвращает массив обязательных аргументов, определенных в конфигурации команды
     * @return array
     */
    private function getRequiredArguments(): array
    {
        return array_filter($this->arguments, function ($argument){
            return $argument->isRequired();
        });
    }

    /**
     * Объявляет опцию на этапе конфигурации команды.
     * @param string $name - название
     * @param string $description - описание
     * @param bool $valueRequired - обязательность заполнения значения
     * @return $this
     * @throws Exception
     */
    protected function addOption(string $name, string $description = '', bool $valueRequired = false): AbstractCommand
    {
        if (array_key_exists($name, $this->options)){
            throw new ConfigException("Опция '{$name}' не может быть объявлена несколько раз");
        }
        $this->options[$name] = new Option($name, $description, $valueRequired);
        return $this;
    }

    /**
     * Возвращает значение опции в команде, либо null, если она не найдена
     * @param string $optionName - название опции
     * @return mixed|null
     */
    protected function getOption(string $optionName)
    {
        if (!$this->hasOption($optionName)) {
            $option = null;
        } else if (count($this->commandData->options[$optionName]) === 1) {
            $option = $this->commandData->options[$optionName][0];
        } else {
            $option = $this->commandData->options[$optionName];
        }
        return $option;
    }

    /**
     * Проверяет наличие опции в команде
     * @param string $optionName - название опции
     * @return bool
     */
    protected function hasOption(string $optionName): bool
    {
        return array_key_exists($optionName, $this->commandData->options);
    }

    /**
     * Метод валидации введенной команды:
     * - поиск необъявленных аргументов
     * - поиск обязательных аргументов отсутствующих в команде
     * - поиск необъявленных опций
     * - поиск опций без значения, которые были объявлены с обязательным заполнением значения
     * @throws Exception
     */
    private function controlData()
    {
        // Ищем необъявленные аргументы
        if (count($this->commandData->arguments) > count($this->arguments)) {
            $lastCommandArgument = end($this->commandData->arguments);
            throw new FormatException("Указан необъявленный аргумент со значением '{$lastCommandArgument}'");
        }

        // Ищем неопределённые обязательные аргументы
        foreach ($this->getRequiredArguments() as $key => $argument) {
            if (!array_key_exists($key, $this->commandData->arguments)) {
                throw new FormatException("Обязательный аргумент '{$argument->getName()}' не определен");
            }
        }

        // Ищем необъявленные опции
        foreach (array_keys($this->commandData->options) as $commandOptionName) {
            if (!array_key_exists($commandOptionName, $this->options)){
                throw new FormatException("Указана необъявленная опция '{$commandOptionName}'");
            }
        }

        // Ищем неопределённые/пустые значения опций (равные '' или null),
        // значения которых должно быть определено
        foreach (array_keys($this->commandData->options) as $commandOptionName){
            foreach ($this->options as $option) {
                if ($commandOptionName === $option->getName() &&
                    $option->isValueRequired() &&
                    in_array(null, $this->commandData->options[$commandOptionName])){
                    throw new FormatException("Не указано значение опции '{$commandOptionName}'");
                }
            }
        }

        // Запускаем метод валидации класса-наследника
        $this->validate();
    }

    /**
     * Метод валидации, определяемый в классе-наследнике
     * В него можно добавить свою логику валидации.
     */
    protected function validate()
    {

    }

}