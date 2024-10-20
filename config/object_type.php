<?php

use Vertuoza\Api\Graphql\Resolvers\Settings\UnitTypes\UnitTypeQuery;
use Vertuoza\Api\Graphql\Types;

return [
    'name' => 'Query',
    'fields' => function () {
        return [
            'hello' => [
                'type' => Types::string(),
                'resolve' => function ($root, $args) {
                    return 'world';
                }
            ],
            ...UnitTypeQuery::get()
        ];
    }
];
