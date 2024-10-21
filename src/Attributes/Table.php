<?php

namespace Vertuoza\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
    public string $name;
}
