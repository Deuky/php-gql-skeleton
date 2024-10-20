<?php

namespace Vertuoza\Interface;

interface DatabaseInterface
{
    public function addConnection(array $config, $name = 'default'): void;
}
