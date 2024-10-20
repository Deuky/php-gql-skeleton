<?php

namespace Vertuoza\Usecases\UnitTypes;

use React\Promise\PromiseInterface;
use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Factories\RepositoriesFactory;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeRepository;

/**
 * @package Vertuoza\Usecases\Settings\UnitTypes
 */
class UnitTypesFindManyUseCase
{
    /**
     * @var UserRequestContext
     */
    private UserRequestContext $userContext;

    /**
     * @var UnitTypeRepository
     */
    private UnitTypeRepository $unitTypeRepository;

    /**
     * @param UnitTypeRepository $unitTypeRepository
     * @param UserRequestContext $userContext
     */
    public function __construct(
        UnitTypeRepository $unitTypeRepository,
        UserRequestContext $userContext,
    ) {
        $this->unitTypeRepository = $unitTypeRepository;
        $this->userContext = $userContext;
    }

    /**
     * @return PromiseInterface
     */
    public function handle(): PromiseInterface
    {
        return $this->unitTypeRepository->findMany($this->userContext->getTenantId());
    }
}
