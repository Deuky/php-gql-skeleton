<?php

namespace Vertuoza\Factories;

use Vertuoza\Entities\UserRequestContext;
use Vertuoza\Kernel;
use Vertuoza\Patterns\FactoryPattern;
use Vertuoza\Repositories\UnitTypes\UnitTypeRepository;
use Vertuoza\Usecases\UnitTypes\UnitTypeUseCases;

class UseCaseFactory extends FactoryPattern
{
    /**
     * @var UnitTypeUseCases $unitType
     */
    public UnitTypeUseCases $unitType;

    /**
     * Constructor
     */
    public function __construct(UserRequestContext $userContext)
    {
        $kernel = Kernel::getInstance();

        $this->unitType = new UnitTypeUseCases($userContext, $kernel->getRepository(UnitTypeRepository::class));
    }
}
