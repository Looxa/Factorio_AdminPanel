<?php

declare(strict_types=1);

namespace Looxa\Factorio\Provider;

use Looxa\Factorio\Interface\PackageProviderInterface;
use Looxa\Factorio\Utils\FileSystem;

/**
 * Class PackageProvider
 */
class PackageProvider implements PackageProviderInterface
{
    /**
     * Returns the path to the config directory.
     *
     * @return string
     */
    public static function config(): string
    {
        return FileSystem::genPath(__DIR__, '..', '..', 'config');
    }
}
