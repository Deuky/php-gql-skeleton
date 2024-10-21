<?php

namespace Vertuoza\Entities;

use Vertuoza\Attributes\PrimaryKey;

class UnitTypeEntity
{
    #[PrimaryKey]
    public string $id;
    public string $name;
    public bool $isSystem;
}
