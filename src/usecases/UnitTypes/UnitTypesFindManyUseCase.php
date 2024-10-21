<?php

namespace Vertuoza\UseCases\UnitTypes;

use React\Promise\PromiseInterface;
use Vertuoza\Repositories\UnitTypes\UnitTypeRepository;

/**
 * @package Vertuoza\Usecases\Settings\UnitTypes
 */
class UnitTypesFindManyUseCase
{
    /**
     * @var UnitTypeRepository
     */
    private UnitTypeRepository $unitTypeRepository;

    /**
     * @param UnitTypeRepository $unitTypeRepository
     */
    public function __construct(
        UnitTypeRepository $unitTypeRepository
    ) {
        $this->unitTypeRepository = $unitTypeRepository;
    }

    /**
     * @return PromiseInterface
     */
    public function handle(): PromiseInterface
    {
        return $this->unitTypeRepository->findMany();
    }
}
