<?php

namespace Vertuoza\UseCases\UnitTypes;

use Vertuoza\Repositories\UnitTypeRepository;

readonly class UnitTypeUseCases
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
     * @param UnitTypeRepository $unitTypeRepository
     */
    public function __construct(UnitTypeRepository $unitTypeRepository)
    {
        $this->unitTypeById = new UnitTypeByIdUseCase($unitTypeRepository);
        $this->unitTypesFindMany = new UnitTypesFindManyUseCase($unitTypeRepository);
    }
}
