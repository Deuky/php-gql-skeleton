<?php

namespace Vertuoza\Factories;

use Exception;
use UnexpectedValueException;
use Vertuoza\Interface\FactoryInterface;
use Vertuoza\Interface\SingletonInterface;

// @todo should renamed
class SingletonFactory implements SingletonInterface
{
    /**
     * @var array<class-string, mixed>
     */
    private static array $instances = [];

    /**
     * @var array<FactoryInterface>
     */
    private static array $loader = [];

    /**
     * @throws Exception
     */
    protected function __construct() {
        throw new Exception("Cannot instantiated a singleton.");
    }

    /**
     * @return void
     */
    protected function __clone(): void {}

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    /**
     * @param array<array<string, class-string<FactoryInterface>> $definitions
     *
     * @return array
     */
    public static function addFactories(array $definitions): array
    {
        $return = [];

        foreach ($definitions as $name => $definition) {
            $factory = array_shift($definition);
            $mainClass = array_shift($definition);

            $return[$name] = static::addFactory($factory, $mainClass, $name);

            static::addAliases($mainClass, ...$definition);
        }

        return $return;
    }

    /**
     * @param class-string<FactoryInterface> $factoryClass
     * @param class-string $className
     * @param string $name
     *
     * @return bool
     */
    public static function addFactory(string $factoryClass, string $className, string $name): bool
    {
        if (!class_exists($factoryClass)) {
            throw new UnexpectedValueException();
        }

        if (!is_subclass_of($factoryClass, FactoryInterface::class)) {
            var_dump($factoryClass);
            throw new UnexpectedValueException();
        }

        $index = $className;

        if (!is_numeric($name)) {
            $index = $name;
            class_alias($className, $index);
        }

        if (static::$loader[$index] ?? null) {
            return false;
        }

        if (static::$instances[$index] ?? null) {
            return false;
        }

        static::$loader[$index] = $factoryClass;

        return true;
    }

    /**
     * @param class-string $className
     * @param array<class-string> $aliases
     *
     * @return bool
     */
    public static function addAliases(string $className, string ...$aliases): bool
    {
        foreach ($aliases as $alias) {
            static::$loader[$alias] = &static::$loader[$className];
            static::$instances[$alias] = &static::$instances[$className];
        }

        return true;
    }

    /**
     * @return array<class-string, mixed>
     */
    public static function getAllInstances(): array
    {
        return static::$instances;
    }

    /**
     * @param string ...$classNames
     *
     * @return array<class-string, mixed>
     */
    public static function getInstances(string ...$classNames): array
    {
        $return = [];

        foreach ($classNames as $className) {
            $return[$className] = static::getInstance($className);
        }

        return $return;
    }

    /**
     * @param class-string $className
     *
     * @return mixed
     */
    public static function getInstance(string $className): mixed
    {
        return static::$instances[$className] ??= static::$loader[$className]::newInstance($className);
    }
}
