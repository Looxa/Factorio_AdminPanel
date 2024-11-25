<?php

declare(strict_types=1);

use Looxa\Factorio\Bootstrap\Loader;

$root = dirname(__DIR__);
require_once implode(DIRECTORY_SEPARATOR, [
    $root,
    "vendor",
    "autoload.php",
]);

$app = Loader::load($root);
$app->run();