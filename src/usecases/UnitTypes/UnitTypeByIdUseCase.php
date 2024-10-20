<?php

namespace Vertuoza\Usecases\UnitTypes;

use React\Promise\Promise;
use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Entities\Settings\UnitTypeEntity;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeRepository;

class UnitTypeByIdUseCase
{
    /**
     * @var UnitTypeRepository
     */
    private UnitTypeRepository $unitTypeRepository;

    /**
     * @var UserRequestContext
     */
    private UserRequestContext $userContext;

    /**
     * @param UnitTypeRepository $unitTypeRepository
     * @param UserRequestContext $userContext
     */
    public function __construct(
        UnitTypeRepository $unitTypeRepository,
        UserRequestContext $userContext
    ) {
        $this->unitTypeRepository = $unitTypeRepository;
        $this->userContext = $userContext;
    }

    /**
     * @param string $id id of the unit type to retrieve
     *
     * @return Promise<UnitTypeEntity>
     */
    public function handle(string $id): Promise
    {
        return $this->unitTypeRepository->getById($id, $this->userContext->getTenantId());
    }
}
