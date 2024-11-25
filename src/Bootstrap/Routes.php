<?php

declare(strict_types=1);

namespace Looxa\Factorio\Bootstrap;

use Psr\Container\ContainerInterface;
use Slim\App;

class Routes
{
    /**
     * Register routes
     * @param \Slim\App<ContainerInterface> $app
     * @return void
     */
    public static function register(App $app): void
    {
        //TODO

        // Test Home
        $app->get('/', function ($request, $response, array $args) use ($app) {
            $name = $app->getContainer()->get('test');
            $response->getBody()->write(sprintf('Hello %s', $name));
            return $response;
        });
    }
}
