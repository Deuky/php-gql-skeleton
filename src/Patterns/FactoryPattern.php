<?php

namespace Vertuoza\Patterns;

use Vertuoza\Interface\FactoryInterface;
use Vertuoza\Registries\FactoryRegistry;
use Vertuoza\Traits\FactoryPatternTrait;

abstract class FactoryPattern implements FactoryInterface
{
    /**
     * @var class-string<FactoryRegistry>
     */
    protected static string $registry = FactoryRegistry::class;

    use FactoryPatternTrait;

    public function __construct(
        string $className,
        array $setter = []
    ) {
        [static::$registry, 'addItem']($this, [
                'setter' => $setter,
                'class_name' => $className,
                'constructor_arguments' => []
             ]
        );
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return [static::$registry, 'get']($this, 'class_name');
    }

    /**
     * @return array
     */
    public function getSetter(): array
    {
        return [static::$registry, 'get']($this, 'setter') ?? [];
    }

    /**
     * @return array
     */
    public function getConstructorArguments(): array
    {
        return [static::$registry, 'get']($this, 'constructor_arguments') ?? [];
    }

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public function addSetter(...$args): static
    {
        return $this->add('setter', ...$args);
    }

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public function addConstructorArgument(...$args): static
    {
        return $this->add('constructor_arguments', ...$args);
    }

    /**
     * @param string $key
     * @param string $setterName
     * @param mixed $value
     *
     * @return static
     */
    public function add(string $key, string $setterName, mixed $value): static
    {
        if (property_exists($this, $setterName)) {
            $this->{$setterName} = $value;
        } else {
            [static::$registry, 'add']($this, $key, $setterName, $value);
        }

        return $this;
    }

    /**
     * @return void
     */
    public function clearSetter(): void
    {
        $this->clear('setter');
    }

    /**
     * @return void
     */
    public function clearConfiguratorArguments(): void
    {
        $this->clear('constructor_arguments');
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function clear(string $key): void
    {
        [static::$registry, 'set']($this, $key, []);
    }

    /**
     * @param $instance
     *
     * @return void
     */
    public function build($instance = null): void
    {
        foreach ($this->getSetter() as $name => $value) {
            $setter = [$instance, 'set'.ucfirst($name)];
            $adder = [$instance, 'add'.ucfirst($name)];

            if (is_callable($setter)) {
                $setter($value);
            } elseif (is_callable($adder) && is_iterable($value)) {
                foreach ($value as $v) {
                    $adder(...$v);
                }
            }
        }
    }
}
