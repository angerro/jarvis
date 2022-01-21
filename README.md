# Jarvis
Jarvis - это консольное приложение для выполнения команд.

## Создание команд

Команды определяются в классах, расширяющих AbstractCommand. 
Например, вы можете захотеть, чтобы команда выводила информацию о человеке.

Для этого необходимо в директории Commands создать класс Example, который унаследован от AbstractCommand.

Namespace класса должен соответствовать той директории, где этот класс располагается: Jarvis\Commands

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