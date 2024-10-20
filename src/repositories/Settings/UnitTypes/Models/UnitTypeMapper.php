<?php

namespace Vertuoza\Repositories\Settings\UnitTypes\Models;

use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeMutationData;
use Vertuoza\Entities\Settings\UnitTypeEntity;

class UnitTypeMapper
{
    /**
     * @param UnitTypeModel $dbData
     *
     * @return UnitTypeEntity
     */
    public static function modelToEntity(UnitTypeModel $dbData): UnitTypeEntity
    {
        $entity = new UnitTypeEntity();
        $entity->id = (string) $dbData->id;
        $entity->name = $dbData->label;
        $entity->isSystem = (($dbData->tenantId ?? null) === null);

        return $entity;
    }

    /**
     * @param UnitTypeMutationData $mutation
     *
     * @return array
     */
    public static function serializeUpdate(UnitTypeMutationData $mutation): array
    {
        return self::serializeMutation($mutation);
    }

    /**
     * @param UnitTypeMutationData $mutation
     * @param string $tenantId
     *
     * @return array
     */
    public static function serializeCreate(UnitTypeMutationData $mutation, string $tenantId): array
    {
        return self::serializeMutation($mutation, $tenantId);
    }

    /**
     * @param UnitTypeMutationData $mutation
     * @param string|null $tenantId
     *
     * @return array
     */
    private static function serializeMutation(UnitTypeMutationData $mutation, string $tenantId = null): array
    {
        $data = [
            'label' => $mutation->name,
        ];

        if ($tenantId) {
            $data[UnitTypeModel::getTenantColumnName()] = $tenantId;
        }
        return $data;
    }
}
