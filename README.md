# Jarvis
Jarvis - это приложение для запуска команд как в консоли, так и в php коде 

# Содержимое

- [Создание команды](#создание-команды)
  - [Аргументы и опции](#аргументы-и-опции)
  - [Определение аргументов и опций](#определение-аргументов-и-опций)
  - [Метод configure](#метод-configure)
  - [Метод execute](#метод-execute)
  - [Регистрация команды](#регистрация-команды)
- [Запуск команды в консоли](#запуск-команды-в-консоли)
- [Запуск команды в php коде](#запуск-команды-в-php-коде)
- [Вывод информации](#вывод-информации)
- [Справочная команда help](#справочная-команда-help)

## Создание команды

Команды определяются в классах, расширяющих AbstractCommand. 
Например, вы можете захотеть, чтобы команда выводила информацию о человеке.

Для этого необходимо в директории Commands создать класс Example, который унаследован от AbstractCommand.

namespace класса должен соответствовать той директории, где этот класс располагается: Jarvis\Commands

```
<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;

class Example extends AbstractCommand
{
    
}
```

Далее необходимо заполнить значение статических переменных $name и $description в которых указывается название и 
описание команды, а также реализовать два публичных метода:
- configure
- execute

В методе configure определяются аргументы и опции команды.

Метод execute нужен для реализации логики самой команды на основе введённых аргументов и опций.
```
<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;

class Example extends AbstractCommand
{
    public static $name = 'example';
    public static $description = 'Команда для демонстрации работы консольного приложения Jarvis';

    public function configure()
    {
        
    }

    public function execute()
    {
       
    }
}
```

### Аргументы и опции

Аргумент - это неименованный параметр команды для которого важен порядок его указания в команде.

Опция - это именованный параметр команды. При запуске команды из консоли опция пишется с префиксом в виде двух дефисов.

К примеру, в команде
```
php jarvis example arg1 arg2 arg3 --option1 --option2=value1 --option2=value2
```
аргументами являются параметры arg1, arg2, arg3, а опциями option1 и option2.

Опции отличаются от аргументов тем, что:
- порядок их указания в команде неважен: 
```
--option1 --option2
```
или
```
--option2 --option1
```
не изменяет самого факта указания опций option1 и option2.
- могут как иметь значения, так и не иметь:
```
--option1 --option2=value1
```
здесь option1 не имеет значения, а option2 - имеет значение равное value1
- могут хранить в себе множество значений
```
--option2=value1 --option2=value2
``` 
здесь option2 имеет два значения: value1 и value2

### Определение аргументов и опций

Аргументы определяются с помощью метода addArgument.

Этот метод имеет следующие параметры:
- название аргумента
- описание аргумента
- обязательность

Опции определяются с помощью метода addOption.

Этот метод имеет следующие параметры:
- название опции
- описание опции
- обязательность указания значения

### Метод configure
Определим аргументы и опции в методе configure для реализации нашей задумки:
```
<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;

class Example extends AbstractCommand
{
    public static $name = 'example';
    public static $description = 'Команда для демонстрации работы консольного приложения Jarvis';

    public function configure()
    {
        $this->addArgument('name', 'имя')
             ->addArgument('surname', 'фамилия', false)
             ->addArgument('second-name', 'отчество', false)
             ->addOption('excellence', 'положительные качества', true)
             ->addOption('job', 'есть работа');
    }

    public function execute()
    {
      
    }
}
```
Получать информацию о человеке мы будем из аргументов и опций.
Здесь определены следующие аргументы:
- имя (обязательное)
- фамилия (необязательное)
- отчество (необязательное)

И опции:
- положительные качества (каждое должно иметь значение)
- есть работа (значение необязательно)

### Метод execute
Осталось сделать метод execute, который будет реализовывать логику команды на основе введённых аргументов и опций:
```
<?php

namespace Jarvis\Commands;

use Jarvis\Vendor\Command\AbstractCommand;
use Jarvis\Vendor\Output\Message;

class Example extends AbstractCommand
{
    public static $name = 'example';
    public static $description = 'Команда для демонстрации работы консольного приложения Jarvis';

    public function configure()
    {
        $this->addArgument('name', 'имя')
             ->addArgument('surname', 'фамилия', false)
             ->addArgument('second-name', 'отчество', false)
             ->addOption('excellence', 'положительные качества', true)
             ->addOption('job', 'есть работа');
    }

    public function execute()
    {
        Message::success('Результат выполнения команды:');
        Message::info("Имя: ".$this->getArgument('name'));
        if ($this->hasArgument('surname')){
            Message::info("Фамилия: ".$this->getArgument('surname'));
        }
        if ($this->hasArgument('second-name')){
            Message::info("Отчество: ".$this->getArgument('second-name'));
        }
        if ($this->hasOption('excellence')){
            Message::info("Положительные качества: ". implode(', ', $this->getOption('excellence')));
        }
        if ($this->hasOption('job')){
            Message::info("Работа: есть");
        }
    }
}
```
Здесь вы можете заметить методы:
- hasArgument
- getArgument
- hasOption
- getOption

Эти методы нужны для определения присутствия и получения значения аргументов и опций в введённой команде.

### Регистрация команды

Чтобы нашу команду example можно было запустить, необходимо её добавить в конфигурационный файл config.php в блок 
commands.
Для этого нужно вписать туда её namespace

## Запуск команды в консоли
Попробуем запустить нашу команду:
```
php jarvis example василий --excellence='веселый парень' --excellence='любит борщ' --job
```
На выходе получим:
```
Результат выполнения команды:
Имя: василий
Положительные качества: веселый парень, любит борщ
Работа: есть
```

## Запуск команды в php коде
Если необходимо запустить команду не из консоли, а прямо в php коде, чтобы результат
выполнения отображался в браузере можно поступить так:
```
<?php

use Jarvis\Vendor\Output\Message;
use Jarvis\Vendor\Input\ArrayInput;
use Jarvis\Vendor\Application;

try {
    require 'autoloader.php';
    $input = new ArrayInput([
        'command'   => 'example',
        'arguments' => [
            'Василий',
            'Пупкин'
        ],
        'options'   => [
            'excellence' => [
                'любит покушать',
                'обожает сериалы',
                'добряк'
            ],
            'job'        => null
        ]
    ]);
    $app = new Application($input);
    $app->run();
} catch (\Exception $e) {
    Message::error($e->getMessage());
}
```
Здесь команда, аргументы и опции определяются в массиве, который передаётся в конструктор класса ArrayInput.

Название ключей этого массива соответствует тому, что передаётся: команда, аргументы или опции.
Порядок аргументов важен - их нужно задавать в том же порядке, что и из-под консоли.

Результат выполнения:
```
Результат выполнения команды:
Имя: Василий
Фамилия: Пупкин
Положительные качества: любит покушать, обожает сериалы, добряк
Работа: есть
```

## Вывод информации
Вывод информации производится за счет универсального класса Message, который содержит в себе
следующие статические методы:
- info
- error
- success
- warning

Все эти методы принимают на вход строку и выводят её с соответствующим цветом:
- серый (в консоли) / бирюзовый (в браузере)
- красный 
- зеленый
- желтый

При этом неважно, где происходит вывод информации - в консоли или в браузере.

## Справочная команда help
В Jarvis по умолчанию встроена команда help, с помощью которой можно получить информацию о списке команд, которые 
зарегистрированы:
```
php jarvis help
```
либо о какой-то конкретной команде:
```
php jarvis help example
```