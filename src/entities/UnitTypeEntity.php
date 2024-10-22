<?php

namespace Vertuoza\Entities;

use Illuminate\Database\Eloquent\Model;

class UnitTypeEntity extends Model
{
    public $incrementing = false;
    protected $table = 'unit_type';
    protected $primaryKey = 'id';
    protected $tenantKey = 'tenant_id';

}
