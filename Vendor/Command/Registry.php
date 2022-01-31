<?php

namespace Jarvis\Vendor\Command;

use ReflectionClass;
use ReflectionException;
use Jarvis\Vendor\Config;
use Exception;
use Jarvis\Vendor\Exception\ConfigException;

class Registry
{
    private static $commands = [];

    private function __clone()
    {

    }

    private function __construct()
    {

    }

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
     * Регистрирует команду по её названию
     * @param string $commandClass
     * @throws ConfigException
     * @throws ReflectionException
     */
    private static function register(string $commandClass)
    {
        $reflectionClass = new ReflectionClass($commandClass);
        if (!$reflectionClass->isSubclassOf(AbstractCommand::class)) {
            throw new ConfigException("Класс команды '$commandClass' нельзя зарегистрировать, т.к. он не является потомком класса AbstractCommand");
        }
        if (empty($commandClass::$name)) {
            throw new ConfigException("В классе команды '$commandClass' не задано название");
        }
        if (array_key_exists($commandClass::$name, self::$commands)) {
            $existClass = self::$commands[$commandClass::$name];
            throw new ConfigException("Команда c названием '$commandClass::$name' уже зарегистрирована в классе '$existClass'");
        }
        /**
         * @var AbstractCommand $commandInstance
         */
        $commandInstance = new $commandClass();
        $commandInstance->configure();
        self::$commands[$commandClass::$name] = $commandInstance;
    }

    public static function getCommandInstance($name)
    {
        return self::$commands[$name] ?: null;
    }

    public static function hasCommand($name): bool
    {
        return self::getCommandInstance($name) !== null;
    }

    public static function hasCommands(): bool
    {
        return !empty(self::$commands);
    }

    public static function getCommands(): array
    {
        return self::$commands;
    }

    public static function checkCommand($command)
    {
        if (empty($command)){
            throw new Exception("Введите команду для выполнения");
        }
        if (!Registry::hasCommand($command)){
            throw new Exception("Класс, описывающий команду '{$command}', не найден");
        }
    }
}