<?php

namespace Vertuoza\Repositories\Settings\UnitTypes;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Overblog\DataLoader\DataLoader;
use Overblog\PromiseAdapter\PromiseAdapterInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Vertuoza\Repositories\Settings\UnitTypes\Models\UnitTypeMapper;
use Vertuoza\Repositories\Settings\UnitTypes\Models\UnitTypeModel;

use function React\Async\async;

class UnitTypeRepository
{
    /**
     * @var array
     */
    protected array $getByIdsDL;

    /**
     * @var Manager
     */
    protected Manager $db;

    /**
     * @var PromiseAdapterInterface
     */
    protected PromiseAdapterInterface $dataLoaderPromiseAdapter;

    /**
     * @param Manager $database
     * @param PromiseAdapterInterface $dataLoaderPromiseAdapter
     */
    public function __construct(
        Manager $database,
        PromiseAdapterInterface $dataLoaderPromiseAdapter
    ) {
        $this->db = $database;
        $this->dataLoaderPromiseAdapter = $dataLoaderPromiseAdapter;
        $this->getByIdsDL = [];
    }

    /**
     * @param string $tenantId
     * @param string ...$ids
     *
     * @return PromiseInterface
     */
    private function fetchByIds(string $tenantId, string ...$ids): PromiseInterface
    {
        return async(function () use ($tenantId, $ids) {
            $query = $this->getQueryBuilder()
                ->where(function ($query) use ($tenantId) {
                    $query->where([UnitTypeModel::getTenantColumnName() => $tenantId])
                        ->orWhere(UnitTypeModel::getTenantColumnName(), null);
                });
            $query->whereNull('deleted_at');
            $query->whereIn(UnitTypeModel::getPkColumnName(), $ids);

            $entities = $query->get()->mapWithKeys(function ($row) {
                $entity = UnitTypeMapper::modelToEntity(UnitTypeModel::fromStdclass($row));
                return [$entity->id => $entity];
            });

            // Map the IDs to the corresponding entities, preserving the order of IDs.
            return collect($ids)
                ->map(fn ($id) => $entities->get($id))
                ->toArray();
        })();
    }

    /**
     * @param string $tenantId
     *
     * @return DataLoader
     */
    protected function getDataloader(string $tenantId): DataLoader
    {
        if (!isset($this->getByIdsDL[$tenantId])) {

            $dl = new DataLoader(function (array $ids) use ($tenantId) {
                return $this->fetchByIds($tenantId, ...$ids);
            }, $this->dataLoaderPromiseAdapter);
            $this->getByIdsDL[$tenantId] = $dl;
        }

        return $this->getByIdsDL[$tenantId];
    }

    /**
     * @return Builder
     */
    protected function getQueryBuilder(): Builder
    {
        return $this->db->getConnection()->table(UnitTypeModel::getTableName());
    }

    /**
     * @param array $ids
     * @param string $tenantId
     *
     * @return Promise
     */
    public function getByIds(array $ids, string $tenantId): Promise
    {
        return $this->getDataloader($tenantId)->loadMany($ids);
    }

    /**
     * @param string $id
     * @param string $tenantId
     *
     * @return Promise
     */
    public function getById(string $id, string $tenantId): Promise
    {
        return $this->getDataloader($tenantId)->load($id);
    }

    public function countUnitTypeWithLabel(string $name, string $tenantId, string|int|null $excludeId = null)
    {
        return async(
            fn () => $this->getQueryBuilder()
                ->where('label', $name)
                ->whereNull('deleted_at')
                ->where(function ($query) use ($excludeId) {
                    if (isset($excludeId))
                        $query->where('id', '!=', $excludeId);
                })
                ->where(function ($query) use ($tenantId) {
                    $query->where(UnitTypeModel::getTenantColumnName(), '=', $tenantId)
                        ->orWhereNull(UnitTypeModel::getTenantColumnName());
                })
        )();
    }

    /**
     * @param string $tenantId
     *
     * @return PromiseInterface
     */
    public function findMany(string $tenantId): PromiseInterface
    {
        return async(
            fn () => $this->getQueryBuilder()
                ->whereNull('deleted_at')
                ->where(function ($query) use ($tenantId) {
                    $query->where(UnitTypeModel::getTenantColumnName(), '=', $tenantId)
                        ->orWhereNull(UnitTypeModel::getTenantColumnName());
                })
                ->get()
                ->map(function ($row) {
                    return UnitTypeMapper::modelToEntity(UnitTypeModel::fromStdclass($row));
                })
        )();
    }

    /**
     * @param UnitTypeMutationData $data
     * @param string $tenantId
     *
     * @return int|string
     */
    public function create(UnitTypeMutationData $data, string $tenantId): int|string
    {
        return $this->getQueryBuilder()->insertGetId(
            UnitTypeMapper::serializeCreate($data, $tenantId)
        );
    }

    /**
     * @param string $id
     * @param UnitTypeMutationData $data
     *
     * @return void
     */
    public function update(string $id, UnitTypeMutationData $data): void
    {
        $this->getQueryBuilder()
            ->where(UnitTypeModel::getPkColumnName(), $id)
            ->update(UnitTypeMapper::serializeUpdate($data));

        $this->clearCache($id);
    }

    /**
     * @param string $id
     *
     * @return void
     */
    private function clearCache(string $id): void
    {
        foreach ($this->getByIdsDL as $dl) {
            if ($dl->key_exists($id)) {
                $dl->clear($id);
                return;
            }
        }
    }
}
