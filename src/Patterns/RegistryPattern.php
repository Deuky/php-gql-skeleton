<?php

namespace Vertuoza\Patterns;

use SplObjectStorage;

abstract class RegistryPattern
{
    /**
     * @return SplObjectStorage
     */
    public static function getRegistry(): iterable
    {
        static $registry;

        return $registry[static::class] ??= ($registry[static::class] = new SplObjectStorage());
    }

    /**
     * @param object $item
     * @param array $set
     *
     * @return void
     */
    public static function addItem(object $item, array $set = []): void
    {
        $registry = static::getRegistry();

        if ($registry->offsetExists($item)) {
            throw new \UnexpectedValueException();
        }

        $registry[$item] = [];

        foreach ($set as $key => $value) {
            static::set($item, $key, $value);
        }
    }

    /**
     * @param $item
     *
     * @return void
     */
    public static function removeItem($item): void
    {
        $registry = static::getRegistry();

        unset($registry[$item]);
    }

    /**
     * @param $item
     * @param $key
     * @param $value
     *
     * @return void
     */
    public static function set($item, $key, $value): void
    {
        $registry = static::getRegistry();

        $registry[$item] = [$key => $value] + $registry[$item];
    }

    /**
     * @param $item
     * @param $value
     *
     * @return void
     */
    public static function append($item, $key, $value): void
    {
        $registry = static::getRegistry();

        $registry[$item][$key][] = $value;
    }

    /**
     * @param $item
     * @param $key
     *
     * @return mixed
     */
    public static function get($item, $key): mixed
    {
        $registry = static::getRegistry();

        return $registry[$item][$key];
    }

    /**
     * @param $item
     * @param $key
     * @param $subKey
     * @param $value
     *
     * @return void
     */
    public static function add($item, $key, $subKey, $value): void
    {
        $registry = static::getRegistry();
        $property = $registry[$item][$key];
        $property[$subKey] = $value;

        static::set($item, $key, $property);
    }
}
