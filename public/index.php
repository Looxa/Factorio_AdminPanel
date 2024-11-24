<?php
$root = dirname(__DIR__);
require_once implode(DIRECTORY_SEPARATOR, [
    $root,
    "vendor",
    "autoload.php",
]);

(new Looxa\Factorio\HelloWorld())->sayHello();