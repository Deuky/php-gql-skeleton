<?php

namespace Vertuoza\Repositories\Settings\UnitTypes\Models;

use DateTime;
use stdClass;

class UnitTypeModel
{
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

    public static function getPkColumnName(): string
    {
        return 'id';
    }

    public static function getTenantColumnName(): string
    {
        return 'tenant_id';
    }

    public static function getTableName(): string
    {
        return 'unit_type';
    }
}
