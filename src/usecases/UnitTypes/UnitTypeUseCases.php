<?php

namespace Vertuoza\Usecases\UnitTypes;

use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeRepository;

class UnitTypeUseCases
{
    /**
     * @var UnitTypeByIdUseCase
     */
    public UnitTypeByIdUseCase $unitTypeById;

    /**
     * @var UnitTypesFindManyUseCase
     */
    public UnitTypesFindManyUseCase $unitTypesFindMany;

    /**
     * @param UserRequestContext $userContext
     * @param UnitTypeRepository $unitTypeRepository
     */
    public function __construct(UserRequestContext $userContext, UnitTypeRepository $unitTypeRepository)
    {
        $this->unitTypeById = new UnitTypeByIdUseCase($unitTypeRepository, $userContext);
        $this->unitTypesFindMany = new UnitTypesFindManyUseCase($unitTypeRepository, $userContext);
    }
}
