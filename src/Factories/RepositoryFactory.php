<?php

namespace Vertuoza\Factories;

use Illuminate\Database\Capsule\Manager;
use Overblog\PromiseAdapter\PromiseAdapterInterface;
use Vertuoza\Patterns\FactoryPattern;

class RepositoryFactory extends FactoryPattern
{
    protected Manager $database;
    protected PromiseAdapterInterface $promiseAdapter;

    public function virginInstance(): mixed
    {
        $className = $this->getClassName();

        return new $className(
            $this->database,
            $this->promiseAdapter
        );
    }
}
