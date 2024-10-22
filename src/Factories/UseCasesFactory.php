<?php

namespace Vertuoza\Factories;

use Vertuoza\Kernel;
use Vertuoza\Repositories\UnitTypeRepository;
use Vertuoza\Usecases\UnitTypes\UnitTypeUseCases;

/**
 * @deprecated move to kernel
 */
readonly class UseCasesFactory
{
    /**
     * @var UnitTypeUseCases $unitType
     */
    public UnitTypeUseCases $unitType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $kernel = Kernel::getInstance();

        $this->unitType = new UnitTypeUseCases($kernel->getRepository(UnitTypeRepository::class));
    }
}
