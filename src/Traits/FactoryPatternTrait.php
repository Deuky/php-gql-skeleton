<?php

namespace Vertuoza\Traits;

use Vertuoza\Exceptions\SupportFactoryException;

trait FactoryPatternTrait
{
    /**
     * @return string
     */
    abstract public function getClassName(): string;
    public function getConstructorArguments(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function supportFactory(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function build(): void {}

    /**
     * @return mixed
     */
    public function virginInstance(): mixed
    {
        $className = $this->getClassName();

        return new $className(
            ...$this->getConstructorArguments()
        );
    }

    /**
     * @return mixed
     */
    public function newInstance(): mixed
    {
        if (!$this->supportFactory()) {
            throw new SupportFactoryException();
        }

        $instance = $this->virginInstance();

        $this->build($instance);

        return $instance;
    }
}
