<?php

namespace Vertuoza\Repositories;

use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Vertuoza\Entities\UnitTypeEntity;
use Vertuoza\Kernel;
use function React\Async\async;

class UnitTypeRepository
{
    /**
     * @param string ...$ids
     *
     * @return PromiseInterface
     */
    private function fetchByIds(string ...$ids): PromiseInterface
    {
        return async(
            fn() => UnitTypeEntity::query()->findMany($ids)
        )();
    }

    /**
     * @param string $id
     *
     * @return Promise
     */
    public function getById(string $id): PromiseInterface
    {
        return async(
            fn() => UnitTypeEntity::query()->find($id)
        )();
    }

    /**
     * @param string $name
     * @param string|int|null $excludeId
     *
     * @return PromiseInterface
     */
    public function countUnitTypeWithLabel(string $name, string|int|null $excludeId = null): PromiseInterface
    {
        die(__METHOD__);
        return async(
            fn () => $this->getQueryBuilder(QueryEnum::SELECT)
                ->where('label', $name)
                ->where(function ($query) use ($excludeId) {
                    if (isset($excludeId)) {
                        $query->where('id', '!=', $excludeId);
                    }
                })
        )();
    }

    /**
     * @return PromiseInterface
     */
    public function findMany(): PromiseInterface
    {
        return async(
            fn () => UnitTypeEntity::query()->get()
        )();
    }

    /**
     * @param UnitTypeMutationData $data
     *
     * @return int|string
     */
    public function create(UnitTypeMutationData $data): int|string
    {
        die(__METHOD__);
        $kernel = Kernel::getInstance();
        $tenantId = $kernel->getUserContext()->getTenantId();

        return $this->getQueryBuilder(QueryEnum::INSERT)
            ->insertGetId(UnitTypeMapper::serializeCreate($data, $tenantId));
    }

    /**
     * @param string $id
     * @param UnitTypeMutationData $data
     *
     * @return void
     */
    public function update(string $id, UnitTypeMutationData $data): void
    {
        die(__METHOD__);
        $this->getQueryBuilder(QueryEnum::UPDATE)
            ->where(UnitTypeModel::getPkColumnName(), $id)
            ->update(UnitTypeMapper::serializeUpdate($data));
    }
}
