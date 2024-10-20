<?php

namespace Vertuoza\Factories;

use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Kernel;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeRepository;
use Vertuoza\Usecases\UnitTypes\UnitTypeUseCases;

/**
 * @deprecated move to kernel
 */
class UseCasesFactory
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
