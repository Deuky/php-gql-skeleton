<?php

namespace Vertuoza\Api\Graphql\Types;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use Vertuoza\Api\Graphql\Types;

class UnitType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'UnitType',
            'description' => 'Unit type"',
            'fields' => static fn (): array => [
                'id' => [
                    'description' => "Unique identifier of the unit type",
                    'type' => Types::id(),
                ],
                'name' => [
                    'description' => "Name of the unit type",
                    'type' => Types::string()
                ],
                'isSystem' => [
                    'description' => "To know if the unit type has been created by the user or is a system unit type of Vertuoza",
                    'type' => new NonNull(Types::boolean())
                ],
            ],
        ]);
    }
}
