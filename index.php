<?php

use Jarvis\Vendor\Output\Message;

try {
    require_once 'autoloader.php';
    // todo: Реализовать пример вызова из браузера
    Message::info('test');
} catch (\Exception $e) {
    Message::error($e->getMessage());
}