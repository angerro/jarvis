<?php

namespace Jarvis\Vendor\Command;

use Exception;
use ReflectionClass;
use ReflectionException;
use Jarvis\Vendor\Config;
use Jarvis\Vendor\Exception\ConfigException;

/**
 * Реестр команд
 */
class Registry
{
    private static $commands = [];

    private function __clone()
    {

    }

    private function __construct()
    {

    }

    /**
     * Регистрирует все команды из раздела 'commands' конфигурационного файла config.php
     * @throws ConfigException
     * @throws ReflectionException
     * @throws Exception
     */
    public static function init()
    {
        if (!empty(self::$commands)) {
            throw new Exception('Реестр команд уже был проинициализирован');
        }
        foreach (Config::get('commands') as $commandClass) {
            self::register($commandClass);
        }
    }

    /**
     * Регистрирует команду по классу, который её описывает
     * @param string $commandClass
     * @throws ConfigException
     * @throws ReflectionException
     */
    private static function register(string $commandClass)
    {
        /**
         * @var AbstractCommand $commandInstance
         */
        $commandInstance = new $commandClass();
        $commandInstance->configure();

        // Проверка на принадлежность к классу AbstractCommand
        $reflectionClass = new ReflectionClass($commandClass);
        if (!$reflectionClass->isSubclassOf(AbstractCommand::class)) {
            throw new ConfigException("Класс команды '$commandClass' нельзя зарегистрировать, т.к. он не "
                . "является потомком класса AbstractCommand");
        }

        // Проверка на существование команды с таким именем
        $commandName = $commandInstance->getName();
        if (self::hasCommand($commandName)) {
            $existClass = get_class(self::getCommandInstance($commandName));
            throw new ConfigException("Класс команды '$commandClass' нельзя зарегистрировать, т.к. команда c "
                . "названием '$commandName' уже зарегистрирована в классе '$existClass'");
        }

        self::$commands[$commandInstance->getName()] = $commandInstance;
    }

    /**
     * Возвращает экземпляр зарегистрированной команды по ее названию
     * @param $name
     * @return mixed|null
     */
    public static function getCommandInstance($name)
    {
        return self::$commands[$name] ?: null;
    }

    /**
     * Проверяет зарегистрирована ли команда
     * @param $name
     * @return bool
     */
    public static function hasCommand($name): bool
    {
        return self::getCommandInstance($name) !== null;
    }

    /**
     * Проверяет, есть ли вообще зарегистрированные команды
     * @return bool
     */
    public static function hasCommands(): bool
    {
        return !empty(self::$commands);
    }

    /**
     * Возвращает зарегистрированные команды в виде массива
     * @return array
     */
    public static function getCommands(): array
    {
        return self::$commands;
    }
}