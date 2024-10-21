<?php

namespace Vertuoza\Repositories\UnitTypes\Models;

use DateTime;
use stdClass;
use Vertuoza\Attributes\PrimaryKey;

class UnitTypeModel
{
    #[PrimaryKey]
    public string $id;
    public string $label;
    public ?DateTime $deletedAt;
    public ?string $tenantId;

    public static function fromStdclass(stdClass $data): UnitTypeModel
    {
        $model = new UnitTypeModel();

        $model->id = $data->id;
        $model->label = $data->label;
        $model->deletedAt = $data->deleted_at;
        $model->tenantId = $data->tenant_id;

        return $model;
    }

    /**
     * @return string
     * @deprecated
     */
    public static function getPkColumnName(): string
    {
        return 'id';
    }

    /**
     * @return string
     * @deprecated
     */
    public static function getTenantColumnName(): string
    {
        return 'tenant_id';
    }

    /**
     * @return string
     * @deprecated
     */
    public static function getTableName(): string
    {
        return 'unit_type';
    }
}
