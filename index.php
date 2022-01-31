<?php

use Jarvis\Vendor\Output\Browser;
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
    $output = new Browser();
    $app = new Application($input, $output);
    $app->run();
} catch (Exception $e) {
    (new Browser())->error($e->getMessage());
}