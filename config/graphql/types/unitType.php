<?php

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

return new ObjectType([
    'name' => 'UnitType',
    'description' => 'Unit type"',
    'fields' => static fn (): array => [
        'id' => [
            'description' => "Unique identifier of the unit type",
            'type' => Type::id(),
        ],
        'name' => [
            'description' => "Name of the unit type",
            'type' => Type::string()
        ],
        'isSystem' => [
            'description' => "To know if the unit type has been created by the user or is a system unit type of Vertuoza",
            'type' => new NonNull(Type::boolean())
        ],
    ],
]);