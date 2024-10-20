<?php

namespace Vertuoza\Factories;

use Illuminate\Database\Capsule\Manager;
use Overblog\PromiseAdapter\PromiseAdapterInterface;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeRepository;

/**
 * @todo to refactor
 */
class RepositoriesFactory
{
    public UnitTypeRepository $unitType;

    public function __construct(Manager $database, PromiseAdapterInterface $dataLoaderPromiseAdapter)
    {
        $this->unitType = new UnitTypeRepository($database, $dataLoaderPromiseAdapter);
    }
}
