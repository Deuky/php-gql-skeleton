<?php

namespace Vertuoza\Repositories\UnitTypes;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Overblog\DataLoader\DataLoader;
use Overblog\PromiseAdapter\PromiseAdapterInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Vertuoza\Kernel;
use Vertuoza\Repositories\UnitTypes\Models\UnitTypeMapper;
use Vertuoza\Repositories\UnitTypes\Models\UnitTypeModel;
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
     * @param string ...$ids
     *
     * @return PromiseInterface
     */
    private function fetchByIds(string ...$ids): PromiseInterface
    {
        return async(function () use ($ids) {
            $query = $this->getQueryBuilder();
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
     * @return DataLoader
     */
    protected function getDataloader(): DataLoader
    {
        $kernel = Kernel::getInstance();
        $tenantId = $kernel->getUserContext()->getTenantId();

        if (!isset($this->getByIdsDL[$tenantId])) {
            $this->getByIdsDL[$tenantId] = new DataLoader(function (array $ids) {
                return $this->fetchByIds(...$ids);
            }, $this->dataLoaderPromiseAdapter);
        }

        return $this->getByIdsDL[$tenantId];
    }

    /**
     * @return Builder
     */
    protected function getQueryBuilder(): Builder
    {
        $kernel = Kernel::getInstance();
        $tenantId = $kernel->getUserContext()->getTenantId();

        return $this->db->getConnection()->table(UnitTypeModel::getTableName())
                    ->where([UnitTypeModel::getTenantColumnName() => $tenantId])
                    ->orWhereNull(UnitTypeModel::getTenantColumnName());
    }

    /**
     * @param array $ids
     *
     * @return Promise
     */
    public function getByIds(array $ids): Promise
    {
        return $this->getDataloader()->loadMany($ids);
    }

    /**
     * @param string $id
     *
     * @return Promise
     */
    public function getById(string $id): Promise
    {
        return $this->getDataloader()->load($id);
    }

    /**
     * @param string $name
     * @param string|int|null $excludeId
     *
     * @return PromiseInterface
     */
    public function countUnitTypeWithLabel(string $name, string|int|null $excludeId = null): PromiseInterface
    {
        return async(
            fn () => $this->getQueryBuilder()
                ->where('label', $name)
                ->whereNull('deleted_at')
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
            fn () => $this->getQueryBuilder()
                ->whereNull('deleted_at')
                ->get()
                ->map(function ($row) {
                    return UnitTypeMapper::modelToEntity(UnitTypeModel::fromStdclass($row));
                })
        )();
    }

    /**
     * @param UnitTypeMutationData $data
     *
     * @return int|string
     */
    public function create(UnitTypeMutationData $data): int|string
    {
        $kernel = Kernel::getInstance();
        $tenantId = $kernel->getUserContext()->getTenantId();

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
