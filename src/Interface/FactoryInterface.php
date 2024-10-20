<?php

namespace Vertuoza\Interface;

interface FactoryInterface
{
    /**
     * @return mixed
     */
    public function newInstance(): mixed;

    public function build($instance): void;

    /**
     * @return bool
     */
    public function supportFactory(): bool;
}
