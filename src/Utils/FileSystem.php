<?php

declare(strict_types=1);

namespace Looxa\Factorio\Utils;

class FileSystem
{
    /**
     * @var string
     */
    private static string $root = '';

    /**
     * @param string $args
     *
     * @return string
     */
    public static function genPath(string ...$args): string
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array_map(fn(string $path) => mb_rtrim($path, DIRECTORY_SEPARATOR), $args)
        );
    }

    /**
     * Set root path
     *
     * @param string $path
     */
    public static function setRoot(string $path): void
    {
        self::$root = self::genPath($path, DIRECTORY_SEPARATOR);
    }

    /**
     * Get root path
     *
     * @return string
     */
    public static function getRoot(): string
    {
        return self::$root;
    }

    /**
     * Get path
     *
     * @param string $args
     *
     * @return string
     */
    public static function getPath(string ...$args): string
    {
        $path = self::genPath(self::$root, ...$args);
        return $path;
    }

    /**
     * Create path
     *
     * @param string $path
     * @param int $mode
     *
     * @return bool
     */
    public static function createPath(string $path, int $mode = 0755): bool
    {
        if (!file_exists($path)) {
            return mkdir($path, $mode, true);
        }
        return false;
    }
}
