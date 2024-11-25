<?php

declare(strict_types=1);

namespace Looxa\Factorio\Bootstrap;

use Slim\App;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Looxa\Factorio\Utils\FileSystem;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Interfaces\RouteGroupInterface;
use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Looxa\Factorio\Interface\PackageProviderInterface;

class Loader
{
    /**
     * Slim App
     * @var App<ContainerInterface>
     */
    private static App $app;

    /**
     * Load Application
     * @param string $path
     * @return App<ContainerInterface>
     */
    public static function load(string $path): App
    {
        $definitions = self::setup($path);
        // Create Container
        $container = self::createContainer($definitions);
        // Create Slim Application
        self::$app = self::createApp($container);
        // Return Application
        return self::$app;
    }

    /**
     * Setup config
     * @param string $path
     * @return array<mixed>
     */
    public static function setup(string $path): array
    {
        // Set root path
        FileSystem::setRoot($path);
        $configs = self::registerPackages();
        // Create config aggregator
        $agregator = new ConfigAggregator($configs);
        // Get merged config
        $definitions = $agregator->getMergedConfig();
        return $definitions;
    }

    /**
     * Register Package Providers
     * @return array<mixed>
     */
    private static function registerPackages(): array
    {
        $configs = [];
        // Get all declared classes
        $classes = get_declared_classes();
        // Iterate over all classes
        foreach ($classes as $class) {
            // Check if class implements PackageProviderInterface
            if (is_subclass_of($class, PackageProviderInterface::class)) {
                // Create instance of class
                $serviceProvider = new $class();
                // Get config path
                $config = $serviceProvider->config();
                // Add config files to aggregator
                $configs[] = new PhpFileProvider(FileSystem::genPath($config, '*.php'));
                $configs[] = new PhpFileProvider(FileSystem::genPath($config, '**/*.php'));
            }
        }
        return $configs;
    }

    /**
     * Create Container
     *
     * @param array<mixed> $definitions
     * @return ContainerInterface
     */
    public static function createContainer(array $definitions): ContainerInterface
    {
        // Create container
        $builder = new ContainerBuilder();
        // Set definitions
        $builder->addDefinitions($definitions);
        // Build container
        return $builder->build();
    }

    /**
     * Create Slim Application
     * @param ContainerInterface $container
     * @return App<ContainerInterface>
     */
    public static function createApp(ContainerInterface $container): App
    {
        // Create Application
        $app = AppFactory::createFromContainer($container);
        // Load middlewares
        self::loadMiddlewares($app);
        // Load routes
        Routes::register($app);
        return $app;
    }

    /**
     * Load middlewares
     * @param App<ContainerInterface> $app
     * @return void
     */
    private static function loadMiddlewares(App $app): void
    {
        // Load middlewares from config
        $container = $app->getContainer();
        $middlewares = $container->has('middlewares') ? $container->get('middlewares') : [];
        // Reverse middlewares (last first)
        $middlewares = array_reverse($middlewares);
        // Add middlewares
        self::addMiddlewares($app, $app, $middlewares);
    }

    /**
     * RegisterMiddlewares for app or group of routes
     *
     * @param RouteCollectorProxy<ContainerInterface> $app
     * @param RouteCollectorProxy<ContainerInterface>|RouteGroupInterface $route
     * @param array<mixed> $middlewares
     * @return void
     */
    private static function addMiddlewares(
        RouteCollectorProxy $app,
        RouteCollectorProxy|RouteGroupInterface $route,
        array $middlewares
    ): void {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                // Check if middleware is a built-in middleware (e.g. method)
                if (method_exists($app, $middleware)) {
                    // Call the application method
                    $app->{$middleware}();
                } elseif (class_exists($middleware)) {
                    // If it is a class, create an instance and add it
                    $middlewareInstance = $app->getContainer()->get($middleware);
                    $route->add($middlewareInstance);
                } else {
                    throw new \DomainException("Unknown middleware: {$middleware}");
                }
            } else {
                $route->add($middleware);
            }
        }
    }
}
